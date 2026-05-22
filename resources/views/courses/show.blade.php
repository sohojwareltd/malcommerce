@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="bg-white py-8 md:py-12" x-data="{ buyOpen: {{ ($errors->any() && !$enrolled) ? 'true' : 'false' }} }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if($course->preview_embed_url)
                <div class="aspect-video rounded-xl overflow-hidden bg-black mb-6">
                    <iframe src="{{ $course->preview_embed_url }}" class="w-full h-full" allowfullscreen title="Preview"></iframe>
                </div>
                @elseif($course->thumbnail_url)
                <img src="{{ $course->thumbnail_url }}" alt="" class="w-full rounded-xl mb-6 aspect-video object-cover">
                @endif

                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">{{ $course->title }}</h1>
                @if($course->category)<p class="text-sm text-gray-500 mb-4">{{ $course->category->name }}</p>@endif
                @if($course->short_description)<p class="text-gray-700 mb-4">{{ $course->short_description }}</p>@endif
                @if($course->description)<div class="prose prose-sm max-w-none text-gray-700">{!! nl2br(e($course->description)) !!}</div>@endif

                <div class="mt-8">
                    <h2 class="text-lg font-bold mb-3 font-bangla">কোর্স কন্টেন্ট ({{ $course->activeLessons->count() }} লেসন)</h2>
                    <ul class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                        @foreach($course->activeLessons as $i => $lesson)
                        <li class="text-sm">
                            @if($lesson->isWatchable($enrolled))
                                <a href="{{ $enrolled ? route('my-courses.show', ['course' => $course->slug, 'lesson' => $i]) : route('courses.lessons.watch', ['course' => $course->slug, 'lesson' => $lesson->id]) }}"
                                   class="flex items-center gap-3 px-4 py-3 text-gray-900 hover:bg-gray-50 font-medium">
                                    @include('courses.partials.lesson-icons', ['unlocked' => true])
                                    <span>{{ $lesson->title }}</span>
                                </a>
                            @else
                                <button type="button" @click="buyOpen = true"
                                        class="flex items-center gap-3 w-full text-left px-4 py-3 text-gray-600 hover:bg-gray-50">
                                    @include('courses.partials.lesson-icons', ['unlocked' => false])
                                    <span>{{ $lesson->title }}</span>
                                </button>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-1" id="buy">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 sticky top-4">
                    @if($enrolled)
                        <p class="text-green-700 font-semibold font-bangla mb-4">আপনার কাছে এই কোর্স আছে</p>
                        <a href="{{ route('my-courses.show', $course) }}" class="block w-full text-center bg-primary text-white py-3 rounded-lg font-semibold font-bangla hover:bg-primary-light transition">
                            কোর্স দেখুন
                        </a>
                    @elseif(auth()->check() && auth()->user()->digitalCourseEnrollments()->where('digital_course_id', $course->id)->exists())
                        <a href="{{ route('my-courses.show', $course) }}" class="block w-full text-center bg-primary text-white py-3 rounded-lg font-semibold font-bangla">কোর্স দেখুন</a>
                    @else
                        <div class="mb-4">
                            @if($course->compare_at_price && $course->compare_at_price > $course->price)
                                <span class="text-gray-400 line-through text-sm">৳{{ number_format($course->compare_at_price, 0) }}</span>
                            @endif
                            <span class="text-2xl font-bold text-gray-900">৳{{ number_format($course->price, 0) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 font-bangla">bKash দিয়ে পেমেন্ট · ফোন নম্বর দিয়ে অ্যাকাউন্ট তৈরি হবে</p>

                        <form method="POST" action="{{ route('courses.checkout', $course) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-bangla">নাম</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                                @error('customer_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-bangla">মোবাইল</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required placeholder="01XXXXXXXXX" class="w-full rounded-lg border-gray-300 text-sm">
                                @error('customer_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#E2136E] hover:bg-[#c9105f] text-white py-3 rounded-lg font-semibold font-bangla transition">
                                <img src="{{ route('assets.bkash.logo') }}" alt="" class="h-6 w-auto" onerror="this.style.display='none'">
                                bKash দিয়ে কিনুন
                            </button>
                        </form>
                        @guest
                        <p class="text-xs text-gray-500 mt-3 text-center font-bangla">কেনার পর <a href="{{ route('login') }}" class="text-primary underline">লগইন</a> করে ভিডিও দেখুন</p>
                        @endguest
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$enrolled)
        @include('courses.partials.buy-course-modal', ['course' => $course])
    @endif
</div>
@endsection
