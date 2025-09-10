<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stars' => 'required|integer|between:1,5',
        ];
    }
}
