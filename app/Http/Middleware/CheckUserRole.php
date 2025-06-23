<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     * Maneja una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login'); // Redirige a la página de login si no está autenticado
        }

        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Verifica si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->role, $roles)) {
            // Puedes redirigir a una página de "Acceso Denegado" o a la página principal
            // Por ahora, redirigiremos a la página de inicio con un mensaje de error.
            return redirect('/dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
            // O abort(403, 'Acceso Denegado.');
        }

        return $next($request);
    }
}