<?php

// --- Factory: CustomerOrderFactory.php ---
// Ubicación: database/factories/CustomerOrderFactory.php
// Comando para crear: php artisan make:factory CustomerOrderFactory

namespace Database\Factories;

use App\Models\CustomerOrder;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(), // Crea un cliente si no existe
            'order_number' => fake()->unique()->numerify('ORD-#####'), // Número de pedido único
            'order_date' => fake()->dateTimeBetween('-1 year', 'now'), // Fecha del pedido
            'total_amount' => fake()->randomFloat(2, 50, 2000), // Monto total del pedido
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'shipping_address' => fake()->address(),
            'billing_address' => fake()->address(),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'tracking_number' => fake()->optional()->numerify('TRK-#####'),
            'notes' => fake()->optional()->sentence(),
            'balance_due' => fake()->randomFloat(2, 0, 500),
            'active' => fake()->boolean(90), // 90% de probabilidad de
        ];
    }
}

?>
