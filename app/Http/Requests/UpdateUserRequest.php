<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'between:3,20',
                // You can also use $this->user()->id
                Rule::unique('users', 'phone')->ignore($this->route('user')->id),
            ],
            'country' => ['nullable', 'string', 'between:3,20'],
            'city' => ['nullable', 'string', 'between:3,20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image'],
        ];
    }
}
