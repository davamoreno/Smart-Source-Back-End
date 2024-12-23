<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\PaperType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaperTypeController extends Controller
{
    public function getPaperTypes(){
        $paperTypes = PaperType::paginate(5);
        return response()->json($paperTypes);
    }

    public function create(Request $request){
        try {
            $this->authorizeRole(['super_admin', 'admin']);

            $request->validate([
                'name' => 'required|string|max:255|unique:paper_types',
            ]);

            $paperType = PaperType::create([
                'name' => $request->name,
                'created_by' => Auth::id(),
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

    public function destroy($id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $faculty = PaperType::findOrFail($id);
        $faculty->delete();

        return response()->json(['message' => 'Paper Type deleted successfully.']);
    }

    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
