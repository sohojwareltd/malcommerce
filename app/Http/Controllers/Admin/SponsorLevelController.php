<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SponsorLevelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SponsorLevel::class, 'sponsor_level');
    }

    public function index()
    {
        $levels = SponsorLevel::query()->orderBy('rank')->withCount('users')->get();

        return view('admin.sponsor-levels.index', compact('levels'));
    }

    public function create()
    {
        return view('admin.sponsor-levels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rank' => 'required|integer|min:0|unique:sponsor_levels,rank',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'is_default_for_new' => 'sometimes|boolean',
        ]);

        $data['is_default_for_new'] = $request->boolean('is_default_for_new');

        if ($data['is_default_for_new']) {
            SponsorLevel::query()->update(['is_default_for_new' => false]);
        }

        SponsorLevel::create($data);

        return redirect()->route('admin.sponsor-levels.index')
            ->with('success', 'Level created.');
    }

    public function edit(SponsorLevel $sponsor_level)
    {
        return view('admin.sponsor-levels.edit', ['level' => $sponsor_level]);
    }

    public function update(Request $request, SponsorLevel $sponsor_level)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rank' => ['required', 'integer', 'min:0', Rule::unique('sponsor_levels', 'rank')->ignore($sponsor_level->id)],
            'commission_percent' => 'required|numeric|min:0|max:100',
            'is_default_for_new' => 'sometimes|boolean',
        ]);

        $data['is_default_for_new'] = $request->boolean('is_default_for_new');

        if ($data['is_default_for_new']) {
            SponsorLevel::query()->where('id', '!=', $sponsor_level->id)->update(['is_default_for_new' => false]);
        }

        $sponsor_level->update($data);

        return redirect()->route('admin.sponsor-levels.index')
            ->with('success', 'Level updated.');
    }

    public function destroy(SponsorLevel $sponsor_level)
    {
        if ($sponsor_level->users()->exists()) {
            return redirect()->route('admin.sponsor-levels.index')
                ->with('error', 'Cannot delete a level that is assigned to sponsors.');
        }

        $sponsor_level->delete();

        return redirect()->route('admin.sponsor-levels.index')
            ->with('success', 'Level deleted.');
    }
}
