<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::query()->simplePaginate(10);
        return Formatter::apiResponse(200, "Sub category list retrieved", $subCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "category" => "required|string|exists:categories,slug",
            "name" => "required|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = Formatter::makeDash($request->name);

        $categoryExist = Category::query()->where("slug", $newSlug)->exists();
        $subCategoryExist = SubCategory::query()->where("slug", $newSlug)->exists();

        if($subCategoryExist || $categoryExist) {
            return Formatter::apiResponse(400, "Sub category already exists");
        }

        $categoryId = Category::query()->where("slug", $request->category)->pluck("id")->first();
        $newSubCategory = SubCategory::query()->create([
            "parent_category_id" => $categoryId,
            "slug" => $newSlug,
            "name" => $request->name
        ]);

        $newSubCategory->category = Category::query()->where("slug", $request->category)->first();
        return Formatter::apiResponse(200, "Sub category created", $newSubCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {

        $subCategory = SubCategory::query()->with(["category","subSubCategories.products"])->where("slug", $slug)->first();
        if (is_null($subCategory)) {
            return Formatter::apiResponse(404, "Sub category not found");
        }
        return Formatter::apiResponse(200, "Sub category found", $subCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $subCategory = SubCategory::query()->with("category")->where("slug", $slug)->first();
        if (is_null($subCategory)) {
            return Formatter::apiResponse(404, "Sub category not found");
        }

        $validator = Validator::make($request->all(), [
            "category" => "sometimes|string|exists:categories,slug",
            "name" => "sometimes|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        if (isset($request->category)) {
            $category = Category::query()->where("slug", $request->category)->first();
            if (is_null($category)) {
                return Formatter::apiResponse(404, "Parent category not found");
            }
            $subCategory->update([
                "parent_category_id" => $category->id
            ]);
        }

        if (isset($request->name)) {
            $newSlug = Formatter::makeDash($request->name);
            $categoryExist = Category::query()->where("slug", $newSlug)->exists();
            $subCategoryExist = SubCategory::query()->where("slug", $newSlug)->exists();
            if($subCategoryExist || $categoryExist) {
                return Formatter::apiResponse(400, "Sub category already exists");
            }
            $subCategory->update([
                "slug" => $newSlug,
                "name" => $request->name
            ]);
        }

        return Formatter::apiResponse(200, "Sub category updated", SubCategory::query()->with("category")->where("slug", $slug)->first());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $subCategory = SubCategory::query()->where("slug", $slug)->first();
        if (is_null($subCategory)){
            return Formatter::apiResponse(404, "Sub category not found");
        }
        $subCategory->delete();
        return Formatter::apiResponse(200, "Sub category deleted");
    }
}
