<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();

        // Calculate total price
        $amount = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100, // in cents
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
            ],
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }
}
