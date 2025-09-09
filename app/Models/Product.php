<?php

namespace App\Models;

use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{

    use HasFactory, InteractsWithMedia;

    protected $fillable = ['title', 'description', 'price', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function likes() {}

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function latestTenResource()
    {
        return ProductResource::collection(
            Product::with(
                'categories'
            )->latest()->limit(10)->get()
        );
    }
}
