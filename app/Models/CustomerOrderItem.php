<?php

// --- Modelo: CustomerOrderItem.php ---
// Representa la tabla 'customer_order_items' y detalla los productos en cada venta.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo

class CustomerOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_order_id',
        'product_id',
        'quantity',
        'price_at_order',
        'active',
        'notes',
    ];

    /**
     * Define la relación inversa uno a muchos con el modelo CustomerOrder.
     * Un ítem de pedido de cliente pertenece a un pedido de cliente.
     */
    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    /**
     * Define la relación inversa uno a muchos con el modelo Product.
     * Un ítem de pedido de cliente pertenece a un producto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

?>