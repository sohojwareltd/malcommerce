@extends('layouts.admin')

@section('title', $jobCircular->title)

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $jobCircular->title }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">
            Deadline: {{ $jobCircular->deadline?->format('M d, Y') ?? 'No deadline' }}
            · {{ $jobCircular->applications->count() }} application(s)
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('jobs.show', $jobCircular) }}" target="_blank" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">View Public</a>
        @can('jobCirculars.update')
        <a href="{{ route('admin.job-circulars.edit', $jobCircular) }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm">Edit</a>
        @endcan
        <a href="{{ route('admin.job-circulars.index') }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">← Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        @if($jobCircular->thumbnail)
        <img src="{{ $jobCircular->thumbnail }}" alt="{{ $jobCircular->title }}" class="w-full max-w-md aspect-video object-cover rounded-lg mb-6">
        @endif
        @if($jobCircular->description)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Description</h2>
            <div class="prose prose-sm max-w-none text-neutral-700 rich-text-content">{!! $jobCircular->description !!}</div>
        </div>
        @endif
        @if($jobCircular->requirements)
        <div>
            <h2 class="text-lg font-semibold mb-2">Requirements</h2>
            <div class="prose prose-sm max-w-none text-neutral-700 rich-text-content">{!! $jobCircular->requirements !!}</div>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Applications</h2>
        <a href="{{ route('admin.job-applications.index', ['job_circular_id' => $jobCircular->id]) }}" class="block text-primary hover:underline font-medium mb-4">View all applications →</a>

        <ul class="space-y-3">
            @forelse($jobCircular->applications->take(10) as $app)
            <li class="border-b border-neutral-100 pb-3 last:border-0">
                <a href="{{ route('admin.job-applications.show', $app) }}" class="font-medium text-neutral-900 hover:text-primary">{{ $app->name }}</a>
                <p class="text-xs text-neutral-500">{{ $app->email }} · {{ $app->created_at->format('M d') }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded {{ $app->status === 'pending' ? 'bg-amber-100' : ($app->status === 'shortlisted' ? 'bg-blue-100' : ($app->status === 'hired' ? 'bg-green-100' : 'bg-red-100')) }}">{{ ucfirst($app->status) }}</span>
            </li>
            @empty
            <li class="text-neutral-500 text-sm">No applications yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
