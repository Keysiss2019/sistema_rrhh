<?php

namespace App\Http\Controllers;                    // Namespace donde se encuentra el controlador

use App\Models\Proyecto;                           // Modelo de proyectos
use App\Models\Departamento;                       // Modelo de departamentos
use App\Models\Tarea;                              // Modelo de tareas
use App\Mail\TareaCorregidaMail;                   // Clase Mail para enviar correo cuando una tarea es corregida
use App\Mail\TareaAprobadaMail;                    // Clase Mail para enviar correo cuando una tarea es aprobada
use App\Notifications\TareaCorregidaNotification;  // Notificación para informar que una tarea fue corregida
use App\Models\Empleado;                           // Modelo de empleados
use Illuminate\Http\Request;                       // Clase Request para manejar formularios y peticiones HTTP
use Illuminate\Support\Facades\Auth;               // Facade para acceder al usuario autenticado
use Illuminate\Support\Facades\DB;                 // Facade para realizar consultas SQL
use Illuminate\Support\Facades\Storage;            // Facade para manejo de archivos y almacenamiento

class ProyectoController extends Controller
{
    /**
     * Normaliza el rol incluso cuando viene como relación/modelo JSON.
     * Convierte cualquier formato de rol a string limpio en minúsculas.
     */
    private function rolActual($user = null): string
    {
        $user = $user ?: Auth::user();
        $rolRaw = $user->rol ?? '';

        // Si el rol es un objeto (relación Eloquent o JSON)
        if (is_object($rolRaw)) {
            $rolRaw = $rolRaw->slug
                ?? $rolRaw->nombre
                ?? $rolRaw->name
                ?? $rolRaw->descripcion
                ?? '';
        }
        // Si el rol viene como array
        elseif (is_array($rolRaw)) {
            $rolRaw = $rolRaw['slug']
                ?? $rolRaw['nombre']
                ?? $rolRaw['name']
                ?? $rolRaw['descripcion']
                ?? '';
        }

        return strtolower(trim((string) $rolRaw));
    }

