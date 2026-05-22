@extends('layouts.app')

@section('title', 'ডিজিটাল কোর্স')
@section('description', 'ভিডিও কোর্স কিনুন এবং দেখুন')

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2 text-center">ডিজিটাল কোর্স</h1>
        <p class="text-center text-gray-600 text-sm font-bangla mb-8">কেনার পর লগইন করে সব লেসন দেখুন</p>

        @if($categories->isNotEmpty())
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <a href="{{ route('courses.index') }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                সব
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $cat->slug ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @forelse($courses as $course)
            <a href="{{ route('courses.show', $course) }}" class="group block">
                <div class="relative aspect-video overflow-hidden rounded-xl bg-gray-200 shadow-sm">
                    @if($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 min-h-[200px]">No image</div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/75 to-transparent p-3 md:p-4">
                        <span class="text-white text-sm md:text-base font-bold">৳{{ number_format($course->price, 0) }}</span>
                        @if($course->lessons_count)
                            <span class="text-white/90 text-sm ml-1">· {{ $course->lessons_count }} লেসন</span>
                        @endif
                    </div>
                </div>
                @if($course->category)
                    <p class="text-sm text-gray-500 mt-3">{{ $course->category->name }}</p>
                @endif
                <h3 class="font-semibold text-gray-900 line-clamp-2 text-base md:text-lg mt-1 group-hover:text-primary transition-colors">{{ $course->title }}</h3>
            </a>
            @empty
            <div class="col-span-full text-center py-16 text-gray-500 font-bangla">কোনো কোর্স পাওয়া যাচ্ছে না।</div>
            @endforelse
        </div>

        @if($courses->hasPages())
        <div class="mt-8 flex justify-center">{{ $courses->links() }}</div>
        @endif
    </div>
</div>
@endsection
