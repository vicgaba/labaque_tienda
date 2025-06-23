<?php

// --- Seeder: UserSeeder.php ---
// Ubicación: database/seeders/UserSeeder.php
// Comando para crear: php artisan make:seeder UserSeeder

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario administrador
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Contraseña: password
        ]);

        // Crear un usuario vendedor
        User::factory()->user()->create([
            'name' => 'User User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'), // Contraseña: password
        ]);

        // Crear usuarios clientes
        User::factory()->client()->count(10)->create(); // 10 clientes con sus usuarios asociados

        // Crear usuarios genéricos (que se asignarán a clientes o se quedarán sin rol específico)
        User::factory()->count(20)->create();
    }
}

?>