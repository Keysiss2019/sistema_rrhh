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
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordTemporalMail;
use Illuminate\Support\Str;

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
      // 1. VALIDACIÓN 
      $request->validate([
          'usuario'     => 'required|unique:users,usuario',
          'email'       => 'required|email|unique:users,email',
          'empleado_id' => 'required|exists:empleados,id',
          'role_id'     => 'required|exists:roles,id',
       ]);

       try {
          DB::beginTransaction();

          // 2. GENERAR CONTRASEÑA (Forma compatible con todas las versiones)
          // Esto crea una clave de 8 caracteres mezcla de letras y números
          $passwordPlano = Str::random(8); 

          // 3. CREAR EL USUARIO
          $nuevoUsuario = User::create([
              'usuario'               => $request->usuario,
              'email'                 => $request->email,
              'password'              => Hash::make($passwordPlano),
              'empleado_id'           => $request->empleado_id,
               'role_id'               => $request->role_id,
               'estado'                => 'activo',
              'debe_cambiar_password' => 1,
           ]);

           if ($nuevoUsuario) {
             // 4. VINCULAR EMPLEADO
             DB::table('empleados')
                ->where('id', $request->empleado_id)
                ->update([
                    'user_id' => $nuevoUsuario->id,
                    'email'   => $request->email
                ]);

              // 5. ENVIAR CORREO
              Mail::to($nuevoUsuario->email)->send(new PasswordTemporalMail($nuevoUsuario, $passwordPlano));
           }

           DB::commit();
          return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado. La contraseña fue enviada al correo.');

        } catch (\Exception $e) {
          DB::rollBack();
          // ESTO TE MOSTRARÁ EL ERROR REAL SI FALLA ALGO INTERNO
          return back()->with('error', 'Error técnico: ' . $e->getMessage())->withInput();
        }
   }
    /**
     * ACTUALIZAR USUARIO
     */
    public function update(Request $request, $id)
    {
      $usuario = User::findOrFail($id);

      $request->validate([
         'usuario' => 'required|unique:users,usuario,' . $id,
         'role_id' => 'required',
         'estado'  => 'required'
       ]);

      // Actualizamos datos básicos
      $usuario->usuario = $request->usuario;
      $usuario->role_id = $request->role_id;
      $usuario->estado  = $request->estado;

      // LÓGICA DE CONTRASEÑA GENÉRICA
      if ($request->has('reset_password')) {
          // Generamos la clave (usamos random para compatibilidad)
          $passwordPlano = Str::random(6) . rand(10, 99); 
        
          $usuario->password = Hash::make($passwordPlano);
          $usuario->debe_cambiar_password = 1; // Forzamos cambio
        
          // Enviamos el correo (el mismo que usaste en store)
          if ($usuario->email) {
              Mail::to($usuario->email)->send(new PasswordTemporalMail($usuario, $passwordPlano));
            }
        }

       $usuario->save();

      // Asegurar vínculo con empleado
      if ($usuario->empleado_id) {
         DB::table('empleados')
            ->where('id', $usuario->empleado_id)
            ->update(['user_id' => $usuario->id]);
       }

       return back()->with('success', 'Usuario actualizado correctamente.');
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
