<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopSeminar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class WorkshopSeminarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkshopSeminar::class);
        $query = WorkshopSeminar::withCount('enrollments')->orderBy('sort_order')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $workshopSeminars = $query->paginate(20)->withQueryString();

        return view('admin.workshop-seminars.index', compact('workshopSeminars'));
    }

    public function create()
    {
        $this->authorize('create', WorkshopSeminar::class);
        return view('admin.workshop-seminars.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', WorkshopSeminar::class);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:workshop_seminars,slug',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('workshop-seminars', $filename, 'public');
            $data['thumbnail'] = Storage::disk('public')->url($path);
        }

        WorkshopSeminar::create($data);

        return redirect()->route('admin.workshop-seminars.index')
            ->with('success', 'Workshop/Seminar created successfully.');
    }

    public function show(WorkshopSeminar $workshopSeminar)
    {
        $this->authorize('view', $workshopSeminar);
        $workshopSeminar->load('enrollments');
        return view('admin.workshop-seminars.show', compact('workshopSeminar'));
    }

    public function edit(WorkshopSeminar $workshopSeminar)
    {
        $this->authorize('update', $workshopSeminar);
        return view('admin.workshop-seminars.edit', compact('workshopSeminar'));
    }

    public function update(Request $request, WorkshopSeminar $workshopSeminar)
    {
        $this->authorize('update', $workshopSeminar);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:workshop_seminars,slug,' . $workshopSeminar->id,
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('workshop-seminars', $filename, 'public');
            $data['thumbnail'] = Storage::disk('public')->url($path);
        }

        $workshopSeminar->update($data);

        return redirect()->route('admin.workshop-seminars.index')
            ->with('success', 'Workshop/Seminar updated successfully.');
    }

    public function destroy(WorkshopSeminar $workshopSeminar)
    {
        $this->authorize('delete', $workshopSeminar);
        $workshopSeminar->delete();

        return redirect()->route('admin.workshop-seminars.index')
            ->with('success', 'Workshop/Seminar deleted successfully.');
    }
}
