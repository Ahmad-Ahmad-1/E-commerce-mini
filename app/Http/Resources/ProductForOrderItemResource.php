<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductForOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'image' => $this->getFirstMediaUrl('images'),
            'averageRating' => $this->summarizedRatings(),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
