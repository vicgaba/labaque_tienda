<?php

// --- Modelo: CustomerOrder.php ---
// Representa la tabla 'customer_orders' y registra las ventas a clientes.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany
use Illuminate\Database\Eloquent\Relations\MorphOne; // Importar MorphOne

class CustomerOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'order_number',
        'order_date',
        'total_amount',
        'status',
        'shipping_address',
        'billing_address',
        'payment_method',
        'tracking_number',
        'notes',
        'balance_due',
        'active',
    ];


    /**
     * Define la relación inversa uno a muchos con el modelo Client.
     * Un pedido de cliente pertenece a un cliente.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Define la relación uno a muchos con el modelo CustomerOrderItem.
     * Un pedido de cliente tiene muchos ítems de pedido.
     */
    public function customerOrderItems(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /**
     * Define la relación polimórfica uno a uno con el modelo StockMovement.
     * Un pedido de cliente puede tener un movimiento de stock asociado (salida).
     */
    public function stockMovement(): MorphOne
    {
        return $this->morphOne(StockMovement::class, 'source');
    }
}

?>