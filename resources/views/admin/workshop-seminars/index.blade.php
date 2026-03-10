@extends('layouts.admin')

@section('title', 'Workshops & Seminars')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Workshops & Seminars</h1>
        <p class="text-neutral-600 mt-1 text-sm">Manage workshops and view enrollments</p>
    </div>
    @can('workshopSeminars.create')
    <a href="{{ route('admin.workshop-seminars.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm text-center">
        + Add Workshop/Seminar
    </a>
    @endcan
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4">
    <form method="GET" action="{{ route('admin.workshop-seminars.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Title or slug..."
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="w-40">
            <label for="active" class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
            <select name="active" id="active" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    {{-- Mobile: cards --}}
    <div class="md:hidden divide-y divide-neutral-200">
        @forelse($workshopSeminars as $ws)
        <div class="p-4">
            <div class="flex items-start justify-between gap-2">
                <a href="{{ route('admin.workshop-seminars.show', $ws) }}" class="font-semibold text-primary hover:underline min-w-0 flex-1">{{ $ws->title }}</a>
                <div class="shrink-0 flex flex-wrap gap-1 justify-end">
                    <span class="px-2 py-0.5 text-xs rounded {{ $ws->is_active ? 'bg-green-100 text-green-800' : 'bg-neutral-200 text-neutral-700' }}">{{ $ws->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($ws->is_featured)<span class="px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800">★</span>@endif
                </div>
            </div>
            <div class="mt-2 text-sm text-neutral-600">
                <p>{{ $ws->event_date?->format('M d, Y') ?? '—' }}</p>
                @if($ws->venue)<p class="text-neutral-500 line-clamp-2">{{ $ws->venue }}</p>@endif
                <p class="mt-0.5">{{ $ws->enrollments_count }} enrollment(s)</p>
            </div>
            <div class="flex flex-wrap gap-2 mt-3">
                @can('workshopSeminars.view')
                <a href="{{ route('admin.workshop-seminars.show', $ws) }}" class="text-primary font-medium text-sm">View</a>
                @endcan
                @can('workshopSeminars.update')
                <a href="{{ route('admin.workshop-seminars.edit', $ws) }}" class="text-neutral-600 font-medium text-sm">Edit</a>
                @endcan
                @can('workshopSeminars.delete')
                <form action="{{ route('admin.workshop-seminars.destroy', $ws) }}" method="POST" class="inline" onsubmit="return confirm('Delete this workshop?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 font-medium text-sm">Delete</button>
                </form>
                @endcan
            </div>
        </div>
        @empty
        <div class="px-4 py-8 text-center text-neutral-500">No workshops/seminars found.</div>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date / Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Enrollments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse($workshopSeminars as $ws)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.workshop-seminars.show', $ws) }}" class="font-medium text-primary hover:underline">{{ $ws->title }}</a>
                        @if($ws->is_featured)<span class="ml-1 text-amber-600 text-xs">★</span>@endif
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600">{{ $ws->sort_order ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600">
                        {{ $ws->event_date?->format('M d, Y') ?? '—' }}
                        @if($ws->venue)<br><span class="text-neutral-500">{{ Str::limit($ws->venue, 30) }}</span>@endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $ws->enrollments_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-xs rounded {{ $ws->is_active ? 'bg-green-100 text-green-800' : 'bg-neutral-200 text-neutral-700' }}">
                            {{ $ws->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($ws->is_featured)<span class="ml-1 px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800">Featured</span>@endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @can('workshopSeminars.view')
                        <a href="{{ route('admin.workshop-seminars.show', $ws) }}" class="text-primary hover:underline text-sm">View</a>
                        @endcan
                        @can('workshopSeminars.update')
                        <a href="{{ route('admin.workshop-seminars.edit', $ws) }}" class="text-neutral-600 hover:underline text-sm">Edit</a>
                        @endcan
                        @can('workshopSeminars.delete')
                        <form action="{{ route('admin.workshop-seminars.destroy', $ws) }}" method="POST" class="inline" onsubmit="return confirm('Delete this workshop?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">No workshops/seminars found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">{{ $workshopSeminars->links() }}</div>
</div>
@endsection
