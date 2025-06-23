<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Pest\ArchPresets\Custom;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class, // Asegura que los clientes tengan usuarios asociados
            CategorySeeder::class,
            ProductSeeder::class, // Depende de CategorySeeder
            SupplierSeeder::class,
            CustomerOrderSeeder::class, // Depende de ClientSeeder y ProductSeeder
            SupplierOrderSeeder::class, // Depende de SupplierSeeder y ProductSeeder
            StockMovementSeeder::class, // Para movimientos manuales, las órdenes ya crean sus propios movimientos
        ]);
    }
}