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
        "image",
        "description"
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function sub_sub_categories()
    {
        return $this->hasManyThrough(SubSubCategory::class, ProductCategory::class, 'sub_sub_category_id', "id");
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
