<?php

// --- Factory: SupplierOrderFactory.php ---
// Ubicación: database/factories/SupplierOrderFactory.php
// Comando para crear: php artisan make:factory SupplierOrderFactory

namespace Database\Factories;

use App\Models\SupplierOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupplierOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(), // Crea un proveedor si no existe
            'total_amount' => fake()->randomFloat(2, 100, 5000), // Monto total de la compra
            'status' => fake()->randomElement(['pending', 'ordered', 'received', 'cancelled']),
            'order_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'expected_delivery_date' => fake()->dateTimeBetween('now', '+1 month'),
            'actual_delivery_date' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'tracking_number' => fake()->optional()->numerify('TRK-#####'), // Número de seguimiento opcional
            'order_number' => fake()->unique()->numerify('PO-#####'), // Número de orden único
            'notes' => fake()->optional()->sentence(),
            'active' => fake()->boolean(90), // 90% de probabilidad de
        ];
    }
}

?>
