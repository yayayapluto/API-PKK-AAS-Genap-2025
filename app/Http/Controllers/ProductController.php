<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productQuery = Product::query();

        if (isset($request->seller)) {
            $productQuery = $productQuery->where("seller_id", $request->seller->id);
        }

        $products = $productQuery->simplePaginate(10);

        foreach ($products as $key => $value) {
            $products[$key]->image = url($value->image);
        }

        return Formatter::apiResponse(200, "Product list retrieved", $products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:products,name",
            "imageFile" => "required|image",
            "price" => "required|integer",
            "stock" => "required|integer",
            "sub_sub_category_id" => "required|integer|exists:sub_sub_categories,id",
            "description" => "sometimes|string"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = Formatter::makeDash(Formatter::removeVowel($request->name));
        if (Product::query()->where("slug", $newSlug)->exists()) {
            return Formatter::apiResponse(400, "Product already exists");
        }

        $imageUrl = null;
        if ($request->hasFile("imageFile")) {
            $fileImage = $request->file("imageFile");
                $path = "product-images";
                $fileName = Formatter::makeDash($newSlug. "-" . Formatter::makeDash(Carbon::now()->toDateString())) . "." . $fileImage->getClientOriginalExtension();
                $storedPath = $fileImage->storeAs($path, $fileName, "public");
                $imageUrl = Storage::url($storedPath);
        }

        if (is_null($imageUrl)) {
            return Formatter::apiResponse(400, "Error occurred while uploading image");
        }

        $cred = $validator->validated();
        $cred["slug"] = $newSlug;
        $cred["seller_id"] = $request->seller->id;
        $cred["image"] = $imageUrl;

        $newProduct = Product::query()->create($cred);
        ProductCategory::query()->create([
            'product_id' => $newProduct->id,
            'sub_sub_category_id' => $request->input('sub_sub_category_id')
        ]);

        $newProduct->image = url($newProduct->image);

        return Formatter::apiResponse(200, "Product created", $newProduct);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $productQuery = Product::query()->with("sub_sub_categories.subCategory.category")->where("slug", $slug);

        if (isset($request->seller)) {
            $productQuery = $productQuery->where("seller_id", $request->seller->id);
        }

        $product = $productQuery->first();

        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        $product->image = url($product->image);

        return Formatter::apiResponse(200, "Product data retrieved", $product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $product = Product::query()->with("sub_sub_categories.subCategory.category")->where("slug", $slug)->where("seller_id", $request->seller->id)->first();
        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        $validator = Validator::make($request->all(), [
            "name" => "sometimes|string|unique:products,name",
            "price" => "sometimes|integer",
            "stock" => "sometimes|integer",
            "description" => "sometimes|string"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = $product->slug;
        if ($request->has("name")) {
            $newSlug = Formatter::makeDash(Formatter::removeVowel($request->name));
            if (Product::query()->where("slug", $newSlug)->exists()) {
                return Formatter::apiResponse(400, "Product already exists");
            }
        }


        $cred = $validator->validated();
        $cred["slug"] = $newSlug;

        $product->update($cred);

        $updatedProduct = Product::query()->find($product->id);
        $updatedProduct->image = url($updatedProduct->image);

        return Formatter::apiResponse(200, "Product updated", $updatedProduct);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        $product = Product::query()->where("slug", $slug)->where("seller_id", $request->seller->id)->first();
        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }
        $product->delete();
        return Formatter::apiResponse(200, "Product deleted");
    }

    public function changeImage(Request $request, string $slug)
    {
        $product = Product::query()->with("sub_sub_categories.subCategory.category")->where("slug", $slug)->where("seller_id", $request->seller->id)->first();
        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        $validator = Validator::make($request->all(), [
            "imageFile" => "required|image",
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $imageUrl = $product->image;
        if ($request->hasFile("imageFile")) {
            $fileImage = $request->file("imageFile");
//            dd($fileImage);
            $path = "product-images";
            $fileName = Formatter::makeDash($product->slug . Formatter::removeVowel($fileImage->getFilename())) . "." . $fileImage->getClientOriginalExtension();
            $storedPath = $fileImage->storeAs($path, $fileName, "public");
            $imageUrl = Storage::url($storedPath);
        }

        $product->update([
            "image" => $imageUrl
        ]);

        $updatedProduct = Product::query()->find($product->id);
        $updatedProduct->image = url($updatedProduct->image);

        return Formatter::apiResponse(200, "Product updated", $updatedProduct);
    }
}
