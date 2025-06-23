<?php

// --- Seeder: CustomerOrderSeeder.php ---
// Ubicación: database/seeders/CustomerOrderSeeder.php
// Comando para crear: php artisan make:seeder CustomerOrderSeeder

namespace Database\Seeders;

use App\Models\CustomerOrder;
use App\Models\Product; // Importar Product
use App\Models\Client; // Importar Client
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existan clientes y productos
        if (Client::count() === 0) {
            $this->call(ClientSeeder::class);
        }
        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }

        CustomerOrder::factory()->count(20)->create()->each(function ($order) {
            // Para cada orden, crear entre 1 y 5 ítems de orden
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            $totalAmount = 0;

            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $priceAtOrder = $product->price;
                $order->customerOrderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_at_order' => $priceAtOrder,
                    'active' => true, // Por defecto, el ítem está activo
                    'notes' => 'Venta a cliente',
                ]);

                $totalAmount += $quantity * $priceAtOrder;

                // Crear un movimiento de stock 'out' para esta venta
                $order->stockMovement()->create([
                    'product_id' => $product->id,
                    'user_id' => $order->client->user->id, // El usuario que hizo el pedido
                    'type' => 'out',
                    'quantity' => $quantity,
                    'reason' => 'Venta a cliente',
                    'source_type' => $order->getMorphClass(),
                    'source_id' => $order->id,
                ]);

                // Actualizar el stock del producto
                $product->decrement('stock', $quantity);
            }
            $order->update(['total_amount' => $totalAmount]);
        });
    }
}

?>