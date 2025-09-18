<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'status' => $this->status,
            'createdAt' => date_format($this->created_at, 'Y-m-d'),

            'customer' => $this->when(
                !$request->routeIs(['orders.myOrders']),
                fn() => new UserSummaryResource($this->user)
            ),
        ];
    }
}
