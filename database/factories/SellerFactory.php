<?php

namespace Database\Factories;

use App\CustomHelper\Formatter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = "seller " . fake()->unique()->userName;
        return [
            'username' => $name,
            'password' => Hash::make('password'),
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            "store_name" => Formatter::makeDash($name),
            "bio" => fake()->paragraph(),
            "last_login_at" => fake()->dateTime()
        ];
    }
}
