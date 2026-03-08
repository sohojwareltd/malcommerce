<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JobCircularController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', JobCircular::class);
        $query = JobCircular::withCount('applications')->orderBy('sort_order')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $jobCirculars = $query->paginate(20)->withQueryString();

        return view('admin.job-circulars.index', compact('jobCirculars'));
    }

    public function create()
    {
        $this->authorize('create', JobCircular::class);
        return view('admin.job-circulars.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', JobCircular::class);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:job_circulars,slug',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'deadline' => 'nullable|date',
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
            $path = $image->storeAs('job-circulars', $filename, 'public');
            $data['thumbnail'] = Storage::disk('public')->url($path);
        }

        JobCircular::create($data);

        return redirect()->route('admin.job-circulars.index')
            ->with('success', 'Job circular created successfully.');
    }

    public function show(JobCircular $jobCircular)
    {
        $this->authorize('view', $jobCircular);
        $jobCircular->load('applications');
        return view('admin.job-circulars.show', compact('jobCircular'));
    }

    public function edit(JobCircular $jobCircular)
    {
        $this->authorize('update', $jobCircular);
        return view('admin.job-circulars.edit', compact('jobCircular'));
    }

    public function update(Request $request, JobCircular $jobCircular)
    {
        $this->authorize('update', $jobCircular);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:job_circulars,slug,' . $jobCircular->id,
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'deadline' => 'nullable|date',
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
            $path = $image->storeAs('job-circulars', $filename, 'public');
            $data['thumbnail'] = Storage::disk('public')->url($path);
        }

        $jobCircular->update($data);

        return redirect()->route('admin.job-circulars.index')
            ->with('success', 'Job circular updated successfully.');
    }

    public function destroy(JobCircular $jobCircular)
    {
        $this->authorize('delete', $jobCircular);
        $jobCircular->delete();

        return redirect()->route('admin.job-circulars.index')
            ->with('success', 'Job circular deleted successfully.');
    }
}
