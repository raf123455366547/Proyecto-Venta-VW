<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password as PasswordRule;

class LoginController extends Controller
{
    /**
     * Muestra la vista principal del inicio de sesión (Login).
     */
    public function index()
    {
        return view('login');
    }

    /**
     * Muestra el formulario de registro para nuevos usuarios.
     */
    public function registerIndex()
    {
        return view('register');
    }

    /**
     * Procesa el registro de un nuevo usuario en el sistema.
     */
    public function register(Request $request)
    {
        // 1. Validación de los campos del formulario de registro
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed', // Requiere que coincida con el campo password_confirmation
                PasswordRule::min(8)
                    ->mixedCase() // Requiere al menos una mayúscula y una minúscula
                    ->letters()   // Requiere al menos una letra
                    ->numbers()   // Requiere al menos un número
                    ->symbols(),  // Requiere al menos un carácter especial (@, $, !, %, *, etc.)
            ],
        ], [
            // Mensajes de error personalizados en español
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            // Reglas específicas de la contraseña desglosadas
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un carácter especial (@, $, !, %, *, etc.).',
        ]);

        // 2. Creación del usuario con verificación automática
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verificación inmediata
        ]);

        // 3. Asignación del rol por defecto 'ventas_ayudante'
        $defaultRole = Role::where('nombre', 'ventas_ayudante')->first();
        if ($defaultRole) {
            $user->roles()->attach($defaultRole->id);
        }

        // 4. Dispara el evento de registro
        event(new Registered($user));

        // 5. Redirección al login con mensaje de éxito
        return redirect()->route('login.index')->with('success', 'Cuenta creada exitosamente. Ya puedes iniciar sesión.');
    }

    /**
     * Autentica e inicia la sesión de un usuario.
     */
    public function login(Request $request)
    {
        // 1. Validación de las credenciales ingresadas
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ], [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa una dirección de correo válida.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 2. Búsqueda del usuario por su correo
        $user = User::where('email', $request->correo)->first();

        // 3. Verificación de la contraseña cifrada
        if ($user && Hash::check($request->password, $user->password)) {

            // Comprueba si el correo electrónico ya fue verificado
            if (!$user->hasVerifiedEmail()) {
                return back()->with('error', 'Debes verificar tu correo electrónico antes de ingresar.');
            }

            // Inicia la sesión y regenera el ID de sesión por seguridad
            Auth::login($user);
            $request->session()->regenerate();

            // Obtiene y normaliza los nombres de los roles del usuario
            $userRoles = $user->roles->pluck('nombre')->map(fn($r) => trim(strtolower($r)))->toArray();

            // Redirección del usuario según sus permisos/rol asignado
            if (in_array('admin', $userRoles)) {
                return redirect()->route('usuarios.index');
            } elseif (array_intersect(['inventario', 'inventario_ayudante', 'inventario ayudante'], $userRoles)) {
                return redirect()->route('inventario.index');
            } elseif (array_intersect(['ventas', 'ventas_ayudante', 'ventas ayudante'], $userRoles)) {
                return redirect()->route('ventas.index');
            }

            return redirect()->route('login.index')->with('error', 'Tu usuario no tiene un rol válido asignado.');
        }

        return back()->with('error', 'Las credenciales proporcionadas no son correctas.');
    }

    /**
     * Muestra el formulario para solicitar la recuperación de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('password'); // Apunta a password.blade.php
    }

    /**
     * Envía el correo con el token para restablecer la contraseña.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validación del correo del usuario
        $request->validate(
            ['email' => 'required|email'],
            [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'Ingresa una dirección de correo válida.',
            ]
        );

        // Envía el enlace de restablecimiento (se escribe en storage/logs/laravel.log)
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'El enlace de recuperación fue guardado')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Muestra el formulario para cambiar la contraseña mediante el token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('password')->with(['token' => $token, 'email' => $request->email]); // Apunta a password.blade.php
    }

    /**
     * Actualiza la contraseña del usuario en la base de datos.
     */
    public function resetPassword(Request $request)
    {
        // 1. Validación del token, correo y nueva contraseña
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            // Reglas específicas de la contraseña desglosadas
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un carácter especial (@, $, !, %, *, etc.).',
        ]);

        // 2. Restablecimiento de la contraseña en la base de datos
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password); // Cifra la nueva contraseña
                $user->save();
            }
        );

        // 3. Redirección final al login
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.index')->with('success', 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.')
            : back()->withErrors(['email' => __($status)]);
    }
}