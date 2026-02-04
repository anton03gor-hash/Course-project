<?php
// app/Models/Manufacturer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'city',
        'street',
        'house_number',
    ];

    /**
     * Получить товары производителя
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Получить полный адрес производителя
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->country,
            $this->city,
            $this->street,
            $this->house_number
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Получить сокращенный адрес (город, улица)
     */
    public function getShortAddressAttribute()
    {
        $parts = array_filter([
            $this->city,
            $this->street
        ]);
        
        return implode(', ', $parts);
    }
}