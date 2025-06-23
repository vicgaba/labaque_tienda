<?php

// --- Seeder: ProductSeeder.php ---
// Ubicación: database/seeders/ProductSeeder.php
// Comando para crear: php artisan make:seeder ProductSeeder

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category; // Importar el modelo Category
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existan categorías antes de crear productos
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        Product::factory()->count(50)->create(); // Crear 50 productos aleatorios
    }
}

?>