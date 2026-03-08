<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopEnrollment;
use App\Models\WorkshopSeminar;
use Illuminate\Http\Request;

class WorkshopEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkshopEnrollment::class);
        $query = WorkshopEnrollment::with('workshopSeminar')->latest();

        if ($request->filled('workshop_seminar_id')) {
            $query->where('workshop_seminar_id', $request->workshop_seminar_id);
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
        $workshopEnrollment->load('workshopSeminar');
        return view('admin.workshop-enrollments.show', compact('workshopEnrollment'));
    }
}
