@extends('layouts.app')

@section('title', $workshopSeminar->title)
@section('description', Str::limit(strip_tags($workshopSeminar->description ?? ''), 160))

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
            @if($workshopSeminar->thumbnail)
            <img src="{{ $workshopSeminar->thumbnail }}" alt="{{ $workshopSeminar->title }}" class="w-full max-w-2xl aspect-video object-cover rounded-lg mb-6">
            @endif
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">{{ $workshopSeminar->title }}</h1>
            <div class="flex flex-wrap gap-4 text-gray-500 text-sm font-bangla">
                @if($workshopSeminar->event_date)
                <span>তারিখ: {{ $workshopSeminar->event_date->format('d M, Y') }}</span>
                @endif
                @if($workshopSeminar->event_time)
                <span>সময়: {{ \Carbon\Carbon::parse($workshopSeminar->event_time)->format('g:i A') }}</span>
                @endif
                @if($workshopSeminar->venue)
                <span>স্থান: {{ $workshopSeminar->venue }}</span>
                @endif
            </div>
        </div>

        @if($workshopSeminar->description)
        <div class="prose prose-lg max-w-none mb-8">
            <h2 class="text-xl font-semibold font-bangla mb-2">বিবরণ</h2>
            <div class="text-gray-700 whitespace-pre-wrap font-bangla">{{ $workshopSeminar->description }}</div>
        </div>
        @endif

        <div class="card p-6 mt-8">
            <h2 class="text-xl font-bold font-bangla mb-6">নিবন্ধন ফর্ম</h2>

            <form action="{{ route('workshops.enroll', $workshopSeminar) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 font-bangla mb-1">নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ফোন নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ঠিকানা</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 font-bangla mb-1">মন্তব্য</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="btn-primary font-bangla w-full md:w-auto md:min-w-[240px] px-8 py-4 text-base md:text-lg rounded-lg">
                        নিবন্ধন করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
