<?php

namespace App\Http\Controllers;

use Exception;
use Stripe\Stripe;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Http\Requests\ConfirmOrderRequest;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;

class OrderController extends Controller
{
    public function index()
    {
        return OrderListResource::collection(Order::latest()->paginate(10));
    }

    public function myOrders(Request $request)
    {
        return OrderListResource::collection($request->user()->orders()->latest()->paginate(10));
    }

    public function show(Order $order)
    {
        return response()->json(new OrderResource($order));
    }

    public function store(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        foreach ($cart->items as $item) {
            if ($item->product->quantity < $item->quantity) {
                return response()->json([
                    'error' => "Product [{$item->product->title}] is out of stock"
                ], 400);
            }
        }

        $amount = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount'   => $amount * 100, // cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        DB::transaction(function () use ($cart, $request, $paymentIntent, $amount) {
            $order = $request->user()->orders()->create([
                'status' => 'pending',
                'total'  => $amount,
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->product->price,
                ]);
            }
        });

        return response()->json(['message' => 'A pending order is created successfully.']);
    }

    public function confirm(ConfirmOrderRequest $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($order->status !== OrderStatus::Pending->value) {
            return response()->json(['error' => 'Order is not pending'], 400);
        }

        foreach ($order->items as $item) {
            if ($item->product->quantity < $item->quantity) {
                return response()->json([
                    'error' => "Product [{$item->product->title}] is out of stock"
                ], 400);
            }
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);

            $paymentIntent->confirm([
                'payment_method' => $request->payment_method,
            ]);

            DB::transaction(function () use ($order) {

                foreach ($order->items as $item) {
                    $item->product->decrement('quantity', $item->quantity);
                }

                $order->update(['status' => OrderStatus::Paid->value]);
            });

            return response()->json([
                'message' => 'Payment confirmed successfully',
                'order' => new OrderResource($order),
            ]);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'Payment confirmation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel(Order $order)
    {
        if ($order->status !== OrderStatus::Pending->value) {
            return response()->json(['message' => 'You can only cancel pending orders.'], 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);
            $paymentIntent->cancel();

            $order->update(['status' => OrderStatus::Cancelled->value]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Stripe cancellation failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Order is cancelled successfully.',
            'order' => new OrderResource($order)
        ]);
    }

    // in webhook env, we'll only update paymentIntent and order statuses here to intermediate statuses
    // the actual work in webhook controller.
    // Webhook based
    // public function cancel(Order $order)
    // {
    //     if ($order->status !== OrderStatus::Pending->value) {
    //         return response()->json(['message' => 'You can only cancel pending orders.'], 400);
    //     }

    //     try {
    //         Stripe::setApiKey(config('services.stripe.secret'));

    //         $paymentIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);
    //         $paymentIntent->cancel();

    //         $order->update(['status' => OrderStatus::CancellationPending]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'message' => 'Stripe cancellation failed. Please try again.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }

    //     return response()->json([
    //         'message' => 'Your order should be cancelled soon.',
    //         'order' => new OrderResource($order)
    //     ]);
    // }

    // public function buyAgain(Order $order, Request $request)
    // {
    //     $user = $request->user();

    //     $addedItems = [];
    //     $skippedProducts = [];

    //     // Iterate through order items
    //     foreach ($order->items as $item) { // <-- use items() relation
    //         $product = $item->product;

    //         if ($product) {
    //             $addedItems[] = [
    //                 'product_id' => $product->id,
    //                 'quantity'   => $item->quantity,
    //                 'price'      => $item->price,
    //             ];
    //         } else {
    //             $skippedProducts[] = $product ? $product->name : "Product #{$item->product_id}";
    //         }
    //     }

    //     if (empty($addedItems)) {
    //         return response()->json([
    //             'message' => 'No products from this order are available to buy again.',
    //             'skipped' => $skippedProducts,
    //         ], 400);
    //     }

    //     // Create new order
    //     $newOrder = $user->orders()->create([
    //         'status' => 'pending',
    //         'total'  => collect($addedItems)->sum(fn($i) => $i['price'] * $i['quantity']),
    //     ]);

    //     // Attach items to new order
    //     foreach ($addedItems as $item) {
    //         $newOrder->items()->create([
    //             'product_id' => $item['product_id'],
    //             'quantity'   => $item['quantity'],
    //             'price'      => $item['price'],
    //         ]);
    //     }

    //     return response()->json([
    //         'message'          => 'Order created successfully.',
    //         'orderId'         => $newOrder->id,
    //         'addedProducts'   => array_column($addedItems, 'product_id'),
    //         'skippedProducts' => $skippedProducts,
    //     ]);
    // }
}
