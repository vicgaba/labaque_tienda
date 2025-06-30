<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'description', 
        'active'
    ];
    
    protected $casts = [
        'active' => 'boolean',
    ];
    
    // Scope para obtener solo categorías activas
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}