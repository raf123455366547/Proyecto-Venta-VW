<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController; 

// Autenticación
Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login.index');
})->name('logout');

// Rutas Productos
Route::get('/producto', [ProductoController::class, 'index'])->name('productos.index');
Route::post('/producto/store', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/producto/edit/{id}', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/producto/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/producto/destroy/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy');

// Rutas Inventario
Route::get('/inventario', [ProductoController::class, 'inventario'])->name('inventario.index');