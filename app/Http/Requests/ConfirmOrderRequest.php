<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string',
        ];
    }
}
