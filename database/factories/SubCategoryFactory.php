<?php

namespace Database\Factories;

use App\CustomHelper\Formatter;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parentCategoryId = Category::query()->inRandomOrder()->pluck("id")->first();
        $name = fake()->unique()->words(2, true);
        $slug = Formatter::makeDash($name);
        return [
            "parent_category_id" => $parentCategoryId,
            "slug" => $slug,
            "name" => $name
        ];
    }
}
