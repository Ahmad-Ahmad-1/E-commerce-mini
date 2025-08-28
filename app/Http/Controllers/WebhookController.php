<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Event;
use App\Models\Order;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // set in .env

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $orderId = $session->metadata->order_id ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'paid']);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
