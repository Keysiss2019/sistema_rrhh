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
            if (
                !$request->routeIs('password.cambiar') &&
                !$request->routeIs('password.actualizar') &&
                !$request->routeIs('logout')
            ) {
                return redirect()->route('password.cambiar')
                    ->with('info', 'Debe cambiar su contraseña temporal para continuar.');
            }
        }

        // 2. SIEMPRE retornar la petición al siguiente middleware
        return $next($request);
    }
}
