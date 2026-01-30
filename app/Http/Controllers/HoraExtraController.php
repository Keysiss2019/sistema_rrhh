<?php

// Define el espacio de nombres del controlador dentro de la aplicación
namespace App\Http\Controllers;

use App\Models\HoraExtra;                 // Modelo que gestiona los registros de horas extra (FT-GTH-002)
use App\Models\SaldoTiempoCompensatorio; // Modelo que maneja el saldo consolidado de tiempo compensatorio del empleado
use App\Models\TiempoCompensatorio;     // Modelo que almacena el historial de movimientos de tiempo compensatorio
use App\Models\Empleado;               // Modelo del empleado (datos personales y laborales)
use Illuminate\Http\Request;          // Clase para manejar las solicitudes HTTP (Request)
use Illuminate\Support\Facades\DB;    // Facade para ejecutar transacciones y consultas directas a la base de datos
use Illuminate\Support\Facades\Auth;  // Facade para obtener el usuario autenticado y controlar permisos


class HoraExtraController extends Controller
{
    
    /**
     * Muestra la bandeja de entrada para jefes/administración
     * Solo registros con estado 'pendiente'
     */
    public function pendientes()
    {
        // Traemos las horas extras con la relación del empleado para ver su nombre
        $pendientes = HoraExtra::with('empleado')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('horas_extras.pendientes', compact('pendientes'));
    }

    /**
     * Registra una nueva solicitud de horas extras (Formato FT-GTH-002)
     */
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'       => 'required|exists:empleados,id',
            'lugar'             => 'required|string|max:255',
            'solicitado_a'      => 'required|string|max:255',
            'cargo_solicitante' => 'required|string|max:255',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'required|date|after_or_equal:fecha_inicio',
            'horas'             => 'required|numeric|min:0.1',
            'detalle_actividad' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            HoraExtra::create([
                'empleado_id'       => $request->empleado_id,
                'lugar'             => $request->lugar,
                'solicitado_a'      => $request->solicitado_a,
                'cargo_solicitante' => $request->cargo_solicitante,
                'fecha_inicio'      => $request->fecha_inicio,
                'fecha_fin'         => $request->fecha_fin,
                'horas_trabajadas'  => $request->horas,
                'detalle_actividad' => $request->detalle_actividad,
                'observaciones_jefe'=> $request->observaciones, 
                'codigo_formato'    => 'FT-GTH-002',
                'estado'            => 'pendiente',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Guardado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Procesa la validación (Aprobación/Rechazo) desde la bandeja de pendientes
     */
    public function validar(Request $request, $id)
{
    $request->validate([
        'accion' => 'required|in:aprobado,rechazado',
        'observaciones_jefe' => 'nullable|string|max:500'
    ]);

    try {
        return DB::transaction(function () use ($request, $id) {
            $registro = HoraExtra::findOrFail($id);

            $registro->update([
                'estado'             => $request->accion,
                'aprobado_por'       => Auth::id(),
                'fecha_aprobacion'   => now(),
                'observaciones_jefe' => $request->observaciones_jefe ?? $registro->observaciones_jefe,
            ]);

            if ($request->accion === 'aprobado') {
                $horas = (float) $registro->horas_trabajadas;

                // A. Registrar Movimiento de Entrada
                TiempoCompensatorio::create([
                    'empleado_id'      => $registro->empleado_id,
                    'tipo_movimiento'  => 'entrada',
                    'horas'            => $horas,
                    'descripcion'      => 'Horas Extras: ' . $registro->detalle_actividad,
                    'autorizado_por'   => Auth::id(),
                    'fecha_movimiento' => now(),
                ]);

                // B. Actualizar Saldo (Acumular)
                $saldo = SaldoTiempoCompensatorio::firstOrCreate(
                    ['empleado_id' => $registro->empleado_id],
                    ['horas_acumuladas' => 0, 'horas_usadas' => 0, 'horas_pagadas' => 0, 'horas_disponibles' => 0]
                );

                $totalAprobadoReal = HoraExtra::where('empleado_id', $registro->empleado_id)
                 ->where('estado', 'aprobado')
                  ->sum('horas_trabajadas');

                $saldo->horas_acumuladas = $totalAprobadoReal;
            }

            $mensaje = $request->accion == 'aprobado' ? 'Aprobado y saldo actualizado' : 'Registro rechazado';
            return redirect()->route('horas_extras.pendientes')->with('success', $mensaje);
        });
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al procesar: ' . $e->getMessage());
    }
}


}