<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['producto', 'usuario'])->latest()->get();
        $productos = Producto::where('cantidad', '>', 0)->get();

        return view('ventas', compact('ventas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $producto = Producto::findOrFail($request->producto_id);

            if ($producto->cantidad < $request->cantidad) {
                return redirect()->back()->with('error', 'Stock insuficiente para realizar la venta.');
            }

            $producto->cantidad -= $request->cantidad;
            $producto->save();

            Venta::create([
                'producto_id' => $producto->id,
                'user_id' => Auth::id(),
                'cantidad' => $request->cantidad,
                'precio_unitario' => $producto->precio,
                'total' => $producto->precio * $request->cantidad,
            ]);

            return redirect()->back()->with('success', 'Venta registrada con éxito y stock actualizado.');
        });
    }
}