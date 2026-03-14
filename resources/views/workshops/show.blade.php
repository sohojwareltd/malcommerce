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
            <img src="{{ $workshopSeminar->thumbnail }}" alt="{{ $workshopSeminar->title }}" class="w-full max-w-2xl h-auto rounded-lg mb-6">
            @endif
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">{{ $workshopSeminar->title }}</h1>
            <div class="flex flex-wrap gap-4 text-gray-500 text-sm font-bangla">
                @if($workshopSeminar->event_date)
                <span>তারিখ: {{ $workshopSeminar->event_date->format('d M, Y') }}</span>
                @endif
                @if($workshopSeminar->event_time)
                <span>সময়: {{ \Carbon\Carbon::parse($workshopSeminar->event_time)->format('g:i A') }}</span>
                @endif
                @if($workshopSeminar->venue_display)
                <span>স্থান: {{ $workshopSeminar->venue_display }}</span>
                @endif
                @if($workshopSeminar->trades->isNotEmpty())
                <span>বৃত্তি: {{ $workshopSeminar->trades->pluck('name')->join(', ') }}</span>
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

                @if($workshopSeminar->venues->isNotEmpty())
                <div>
                    <label for="venue_id" class="block text-sm font-medium text-gray-700 font-bangla mb-1">স্থান নির্বাচন করুন <span class="text-red-500">*</span></label>
                    <select name="venue_id" id="venue_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('venue_id') border-red-500 @enderror">
                        <option value="">— স্থান নির্বাচন করুন —</option>
                        @foreach($workshopSeminar->venues as $v)
                        <option value="{{ $v->id }}" {{ old('venue_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @error('venue_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($workshopSeminar->trades->isNotEmpty())
                <div>
                    <label for="trade_id" class="block text-sm font-medium text-gray-700 font-bangla mb-1">বৃত্তি নির্বাচন করুন <span class="text-red-500">*</span></label>
                    <select name="trade_id" id="trade_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('trade_id') border-red-500 @enderror">
                        <option value="">— বৃত্তি নির্বাচন করুন —</option>
                        @foreach($workshopSeminar->trades as $t)
                        <option value="{{ $t->id }}" {{ old('trade_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('trade_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 font-bangla mb-1">নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($workshopSeminar->show_phone ?? true)
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ফোন নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" @if($workshopSeminar->show_phone ?? true) required @endif
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($workshopSeminar->show_address ?? true)
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 font-bangla mb-1">ঠিকানা</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($workshopSeminar->show_notes ?? true)
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 font-bangla mb-1">মন্তব্য</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

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
