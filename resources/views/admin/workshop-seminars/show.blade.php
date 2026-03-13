@extends('layouts.admin')

@section('title', $workshopSeminar->title)

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $workshopSeminar->title }}</h1>
        <p class="text-neutral-600 mt-1 text-sm">
            {{ $workshopSeminar->event_date?->format('M d, Y') ?? 'Date TBD' }}
            @if($workshopSeminar->venue_display) · {{ $workshopSeminar->venue_display }}@endif
            @if($workshopSeminar->trades->isNotEmpty()) · {{ $workshopSeminar->trades->pluck('name')->join(', ') }}@endif
            · {{ $workshopSeminar->enrollments->count() }} enrollment(s)
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('workshops.show', $workshopSeminar) }}" target="_blank" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">View Public</a>
        @can('workshopSeminars.update')
        <a href="{{ route('admin.workshop-seminars.edit', $workshopSeminar) }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm">Edit</a>
        @endcan
        <a href="{{ route('admin.workshop-seminars.index') }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm">← Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        @if($workshopSeminar->thumbnail)
        <img src="{{ $workshopSeminar->thumbnail }}" alt="{{ $workshopSeminar->title }}" class="w-full max-w-md aspect-video object-cover rounded-lg mb-6">
        @endif
        @if($workshopSeminar->description)
        <h2 class="text-lg font-semibold mb-2">Description</h2>
        <div class="prose prose-sm max-w-none text-neutral-700 whitespace-pre-wrap">{{ $workshopSeminar->description }}</div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Enrollments</h2>
        <a href="{{ route('admin.workshop-enrollments.index', ['workshop_seminar_id' => $workshopSeminar->id]) }}" class="block text-primary hover:underline font-medium mb-4">View all enrollments →</a>

        <ul class="space-y-3">
            @forelse($workshopSeminar->enrollments->take(10) as $en)
            <li class="border-b border-neutral-100 pb-3 last:border-0">
                <a href="{{ route('admin.workshop-enrollments.show', $en) }}" class="font-medium text-neutral-900 hover:text-primary">{{ $en->name }}</a>
                <p class="text-xs text-neutral-500">{{ $en->phone }} · {{ $en->created_at->format('M d') }}</p>
            </li>
            @empty
            <li class="text-neutral-500 text-sm">No enrollments yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
