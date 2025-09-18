<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentSecondLevelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'content' => $this->content,
            'createdAt' => date_format($this->created_at, 'Y-m-d'),
            'lastModify' => date_format($this->updated_at, 'Y-m-d'),
            // 'commenter' => 
        ];
    }
}
