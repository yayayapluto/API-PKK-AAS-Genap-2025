<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        "slug",
        "seller_id",
        "name",
        "price",
        "stock",
        "images",
        "description"
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function productSubCategories()
    {
        return $this->hasMany(ProductSubCategory::class);
    }

    public function productSubSubCategories()
    {
        return $this->hasMany(ProductSubSubCategory::class);
    }
}
