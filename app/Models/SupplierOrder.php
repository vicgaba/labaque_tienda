<?php

// --- Modelo: SupplierOrder.php ---
// Representa la tabla 'supplier_orders' y registra las compras a proveedores.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany
use Illuminate\Database\Eloquent\Relations\MorphOne; // Importar MorphOne

class SupplierOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'total_amount',
        'status',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'tracking_number',
        'order_number',
        'notes',
        'active',
    ];

    /**
     * Define la relación inversa uno a muchos con el modelo Supplier.
     * Un pedido a proveedor pertenece a un proveedor.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Define la relación uno a muchos con el modelo SupplierOrderItem.
     * Un pedido a proveedor tiene muchos ítems de pedido.
     */
    public function supplierOrderItems(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    /**
     * Define la relación polimórfica uno a uno con el modelo StockMovement.
     * Un pedido a proveedor puede tener un movimiento de stock asociado (entrada).
     */
    public function stockMovement(): MorphOne
    {
        return $this->morphOne(StockMovement::class, 'source');
    }
}

?>