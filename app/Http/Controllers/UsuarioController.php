<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        if (!Auth::user()->roles()->where('nombre', 'admin')->exists()) {
            return redirect()->back();
        }

        $usuarios = User::with('roles')
            ->where('id', '!=', 1)
            ->get();

        $roles = Role::all();

        return view('users', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->roles()->where('nombre', 'admin')->exists()) {
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
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $usuario->roles()->attach($request->roles);

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->roles()->where('nombre', 'admin')->exists()) {
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
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;

        if ($request->filled('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        $usuario->roles()->sync($request->roles);

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->roles()->where('nombre', 'admin')->exists()) {
            return redirect()->back();
        }

        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }
}