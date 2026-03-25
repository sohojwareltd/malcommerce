<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobCircular;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
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

    public function updateStatus(Request $request, JobApplication $jobApplication, SmsService $smsService)
    {
        $this->authorize('update', $jobApplication);
        $request->validate(['status' => 'required|in:pending,shortlisted,rejected,hired']);

        $oldStatus = $jobApplication->status;
        $newStatus = $request->status;
        $jobApplication->update(['status' => $newStatus]);

        if ($oldStatus !== $newStatus && $jobApplication->phone) {
            $message = $this->buildJobApplicationStatusMessage($jobApplication, $newStatus);
            if ($message) {
                try {
                    $smsService->send($jobApplication->phone, $message);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to send job application status SMS', [
                        'job_application_id' => $jobApplication->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Application status updated.');
    }

    public function bulkUpdateStatus(Request $request, SmsService $smsService)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'integer|exists:job_applications,id',
            'status' => 'required|in:pending,shortlisted,rejected,hired',
        ]);

        $applications = JobApplication::whereIn('id', $validated['application_ids'])
            ->get();

        if ($applications->isEmpty()) {
            return redirect()->back()->with('error', 'No applications selected.');
        }

        $updatedCount = 0;
        foreach ($applications as $application) {
            $this->authorize('update', $application);

            $oldStatus = $application->status;
            $newStatus = $validated['status'];

            if ($oldStatus === $newStatus) {
                continue;
            }

            $application->update(['status' => $newStatus]);
            $updatedCount++;

            if ($application->phone) {
                $message = $this->buildJobApplicationStatusMessage($application, $newStatus);
                if ($message) {
                    try {
                        $smsService->send($application->phone, $message);
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to send bulk job application status SMS', [
                            'job_application_id' => $application->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', "{$updatedCount} application(s) status updated.");
    }

    public function destroy(JobApplication $jobApplication)
    {
        $this->authorize('delete', $jobApplication);
        $jobApplication->delete();

        return redirect()->route('admin.job-applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    public function export(Request $request): StreamedResponse
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
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $applications = $query->get();
        $filename = 'job-applications-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Applied At', 'Name', 'Job Title', 'Email', 'Phone', 'Address', 'Status']);

            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->created_at?->format('Y-m-d H:i:s'),
                    $app->name,
                    $app->jobCircular?->title ?? '',
                    $app->email ?? '',
                    $app->phone ?? '',
                    $app->address ?? '',
                    $app->status ?? 'pending',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function buildJobApplicationStatusMessage(JobApplication $jobApplication, string $status): ?string
    {
        $jobApplication->loadMissing('jobCircular');
        $circular = $jobApplication->jobCircular;
        $templates = $circular->sms_templates ?? [];
        $template = $templates[$status] ?? null;

        $defaultMessage = "Dear {$jobApplication->name}, your application for {$circular->title} is now " . ucfirst($status) . ".";
        $message = $template ?: $defaultMessage;

        $replacements = [
            '{name}' => $jobApplication->name,
            '{job_title}' => $circular->title,
            '{status}' => ucfirst($status),
            '{phone}' => $jobApplication->phone ?? '',
            '{email}' => $jobApplication->email ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
}
