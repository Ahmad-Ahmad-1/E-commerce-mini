<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->category_name,
            'createdAt' => date_format($this->created_at, 'Y-m-d'),
            'lastModification' => date_format($this->updated_at, 'Y-m-d'),
        ];
    }
}
