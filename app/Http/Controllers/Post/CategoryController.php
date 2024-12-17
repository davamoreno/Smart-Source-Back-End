<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::paginate(10);
        return response()->json($categories);
    }

    public function create(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ]);

            $category = Category::create([
                'name' => $request->name,
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

}
