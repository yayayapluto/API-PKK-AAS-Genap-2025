<?php

namespace Database\Factories;

use App\CustomHelper\Formatter;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubSubCategory>
 */
class SubSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parentSubCategoryId = Category::query()->inRandomOrder()->pluck("id")->first();
        $name = fake()->unique()->words(3, true);
        $slug = Formatter::makeDash($name);
        return [
            "parent_sub_category_id" => $parentSubCategoryId,
            "slug" => $slug,
            "name" => $name
        ];
    }
}
