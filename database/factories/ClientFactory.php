<?php

// --- Factory: ClientFactory.php ---
// Ubicación: database/factories/ClientFactory.php
// Comando para crear: php artisan make:factory ClientFactory

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Crea un usuario asociado al cliente, si no existe
            'user_id' => User::factory()->client(), // Asegura que el usuario tenga rol 'client'
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'country' => fake()->country(),
            'instagram' => fake()->unique()->userName(), // Nombre de usuario único de Instagram            'phone' => fake()->phoneNumber(),
            'dni' => fake()->unique()->randomNumber(8), // DNI de 8 dígitos único
            'active' => true, // Por defecto, el cliente está activo
        ];
    }
}

?>