<?php

namespace App\Models;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'category_name',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function latestTenProducts()
    {
        return ProductResource::collection($this->products()
            ->latest()
            ->limit(10)
            ->paginate(10));
    }

    public static function latestTen()
    {
        return Category::latest()->limit(10)->get();
    }

    public static function latestTenResource()
    {
        return CategoryResource::collection(Category::latestTen());
    }
}
