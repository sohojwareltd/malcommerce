<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\WorkshopSeminar::class);
        $trades = Trade::withCount('venues')->orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.trades.index', compact('trades'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\WorkshopSeminar::class);
        return view('admin.trades.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\WorkshopSeminar::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        Trade::create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        return redirect()->route('admin.trades.index')
            ->with('success', 'Trade created successfully.');
    }

    public function edit(Trade $trade)
    {
        $this->authorize('update', \App\Models\WorkshopSeminar::class);
        return view('admin.trades.edit', compact('trade'));
    }

    public function update(Request $request, Trade $trade)
    {
        $this->authorize('update', \App\Models\WorkshopSeminar::class);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $trade->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        return redirect()->route('admin.trades.index')
            ->with('success', 'Trade updated successfully.');
    }

    public function destroy(Trade $trade)
    {
        $this->authorize('delete', \App\Models\WorkshopSeminar::class);
        $trade->delete();
        return redirect()->route('admin.trades.index')
            ->with('success', 'Trade deleted successfully.');
    }
}
