@extends('layouts.admin')

@section('title', 'Enrollment - ' . $workshopEnrollment->name)

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $workshopEnrollment->name }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">Enrolled in {{ $workshopEnrollment->workshopSeminar->title }}</p>
    </div>
    <div class="flex gap-2">
        @can('update', $workshopEnrollment)
        <form action="{{ route('admin.workshop-enrollments.update-status', $workshopEnrollment) }}" method="POST" class="inline" id="enrollment-status-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="enrollment-status-input" value="{{ $workshopEnrollment->status ?? 'pending' }}">
            <select name="status_select" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="pending" {{ ($workshopEnrollment->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ ($workshopEnrollment->status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ ($workshopEnrollment->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
        @endcan
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

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <dl class="space-y-4">
        <div>
            <dt class="text-sm font-medium text-neutral-500">Name</dt>
            <dd class="mt-1 font-medium">{{ $workshopEnrollment->name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-neutral-500">Phone</dt>
            <dd class="mt-1">{{ $workshopEnrollment->phone }}</dd>
        </div>
        @if($workshopEnrollment->address)
        <div>
            <dt class="text-sm font-medium text-neutral-500">Address</dt>
            <dd class="mt-1">{{ $workshopEnrollment->address }}</dd>
        </div>
        @endif
        @if($workshopEnrollment->notes)
        <div>
            <dt class="text-sm font-medium text-neutral-500">Notes</dt>
            <dd class="mt-1 whitespace-pre-wrap">{{ $workshopEnrollment->notes }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-sm font-medium text-neutral-500">Status</dt>
            <dd class="mt-1">
                @php($status = $workshopEnrollment->status ?? 'pending')
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                    {{ $status === 'confirmed' ? 'bg-green-100 text-green-800' : ($status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($status) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-neutral-500">Workshop</dt>
            <dd class="mt-1"><a href="{{ route('admin.workshop-seminars.show', $workshopEnrollment->workshopSeminar) }}" class="text-primary hover:underline">{{ $workshopEnrollment->workshopSeminar->title }}</a></dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-neutral-500">Enrolled on</dt>
            <dd class="mt-1">{{ $workshopEnrollment->created_at->format('M d, Y h:i A') }}</dd>
        </div>
    </dl>
</div>
@endsection
