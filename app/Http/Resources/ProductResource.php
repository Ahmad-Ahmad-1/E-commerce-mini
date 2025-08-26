<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'categoryName' => Category::firstWhere('id', '=', $this->category_id)->category_name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'lastModified' => date_format($this->updated_at, 'Y-m-d'),
            'image' => $this->getFirstMediaUrl('images'),
        ];
    }
}
