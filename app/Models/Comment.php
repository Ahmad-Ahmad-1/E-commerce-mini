<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'content', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function hasParent()
    {
        return $this->parent_id ? true : false;
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function repliesCount() {
        return $this->replies()->count();
    }
}
