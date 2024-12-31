<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\File;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller{    

    public function search(Request $request){
        $keyword = $request->input('keyword');

        $post = Post::where('title', 'LIKE', '%' . $keyword . '%')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Search results retrieved successfully',
        ], 200);
    }

    public function getAllUserPost(Request $request){
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy'])->latest();
        if ($request->has('allow') && $request->allow === 'true') {
            $posts = $posts->where('status', 'allow')->get();
        }
        else if($request->has('deny') && $request->deny === 'true') {
            $posts = $posts->where('status', 'deny')->get();
        }
        else
        {
            $posts = $posts->where('status', 'pending')->get();
        }

        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json($posts);
    }

    public function getUserPost(Request $request, $id){
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy']);
            if($request->has('allow') && $request->allow === 'true') {
                $posts = $posts->where('status', 'allow')->find($id);
            }

        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json([$posts], 200);
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
        return response()->json(['message' => 'Post Success Created, Please Wait For Admin Approvement', 'post' => $post], 201);
    }

    public function validatePost(Request $request, $id){
        $this->authorizeRole(['admin', 'super_admin']);
        $post = Post::find($id);

        if(!$post)
        {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        if ($post->report_status === 'accept' && $post->status === 'deny') 
        {
            return response()->json(['message' => 'This Post Has Been Taken Down'], 404);
        }
        else if ($post->status === 'deny') 
        {
            return response()->json(['message' => 'This Post Has Been Denied'], 404);
        }
        else if ($post->status === 'allow') 
        {
            return response()->json(['message' => 'This Post Has Been Allowed'], 404);
        }

        $request->validate([
            'status' => 'required|in:allow,deny',
        ]);

        $post->status = $request->input('status');
        $post->approve_at = $request->input('status') === 'allow' ? now() : null;
        $post->approve_by = Auth::id();
        $post->save();

        return response()->json(['message' => 'Post status updated successfully', 'Post' => $post], 201);
    }

    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}