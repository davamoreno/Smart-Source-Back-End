<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $history = $user->histories()->with('posts')->orderBy('seen_at', 'desc')->take(10)->get();
        return response()->json($history);
    }

    public function create($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $history = History::updateOrCreate([
            'post_id' => $post->id,
            'user_id' => auth()->user()->id
        ], [
            'seen_at' => now()
        ]);
    
        $historyCount = History::where('post_id', $post->id)
            ->where('user_id', auth()->user()->id)
            ->count();
    
        if ($historyCount > 10) {
            History::where('post_id', $post->id)
                ->where('user_id', auth()->user()->id)
                ->orderBy('seen_at', 'asc')
                ->take($historyCount - 10)
                ->delete();
        }
    
        return response()->json([
            'data' => $history,
            'post' => $post
        ], 201);
    }    
}
