<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'lastModified' => date_format($this->updated_at, 'Y-m-d'),
            'image' => $this->getFirstMediaUrl('images'),
            'categories' => CategoryResource::collection($this->categories)
        ];
    }
}
