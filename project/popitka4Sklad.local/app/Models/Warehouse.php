<?php
// app/Models/Warehouse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'country',
        'city',
        'street',
        'house_number'
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function fromMovements()
    {
        return $this->hasMany(Movement::class, 'from_warehouse_id');
    }

    public function toMovements()
    {
        return $this->hasMany(Movement::class, 'to_warehouse_id');
    }

    /**
     * Полный адрес склада
     */
    public function getFullAddressAttribute()
    {
        return "{$this->country}, {$this->city}, {$this->street}, {$this->house_number}";
    }

    /**
     * Краткий адрес (город, улица)
     */
    public function getShortAddressAttribute()
    {
        return "{$this->city}, {$this->street}";
    }

    /**
     * Проверка есть ли координаты у склада
     */
    public function hasCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}