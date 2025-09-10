<?php

namespace App\Http\Controllers;

use App\Http\Traits\HasRating;
use App\Models\User;
use App\Models\Product;
use App\Http\Services\RatingService;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Traits\ManageRating;

class RatingController extends Controller
{
    use ManageRating;

    public function storeProductRating(StoreRatingRequest $request, Product $product)
    {
        return response()->json($this->storeRating(
            $request->user(),
            $product,
            $request->validated('stars'),
        ));
    }

    public function storeUserRating(StoreRatingRequest $request, User $user)
    {
        if (!$user->hasRole('Super Admin')) {
            return response()->json([
                'message' => "This user is not a seller, you can't rate him.",
            ], 400);
        }

        return response()->json($this->storeRating(
            $request->user(),
            $user,
            $request->validated('stars'),
        ));
    }
}
