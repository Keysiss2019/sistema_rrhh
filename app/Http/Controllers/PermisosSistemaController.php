<?php

namespace App\Http\Controllers; //Define que esta clase pertenece al espacio de nombres de controladores.

use Illuminate\Http\Request;    //Importación de la clase Request.
use App\Models\Role;            // Importación del modelo Role.
use App\Models\RolModulo;       //Importación del modelo RolModulo.


class PermisosSistemaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Método index
    |--------------------------------------------------------------------------
    | Muestra la pantalla de administración de permisos del sistema.
    | Permite seleccionar un rol y ver los módulos habilitados.
    */
    public function index(Request $request)
    {
        // 1. Obtener todos los roles registrados en el sistema
        $roles = Role::all();

        // 2. Obtener el rol seleccionado desde la petición
        // Si no se envía role_id, se selecciona el primer rol por defecto
        $roleId = $request->get('role_id', $roles->first()->id);

        // 3. Obtener los permisos actuales del rol seleccionado
        // El resultado es un arreglo tipo:
        // ['seguridad' => 1, 'informes' => 0, ...]
        $permisos = RolModulo::where('role_id', $roleId)
            ->pluck('visible', 'modulo');

        // 4. Retornar la vista con los datos necesarios
        return view('seguridad.permisos', compact(
            'roles',
            'roleId',
            'permisos'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Método update
    |--------------------------------------------------------------------------
    | Guarda o actualiza los permisos de acceso a módulos
    | para un rol específico.
    */
    public function update(Request $request)
    {
        // 1. Validación de los datos recibidos del formulario
        $request->validate([
            'role_id' => 'required|exists:roles,id', // El rol debe existir
            'modulos' => 'array'                     // Los módulos deben ser un arreglo
        ]);

        // 2. Obtener el ID del rol seleccionado
        $roleId = $request->role_id;

        // 3. Definir la lista oficial de módulos del sistema
        // Esto evita permisos no autorizados o inexistentes
        $modulosSistema = [
            'seguridad',
            'permisos_laborales',
            'informes',
            'proyectos'
        ];

        // 4. Recorrer cada módulo y actualizar su visibilidad
        foreach ($modulosSistema as $modulo) {

            // updateOrCreate:
            // - Si el registro existe, lo actualiza
            // - Si no existe, lo crea automáticamente
            RolModulo::updateOrCreate(
                [
                    'role_id' => $roleId, // Rol asociado
                    'modulo'  => $modulo  // Nombre del módulo
                ],
                [
                    // Si el módulo viene marcado en el formulario → visible = 1
                    // Si no viene → visible = 0
                    'visible' => isset($request->modulos[$modulo]) ? 1 : 0
                ]
            );
        }

        // 5. Retornar a la vista anterior con mensaje de éxito
        return back()->with('success', 'Permisos actualizados correctamente');
    }
}
