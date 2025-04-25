<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return \App\CustomHelper\Formatter::apiResponse(404, "Route not found");
});
