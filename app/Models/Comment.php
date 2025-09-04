<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * - This is a unified Comment model that works on all resources (Product, Post, Video...),
 *   If you only want to do comments on one model, like only on products (even if they are
 *   nested comments, then you don't need morph).
 * 
 *  - 
 */

class Comment extends Model
{
    protected $fillable = ['user_id', 'body'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
