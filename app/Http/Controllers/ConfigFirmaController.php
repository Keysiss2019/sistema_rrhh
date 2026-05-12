<?php
namespace App\Http\Controllers;                  // Namespace donde se encuentra el controlador       

use Illuminate\Http\Request;                     // Importación de la clase Request para manejar formularios y peticiones HTTP
use Illuminate\Support\Facades\DB;               // Facade para consultas SQL

// Controlador encargado de la configuración
// del flujo de firmas del sistema
class ConfigFirmaController extends Controller
{
    /**
     * MÉTODO: index
     * ------------------------------------------------
     * Muestra la vista principal con todos
     * los pasos de firma configurados.
     */
    public function index()
    {
        // Obtiene todos los pasos configurados
        // ordenados de menor a mayor según el campo orden
        $pasosConfigurados = DB::table('flujo_firmas_config')
            ->orderBy('orden', 'asc')
            ->get();

        // Retorna la vista enviando la colección
        return view(
            'horas_extras.configuracion_firmas',
            compact('pasosConfigurados')
        );
    }

    /**
     * MÉTODO: store
     * ------------------------------------------------
     * Guarda un nuevo nivel/paso de firma.
     */
    public function store(Request $request)
    {
        // Inserta un nuevo registro en la tabla
        DB::table('flujo_firmas_config')->insert([

            // Nombre completo del paso
            'nombre_paso'  => $request->nombre_paso,

            // Nombre corto o abreviado
            'nombre_corto' => $request->nombre_corto,

            // Orden dentro del flujo de firmas
            'orden'        => $request->orden,

            // Icono visual del paso
            // Si no viene, usa fa-signature por defecto
            'icono'        => $request->icono ?? 'fa-signature',

            // Estado activo/inactivo
            'activo'       => $request->has('activo') ? 1 : 0
        ]);

        // Redirecciona con mensaje de éxito
        return redirect()
            ->back()
            ->with('success', 'Paso de firma agregado.');
    }

    /**
     * MÉTODO: toggle
     * ------------------------------------------------
     * Cambia el estado del paso:
     * activo ↔ inactivo
     */
    public function toggle($id)
    {
        // Busca el paso por ID
        $paso = DB::table('flujo_firmas_config')
            ->where('id', $id)
            ->first();

        // Cambia el estado actual al contrario
        DB::table('flujo_firmas_config')
            ->where('id', $id)
            ->update([
                'activo' => !$paso->activo
            ]);

        // Retorna a la página anterior
        return redirect()->back();
    }

    /**
     * MÉTODO: destroy
     * ------------------------------------------------
     * Elimina un nivel de firma.
     */
    public function destroy($id)
    {
        // Busca el paso de firma
        $paso = DB::table('flujo_firmas_config')
            ->where('id', $id)
            ->first();

        // Validar existencia
        if (!$paso) {

            return redirect()
                ->back()
                ->with('error', 'El nivel de firma no existe.');
        }

        // Intentamos eliminar el registro
        try {

            // Eliminación directa
            DB::table('flujo_firmas_config')
                ->where('id', $id)
                ->delete();

            // Mensaje de éxito
            return redirect()
                ->back()
                ->with('success', 'Nivel de firma eliminado correctamente.');

        } catch (\Exception $e) {

            // Captura cualquier error inesperado
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * MÉTODO: update
     * ------------------------------------------------
     * Actualiza un nivel/paso de firma existente.
     */
    public function update(Request $request, $id)
    {
        // Actualiza el registro según ID
        DB::table('flujo_firmas_config')
            ->where('id', $id)
            ->update([

                // Nuevo nombre del paso
                'nombre_paso'  => $request->nombre_paso,

                // Nuevo nombre corto
                'nombre_corto' => $request->nombre_corto,

                // Nuevo orden
                'orden'        => $request->orden,

                // Fecha de actualización
                'updated_at'   => now()
            ]);

        // Redirecciona con mensaje de éxito
        return redirect()
            ->back()
            ->with('success', 'El nivel de firma se actualizó correctamente.');
    }
}
