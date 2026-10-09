<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'adm',
            'ventas',
            'inventario',
            'ayudante_ventas',
            'ayudante_inventario',
            'admin',
        ];

        foreach ($roles as $nombreRol) {
            Role::firstOrCreate(['nombre' => $nombreRol]);
        }
    }
}