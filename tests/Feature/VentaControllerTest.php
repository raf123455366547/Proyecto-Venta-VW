<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_checkout_creates_each_sale_and_decreases_inventory(): void
    {
        $user = User::factory()->create();
        $firstProduct = Producto::create([
            'nombre' => 'Filtro',
            'precio' => 125,
            'cantidad' => 5,
        ]);
        $secondProduct = Producto::create([
            'nombre' => 'Aceite',
            'precio' => 40,
            'cantidad' => 3,
        ]);

        $response = $this->actingAs($user)->post(route('ventas.store'), [
            'items' => [
                ['producto_id' => $firstProduct->id, 'cantidad' => 2],
                ['producto_id' => $secondProduct->id, 'cantidad' => 1],
            ],
        ]);

        $response->assertRedirectToRoute('ventas.index');
        $response->assertSessionHas('success', 'Venta registrada con éxito y stock actualizado.');
        $this->assertDatabaseCount('ventas', 2);
        $this->assertDatabaseHas('ventas', [
            'producto_id' => $firstProduct->id,
            'user_id' => $user->id,
            'cantidad' => 2,
        ]);
        $this->assertSame('250.00', Venta::query()->where('producto_id', $firstProduct->id)->firstOrFail()->total);
        $this->assertSame(3, $firstProduct->refresh()->cantidad);
        $this->assertSame(2, $secondProduct->refresh()->cantidad);
    }

    public function test_checkout_keeps_all_inventory_unchanged_if_one_product_has_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $availableProduct = Producto::create([
            'nombre' => 'Filtro',
            'precio' => 125,
            'cantidad' => 5,
        ]);
        $limitedProduct = Producto::create([
            'nombre' => 'Aceite',
            'precio' => 40,
            'cantidad' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('ventas.store'), [
            'items' => [
                ['producto_id' => $availableProduct->id, 'cantidad' => 2],
                ['producto_id' => $limitedProduct->id, 'cantidad' => 2],
            ],
        ]);

        $response->assertRedirectToRoute('ventas.index');
        $response->assertSessionHas('error', 'Stock insuficiente para Aceite. Disponible: 1.');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertSame(5, $availableProduct->refresh()->cantidad);
        $this->assertSame(1, $limitedProduct->refresh()->cantidad);
    }

    public function test_checkout_rejects_zero_quantity_without_changing_inventory(): void
    {
        $user = User::factory()->create();
        $product = Producto::create([
            'nombre' => 'Filtro',
            'precio' => 125,
            'cantidad' => 5,
        ]);

        $response = $this->actingAs($user)->post(route('ventas.store'), [
            'items' => [
                ['producto_id' => $product->id, 'cantidad' => 0],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.cantidad');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertSame(5, $product->refresh()->cantidad);
    }

    public function test_single_product_checkout_still_decreases_inventory(): void
    {
        $user = User::factory()->create();
        $product = Producto::create([
            'nombre' => 'Filtro',
            'precio' => 125,
            'cantidad' => 5,
        ]);

        $response = $this->actingAs($user)->post(route('ventas.store'), [
            'producto_id' => $product->id,
            'cantidad' => 1,
        ]);

        $response->assertRedirectToRoute('ventas.index');
        $this->assertDatabaseHas('ventas', [
            'producto_id' => $product->id,
            'user_id' => $user->id,
            'cantidad' => 1,
        ]);
        $this->assertSame(4, $product->refresh()->cantidad);
    }

    public function test_deleting_a_sale_returns_its_quantity_to_inventory(): void
    {
        $user = User::factory()->create();
        $product = Producto::create([
            'nombre' => 'Filtro',
            'precio' => 125,
            'cantidad' => 3,
        ]);
        $sale = Venta::create([
            'producto_id' => $product->id,
            'user_id' => $user->id,
            'cantidad' => 2,
            'precio_unitario' => 125,
            'total' => 250,
        ]);

        $response = $this->actingAs($user)->delete(route('ventas.destroy', $sale->id));

        $response->assertRedirectToRoute('ventas.index');
        $this->assertDatabaseMissing('ventas', ['id' => $sale->id]);
        $this->assertSame(5, $product->refresh()->cantidad);
    }

    public function test_sales_page_uses_only_products_that_exist_in_inventory(): void
    {
        $user = User::factory()->create();
        $availableProduct = Producto::create([
            'nombre' => 'Filtro del inventario',
            'precio' => 125,
            'cantidad' => 5,
        ]);
        Producto::create([
            'nombre' => 'Producto agotado',
            'precio' => 40,
            'cantidad' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('ventas.index'));

        $response->assertSee($availableProduct->nombre);
        $response->assertDontSee('Producto agotado');
        $response->assertDontSee('Filtro Aceite VW');
    }

    public function test_guest_is_redirected_to_login_before_accessing_sales(): void
    {
        $this->get(route('ventas.index'))->assertRedirectToRoute('login.index');
    }
}
