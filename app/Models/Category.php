<?php

namespace App\Models;

use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Category extends Model
{
    //
    use HasFactory;
     protected $fillable = [
        'category_name',
    ];
    public function products(){
        return $this->hasMany(Product::class);
    }

    public static function latestTen () {
        return Category::latest()->limit(10)->get();
    }

    public static function latestTenResource () {
        return CategoryResource::collection(Category::latestTen());
    }
}
