<?php

/*
 Namespace del controlador
*/
namespace App\Http\Controllers;

/*
 Importación de modelos
*/
use App\Models\Empleado;
use App\Models\DocumentoLaboral;
use App\Models\PoliticaVacaciones;
use App\Models\Departamento;

/*
|--------------------------------------------------------------------------
| Importación de clases del framework
|--------------------------------------------------------------------------
| Request: permite acceder a los datos enviados desde formularios.
| Storage: permite manejar archivos en el sistema de almacenamiento.
| Rule: se utiliza para validaciones avanzadas (unique en update).
*/
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Librería para manejo avanzado de fechas y horas (parseo, formato, cálculos, diferencias de tiempo, etc.)
use Illuminate\Support\Facades\DB; // Permite interactuar directamente con la base de datos usando el Query Builder y transacciones


/*
|--------------------------------------------------------------------------
| Controlador EmpleadoController
|--------------------------------------------------------------------------
| Este controlador gestiona todas las operaciones CRUD de los empleados:
| - Listado
| - Creación
| - Actualización
| - Eliminación
*/
class EmpleadoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Método index
    |--------------------------------------------------------------------------
    | Muestra el listado de empleados.
    | Permite realizar búsquedas por diferentes campos y
    | utiliza paginación para mejorar el rendimiento.
    */
 public function index(Request $request)
{
    $search = $request->input('search');

    $empleados = Empleado::when($search, function ($query, $search) {
            return $query->where('nombre', 'LIKE', "%{$search}%")
                         ->orWhere('apellido', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('cargo', 'LIKE', "%{$search}%");
        })
        ->paginate(10);

    $departamentos = Departamento::with('jefeEmpleado')->get();

    // ✅ Traemos todas las políticas
    $politicas = PoliticaVacaciones::all();

    return view('empleado.index', compact('empleados', 'departamentos', 'politicas'));
}



    /*
    |--------------------------------------------------------------------------
    | Método store
    |--------------------------------------------------------------------------
    | Se encarga de registrar un nuevo empleado en la base de datos,
    | incluyendo validaciones, asignación de política y carga de documentos.
    */
    public function store(Request $request)
{
    $request->merge([
        'nombre'   => trim($request->nombre),
        'apellido' => trim($request->apellido)
    ]);

    $request->validate([
        'nombre'           => 'required|unique:empleados,nombre',
        'apellido'         => 'required',
        // El email ahora es opcional (nullable) para RRHH
        'email'            => 'nullable|email|unique:empleados,email', 
        'cargo'            => 'required',
        'departamento'     => 'required|exists:departamentos,id',
        'fecha_ingreso'    => 'required|date',
        'fecha_nacimiento' => 'nullable|date',
        'politica_id'      => 'required|exists:politicas_vacaciones,id',
    ], [
        'nombre.unique' => 'Ya existe un empleado registrado con este nombre.',
        'email.unique'  => 'Este correo electrónico ya está en uso.',
    ]);

    $politica = PoliticaVacaciones::findOrFail($request->politica_id);

    $empleado = new Empleado();
    $empleado->nombre           = $request->nombre;
    $empleado->apellido         = $request->apellido;
    // Si RRHH no pone email, se guarda como null hasta que Tecnología cree el usuario
    $empleado->email            = $request->email; 
    $empleado->cargo            = $request->cargo;
    $empleado->departamento_id  = $request->departamento;
    $empleado->fecha_ingreso    = $request->fecha_ingreso;
    $empleado->fecha_nacimiento = $request->fecha_nacimiento;
    $empleado->estado           = 'activo';
    $empleado->contacto         = $request->input('contacto') ?? 'N/A';
    $empleado->tipo_contrato    = $politica->tipo_contrato;
    $empleado->dias_vacaciones_anuales = $politica->dias_anuales;

    $empleado->save();

    // Lógica de documentos (se mantiene igual...)
    if ($request->hasFile('documentos')) {
        foreach ($request->file('documentos') as $index => $archivo) {
            $tipo = $request->tipos_documento[$index] ?? 'Documento Laboral';
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

            DocumentoLaboral::create([
                'empleado_id'    => $empleado->id,
                'tipo_documento' => $tipo,
                'nombre_archivo' => $nombreArchivo,
                'ruta_archivo'   => $ruta,
            ]);
        }
    }

    return redirect()->route('empleado.index')->with('success', 'Empleado creado. Pendiente creación de usuario por TI.');
}

    /*
    |--------------------------------------------------------------------------
    | Método update
    |--------------------------------------------------------------------------
    | Actualiza la información de un empleado existente.
    | Se ignoran los valores únicos del propio registro.
    */
 public function update(Request $request, $id)
{
    $empleado = Empleado::findOrFail($id);

    $request->validate([
        'nombre' => ['required', Rule::unique('empleados')->ignore($id)],
        'apellido' => 'required',
        'email' => ['nullable', 'email', Rule::unique('empleados')->ignore($id)],
        'departamento' => 'required|exists:departamentos,id',
        'politica_id' => 'required|exists:politicas_vacaciones,id',
        'fecha_ingreso' => 'required|date', // Validar fechas
        'fecha_baja' => 'nullable|date',
    ]);

    // Asignación manual
    $empleado->nombre          = trim($request->nombre);
    $empleado->apellido        = trim($request->apellido);
    $empleado->email           = $request->email;
    $empleado->cargo           = $request->cargo;
    $empleado->departamento_id = $request->departamento; // Asegúrate que en la DB se llame así
    $empleado->estado          = $request->estado;
    $empleado->contacto        = $request->input('contacto') ?? 'N/A';
    
    // ESTO FALTABA:
    $empleado->fecha_ingreso   = $request->fecha_ingreso;
    $empleado->fecha_baja      = $request->fecha_baja;

    // Sincronización con Usuario
    if ($empleado->user_id && $request->filled('email')) {
        DB::table('users')
            ->where('id', $empleado->user_id)
            ->update(['email' => $request->email]);
    }

    // Lógica de política
    $politicaNueva = PoliticaVacaciones::findOrFail($request->politica_id);
    if ($empleado->tipo_contrato !== $politicaNueva->tipo_contrato) {
        $empleado->tipo_contrato = $politicaNueva->tipo_contrato;
        $empleado->dias_vacaciones_anuales = $politicaNueva->dias_anuales;
        $empleado->fecha_cambio_contrato = now()->format('Y-m-d');
    }

    // Guardar cambios
    $empleado->save();

    return redirect()->route('empleado.index')->with('success', 'Empleado actualizado correctamente.');
}


    /*
    |--------------------------------------------------------------------------
    | Método destroy
    |--------------------------------------------------------------------------
    | Elimina un empleado junto con sus documentos asociados.
    */
    public function destroy($id)
    {
        // Obtiene el empleado
        $empleado = Empleado::findOrFail($id);

        /*
        | Eliminación de documentos físicos y registros
        */
        foreach ($empleado->documentos as $doc) {
            Storage::disk('public')->delete(
                str_replace('storage/', '', $doc->ruta_archivo)
            );
            $doc->delete();
        }

        // Elimina el empleado
        $empleado->delete();

        return redirect()->route('empleado.index')
                         ->with('success', 'Empleado eliminado correctamente.');
    }

    /**
 * Cambia el estado del empleado (Activo/Inactivo)
 */
public function cambiarEstado($id)
{
    // 1. Busca al empleado
    $empleado = Empleado::findOrFail($id);

    // 2. Determina el nuevo estado
    if ($empleado->estado === 'activo') {
        $empleado->estado = 'inactivo';
        // Opcional: Registrar la fecha de salida automáticamente
        $empleado->fecha_baja = Carbon::now()->format('Y-m-d');
        $mensaje = "El empleado " . $empleado->nombre . " ha sido desactivado.";
    } else {
        $empleado->estado = 'activo';
        // Al reactivarlo, limpiamos la fecha de baja
        $empleado->fecha_baja = null; 
        $mensaje = "El empleado " . $empleado->nombre . " ha sido activado correctamente.";
    }

    // 3. Guarda los cambios
    $empleado->save();

    // 4. Redirecciona con mensaje
    return redirect()->back()->with('success', $mensaje);
}
}
