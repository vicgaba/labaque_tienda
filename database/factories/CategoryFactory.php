<?php

// --- Factory: CategoryFactory.php ---
// Ubicación: database/factories/CategoryFactory.php
// Comando para crear: php artisan make:factory CategoryFactory

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' ' . fake()->word(), // Ej: "Ropa Deportiva", "Accesorios Fitness"
            'description' => fake()->sentence(),
            'active' => fake()->boolean(), // Activo o inactivo
        ];
    }
}

?>
