<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController; 
use App\Http\Controllers\UsuarioController; 
use App\Http\Controllers\VentaController;

// 1. RUTAS PÚBLICAS (Sin autenticación)
Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login.index');
})->name('logout');


Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/update/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/destroy/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::middleware(['role:admin,inventario'])->group(function () {
        Route::get('/producto', [ProductoController::class, 'index'])->name('productos.index');
        Route::post('/producto/store', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/producto/edit/{id}', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/producto/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/producto/destroy/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy');
        Route::get('/inventario', [ProductoController::class, 'inventario'])->name('inventario.index');
    });

    Route::middleware(['role:admin,ventas'])->group(function () {
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::post('/ventas/store', [VentaController::class, 'store'])->name('ventas.store');
    });

});