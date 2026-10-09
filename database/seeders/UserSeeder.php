<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear o buscar el rol 'admin'
        $adminRole = Role::firstOrCreate(['nombre' => 'admin']);

        // 2. Buscar o crear el usuario de prueba
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Asignarle el rol admin sin duplicar relaciones
        $user->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}