<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PerfilController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => [
                'nullable',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Ese correo electrónico ya está registrado.',
            'current_password.required_with' => 'Ingresa tu contraseña actual para cambiarla.',
            'current_password.current_password' => 'La contraseña actual no coincide.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe incluir mayúsculas y minúsculas.',
            'password.letters' => 'La contraseña debe incluir letras.',
            'password.numbers' => 'La contraseña debe incluir números.',
            'password.symbols' => 'La contraseña debe incluir un símbolo.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'profile')
                ->withInput($request->except(['current_password', 'password', 'password_confirmation']));
        }

        $validated = $validator->validated();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return back()->with('profile_success', 'Tu perfil se actualizó correctamente.');
    }
}
