<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Stripe\Webhook;
use App\Models\Order;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $paymentIntent = $event->data->object;
        $orderId = $paymentIntent->metadata->order_id ?? null;
        $order = $orderId ? Order::with('items.product', 'user.cart.items')->find($orderId) : null;

        if ($event->type === 'payment_intent.succeeded') {
            if ($order && $order->status !== 'paid') {
                $order->update(['status' => 'paid']);

                foreach ($order->items as $item) {
                    $item->product?->decrement('quantity', $item->quantity);
                }

                $order->user->cart?->items()->delete();
            }
        } elseif ($event->type === 'payment_intent.canceled') {
            if ($order && $order->status !== OrderStatus::Cancelled) {
                $order->update(['status' => OrderStatus::Cancelled]);
            }
        }

        // return response()->json(['received' => true]);
    }
}
