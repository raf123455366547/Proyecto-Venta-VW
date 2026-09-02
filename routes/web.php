<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController; 
use App\Http\Controllers\LoginController; 
use App\Models\Producto;

//Rutas 

Route::get('/producto', function () {
    $productos = Producto::all();
    return view('welcome', compact('productos'));
})->name('productos.index');

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/', [LoginController::class, 'login'])->name('login.post');
Route::post('/guardar-producto', [ProductoController::class, 'guardar'])->name('productos.store');
Route::get('/editar-producto/{id}', [ProductoController::class, 'editar'])->name('productos.edit');
Route::post('/actualizar-producto/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/eliminar-producto/{id}', [ProductoController::class, 'destroy'])->name('productos.delete');