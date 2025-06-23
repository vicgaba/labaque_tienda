<?php

// --- Modelo: Category.php ---
// Representa la tabla 'categories' y define los rubros de los productos.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    /**
     * Define la relación uno a muchos con el modelo Product.
     * Una categoría puede tener muchos productos.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

?>