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

    public function guardar(Request $request)
    {
        $nuevoProducto = new Producto();

        $nuevoProducto->nombre = $request->nombre;
        $nuevoProducto->precio = $request->precio;
        $nuevoProducto->cantidad = $request->cantidad;

        $nuevoProducto->save();

        return redirect()->back();
    }

    public function editar($id) 
    {
        $producto = Producto::find($id);
        return view('update_productos', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());

        return redirect()->back();
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        
        return redirect()->back(); 
    }
     public function inventario() {

    $productos = Producto::all();
    return view('inventario', compact('productos'));
     }
     
}