<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Order;

class PaymentController extends Controller
{
    public function checkout(Order $order)
    {
        $this->authorize('pay', $order);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order cannot be paid'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order #' . $order->id,
                    ],
                    'unit_amount' => $order->total * 100, // in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['order_id' => $order->id]),
            'cancel_url' => route('payment.cancel', ['order_id' => $order->id]),
        ]);

        return response()->json(['id' => $session->id]);
    }

    public function success(Request $request)
    {
        // ⚠️ Do NOT mark order as paid here blindly
        // Instead, let the webhook handle final confirmation
        return response()->json(['message' => 'Payment successful (pending verification)']);
    }

    public function cancel(Request $request)
    {
        return response()->json(['message' => 'Payment cancelled']);
    }
}
