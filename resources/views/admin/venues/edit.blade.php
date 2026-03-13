@extends('layouts.admin')

@section('title', 'Edit Venue')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Edit Venue</h1>
            <p class="text-neutral-600 mt-1 text-sm sm:text-base">{{ $venue->name }}</p>
        </div>
        <a href="{{ route('admin.venues.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm">← Back</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.venues.update', $venue) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $venue->name) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
            <textarea name="address" id="address" rows="2"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('address') border-red-500 @enderror">{{ old('address', $venue->address) }}</textarea>
            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $venue->sort_order ?? 0) }}" min="0"
                class="w-full max-w-[120px] px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('sort_order') border-red-500 @enderror">
            @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <p class="block text-sm font-medium text-neutral-700 mb-2">Trades available at this venue</p>
            <div class="space-y-2 border border-neutral-200 rounded-lg p-3 max-h-48 overflow-y-auto">
                @forelse($trades as $trade)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="trades[]" value="{{ $trade->id }}" {{ in_array($trade->id, old('trades', $venue->trades->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded">
                    <span class="text-sm text-neutral-700">{{ $trade->name }}</span>
                </label>
                @empty
                <p class="text-sm text-neutral-500">No trades yet. <a href="{{ route('admin.trades.create') }}" class="text-primary font-semibold">Add trades</a> first.</p>
                @endforelse
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">Update</button>
            <a href="{{ route('admin.venues.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">Cancel</a>
        </div>
    </form>
</div>
@endsection
