<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $productos = Producto::when($buscar, function ($query, $buscar) {
            return $query->where('nombre', 'LIKE', "%{$buscar}%");
        })->get();

        return view('productos', compact('productos'));
    }

    public function inventario(Request $request)
    {
        $buscar = $request->get('buscar');

        $productos = Producto::when($buscar, function ($query, $buscar) {
            return $query->where('nombre', 'LIKE', "%{$buscar}%");
        })->get();

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
        return back(); // Al usar modales, regresamos a la vista actual
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