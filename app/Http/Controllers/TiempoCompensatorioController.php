<?php

/* =========================
   NAMESPACE Y DEPENDENCIAS
========================= */

namespace App\Http\Controllers; // Namespace del controlador

use App\Models\TiempoCompensatorio;        // Historial de movimientos de tiempo compensatorio
use App\Models\SaldoTiempoCompensatorio;   // Tabla consolidada de saldos
use App\Models\HoraExtra;                  // Historial de horas extra
use App\Models\Empleado;                   // Modelo de empleados
use Illuminate\Http\Request;               // Manejo de requests HTTP
use Illuminate\Support\Facades\Auth;       // Autenticación de usuarios

/* =========================
   CONTROLADOR
========================= */

class TiempoCompensatorioController extends Controller
{
    /**
     * =========================
     * INDEX
     * Muestra el historial de tiempo compensatorio
     * según rol (Admin, Dirección, Jefe o Empleado)
     * =========================
     */
    public function index(Request $request)
    {
        $user = Auth::user(); // Usuario autenticado

        /* =========================
           DEFINICIÓN DE ROLES
        ========================= */
        $esAdmin     = $user->rol && $user->rol->nombre === 'Administrador';
        $esJefe      = $user->rol && $user->rol->nombre === 'Jefe';
        $esDireccion = $user->rol && $user->rol->nombre === 'Direccion';
        $esEmpleado  = !$esAdmin && !$esJefe && !$esDireccion;

        /* =========================
           EMPLEADOS DISPONIBLES
           PARA EL SELECTOR
        ========================= */
        if ($esAdmin || $esDireccion) {
            // Admin y Dirección ven todos los empleados
            $empleados = Empleado::orderBy('nombre')->get();
        } elseif ($esJefe) {
            // El jefe solo ve a sus subordinados
            $empleados = Empleado::where('jefe_id', $user->empleado_id)
                ->orderBy('nombre')
                ->get();
        } else {
            // El empleado normal no ve selector
            $empleados = collect();
        }

        /* =========================
           DEFINIR EMPLEADO A CONSULTAR
        ========================= */
        if ($esEmpleado) {
            // El empleado solo puede verse a sí mismo
            $empleado_id = $user->empleado_id;
        } else {
            // Jefes/Admin usan el selector
            $empleado_id = $request->empleado_id;
        }

        /* =========================
           VARIABLES POR DEFECTO
        ========================= */
        $movimientos = collect(); // Historial de movimientos
        $horasExtra  = collect(); // Historial de horas extra
        $empleado    = null;      // Empleado seleccionado
        $saldo       = 0;         // Saldo calculado

        /* =========================
           VALIDACIÓN Y CARGA DE DATOS
        ========================= */
        if ($empleado_id) {
            $empleado = Empleado::find($empleado_id);

            // Validar que el empleado exista
            if (!$empleado) {
                abort(404, 'Empleado no encontrado');
            }

            // Seguridad: el jefe no puede acceder a empleados ajenos
            if ($esJefe && $empleado->jefe_id !== $user->empleado_id) {
                abort(403, 'No autorizado');
            }

            /* =========================
               HISTORIAL DE MOVIMIENTOS
            ========================= */
            $movimientos = TiempoCompensatorio::with('autorizadoPor.empleado')
                ->where('empleado_id', $empleado->id)
                ->orderByDesc('fecha_movimiento')
                ->get();

            /* =========================
               HISTORIAL DE HORAS EXTRA
            ========================= */
            $horasExtra = HoraExtra::where('empleado_id', $empleado->id)
                ->orderByDesc('created_at')
                ->get();

            /* =========================
               CÁLCULO DINÁMICO DE SALDO
            ========================= */
            $acumulado = $movimientos->where('tipo_movimiento', 'entrada')->sum('horas');
            $libre     = $movimientos->where('tipo_movimiento', 'libre')->sum('horas');
            $pagado    = $movimientos->where('tipo_movimiento', 'pagado')->sum('horas');

            // Fórmula final
            $saldo = $acumulado - ($libre + $pagado);
        }

        // Retornar vista
        return view('tiempo_compensatorio.index', compact(
            'movimientos',
            'empleados',
            'empleado',
            'saldo',
            'horasExtra'
        ));
    }

