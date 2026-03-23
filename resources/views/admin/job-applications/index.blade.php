@extends('layouts.admin')

@section('title', 'Job Applications')

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
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
    <div class="print-report p-6">
        <h2 class="text-xl font-bold text-neutral-900">Job Applications Report</h2>
        <p class="text-sm text-neutral-600 print-only mt-1">Printed on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
    <div class="md:hidden divide-y divide-neutral-200 no-print">
        @forelse($applications as $app)
        <div class="p-4 hover:bg-neutral-50/50">
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
                <span class="shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full {{ $app->status === 'hired' ? 'bg-green-100 text-green-800' : ($app->status === 'shortlisted' ? 'bg-blue-100 text-blue-800' : ($app->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">{{ ucfirst($app->status) }}</span>
            </div>
            <p class="text-xs text-neutral-400 mt-2">{{ $app->created_at->format('M d, Y h:i A') }}</p>
            <div class="mt-3 flex gap-3 items-center">
                @can('jobApplications.view')
                <a href="{{ route('admin.job-applications.show', $app) }}" class="text-primary font-medium text-sm">View</a>
                @endcan
                <button type="button"
                        class="text-indigo-600 font-medium text-sm quick-view-btn"
                        data-name="{{ $app->name }}"
                        data-job="{{ $app->jobCircular->title }}"
                        data-email="{{ $app->email }}"
                        data-phone="{{ $app->phone }}"
                        data-address="{{ $app->address ?? '—' }}"
                        data-status="{{ ucfirst($app->status) }}"
                        data-applied="{{ $app->created_at->format('M d, Y h:i A') }}">
                    Popup
                </button>
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
                <tr class="hover:bg-neutral-50/50">
                    <td class="px-5 py-4 font-medium text-neutral-900">{{ $app->name }}</td>
                    <td class="px-5 py-4 text-sm text-neutral-600">{{ $app->jobCircular->title }}</td>
                    <td class="px-5 py-4 text-sm"><span class="block truncate max-w-[200px]">{{ $app->email }}</span><span class="text-neutral-500 text-xs">{{ $app->phone }}</span></td>
                    <td class="px-5 py-4 text-sm text-neutral-600 max-w-[220px] truncate">{{ $app->address ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $app->status === 'hired' ? 'bg-green-100 text-green-800' : ($app->status === 'shortlisted' ? 'bg-blue-100 text-blue-800' : ($app->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">{{ ucfirst($app->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-neutral-500">{{ $app->created_at->format('M d, Y h:i A') }}</td>
                    <td class="px-5 py-4 text-right no-print">
                        @can('jobApplications.view')
                        <a href="{{ route('admin.job-applications.show', $app) }}" class="text-primary hover:underline text-sm font-medium">View</a>
                        @endcan
                        <button type="button"
                                class="ml-3 text-indigo-600 hover:underline text-sm font-medium quick-view-btn"
                                data-name="{{ $app->name }}"
                                data-job="{{ $app->jobCircular->title }}"
                                data-email="{{ $app->email }}"
                                data-phone="{{ $app->phone }}"
                                data-address="{{ $app->address ?? '—' }}"
                                data-status="{{ ucfirst($app->status) }}"
                                data-applied="{{ $app->created_at->format('M d, Y h:i A') }}">
                            Popup
                        </button>
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
                    <td colspan="7" class="px-5 py-12 text-center text-neutral-500">No applications found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print px-4 py-3 border-t border-neutral-200 bg-neutral-50/50">{{ $applications->links() }}</div>
</div>

<div id="quick-view-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-xl border border-neutral-200">
        <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-200">
            <h3 class="font-semibold text-neutral-900">Application Quick View</h3>
            <button type="button" id="quick-view-close" class="text-neutral-500 hover:text-neutral-700">✕</button>
        </div>
        <div class="p-4 space-y-2 text-sm">
            <p><span class="text-neutral-500">Name:</span> <span id="qv-name" class="font-medium text-neutral-900"></span></p>
            <p><span class="text-neutral-500">Job:</span> <span id="qv-job"></span></p>
            <p><span class="text-neutral-500">Email:</span> <span id="qv-email"></span></p>
            <p><span class="text-neutral-500">Phone:</span> <span id="qv-phone"></span></p>
            <p><span class="text-neutral-500">Address:</span> <span id="qv-address"></span></p>
            <p><span class="text-neutral-500">Status:</span> <span id="qv-status"></span></p>
            <p><span class="text-neutral-500">Applied:</span> <span id="qv-applied"></span></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('quick-view-modal');
    const closeBtn = document.getElementById('quick-view-close');
    const fields = {
        name: document.getElementById('qv-name'),
        job: document.getElementById('qv-job'),
        email: document.getElementById('qv-email'),
        phone: document.getElementById('qv-phone'),
        address: document.getElementById('qv-address'),
        status: document.getElementById('qv-status'),
        applied: document.getElementById('qv-applied'),
    };

    document.querySelectorAll('.quick-view-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            fields.name.textContent = btn.dataset.name || '';
            fields.job.textContent = btn.dataset.job || '';
            fields.email.textContent = btn.dataset.email || '';
            fields.phone.textContent = btn.dataset.phone || '';
            fields.address.textContent = btn.dataset.address || '';
            fields.status.textContent = btn.dataset.status || '';
            fields.applied.textContent = btn.dataset.applied || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    closeBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
});
</script>
@endpush
@endsection
