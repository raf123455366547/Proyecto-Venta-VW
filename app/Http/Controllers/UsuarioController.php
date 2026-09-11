<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        if (strtolower(Auth::user()->role?->nombre ?? '') !== 'admin') {
            return redirect()->back();
        }

        $usuarios = User::with('role')
            ->where('id', '!=', 1)
            ->get();

        return view('users', compact('usuarios'));
    }

    public function store(Request $request)
    {
        if (strtolower(Auth::user()->role?->nombre ?? '') !== 'admin') {
            return redirect()->back();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (strtolower(Auth::user()->role?->nombre ?? '') !== 'admin') {
            return redirect()->back();
        }

        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'role_id' => 'required|exists:roles,id',
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->role_id = $request->role_id;

        if ($request->filled('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (strtolower(Auth::user()->role?->nombre ?? '') !== 'admin') {
            return redirect()->back();
        }

        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }
}