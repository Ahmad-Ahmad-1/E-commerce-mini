<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class CheckoutController extends Controller
{
    public function createSession(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Calculate total in cents
        $lineItems = $cart->items->map(function ($item) {
            if (!$item->product) {
                throw new \Exception("Cart item #{$item->id} has no associated product");
            }

            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->title,
                    ],
                    'unit_amount' => intval($item->product->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',

            // 'success_url' => env('FRONTEND_URL') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            // 'cancel_url' => env('FRONTEND_URL') . '/checkout/cancel',

            'success_url' => route('success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',

            'metadata' => [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
            ],
        ]);

        return response()->json(['sessionId' => $checkoutSession->id]);
    }

    // Optional: handle success redirect (SPA can call API to finalize order)
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return response()->json(['error' => 'Session ID missing'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = CheckoutSession::retrieve($sessionId);

        $userId = $session->metadata->user_id;
        $cartId = $session->metadata->cart_id;

        $user = \App\Models\User::findOrFail($userId);
        $cart = $user->cart()->with('items.product')->findOrFail($cartId);

        // Create the order
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'total' => $cart->items->sum(fn($item) => $item->product->price * $item->quantity),
            'status' => 'paid',
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // Clear cart items
        $cart->items()->delete();

        return response()->json([
            'message' => 'Payment successful and order created',
            'order_id' => $order->id,
        ]);
    }
}
