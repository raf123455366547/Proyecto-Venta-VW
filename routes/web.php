<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController; // 1. Importamos el controlador

// Esta es la ruta pasada 
Route::get('/', function () {
    return view('welcome');
});

// 2. ruta para GUARDAR los datos
Route::post('/guardar-producto', [ProductoController::class, 'guardar'])->name('productos.store');