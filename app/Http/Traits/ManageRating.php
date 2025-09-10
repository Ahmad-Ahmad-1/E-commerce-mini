<?php

namespace App\Http\Traits;

use App\Models\Rating;

trait ManageRating
{
    public function storeRating($user, $rateable, int $stars)
    {
        Rating::updateOrCreate(
            [
                'user_id' => $user->id,
                'rateable_id' => $rateable->id,
                'rateable_type' => get_class($rateable),
            ],
            ['stars' => $stars]
        );

        return $rateable->distributedRatings();
    }

    public function removeRating($user, $rateable) {}
}
