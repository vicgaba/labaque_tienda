<?php

// --- Modelo: Client.php ---
// Representa la tabla 'clients' y almacena información adicional de los clientes.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'dni',
        'city',
        'state',
        'zip_code',
        'country',
        'instagram',
        'notes',
        'active',
    ];

    /**
     * Define la relación inversa uno a uno con el modelo User.
     * Un cliente pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación uno a muchos con el modelo CustomerOrder.
     * Un cliente puede tener muchos pedidos.
     */
    public function customerOrders(): HasMany
    {
        return $this->hasMany(CustomerOrder::class);
    }
}

?>