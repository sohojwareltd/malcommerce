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

            <form action="{{ route('jobs.apply', $jobCircular) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 font-bangla mb-1">লিঙ্গ</label>
                        <select name="gender" id="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">নির্বাচন করুন</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>পুরুষ</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>মহিলা</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>অন্যান্য</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="education" class="block text-sm font-medium text-gray-700 font-bangla mb-1">শিক্ষাগত যোগ্যতা</label>
                    <textarea name="education" id="education" rows="4" placeholder="উদাহরণ: BSc in CSE, Dhaka University, 2020"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('education') border-red-500 @enderror">{{ old('education') }}</textarea>
                    @error('education')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="experience" class="block text-sm font-medium text-gray-700 font-bangla mb-1">অভিজ্ঞতা</label>
                    <textarea name="experience" id="experience" rows="4" placeholder="উদাহরণ: Software Engineer at XYZ Corp (2019-2022)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('experience') border-red-500 @enderror">{{ old('experience') }}</textarea>
                    @error('experience')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="cover_letter" class="block text-sm font-medium text-gray-700 font-bangla mb-1">কভার লেটার</label>
                    <textarea name="cover_letter" id="cover_letter" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('cover_letter') border-red-500 @enderror">{{ old('cover_letter') }}</textarea>
                    @error('cover_letter')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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

