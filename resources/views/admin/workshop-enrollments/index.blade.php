@extends('layouts.admin')

@section('title', 'Workshop Enrollments')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Workshop Enrollments</h1>
        <p class="text-neutral-600 mt-1 text-sm">View workshop/seminar enrollments</p>
    </div>
    <nav class="flex flex-wrap gap-2" aria-label="Status tabs">
        @php
            $statusTabs = [
                '' => 'All',
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'cancelled' => 'Cancelled',
            ];
            $currentStatus = request('status', '');
        @endphp
        @foreach($statusTabs as $value => $label)
            <a href="{{ route('admin.workshop-enrollments.index', array_merge(request()->query(), ['status' => $value, 'page' => null])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium {{ ($currentStatus === (string)$value) ? 'bg-primary text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4">
    <form method="GET" action="{{ route('admin.workshop-enrollments.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="min-w-[200px]">
            <label for="workshop_seminar_id" class="block text-sm font-medium text-neutral-700 mb-1">Workshop</label>
            <select name="workshop_seminar_id" id="workshop_seminar_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($workshopSeminars as $ws)
                    <option value="{{ $ws->id }}" {{ request('workshop_seminar_id') == $ws->id ? 'selected' : '' }}>{{ $ws->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label for="status" class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, phone, address..."
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Workshop</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Enrolled</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse($enrollments as $en)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 font-medium text-neutral-900">{{ $en->name }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600">{{ $en->workshopSeminar->title }}</td>
                    <td class="px-6 py-4">
                        @php($st = $en->status ?? 'pending')
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $st === 'confirmed' ? 'bg-green-100 text-green-800' : ($st === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($st) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $en->phone }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600 max-w-xs truncate">{{ $en->address ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500">{{ $en->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        @can('workshopEnrollments.view')
                        <a href="{{ route('admin.workshop-enrollments.show', $en) }}" class="text-primary hover:underline text-sm">View</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-neutral-500">No enrollments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">{{ $enrollments->links() }}</div>
</div>
@endsection
