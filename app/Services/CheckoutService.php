<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutService
{
    /*
        Create a Stripe PaymentIntent tied with total amount in cart.
        create an order with pending status and copy cart items to it.
        Do not clear the cart yet.
    */
    public function createPaymentIntent(Cart $cart, User $user): array
    {
        $amount = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount'   => $amount * 100, // cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        $orderId = DB::transaction(function () use ($cart, $user, $paymentIntent, $amount) {
            $order = $user->orders()->create([
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

            return $order->id;
        });

        return [
            'clientSecret' => $paymentIntent->client_secret,
            'orderId'      => $orderId,
        ];
    }
}
