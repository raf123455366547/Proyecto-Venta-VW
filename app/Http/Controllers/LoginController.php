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
                'password' => $request->password,
            ]);
        }

        if ($user->password === $request->password || Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/producto');
        }

        return back()->with('error', 'La contraseña no coincide para este correo.');
    }
}