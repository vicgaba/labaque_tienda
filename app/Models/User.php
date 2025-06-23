<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Permitir asignación masiva para el rol
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Define la relación uno a uno con el modelo Client.
     * Un usuario puede ser un cliente.
     */
    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Define la relación uno a muchos con el modelo StockMovement.
     * Un usuario puede realizar muchos movimientos de stock.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Verifica si el usuario tiene el rol de administrador.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verifica si el usuario tiene el rol de vendedor.
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->role === 'user'; // Asumiendo que 'user' es el rol de vendedor
    }

    /**
     * Verifica si el usuario tiene el rol de cliente.
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}