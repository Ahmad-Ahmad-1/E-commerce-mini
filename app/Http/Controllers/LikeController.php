<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Product $post, Request $request)
    {
        $like = $request->user()->likes()->where('post_id', $post->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        }

        $request->user()->likes()->create(['post_id' => $post->id]);
        
        return response()->json(['liked' => true]);
    }
}
