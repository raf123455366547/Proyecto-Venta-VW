<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController; 
use App\Models\Producto;

Route::get('/', function () {
    $productos = Producto::all();
    return view('welcome', compact('productos'));
});

// guarda los datos
Route::post('/guardar-producto', [ProductoController::class, 'guardar'])->name('productos.store');
Route::get('/editar-producto/{id}', [ProductoController::class, 'editar'])->name('productos.edit');