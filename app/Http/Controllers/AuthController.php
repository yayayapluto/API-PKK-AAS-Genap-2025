<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required|string",
            "password" => "required|string",
            "role" => "required|in:admin,user,seller"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $role = $request->input('role');
        $username = $request->input('username');
        $password = $request->input('password');
        $model = null;

        switch ($role) {
            case 'admin':
                $model = Admin::where('username', $username)->first();
                if ($model) {
                    $model->role = "admin";
                    $model->update(["last_login_at" => Carbon::now()]);
                }
                break;
            case 'user':
                $model = User::where('username', $username)->first();
                if ($model) {
                    $model->role = "user";
                }
                break;
            case 'seller':
                $model = Seller::where('username', $username)->first();
                if ($model) {
                    $model->role = "seller";
                    $model->update(["last_login_at" => Carbon::now()]);
                }
                break;
        }


        if (!$model || !Hash::check($password, $model->password)) {
            return Formatter::apiResponse(403, "Username or password invalid");
        }

        $token = $model->createToken("auth_token")->plainTextToken;
        return Formatter::apiResponse(200, "logged in", [
            "token" => $token,
            "user" => $model
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard("sanctum")->user()->tokens()->delete();
        return Formatter::apiResponse(200, "Logged out");
    }
}
