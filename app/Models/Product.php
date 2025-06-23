<?php

// --- Modelo: Product.php ---
// Representa la tabla 'products' y almacena la información de los artículos deportivos.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany
use Illuminate\Database\Eloquent\Relations\MorphMany; // Importar MorphMany

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'size',
        'color',
        'brand',
        'image_path',
        'active',
    ];

    /**
     * Define la relación inversa uno a muchos con el modelo Category.
     * Un producto pertenece a una categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define la relación uno a muchos con el modelo CustomerOrderItem.
     * Un producto puede estar en muchos ítems de pedidos de clientes.
     */
    public function customerOrderItems(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /**
     * Define la relación uno a muchos con el modelo SupplierOrderItem.
     * Un producto puede estar en muchos ítems de pedidos a proveedores.
     */
    public function supplierOrderItems(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    /**
     * Define la relación polimórfica uno a muchos con el modelo StockMovement.
     * Un producto puede tener muchos movimientos de stock.
     */
    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'source'); // 'source' es el prefijo de las columnas morph
    }
}

?>