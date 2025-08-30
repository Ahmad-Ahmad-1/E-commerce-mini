<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_intent_id' => 'required|string',
        ]);

        // Set Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));

        // Retrieve PaymentIntent from Stripe
        $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

        $order = Order::with('items.product', 'user.cart.items')->findOrFail($request->order_id);

        // Prevent paying the same order twice.
        if ($order->status === 'paid') {
            return response()->json([
                'message' => 'This order has already been paid.',
                'order'   => new OrderResource($order),
            ], 400);
        }

        // Check if payment succeeded
        if ($paymentIntent->status === 'succeeded') {
            // Compare amounts safely (convert float to int cents)
            if ((int) round($order->total * 100) !== $paymentIntent->amount_received) {
                return response()->json([
                    'message' => 'Payment amount mismatch.',
                    'order_total_cents' => (int) round($order->total * 100),
                    'amount_received' => $paymentIntent->amount_received,
                ], 400);
            }

            // Mark order as paid
            $order->update(['status' => 'paid']);

            // Decrement stock for each purchased product
            foreach ($order->items as $orderItem) {
                $product = $orderItem->product;
                if ($product) {
                    $product->decrement('quantity', $orderItem->quantity);
                }
            }

            // Clear user's cart items
            $cart = $order->user->cart;
            if ($cart) {
                $cart->items()->delete();
            }

            return response()->json([
                'message' => 'Payment successful',
                'order'   => new OrderResource($order->load('items.product')),
            ]);
        }

        return response()->json([
            'message' => 'Payment not completed yet.',
            'status'  => $paymentIntent->status,
        ], 400);
    }
}
