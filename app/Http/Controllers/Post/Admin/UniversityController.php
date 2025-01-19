<?php

namespace App\Http\Controllers\Post\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Faculty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UniversityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('all') && $request->all === 'true') {
            $universities = University::withCount(['faculities', 'users'])
                ->get();
        } else {
            $universities = University::withCount(['faculities', 'users'])
                ->paginate(10);
        }
    
        return response()->json($universities);
    }
    

    public function store(Request $request)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities',
        ]);

        $university = University::create([
            'name' => $validated['name'],
            'created_by' => Auth::id(),
        ]);

        return response()->json($university, 201);
    }

    public function show($id)
    {
        $university = University::findOrFail($id);
        return response()->json($university);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $university = University::findOrFail($id);
        $university->update([
            'name' => $validated['name'],
        ]);

        return response()->json($university);
    }

    public function destroy($id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $university = University::findOrFail($id);
        $university->delete();

        return response()->json(['message' => 'University deleted successfully.']);
    }

    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
