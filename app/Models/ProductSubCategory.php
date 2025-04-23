<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSubCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        "product_id",
        "sub_category_id"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
