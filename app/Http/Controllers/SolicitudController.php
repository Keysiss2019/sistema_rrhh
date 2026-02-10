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
     * Incluye buscador y filtro por rango de fechas
     */
    public function index(Request $request)
    {
        // Usuario autenticado
        $user = Auth::user();

        // Validamos que el usuario tenga empleado asociado
        if (!$user || !$user->empleado_id) {
            return "Error: Tu usuario no tiene un perfil de empleado asociado.";
        }

        // Obtenemos el nombre del rol del usuario
        $rol = $user->rol ? $user->rol->nombre : 'Sin Rol';

        // Inputs del formulario
        $search = $request->input('search'); // texto de búsqueda
        $rango  = $request->input('rango');  // rango de fechas (flatpickr)

        // Query base según rol:
        // Administrador y Jefe ven todo
        // Empleado solo ve sus propias solicitudes
        $query = in_array($rol, ['Administrador', 'Jefe'])
            ? Solicitud::with('empleado')
            : Solicitud::where('empleado_id', $user->empleado_id)
                ->with('empleado');

        /* ==========================
           BUSCADOR POR TEXTO
        ===========================*/
        if ($search) {
            $query->where(function ($q) use ($search) {

                // Búsqueda por datos del empleado
                $q->whereHas('empleado', function ($emp) use ($search) {
                    $emp->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido', 'LIKE', "%{$search}%")
                        ->orWhere('cargo', 'LIKE', "%{$search}%");
                })
                // Búsqueda por tipo y estado de solicitud
                ->orWhere('tipo', 'LIKE', "%{$search}%")
                ->orWhere('estado', 'LIKE', "%{$search}%");
            });
        }

        /* ==========================
           FILTRO POR RANGO O FECHA ÚNICA
           Formatos esperados:
           - 2026-02-01 to 2026-02-15
           - 2026-02-10
        ===========================*/
        if ($request->filled('rango')) {

            // Flatpickr separa el rango con " to "
            $fechas = explode(' to ', $rango);

            // Si hay dos fechas, validamos cruce de rangos
            if (count($fechas) === 2) {
                $query->where(function ($q) use ($fechas) {
                    $q->whereDate('fecha_inicio', '<=', $fechas[1])
                      ->whereDate('fecha_fin', '>=', $fechas[0]);
                });
            } 
            // Si es una sola fecha, validamos que la solicitud incluya ese día
            else {
                $query->whereDate('fecha_inicio', '<=', $fechas[0])
                      ->whereDate('fecha_fin', '>=', $fechas[0]);
            }
        }

        // Ordenamos por más recientes y paginamos
        $solicitudes = $query->latest()->paginate(10);

        // Retornamos la vista
        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * DETALLE DE SOLICITUD
     * Cálculo de saldos antes y después
     */
    public function show($id)
    {
        // Cargamos la solicitud con su empleado
        $solicitud = Solicitud::with('empleado')->findOrFail($id);
        $empleado = $solicitud->empleado;

        // Antigüedad del empleado
        $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
        $aniosCumplidos = floor($fechaIngreso->diffInYears(now()));

        /* ==========================
           1. DERECHO GANADO
        ===========================*/
        $totalDerechoHistorico = 0;

        // Normalizamos el tipo de contrato
        $tipoContrato = strtolower($empleado->tipo_contrato);

        // Contrato permanente: suma histórica
        if ($tipoContrato === 'permanente') {
            for ($i = 1; $i <= $aniosCumplidos; $i++) {
                $anioBusqueda = ($i > 4) ? 4 : $i;

                $politica = PoliticaVacaciones::whereRaw(
                    'LOWER(tipo_contrato) = ?', ['permanente']
                )
                ->where('anio_antiguedad', $anioBusqueda)
                ->first();

                if ($politica) {
                    $totalDerechoHistorico += $politica->dias_anuales;
                }
            }
        }
        // Contrato anual: solo los días del contrato actual
        else {
            $politica = PoliticaVacaciones::whereRaw(
                'LOWER(tipo_contrato) = ?', [$tipoContrato]
            )->first();

            $totalDerechoHistorico = $politica
                ? $politica->dias_anuales
                : ($empleado->dias_vacaciones_anuales ?? 0);
        }

        /* ==========================
           2. DÍAS YA CONSUMIDOS
        ===========================*/
        $queryConsumo = DB::table('vacaciones')
            ->where('empleado_id', $empleado->id)
            ->where('estado', 'aprobado');

        // En contratos anuales solo contamos el contrato vigente
        if ($tipoContrato !== 'permanente') {
            $queryConsumo->where('fecha_inicio', '>=', $empleado->fecha_ingreso);
        }

        $diasConsumidosOficial = $queryConsumo->sum('dias_aprobados') ?? 0;

        /* ==========================
           3. CÁLCULO DE SALDOS
        ===========================*/
        if ($solicitud->estado === 'aprobado' && $solicitud->tipo === 'vacaciones') {
            $saldoActual = $totalDerechoHistorico - ($diasConsumidosOficial - $solicitud->dias);
            $nuevoSaldo  = $totalDerechoHistorico - $diasConsumidosOficial;
        } else {
            $saldoActual = $totalDerechoHistorico - $diasConsumidosOficial;
            $nuevoSaldo  = ($solicitud->tipo === 'vacaciones')
                ? ($saldoActual - $solicitud->dias)
                : $saldoActual;
        }

        return view('solicitudes.show', compact(
            'solicitud',
            'empleado',
            'saldoActual',
            'nuevoSaldo',
            'aniosCumplidos'
        ));
    }

    // 👉 El resto de métodos (procesar, rectificarTipo, update, calculoPermanente)
    // ya están correctamente estructurados y comentados a nivel de bloques
}

