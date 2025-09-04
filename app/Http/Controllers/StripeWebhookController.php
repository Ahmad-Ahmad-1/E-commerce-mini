<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Stripe webhook received', [
            'payload' => $request->getContent(),
            'headers' => $request->headers->all(),
        ]);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret') // set in .env
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Handle successful payment
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;

            $orderId = $paymentIntent->metadata->order_id;
            $order = Order::with('items.product', 'user.cart.items')->find($orderId);

            if ($order && $order->status !== 'paid') {
                $order->update(['status' => 'paid']);

                // Decrement stock
                foreach ($order->items as $item) {
                    $item->product?->decrement('quantity', $item->quantity);
                }

                // Clear user's cart
                $order->user->cart?->items()->delete();
            }
        }

        return response()->json(['received' => true]);
    }
}
