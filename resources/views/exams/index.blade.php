@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-2 font-bangla">অনলাইন পরীক্ষা</h1>
    <p class="text-gray-600 mb-8 font-bangla">MCQ পরীক্ষায় অংশ নিন এবং সফল হলে সার্টিফিকেট পান।</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
        <a href="{{ route('exams.show', $exam) }}" class="block rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
            <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $exam->title }}</h2>
            <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $exam->description }}</p>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="rounded-full bg-gray-100 px-2 py-1">{{ $exam->questions_count }} questions</span>
                <span class="rounded-full bg-gray-100 px-2 py-1">{{ \App\Models\Exam::accessTypeLabels()[$exam->access_type] ?? $exam->access_type }}</span>
                @if($exam->requiresPayment())
                <span class="rounded-full bg-primary/10 text-primary px-2 py-1">৳{{ number_format($exam->price, 0) }}</span>
                @else
                <span class="rounded-full bg-green-100 text-green-700 px-2 py-1">Free</span>
                @endif
            </div>
        </a>
        @empty
        <p class="text-gray-500 font-bangla">কোনো পরীক্ষা নেই।</p>
        @endforelse
    </div>

    <div class="mt-8">{{ $exams->links() }}</div>
</div>
@endsection
