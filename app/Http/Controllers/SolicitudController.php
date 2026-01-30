<?php

/* =========================
   NAMESPACE Y USES
========================= */

namespace App\Http\Controllers; // Define el namespace del controlador

use App\Models\Solicitud;               // Modelo de solicitudes
use App\Models\Empleado;                // Modelo de empleados
use App\Models\PoliticaVacaciones;      // Modelo de políticas de vacaciones
use App\Models\TiempoCompensatorio;     // Modelo para registrar movimientos de tiempo compensatorio
use App\Models\SaldoTiempoCompensatorio; // Modelo para saldo de tiempo compensatorio
use Illuminate\Support\Facades\DB;      // Facade para operaciones de base de datos y transacciones
use Illuminate\Http\Request;            // Clase para capturar requests HTTP
use Illuminate\Support\Facades\Auth;    // Facade para autenticación
use Carbon\Carbon;                      // Biblioteca para manejo de fechas

/* =========================
   CLASE CONTROLADOR
========================= */

class SolicitudController extends Controller
{
    /* =========================
       MÉTODO INDEX
       Lista y filtra solicitudes
    ========================== */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->empleado_id) {
            return "Error: Tu usuario no tiene un perfil de empleado asociado.";
        }

        $rol = $user->rol ? $user->rol->nombre : 'Sin Rol';
        $search = $request->input('search');

        $query = in_array($rol, ['Administrador', 'Jefe'])
            ? Solicitud::with('empleado')
            : Solicitud::where('empleado_id', $user->empleado_id)->with('empleado');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('empleado', function($emp) use ($search) {
                    $emp->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido', 'LIKE', "%{$search}%")
                        ->orWhere('cargo', 'LIKE', "%{$search}%");
                })
                ->orWhere('tipo', 'LIKE', "%{$search}%")
                ->orWhere('estado', 'LIKE', "%{$search}%");
            });
        }

        $solicitudes = $query->latest()->paginate(10);

        return view('solicitudes.index', compact('solicitudes'));
    }

    /* =========================
       MÉTODO SHOW
       Mostrar detalles de una solicitud
    ========================== */
    public function show($id)
    {
        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado  = $solicitud->empleado;

        $jornadaDiaria = 8;
        $politica = PoliticaVacaciones::where('tipo_contrato', $empleado->tipo_contrato)->first();
        $diasAnuales = $politica ? $politica->dias_anuales : 0;

        if ($empleado->tipo_contrato === 'permanente' && $empleado->fecha_ingreso) {
            $meses = Carbon::parse($empleado->fecha_ingreso)->diffInMonths(now());
            $diasDerecho = (($diasAnuales * $jornadaDiaria) / 12 * $meses) / $jornadaDiaria;
        } else {
            $diasDerecho = $diasAnuales;
        }

        $diasUsados = Solicitud::where('empleado_id', $empleado->id)
            ->where('tipo', 'vacaciones')
            ->where('estado', 'aprobado')
            ->sum('dias');

        $saldoActual = $diasDerecho - $diasUsados;
        $nuevoSaldo = $solicitud->tipo === 'vacaciones'
            ? $saldoActual - $solicitud->dias
            : $saldoActual;

        return view('solicitudes.show', compact(
            'solicitud',
            'empleado',
            'saldoActual',
            'nuevoSaldo'
        ));
    }

    /* =========================
       MÉTODO PROCESAR
       Aprobar o rechazar solicitudes
    ========================== */
    public function procesar(Request $request, $id)
    {
        $request->validate(['estado' => 'required|in:aprobado,rechazado']);

        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado = $solicitud->empleado;

        return DB::transaction(function () use ($request, $solicitud, $empleado) {

            if ($request->estado === 'rechazado') {
                $solicitud->update([
                    'estado' => 'rechazado',
                    'aprobado_por' => Auth::id(),
                    'fecha_aprobacion' => now(),
                ]);
                return redirect()->route('solicitudes.index')->with('success', 'La solicitud ha sido rechazada.');
            }

            $solicitud->update([
                'estado' => 'aprobado',
                'aprobado_por' => Auth::id(),
                'fecha_aprobacion' => now(),
            ]);

            if ($solicitud->tipo === 'tiempo_compensatorio') {
                $horasSolicitadas = (float) $solicitud->horas;

                TiempoCompensatorio::updateOrCreate(
                    ['solicitud_id' => $solicitud->id],
                    [
                        'empleado_id'      => $empleado->id,
                        'tipo_movimiento'  => 'salida',
                        'horas'            => $horasSolicitadas,
                        'autorizado_por'   => Auth::id(),
                        'fecha_movimiento' => now(),
                        'descripcion'      => $solicitud->detalles,
                    ]
                );

                $saldo = SaldoTiempoCompensatorio::firstOrCreate(
                    ['empleado_id' => $empleado->id],
                    ['horas_acumuladas' => 0, 'horas_usadas' => 0, 'horas_pagadas' => 0, 'horas_disponibles' => 0]
                );

                $saldo->horas_usadas += $horasSolicitadas;

                $totalConsumido = $saldo->horas_usadas + $saldo->horas_pagadas;
                if ($saldo->horas_acumuladas >= $totalConsumido) {
                    $saldo->horas_disponibles = $saldo->horas_acumuladas - $totalConsumido;
                    $saldo->horas_debe = 0;
                } else {
                    $saldo->horas_disponibles = 0;
                    $saldo->horas_debe = $totalConsumido - $saldo->horas_acumuladas;
                }
                $saldo->save();
            }

            return redirect()->route('solicitudes.index')->with('success', 'Solicitud aprobada y saldo actualizado.');
        });
    }

    /* =========================
       MÉTODO RECTIFICAR TIPO
       Cambia tipo de solicitud y ajusta historial
       (Se eliminó el envío de correo)
    ========================== */
    public function rectificarTipo(Request $request, $id)
    {
        $request->validate([
            'nuevo_tipo' => 'required|in:vacaciones,sin_goce,tiempo_compensatorio',
            'motivo' => 'required|string|min:5'
        ]);

        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $tipoAnterior = $solicitud->tipo;
        $horas = (float) $solicitud->horas;

        DB::transaction(function () use ($solicitud, $tipoAnterior, $horas, $request) {
            if ($tipoAnterior === 'tiempo_compensatorio') {
                TiempoCompensatorio::where('solicitud_id', $solicitud->id)->delete();

                $saldo = SaldoTiempoCompensatorio::where('empleado_id', $solicitud->empleado_id)->first();
                if ($saldo) {
                    $saldo->horas_usadas -= $horas;
                    $totalConsumido = $saldo->horas_usadas + $saldo->horas_pagadas;
                    if ($totalConsumido > $saldo->horas_acumuladas) {
                        $saldo->horas_debe = $totalConsumido - $saldo->horas_acumuladas;
                        $saldo->horas_disponibles = 0;
                    } else {
                        $saldo->horas_debe = 0;
                        $saldo->horas_disponibles = $saldo->horas_acumuladas - $totalConsumido;
                    }
                    $saldo->save();
                }
            }

            $solicitud->tipo = $request->nuevo_tipo;
            $solicitud->detalles .= "\n[RECTIFICACIÓN]: " . $request->motivo;
            $solicitud->save();
        });

        // Nota: El envío de correo fue eliminado

        return back()->with('success', 'Cambios aplicados: Historial limpio y solicitud actualizada.');
    }

    /* =========================
       MÉTODO UPDATE
       Actualiza solicitud pendiente
    ========================== */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'No se puede editar una solicitud que ya fue ' . $solicitud->estado);
        }

        $request->validate([
            'tipo' => 'required|in:vacaciones,tiempo_compensatorio,sin_goce',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|min:5',
        ]);

        $solicitud->update([
            'tipo' => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'detalles' => $request->motivo, 
        ]);

        return back()->with('success', 'Solicitud actualizada correctamente.');
    }
}
