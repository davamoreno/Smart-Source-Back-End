<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller{

    public function index(){
        $posts = Post::with(['user', 'category', 'paperType', 'file'])->latest()->get();
        return response()->json($posts);
    }

    public function show($id){
        $posts = Post::with(['user', 'category', 'paperType', 'file'])->find($id);
        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json([$posts], 201);
    }

    public function create(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_id' => 'nullable|exists:category,id',
            'paper_type_id' => 'nullable|exist:paper_type,id',
            'file' => 'nullable|file|mimes:pdf,docx|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

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