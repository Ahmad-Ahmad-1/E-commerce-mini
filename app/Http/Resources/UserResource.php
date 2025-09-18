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
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'image' => $this->getFirstMediaUrl('profilePicture'),
            'roles' => $this->getRoleNames(),
            'ratings' => $this->distributedRatings(),
            'latestProducts' => ProductSummaryResource::collection(
                $this->products()->latest()->limit(10)->get()
            ),
        ];
    }
}
