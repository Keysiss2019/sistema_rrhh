<?php

// Namespace del controlador
// Indica que este controlador pertenece a App\Http\Controllers
namespace App\Http\Controllers;

// Importamos Request para manejar datos enviados por formularios
use Illuminate\Http\Request;

// Importamos Auth para manejar autenticación (login, logout, usuario actual)
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra la vista del formulario de inicio de sesión
    public function showLogin() {
        return view('auth.login');
    }

    // Procesa el intento de inicio de sesión
   public function login(Request $request) {

    // Validamos el campo usuario/email y password
    $request->validate([
        'usuario'  => 'required|string',
        'password' => 'required|string',
    ]);

    $login = $request->input('usuario'); // lo que el usuario ingresa
    $password = $request->input('password');

    // Buscamos al usuario por usuario o email
    $user = \App\Models\User::where('usuario', $login)
                ->orWhere('email', $login)
                ->first();

    // Verificamos que exista y la contraseña coincida
    if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {

        // Iniciamos sesión manualmente
        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

        // 1. Verificamos estado del usuario
        if ($user->estado !== 'activo') {
            Auth::logout();
            return back()->withErrors([
                'usuario' => 'Tu cuenta está inactiva. Contacta al administrador.'
            ]);
        }

        // 2. Validar estado del EMPLEADO
        if (!$user->empleado || 
            $user->empleado->estado !== 'activo' || 
            $user->empleado->fecha_baja !== null ||
            ($user->empleado->estado ?? 'activo') !== 'activo'
        ) {
            Auth::logout();
            return back()->withErrors([
                'usuario' => 'El acceso al sistema se encuentra deshabilitado. Contacta al administrador.'
            ]);
        }

        // 3. Forzar cambio de contraseña
        if ($user->debe_cambiar_password == 1) {
            return redirect()->route('password.cambiar')
                ->with('info', 'Debes actualizar tu contraseña temporal para continuar.');
        }

        // 4. Login exitoso
        return redirect()->intended('/dashboard');

    } else {
        // Credenciales incorrectas
        return back()->withErrors([
            'usuario' => 'El usuario o la contraseña son incorrectos.'
        ])->onlyInput('usuario');
    }
}


    // Cierra la sesión del usuario
    public function logout(Request $request) {

        // Cerramos la sesión
        Auth::logout();

        // Invalidamos la sesión actual
        $request->session()->invalidate();

        // Regeneramos el token CSRF por seguridad
        $request->session()->regenerateToken();

        // Redirigimos al formulario de login
        return redirect('/login');
    }
}
