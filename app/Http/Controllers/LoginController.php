<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
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
            'email_verified_at' => now(),
        ]);

        // 3. Asignación del rol por defecto 'ayudante_ventas'
        $defaultRole = Role::where('nombre', 'ayudante_ventas')->first();
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
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa una dirección de correo válida.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $credentials['correo'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Las credenciales proporcionadas no son correctas.');
        }

        if (! $user->hasVerifiedEmail()) {
            return back()->with('error', 'Debes verificar tu correo electrónico antes de ingresar.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $userRoles = $user->roles->pluck('nombre')->map(fn ($role) => trim(strtolower($role)))->toArray();

        if (array_intersect(['adm', 'admin'], $userRoles)) {
            return redirect()->route('usuarios.index');
        }

        if (array_intersect(['inventario', 'ayudante_inventario', 'inventario_ayudante', 'inventario ayudante'], $userRoles)) {
            return redirect()->route('inventario.index');
        }

        if (array_intersect(['ventas', 'ayudante_ventas', 'ventas_ayudante', 'ventas ayudante'], $userRoles)) {
            return redirect()->route('ventas.index');
        }

        return redirect()->route('login.index')->with('success', 'Sesión iniciada correctamente.');
    }

    /**
     * Muestra el formulario para solicitar la recuperación de contraseña.
     */
    public function showLinkRequestForm(): View
    {
        return view('password');
    }

    /**
     * Envía el correo con el token para restablecer la contraseña.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa una dirección de correo válida.',
        ]);

        $status = Password::sendResetLink($credentials);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'El enlace de recuperación fue guardado')
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Muestra el formulario para cambiar la contraseña mediante el token.
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('password')->with(['token' => $token, 'email' => $request->email]);
    }

    /**
     * Actualiza la contraseña del usuario en la base de datos.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
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
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un carácter especial (@, $, !, %, *, etc.).',
        ]);

        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.index')->with('success', 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.')
            : back()->withErrors(['email' => __($status)]);
    }
}
