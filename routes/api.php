<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("admin")->group(function () {
    Route::apiResource("categories", \App\Http\Controllers\CategoryController::class);
    Route::apiResource("sub-categories", \App\Http\Controllers\SubCategoryController::class);
});
