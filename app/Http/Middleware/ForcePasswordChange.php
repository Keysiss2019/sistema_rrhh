<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Si está logueado y tiene la marca activa
        if (Auth::check() && Auth::user()->debe_cambiar_password) {
            
            // 2. No lo bloquees si ya está en la ruta de cambiar clave o haciendo logout
            if (!$request->is('cambiar-password*') && !$request->is('logout')) {
                return redirect()->route('password.cambiar')
                    ->with('info', 'Debe cambiar su contraseña temporal para continuar.');
            }
        }

        return $next($request);
    }
}
