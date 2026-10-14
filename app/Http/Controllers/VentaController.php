<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->get('buscar');

        // Historial de ventas para la vista
        $ventas = Venta::with(['producto', 'usuario'])
            ->when($buscar, function ($query, $buscar) {
                return $query->whereHas('producto', function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->get();

        // Mapeamos los productos con stock disponible para la pantalla táctil y carrito JS
        $productosDB = Producto::query()
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get()
            ->map(function (Producto $producto): array {
                return [
                    'id' => $producto->id,
                    'codigo' => '00'.$producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => (float) $producto->precio,
                    'stock' => (int) $producto->cantidad,
                    'icono' => '📦',
                ];
            });

        // Retornamos a la vista 'ventas' (resources/views/ventas.blade.php) enviando los productos formateados
        return view('ventas', compact('ventas', 'productosDB', 'buscar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:producto_id', 'array', 'min:1'],
            'items.*.producto_id' => ['required_with:items', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required_with:items', 'integer', 'min:1'],
            'producto_id' => ['required_without:items', 'integer', 'exists:productos,id'],
            'cantidad' => ['required_without:items', 'integer', 'min:1'],
        ]);

        $items = collect($validated['items'] ?? [[
            'producto_id' => $validated['producto_id'],
            'cantidad' => $validated['cantidad'],
        ]])
            ->groupBy('producto_id')
            ->map(fn ($productItems, $productId): array => [
                'producto_id' => (int) $productId,
                'cantidad' => (int) $productItems->sum('cantidad'),
            ])
            ->values();

        return DB::transaction(function () use ($items): RedirectResponse {
            $productos = Producto::query()
                ->whereKey($items->pluck('producto_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $producto = $productos->get($item['producto_id']);

                if (! $producto) {
                    return redirect()->route('ventas.index')
                        ->with('error', 'Uno de los productos ya no está disponible en el inventario.');
                }

                if ($producto->cantidad < $item['cantidad']) {
                    return redirect()->route('ventas.index')
                        ->with('error', "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->cantidad}.");
                }
            }

            foreach ($items as $item) {
                $producto = $productos->get($item['producto_id']);
                $producto->decrement('cantidad', $item['cantidad']);

                Venta::create([
                    'producto_id' => $producto->id,
                    'user_id' => Auth::id(),
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'total' => $producto->precio * $item['cantidad'],
                ]);
            }

            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada con éxito y stock actualizado.');
        });
    }

    public function destroy(int $id): RedirectResponse
    {
        return DB::transaction(function () use ($id): RedirectResponse {
            $venta = Venta::query()->lockForUpdate()->findOrFail($id);
            $producto = Producto::query()
                ->whereKey($venta->producto_id)
                ->lockForUpdate()
                ->first();

            if ($producto) {
                $producto->increment('cantidad', $venta->cantidad);
            }

            $venta->delete();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta eliminada con éxito y el stock ha sido devuelto.');
        });
    }
}
