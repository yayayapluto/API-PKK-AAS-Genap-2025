<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()->simplePaginate(10);
        return Formatter::apiResponse(200, "Category list retrieves", $categories, null);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = Formatter::makeDash($request->name);
        $categoryExist = Category::query()->where("slug", $newSlug)->exists();

        if($categoryExist) {
            return Formatter::apiResponse(400, "Category already exists");
        }

        $newCategory = Category::query()->create([
            "slug" => $newSlug,
            "name" => $request->name
        ]);

        return Formatter::apiResponse(200, "Category created", $newCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::query()->where("slug", $slug)->first();

        if (is_null($category)){
            return Formatter::apiResponse(404, "Category not found");
        }

        return Formatter::apiResponse(200, "Category found", $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $category = Category::query()->where("slug", $slug)->first();

        if (is_null($category)){
            return Formatter::apiResponse(404, "Category not found");
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = Formatter::makeDash($request->name);
        if ($newSlug == $category->slug) {
            return Formatter::apiResponse(400, "Category name should not same as previous name");
        }

        $categoryExist = Category::query()->where("slug", $newSlug)->where("name", $request->name)->exists();
        if ($categoryExist) {
            return Formatter::apiResponse(400, "Category already exist");
        }

        $category->update([
            "slug" => $newSlug,
            "name" => $request->name
        ]);

        return Formatter::apiResponse(200, "Category updated", $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $category = Category::query()->where("slug", $slug)->first();
        if (is_null($category)){
            return Formatter::apiResponse(404, "Category not found");
        }
        $category->delete();
        return Formatter::apiResponse(200, "Category deleted");
    }
}
