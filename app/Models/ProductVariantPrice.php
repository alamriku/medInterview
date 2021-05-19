<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    public function productVariantOne()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one', 'id');
    }

    public function productVariantTwo()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two', 'id');
    }

    public function productVariantThree()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function scopeItem($query,$productId)
    {
        return $query->where('product_id', $productId);
    }

}