    /**
     * =========================
     * STORE
     * Registra un nuevo movimiento
     * manual de tiempo compensatorio
     * =========================
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'empleado_id'     => 'required|exists:empleados,id',
            'tipo_movimiento' => 'required|in:acumulado,libre,pagado',
            'horas'           => 'required|numeric|min:0.1',
        ]);

        $horas = (float) $request->horas;
        $tipo  = $request->tipo_movimiento;

        /* =========================
           REGISTRO EN HISTORIAL
        ========================= */
        TiempoCompensatorio::create([
            'empleado_id'      => $request->empleado_id,
            'tipo_movimiento'  => $tipo,
            'horas'            => $horas,
            'fecha_movimiento' => now(),
            'autorizado_por'   => Auth::id(),
            'observaciones'    => $request->observaciones ?? 'Carga manual de horas extra',
            'origen'           => 'manual',
        ]);

        /* =========================
           ACTUALIZACIÓN DE SALDO
        ========================= */
        $saldo = SaldoTiempoCompensatorio::firstOrCreate(
            ['empleado_id' => $request->empleado_id]
        );

        // Actualizar columna según tipo
        if ($tipo === 'acumulado') {
            $saldo->horas_acumuladas += $horas;
        } elseif ($tipo === 'libre') {
            $saldo->horas_usadas += $horas;
        } elseif ($tipo === 'pagado') {
            $saldo->horas_pagadas += $horas;
        }

        /* =========================
           RECÁLCULO FINAL
        ========================= */
        $totalConsumido = $saldo->horas_usadas + $saldo->horas_pagadas;

        if ($totalConsumido > $saldo->horas_acumuladas) {
            $saldo->horas_debe = $totalConsumido - $saldo->horas_acumuladas;
            $saldo->horas_disponibles = 0;
        } else {
            $saldo->horas_debe = 0;
            $saldo->horas_disponibles = $saldo->horas_acumuladas - $totalConsumido;
        }

        $saldo->save();

        return back()->with('success', 'Movimiento registrado exitosamente en el historial y saldo actualizado.');
    }

    /**
     * =========================
     * SHOW
     * Vista simple de un empleado
     * =========================
     */
    public function show($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('tiempo_compensatorio.show', compact('empleado'));
    }

    /**
     * =========================
     * RECALCULAR TODO
     * Reconstruye saldos desde
     * el historial completo
     * =========================
     */
    public function recalcularTodo($empleado_id)
    {
        // Obtener historial completo
        $movimientos = TiempoCompensatorio::where('empleado_id', $empleado_id)->get();

        // Sumar por tipo
        $acumuladas = $movimientos->where('tipo_movimiento', 'acumulado')->sum('horas');
        $usadas     = $movimientos->where('tipo_movimiento', 'libre')->sum('horas');
        $pagadas    = $movimientos->where('tipo_movimiento', 'pagado')->sum('horas');

        // Fórmula final
        $totalConsumido = $usadas + $pagadas;
        $debe = 0;
        $disponible = 0;

        if ($totalConsumido > $acumuladas) {
            $debe = $totalConsumido - $acumuladas;
        } else {
            $disponible = $acumuladas - $totalConsumido;
        }

        // Guardar saldo definitivo
        SaldoTiempoCompensatorio::updateOrCreate(
            ['empleado_id' => $empleado_id],
            [
                'horas_acumuladas'  => $acumuladas,
                'horas_usadas'      => $usadas,
                'horas_pagadas'     => $pagadas,
                'horas_debe'        => $debe,
                'horas_disponibles' => $disponible,
            ]
        );

        return back()->with('success', 'Saldos reconstruidos correctamente desde el historial.');
    }
}
