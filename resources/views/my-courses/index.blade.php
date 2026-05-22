@extends('layouts.app')

@section('title', 'আমার কেনা কোর্স')
@section('description', 'আপনার কেনা ডিজিটাল কোর্সগুলো দেখুন')

@section('content')
<div class="bg-white py-10 md:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-bangla">আমার কেনা কোর্স</h1>
                <p class="text-gray-600 mt-2 text-sm font-bangla">আপনি যে কোর্সগুলো কিনেছেন সেগুলো এখানে দেখতে পারবেন</p>
            </div>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 font-bangla">
                নতুন কোর্স দেখুন
            </a>
        </div>

        @if($enrollments->isEmpty())
            <div class="text-center py-16 border border-dashed border-gray-200 rounded-xl">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-gray-600 font-bangla mb-4">আপনি এখনও কোনো কোর্স কিনেননি।</p>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-light font-bangla">
                    কোর্স কিনুন
                </a>
            </div>
        @else
            <p class="text-sm text-gray-500 mb-6 font-bangla">{{ $enrollments->count() }}টি কোর্স</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                @if(!$course) @continue @endif
                <article class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow bg-white flex flex-col">
                    <a href="{{ route('my-courses.show', $course) }}" class="block group">
                        <div class="aspect-video bg-gray-100 relative overflow-hidden">
                            @if($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 min-h-[220px]">No image</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-5 md:p-6 flex flex-col flex-1">
                        @if($course->category)
                            <p class="text-xs text-gray-500 mb-1">{{ $course->category->name }}</p>
                        @endif
                        <h2 class="font-bold text-gray-900 text-lg md:text-xl line-clamp-2 mb-2">
                            <a href="{{ route('my-courses.show', $course) }}" class="hover:text-primary">{{ $course->title }}</a>
                        </h2>
                        <p class="text-sm text-gray-500 mt-auto space-y-1">
                            <span class="block">{{ $course->lessons_count ?? 0 }} লেসন</span>
                            <span class="block">কেনা: {{ $enrollment->granted_at->format('M d, Y') }}</span>
                            @if($enrollment->order)
                                <span class="block font-mono">#{{ $enrollment->order->order_number }}</span>
                            @endif
                        </p>
                        <a href="{{ route('my-courses.show', $course) }}"
                           class="mt-4 inline-flex items-center justify-center w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-light font-bangla">
                            কোর্স চালু করুন
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
