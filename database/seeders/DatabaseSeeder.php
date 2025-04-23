<?php

namespace Database\Seeders;

use App\CustomHelper\Formatter;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
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
use App\Models\VariantOption;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        User::factory(50)->create();
        $users = User::all()->shuffle();

        $admins = $users->take(5);
        foreach ($admins as $admin) {
            Admin::query()->create([
                "user_id" => $admin->id
            ]);
        }

        $sellers = $users->slice(5, 20);
        foreach ($sellers as $seller) {
            Seller::query()->create([
                "user_id" => $seller->id,
                "store_name" => fake()->unique()->words(2, true),
                "bio" => fake()->paragraph()
            ]);
        }
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
                    "images" => "image urls here",
                    "description" => fake()->paragraph()
                ]);
            }
        }

        $customers = $users->slice(25, 25);
        foreach ($customers as $customer) {
            Customer::query()->create([
                "user_id" => $customer->id
            ]);
        }
        $customers = Customer::all()->shuffle();
        foreach ($customers as $customer) {
            Wishlist::query()->create([
                "customer_id" => $customer->id
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
        ProductCategory::factory(5)->create();

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
        ProductSubCategory::factory(50)->create();

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
        ProductSubSubCategory::factory(150)->create();

        Order::factory(200)->create();
        $orders = Order::all()->shuffle();
        foreach ($orders as $order) {
            $orderItemTotal = fake()->numberBetween(1, 10);
            for ($l = 0; $l < $orderItemTotal; $l++) {
                OrderItem::query()->create([
                    "order_id" => $order->id,
                    "quantity" => fake()->numberBetween(1, 30),
                    "price" => fake()->numberBetween(10000, 100000) ,
                    "product_id" => Product::all()->pluck("id")->random()
                ]);
            }
        }

        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}
