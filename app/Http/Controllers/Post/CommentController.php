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

        $comment->update([
            'parent_id' => $comment->id,
        ]);

        return response()->json([
            'message' => 'Main Comment successfully uploaded',
            'comment' => $comment
        ], 201);
    }

    public function addReplyComment(CommentRequest $request, $slug, $commentId)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $mainComment = Comment::find($commentId);
    
        if (!$post) {
            return response()->json([
                'message' => 'Post Not Found'
            ], 404);
        }
    
        if (!$mainComment || $mainComment->post_id != $post->id) {
            return response()->json([
                'message' => 'Invalid main comment'
            ], 400);
        }
    
        $reply = $post->comments()->create([
            'content' => $request->content,
            'parent_id' => $commentId,
            'user_id' => Auth::id(),
        ]);
    
        return response()->json([
            'message' => 'Reply comment successfully uploaded',
            'reply' => $reply
        ], 201);
    }

    public function showMainComment($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
    
        $mainComments = Comment::where('post_id', $post->id)
            ->whereColumn('parent_id', 'id')
            ->with([
                'user', 
                'user.userProfile', 
                'replies.user', 
                'replies.user.userProfile'
            ])->orderBy('created_at', 'desc')->get();
    
        return response()->json([
            'post' => $post,
            'mainComments' => $mainComments
        ]);
    }
    

    public function showReplyComment($slug, $commentId) {
    $post = Post::where('slug', $slug)->firstOrFail();
    $mainComment = Comment::where('id', $commentId)
        ->whereColumn('parent_id', 'id')
        ->where('post_id', $post->id)
        ->with([
            'user.userProfile',
            'replies' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'replies.user', 
            'replies.user.userProfile'
        ])->first();

    if (!$mainComment) {
        return response()->json([
            'message' => 'Invalid main comment'
        ], 400);
    }

    return response()->json([
        'mainComment' => $mainComment
    ]);
}

}
