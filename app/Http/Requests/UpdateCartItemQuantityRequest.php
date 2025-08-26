<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemQuantityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
        ];
    }
}
