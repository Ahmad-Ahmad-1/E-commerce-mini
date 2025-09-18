<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'quantity' => $this->quantity,
            'product' => new ProductSummaryResource($this->product),
        ];
    }
}
