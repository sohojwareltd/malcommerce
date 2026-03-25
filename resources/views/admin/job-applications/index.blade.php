@extends('layouts.admin')

@section('title', 'Job Applications')

@push('styles')
<style>
.print-only { display: none; }
.status-pill { display:inline-flex; padding:2px 10px; font-size:12px; font-weight:600; border-radius:9999px; }
.status-pending { background:#fef3c7; color:#92400e; }
.status-shortlisted { background:#dbeafe; color:#1e40af; }
.status-rejected { background:#fee2e2; color:#991b1b; }
.status-hired { background:#dcfce7; color:#166534; }
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
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Job Applications</h1>
            <p class="text-neutral-600 mt-1 text-sm">View and manage job applications</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-neutral-100 text-neutral-700 hover:bg-neutral-200 px-4 py-2 rounded-lg transition font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-1v8"></path></svg>
                Print report
            </button>
        </div>
    </div>
    <nav class="flex flex-wrap gap-2" aria-label="Status tabs">
        @php
            $statusTabs = ['' => 'All', 'pending' => 'Pending', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected', 'hired' => 'Hired'];
            $currentStatus = request('status', '');
        @endphp
        @foreach($statusTabs as $value => $label)
            <a href="{{ route('admin.job-applications.index', array_merge(request()->query(), ['status' => $value, 'page' => null])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium {{ ($currentStatus === (string)$value) ? 'bg-primary text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>

@php
    $statusCounts = [
        'total' => $applications->total(),
        'pending' => $applications->where('status', 'pending')->count(),
        'shortlisted' => $applications->where('status', 'shortlisted')->count(),
        'hired' => $applications->where('status', 'hired')->count(),
    ];
    $canUpdateApplications = auth()->user()?->can('jobApplications.update') ?? false;
@endphp
<div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Total</p>
        <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $statusCounts['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Pending</p>
        <p class="text-2xl font-bold text-amber-700 mt-1">{{ $statusCounts['pending'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Shortlisted</p>
        <p class="text-2xl font-bold text-blue-700 mt-1">{{ $statusCounts['shortlisted'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-green-600">Hired</p>
        <p class="text-2xl font-bold text-green-700 mt-1">{{ $statusCounts['hired'] }}</p>
    </div>
</div>

<div class="no-print bg-white rounded-xl shadow-sm border border-neutral-200 p-4 sm:p-6 mb-4">
    <form method="GET" action="{{ route('admin.job-applications.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="min-w-[200px]">
            <label for="job_circular_id" class="block text-sm font-medium text-neutral-700 mb-1">Job</label>
            <select name="job_circular_id" id="job_circular_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All Jobs</option>
                @foreach($jobCirculars as $jc)
                    <option value="{{ $jc->id }}" {{ request('job_circular_id') == $jc->id ? 'selected' : '' }}>{{ $jc->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[120px]">
            <label for="status" class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, email, phone, address..."
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">Filter</button>
            <a href="{{ route('admin.job-applications.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-neutral-100 text-neutral-700 hover:bg-neutral-200">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
    @can('jobApplications.update')
    <div class="no-print px-4 sm:px-6 py-3 border-b border-neutral-200 bg-neutral-50">
        <form id="bulk-status-form" method="POST" action="{{ route('admin.job-applications.bulk-update-status') }}" class="flex flex-wrap items-center gap-2 sm:gap-3">
            @csrf
            @method('PATCH')
            <span class="text-sm font-medium text-neutral-700">Bulk status:</span>
            <select name="status" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">Select status</option>
                <option value="pending">Pending</option>
                <option value="shortlisted">Shortlisted</option>
                <option value="rejected">Rejected</option>
                <option value="hired">Hired</option>
            </select>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">Apply to selected</button>
            <span class="text-xs text-neutral-500">Select rows first</span>
        </form>
    </div>
    @endcan

    <div class="print-report p-6">
        <h2 class="text-xl font-bold text-neutral-900">Job Applications Report</h2>
        <p class="text-sm text-neutral-600 print-only mt-1">Printed on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
    <div class="md:hidden divide-y divide-neutral-200 no-print">
        @forelse($applications as $app)
        <div class="p-4 hover:bg-neutral-50/50">
            @can('jobApplications.update')
            <div class="mb-2">
                <label class="inline-flex items-center gap-2 text-xs text-neutral-600">
                    <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" form="bulk-status-form" class="rounded border-neutral-300 text-primary focus:ring-primary">
                    Select for bulk update
                </label>
            </div>
            @endcan
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-neutral-900 truncate">{{ $app->name }}</p>
                    <p class="text-sm text-neutral-600 mt-0.5">{{ $app->jobCircular->title }}</p>
                    <p class="text-sm text-neutral-500 mt-1 truncate">{{ $app->email }}</p>
                    <p class="text-sm text-neutral-500">{{ $app->phone }}</p>
                    @if($app->address)
                    <p class="text-sm text-neutral-500 mt-0.5 line-clamp-2">{{ $app->address }}</p>
                    @endif
                </div>
                <span class="shrink-0 status-pill {{ $app->status === 'hired' ? 'status-hired' : ($app->status === 'shortlisted' ? 'status-shortlisted' : ($app->status === 'rejected' ? 'status-rejected' : 'status-pending')) }}">{{ ucfirst($app->status) }}</span>
            </div>
            <p class="text-xs text-neutral-400 mt-2">{{ $app->created_at->format('M d, Y h:i A') }}</p>
            @can('jobApplications.update')
            <form method="POST" action="{{ route('admin.job-applications.update-status', $app) }}" class="mt-3 flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                    <option value="pending" {{ $app->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shortlisted" {{ $app->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                    <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="hired" {{ $app->status === 'hired' ? 'selected' : '' }}>Hired</option>
                </select>
                <button type="submit" class="px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:opacity-90">Update</button>
            </form>
            @endcan
            <div class="mt-3 flex gap-3 items-center">
                @can('jobApplications.view')
                <a href="{{ route('admin.job-applications.show', $app) }}" class="text-primary font-medium text-sm">View</a>
                @endcan
                @can('jobApplications.delete')
                <form method="POST" action="{{ route('admin.job-applications.destroy', $app) }}" onsubmit="return confirm('Delete this application?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 font-medium text-sm">Delete</button>
                </form>
                @endcan
            </div>
        </div>
        @empty
        <div class="px-4 py-12 text-center text-neutral-500">No applications found.</div>
        @endforelse
    </div>

    <div class="hidden md:block overflow-x-auto print-report-table">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    @can('jobApplications.update')
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider no-print">
                        <input type="checkbox" id="select-all-applications" class="rounded border-neutral-300 text-primary focus:ring-primary">
                    </th>
                    @endcan
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Job</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Email / Phone</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Address</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">Applied</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-neutral-600 uppercase tracking-wider no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white">
                @forelse($applications as $app)
                <tr class="hover:bg-neutral-50/50 odd:bg-white even:bg-neutral-50/30">
                    @can('jobApplications.update')
                    <td class="px-5 py-4 no-print">
                        <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" form="bulk-status-form" class="application-checkbox rounded border-neutral-300 text-primary focus:ring-primary">
                    </td>
                    @endcan
                    <td class="px-5 py-4 font-medium text-neutral-900">{{ $app->name }}</td>
                    <td class="px-5 py-4 text-sm text-neutral-600">{{ $app->jobCircular->title }}</td>
                    <td class="px-5 py-4 text-sm"><span class="block truncate max-w-[200px]">{{ $app->email }}</span><span class="text-neutral-500 text-xs">{{ $app->phone }}</span></td>
                    <td class="px-5 py-4 text-sm text-neutral-600 max-w-[220px] truncate">{{ $app->address ?? '—' }}</td>
                    <td class="px-5 py-4">
                        @can('jobApplications.update')
                        <form method="POST" action="{{ route('admin.job-applications.update-status', $app) }}" class="flex items-center gap-2 no-print">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="px-3 py-1.5 border border-neutral-300 rounded-lg text-sm">
                                <option value="pending" {{ $app->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="shortlisted" {{ $app->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="hired" {{ $app->status === 'hired' ? 'selected' : '' }}>Hired</option>
                            </select>
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-medium hover:opacity-90">Update</button>
                        </form>
                        <span class="status-pill print-only {{ $app->status === 'hired' ? 'status-hired' : ($app->status === 'shortlisted' ? 'status-shortlisted' : ($app->status === 'rejected' ? 'status-rejected' : 'status-pending')) }}">{{ ucfirst($app->status) }}</span>
                        @else
                        <span class="status-pill {{ $app->status === 'hired' ? 'status-hired' : ($app->status === 'shortlisted' ? 'status-shortlisted' : ($app->status === 'rejected' ? 'status-rejected' : 'status-pending')) }}">{{ ucfirst($app->status) }}</span>
                        @endcan
                    </td>
                    <td class="px-5 py-4 text-sm text-neutral-500">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                    <td class="px-5 py-4 text-right no-print">
                        @can('jobApplications.view')
                        <a href="{{ route('admin.job-applications.show', $app) }}" class="text-primary hover:underline text-sm font-medium">View</a>
                        @endcan
                        @can('jobApplications.delete')
                        <form method="POST" action="{{ route('admin.job-applications.destroy', $app) }}" class="inline ml-3" onsubmit="return confirm('Delete this application?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm font-medium">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $canUpdateApplications ? 8 : 7 }}" class="px-5 py-12 text-center text-neutral-500">No applications found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print px-4 py-3 border-t border-neutral-200 bg-neutral-50/50">{{ $applications->links() }}</div>
</div>

@can('jobApplications.update')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-applications');
    const checkboxes = document.querySelectorAll('.application-checkbox');
    if (!selectAll || checkboxes.length === 0) return;

    selectAll.addEventListener('change', function () {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
    });
});
</script>
@endcan
@endsection
