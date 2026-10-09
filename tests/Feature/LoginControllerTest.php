<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_log_in_with_email_without_a_role(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.post'), [
            'correo' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('login.index');
        $response->assertSessionHas('success', 'Sesión iniciada correctamente.');
        $this->assertAuthenticatedAs($user);

        $this->get(route('login.index'))
            ->assertSee($user->email)
            ->assertDontSee(route('login.post'));
    }

    public function test_user_with_the_assigned_sales_assistant_role_can_open_sales(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['nombre' => 'ayudante_ventas']);
        $user->roles()->attach($role);

        $response = $this->post(route('login.post'), [
            'correo' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('ventas.index');
        $this->get(route('ventas.index'))->assertOk();
    }

    public function test_invalid_password_does_not_authenticate_user(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.post'), [
            'correo' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Las credenciales proporcionadas no son correctas.');
        $this->assertGuest();
    }
}
