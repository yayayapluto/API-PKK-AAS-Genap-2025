<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected $fillable = [
        "slug",
        "name"
    ];

    protected $hidden = [
        "id"
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, "parent_category_id");
    }

    public function subSubCategories()
    {
        return $this->hasManyThrough(SubSubCategory::class, SubCategory::class, 'parent_category_id', 'parent_sub_category_id');
    }
}
