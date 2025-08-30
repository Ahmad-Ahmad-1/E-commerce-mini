<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $cart = $request->user()->cart()->with('items.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $amount = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);

        // Create pending order
        $order = $request->user()->orders()->create([
            'status' => 'pending',
            'total' => $amount,
        ]);

        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'quantity'   => $cartItem->quantity,
                'price'      => $cartItem->product->price,
            ]);
        }

        // Set Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create PaymentIntent with only card payments
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100, // in cents
            'currency' => 'usd',
            'payment_method_types' => ['card'], // 👈 restrict to card only
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
            'orderId' => $order->id,
        ]);
    }
}
