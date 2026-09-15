<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.index')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verifica si el usuario posee al menos uno de los roles permitidos en la ruta
        $hasRole = Auth::user()->roles()->whereIn('nombre', $roles)->exists();

        if (!$hasRole) {
            return redirect()->route('login.index')->with('error', 'No tienes permisos para acceder a este módulo.');
        }

        return $next($request);
    }
}