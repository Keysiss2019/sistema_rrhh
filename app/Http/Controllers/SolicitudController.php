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
use Illuminate\Support\Facades\Mail;      // Envío de correos
use App\Mail\CambioTipoSolicitudMail;     // Mailable para notificar cambio de tipo
use Illuminate\Support\Facades\DB;      // Facade para operaciones de base de datos y transacciones
use Illuminate\Http\Request;            // Clase para capturar requests HTTP
use Illuminate\Support\Facades\Auth;    // Facade para autenticación
use Carbon\Carbon;                      // Biblioteca para manejo de fechas

/* =========================
   CLASE CONTROLADOR
========================= */

class SolicitudController extends Controller
{
    /**
     * LISTADO DE SOLICITUDES
     * - Admin/Jefe: ve todas
     * - Empleado: solo las propias
     * - Incluye búsqueda por empleado, tipo y estado
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Validación básica: el usuario debe estar ligado a un empleado
        if (!$user || !$user->empleado_id) {
            return "Error: Tu usuario no tiene un perfil de empleado asociado.";
        }

        $rol = $user->rol ? $user->rol->nombre : 'Sin Rol';
        $search = $request->input('search');

        // Query base según rol
        $query = in_array($rol, ['Administrador', 'Jefe'])
            ? Solicitud::with('empleado')
            : Solicitud::where('empleado_id', $user->empleado_id)->with('empleado');

        // Filtro de búsqueda
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

        // Orden y paginación
        $solicitudes = $query->latest()->paginate(10);

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * VER DETALLE DE UNA SOLICITUD
     * - Calcula saldo de vacaciones según periodo actual
     * - Respeta días congelados si ya fue aprobada
     */
    public function show($id)
    {
        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado  = $solicitud->empleado;

        // 1. Días anuales según contrato
        if ($solicitud->estado === 'aprobado' && $solicitud->dias_anuales_aplicados) {
            // Usar días congelados al aprobar
            $diasAnualesContrato = $solicitud->dias_anuales_aplicados;
        } else {
            // Usar política vigente
            $politica = PoliticaVacaciones::where('tipo_contrato', $empleado->tipo_contrato)->first();
            $diasAnualesContrato = $politica ? $politica->dias_anuales : 0;
        }

        // 2. Inicio del periodo actual (aniversario laboral)
        $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
        $aniversarioEsteAño = $fechaIngreso->copy()->year(now()->year);

        $inicioPeriodoActual = ($aniversarioEsteAño->isFuture())
            ? $aniversarioEsteAño->subYear()
            : $aniversarioEsteAño;

        // 3. Derecho del ciclo actual (no acumulable)
        $diasDerechoCicloActual = $diasAnualesContrato;

        // 4. Días usados solo en este periodo
        $diasUsadosPeriodo = Solicitud::where('empleado_id', $empleado->id)
            ->where('tipo', 'vacaciones')
            ->where('estado', 'aprobado')
            ->where('fecha_inicio', '>=', $inicioPeriodoActual)
            ->sum('dias') ?? 0;

        // 5. Saldo actual y saldo proyectado
        $saldoActual = round($diasDerechoCicloActual - $diasUsadosPeriodo, 2);

        $nuevoSaldo = ($solicitud->tipo === 'vacaciones')
            ? $saldoActual - $solicitud->dias
            : $saldoActual;

        return view('solicitudes.show', compact(
            'solicitud',
            'empleado',
            'saldoActual',
            'nuevoSaldo',
            'inicioPeriodoActual'
        ));
    }

    /**
     * PROCESAR SOLICITUD (APROBAR / RECHAZAR)
     * - Maneja vacaciones y tiempo compensatorio
     * - Todo dentro de transacción
     */
    public function procesar(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado'
        ]);

        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado  = $solicitud->empleado;

        return DB::transaction(function () use ($request, $solicitud, $empleado) {

            // 1. RECHAZAR
            if ($request->estado === 'rechazado') {
                $solicitud->update([
                    'estado'           => 'rechazado',
                    'aprobado_por'     => Auth::id(),
                    'fecha_aprobacion' => now(),
                ]);

                return redirect()->route('solicitudes.index')
                    ->with('success', 'La solicitud fue rechazada.');
            }

            // 2. DÍAS DEL CONTRATO
            $politica = PoliticaVacaciones::where('tipo_contrato', $empleado->tipo_contrato)->first();
            $diasContrato = $politica ? $politica->dias_anuales : 0;

            // 3. APROBAR SOLICITUD
            $solicitud->update([
                'estado'                  => 'aprobado',
                'aprobado_por'            => Auth::id(),
                'fecha_aprobacion'        => now(),
                'dias_anuales_aplicados'  => $diasContrato, // Congelado
            ]);

            // 4. TIEMPO COMPENSATORIO
            if ($solicitud->tipo === 'tiempo_compensatorio') {
                $horasSolicitadas = (float) $solicitud->horas;

                // Registrar salida de horas
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

                // Actualizar saldo
                $saldo = SaldoTiempoCompensatorio::firstOrCreate(
                    ['empleado_id' => $empleado->id],
                    [
                        'horas_acumuladas' => 0,
                        'horas_usadas' => 0,
                        'horas_pagadas' => 0,
                        'horas_disponibles' => 0,
                        'horas_debe' => 0
                    ]
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

            return redirect()->route('solicitudes.index')
                ->with('success', 'Solicitud aprobada correctamente.');
        });
    }

    /**
     * RECTIFICAR TIPO DE SOLICITUD
     * - Revierte impactos del tipo anterior
     * - Registra motivo y notifica por correo
     */
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

            // Revertir impacto si antes era compensatorio
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

            // Actualizar tipo y registrar motivo
            $solicitud->tipo = $request->nuevo_tipo;
            $solicitud->detalles .= "\n[RECTIFICACIÓN]: " . $request->motivo;
            $solicitud->save();
        });

        // Notificar al empleado
        if ($solicitud->empleado && $solicitud->empleado->email) {
            Mail::to($solicitud->empleado->email)
                ->send(new CambioTipoSolicitudMail($solicitud, $tipoAnterior, $request->motivo));
        }

        return back()->with('success', 'Cambios aplicados correctamente.');
    }

    /**
     * EDITAR SOLICITUD (solo si está pendiente)
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'No se puede editar una solicitud ya ' . $solicitud->estado);
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

    /**
     * CÁLCULO PERMANENTE (Ley Honduras)
     * - Calcula derecho histórico total
     * - Resta vacaciones gozadas
     */
    public function calculoPermanente($empleadoId)
    {
        $empleado = Empleado::findOrFail($empleadoId);
        $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
        $hoy = Carbon::now();

        $totalDerechoHistorico = 0;
        $aniosCumplidos = $fechaIngreso->diffInYears($hoy);

        for ($i = 1; $i <= ($aniosCumplidos + 1); $i++) {
            if ($i == 1) {
                $totalDerechoHistorico += 10;
            } elseif ($i == 2) {
                $totalDerechoHistorico += 12;
            } elseif ($i == 3) {
                $totalDerechoHistorico += 15;
            } else {
                $totalDerechoHistorico += 20;
            }
        }

        $solicitudes = Solicitud::where('empleado_id', $empleado->id)
            ->where('tipo', 'vacaciones')
            ->where('estado', 'aprobado')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $totalGozados = $solicitudes->sum('dias');
        $saldo = $totalDerechoHistorico - $totalGozados;

        return view('solicitudes.calculo_modal', compact(
            'empleado',
            'totalDerechoHistorico',
            'totalGozados',
            'saldo',
            'solicitudes',
            'aniosCumplidos'
        ));
    }
}
