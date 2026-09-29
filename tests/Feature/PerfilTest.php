<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('perfil.update'), [
            'name' => 'Nombre actualizado',
            'email' => 'actualizado@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('profile_success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre actualizado',
            'email' => 'actualizado@example.com',
        ]);
    }

    public function test_authenticated_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword1!']);

        $response = $this->actingAs($user)->put(route('perfil.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'OldPassword1!',
            'password' => 'NewPassword2!',
            'password_confirmation' => 'NewPassword2!',
        ]);

        $response->assertRedirect();
        $this->assertTrue(password_verify('NewPassword2!', $user->fresh()->password));
    }

    public function test_password_change_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('perfil.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'incorrecta',
            'password' => 'NewPassword2!',
            'password_confirmation' => 'NewPassword2!',
        ]);

        $response->assertSessionHasErrorsIn('profile', 'current_password');
        $this->assertTrue(password_verify('password', $user->fresh()->password));
    }

    public function test_profile_button_and_modal_are_rendered_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login.index'));

        $response->assertOk();
        $response->assertSee('Mi perfil');
        $response->assertSee('Guardar cambios');
        $response->assertSee(route('perfil.update'));
    }

    public function test_guest_cannot_update_a_profile(): void
    {
        $response = $this->withHeader('Accept', 'application/json')->put(route('perfil.update'), [
            'name' => 'Nombre',
            'email' => 'nombre@example.com',
        ]);

        $response->assertUnauthorized();
    }
}
