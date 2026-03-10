@extends('layouts.app')

@section('title', 'ওয়ার্কশপ ও সেমিনার')
@section('description', 'ওয়ার্কশপ ও সেমিনারে অংশ নিন এবং নিবন্ধন করুন')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">ওয়ার্কশপ ও সেমিনার</h1>
            <p class="text-gray-600 font-bangla">আমাদের আয়োজিত ওয়ার্কশপ ও সেমিনারে নিবন্ধন করুন</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($workshops as $ws)
            <div class="card overflow-hidden hover:shadow-lg transition">
                @if($ws->thumbnail)
                <div class="aspect-video bg-gray-100">
                    <img src="{{ $ws->thumbnail }}" alt="{{ $ws->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                <div class="p-4 sm:p-6">
                    <h2 class="text-xl font-bold text-gray-900 font-bangla mb-2">
                        <a href="{{ route('workshops.show', $ws) }}" class="hover:text-primary">{{ $ws->title }}</a>
                    </h2>
                    @if($ws->event_date)
                    <p class="text-sm text-gray-500 mb-1 font-bangla">তারিখ: {{ $ws->event_date->format('d M, Y') }}</p>
                    @endif
                    @if($ws->venue)
                    <p class="text-sm text-gray-500 mb-3 font-bangla">স্থান: {{ Str::limit($ws->venue, 40) }}</p>
                    @endif
                    @if($ws->description)
                    <p class="text-gray-600 text-sm line-clamp-3 font-bangla">{{ Str::limit(strip_tags($ws->description), 120) }}</p>
                    @endif
                    <a href="{{ route('workshops.show', $ws) }}" class="inline-block mt-4 btn-primary font-bangla px-4 py-2 rounded-lg text-sm">
                        বিস্তারিত ও নিবন্ধন
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-500 font-bangla">
                বর্তমানে কোন ওয়ার্কশপ বা সেমিনার নেই।
            </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $workshops->links() }}</div>
    </div>
</div>
@endsection
