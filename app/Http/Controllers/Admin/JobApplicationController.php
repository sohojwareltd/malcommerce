<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobCircular;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', JobApplication::class);
        $query = JobApplication::with('jobCircular')->latest();

        if ($request->filled('job_circular_id')) {
            $query->where('job_circular_id', $request->job_circular_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $applications = $query->paginate(20)->withQueryString();
        $jobCirculars = JobCircular::orderBy('title')->get(['id', 'title']);

        return view('admin.job-applications.index', compact('applications', 'jobCirculars'));
    }

    public function show(JobApplication $jobApplication)
    {
        $this->authorize('view', $jobApplication);
        $jobApplication->load('jobCircular');
        return view('admin.job-applications.show', compact('jobApplication'));
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
        $this->authorize('update', $jobApplication);
        $request->validate(['status' => 'required|in:pending,shortlisted,rejected,hired']);

        $jobApplication->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Application status updated.');
    }
}
