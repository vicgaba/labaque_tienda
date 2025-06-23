<?php

// --- Modelo: StockMovement.php ---
// Representa la tabla 'stock_movements' y registra cada movimiento de inventario.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Importar HasFactory
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\MorphTo; // Importar MorphTo

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'active',
        'source_type', // Parte de la relación polimórfica
        'source_id',   // Parte de la relación polimórfica
    ];

    /**
     * Define la relación inversa uno a muchos con el modelo Product.
     * Un movimiento de stock pertenece a un producto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Define la relación inversa uno a muchos con el modelo User.
     * Un movimiento de stock puede ser realizado por un usuario (opcional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación polimórfica inversa.
     * Un movimiento de stock pertenece a una fuente (ej. CustomerOrder o SupplierOrder).
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
