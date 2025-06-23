<?php

// --- Seeder: SupplierSeeder.php ---
// Ubicación: database/seeders/SupplierSeeder.php
// Comando para crear: php artisan make:seeder SupplierSeeder

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory()->count(10)->create(); // Crear 10 proveedores aleatorios
    }
}

?>