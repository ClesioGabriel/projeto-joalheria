<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        // --- ADICIONAR ESTES ---
        'metal',
        'weight',
        'stone_type',
        'stone_size',
        'photo_path',
        'serial_number',
        'location',
        // -------------------------
    ];


    public function getPriceAttribute($value)
    {
        return (float) $value;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) $value; 
    }
    
    // Você pode adicionar casts para peso/quilates se necessário
    protected $casts = [
        'price' => 'float',
        'weight' => 'float', // Garante que o peso seja tratado como float
    ];
}