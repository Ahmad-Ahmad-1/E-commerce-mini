<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'role' => $this->getRoleNames(),
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'image' => $this->getFirstMediaUrl('profilePicture'),
            'ratings' => $this->distributedRatings(),
            'latestProducts' => ProductResource::collection($this->products()->latest()->limit(10)->get()),
            // 'latestProducts' => ProductForUserResource::collection($this->products()->latest()->limit(10)->get()),
        ];
    }
}
