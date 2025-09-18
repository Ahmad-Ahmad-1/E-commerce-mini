<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserRolesRequest;
use App\Http\Resources\UserSummaryResource;

class UserController extends Controller
{
    public function index()
    {
        return UserSummaryResource::collection(User::latest()->paginate(10));
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        if ($user->id != auth()->id()) {
            return response()->json([
                'message' => "You can't modify other users.",
            ]);
        }

        $user->update($request->validated());

        $user->clearMediaCollection('profilePicture');

        if ($request->hasFile('image')) {
            $user->addMediaFromRequest('image')
                ->usingFileName(uniqid('user_' . $user->id . '_') .
                    '.' . $request->file('image')->extension())
                ->toMediaCollection('profilePicture');
        }

        return response()->json([
            'message' => 'Your profile has been updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function updateRoles(User $user, UpdateUserRolesRequest $request)
    {
        $user->syncRoles($request->validated('role'));

        return response()->json([
            'message' => 'The user is a Super Admin now.',
            'user' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin') && $user->id != auth()->id()) {
            return response()->json([
                'message' => "This user is Super Admin, you can't delete him.",
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'user has been deleted successfully.',
        ]);
    }
}
