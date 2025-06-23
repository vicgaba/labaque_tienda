<?php

// --- Factory: ProductFactory.php ---
// Ubicación: database/factories/ProductFactory.php
// Comando para crear: php artisan make:factory ProductFactory

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Único'];
        $colors = ['Rojo', 'Azul', 'Verde', 'Negro', 'Blanco', 'Gris'];
        $brands = ['Nike', 'Adidas', 'Puma', 'Under Armour', 'Reebok', 'Fila'];

        return [
            'category_id' => Category::factory(), // Crea una categoría si no existe
            'name' => fake()->words(2, true) . ' ' . fake()->randomElement(['Remera', 'Pantalón', 'Zapatillas', 'Buzo', 'Pelota']),
            'sku' => fake()->unique()->ean13(), // SKU como un código de barras
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500), // Precio entre 10 y 500 con 2 decimales
            'stock' => fake()->numberBetween(0, 200), // Stock entre 0 y 200
            'size' => fake()->randomElement($sizes),
            'color' => fake()->randomElement($colors),
            'brand' => fake()->randomElement($brands),
            'image_path' => 'products/' . fake()->image('public/storage/products', 640, 480, null, false), // Genera una imagen ficticia
            'active' => fake()->boolean(90), // 90% de probabilidad de ser activo
        ];
    }
}

?>
