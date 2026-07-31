<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;

class InstituteController extends Controller
{
    public function index(Request $request)
    {
        $query = Institute::query()->withCount(['students']);

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $institutes = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.institutes.index', compact('institutes'));
    }

    public function create()
    {
        return view('admin.institutes.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateInstitute($request);

        $institute = Institute::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $institute->id,
                'name' => $institute->name,
                'message' => 'Institute created.',
            ], 201);
        }

        return redirect()->route('admin.institutes.index')->with('success', 'Institute created.');
    }

    public function edit(Institute $institute)
    {
        return view('admin.institutes.edit', compact('institute'));
    }

    public function update(Request $request, Institute $institute)
    {
        $validated = $this->validateInstitute($request);

        $institute->update($validated);

        return redirect()->route('admin.institutes.index')->with('success', 'Institute updated.');
    }

    public function destroy(Institute $institute)
    {
        $institute->delete();

        return redirect()->route('admin.institutes.index')->with('success', 'Institute deleted.');
    }

    public function restore(int $institute)
    {
        Institute::onlyTrashed()->findOrFail($institute)->restore();

        return redirect()->route('admin.institutes.index', ['trashed' => 1])->with('success', 'Institute restored.');
    }

    protected function validateInstitute(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
}
