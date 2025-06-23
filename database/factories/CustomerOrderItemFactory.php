<?php

// --- Factory: CustomerOrderItemFactory.php ---
// Ubicación: database/factories/CustomerOrderItemFactory.php
// Comando para crear: php artisan make:factory CustomerOrderItemFactory

namespace Database\Factories;

use App\Models\CustomerOrderItem;
use App\Models\CustomerOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_order_id' => CustomerOrder::factory(), // Crea un pedido de cliente
            'product_id' => Product::factory(), // Crea un producto
            'quantity' => fake()->numberBetween(1, 5), // Cantidad de 1 a 5 unidades
            'price_at_order' => fake()->randomFloat(2, 10, 300), // Precio del producto al momento del pedido
            'active' => true, // Por defecto, el ítem está activo
            'notes' => fake()->optional()->sentence(), // Notas opcionales
        ];
    }
}

?>