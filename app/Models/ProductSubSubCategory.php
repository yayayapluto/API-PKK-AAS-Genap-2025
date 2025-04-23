<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubSubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSubSubCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        "product_id",
        "sub_sub_category_id"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function subSubCategory()
    {
        return $this->belongsTo(SubSubCategory::class);
    }
}
