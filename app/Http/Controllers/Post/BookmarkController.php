<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function show(){
        $bookmarks = auth()->user()->bookmarks->load('post');

        return response()->json($bookmarks);
    }

    public function create(Post $post){
        $bookmark = Bookmark::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id
        ]);

        return response()->json([
            'message' => 'This post bookmarked !',
            'bookmark' => $bookmark
        ]);
    }

    public function delete(Post $post){
        Bookmark::where('user_id', Auth::id())
                ->where('post_id', $post->id)
                ->firstOrFail()
                ?->delete();

        return response()->json([
            'message' => 'This post has unbookmarked !',
        ]);
    }
}
