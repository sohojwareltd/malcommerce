@extends('layouts.admin')

@section('title', 'Create sponsor level')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Create sponsor level</h1>
    <p class="text-neutral-600 mt-1 text-sm">Lower rank = closer to the top (0 = apex).</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-xl">
    <form action="{{ route('admin.sponsor-levels.store') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="rank" class="block text-sm font-medium text-neutral-700 mb-1">Rank <span class="text-red-500">*</span></label>
            <input type="number" name="rank" id="rank" value="{{ old('rank', 1000) }}" min="0" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
            @error('rank')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="commission_percent" class="block text-sm font-medium text-neutral-700 mb-1">Commission % <span class="text-red-500">*</span></label>
            <input type="number" name="commission_percent" id="commission_percent" value="{{ old('commission_percent', 0) }}" min="0" max="100" step="0.01" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
            @error('commission_percent')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_default_for_new" id="is_default_for_new" value="1" class="rounded border-neutral-300" {{ old('is_default_for_new') ? 'checked' : '' }}>
            <label for="is_default_for_new" class="text-sm text-neutral-700">Default level for newly created sponsors</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-primary-light font-semibold">Save</button>
            <a href="{{ route('admin.sponsor-levels.index') }}" class="px-5 py-2 rounded-lg border border-neutral-300 text-neutral-700 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
