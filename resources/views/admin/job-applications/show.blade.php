@extends('layouts.admin')

@section('title', 'Application - ' . $jobApplication->name)

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
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">{{ $jobApplication->name }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">Application for {{ $jobApplication->jobCircular->title }}</p>
    </div>
    <div class="flex flex-wrap gap-2 items-center">
        @can('jobApplications.update')
        <form action="{{ route('admin.job-applications.update-status', $jobApplication) }}" method="POST" class="inline" id="status-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="status-input" value="{{ $jobApplication->status }}">
            <select name="status_select" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm font-medium bg-white shadow-sm">
                <option value="pending" {{ $jobApplication->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="shortlisted" {{ $jobApplication->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                <option value="rejected" {{ $jobApplication->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="hired" {{ $jobApplication->status === 'hired' ? 'selected' : '' }}>Hired</option>
            </select>
        </form>
        @endcan
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-neutral-100 text-neutral-700 hover:bg-neutral-200 px-4 py-2 rounded-lg transition font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-1v8"></path></svg>
            Print
        </button>
        <a href="{{ route('admin.job-applications.index', ['job_circular_id' => $jobApplication->job_circular_id]) }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">← Back</a>
    </div>
</div>

@can('jobApplications.update')
<script>
document.getElementById('status-form')?.querySelector('select[name="status_select"]')?.addEventListener('change', function() {
    this.form.querySelector('#status-input').value = this.value;
    this.form.submit();
});
</script>
@endcan

<div class="print-report">
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden max-w-4xl">
        <div class="bg-gradient-to-r from-primary/10 to-primary/5 px-6 py-4 border-b border-neutral-200">
            <h2 class="text-lg font-bold text-neutral-900">Job Application</h2>
            <p class="text-sm text-neutral-600 mt-0.5">{{ $jobApplication->jobCircular->title }}</p>
            <p class="text-xs text-neutral-500 mt-1">Application #{{ $jobApplication->id }} · Applied {{ $jobApplication->created_at->format('M d, Y h:i A') }}</p>
            <p class="text-xs text-neutral-500 print-only mt-1">Printed on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Personal information</h3>
                    <dl class="space-y-2 text-sm">
                        <div><dt class="text-neutral-500">Name</dt><dd class="font-semibold text-neutral-900 mt-0.5">{{ $jobApplication->name }}</dd></div>
                        <div><dt class="text-neutral-500">Email</dt><dd class="mt-0.5">{{ $jobApplication->email }}</dd></div>
                        <div><dt class="text-neutral-500">Phone</dt><dd class="mt-0.5">{{ $jobApplication->phone }}</dd></div>
                        @if($jobApplication->address)<div><dt class="text-neutral-500">Address</dt><dd class="mt-0.5">{{ $jobApplication->address }}</dd></div>@endif
                        @if($jobApplication->date_of_birth)<div><dt class="text-neutral-500">Date of birth</dt><dd class="mt-0.5">{{ $jobApplication->date_of_birth }}</dd></div>@endif
                        @if($jobApplication->gender)<div><dt class="text-neutral-500">Gender</dt><dd class="mt-0.5">{{ $jobApplication->gender }}</dd></div>@endif
                        <div><dt class="text-neutral-500">Status</dt><dd class="mt-0.5"><span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $jobApplication->status === 'hired' ? 'bg-green-100 text-green-800' : ($jobApplication->status === 'shortlisted' ? 'bg-blue-100 text-blue-800' : ($jobApplication->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">{{ ucfirst($jobApplication->status) }}</span></dd></div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Job</h3>
                    <p class="font-medium text-neutral-900"><a href="{{ route('admin.job-circulars.show', $jobApplication->jobCircular) }}" class="text-primary hover:underline">{{ $jobApplication->jobCircular->title }}</a></p>
                </div>
            </div>

            @if($jobApplication->education && count($jobApplication->education) > 0)
            <div>
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Education</h3>
                <div class="space-y-3">
                    @foreach($jobApplication->education as $edu)
                    <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50/50">
                        @if(is_array($edu))
                            @if(isset($edu['details']))
                                <p class="text-neutral-700 text-sm">{{ $edu['details'] }}</p>
                            @else
                                <p class="font-medium text-neutral-900">{{ $edu['degree'] ?? $edu['institution'] ?? '—' }}</p>
                                <p class="text-sm text-neutral-600">{{ $edu['institution'] ?? '' }}{{ isset($edu['year']) ? ' · ' . $edu['year'] : '' }}</p>
                                @if(!empty($edu['result']))<p class="text-sm text-neutral-500 mt-0.5">{{ $edu['result'] }}</p>@endif
                            @endif
                        @else
                            <p class="text-sm">{{ $edu }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($jobApplication->experience && count($jobApplication->experience) > 0)
            <div>
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-3">Experience</h3>
                <div class="space-y-3">
                    @foreach($jobApplication->experience as $exp)
                    <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50/50">
                        @if(is_array($exp))
                            @if(isset($exp['details']))
                                <p class="text-neutral-700 text-sm">{{ $exp['details'] }}</p>
                            @else
                                <p class="font-medium text-neutral-900">{{ $exp['company'] ?? $exp['role'] ?? '—' }}</p>
                                <p class="text-sm text-neutral-600">{{ $exp['role'] ?? '' }}{{ isset($exp['duration']) ? ' · ' . $exp['duration'] : '' }}</p>
                                @if(!empty($exp['description']))<p class="text-sm text-neutral-700 mt-2">{{ $exp['description'] }}</p>@endif
                            @endif
                        @else
                            <p class="text-sm">{{ $exp }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($jobApplication->resume_path)
            <div class="no-print">
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Resume / CV</h3>
                <a href="{{ asset('storage/' . $jobApplication->resume_path) }}" target="_blank" class="text-primary hover:underline font-medium text-sm">Download resume</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
