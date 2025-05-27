<?php

namespace App\Http\Controllers;

use App\CustomHelper\Formatter;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\alert;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query("status");
        $orderQuery = Order::query()->with(["product", "user"]);

        if (isset($request->seller)) {
            $orderQuery = $orderQuery->where("seller_id", $request->seller->id);
        }

        if (isset($request->user)) {
            $orderQuery = $orderQuery->where("user_id", $request->user->id);
        }

        if (isset($status) && in_array($status, ["pending", "on progress", "finished", "cancelled"])) {
            return Formatter::apiResponse(200, "Order list retrieved", $orderQuery->where("status", $status)->simplePaginate(10));
        }

        return Formatter::apiResponse(200, "Order list retrieved", $orderQuery->simplePaginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "product" => "required|exists:products,slug"
        ]);

        if ($validator->fails()) {
            return Formatter::apiResponse(422, "Validation failed", null, $validator->errors()->all());
        }

        $validated = $validator->validated();
        $slug = $validated["product"];

        $product = Product::query()->where("slug", $slug)->first();

        if (is_null($product)) {
            return Formatter::apiResponse(404, "Product not found");
        }

        $orderData = [
            "total_price" => $product->price,
            "user_id" => $request->user->id,
            "product_id" => $product->id,
            "seller_id" => $product->seller_id
        ];

        $isThereAnyPendingWithInSameProduct = Order::query()->where("user_id", $request->user->id)->where("product_id", $product->id)->where("status", "pending")->exists();
        if ($isThereAnyPendingWithInSameProduct) {
            return Formatter::apiResponse(400, "There is previous pending order within same product, please contact seller");
        }

        $newOrder = Order::query()->create($orderData);
        return Formatter::apiResponse(200, "Order placed", [
            "order_data" => $newOrder,
            "seller_phone" => "https://wa.me/" . Seller::query()->find($product->seller_id)->pluck("phone")->first() . "?text=hi%20seller%20orderId:%20" . $newOrder->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $orderQuery = Order::query()->with(["product", "user"])->where("id", $id);
        if (isset($request->seller)) {
            $orderQuery = $orderQuery->where("seller_id", $request->seller->id);
        }

        if (isset($request->user)) {
            $orderQuery = $orderQuery->where("user_id", $request->user->id);
        }

        $order = $orderQuery->first();

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
        $order = Order::query()->with(["product", "user"])->where("id", $id)->where("seller_id", $request->seller->id)->first();
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
        return Formatter::apiResponse(200, "Order updated", Order::query()->with(["product", "user"])->where("id", $id)->where("seller_id", $request->seller->id)->first());
    }
}
