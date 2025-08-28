<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'between:3,25', Rule::in(['Super Admin'])],
        ];
    }
}
