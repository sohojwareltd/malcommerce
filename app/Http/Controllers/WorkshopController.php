<?php

namespace App\Http\Controllers;

use App\Models\WorkshopEnrollment;
use App\Models\WorkshopSeminar;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = WorkshopSeminar::active()
            ->where(function ($q) {
                $q->whereNull('event_date')->orWhere('event_date', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('workshops.index', compact('workshops'));
    }

    public function show(WorkshopSeminar $workshopSeminar)
    {
        if (!$workshopSeminar->is_active) {
            abort(404);
        }
        $workshopSeminar->load(['venueRelation', 'trades']);

        return view('workshops.show', compact('workshopSeminar'));
    }

    public function enroll(Request $request, WorkshopSeminar $workshopSeminar)
    {
        if (!$workshopSeminar->is_active) {
            return redirect()->route('workshops.index')->with('error', 'This workshop is no longer accepting enrollments.');
        }

        $rules = ['name' => 'required|string|max:255'];
        if ($workshopSeminar->show_phone ?? true) {
            $rules['phone'] = 'required|string|max:20';
        } else {
            $rules['phone'] = 'nullable|string|max:20';
        }
        if ($workshopSeminar->show_address ?? true) {
            $rules['address'] = 'nullable|string';
        }
        if ($workshopSeminar->show_notes ?? true) {
            $rules['notes'] = 'nullable|string';
        }
        $request->validate($rules);

        if ($workshopSeminar->max_participants) {
            $current = $workshopSeminar->enrollments()->count();
            if ($current >= $workshopSeminar->max_participants) {
                return redirect()->back()->with('error', 'Sorry, this workshop has reached maximum participants.');
            }
        }

        WorkshopEnrollment::create([
            'workshop_seminar_id' => $workshopSeminar->id,
            'name' => $request->name,
            'phone' => $request->input('phone'),
            'address' => ($workshopSeminar->show_address ?? true) ? ($request->address ?? null) : null,
            'notes' => ($workshopSeminar->show_notes ?? true) ? ($request->notes ?? null) : null,
        ]);

        return redirect()->route('workshops.show', $workshopSeminar)
            ->with('success', 'You have successfully enrolled in this workshop/seminar.');
    }
}
