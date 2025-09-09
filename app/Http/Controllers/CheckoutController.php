<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    public function createPaymentIntent(Request $request, CheckoutService $checkout)
    {
        $cart = $request->user()->cart()->with('items.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        return response()->json(
            $checkout->createPaymentIntent($cart, $request->user())
        );
    }
}
