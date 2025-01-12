<?php

namespace App\Http\Controllers\Post;

use App\Models\File;
use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;

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

    public function showUserPost(Request $request){
        $perPage = $request->input('per_page', 10);
        $posts = Post::with([
            'user', 'category', 'paperType', 'file', 'approvedBy', 
            'likes' => function ($query) {
                $query->where('user_id', auth('sanctum')->user()?->id);
            }])->where('status', 'allow')
               ->latest()
               ->paginate($perPage)
               ->through(function ($post) {
                    $post->like = !$post->likes?->isEmpty();
                    return $post;
            });

        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json($posts);
    }

    public function showPostPending()
    {
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy'])->where('status', 'pending')->orderBy('created_at', 'asc')->paginate(10);
        if ($posts->isEmpty()) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }
    
        return response()->json($posts);
    }

    public function showDenyPost(){
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy'])->latest();
        $posts = $posts->where('status', 'deny')->paginate(10);
        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }
        return response()->json($posts);
    }

    public function getUserPost($id){
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy', 'likes' => function ($query) {
            $query->where('user_id', auth('sanctum')->user()?->id);
        }]);
        
        $posts = $posts->where('status', 'allow')->find($id);

        $posts->like = !$posts->likes?->isEmpty();

        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        return response()->json([$posts], 200);
    }

    public function getMyPost(){
        $user = Auth::user();
        $posts = $user->posts()->with(['category', 'paperType', 'file', 'approvedBy'])->get();

        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found'], 404);
        }

        return response()->json(['posts' => $posts], 200);
    }

    public function create(PostRequest $request){
        $post = new Post($request->input());
        auth()->user()->posts()->save($post);

        if($request->hasFile('file')){
            $file = $request->file('file');
            $filePath = $file->store('files', 'public');
            $fileType = $file->getClientMimeType();
            $post->file()->create([
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $request->file('file')->getSize(),
                'file_type' => $fileType
            ]);
        }
        return response()->json(['message' => 'Post Success Created, Please Wait For Admin Approvement', 'post' => $post ], 201);
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