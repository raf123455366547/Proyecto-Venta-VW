<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function index() {
        return view('login');
    }

    public function login(Request $request) {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = DB::table('users')->where('email', $request->correo)->first();

        // Encripta lo que escribes en el formulario a MD5 para comparar con la BD
        if ($usuario && $usuario->password === md5($request->password)) {
            session(['usuario' => $usuario->name]);
            return redirect()->route('productos.index');
        }

        return back()->with('error', 'El correo o la contraseña son incorrectos.')->withInput();
    }
}