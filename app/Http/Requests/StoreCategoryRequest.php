<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'between:1,25', 'unique:categories,category_name'],
        ];
    }
}
