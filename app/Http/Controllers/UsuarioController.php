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

class UsuarioController extends Controller
{
    /**
     * LISTADO DE USUARIOS + DATOS PARA EL OFFCANVAS
     */
    public function index(Request $request)
    {
        // Creamos la consulta inicial incluyendo las relaciones 'empleado' y 'rol'
        $query = User::with(['empleado', 'rol']);

        // Buscador por usuario o nombre de empleado
        if ($request->filled('buscar')) {
            $query->where('usuario', 'like', '%' . $request->buscar . '%')
                  ->orWhereHas('empleado', function ($q) use ($request) {
                      // Filtra también por nombre de empleado relacionado
                      $q->where('nombre', 'like', '%' . $request->buscar . '%');
                  });
        }

        // Paginación de resultados: 8 usuarios por página
        $usuarios = $query->paginate(8);

        // --- NUEVO: Datos necesarios para el formulario "Nuevo Usuario" ---
        // Obtenemos empleados que todavía no tienen usuario, ordenados alfabéticamente
        $empleados = Empleado::whereDoesntHave('user')
            ->orderBy('nombre', 'asc')
            ->get();

        // Obtenemos todos los roles disponibles
        $roles = Role::all();

        // Enviamos las 3 variables a la vista index (usuarios, empleados y roles)
        return view('usuarios.index', compact('usuarios', 'empleados', 'roles'));
    }

    /**
     * GUARDAR USUARIO
     */
    public function store(Request $request)
    {
        // Validaciones de los campos obligatorios
        $request->validate([
            'usuario'     => 'required|unique:users,usuario', // Usuario único
            'empleado_id' => 'required|exists:empleados,id',  // Debe existir en empleados
            'role_id'     => 'required|exists:roles,id',      // Debe existir en roles
            'password'    => 'required|min:6',                // Contraseña mínima 6 caracteres
            'estado'      => 'required|in:activo,inactivo'   // Solo estos estados
        ]);

        // Crear usuario en la base de datos
        User::create([
            'usuario'               => $request->usuario,
            'password'              => bcrypt($request->password), // Encriptar contraseña
            'empleado_id'           => $request->empleado_id,
            'role_id'               => $request->role_id,
            'estado'                => $request->estado,
            'debe_cambiar_password' => $request->has('debe_cambiar_password') ? 1 : 0, 
            // Marcar si el usuario debe cambiar la contraseña al ingresar
        ]);

        // Redirige de vuelta al listado de usuarios con mensaje de éxito
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado. Se le solicitará cambio de contraseña al ingresar.');
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
            $usuario->password = Hash::make($request->password); 
            // Forzamos el cambio de contraseña al próximo inicio
            $usuario->debe_cambiar_password = 1; 
        }

        // Guardamos los cambios
        $usuario->save();

        // Retorna con mensaje de éxito
        return back()->with('success', 'Usuario actualizado. Se requerirá cambio de contraseña al ingresar.');
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
        // Buscar y eliminar usuario por ID
        User::findOrFail($id)->delete();

        // Retorna con mensaje de éxito
        return back()->with('success', 'Usuario eliminado correctamente.');
    }

public function actualizarPassword(Request $request)
{
    // Validar que password y confirmación coincidan
    $request->validate([
        'password' => 'required|confirmed|min:6',
    ]);

    // Obtener usuario autenticado
    $user = User::findOrFail(Auth::id());

    // Asignar nueva contraseña y quitar flag
    $user->password = $request->password; // Mutador hace Hash automáticamente
    $user->debe_cambiar_password = 0;
    $user->save(); // Guarda en BD

    // Logout automático
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('success', 'Contraseña actualizada. Inicia sesión nuevamente.');
}



}
