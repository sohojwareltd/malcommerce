@extends('layouts.app')

@section('title', $jobCircular->title)
@section('description', Str::limit(strip_tags($jobCircular->description ?? ''), 160))

@section('content')
<div class="bg-white py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg font-bangla">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg font-bangla">{{ session('error') }}</div>
        @endif

        <div class="mb-8">
            @if($jobCircular->thumbnail)
            <img src="{{ $jobCircular->thumbnail }}" alt="{{ $jobCircular->title }}" class="w-full max-w-2xl aspect-video object-cover rounded-lg mb-6">
            @endif
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">{{ $jobCircular->title }}</h1>
            @if($jobCircular->deadline)
            <p class="text-gray-500 font-bangla">আবেদনের শেষ তারিখ: {{ $jobCircular->deadline->format('d M, Y') }}</p>
            @endif
        </div>

        @if($jobCircular->description)
        <div class="prose prose-lg max-w-none mb-8">
            <h2 class="text-xl font-semibold font-bangla mb-2">বিবরণ</h2>
            <div class="text-gray-700 rich-text-content font-bangla">{!! $jobCircular->description !!}</div>
        </div>
        @endif

        @if($jobCircular->requirements)
        <div class="prose prose-lg max-w-none mb-8">
            <h2 class="text-xl font-semibold font-bangla mb-2">প্রয়োজনীয় যোগ্যতা</h2>
            <div class="text-gray-700 rich-text-content font-bangla">{!! $jobCircular->requirements !!}</div>
        </div>
        @endif

        <div class="card p-6 mt-8">
            <h2 class="text-xl font-bold font-bangla mb-6">আবেদন ফর্ম</h2>

            <form id="job-application-form" action="{{ route('jobs.apply', $jobCircular) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 font-bangla mb-1">নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('name') border-red-500 @enderror">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ইমেইল</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('email') border-red-500 @enderror">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ফোন নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ঠিকানা</label>
                    <textarea name="address" id="address" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 font-bangla mb-1">জন্মতারিখ</label>
                        <input
                            type="text"
                            name="date_of_birth"
                            id="date_of_birth"
                            value="{{ old('date_of_birth') }}"
                            placeholder="উদাহরণ: 15-03-2000 (DD-MM-YYYY)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('date_of_birth') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500 font-bangla">দয়া করে এই ফরম্যাটে লিখুন: DD-MM-YYYY (যেমন: 15-03-2000)</p>
                        @error('date_of_birth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700 font-bangla mb-1">লিঙ্গ</span>
                        <div class="flex items-center gap-4 mt-1">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="gender" value="male" class="h-4 w-4 text-primary border-gray-300"
                                    {{ old('gender') === 'male' ? 'checked' : '' }}>
                                <span class="text-sm font-bangla">পুরুষ</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="gender" value="female" class="h-4 w-4 text-primary border-gray-300"
                                    {{ old('gender') === 'female' ? 'checked' : '' }}>
                                <span class="text-sm font-bangla">মহিলা</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="gender" value="other" class="h-4 w-4 text-primary border-gray-300"
                                    {{ old('gender') === 'other' ? 'checked' : '' }}>
                                <span class="text-sm font-bangla">অন্যান্য</span>
                            </label>
                        </div>
                    </div>
                </div>

                @php
                    $educationOptions = $jobCircular->education_options ?? [];
                    $experienceOptions = $jobCircular->experience_options ?? [];
                @endphp

                <input type="hidden" name="education" id="education_json" value="{{ old('education') }}">
                <div class="space-y-3">
                    <span class="block text-sm font-medium text-gray-700 font-bangla mb-1">শিক্ষাগত যোগ্যতা</span>
                    @if(!empty($educationOptions))
                        <div class="space-y-2">
                            @foreach($educationOptions as $option)
                                <label class="flex items-start gap-2">
                                    <input
                                        type="checkbox"
                                        name="education_selected[]"
                                        value="{{ $option }}"
                                        class="mt-1 h-4 w-4 text-primary border-gray-300 rounded">
                                    <span class="text-sm font-bangla">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <label for="education_custom" class="block text-sm font-medium text-gray-700 font-bangla mt-3">অতিরিক্ত / কাস্টম শিক্ষাগত যোগ্যতা</label>
                    <textarea
                        name="education_custom"
                        id="education_custom"
                        rows="3"
                        placeholder="উদাহরণ: BSc in CSE, Dhaka University, 2020"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('education_custom') }}</textarea>
                    @error('education')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <input type="hidden" name="experience" id="experience_json" value="{{ old('experience') }}">
                <div class="space-y-3">
                    <span class="block text-sm font-medium text-gray-700 font-bangla mb-1">অভিজ্ঞতা</span>
                    @if(!empty($experienceOptions))
                        <div class="space-y-2">
                            @foreach($experienceOptions as $option)
                                <label class="flex items-start gap-2">
                                    <input
                                        type="checkbox"
                                        name="experience_selected[]"
                                        value="{{ $option }}"
                                        class="mt-1 h-4 w-4 text-primary border-gray-300 rounded">
                                    <span class="text-sm font-bangla">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <label for="experience_custom" class="block text-sm font-medium text-gray-700 font-bangla mt-3">অতিরিক্ত / কাস্টম অভিজ্ঞতা</label>
                    <textarea
                        name="experience_custom"
                        id="experience_custom"
                        rows="3"
                        placeholder="উদাহরণ: কেয়ার গিভার হিসেবে ২ বছরের অভিজ্ঞতা"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('experience_custom') }}</textarea>
                    @error('experience')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="resume" class="block text-sm font-medium text-gray-700 font-bangla mb-1">সিভি / রিজিউমি (PDF, DOC)</label>
                    <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('resume') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">সর্বোচ্চ 5MB</p>
                    @error('resume')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="btn-primary font-bangla w-full md:w-auto md:min-w-[240px] px-8 py-4 text-base md:text-lg rounded-lg">
                        আবেদন জমা দিন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('job-application-form');
    if (!form) return;

    form.addEventListener('submit', function () {
        function buildItems(selectedName, customId) {
            const items = [];
            const selected = Array.from(form.querySelectorAll('input[name="' + selectedName + '"]:checked'))
                .map(function (el) { return el.value.trim(); })
                .filter(function (v) { return v.length > 0; });

            selected.forEach(function (value) {
                items.push({ details: value, predefined: true });
            });

            const customEl = document.getElementById(customId);
            if (customEl) {
                const customValue = customEl.value.trim();
                if (customValue.length > 0) {
                    items.push({ details: customValue, predefined: false });
                }
            }

            return items;
        }

        const educationItems = buildItems('education_selected[]', 'education_custom');
        const experienceItems = buildItems('experience_selected[]', 'experience_custom');

        const educationJsonInput = document.getElementById('education_json');
        const experienceJsonInput = document.getElementById('experience_json');

        if (educationJsonInput) {
            educationJsonInput.value = educationItems.length ? JSON.stringify(educationItems) : '';
        }
        if (experienceJsonInput) {
            experienceJsonInput.value = experienceItems.length ? JSON.stringify(experienceItems) : '';
        }
    });
});
</script>
@endpush
