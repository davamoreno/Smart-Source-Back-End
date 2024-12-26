<?php

namespace App\Http\Controllers\Post\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use Illuminate\Support\Facades\Auth;
use App\Models\University;
use Spatie\Permission\Models\Role;

class FacultyController extends Controller
{
    public function index(Request $request)
    {   
        if($request->has('all') && $request->all === 'true'){
            $faculties = Faculty::select('id', 'name')->get();
        }else{
            $faculties = Faculty::paginate(10);
        }
        return response()->json($faculties);
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name,NULL,id,university_id,' . $request->university_id,
            'university_id' => 'required|exists:universities,id',
        ]);

        $faculty = Faculty::create([
            'name' => $validated['name'],
            'university_id' => $validated['university_id'],
            'created_by' => Auth::id(),
        ]);

        return response()->json($faculty, 201);
    }

    public function show($id)
    {
        $faculty = Faculty::with('university')->findOrFail($id);
        return response()->json($faculty);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
        ]);

        $faculty = Faculty::findOrFail($id);
        $faculty->update([
            'name' => $validated['name'],
            'university_id' => $validated['university_id'],
        ]);

        return response()->json($faculty);
    }

    public function destroy($id)
    {
        $this->authorizeRole(['super_admin', 'admin']);

        $faculty = Faculty::findOrFail($id);
        $faculty->delete();

        return response()->json(['message' => 'Faculty deleted successfully.']);
    }

    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
