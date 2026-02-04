<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable =[
        'name', 'description'
    ];
    protected $casts=[
        'created_at'=>'datetime',
        'updated_at'=>'datetime',
    ];
        public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeWithActiveProductsCount($query)
    {
        return $query->withCount([
            'products as active_products_count' => function($query) {
                $query->whereHas('stocks', function($q) {
                    $q->where('quantity', '>', 0);
                });
            }
        ]);
    }

    public function scopeWithOutOfStockCount($query)
    {
        return $query->withCount([
            'products as out_of_stock_count' => function($query) {
                $query->whereDoesntHave('stocks', function($q) {
                    $q->where('quantity', '>', 0);
                });
            }
        ]);
    }
}
