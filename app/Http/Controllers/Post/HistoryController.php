<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Post;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function show(){
        $history = auth()->user()->histories->load('post');
        
        return response()->json($history);
    }

    public function create($slug){
        $post = Post::where('slug', $slug)->firstOrFail();

        $history = History::updateOrCreate([
            'post_id' => $post->id,
            'user_id' => auth()->user()->id,
            'seen_at' => now()
        ]);

        return response()->json([
            'data' => $history,
            'post' => $post
        ], 201);
    }
}
