<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SubCategoryFactory> */
    use HasFactory;
    protected $fillable = [
        "parent_category_id",
        "slug",
        "name"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, "parent_category_id");
    }

    public function subSubCategories()
    {
        return $this->hasMany(SubSubCategory::class, "parent_sub_category_id");
    }
}
