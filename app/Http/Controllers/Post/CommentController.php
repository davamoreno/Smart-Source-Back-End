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
    public function mainComment(CommentRequest $request, $slug){
        $post = Post::where('slug', $slug)->firstOrFail();

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

        return response()->json([
            'message' => 'Main Comment successfully uploaded',
            'comment' => $comment
        ], 201);
    }

    public function addReplyComment(CommentRequest $request, $slug, $parent_id)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $parentComment = Comment::find($parent_id);
    
        if (!$post) {
            return response()->json([
                'message' => 'Post Not Found'
            ], 404);
        }
  
        if (!$parentComment || $parentComment->post_id != $post->id) {
            return response()->json([
                'message' => 'Invalid parent comment'
            ], 400);
        }
    
        $comment = $post->comments()->create([
            'content' => $request->content,
            'parent_id' => $parent_id,
            'user_id' => Auth::id(),
        ]);
    
        return response()->json([
            'message' => 'Reply comment successfully uploaded',
            'comment' => $comment
        ], 201);
    }

    public function show($slug) {
        $post = Post::where('slug', $slug)
            ->with([
                'comments.user', 
                'comments.user.userProfile', 
                'comments.replies'
            ])
            ->firstOrFail();

        return response()->json($post);
    }
}
