<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class CategoryController extends Controller
{
    public function index(Request $request){
        if($request->has('all') && $request->all === 'true') {
          $categories = Category::select('id', 'name')->get();  
        }
        else{
            $categories = Category::paginate(5);
        }
        return response()->json($categories);
    }

    public function create(Request $request){
        try {
            $this->authorizeRole(['super_admin', 'admin']);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ]);

            $category = Category::create([
                'name' => $request->name,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Category created successfully.',
                'category' => $category
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $faculty = Category::findOrFail($id);
        $faculty->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }

}
