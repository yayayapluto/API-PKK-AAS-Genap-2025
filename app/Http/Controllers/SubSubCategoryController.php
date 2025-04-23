<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subSubCategories = SubSubCategory::query()->simplePaginate(10);
        return Formatter::apiResponse(200, "Sub sub category list retrieved", $subSubCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "subCategory" => "required|string|exists:sub_categories,slug",
            "name" => "required|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $newSlug = Formatter::makeDash($request->name);

        $categoryExist = Category::query()->where("slug", $newSlug)->exists();
        $subCategoryExist = SubCategory::query()->where("slug", $newSlug)->exists();
        $subSubCategoryExist = SubSubCategory::query()->where("slug", $newSlug)->exists();

        if($subSubCategoryExist || $subCategoryExist || $categoryExist) {
            return Formatter::apiResponse(400, "Sub sub category already exists");
        }

        $subCategoryId = SubCategory::query()->where("slug", $request->subCategory)->pluck("id")->first();
        $newSubSubCategory = SubSubCategory::query()->create([
            "parent_sub_category_id" => $subCategoryId,
            "slug" => $newSlug,
            "name" => $request->name
        ]);

        $newSubSubCategory->subCategory = SubCategory::query()->where("slug", $request->subCategory)->first();
        return Formatter::apiResponse(200, "Sub sub category created", $newSubSubCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $subSubCategory = SubSubCategory::query();

        if (!is_null($request->query("with"))) {
            // ?with=subCategories,subSubCategories
            $queryParam = explode(",", $request->query("with"));
            if (in_array("subCategory", $queryParam)) {
                $subSubCategory->with("subCategory");
            }
        }

        $subSubCategory = $subSubCategory->where("slug", $slug)->first();

        if (is_null($subSubCategory)) {
            return Formatter::apiResponse(404, "Sub sub category not found");
        }
        return Formatter::apiResponse(200, "Sub sub category found", $subSubCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $subSubCategory = SubSubCategory::query()->with("subCategory")->where("slug", $slug)->first();
        if (is_null($subSubCategory)) {
            return Formatter::apiResponse(404, "Sub sub category not found");
        }

        $validator = Validator::make($request->all(), [
            "subCategory" => "sometimes|string|exists:sub_categories,slug",
            "name" => "sometimes|string|max:20"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        if (isset($request->subCategory)) {
            $subCategory = SubCategory::query()->where("slug", $request->subCategory)->first();
            if (is_null($subCategory)) {
                return Formatter::apiResponse(404, "Parent sub category not found");
            }
            $subSubCategory->update([
                "parent_sub_category_id" => $subCategory->id
            ]);
        }

        if (isset($request->name)) {
            $newSlug = Formatter::makeDash($request->name);
            $categoryExist = Category::query()->where("slug", $newSlug)->exists();
            $subCategoryExist = SubCategory::query()->where("slug", $newSlug)->exists();
            $subSubCategoryExist = SubSubCategory::query()->where("slug", $newSlug)->exists();

            if($subSubCategoryExist || $subCategoryExist || $categoryExist) {
                return Formatter::apiResponse(400, "Sub sub category already exists");
            }

            $subSubCategory->update([
                "slug" => $newSlug,
                "name" => $request->name
            ]);
        }

        return Formatter::apiResponse(200, "Sub sub category updated", SubSubCategory::query()->with("subCategory")->where("slug", $slug)->first());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $subSubCategory = SubSubCategory::query()->with("subCategory")->where("slug", $slug)->first();
        if (is_null($subSubCategory)) {
            return Formatter::apiResponse(404, "Sub sub category not found");
        }
        $subSubCategory->delete();
        return Formatter::apiResponse(200, "Sub sub category deleted");
    }
}
