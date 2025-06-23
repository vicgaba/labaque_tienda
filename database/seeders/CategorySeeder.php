<?php

// --- Seeder: CategorySeeder.php ---
// Ubicación: database/seeders/CategorySeeder.php
// Comando para crear: php artisan make:seeder CategorySeeder

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunas categorías fijas para empezar
        $categories = [
            'Zapatillas',
            'Remeras',
            'Pantalones',
            'Buzos y Camperas',
            'Accesorios',
            'Balones',
            'Equipamiento Deportivo',
        ];

        foreach ($categories as $categoryName) {
            Category::factory()->create([
                'name' => $categoryName,
                'description' => 'Categoría de ' . strtolower($categoryName) . '.',
            ]);
        }

        // Crear algunas categorías adicionales aleatorias
        Category::factory()->count(5)->create();
    }
}

?>
