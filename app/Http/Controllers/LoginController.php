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

        // Validamos las credenciales enviadas desde el formulario
        $credentials = $request->validate([
            'usuario'  => 'required|string',   // Campo usuario obligatorio
            'password' => 'required|string',   // Campo contraseña obligatorio
        ]);

        // Intentamos autenticar al usuario con las credenciales
        if (Auth::attempt($credentials)) {

            // Regeneramos la sesión por seguridad (previene session fixation)
            $request->session()->regenerate();

            // Obtenemos el usuario autenticado
            $user = Auth::user();

            // 1. Verificamos si el usuario está inactivo
            // Si no está activo, se cierra sesión inmediatamente
            if ($user->estado !== 'activo') {
                Auth::logout();
                return back()->withErrors([
                    'usuario' => 'Tu cuenta está inactiva. Contacta al administrador.'
                ]);
            }

            // 2. Verificamos si el usuario tiene contraseña temporal
            if ($user->debe_cambiar_password == 1) {

                // Redirigimos obligatoriamente a la vista de cambio de contraseña
                return redirect()->route('password.cambiar')
                    ->with('info', 'Debes actualizar tu contraseña temporal para continuar.');
            }

            // 3. Si todo está correcto, redirigimos al dashboard
            return redirect()->intended('/dashboard');
        }

        // Si el intento de login falla (credenciales incorrectas)
        return back()->withErrors([
            'usuario' => 'El usuario o la contraseña son incorrectos.',
        ])->onlyInput('usuario'); // Conserva el usuario ingresado
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
