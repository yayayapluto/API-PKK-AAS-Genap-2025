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
        Category::factory(20)->create();

        SubCategory::query()->delete();
        SubCategory::factory(30)->create();

        SubSubCategory::query()->delete();
        SubSubCategory::factory(40)->create();
    }
}
