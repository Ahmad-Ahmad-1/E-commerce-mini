<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->getFirstMediaUrl('images'),

            'averageRating' => $this->when(
                !$request->routeIs(['orders.items', 'cart.*']),
                fn() => $this->summarizedRatings()
            ),
            'categories' => $this->when(
                !$request->routeIs(['orders.items', 'cart.*']),
                fn() => CategoryResource::collection($this->categories)
            ),
            'quantity' => $this->when(
                !$request->routeIs(['orders.items', 'cart.*']),
                fn() => $this->quantity
            ),
            'price' => $this->when(
                !$request->routeIs(['orders.items']),
                fn() => $this->price
            ),
            'lastModified' => $this->when(
                !$request->routeIs(['orders.items', 'cart.*']),
                fn() => date_format($this->updated_at, 'Y-m-d')
            ),
            'seller' => $this->when(
                !$request->routeIs(['users.show', 'users.myProducts', 'orders.items', 'cart.*']),
                fn() => new UserSummaryResource($this->user)
            ),
        ];
    }
}
