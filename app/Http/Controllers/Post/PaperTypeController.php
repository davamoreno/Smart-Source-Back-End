<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\PaperType;
use Illuminate\Http\Request;

class PaperTypeController extends Controller
{
    public function create(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:paper_types',
            ]);

            $paperType = PaperType::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'paperType created successfully.',
                'paperType' => $paperType
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create paperType.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
