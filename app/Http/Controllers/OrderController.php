<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'orders' => OrderResource::collection($request->user()->orders()->latest()->get())
        ]);
    }

    public function show(Order $order)
    {
        return response()->json(new OrderResource($order));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Product has been deleted successfully.',
        ]);
    }

    public function cancel(Order $order)
    {
        $order->update(['status' => OrderStatus::Cancelled]);

        return response()->json(new OrderResource($order));
    }
}
