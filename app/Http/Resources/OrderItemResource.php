<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unitPrice' => $this->price,
            'totalPrice' => $this->quantity * $this->price,
            'product' => new ProductForOrderItemResource($this->product),
        ];
    }
}
