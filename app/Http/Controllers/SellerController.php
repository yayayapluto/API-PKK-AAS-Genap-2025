<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::query()->simplePaginate(10);
        return Formatter::apiResponse(200, "Seller list retrieved", $sellers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string|unique:sellers,username",
            "password" => "required|string",
            "email" => "sometimes|string|email|unique:sellers,email",
            "phone" => "required|string",
            "store_name" => "required|string|unique:sellers,store_name",
            "bio" => "sometimes|string",
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $validator->validated()["password"] = Hash::make($validator->validated()["password"]);
        $newSeller  = Seller::query()->create($validator->validated());
        return Formatter::apiResponse(200, "Seller created", $newSeller);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seller = Seller::query()->with("products")->find($id);
        if (is_null($seller)){
            return Formatter::apiResponse(404, "Seller not found");
        }
        return Formatter::apiResponse(200, "Seller found", $seller);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seller = Seller::query()->find($id);
        if (is_null($seller)){
            return Formatter::apiResponse(404, "Seller not found");
        }

        $validator = Validator::make($request->all(), [
            "username" => "sometimes|string|unique:sellers,username",
            "password" => "sometimes|string",
            "email" => "sometimes|string|email|unique:sellers,email",
            "phone" => "sometimes|string",
            "store_name" => "sometimes|string|unique:sellers,store_name",
            "bio" => "sometimes|string",
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        if (isset($validator->validated()["password"])) {
            $validator->validated()["password"] = Hash::make($validator->validated()["password"]);
        }

        $seller->update($validator->validated());
        return Formatter::apiResponse(200, "Seller updated", Seller::query()->find($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seller = Seller::query()->find($id);
        if (is_null($seller)){
            return Formatter::apiResponse(404, "Seller not found");
        }

        $seller->delete();
        return Formatter::apiResponse(200, "Seller deleted");
    }
}
