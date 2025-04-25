<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubSubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "product_id" => Product::all()->pluck("id")->random(),
            "sub_sub_category_id" => SubSubCategory::all()->pluck("id")->random(),
        ];
    }
}
