<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController; 
use App\Http\Controllers\UsuarioController; 
use App\Http\Controllers\VentaController;

// Rutas de Autenticación Básica
Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Registro de Usuarios
Route::get('/register', [LoginController::class, 'registerIndex'])->name('register.index');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');

// Recuperación de Contraseña
Route::get('/forgot-password', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

// Verificación de Correo Electrónico
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('login.index')->with('success', '¡Correo verificado con éxito! Ya puedes iniciar sesión.');
})->middleware(['signed'])->name('verification.verify');

// Cerrar Sesión
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login.index');
})->name('logout');


// Rutas Protegidas por Autenticación
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/update/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/destroy/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::middleware(['role:admin,inventario,inventario_ayudante'])->group(function () {
        Route::get('/producto', [ProductoController::class, 'index'])->name('productos.index');
        Route::post('/producto/store', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/producto/edit/{id}', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/producto/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::get('/inventario', [ProductoController::class, 'inventario'])->name('inventario.index');
    });

    Route::middleware(['role:admin,inventario'])->group(function () {
        Route::delete('/producto/destroy/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy');
    });

    Route::middleware(['role:admin,ventas,ventas_ayudante'])->group(function () {
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::post('/ventas/store', [VentaController::class, 'store'])->name('ventas.store');
    });

    Route::middleware(['role:admin,ventas'])->group(function () {
        Route::delete('/ventas/destroy/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy');
    });

});