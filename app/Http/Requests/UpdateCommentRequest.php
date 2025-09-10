<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required|string|between:1,1000',
            // 'parent_id' => 'nullable|exists:comments,id',
        ];
    }
}
