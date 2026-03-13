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
            'education_options' => 'nullable|string',
            'experience_options' => 'nullable|string',
            'deadline' => 'nullable|date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'sms_templates' => 'nullable|array',
            'sms_templates.pending' => 'nullable|string|max:1000',
            'sms_templates.shortlisted' => 'nullable|string|max:1000',
            'sms_templates.rejected' => 'nullable|string|max:1000',
            'sms_templates.hired' => 'nullable|string|max:1000',
            'show_email' => 'boolean',
            'show_address' => 'boolean',
            'show_date_of_birth' => 'boolean',
            'show_gender' => 'boolean',
            'show_education' => 'boolean',
            'show_experience' => 'boolean',
            'show_resume' => 'boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['sms_templates'] = $request->input('sms_templates');
        $data['education_options'] = $this->parseOptionLines($request->input('education_options'));
        $data['experience_options'] = $this->parseOptionLines($request->input('experience_options'));
        $data['show_email'] = $request->has('show_email');
        $data['show_address'] = $request->has('show_address');
        $data['show_date_of_birth'] = $request->has('show_date_of_birth');
        $data['show_gender'] = $request->has('show_gender');
        $data['show_education'] = $request->has('show_education');
        $data['show_experience'] = $request->has('show_experience');
        $data['show_resume'] = $request->has('show_resume');

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
            'education_options' => 'nullable|string',
            'experience_options' => 'nullable|string',
            'deadline' => 'nullable|date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'sms_templates' => 'nullable|array',
            'sms_templates.pending' => 'nullable|string|max:1000',
            'sms_templates.shortlisted' => 'nullable|string|max:1000',
            'sms_templates.rejected' => 'nullable|string|max:1000',
            'sms_templates.hired' => 'nullable|string|max:1000',
            'show_email' => 'boolean',
            'show_address' => 'boolean',
            'show_date_of_birth' => 'boolean',
            'show_gender' => 'boolean',
            'show_education' => 'boolean',
            'show_experience' => 'boolean',
            'show_resume' => 'boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['sms_templates'] = $request->input('sms_templates');
        $data['education_options'] = $this->parseOptionLines($request->input('education_options'));
        $data['experience_options'] = $this->parseOptionLines($request->input('experience_options'));
        $data['show_email'] = $request->has('show_email');
        $data['show_address'] = $request->has('show_address');
        $data['show_date_of_birth'] = $request->has('show_date_of_birth');
        $data['show_gender'] = $request->has('show_gender');
        $data['show_education'] = $request->has('show_education');
        $data['show_experience'] = $request->has('show_experience');
        $data['show_resume'] = $request->has('show_resume');

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

    protected function parseOptionLines(?string $value): ?array
    {
        if (empty($value)) {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $value);
        if (!$lines) {
            return null;
        }

        $items = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $items[] = $line;
            }
        }

        return $items ?: null;
    }
}
