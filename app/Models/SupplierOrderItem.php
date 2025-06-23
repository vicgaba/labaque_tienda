<?php

// --- Modelo: SupplierOrderItem.php ---
// Representa la tabla 'supplier_order_items' y detalla los productos en cada compra a proveedor.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo

class SupplierOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_order_id',
        'product_id',
        'quantity',
        'cost_at_order',
        'active',
        'notes',
        'received_quantity',
    ];

    /**
     * Define la relación inversa uno a muchos con el modelo SupplierOrder.
     * Un ítem de pedido a proveedor pertenece a un pedido a proveedor.
     */
    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    /**
     * Define la relación inversa uno a muchos con el modelo Product.
     * Un ítem de pedido a proveedor pertenece a un producto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

?>