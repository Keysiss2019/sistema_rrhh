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
        // Obtiene el término de búsqueda desde el formulario
        $search = $request->input('search');

        /*
        | Consulta condicional:
        | Solo aplica el filtro si existe un término de búsqueda.
        */
        $empleados = Empleado::when($search, function ($query, $search) {
            return $query->where('id', 'LIKE', "%{$search}%")
                         ->orWhere('nombre', 'LIKE', "%{$search}%")
                         ->orWhere('apellido', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('estado', 'LIKE', "%{$search}%");
        })
        // Pagina los resultados (10 por página)
        ->paginate(10);

        // Obtiene todas las políticas de vacaciones
        $politicas = PoliticaVacaciones::all();

        // Retorna la vista con los datos necesarios
        return view('empleado.index', compact('empleados', 'politicas'));
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
        /*
        | Limpieza de datos:
        | Se eliminan espacios en blanco al inicio y final del nombre y apellido.
        */
        $request->merge([
            'nombre'   => trim($request->nombre),
            'apellido' => trim($request->apellido)
        ]);

        /*
        | Validaciones del formulario
        | Se asegura que los datos cumplan las reglas definidas
        | antes de ser almacenados en la base de datos.
        */
        $request->validate([
            'nombre'           => 'required|unique:empleados,nombre',
            'apellido'         => 'required',
            'email'            => 'required|email|unique:empleados,email',
            'cargo'            => 'required',
            'departamento'     => 'required',
            'fecha_ingreso'    => 'required|date',
            'fecha_baja'       => 'nullable|date',
            'fecha_nacimiento' => 'nullable|date',
            'estado'           => 'required|in:activo,inactivo',
            'contacto'         => 'nullable|string|max:20',
            'politica_id'      => 'required|exists:politicas_vacaciones,id',
        ], [
            // Mensajes personalizados de validación
            'nombre.unique' => 'Ya existe un empleado registrado con este nombre.',
            'email.unique'  => 'Este correo electrónico ya está en uso por otro empleado.',
        ]);

        // Obtiene la política de vacaciones seleccionada
        $politica = PoliticaVacaciones::findOrFail($request->politica_id);

        /*
        | Creación del empleado
        | Se asignan los valores recibidos desde el formulario.
        */
        $empleado = new Empleado();
        $empleado->nombre           = $request->nombre;
        $empleado->apellido         = $request->apellido;
        $empleado->email            = $request->email;
        $empleado->cargo            = $request->cargo;
        $empleado->departamento     = $request->departamento;
        $empleado->fecha_ingreso    = $request->fecha_ingreso;
        $empleado->fecha_nacimiento = $request->fecha_nacimiento;
        $empleado->fecha_baja       = $request->fecha_baja;
        $empleado->estado           = $request->estado;
        $empleado->jefe_inmediato   = $request->jefe_inmediato;
        $empleado->contacto         = $request->input('contacto') ?? 'N/A';
        $empleado->tipo_contrato    = $politica->tipo_contrato;
        $empleado->user_id          = null;

        // Guarda el empleado en la base de datos
        $empleado->save();

        /*
        | Carga de documentos laborales
        | Se recorren los archivos enviados y se almacenan
        | en el sistema de archivos junto con su registro en la BD.
        */
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $index => $archivo) {

                // Tipo de documento asociado
                $tipo = $request->tipos_documento[$index] ?? 'Documento Laboral';

                // Nombre único del archivo
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();

                // Almacenamiento en disco público
                $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

                // Registro del documento en la base de datos
                DocumentoLaboral::create([
                    'empleado_id'    => $empleado->id,
                    'tipo_documento' => $tipo,
                    'nombre_archivo' => $nombreArchivo,
                    'ruta_archivo'   => $ruta,
                ]);
            }
        }

        // Redirección con mensaje de éxito
        return redirect()->route('empleado.index')
                         ->with('success', 'Empleado creado correctamente.');
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
        // Obtiene el empleado o lanza error 404
        $empleado = Empleado::findOrFail($id);

        /*
        | Validaciones para actualización
        | Rule::unique()->ignore permite mantener valores existentes.
        */
        $request->validate([
            'nombre' => [
                'required',
                Rule::unique('empleados')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('empleados')->ignore($id),
            ],
            'politica_id' => 'required|exists:politicas_vacaciones,id',
            'contacto'    => 'nullable|string',
        ]);

        // Actualización de campos
        $empleado->nombre           = trim($request->nombre);
        $empleado->apellido         = trim($request->apellido);
        $empleado->email            = $request->email;
        $empleado->cargo            = $request->cargo;
        $empleado->departamento     = $request->departamento;
        $empleado->estado           = $request->estado;
        $empleado->contacto         = $request->input('contacto') ?? 'N/A';
        $empleado->fecha_nacimiento = $request->fecha_nacimiento;
        $empleado->fecha_baja       = $request->fecha_baja;
        $empleado->fecha_ingreso    = $request->fecha_ingreso;

        // Actualiza tipo de contrato según política
        $politica = PoliticaVacaciones::find($request->politica_id);
        if ($politica) {
            $empleado->tipo_contrato = $politica->tipo_contrato;
        }

        // Guarda cambios
        $empleado->save();

        return redirect()->route('empleado.index')
                         ->with('success', 'Empleado actualizado correctamente');
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
}
