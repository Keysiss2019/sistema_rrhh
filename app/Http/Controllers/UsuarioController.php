<?php

// Namespace del controlador
// Todos los controladores de Laravel se ubican en App\Http\Controllers
namespace App\Http\Controllers;

// Importamos los modelos que vamos a usar
use App\Models\User;                         // Modelo de usuarios
use App\Models\Empleado;                    // Modelo de empleados relacionados
use App\Models\Role;                       // Modelo de roles de usuario
use Illuminate\Http\Request;              // Para manejar solicitudes HTTP
use Illuminate\Support\Facades\Hash;      // Para encriptar contraseñas
use Illuminate\Support\Facades\Auth;      // Permite obtener el usuario autenticado con Auth::user()
use Illuminate\Support\Facades\DB;       // Facade para consultas directas a base de datos
use Illuminate\Support\Facades\Mail;     // Facade para envío de correos
use App\Mail\PasswordTemporalMail;       // Clase del correo de contraseña temporal
use Illuminate\Support\Str;              // Clase para generar cadenas aleatorias
use Illuminate\Validation\Rule;

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

            // Captura el texto ingresado
            $buscar = $request->buscar;

            // Busca por usuario o datos del empleado relacionado
            $query->where(function($q) use ($buscar) {

                // Buscar por nombre de usuario
                $q->where('usuario', 'like', "%$buscar%")

                  // Buscar dentro de la relación empleado
                  ->orWhereHas('empleado', function ($queryEmp) use ($buscar) {

                      // Buscar por nombre o apellido
                      $queryEmp->where('nombre', 'like', "%$buscar%")
                               ->orWhere('apellido', 'like', "%$buscar%");
                  });
            });
        }

        // Paginar resultados
        $usuarios = $query->paginate(8);

        // Empleados que NO tienen usuario asignado (vínculo inverso)
        $empleados = Empleado::whereDoesntHave('user')

            // Ordenar alfabéticamente
            ->orderBy('nombre', 'asc')

            // Obtener resultados
            ->get();

        // Obtener todos los roles
        $roles = Role::all();

        // Retornar vista principal
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

          // Iniciar transacción de base de datos
          DB::beginTransaction();

          // 2. GENERAR CONTRASEÑA (Forma compatible con todas las versiones)
          // Esto crea una clave de 8 caracteres mezcla de letras y números
          $passwordPlano = Str::random(8); 

          // 3. CREAR EL USUARIO
          $nuevoUsuario = User::create([

              // Nombre de usuario
              'usuario'               => $request->usuario,

              // Correo institucional
              'email'                 => $request->email,

              // Contraseña encriptada
              'password'              => Hash::make($passwordPlano),

              // Relación con empleado
              'empleado_id'           => $request->empleado_id,

               // Rol asignado
               'role_id'               => $request->role_id,

               // Estado inicial del usuario
               'estado'                => 'activo',

              // Obliga a cambiar contraseña al iniciar sesión
              'debe_cambiar_password' => 1,
           ]);

           // Si el usuario se creó correctamente
           if ($nuevoUsuario) {

             // 4. VINCULAR EMPLEADO
             DB::table('empleados')
                ->where('id', $request->empleado_id)
                ->update([

                    // Vincular con el usuario
                    'user_id' => $nuevoUsuario->id,

                    // Actualizar correo del empleado
                    'email'   => $request->email
                ]);

              // 5. ENVIAR CORREO
              Mail::to($nuevoUsuario->email)
                  ->send(new PasswordTemporalMail($nuevoUsuario, $passwordPlano));
           }

           // Confirmar transacción
           DB::commit();

          // Redireccionar con mensaje de éxito
          return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado. La contraseña fue enviada al correo.');

        } catch (\Exception $e) {

          // Revertir cambios si ocurre un error
          DB::rollBack();

          // ESTO TE MOSTRARÁ EL ERROR REAL SI FALLA ALGO INTERNO
          return back()
                ->with('error', 'Error técnico: ' . $e->getMessage())
                ->withInput();
        }
   }

    /**
     * ACTUALIZAR USUARIO
     */
    public function update(Request $request, $id)
    {
    // 1. Buscar usuario
    $usuario = User::with('empleado')->findOrFail($id);

    // 2. Definir roles permitidos
    $allowedRoleIds = Role::whereIn('nombre', config('role.editables', []))
        ->pluck('id')
        ->toArray();

    // 3. Validación MANUAL (Evita redirección automática de $request->validate)
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'usuario' => 'required|unique:users,usuario,' . $id,
        'role_id' => ['required', \Illuminate\Validation\Rule::in($allowedRoleIds)],
        'estado'  => 'required'
    ]);

    // 4. Si la validación falla o el rol no es permitido:
    if ($validator->fails() || !in_array($request->role_id, $allowedRoleIds)) {
        
        // CREA UN NUEVO CONTENEDOR DE ERRORES VACÍO
        $errores = new \Illuminate\Support\MessageBag();
    
        $errores->add('role_id', 'Rol ya esta ocupado');

        return redirect()->route('usuarios.index')
            ->withErrors($errores)
            ->withInput()
            ->with('abrir_edicion', [
                'id' => $usuario->id,
                'empleado' => $usuario->empleado->nombre . ' ' . $usuario->empleado->apellido,
            ]);
    }

    // 5. Lógica de actualización (si la validación pasa)
    $usuario->usuario = $request->usuario;
    $usuario->role_id = $request->role_id;
    $usuario->estado  = $request->estado;

    // Lógica de contraseña
    if ($request->has('reset_password')) {
        $passwordPlano = \Illuminate\Support\Str::random(6) . rand(10, 99); 
        $usuario->password = \Illuminate\Support\Facades\Hash::make($passwordPlano);
        $usuario->debe_cambiar_password = 1; 

        if ($usuario->email) {
            \Illuminate\Support\Facades\Mail::to($usuario->email)
                ->send(new \App\Mail\PasswordTemporalMail($usuario, $passwordPlano));
        }
    }

    $usuario->save();

    // Asegurar vínculo con empleado
    if ($usuario->empleado_id) {
        \Illuminate\Support\Facades\DB::table('empleados')
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

        // Guardar cambio
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
                ->update([

                    // Eliminar vínculo
                    'user_id' => null
                ]);

            // 3. Borramos al usuario
            $usuario->delete();

            return back()->with(
                'success',
                'El usuario ha sido eliminado y el empleado quedó libre.'
            );

        } catch (\Illuminate\Database\QueryException $e) {

            // 4. Si MySQL lanza un error (como el 1451), cae aquí
            return back()->with(
                'error',
                'No se puede eliminar: el usuario todavía está vinculado a registros importantes.'
            );
        
        } catch (\Exception $e) {

            // 5. Cualquier otro error inesperado
            return back()->with(
                'error',
                'Ocurrió un error inesperado al intentar eliminar.'
            );
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

        // Asignar nueva contraseña encriptada
        $user->password = Hash::make($request->password);

        // Quitar bandera de cambio obligatorio
        $user->debe_cambiar_password = 0;

        // Guardar cambios
        $user->save(); 

        // Redirigir directo al dashboard / sistema
        return redirect('/dashboard')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
