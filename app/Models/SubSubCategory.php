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

    protected $hidden = [
        "id",
        "parent_sub_category_id"
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, "parent_sub_category_id");
    }
}
