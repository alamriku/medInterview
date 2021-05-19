<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class,'product_id', 'product_id');
    }
}
