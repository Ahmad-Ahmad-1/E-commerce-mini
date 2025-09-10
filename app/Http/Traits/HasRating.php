<?php

namespace App\Http\Traits;

use App\Models\Rating;

trait HasRating
{
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function distributedRatings()
    {
        return [
            'average' => round($this->ratings()->avg('stars'), 2),
            'count'   => $this->ratings()->count(),
            'distribution' => $this->ratings()
                ->selectRaw('stars, COUNT(*) as count')
                ->groupBy('stars')
                ->pluck('count', 'stars'),
        ];
    }

    public function summarizedRatings()
    {
        return [
            'average' => round($this->ratings()->avg('stars'), 2),
            'count'   => $this->ratings()->count(),
        ];
    }
}
