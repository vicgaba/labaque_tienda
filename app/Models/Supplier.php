<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany

class Supplier extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'website',
        'notes',
        'active',
    ];
        

    /**
     * Define la relación uno a muchos con el modelo Product.
     * Un proveedor puede suministrar muchos productos.
     */
    public function supplierOrders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class);
    }
}
?>