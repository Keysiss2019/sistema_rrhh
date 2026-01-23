<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Empleado;
use App\Models\PoliticaVacaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes según el rol.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->empleado_id) {
            return "Error: Tu usuario no tiene un perfil de empleado asociado.";
        }

        $nombreDelRol = ($user->rol) ? $user->rol->nombre : 'Sin Rol';

        if ($nombreDelRol === 'Administrador' || $nombreDelRol === 'Jefe') {
            $solicitudes = Solicitud::with('empleado')->latest()->get();
        } else {
            $solicitudes = Solicitud::where('empleado_id', $user->empleado_id)
                                    ->latest()
                                    ->get();
        }

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Muestra el detalle de una solicitud específica (para el formato de impresión).
     * Mostrar detalle de una solicitud y saldo de vacaciones acumuladas (por horas)
    */
    public function show($id)
    {
      $solicitud = Solicitud::with('empleado')->findOrFail($id);
      $empleado  = $solicitud->empleado;

      // Jornada laboral de referencia en horas (ej. 8h/día)
      $jornadaDiaria = 8;

      // Obtener política según tipo de contrato
       $politica = PoliticaVacaciones::where('tipo_contrato', $empleado->tipo_contrato)->first();
       $diasAnuales = $politica ? $politica->dias_anuales : 0;

       $diasDerecho = 0;

       if ($empleado->tipo_contrato === 'permanente' && $empleado->fecha_ingreso && $diasAnuales > 0) {

          // 1. Calcular meses completos trabajados
          $fechaIngreso = Carbon::parse($empleado->fecha_ingreso)->startOfDay();
          $hoy = Carbon::now()->startOfDay();
          $mesesTrabajados = $fechaIngreso->diffInMonths($hoy);

          // 2. Convertir la política anual a horas y calcular tasa mensual en horas
          $tasaMensualHoras = ($diasAnuales * $jornadaDiaria) / 12;

          // 3. Acumulado total en horas
          $acumuladoHoras = $mesesTrabajados * $tasaMensualHoras;

          // 4. Convertir acumulado a días decimales para mostrar
          $diasDerecho = $acumuladoHoras / $jornadaDiaria;

        } else {
          // Para contratos no permanentes, usar días anuales completos
          $diasDerecho = $diasAnuales;
        }

       // 5. Calcular días ya usados (solo aprobados)
        $diasYaUsados = Solicitud::where('empleado_id', $empleado->id)
        ->where('tipo', 'vacaciones')
        ->where('estado', 'aprobado')
        ->sum('dias');

        // 6. Saldo actual real en días decimales
        $saldoActual = $diasDerecho - $diasYaUsados;

        // 7. Simulación de nuevo saldo si se aprueba esta solicitud
        $nuevoSaldo = $saldoActual;
        if ($solicitud->tipo === 'vacaciones') {
          $nuevoSaldo = $saldoActual - $solicitud->dias;
        }

        // 8. Enviar datos a la vista
        return view(
          'solicitudes.show',
          compact(
              'solicitud',
              'empleado',
              'saldoActual',
              'nuevoSaldo'
            )
        );
    }
    /**
    * Procesar solicitud (Aprobar/Rechazar) considerando acumulación por horas
    */
    public function procesar(Request $request, $id)
    {
        $request->validate([
         'estado' => 'required|in:aprobado,rechazado'
        ]);

        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado  = $solicitud->empleado;

       // Jornada laboral de referencia en horas
       $jornadaDiaria = 8;

       // Si se va a rechazar, no se valida saldo
       if ($request->estado === 'rechazado') {
          $solicitud->update([
             'estado' => 'rechazado',
             'aprobado_por' => Auth::id(),
             'fecha_aprobacion' => now(),
            ]);

           return redirect()->route('solicitudes.index')
            ->with('success', 'La solicitud ha sido rechazada con éxito.');
        }

        // 1. Obtener política de vacaciones
        $politica = PoliticaVacaciones::where('tipo_contrato', $empleado->tipo_contrato)->first();
        $diasAnuales = $politica ? $politica->dias_anuales : 0;

        $diasAcumulados = 0;

        if ($empleado->tipo_contrato === 'permanente' && $empleado->fecha_ingreso && $diasAnuales > 0) {

         // 2. Calcular meses completos trabajados hasta hoy
         $fechaIngreso = Carbon::parse($empleado->fecha_ingreso)->startOfDay();
         $fechaReferencia = Carbon::now()->startOfDay();
         $mesesTrabajados = $fechaIngreso->diffInMonths($fechaReferencia);

         // 3. Tasa mensual en horas
         $tasaMensualHoras = ($diasAnuales * $jornadaDiaria) / 12;

         // 4. Acumulado total en horas
         $acumuladoHoras = $mesesTrabajados * $tasaMensualHoras;

         // 5. Convertir acumulado a días decimales para el sistema
         $diasAcumulados = $acumuladoHoras / $jornadaDiaria;

        } else {
         $diasAcumulados = $diasAnuales;
        }

        // 6. Calcular días ya usados (solo aprobados, excluyendo esta solicitud)
       $diasYaUsados = Solicitud::where('empleado_id', $empleado->id)
         ->where('tipo', 'vacaciones')
         ->where('estado', 'aprobado')
         ->where('id', '!=', $solicitud->id)
         ->sum('dias');

         // 7. Validación: comprobar que la solicitud no exceda el saldo acumulado
          $diasSolicitados = ($solicitud->tipo === 'vacaciones') ? $solicitud->dias : 0;

         if (($diasYaUsados + $diasSolicitados) > $diasAcumulados) {
             return redirect()->back()->withErrors([
                 'saldo' => 'No se puede aprobar la solicitud. '
                  . 'El empleado no tiene suficientes vacaciones acumuladas a la fecha. '
                  . 'Saldo disponible: ' . number_format($diasAcumulados - $diasYaUsados, 2) . ' días.'
                ]);
            }

           // 8. Aprobar la solicitud
          $solicitud->update([
             'estado' => 'aprobado',
             'aprobado_por' => Auth::id(),
             'fecha_aprobacion' => now(),
            ]);

          return redirect()->route('solicitudes.index')
        ->with('success', 'La solicitud ha sido aprobada con éxito.');
    }
}