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
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function chart()
    {
        return $this->hasMany(Cart::class);
    }

    public static function latestTen () {
        return Product::latest()->limit(10)->get();
    }

    public static function latestTenResource () {
        return ProductResource::collection(Product::latestTen());
    }
}
