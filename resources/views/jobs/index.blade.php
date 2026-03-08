@extends('layouts.app')

@section('title', 'চাকরির বিজ্ঞপ্তি')
@section('description', 'বর্তমানে খালি পদসমূহ দেখুন এবং আবেদন করুন')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">চাকরির বিজ্ঞপ্তি</h1>
            <p class="text-gray-600 font-bangla">বর্তমানে খালি পদসমূহ দেখুন এবং আবেদন করুন</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jobs as $job)
            <div class="card overflow-hidden hover:shadow-lg transition">
                @if($job->thumbnail)
                <div class="aspect-video bg-gray-100">
                    <img src="{{ $job->thumbnail }}" alt="{{ $job->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 font-bangla mb-2">
                        <a href="{{ route('jobs.show', $job) }}" class="hover:text-primary">{{ $job->title }}</a>
                    </h2>
                    @if($job->deadline)
                    <p class="text-sm text-gray-500 mb-3">আবেদনের শেষ তারিখ: {{ $job->deadline->format('d M, Y') }}</p>
                    @endif
                    @if($job->description)
                    <p class="text-gray-600 text-sm line-clamp-3 font-bangla">{{ Str::limit(strip_tags($job->description), 120) }}</p>
                    @endif
                    <a href="{{ route('jobs.show', $job) }}" class="inline-block mt-4 btn-primary font-bangla px-4 py-2 rounded-lg text-sm">
                        বিস্তারিত ও আবেদন
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-500 font-bangla">
                বর্তমানে কোন চাকরির বিজ্ঞপ্তি নেই।
            </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $jobs->links() }}</div>
    </div>
</div>
@endsection
