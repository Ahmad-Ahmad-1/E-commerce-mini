<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->getFirstMediaUrl('profilePicture'),

            'email' => $this->when(
                $request->routeIs(['users.index', 'orders.index']),
                fn() => $this->email
            ),
            'roles' => $this->when(
                $request->routeIs(['users.index']),
                fn() => $this->getRoleNames()
            ),
        ];
    }
}
