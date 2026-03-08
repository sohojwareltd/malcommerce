@extends('layouts.admin')

@section('title', 'Application - ' . $jobApplication->name)

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $jobApplication->name }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">Application for {{ $jobApplication->jobCircular->title }}</p>
    </div>
    <div class="flex gap-2">
        @can('jobApplications.update')
        <form action="{{ route('admin.job-applications.update-status', $jobApplication) }}" method="POST" class="inline" id="status-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="status-input" value="{{ $jobApplication->status }}">
            <select name="status_select" class="px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="pending" {{ $jobApplication->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="shortlisted" {{ $jobApplication->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                <option value="rejected" {{ $jobApplication->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="hired" {{ $jobApplication->status === 'hired' ? 'selected' : '' }}>Hired</option>
            </select>
        </form>
        @endcan
        <a href="{{ route('admin.job-applications.index', ['job_circular_id' => $jobApplication->job_circular_id]) }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">← Back</a>
    </div>
</div>

<script>
document.getElementById('status-form')?.querySelector('select[name="status_select"]')?.addEventListener('change', function() {
    this.form.querySelector('#status-input').value = this.value;
    this.form.submit();
});
</script>

<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-neutral-500">Name</dt><dd class="font-medium">{{ $jobApplication->name }}</dd></div>
                <div><dt class="text-neutral-500">Email</dt><dd>{{ $jobApplication->email }}</dd></div>
                <div><dt class="text-neutral-500">Phone</dt><dd>{{ $jobApplication->phone }}</dd></div>
                @if($jobApplication->address)<div><dt class="text-neutral-500">Address</dt><dd>{{ $jobApplication->address }}</dd></div>@endif
                @if($jobApplication->date_of_birth)<div><dt class="text-neutral-500">Date of Birth</dt><dd>{{ $jobApplication->date_of_birth->format('M d, Y') }}</dd></div>@endif
                @if($jobApplication->gender)<div><dt class="text-neutral-500">Gender</dt><dd>{{ $jobApplication->gender }}</dd></div>@endif
            </dl>
        </div>

        @if($jobApplication->education && count($jobApplication->education) > 0)
        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Education</h2>
            <div class="space-y-3">
                @foreach($jobApplication->education as $edu)
                <div class="border border-neutral-200 rounded-lg p-4">
                    @if(is_array($edu))
                        @if(isset($edu['details']))
                            <p class="text-neutral-700">{{ $edu['details'] }}</p>
                        @else
                            <p class="font-medium">{{ $edu['degree'] ?? $edu['institution'] ?? '—' }}</p>
                            <p class="text-sm text-neutral-600">{{ $edu['institution'] ?? '' }} {{ isset($edu['year']) ? ' · ' . $edu['year'] : '' }}</p>
                            @if(!empty($edu['result']))<p class="text-sm text-neutral-500">{{ $edu['result'] }}</p>@endif
                        @endif
                    @else
                        <p>{{ $edu }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($jobApplication->experience && count($jobApplication->experience) > 0)
        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Experience</h2>
            <div class="space-y-3">
                @foreach($jobApplication->experience as $exp)
                <div class="border border-neutral-200 rounded-lg p-4">
                    @if(is_array($exp))
                        @if(isset($exp['details']))
                            <p class="text-neutral-700">{{ $exp['details'] }}</p>
                        @else
                            <p class="font-medium">{{ $exp['company'] ?? $exp['role'] ?? '—' }}</p>
                            <p class="text-sm text-neutral-600">{{ $exp['role'] ?? '' }} {{ isset($exp['duration']) ? ' · ' . $exp['duration'] : '' }}</p>
                            @if(!empty($exp['description']))<p class="text-sm text-neutral-700 mt-2">{{ $exp['description'] }}</p>@endif
                        @endif
                    @else
                        <p>{{ $exp }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($jobApplication->cover_letter)
        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Cover Letter</h2>
            <div class="border border-neutral-200 rounded-lg p-4 text-sm whitespace-pre-wrap">{{ $jobApplication->cover_letter }}</div>
        </div>
        @endif

        @if($jobApplication->resume_path)
        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Resume / CV</h2>
            <a href="{{ asset('storage/' . $jobApplication->resume_path) }}" target="_blank" class="text-primary hover:underline font-medium">Download resume</a>
        </div>
        @endif
    </div>
</div>
@endsection
