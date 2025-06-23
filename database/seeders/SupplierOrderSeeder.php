<?php

// --- Seeder: SupplierOrderSeeder.php ---
// Ubicación: database/seeders/SupplierOrderSeeder.php
// Comando para crear: php artisan make:seeder SupplierOrderSeeder

namespace Database\Seeders;

use App\Models\SupplierOrder;
use App\Models\Product; // Importar Product
use App\Models\Supplier; // Importar Supplier
use App\Models\User; // Importar User
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existan proveedores y productos
        if (Supplier::count() === 0) {
            $this->call(SupplierSeeder::class);
        }
        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }

        SupplierOrder::factory()->count(10)->create()->each(function ($order) {
            // Para cada orden, crear entre 1 y 3 ítems de orden
            $products = Product::inRandomOrder()->take(rand(1, 3))->get();
            $totalAmount = 0;

            foreach ($products as $product) {
                $quantity = rand(5, 20); // Más cantidad para pedidos a proveedor
                $costAtOrder = $product->price * 0.7; // Costo es 70% del precio de venta (ejemplo)
                $order->supplierOrderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost_at_order' => $costAtOrder,
                ]);

                $totalAmount += $quantity * $costAtOrder;

                // Crear un movimiento de stock 'in' para esta compra
                $order->stockMovement()->create([
                    'product_id' => $product->id,
                    'user_id' => User::where('role', 'admin')->first()->id, // Asignar a un admin
                    'type' => 'in',
                    'quantity' => $quantity,
                    'reason' => 'Compra a proveedor',
                    'source_type' => $order->getMorphClass(),
                    'source_id' => $order->id,
                ]);

                // Actualizar el stock del producto
                $product->increment('stock', $quantity);
            }
            $order->update(['total_amount' => $totalAmount]);
        });
    }
}

?>