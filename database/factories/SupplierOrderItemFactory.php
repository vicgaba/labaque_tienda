<?php

// --- Factory: SupplierOrderItemFactory.php ---
// Ubicación: database/factories/SupplierOrderItemFactory.php
// Comando para crear: php artisan make:factory SupplierOrderItemFactory

namespace Database\Factories;

use App\Models\SupplierOrderItem;
use App\Models\SupplierOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupplierOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_order_id' => SupplierOrder::factory(), // Crea un pedido a proveedor
            'product_id' => Product::factory(), // Crea un producto
            'quantity' => fake()->numberBetween(5, 50), // Cantidad de 5 a 50 unidades
            'cost_at_order' => fake()->randomFloat(2, 5, 200), // Costo del producto al momento del pedido
            'active' => fake()->boolean(90), // 90% de probabilidad de que esté activo
            'notes' => fake()->optional()->sentence(), // Notas opcionales
            'received_quantity' => fake()->optional()->numberBetween(0, 50), //
        ];
    }
}

?>