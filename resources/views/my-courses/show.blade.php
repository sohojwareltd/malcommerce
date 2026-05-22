@extends('layouts.app')

@section('title', $course->title . ' — আমার কোর্স')

@section('content')
<div class="bg-white py-8 md:py-12 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('my-courses.index') }}" class="text-gray-500 hover:text-primary text-sm font-bangla">← আমার কোর্স</a>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mt-2 mb-6">{{ $course->title }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                @if($activeLesson)
                <div class="aspect-video bg-black rounded-xl overflow-hidden mb-3">
                    <iframe src="{{ $activeLesson->embed_url }}" class="w-full h-full" allowfullscreen title="{{ $activeLesson->title }}"></iframe>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $activeLesson->title }}</h2>
                @else
                <p class="text-gray-600 font-bangla">এই কোর্সে এখনও কোনো লেসন নেই।</p>
                @endif
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <p class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-gray-100 font-bangla">লেসন তালিকা</p>
                    <ul class="max-h-[60vh] overflow-y-auto divide-y divide-gray-100">
                        @foreach($lessons as $i => $lesson)
                        <li>
                            <a href="{{ route('my-courses.show', ['course' => $course->slug, 'lesson' => $i]) }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm {{ ($activeLesson && $activeLesson->id === $lesson->id) ? 'bg-primary/5 text-primary font-medium' : 'text-gray-800 hover:bg-gray-50' }}">
                                @include('courses.partials.lesson-icons', ['unlocked' => true])
                                <span class="min-w-0 flex-1"><span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $lesson->title }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
