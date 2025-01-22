<?php

namespace App\Http\Controllers\Post;


use App\Models\File;
use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use App\Models\Category;
use App\Models\PaperType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller{    
    
    public function showPostByCategoryName($categoryName) {
        $category = Category::where('name', $categoryName)->first();
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $posts = Post::with([
            'user',
            'category',
            'papertype',
            'likes' => function ($query) {
                $query->where('user_id', auth('sanctum')->user()?->id);
            },
            'bookmarks' => function($query){
                $query->where('user_id', auth('sanctum')->user()?->id);
            }
        ])->where('category_id', $category->id)->where('status', 'allow')->get();

        $posts->transform(function ($post) {
            $post->like = !$post->likes->isEmpty();
            $post->bookmark = !$post->bookmarks->isEmpty();
            return $post;
        });
    
        return response()->json($posts);
    }

    public function showPostByPaperTypeName($paperTypeName) {
        $paperType = PaperType::where('name', $paperTypeName)->first();
        if (!$paperType) {
            return response()->json(['message' => 'PaperType not found'], 404);
        }

        $posts = Post::with([
            'user',
            'category',
            'papertype',
            'likes' => function ($query) {
                $query->where('user_id', auth('sanctum')->user()?->id);
            },
            'bookmarks' => function($query){
                $query->where('user_id', auth('sanctum')->user()?->id);
            }
        ])->where('paper_type_id', $paperType->id)->where('status', 'allow')->get();

        $posts->transform(function ($post) {
            $post->like = !$post->likes->isEmpty();
            $post->bookmark = !$post->bookmarks->isEmpty();
            return $post;
        });

        return response()->json($posts);
    }

    public function showUserPost(Request $request){
        $perPage = $request->input('per_page', 10);
        $keyword = $request->input('keyword');
        $sortBy = $request->input('sort_by');
        $sortOrder = $request->input('sort_order', 'asc');
        $categoryId = $request->input('category_id');
        $paperTypeId = $request->input('paper_type_id');

        $validSortColumns = ['title', 'created_at', 'category_id', 'paper_type_id'];

        $posts = Post::with([
            'user',
            'category', 
            'paperType', 
            'file', 
            'approvedBy', 
            'likes' => function ($query) {
                $query->where('user_id', auth('sanctum')->user()?->id);
            },
            'bookmarks' => function($query){
                $query->where('user_id', auth('sanctum')->user()?->id);
            }
        ])->where('status', 'allow')->latest();

        if ($keyword) {
            $posts->where(function ($subQuery) use ($keyword) {
                $subQuery->where('title', 'LIKE', '%' . $keyword . '%')
                         ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($categoryId) {
            $posts->where('category_id', $categoryId);
        }

        if ($paperTypeId) {
            $posts->where('paper_type_id', $paperTypeId);
        }

        if ($sortBy && in_array($sortBy, $validSortColumns)) {
            $posts->orderBy($sortBy, $sortOrder);
        }

        $posts = $posts->paginate($perPage)->through(function ($post) {
            $post->like = !$post->likes?->isEmpty();
            $post->bookmark = !$post->bookmarks?->isEmpty();
            return $post;
        });

        if ($posts->isEmpty()) {
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
        $posts = Post::with(['user', 'category', 'paperType', 'file', 'approvedBy']);
        $posts = $posts->where('status', 'deny')->latest()->paginate(10);
        if (!$posts) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }
        return response()->json($posts);
    }

    public function getDetailPost($slug){
        $post = Post::with([
            'user', 
            'category', 
            'paperType', 
            'file', 
            'approvedBy',
            'comments',
            'comments.replies', 
            'likes' => function ($query) {
                $query->where('user_id', auth('sanctum')->user()?->id);
            },
            'bookmarks' => function($query){
                $query->where('user_id', auth('sanctum')->user()?->id);
            },
        ])->where('slug', $slug)
          ->where('status', 'allow')
          ->first();
        
        if (!$post) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }

        $post->like = !$post->likes?->isEmpty();
        $post->bookmark = !$post->bookmarks?->isEmpty();
        return response()->json($post, 200);
    }

    public function getMyPost(){
        $user = Auth::user();
        $posts = $user->posts()->with([
        'category', 
        'paperType', 
        'file', 
        'approvedBy'
        ])->get();

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

    public function update(PostRequest $request, $slug) {
        $post = Post::where('slug', $slug)
            ->where('status', 'pending')
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found or cannot be updated'], 404);
        }

        $post->fill($request->only(['title', 'description', 'category_id', 'paper_type_id']));

        $post->save();

        if ($request->hasFile('file')) {
            if ($post->file) {
                Storage::disk('public')->delete($post->file->file_path);
                $post->file()->delete();
            }

            $file = $request->file('file');
            $filePath = $file->store('files', 'public');
            $fileType = $file->getClientMimeType();

            $post->file()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $fileType,
            ]);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }

    public function delete($slug) {
        $post = Post::where('slug', $slug)->where('user_id', auth()->user()->id)->firstOrFail();

        if (!$post) {
            return response()->json(['message' => 'Post not found or you are not authorized to delete this post'], 404);
        }

        if ($post->file) {
            Storage::disk('public')->delete($post->file->file_path);
            $post->file()->delete();
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }


    private function authorizeRole(array $roles) {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}