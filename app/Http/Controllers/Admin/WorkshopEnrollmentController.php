<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopEnrollment;
use App\Models\WorkshopSeminar;
use App\Services\SmsService;
use Illuminate\Http\Request;

class WorkshopEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkshopEnrollment::class);
        $query = WorkshopEnrollment::with(['workshopSeminar', 'venue', 'trade'])->latest();

        if ($request->filled('workshop_seminar_id')) {
            $query->where('workshop_seminar_id', $request->workshop_seminar_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $workshopSeminars = WorkshopSeminar::orderBy('title')->get(['id', 'title']);

        return view('admin.workshop-enrollments.index', compact('enrollments', 'workshopSeminars'));
    }

    public function show(WorkshopEnrollment $workshopEnrollment)
    {
        $this->authorize('view', $workshopEnrollment);
        $workshopEnrollment->load(['workshopSeminar', 'venue', 'trade']);
        return view('admin.workshop-enrollments.show', compact('workshopEnrollment'));
    }

    public function updateStatus(Request $request, WorkshopEnrollment $workshopEnrollment, SmsService $smsService)
    {
        $this->authorize('update', $workshopEnrollment);
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);

        $oldStatus = $workshopEnrollment->status ?? 'pending';
        $newStatus = $request->status;
        $workshopEnrollment->update(['status' => $newStatus]);

        if ($oldStatus !== $newStatus && $workshopEnrollment->phone) {
            $message = $this->buildEnrollmentStatusMessage($workshopEnrollment, $newStatus);
            if ($message) {
                try {
                    $smsService->send($workshopEnrollment->phone, $message);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to send workshop enrollment status SMS', [
                        'workshop_enrollment_id' => $workshopEnrollment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Enrollment status updated.');
    }

    protected function buildEnrollmentStatusMessage(WorkshopEnrollment $workshopEnrollment, string $status): ?string
    {
        $workshopEnrollment->loadMissing('workshopSeminar');
        $workshop = $workshopEnrollment->workshopSeminar;
        $templates = $workshop->sms_templates ?? [];
        $template = $templates[$status] ?? null;

        $defaultMessage = "Dear {$workshopEnrollment->name}, your enrollment for {$workshop->title} is now " . ucfirst($status) . ".";
        $message = $template ?: $defaultMessage;

        $replacements = [
            '{name}' => $workshopEnrollment->name,
            '{workshop_title}' => $workshop->title,
            '{status}' => ucfirst($status),
            '{phone}' => $workshopEnrollment->phone ?? '',
            '{venue}' => $workshop->venue ?? '',
            '{event_date}' => $workshop->event_date ? $workshop->event_date->format('M d, Y') : '',
            '{event_time}' => $workshop->event_time ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
}
