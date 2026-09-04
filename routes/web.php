<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;

Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::post('/', [LoginController::class, 'login'])->name('login.post');

Route::get('/producto', [ProductoController::class, 'index'])->name('productos.index');


Route::post('/guardar-producto', [ProductoController::class, 'guardar'])->name('productos.store');
Route::put('/producto/update/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/eliminar-producto/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy');

Route::get('/inventario', [ProductoController::class, 'inventario'])->name('inventario.index');

