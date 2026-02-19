<?php

// Namespace del controlador
// Todos los controladores de Laravel se ubican en App\Http\Controllers
namespace App\Http\Controllers;

// Importamos los modelos que vamos a usar
use App\Models\User;      // Modelo de usuarios
use App\Models\Empleado;  // Modelo de empleados relacionados
use App\Models\Role;      // Modelo de roles de usuario
use Illuminate\Http\Request; // Para manejar solicitudes HTTP
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas
use Illuminate\Support\Facades\Auth; // - Obtener el usuario autenticado (Auth::user)
use Illuminate\Support\Facades\DB; // Permite interactuar directamente con la base de datos usando el Query Builder y transacciones

class UsuarioController extends Controller
{
    /**
     * LISTADO DE USUARIOS + DATOS PARA EL OFFCANVAS
     */
    /**
     * LISTADO DE USUARIOS
     */
    public function index(Request $request)
    {
        // Consulta con relaciones
        $query = User::with(['empleado', 'rol']);

        // Buscador
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('usuario', 'like', "%$buscar%")
                  ->orWhereHas('empleado', function ($queryEmp) use ($buscar) {
                      $queryEmp->where('nombre', 'like', "%$buscar%")
                               ->orWhere('apellido', 'like', "%$buscar%");
                  });
            });
        }

        $usuarios = $query->paginate(8);

        // Empleados que NO tienen usuario asignado (vínculo inverso)
        $empleados = Empleado::whereDoesntHave('user')
            ->orderBy('nombre', 'asc')
            ->get();

        $roles = Role::all();

        return view('usuarios.index', compact('usuarios', 'empleados', 'roles'));
    }
 
    /**
    * GUARDAR USUARIO (CON CORREO INSTITUCIONAL)
    */
    public function store(Request $request)
    {
     // 1. Validaciones: Agregamos el campo 'email'
     $request->validate([
         'usuario'     => 'required|unique:users,usuario',
         'email'       => 'required|email|unique:users,email', // <-- Nuevo
         'empleado_id' => 'required|exists:empleados,id',
         'role_id'     => 'required|exists:roles,id',
         'password'    => 'required|min:6',
       ]);
    
    try {
        // Iniciamos una transacción para que se guarde en ambas tablas o en ninguna
        DB::beginTransaction();

        // 2. Creamos el usuario con el email que Tecnología escribió en el formulario
        $nuevoUsuario = User::create([
            'usuario'               => $request->usuario,
            'email'                 => $request->email, // El correo institucional asignado
            'password'              => bcrypt($request->password), 
            'empleado_id'           => $request->empleado_id,
            'role_id'               => $request->role_id,
            'estado'                => 'activo',
            'debe_cambiar_password' => $request->has('debe_cambiar_password') ? 1 : 0,
        ]);

        if ($nuevoUsuario) {
            // 3. ACTUALIZACIÓN EN EMPLEADOS (Vínculo + Correo)
            // Sincronizamos el ID del usuario y el nuevo correo en la ficha del empleado
            DB::table('empleados')
                ->where('id', $request->empleado_id)
                ->update([
                    'user_id' => $nuevoUsuario->id,
                    'email'   => $request->email // Así RRHH ya tiene el correo oficial
                ]);
        }

        DB::commit();
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado y correo institucional vinculado correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al crear usuario: ' . $e->getMessage())->withInput();
    }
    }

    /**
     * ACTUALIZAR USUARIO
     */
    public function update(Request $request, $id)
    {
        // Buscar usuario por ID o fallar
        $usuario = User::findOrFail($id);

        // Validaciones de actualización
        $request->validate([
            'usuario'  => 'required|unique:users,usuario,' . $id, // Ignora este usuario al validar unicidad
            'role_id'  => 'required',  // Rol obligatorio
            'estado'   => 'required',  // Estado obligatorio
            'password' => 'nullable|min:6' // Contraseña opcional, solo si se quiere cambiar
        ]);

        // Actualizamos campos básicos
        $usuario->usuario = $request->usuario;
        $usuario->role_id = $request->role_id;
        $usuario->estado  = $request->estado;

        // Si el administrador ingresó una nueva contraseña
        if ($request->filled('password')) {

           $passwordPlano = $request->password;

          $usuario->password = Hash::make($passwordPlano);
          $usuario->debe_cambiar_password = 1;
          $usuario->save();

            // Enviar correo con contraseña temporal
               if ($usuario->email) {
                  Mail::to($usuario->email)
                 ->send(new PasswordTemporalMail($usuario, $passwordPlano));
                }
            } 


        // Guardamos los cambios
        $usuario->save();

        // 4. ASEGURAR EL VÍNCULO EN LA TABLA EMPLEADOS
    // Buscamos al empleado asociado a este usuario (usando el empleado_id que ya tiene el usuario)
    if ($usuario->empleado_id) {
        DB::table('empleados')
            ->where('id', $usuario->empleado_id)
            ->update(['user_id' => $usuario->id]);
    }
        // Retorna con mensaje de éxito
        return back()->with('success', 'Usuario actualizado.');
    }

    /**
     * ACTIVAR / INACTIVAR USUARIO
     */
    public function toggleEstado($id)
    {
        // Buscar usuario
        $usuario = User::findOrFail($id);

        // Cambiar estado alternando entre activo/inactivo
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        // Retorna con mensaje de éxito
        return back()->with('success', 'Estado del usuario actualizado.');
    }

    /**
     * ELIMINAR USUARIO
     */
    public function destroy($id)
{
    try {
        // 1. Buscamos el usuario
        $usuario = User::findOrFail($id);

        // 2. Intentamos desvincular al empleado (user_id = null)
        // Esto es lo que evita el error de integridad
        DB::table('empleados')
            ->where('user_id', $id)
            ->update(['user_id' => null]);

        // 3. Borramos al usuario
        $usuario->delete();

        return back()->with('success', 'El usuario ha sido eliminado y el empleado quedó libre.');

    } catch (\Illuminate\Database\QueryException $e) {
        // 4. Si MySQL lanza un error (como el 1451), cae aquí
        return back()->with('error', 'No se puede eliminar: el usuario todavía está vinculado a registros importantes.');
        
    } catch (\Exception $e) {
        // 5. Cualquier otro error inesperado
        return back()->with('error', 'Ocurrió un error inesperado al intentar eliminar.');
    }
}

public function actualizarPassword(Request $request)
{
    // Validar que password y confirmación coincidan
    $request->validate([
        'password' => 'required|confirmed|min:8', // mejor 8 para seguridad
    ]);

    // Obtener usuario autenticado
    $user = Auth::user(); // ya logueado, no hace falta findOrFail

    // Asignar nueva contraseña y quitar flag
    $user->password = Hash::make($request->password); // Hash manual
    $user->debe_cambiar_password = 0;
    $user->save(); // Guarda en BD

   

    // Redirigir directo al dashboard / sistema
    return redirect('/dashboard')
        ->with('success', 'Contraseña actualizada correctamente.');
}

}
