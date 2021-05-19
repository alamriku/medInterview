<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    protected $guarded = [];

    public function productVariantPrices()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }
}
