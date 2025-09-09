<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'totalPrice' => $this->total,
            'status' => $this->status,
            'stripePaymentIntentId' => $this->stripe_payment_intent_id,
            'createdAt' => date_format($this->created_at, 'Y-m-d'),
        ];
    }
}
