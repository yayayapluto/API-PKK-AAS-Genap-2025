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

        Wishlist::query()->truncate();
        WishlistItem::query()->truncate();
        Category::query()->truncate();
        SubCategory::query()->truncate();
        SubSubCategory::query()->truncate();
        ProductCategory::query()->truncate();
        Order::query()->truncate();

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
        $namaProdukGaul = [
            "Kaos Santuy", "Celana Mager", "Topi Gaul", "Sepatu Ngegas", "Tas Nongki", "Kacamata Stylish",
            "Jaket Adem", "Sandal Nyantai", "Hoodie Rebahan", "Dompet Anak Hits", "Sweater Cuan",
            "Baju Gacor", "Rok Cewek Keren", "Kemeja Ngejreng", "Jas Rapi Jali", "Sneakers Mantul",
            "Jam Tangan Kece", "Gelang Hoki", "Topi Cuan", "Kaos Gokil", "Celana Ganteng", "Kaos Cewek Kece",
            "Kacamata Ciee", "Dompet Sultan", "Hoodie Nongkrong", "Jaket Anak Senja"
        ];

        foreach ($sellers as $seller) {
            $totalProduct = fake()->numberBetween(1, 10);
            for ($k = 0; $k < $totalProduct; $k++) {
                $name = $namaProdukGaul[array_rand($namaProdukGaul)] . " " . $seller->store_name;
                Product::query()->create([
                    "slug" => Formatter::makeDash($name),
                    "seller_id" => $seller->id,
                    "name" => $name,
                    "price" => fake()->numberBetween(1000, 100000),
                    "stock" => fake()->numberBetween(10, 100),
                    "image" => "image urls here",
                    "description" => fake()->paragraph()
                ]);
            }
        }




        Category::factory(15)->create();
        $categories = Category::all()->shuffle();
        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) {
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
            for ($j = 0; $j < 3; $j++) {
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
