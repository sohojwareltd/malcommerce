@extends('layouts.app')

@section('title', $lesson->title . ' — ' . $course->title)

@section('content')
<div class="bg-white py-8 md:py-12 min-h-screen" x-data="{ buyOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('courses.show', $course) }}" class="text-gray-500 hover:text-primary text-sm font-bangla">← {{ $course->title }}</a>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2 mb-6">{{ $lesson->title }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="aspect-video bg-black rounded-xl overflow-hidden mb-3">
                    <iframe src="{{ $lesson->embed_url }}" class="w-full h-full" allowfullscreen title="{{ $lesson->title }}"></iframe>
                </div>
            </div>
            <div class="lg:col-span-1">
                @include('courses.partials.lesson-sidebar', ['course' => $course, 'enrolled' => $enrolled, 'activeLessonId' => $lesson->id])
            </div>
        </div>
    </div>

    @if(!$enrolled)
        @include('courses.partials.buy-course-modal', ['course' => $course])
    @endif
</div>
@endsection