    /**
     * LISTADO PRINCIPAL DE PROYECTOS
     * Incluye filtro por rol + selección de proyecto + diagrama de asignaciones
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $rol = $this->rolActual($user); 

        // Query base con relaciones necesarias
        $query = Proyecto::with(['usuario', 'designados'])
            ->orderByDesc('created_at');

        // Si no es admin ni jefe, solo ve sus proyectos o donde está asignado
        if (!str_contains($rol, 'admin') && !str_contains($rol, 'jefe')) {
            $query->where(function ($q) use ($user) {
                $q->where('empleado_id', $user->id)
                  ->orWhereHas('designados', function ($d) use ($user) {
                      $d->where('user_id', $user->id);
                  });
            });
        }

        // OJO: aquí tienes duplicado paginate + get (esto puede ser problema)
        $proyectos = $query->get();
        $proyectos = $query->paginate(5)->appends($request->all());

        // ID del proyecto seleccionado desde URL
        $proyectoSeleccionadoId = $request->query('proyecto_id');

        // Buscar proyecto seleccionado dentro de la colección
        $proyectoSeleccionado = $proyectoSeleccionadoId
            ? $proyectos->firstWhere('id', $proyectoSeleccionadoId)
            : null;

        $diagramaAsignaciones = collect();

        // Construcción del diagrama de asignaciones
        if ($proyectoSeleccionado) {
            $diagramaAsignaciones = $proyectoSeleccionado->designados->map(function ($u) use ($proyectoSeleccionado) {

                // Obtener tareas por usuario usando asignado_user_id
                $tareasAsignadas = \App\Models\Tarea::where('proyecto_id', $proyectoSeleccionado->id)
                    ->where('asignado_user_id', $u->id)
                    ->get();

                return [
                    'usuario' => $u->usuario,
                    'es_encargado' => (int) $u->pivot->es_encargado === 1,
                    'tareas' => $tareasAsignadas,
                    'conteo' => $tareasAsignadas->count()
                ];
            });
        }

        return view('proyectos.index', [
            'proyectos' => $proyectos,
            'proyectoSeleccionado' => $proyectoSeleccionado,
            'diagramaAsignaciones' => $diagramaAsignaciones,
            'rol' => $rol,
            'departamentos' => Departamento::with('empleados')
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    /**
     * CREAR PROYECTO + EQUIPO + TAREAS
     */
    public function store(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'designados' => 'nullable|array',
            'tareas' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Crear proyecto principal
            $proyecto = Proyecto::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'empleado_id' => Auth::id(),
                'estado' => 'Pendiente',
                'progreso' => 0,
                'validado_jefe' => 0,
            ]);

            // IDs desde checkboxes del equipo
            $designadosIds = $request->input('designados', []);

            // IDs desde tareas (asignaciones indirectas)
            $tareas = $request->input('tareas', []);
            $idsDesdeTareas = collect($tareas)
                ->pluck('asignado_user_id')
                ->filter()
                ->toArray();

            // Unión de ambos equipos sin duplicados
            $equipoFinal = array_unique(array_merge($designadosIds, $idsDesdeTareas));

            // Guardar en tabla pivote proyecto_designados
            foreach ($equipoFinal as $userId) {
                $esEncargado = (int)$userId === (int)Auth::id() ? 1 : 0;

                $proyecto->designados()->attach($userId, [
                    'es_encargado' => $esEncargado
                ]);
            }

            // Guardar tareas
            if (!empty($tareas)) {
                foreach ($tareas as $tareaData) {
                    if (!empty($tareaData['titulo'])) {
                        $proyecto->tareas()->create([
                            'asignado_user_id' => $tareaData['asignado_user_id'],
                            'titulo' => $tareaData['titulo'],
                            'fecha_inicio' => $tareaData['fecha_inicio'],
                            'fecha_fin' => $tareaData['fecha_fin'],
                            'peso' => $tareaData['peso'] ?? 10,
                            'completada' => 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('proyectos.index', ['proyecto_id' => $proyecto->id])
                ->with('success', 'Proyecto institucional creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * OBTENER TAREAS DEL PROYECTO (AJAX)
     */
    public function getTareas($id)
    {
        $proyecto = Proyecto::with('tareas.responsable')->findOrFail($id);

        $tareas = $proyecto->tareas->map(function ($t) {
            return [
                'id' => $t->id,
                'titulo' => $t->titulo,
                'estado' => $t->estado,
                'peso' => $t->peso,
                'observaciones_empleado' => $t->observaciones_empleado,
                'asignado_user_id' => $t->asignado_user_id,
                'archivo_evidencia' => $t->archivo_evidencia,

                // URL del archivo si existe
                'archivo_url' => (!empty($t->archivo_evidencia) && Storage::disk('public')->exists($t->archivo_evidencia))
                    ? Storage::disk('public')->url($t->archivo_evidencia)
                    : null,

                // Responsable con fallback
                'responsable' => $t->responsable ? [
                    'id' => $t->responsable->id,
                    'name' => ($t->responsable->empleado) 
                        ? $t->responsable->empleado->nombre . ' ' . $t->responsable->empleado->apellido 
                        : $t->responsable->usuario,
                ] : null,
            ];
        });

        return response()->json(['tareas' => $tareas]);
    }

    /**
     * SUBIR DOCUMENTO DE TAREA
     */
    public function guardarDocumento(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf,doc,docx,jpg,png|max:5120',
            'tarea_id' => 'required|exists:tareas,id'
        ]);

        if ($request->hasFile('archivo')) {

            $archivo = $request->file('archivo');

            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();

            $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

            return response()->json([
                'success' => true,
                'mensaje' => 'Archivo guardado en public/storage/documentos',
                'url' => asset('storage/' . $ruta)
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => 'No se subió ningún archivo'
        ], 400);
    }

    /**
     * ENVIAR TAREA A REVISIÓN
     */
    public function enviarRevision(Request $request)
    {
        $tarea = Tarea::findOrFail($request->id);

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('documentos', 'public');
            $tarea->archivo_evidencia = $path;
        }

        $tarea->observaciones_empleado = $request->observaciones;
        $tarea->estado = 'En Revision';
        $tarea->save();

        return response()->json([
            'success' => true,
            'proyecto_id' => $tarea->proyecto_id
        ]);
    }

    /**
     * COMPLETAR TAREA
     */
    public function completarTarea(Request $request)
    {
        $tarea = Tarea::findOrFail($request->id);

        $tarea->completada = $request->completada;
        $tarea->observaciones_empleado = $request->observaciones;

        if ($request->completada == 1) {
            $tarea->updated_at = now();
        }

        $tarea->save();

        return response()->json([
            'success' => true,
            'proyecto_id' => $tarea->proyecto_id,
            'nuevo_progreso' => $tarea->proyecto->progreso
        ]);
    }

    /**
     * ACTUALIZAR ESTADO DE TAREA
     */
    public function actualizarEstadoTarea(Request $request)
    {
        $tarea = Tarea::findOrFail($request->id);

        if ($request->estado == 'En Revision') {
            $tarea->estado = 'En Revision';
            $tarea->observaciones_empleado = $request->notas;
            $tarea->fecha_entrega = now();
        }

        if ($request->estado == 'Completado') {
            $tarea->estado = 'Completado';
            $tarea->completada = 1;
        }

        $tarea->save();

        return response()->json([
            'success' => true,
            'estado_actual' => $tarea->estado,
            'proyecto_id' => $tarea->proyecto_id,
            'nuevo_progreso' => $tarea->proyecto->progreso
        ]);
    }

    /**
     * VALIDACIÓN DEL JEFE + ENVÍO DE CORREO
     */
    public function validarJefe(Request $request)
    {
        try {
            $tarea = Tarea::findOrFail($request->id);

            $tarea->estado = 'Completado';
            $tarea->completada = 1;
            $tarea->save();

            $usuario = \App\Models\User::find($tarea->asignado_user_id);
            $empleado = \App\Models\Empleado::where('user_id', $usuario->id)->first();

            if ($empleado && !empty($empleado->email)) {
                try {
                    \Mail::to($empleado->email)
                        ->send(new \App\Mail\TareaAprobadaMail($tarea));
                } catch (\Exception $e) {
                    \Log::error("Fallo al enviar correo IHCI: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'proyecto_id' => $tarea->proyecto_id,
                'nuevo_progreso' => $tarea->proyecto->progreso
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ACTUALIZAR PROGRESO DEL PROYECTO
     */
    public function updateProgress(Request $request, $id)
    {
        $proyecto = Proyecto::with('designados')->findOrFail($id);
        $rol = $this->rolActual(Auth::user());

        $esAdmin = str_contains($rol, 'admin');
        $esDuenio = ((int) $proyecto->empleado_id === (int) Auth::id());
        $esEncargado = $proyecto->designados->contains(function ($u) {
            return (int) $u->id === (int) Auth::id()
                && (int) $u->pivot->es_encargado === 1;
        });

        if (!$esAdmin && !$esDuenio && !$esEncargado) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para editar este proyecto.');
        }

        $request->validate([
            'progreso' => 'required|integer|min:0|max:100',
            'estado' => 'required|in:Pendiente,En Proceso, En Revision, Completado, Cancelado, Rechazado',
        ]);

        $proyecto->update([
            'progreso' => $request->progreso,
            'estado' => $request->estado,
        ]);

        return redirect()->back()->with('success', 'Avance actualizado.');
    }

    /**
     * VALIDAR PROYECTO (JEFE/ADMIN)
     */
    public function validar($id)
    {
        $rol = $this->rolActual(Auth::user());

        if (!str_contains($rol, 'admin') && !str_contains($rol, 'jefe')) {
            return redirect()->back()->with('error', 'No tienes permiso para validar este proyecto.');
        }

        $proyecto = Proyecto::findOrFail($id);

        $proyecto->update([
            'validado_jefe' => 1,
            'creado_por' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Proyecto validado con éxito.');
    }

    /**
     * SOLICITAR CORRECCIÓN DE TAREA + CORREO
     */
    public function solicitarCorreccion(Request $request)
    {
        try {
            $tarea = Tarea::findOrFail($request->id);

            $tarea->observaciones_jefe = $request->observaciones_jefe;
            $tarea->estado = 'Pendiente';
            $tarea->save();

            $usuario = \App\Models\User::find($tarea->asignado_user_id);
            $empleado = \App\Models\Empleado::where('user_id', $usuario->id)->first();

            if ($empleado && !empty($empleado->email)) {
                try {
                    \Mail::to($empleado->email)
                        ->send(new \App\Mail\TareaCorregidaMail($tarea));
                } catch (\Exception $mailEx) {
                    \Log::error("Error de correo: " . $mailEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'proyecto_id' => $tarea->proyecto_id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * OBTENER EMPLEADOS POR DEPARTAMENTO (AJAX)
     */
    public function getEmpleados($id)
    {
        return response()->json(
            \App\Models\Empleado::where('departamento_id', $id)
                ->where('estado', 'activo')
                ->get(['id', 'user_id', 'nombre', 'apellido'])
        );
    }

   /**
 * EDITAR PROYECTO (AJAX)
 * Muestra el proyecto con sus tareas y el equipo asignado de forma limpia.
 */

/**
 * Obtener datos del proyecto y sus tareas por ID (AJAX)
 */
public function edit($id)
{
    // 1. Ver qué hay en el proyecto
    $proyecto = \DB::table('proyectos')->where('id', $id)->first();

    // 2. BUSCAR TAREAS SIN FILTRAR (Queremos ver qué hay realmente en la tabla)
    // Esto traerá TODAS las tareas que existan, no solo las del ID 9
    $todasLasTareas = \DB::table('tareas')->get();

    // 3. Filtrar manualmente en PHP para depurar
    $tareasCoincidentes = $todasLasTareas->filter(function ($tarea) use ($id) {
        return (int)$tarea->proyecto_id === (int)$id;
    });

    return response()->json([
        'debug' => [
            'id_buscado' => (int)$id,
            'total_tareas_en_tabla' => $todasLasTareas->count(),
            'ejemplo_proyecto_id_en_tareas' => $todasLasTareas->take(1)->pluck('proyecto_id')
        ],
        'proyecto' => $proyecto,
        'tareas' => $tareasCoincidentes->values(),
        'colaboradores_actuales' => [] // Simplificado para probar
    ]);
}
    /**
     * ACTUALIZAR PROYECTO + TAREAS + EQUIPO
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'designados' => 'required|array|min:1',
            'tareas' => 'nullable|array',
            'tareas.*.titulo' => 'required|string',
            'tareas.*.asignado_user_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $proyecto = Proyecto::findOrFail($id);

            $proyecto->update($request->only('nombre', 'fecha_inicio', 'fecha_fin'));

            // sincroniza equipo
            $proyecto->designados()->sync($request->designados);

            $tareasMantenerIds = [];

            // actualizar o crear tareas
            if ($request->has('tareas')) {
                foreach ($request->tareas as $tareaData) {

                    if (isset($tareaData['id']) && !empty($tareaData['id'])) {
                        $tarea = Tarea::findOrFail($tareaData['id']);
                        $tarea->update($tareaData);
                        $tareasMantenerIds[] = $tarea->id;
                    } else {
                        $nuevaTarea = $proyecto->tareas()->create([
                            'titulo' => $tareaData['titulo'],
                            'asignado_user_id' => $tareaData['asignado_user_id'],
                            'fecha_inicio' => $tareaData['fecha_inicio'],
                            'fecha_fin' => $tareaData['fecha_fin'],
                            'peso' => $tareaData['peso'] ?? 10,
                            'estado' => 'Pendiente'
                        ]);

                        $tareasMantenerIds[] = $nuevaTarea->id;
                    }
                }
            }

            // eliminar tareas que ya no existen
            $proyecto->tareas()
                ->whereNotIn('id', $tareasMantenerIds)
                ->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '¡Proyecto y equipo actualizados con éxito!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}