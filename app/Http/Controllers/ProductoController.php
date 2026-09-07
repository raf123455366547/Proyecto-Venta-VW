<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        return view('productos');
    }

    public function inventario()
    {
        $productos = Producto::all();
        return view('inventario', compact('productos'));
    }

    public function store(Request $request)
    {
        Producto::create($request->all());
        return back()->with('success', 'Producto guardado correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('editar_producto', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());
        return back()->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return back()->with('success', 'Producto eliminado correctamente.');
    }
}