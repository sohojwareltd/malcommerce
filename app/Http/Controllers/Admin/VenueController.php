<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\Trade;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\WorkshopSeminar::class);
        $venues = Venue::withCount('trades')->orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.venues.index', compact('venues'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\WorkshopSeminar::class);
        $trades = Trade::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.venues.create', compact('trades'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\WorkshopSeminar::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'trades' => 'nullable|array',
            'trades.*' => 'exists:trades,id',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $venue = Venue::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'sort_order' => $data['sort_order'],
        ]);
        $venue->trades()->sync($request->input('trades', []));
        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue created successfully.');
    }

    public function edit(Venue $venue)
    {
        $this->authorize('update', \App\Models\WorkshopSeminar::class);
        $trades = Trade::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.venues.edit', compact('venue', 'trades'));
    }

    public function update(Request $request, Venue $venue)
    {
        $this->authorize('update', \App\Models\WorkshopSeminar::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'trades' => 'nullable|array',
            'trades.*' => 'exists:trades,id',
        ]);
        $venue->update([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        $venue->trades()->sync($request->input('trades', []));
        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue updated successfully.');
    }

    public function destroy(Venue $venue)
    {
        $this->authorize('delete', \App\Models\WorkshopSeminar::class);
        $venue->delete();
        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue deleted successfully.');
    }
}
