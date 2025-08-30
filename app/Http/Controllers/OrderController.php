<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {
        return response()->json([
            'orders' => OrderResource::collection(Order::latest()->paginate(10)),
        ]);
    }

    public function myOrders(Request $request)
    {
        return response()->json([
            'orders' => OrderResource::collection(
                $request->user()->orders()->latest()->paginate(10)
            )
        ]);
    }

    public function show(Order $order)
    {
        return response()->json(new OrderResource($order));
    }

    public function cancel(Order $order)
    {
        $order->update(['status' => OrderStatus::Cancelled]);

        return response()->json(new OrderResource($order));
    }

    public function buyAgain(Order $order, Request $request)
    {
        $user = $request->user();

        $addedItems = [];
        $skippedProducts = [];

        // Iterate through order items
        foreach ($order->items as $item) { // <-- use items() relation
            $product = $item->product;

            if ($product) {
                $addedItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ];
            } else {
                $skippedProducts[] = $product ? $product->name : "Product #{$item->product_id}";
            }
        }

        if (empty($addedItems)) {
            return response()->json([
                'message' => 'No products from this order are available to buy again.',
                'skipped' => $skippedProducts,
            ], 400);
        }

        // Create new order
        $newOrder = $user->orders()->create([
            'status' => 'pending',
            'total'  => collect($addedItems)->sum(fn($i) => $i['price'] * $i['quantity']),
        ]);

        // Attach items to new order
        foreach ($addedItems as $item) {
            $newOrder->items()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        return response()->json([
            'message'          => 'Order created successfully.',
            'order_id'         => $newOrder->id,
            'added_products'   => array_column($addedItems, 'product_id'),
            'skipped_products' => $skippedProducts,
        ]);
    }
}
