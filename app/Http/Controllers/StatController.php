<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function overview()
    {
        $userCount = User::all()->count();
        $sellerCount = Seller::all()->count();
        $productCount = Product::all()->count();

        $orderQuery = Order::query();
        $orderCount = [
            "today" => $orderQuery->whereDate("created_at", Carbon::today())->count(),
            "thisWeek" => $orderQuery->whereBetween("created_at", [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            "thisMonth" => $orderQuery->whereMonth("created_at", Carbon::now()->month)->whereYear("created_at", Carbon::now()->year)->count(),
            "thisYear" => $orderQuery->whereYear("created_at", Carbon::now()->year)->count(),
        ];

        return Formatter::apiResponse(200, "Overview data retrieved", [
            "userCount" => $userCount,
            "sellerCount" => $sellerCount,
            "productCount" => $productCount,
            "orderCount" => $orderCount
        ]);
    }

    public function user()
    {
        $userQuery = User::query();
        $userCount = [
            "today" => $userQuery->whereDate("created_at", Carbon::today())->count(),
            "thisWeek" => $userQuery->whereBetween("created_at", [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            "thisMonth" => $userQuery->whereMonth("created_at", Carbon::now()->month)->whereYear("created_at", Carbon::now()->year)->count(),
            "thisYear" => $userQuery->whereYear("created_at", Carbon::now()->year)->count(),
        ];
        $userData = $userQuery->with("orders")->with("wishlist")->simplePaginate(10);
        return Formatter::apiResponse(200, "User data retrieved", [
            "userCount" => $userCount,
            "userData" => $userData
        ]);
    }

    public function seller()
    {
        $sellerQuery = Seller::query();
        $sellerCount = [
            "today" => $sellerQuery->whereDate("created_at", Carbon::today())->count(),
            "thisWeek" => $sellerQuery->whereBetween("created_at", [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            "thisMonth" => $sellerQuery->whereMonth("created_at", Carbon::now()->month)->whereYear("created_at", Carbon::now()->year)->count(),
            "thisYear" => $sellerQuery->whereYear("created_at", Carbon::now()->year)->count(),
        ];
        $sellerData = $sellerQuery->with("products")->simplePaginate(10);
        return Formatter::apiResponse(200, "Seller data retrieved", [
            "sellerCount" => $sellerCount,
            "sellerData" => $sellerData
        ]);
    }

    public function order()
    {
        $orderQuery = Order::query();
        $orderCount = [
            "today" => $orderQuery->whereDate("created_at", Carbon::today())->count(),
            "thisWeek" => $orderQuery->whereBetween("created_at", [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            "thisMonth" => $orderQuery->whereMonth("created_at", Carbon::now()->month)->whereYear("created_at", Carbon::now()->year)->count(),
            "thisYear" => $orderQuery->whereYear("created_at", Carbon::now()->year)->count(),
        ];
        $orderStatus = [
            "pending" => $orderQuery->where("status", "pending")->count(),
            "on progress" => $orderQuery->where("status", "on progress")->count(),
            "finished" => $orderQuery->where("status", "finished")->count(),
            "cancelled" => $orderQuery->where("status", "cancelled")->count()
        ];
        return Formatter::apiResponse(200, "Order data retrieved", [
            "orderCount" => $orderCount,
            "orderStatus" => $orderStatus
        ]);
    }
}
