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

        // Permitir el paso directo a usuarios autenticados sin validar tabla de roles
        return $next($request);
    }
}