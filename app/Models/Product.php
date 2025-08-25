<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product extends Model
{
    //
    use HasFactory;
protected $fillable=['title','description','image','category_id','price','quantity'];
public function category(){
        return $this->belongsTo(Category::class);
    }
 public function chart(){
        return $this->hasMany(Cart::class);
    }
}
