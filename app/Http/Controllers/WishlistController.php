<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Dotenv\Validator;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user->id;
        $wishlist = Wishlist::query()->with("products")->where("user_id", $userId)->first();

        if (is_null($wishlist)) {
            return Formatter::apiResponse(400, "You dont have any wishlist yet");
        }

        return Formatter::apiResponse(200, "Wishlist data retrieved", $wishlist);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add(Request $request, string $slug)
    {
        $userId = $request->user->id;

        $wishlist = Wishlist::query()->with("products")->firstOrCreate([
            "user_id" => $userId
        ]);

        $product = Product::query()->where("slug", $slug)->first();
        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        if (WishlistItem::query()->where("wishlist_id", $wishlist->id)->where("product_id", $product->id)->exists()) {
            return Formatter::apiResponse(400, "Product already in wishlist");
        }

        WishlistItem::query()->create([
            "wishlist_id" => $wishlist->id,
            "product_id" => $product->id,
        ]);

        return Formatter::apiResponse(200, "Product added to wishlist", Wishlist::query()->with("products")->where("user_id", $userId)->first());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        $productId = Product::query()->where("slug", $slug)->pluck("id")->first();
        if (is_null($productId)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        $userId = $request->user->id;
        $wishlistId = Wishlist::query()->where("user_id", $userId)->pluck("id")->first();

        $wishlistItem = WishlistItem::query()->where("wishlist_id", $wishlistId)->where("product_id", $productId)->first();
        if (is_null($wishlistItem)) {
            return Formatter::apiResponse(404, "Wishlist product not found");
        }
        $wishlistItem->delete();
        return Formatter::apiResponse(200, "Product removed from wishlist", Wishlist::query()->with("products")->where("user_id", $userId)->first());
    }
}
