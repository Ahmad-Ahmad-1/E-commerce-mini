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

        $stripePaymentIntentId = $event->data->object->id;

        // you don't need to eager load cart items.
        $order = Order::with('items.product', 'user.cart.items')
            ->where('stripe_payment_intent_id', $stripePaymentIntentId)
            ->first();

        if ($event->type === 'payment_intent.succeeded') {
            if ($order && $order->status !== OrderStatus::Paid->value) {
                $order->update(['status' => OrderStatus::Paid->value]);

                foreach ($order->items as $item) {
                    $item->product?->decrement('quantity', $item->quantity);
                }

                $order->user->cart?->items()->delete();
            }
        } elseif ($event->type === 'payment_intent.canceled') {
            if ($order && $order->status === OrderStatus::CancellationPending->value) {
                $order->update(['status' => OrderStatus::Cancelled->value]);
            }
        }
    }
}
