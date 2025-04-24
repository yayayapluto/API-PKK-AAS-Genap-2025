<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()->simplePaginate(10);
        return Formatter::apiResponse(200, "User list retrieved", $users);
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
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $validator->validated()["password"] = Hash::make($validator->validated()["password"]);

        $newUser = User::query()->create($validator->validated());

        return Formatter::apiResponse(200, "User created", $newUser);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::query()->with("wishlist")->with("orders")->find($id);

        if (is_null($user)) {
            return Formatter::apiResponse(404, "User not found");
        }

        return Formatter::apiResponse(200, "User found", $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::query()->find($id);

        if (is_null($user)) {
            return Formatter::apiResponse(404, "User not found");
        }

        $validator = Validator::make($request->all(), [
            "username" => "sometimes|string|unique:sellers,username",
            "password" => "sometimes|string",
            "email" => "sometimes|string|email|unique:sellers,email",
            "phone" => "sometimes|string",
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        if (isset($validator->validated()["password"])) {
            $validator->validated()["password"] = Hash::make($validator->validated()["password"]);
        }

        $user->update($validator->validated());
        return Formatter::apiResponse(200, "User updated", User::query()->find($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::query()->find($id);

        if (is_null($user)) {
            return Formatter::apiResponse(404, "User not found");
        }

        $user->delete();
        return Formatter::apiResponse(200, "User deleted");
    }
}
