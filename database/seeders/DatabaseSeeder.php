<?php

namespace Database\Seeders;

use App\CustomHelper\Formatter;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\Seller;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        Admin::query()->truncate();
        Admin::query()->create([
            "username" => "admin",
            "password" => Hash::make("admin")
        ]);

        Seller::query()->truncate();
        Seller::query()->create([
            "username" => "seller",
            "password" => Hash::make("seller"),
            "phone" => "6287774666310",
            "store_name" => "Store " . fake()->word()
        ]);

        User::query()->truncate();
        User::query()->create([
            'username' => "user",
            'password' => Hash::make("user"),
            'phone' => fake()->phoneNumber()
        ]);

        Product::query()->truncate();
        $sellers = Seller::all()->shuffle();
        foreach ($sellers as $seller) {
            $totalProduct = fake()->numberBetween(1, 10);
            for ($k = 0; $k < $totalProduct; $k++) {
                $name = "product " . $seller->store_name . " " . $k;
                Product::query()->create([
                    "slug" => Formatter::makeDash($name),
                    "seller_id" => $seller->id,
                    "name" => $name,
                    "price" => fake()->numberBetween(1000,100000),
                    "stock" => fake()->numberBetween(10, 100),
                    "image" => "image urls here",
                    "description" => fake()->paragraph()
                ]);
            }
        }

        Wishlist::query()->truncate();
        WishlistItem::query()->truncate();
        Category::query()->truncate();
        SubCategory::query()->truncate();
        SubSubCategory::query()->truncate();
        // Product::query()->truncate();
        ProductCategory::query()->truncate();
        Order::query()->truncate();

        User::factory(50)->create();
        $users = User::all()->shuffle();
        foreach ($users as $user) {
            Wishlist::query()->create([
                "user_id" => $user->id
            ]);
        }
        $wishlists = Wishlist::all()->shuffle();
        foreach ($wishlists as $wishlist) {
            $wishlistItemTotal = fake()->numberBetween(1, 10);
            for ($m = 0; $m < $wishlistItemTotal; $m++) {
                WishlistItem::query()->create([
                    "wishlist_id" => $wishlist->id,
                    "product_id" => Product::all()->pluck("id")->random()
                ]);
            }
        }

        Category::factory(5)->create();
        $categories = Category::all()->shuffle();
        foreach ($categories as $category) {
            for ($i = 0; $i < 10; $i++) {
                $name = "sub category " . $category->name . " " . $i;
                SubCategory::query()->create([
                    "parent_category_id" => $category->id,
                    "slug" => Formatter::makeDash($name),
                    "name" => $name
                ]);
            }
        }

        $subCategories = SubCategory::all()->shuffle();
        foreach ($subCategories as $subCategory) {
            for ($j = 0; $j < 15; $j++) {
                $name = "sub sub category " . $subCategory->name . " " . $j;
                SubSubCategory::query()->create([
                    "parent_sub_category_id" => $subCategory->id,
                    "slug" => Formatter::makeDash($name),
                    "name" => $name
                ]);
            }
        }

        $subSubCategoryCount = SubSubCategory::all()->count();
        ProductCategory::factory($subSubCategoryCount)->create();


        // Order::factory(200)->create();

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}
