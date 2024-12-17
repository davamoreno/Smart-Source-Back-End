<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller{

    public function search(Request $request){
        $keyword = $request->input('keyword');

        $post = Post::where('title', 'LIKE', '%', $keyword, '%')->paginate(10);

        return response()->json($post);
    }

    public function getAllUserPost(){
        $posts = Post::with(['user', 'category', 'paperType', 'file'])->latest()->get();
        return response()->json($posts);
    }

    public function getUserPost($id){
        $posts = Post::with(['user', 'category', 'paperType', 'file'])->find($id);
        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json([$posts], 201);
    }

    public function create(PostRequest $request){
        $post = new Post($request->input());
        auth()->user()->posts()->save($post);

        if($request->hasFile('file')){
            $filePath = $request->file('file')->store('files');
            $post->file()->create([
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $request->file('file')->getSize()
            ]);
        }
        return response()->json(['message' => 'Post Has Been Created', 'post' => $post], 201);
    }
}