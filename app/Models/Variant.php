<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class,'variant_id', 'id');
    }
}
