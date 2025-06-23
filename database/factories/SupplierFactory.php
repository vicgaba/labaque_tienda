<?php

// --- Factory: SupplierFactory.php ---
// Ubicación: database/factories/SupplierFactory.php
// Comando para crear: php artisan make:factory SupplierFactory

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'country' => fake()->country(),
            'website' => fake()->url(),
            'active' => fake()->boolean(90), // Activo o inactivo
            'notes' => fake()->sentence(),
        ];
    }
}

