<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "status" => $this->faker->randomElement(["pending", "on progress", "finished", "cancelled"]),
            "total_price" => $this->faker->numberBetween(10000, 100000),
            "user_id" => User::all()->pluck("id")->random(),
            "product_id" => Product::all()->pluck("id")->random(),
            "seller_id" => Seller::all()->pluck("id")->random()
        ];
    }
}
