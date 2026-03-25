@extends('layouts.admin')

@section('title', 'Workshop Enrollments')

@push('styles')
<style>
.print-only { display: none; }
@media print {
    body * { visibility: hidden; }
    .print-report, .print-report * { visibility: visible; }
    .print-report-table, .print-report-table * { visibility: visible; }
    .print-report { position: absolute; left: 0; top: 0; width: 100%; }
    .print-report-table { position: relative; }
    .no-print { display: none !important; }
    .print-only { display: block !important; visibility: visible; }
    .print-report-table { font-size: 11px; }
    .print-report-table .no-print { display: none !important; }
}
</style>
@endpush

@section('content')
<div class="no-print mb-4 sm:mb-6 flex flex-col gap-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Workshop Enrollments</h1>
            <p class="text-neutral-600 mt-1 text-sm">View and manage workshop/seminar enrollments</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <a href="{{ route('admin.workshop-enrollments.export', request()->query()) }}" class="inline-flex items-center gap-2 bg-neutral-200 text-neutral-700 hover:bg-neutral-300 px-4 py-2 rounded-lg transition font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v10m0 0l-4-4m4 4l4-4M4 20h16"></path></svg>
                Export CSV
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-neutral-100 text-neutral-700 hover:bg-neutral-200 px-4 py-2 rounded-lg transition font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-1v8"></path></svg>
                Print report
            </button>
        </div>
    </div>
    <nav class="flex flex-wrap gap-2" aria-label="Status tabs">
        @php
            $statusTabs = ['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];
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

<div class="no-print bg-white rounded-xl shadow-sm border border-neutral-200 p-4 sm:p-6 mb-4">
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
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
    <div class="print-report p-6">
        <h2 class="text-xl font-bold text-neutral-900">Workshop Enrollments Report</h2>
        <p class="text-sm text-neutral-600 print-only mt-1">Printed on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
    <div class="md:hidden divide-y divide-neutral-200 no-print">
        @forelse($enrollments as $en)
        <div class="p-4 hover:bg-neutral-50/50">
            @php($st = $en->status ?? 'pending')
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-neutral-900">{{ $en->name }}</p>
                    <p class="text-sm text-neutral-600 mt-0.5">{{ $en->workshopSeminar->title }}</p>
                    <p class="text-sm text-neutral-500 mt-1">{{ $en->phone }}</p>
                    @if($en->address)
                    <p class="text-sm text-neutral-500 mt-0.5 line-clamp-2">{{ $en->address }}</p>
                    @endif
                </div>
                <span class="shrink-0 inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $st === 'confirmed' ? 'bg-green-100 text-green-800' : ($st === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($st) }}</span>
            </div>
            <p class="text-xs text-neutral-400 mt-2">{{ $en->created_at->format('M d, Y') }}</p>
            @can('workshopEnrollments.view')
            <a href="{{ route('admin.workshop-enrollments.show', $en) }}" class="inline-block mt-3 text-primary font-medium text-sm">View →</a>
            @endcan
        </div>
        @empty
        <div class="px-4 py-12 text-center text-neutral-500">No enrollments found.</div>
        @endforelse
    </div>

    <div class="hidden md:block overflow-x-auto print-report-table">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Workshop</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Venue / Trade</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Phone</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Enrolled</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white">
                @forelse($enrollments as $en)
                <tr class="hover:bg-neutral-50/50">
                    <td class="px-5 py-4 font-medium text-neutral-900">{{ $en->name }}</td>
                    <td class="px-5 py-4 text-sm text-neutral-600">{{ $en->workshopSeminar->title }}</td>
                    <td class="px-5 py-4 text-sm text-neutral-600">
                        @if($en->venue_id && $en->relationLoaded('venue') && $en->venue){{ $en->venue->name }}@endif
                        @if($en->trade_id && $en->relationLoaded('trade') && $en->trade){{ $en->venue_id && $en->venue ? ' · ' : '' }}{{ $en->trade->name }}@endif
                        @if(!$en->venue_id && !$en->trade_id)—@endif
                    </td>
                    <td class="px-5 py-4">
                        @php($st = $en->status ?? 'pending')
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $st === 'confirmed' ? 'bg-green-100 text-green-800' : ($st === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($st) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm">{{ $en->phone }}</td>
                    <td class="px-5 py-4 text-sm text-neutral-500">{{ $en->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-4 text-right no-print">
                        @can('workshopEnrollments.view')
                        <a href="{{ route('admin.workshop-enrollments.show', $en) }}" class="text-primary hover:underline text-sm font-medium">View</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-neutral-500">No enrollments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print px-4 py-3 border-t border-neutral-200 bg-neutral-50/50">{{ $enrollments->links() }}</div>
</div>
@endsection
