<?php

namespace App\Http\Controllers\Post;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function userComment(CommentRequest $request, $id){
        $post = Post::findOrFail($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post Not Found'
            ], 404);
        }

        $comment = $post->comments()->create([
            'content' => $request->content,
            'parent_id' => null,
            'user_id' => Auth::id(),
        ]);

        if (!$request->parent_id) {
            $comment->update(['parent_id' => $comment->id]);
        } else {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->post_id != $id) {
                return response()->json([
                    'message' => 'Invalid parent comment'
                ], 400);
            }
            $comment->update(['parent_id' => $request->parent_id]);
        }

        return response()->json([
            'message' => 'Comment successfully uploaded',
            'comment' => $comment
        ], 201);
    }

    public function show($id)
    {
        $comment = Comment::with('replies')->findOrFail($id);
    
        return response()->json($comment);
    }
}
