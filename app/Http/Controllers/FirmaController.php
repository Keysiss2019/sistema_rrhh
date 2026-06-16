<?php

// Namespace donde se encuentra el controlador
namespace App\Http\Controllers;

// Manejo de solicitudes HTTP
use Illuminate\Http\Request;

// Modelo de firmas
use App\Models\Firma;

// Modelo de empleados
use App\Models\Empleado;


// Definición del controlador de firmas
class FirmaController extends Controller
{
    /**
     * MÉTODO: index
     * ---------------------------------
     * Muestra la vista principal de firmas.
     * Carga empleados y firmas existentes.
     */
    public function index()
    {
       $user = auth()->user();
    
    // Si es administrador, trae todas. Si no, solo la suya (filtrada por empleado_id)
    if ($user->isAdmin()) {
        $firmas = Firma::with('empleado')->get();
    } else {
        $firmas = Firma::with('empleado')->where('empleado_id', $user->empleado_id)->get();
    }

    $empleados = Empleado::orderBy('nombre')->get();

        // Retorna la vista 'firmas.index' enviando ambas variables
        return view('firmas.index', compact('empleados', 'firmas'));
    }


    /**
     * MÉTODO: store
     * ---------------------------------
     * Guarda o actualiza una firma.
     */
    public function store(Request $request)
    {
        // Validación de datos del formulario
        $request->validate([

            // El empleado debe existir en la tabla empleados
            'empleado_id' => 'required|exists:empleados,id',

            // La foto debe ser una imagen y no pesar más de 2MB
            'foto' => 'required|image|max:2048'
        ]);

        // Convierte la imagen subida a formato binario (BLOB)
        // Esto permite guardarla directamente en la base de datos
        $binario = file_get_contents($request->file('foto')->getRealPath());

        // updateOrCreate:
        // - Si ya existe una firma con ese empleado_id → la actualiza
        // - Si no existe → crea una nueva
        Firma::updateOrCreate(

            // Condición de búsqueda
            ['empleado_id' => $request->empleado_id],

            [
                // Imagen almacenada en binario
                'imagen_path' => $binario,

                // Estado activo
                'activo' => 1
            ]
        );

        // Si no es admin, validar que el empleado_id seleccionado sea el del usuario actual
    if (auth()->user()->role !== 'admin') {
        $esSuEmpleado = \App\Models\Empleado::where('id', $request->empleado_id)
                                            ->where('user_id', auth()->id())
                                            ->exists();
        if (!$esSuEmpleado) {
             return back()->with('error', 'No tienes permiso para modificar esta firma.');
        }
    }
        // Redirige hacia atrás con mensaje de éxito
        return back()->with('success', 'Firma actualizada correctamente.');
    }


    /**
     * MÉTODO: destroy
     * ---------------------------------
     * Elimina una firma por su ID.
     */
   public function destroy($id)
{
    // Verificamos que el usuario tenga rol de admin
   if (!auth()->user()->isAdmin()) {
        return back()->with('error', 'No autorizado.');
    }
    
    $firma = Firma::findOrFail($id);
    $firma->delete();
    return back()->with('success', 'Eliminado.');
}
}