<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function guardar(Request $request)
    {
        $nuevoProducto = new Producto();

        $nuevoProducto->nombre = $request->nombre;
        $nuevoProducto->precio = $request->precio;
        $nuevoProducto->cantidad = $request->cantidad;

        $nuevoProducto->save();

        return redirect()->back();
    }
}