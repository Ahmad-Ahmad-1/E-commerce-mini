<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentFirstLevelResource;
use App\Models\Comment;

class CommentController extends Controller
{
    public function storeProductComment(StoreCommentRequest $request, Product $product)
    {
        // if ($request->parent_id) {
        //     $parent = Comment::find($request->parent_id);
        //     if ($parent->hasParent()) {
        //         return response()->json([
        //             'error' => 'Replies to replies are not allowed.'
        //         ], 422);
        //     }
        // }

        $comment = $product->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
            // 'parent_id' => $request->parent_id,
        ]);

        return new CommentFirstLevelResource($comment);
    }

    public function updateProductComment(Product $product, Comment $comment, UpdateCommentRequest $request)
    {
        $comment->update($request->validated());

        return new CommentFirstLevelResource($comment);
    }

    public function indexProductComments(Product $product)
    {
        return CommentFirstLevelResource::collection($product->comments()->paginate(10));
    }

    public function destroyProductComment(Product $product, Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'The comment has been removed successfully.',
        ]);
    }
}
