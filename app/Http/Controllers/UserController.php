<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function update(User $user, UpdateUserRequest $request)
    {
        $user->syncRoles($request->validated('role'));

        return response()->json([
            'message' => 'The user is a Super Admin now.'
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin')) {
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
