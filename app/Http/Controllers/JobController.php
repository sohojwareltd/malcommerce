<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobCircular;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobCircular::active()
            ->where(function ($q) {
                $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('jobs.index', compact('jobs'));
    }

    public function show(JobCircular $jobCircular)
    {
        if (!$jobCircular->is_active) {
            abort(404);
        }

        return view('jobs.show', compact('jobCircular'));
    }

    public function apply(Request $request, JobCircular $jobCircular)
    {
        if (!$jobCircular->is_active) {
            return redirect()->route('jobs.index')->with('error', 'This job is no longer accepting applications.');
        }

        if ($jobCircular->deadline && $jobCircular->deadline->isPast()) {
            return redirect()->back()->with('error', 'Application deadline has passed.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => ['nullable', 'string', 'max:20', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            'gender' => 'nullable|string|max:20',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $education = $this->parseJsonField($request->education) ?? ($request->education ? [['details' => $request->education]] : null);
        $experience = $this->parseJsonField($request->experience) ?? ($request->experience ? [['details' => $request->experience]] : null);

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        JobApplication::create([
            'job_circular_id' => $jobCircular->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'education' => $education,
            'experience' => $experience,
            'resume_path' => $resumePath,
        ]);

        return redirect()->route('jobs.show', $jobCircular)
            ->with('success', 'Your application has been submitted successfully.');
    }

    protected function parseJsonField(?string $value): ?array
    {
        if (empty($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }
}
