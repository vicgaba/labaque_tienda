<?php

// --- Seeder: StockMovementSeeder.php ---
// Ubicación: database/seeders/StockMovementSeeder.php
// Comando para crear: php artisan make:seeder StockMovementSeeder
// Nota: Este seeder es más simple porque los movimientos de stock
// ya se crean automáticamente desde CustomerOrderSeeder y SupplierOrderSeeder
// al simular ventas y compras. Este seeder solo crearía movimientos
// "manuales" o por otras razones no directamente vinculadas a órdenes.

namespace Database\Seeders;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existan productos y al menos un usuario administrador
        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }
        if (User::where('role', 'admin')->count() === 0) {
            $this->call(UserSeeder::class); // Asegura que haya admins
        }

        // Crear algunos movimientos de stock manuales, no asociados a órdenes
        $adminUser = User::where('role', 'admin')->first();

        // Movimientos de ajuste o inventario inicial
        StockMovement::factory()->count(10)->create([
            'user_id' => $adminUser->id,
//            'source_type' => null, // No asociado a una orden específica
//            'source_id' => null,
            'reason' => fake()->randomElement(['Ajuste de inventario', 'Inventario inicial', 'Devolución de cliente sin orden']),
            'type' => fake()->randomElement(['in', 'out']),
        ])->each(function ($movement) {
            // Actualizar el stock del producto basado en el movimiento manual
            $product = $movement->product;
            if ($movement->type === 'in') {
                $product->increment('stock', $movement->quantity);
            } else {
                $product->decrement('stock', $movement->quantity);
            }
        });
    }
}