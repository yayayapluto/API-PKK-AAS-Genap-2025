<?php

namespace Database\Factories;

use App\CustomHelper\Formatter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = "category " . fake()->unique()->word();
        $slug = Formatter::makeDash($name);
        return [
            "slug" => $slug,
            "name" => $name
        ];
    }
}
