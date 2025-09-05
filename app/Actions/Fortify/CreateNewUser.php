<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'country' => ['nullable', 'string', 'between:3,20'],
            'city' => ['nullable', 'string', 'between:3,20'],
            'image' => ['nullable', 'image']
        ])->validate();

        $user = User::create($validated);

        if (isset($input['image'])) {
            $file = $input['image'];
            $user->addMedia($file)
                ->usingFileName(uniqid('user_' . $user->id . '_') . '.' . $file->getClientOriginalExtension())
                ->toMediaCollection('profilePicture');
        }

        return $user;
    }
}
