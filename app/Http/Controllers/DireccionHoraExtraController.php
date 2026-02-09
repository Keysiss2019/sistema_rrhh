<?php
namespace App\Http\Controllers;

use App\Models\HoraExtra;
use App\Models\TiempoCompensatorio;
use App\Models\SaldoTiempoCompensatorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DireccionHoraExtraController extends Controller
{
public function index()
{
    $user = auth()->user();

    // Validamos por ID: 1 para Administrador, 4 para Dirección
    // Suponiendo que tu columna en la tabla 'users' se llama 'role_id'
    if ($user->role_id != 1 && $user->role_id != 4) {
        abort(403, 'No tiene permisos para acceder a esta sección.');
    }

    $pendientes = HoraExtra::with('empleado')
        ->where('estado', 'pendiente')
        ->orderBy('created_at')
        ->get();

    return view('tiempo_compensatorio.direccion', compact('pendientes'));
}

    public function decidir(Request $request, $id)
{
    // 1. Validación dinámica por ID de Rol (1=Admin, 4=Dirección)
    $rol_id = auth()->user()->role_id;
    if ($rol_id != 1 && $rol_id != 4) {
        abort(403, 'No tiene autoridad para procesar esta solicitud.');
    }

    // 2. Validación de datos de entrada
    $request->validate([
        'accion'        => 'required|in:aprobado,rechazado',
        'horas_pagadas' => 'nullable|numeric|min:0',
        'observaciones' => $request->accion === 'rechazado' ? 'required|string|max:500' : 'nullable|string|max:500',
    ], [
        'observaciones.required' => 'Debe indicar el motivo del rechazo.'
    ]);

    return DB::transaction(function () use ($request, $id) {
        $hora = HoraExtra::findOrFail($id);
        $msg = "";

        if ($request->accion === 'rechazado') {
            $hora->update([
                'estado'             => 'rechazado',
                'aprobado_por'       => Auth::id(),
                'fecha_aprobacion'   => now(),
                'observaciones_jefe' => $request->observaciones, 
            ]);
            $msg = 'Solicitud rechazada correctamente.';
        } else {
            // Lógica de Aprobación
            $horasTrabajadas = (float) $hora->horas_trabajadas;
            $horasPagadas    = (float) ($request->horas_pagadas ?? 0);

            if ($horasPagadas > $horasTrabajadas) {
                return back()->with('error', 'Las horas pagadas no pueden superar las trabajadas.');
            }

            $hora->update([
                'estado'           => 'aprobado',
                'horas_pagadas'    => $horasPagadas ?: null,
                'aprobado_por'     => Auth::id(),
                'fecha_aprobacion' => now(),
                'observaciones_direccion' => $request->observaciones,
            ]);

            // Registro en historial: Horas para Tiempo Libre (Acumuladas)
            $restanteCompensatorio = $horasTrabajadas - $horasPagadas;
            if ($restanteCompensatorio > 0) {
                TiempoCompensatorio::create([
                    'empleado_id'      => $hora->empleado_id,
                    'tipo_movimiento'  => 'acumulado',
                    'horas'            => $restanteCompensatorio,
                    'fecha_movimiento' => now(),
                    'autorizado_por'   => Auth::id(),
                    'origen'           => 'horas_extra',
                    'observaciones'    => 'Aprobado por Dirección: Tiempo Compensatorio',
                ]);
            }

            // Registro en historial: Horas Pagadas en Efectivo
            if ($horasPagadas > 0) {
                TiempoCompensatorio::create([
                    'empleado_id'      => $hora->empleado_id,
                    'tipo_movimiento'  => 'pagado',
                    'horas'            => $horasPagadas,
                    'fecha_movimiento' => now(),
                    'autorizado_por'   => Auth::id(),
                    'origen'           => 'horas_extra',
                    'observaciones'    => 'Aprobado por Dirección: Pago en Efectivo',
                ]);
            }

            $this->recalcularSaldo($hora->empleado_id);
            $msg = 'Solicitud aprobada y saldos actualizados.';
        }

        return redirect()->route('direccion.horas_extras')->with('success', $msg);
    });
}

    /**
     * =========================
     * RECÁLCULO DE SALDO GLOBAL
     * =========================
     * Se ejecuta siempre que hay cambios
     */
    private function recalcularSaldo($empleado_id)
    {
        // Traemos todos los movimientos del empleado
        $movs = TiempoCompensatorio::where('empleado_id', $empleado_id)->get();

        // Sumatorias por tipo
        $acum = $movs->where('tipo_movimiento', 'acumulado')->sum('horas');
        $lib  = $movs->where('tipo_movimiento', 'libre')->sum('horas');
        $pag  = $movs->where('tipo_movimiento', 'pagado')->sum('horas');

        $totalUsado = $lib + $pag;

        // Actualizamos o creamos el saldo
        SaldoTiempoCompensatorio::updateOrCreate(
            ['empleado_id' => $empleado_id],
            [
                'horas_acumuladas'  => $acum,
                'horas_usadas'      => $lib,
                'horas_pagadas'     => $pag,
                'horas_debe'        => max(0, $totalUsado - $acum),
                'horas_disponibles' => max(0, $acum - $totalUsado),
            ]
        );
    }
}