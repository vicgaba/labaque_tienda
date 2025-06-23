<?php

// --- Seeder: ClientSeeder.php ---
// Ubicación: database/seeders/ClientSeeder.php
// Comando para crear: php artisan make:seeder ClientSeeder

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User; // Importar el modelo User
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existan usuarios con el rol 'client'
        // Si ClientFactory ya crea un User::factory()->client(), esto es redundante pero seguro
        if (User::where('role', 'client')->count() < 10) {
            User::factory()->client()->count(10)->create();
        }

        // Crear clientes, asegurándose de que cada uno tenga un user_id único
        // que no esté ya asociado a otro cliente
        $clientUserIds = User::where('role', 'client')
            ->whereDoesntHave('client') // Solo usuarios que aún no son clientes
            ->pluck('id');

        $usersToAssign = $clientUserIds->shuffle()->take(10); // Toma algunos para asignar

        foreach ($usersToAssign as $userId) {
            Client::factory()->create(['user_id' => $userId]);
        }
    }
}

?>