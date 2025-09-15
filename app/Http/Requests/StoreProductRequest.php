<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'between:2,25', 'string'],
            'description' => ['required', 'string', 'max:1000'],
            'category_name' => ['required', 'array'],
            'category_name.*' => ['required', 'string', 'between:2,25', 'exists:categories,category_name'],
            'price' => ['required'],
            'quantity' => ['required', 'min:1'],
            'image' => ['required', 'image'],
        ];
    }
}
