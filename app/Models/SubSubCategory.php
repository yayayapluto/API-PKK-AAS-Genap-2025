<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SubSubCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        "parent_sub_category_id",
        "slug",
        "name"
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, "parent_sub_category_id");
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id',                   // PK di SubCategory
            'id',                   // PK di Category
            'parent_sub_category_id', // FK di SubSubCategory
            'parent_category_id'      // FK di SubCategory
        );
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class, 
            ProductCategory::class, 
            'id', 
            "id",
            "product_id",
            "product_id"
        );
    }

    //solusi sementara
    public function productCategories() {
        return $this->hasMany(related: ProductCategory::class);
    }
}
