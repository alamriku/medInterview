<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function productVariantPricesOne()
    {
        return $this->hasMany(ProductVariantPrice::class,'product_variant_one', 'id');
    }

    public function productVariantPricesTwo()
    {
        return $this->hasMany(ProductVariantPrice::class,'product_variant_two', 'id');
    }

    public function productVariantPricesThree()
    {
        return $this->hasMany(ProductVariantPrice::class,'product_variant_three', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class,'variant_id', 'id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class,'product_id', 'product_id');
    }

    public function scopeCombinationElement($query,$variantId,$productId)
    {
        return $query->where([
            'variant_id' => $variantId,
            'product_id' => $productId
        ]);
    }

    public function scopeProduct($query,$productId)
    {
        return $query->where('product_id', $productId);
    }
}
