<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query("status");
        $orderQuery = Order::query()->with(["product","user"])->where("seller_id", $request->seller->id);

        if (isset($status) && in_array($status, ["pending","on progress","finished","cancelled"])) {
            return Formatter::apiResponse(200, "Order list retrieved", $orderQuery->where("status", $status)->simplePaginate(10));
        }

        return Formatter::apiResponse(200, "Order list retrieved", $orderQuery->simplePaginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // nanti
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $order = Order::query()->with(["product","user"])->where("id", $id)->where("seller_id", $request->seller->id)->first();
        if (is_null($order)) {
            return Formatter::apiResponse(404, "Order data not found", $order);
        }
        return Formatter::apiResponse(200, "Order data retrieved", $order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::query()->with(["product","user"])->where("id", $id)->where("seller_id", $request->seller->id)->first();
        if (is_null($order)) {
            return Formatter::apiResponse(404, "Order data not found", $order);
        }

        $validator = Validator::make($request->all(), [
            "status" => "sometimes|in:pending,on progress,finished,cancelled",
            "total_price" => "sometimes|integer"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $order->update($validator->validated());
        return Formatter::apiResponse(200, "Order updated", Order::query()->with(["product","user"])->where("id", $id)->where("seller_id", $request->seller->id)->first());
    }
}
