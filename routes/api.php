<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("auth")->group(function () {
    Route::post("login", [\App\Http\Controllers\AuthController::class, "login"]);
    Route::post("register", [\App\Http\Controllers\AuthController::class, "register"]);
    Route::get("logout", [\App\Http\Controllers\AuthController::class, "logout"])->middleware("need-token");
});

// Public routes
Route::apiResource("products", \App\Http\Controllers\ProductController::class)->only(["index","show"]);
Route::apiResource("categories", \App\Http\Controllers\CategoryController::class)->only(["index","show"]);
Route::apiResource("sub-categories", \App\Http\Controllers\SubCategoryController::class)->only(["index","show"]);
Route::apiResource("sub-sub-categories", \App\Http\Controllers\SubSubCategoryController::class)->only(["index","show"]);

Route::middleware("need-token")->group(function () {

    Route::prefix("admin")->middleware("role:admin")->group(function () {
        Route::apiResource("categories", \App\Http\Controllers\CategoryController::class);
        Route::apiResource("sub-categories", \App\Http\Controllers\SubCategoryController::class);
        Route::apiResource("sub-sub-categories", \App\Http\Controllers\SubSubCategoryController::class);
        Route::apiResource("sellers", \App\Http\Controllers\SellerController::class);
        Route::apiResource("users", \App\Http\Controllers\UserController::class);

        Route::prefix("stats")->group(function () {
            Route::get("overview", [\App\Http\Controllers\StatController::class, "overview"]);
            Route::get("user", [\App\Http\Controllers\StatController::class, "user"]);
            Route::get("seller", [\App\Http\Controllers\StatController::class, "seller"]);
            Route::get("order", [\App\Http\Controllers\StatController::class, "order"]);
        });
    });

    Route::prefix("seller")->middleware("role:seller")->group(function () {
        Route::apiResource("products", \App\Http\Controllers\ProductController::class);
        Route::post("products/{slug}/change-image", [\App\Http\Controllers\ProductController::class, "changeImage"]);
        Route::apiResource("orders", \App\Http\Controllers\OrderController::class)->except(["store","destroy"]);
    });

    Route::prefix("user")->middleware("role:user")->group(function () {
        Route::apiResource("orders", \App\Http\Controllers\OrderController::class)->except(["update","destroy"]);
        Route::apiResource("wishlists", \App\Http\Controllers\WishlistController::class)->only(["index","destroy"]);
        Route::post("wishlists/{slug}", [\App\Http\Controllers\WishlistController::class, "add"]);
    });
});

Route::fallback(function () {
    return \App\CustomHelper\Formatter::apiResponse(404, "Route not found");
});
