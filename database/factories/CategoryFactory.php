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
        $categories = [
            "Elektronik",
            "Peralatan Rumah Tangga",
            "Buku & Alat Tulis",
            "Pakaian & Aksesori",
            "Kesehatan & Kecantikan",
            "Mainan & Permainan",
            "Peralatan Olahraga",
            "Suku Cadang Kendaraan",
            "Makanan & Minuman",
            "Perlengkapan Hewan",
            "Peralatan Kantor",
            "Taman & Outdoor",
            "Perlengkapan Bayi",
            "Alat Musik",
            "Kerajinan Tangan"
        ];

        $name = fake()->unique()->randomElement($categories);
        $slug = Formatter::makeDash($name);

        return [
            "slug" => $slug,
            "name" => $name
        ];
    }

}
