<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;

class OrderController extends Controller
{
    public function index()
    {
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
        if ($order->status !== OrderStatus::Pending) {
            return response()->json(['message' => 'You can only cancel pending orders.'], 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Retrieve and cancel the PaymentIntent
            $paymentIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);
            $paymentIntent->cancel();

            // Mark order as cancellation pending (final state comes via webhook)
            $order->update(['status' => OrderStatus::CancellationPending]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Stripe cancellation failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Your order should be cancelled soon.',
            'order' => new OrderResource($order)
        ]);
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
