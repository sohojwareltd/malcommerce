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
        $workshopSeminar->load(['venues', 'trades']);

        return view('workshops.show', compact('workshopSeminar'));
    }

    public function enroll(Request $request, WorkshopSeminar $workshopSeminar)
    {
        if (!$workshopSeminar->is_active) {
            return redirect()->route('workshops.index')->with('error', 'This workshop is no longer accepting enrollments.');
        }

        $rules = ['name' => 'required|string|max:255'];
        $workshopSeminar->load(['venues', 'trades']);
        if ($workshopSeminar->venues->isNotEmpty()) {
            $rules['venue_id'] = 'required|exists:venues,id';
        } else {
            $rules['venue_id'] = 'nullable|exists:venues,id';
        }
        if ($workshopSeminar->trades->isNotEmpty()) {
            $rules['trade_id'] = 'required|exists:trades,id';
        } else {
            $rules['trade_id'] = 'nullable|exists:trades,id';
        }
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
        $validated = $request->validate($rules);

        if ($workshopSeminar->venues->isNotEmpty() && $request->filled('venue_id')) {
            $validVenueIds = $workshopSeminar->venues->pluck('id')->toArray();
            if (!in_array((int) $request->venue_id, $validVenueIds, true)) {
                return redirect()->back()->withInput()->withErrors(['venue_id' => 'Please select a valid venue for this workshop.']);
            }
        }
        if ($workshopSeminar->trades->isNotEmpty() && $request->filled('trade_id')) {
            $validTradeIds = $workshopSeminar->trades->pluck('id')->toArray();
            if (!in_array((int) $request->trade_id, $validTradeIds, true)) {
                return redirect()->back()->withInput()->withErrors(['trade_id' => 'Please select a valid trade for this workshop.']);
            }
        }

        if ($workshopSeminar->max_participants) {
            $current = $workshopSeminar->enrollments()->count();
            if ($current >= $workshopSeminar->max_participants) {
                return redirect()->back()->with('error', 'Sorry, this workshop has reached maximum participants.');
            }
        }

        WorkshopEnrollment::create([
            'workshop_seminar_id' => $workshopSeminar->id,
            'venue_id' => $request->filled('venue_id') ? $request->venue_id : null,
            'trade_id' => $request->filled('trade_id') ? $request->trade_id : null,
            'name' => $request->name,
            'phone' => $request->input('phone'),
            'address' => ($workshopSeminar->show_address ?? true) ? ($request->address ?? null) : null,
            'notes' => ($workshopSeminar->show_notes ?? true) ? ($request->notes ?? null) : null,
        ]);

        return redirect()->route('workshops.show', $workshopSeminar)
            ->with('success', 'You have successfully enrolled in this workshop/seminar.');
    }
}
