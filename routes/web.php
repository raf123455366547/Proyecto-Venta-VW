<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController; 
use App\Http\Controllers\UsuarioController; 

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login.index');
})->name('logout');


Route::get('/producto', [ProductoController::class, 'index'])->name('productos.index');
Route::post('/producto/store', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/producto/edit/{id}', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/producto/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/producto/destroy/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy');
Route::get('/inventario', [ProductoController::class, 'inventario'])->name('inventario.index');
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::put('/usuarios/update/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/destroy/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
