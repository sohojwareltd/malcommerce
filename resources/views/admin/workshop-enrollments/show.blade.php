@extends('layouts.admin')

@section('title', 'Enrollment - ' . $workshopEnrollment->name)

@push('styles')
<style>
.print-only { display: none; }
@media print {
    body * { visibility: hidden; }
    .print-report, .print-report * { visibility: visible; }
    .print-report { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
    .print-only { display: block !important; visibility: visible; }
}
</style>
@endpush

@section('content')
<div class="no-print mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">{{ $workshopEnrollment->name }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">Enrolled in {{ $workshopEnrollment->workshopSeminar->title }}</p>
    </div>
    <div class="flex flex-wrap gap-2 items-center">
        @can('update', $workshopEnrollment)
        <form action="{{ route('admin.workshop-enrollments.update-status', $workshopEnrollment) }}" method="POST" class="inline" id="enrollment-status-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="enrollment-status-input" value="{{ $workshopEnrollment->status ?? 'pending' }}">
            <select name="status_select" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm font-medium bg-white shadow-sm">
                <option value="pending" {{ ($workshopEnrollment->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ ($workshopEnrollment->status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ ($workshopEnrollment->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
        @endcan
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-neutral-100 text-neutral-700 hover:bg-neutral-200 px-4 py-2 rounded-lg transition font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-1v8"></path></svg>
            Print
        </button>
        <a href="{{ route('admin.workshop-enrollments.index', ['workshop_seminar_id' => $workshopEnrollment->workshop_seminar_id]) }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">← Back</a>
    </div>
</div>

@can('update', $workshopEnrollment)
<script>
document.getElementById('enrollment-status-form')?.querySelector('select[name="status_select"]')?.addEventListener('change', function() {
    this.form.querySelector('#enrollment-status-input').value = this.value;
    this.form.submit();
});
</script>
@endcan

<div class="print-report">
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden max-w-3xl">
        <div class="bg-gradient-to-r from-primary/10 to-primary/5 px-6 py-4 border-b border-neutral-200">
            <h2 class="text-lg font-bold text-neutral-900">Workshop Enrollment</h2>
            <p class="text-sm text-neutral-600 mt-0.5">{{ $workshopEnrollment->workshopSeminar->title }}</p>
            <p class="text-xs text-neutral-500 mt-1">Enrollment #{{ $workshopEnrollment->id }} · {{ $workshopEnrollment->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Applicant</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-neutral-500">Name</dt>
                            <dd class="font-semibold text-neutral-900 mt-0.5">{{ $workshopEnrollment->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-500">Phone</dt>
                            <dd class="mt-0.5">{{ $workshopEnrollment->phone }}</dd>
                        </div>
                        @if($workshopEnrollment->address)
                        <div>
                            <dt class="text-xs text-neutral-500">Address</dt>
                            <dd class="mt-0.5 text-neutral-700">{{ $workshopEnrollment->address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Details</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-neutral-500">Status</dt>
                            <dd class="mt-0.5">
                                @php($status = $workshopEnrollment->status ?? 'pending')
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ $status === 'confirmed' ? 'bg-green-100 text-green-800' : ($status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-500">Workshop</dt>
                            <dd class="mt-0.5"><a href="{{ route('admin.workshop-seminars.show', $workshopEnrollment->workshopSeminar) }}" class="text-primary hover:underline font-medium">{{ $workshopEnrollment->workshopSeminar->title }}</a></dd>
                        </div>
                        @if($workshopEnrollment->venue_id && $workshopEnrollment->venue)
                        <div>
                            <dt class="text-xs text-neutral-500">Venue</dt>
                            <dd class="mt-0.5">{{ $workshopEnrollment->venue->name }}</dd>
                        </div>
                        @endif
                        @if($workshopEnrollment->trade_id && $workshopEnrollment->trade)
                        <div>
                            <dt class="text-xs text-neutral-500">Trade</dt>
                            <dd class="mt-0.5">{{ $workshopEnrollment->trade->name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs text-neutral-500">Enrolled on</dt>
                            <dd class="mt-0.5">{{ $workshopEnrollment->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @if($workshopEnrollment->notes)
            <div>
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Notes</h3>
                <p class="text-neutral-700 whitespace-pre-wrap text-sm bg-neutral-50 rounded-lg p-4">{{ $workshopEnrollment->notes }}</p>
            </div>
            @endif
        </div>
    </div>
    <p class="mt-4 text-xs text-neutral-400 print-only">Printed on {{ now()->format('M d, Y h:i A') }}</p>
</div>
@endsection
