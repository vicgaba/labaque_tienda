<?php

// --- Factory: StockMovementFactory.php ---
// Ubicación: database/factories/StockMovementFactory.php
// Comando para crear: php artisan make:factory StockMovementFactory

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use App\Models\CustomerOrder;
use App\Models\SupplierOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['in', 'out'];
        $type = fake()->randomElement($types);
        $reason = ($type == 'in') ? 'Compra a proveedor' : 'Venta';

        // Definir polimórficamente la fuente del movimiento
        $sourceModel = fake()->randomElement([
            CustomerOrder::factory()->create(),
            SupplierOrder::factory()->create(),
        ]);

        $morphClass = $sourceModel->getMorphClass();
        $id = $sourceModel->id;
/*
        dd([
        //'class' => get_class($sourceModel),
        'morph' => $sourceModel->getMorphClass(),
        'id' => $sourceModel->id,
        ]);
*/
        return [
            'product_id' => Product::factory(), // Crea un producto si no existe
            'user_id' => User::factory()->admin(), // Asigna un usuario admin para el movimiento
            'type' => $type,
            'quantity' => fake()->numberBetween(1, 20),
            'reason' => $reason,
            'active' => fake()->boolean(90), // 90% de probabilidad de que esté activo
            'source_type' => $morphClass, //$sourceModel->getMorphClass(), // Nombre completo de la clase del modelo fuente
            'source_id' => $id, //$sourceModel->id, // ID del modelo fuente
        ];
    }
}
