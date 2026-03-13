@extends('layouts.admin')

@section('title', 'Venues')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Venues</h1>
        <p class="text-neutral-600 mt-1 text-sm sm:text-base">Manage venues for workshops</p>
    </div>
    <a href="{{ route('admin.venues.create') }}" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
        + Add Venue
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Address</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Trades</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Sort</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($venues as $venue)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $venue->name }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600 max-w-md truncate">{{ $venue->address ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500 text-right">{{ $venue->trades_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500 text-right">{{ $venue->sort_order ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-right space-x-2">
                        <a href="{{ route('admin.venues.edit', $venue) }}" class="text-primary hover:text-primary-light font-semibold">Edit</a>
                        <form action="{{ route('admin.venues.destroy', $venue) }}" method="POST" class="inline" onsubmit="return confirm('Delete this venue?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-neutral-500">No venues yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">
        {{ $venues->links() }}
    </div>
</div>
@endsection
