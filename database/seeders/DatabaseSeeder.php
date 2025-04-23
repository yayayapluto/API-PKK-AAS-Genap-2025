<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Category::query()->delete();
        Category::factory(2)->create();

        SubCategory::query()->delete();
        SubCategory::factory(10)->create();

        SubSubCategory::query()->delete();
        SubSubCategory::factory(50)->create();
    }
}
