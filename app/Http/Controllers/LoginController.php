<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->correo)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Usuario Nuevo',
                'email' => $request->correo,
                'password' => Hash::make($request->password),
            ]);
        }

        if (Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            // Obtenemos el nombre del rol en minúsculas
            $userRole = strtolower($user->role?->nombre ?? '');

            // Redirección inteligente basada en el rol del usuario
            if ($userRole === 'admin') {
                return redirect()->route('usuarios.index');
            } elseif ($userRole === 'inventario') {
                return redirect()->route('inventario.index');
            } elseif ($userRole === 'ventas') {
                return redirect()->route('ventas.index');
            }

            // Si el usuario no tiene rol asignado o es un rol nuevo
            return redirect()->route('login.index')->with('error', 'Tu usuario no tiene un rol válido asignado.');
        }

        return back()->with('error', 'La contraseña no coincide para este correo.');
    }
}