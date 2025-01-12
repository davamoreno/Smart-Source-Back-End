<?php

namespace App\Http\Controllers\Post;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LikeController extends Controller
{
    public function show(Post $post){
       return response()->json($post->likes->load('user')->pluck('user'));
    }

    public function create(Post $post){
        if ($post->status !== 'allow') {
            throw ValidationException::withMessages([
                'status' => 'Status is not allowed'
            ]);
        }
        $like = Like::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id
        ]);

        $this->calculatePostLike($post);
        
        return response()->json([
            'message' => 'This post liked !',
            'data' => $like,
            'post' => $post
        ], 201);
    }
    
    public function delete(Post $post){
        Like::where('user_id', auth()->user()->id)
                ->where('post_id', $post->id)
                ->firstOrFail()
                ?->delete();

        $this->calculatePostLike($post);

        return response()->json([
            'message' => 'This post has unliked !',
            'post' => $post
        ]);
    }

    public function calculatePostLike(Post $post){
        $post->likes_count = $post->likes->count();
        $post->save();
    }
}
