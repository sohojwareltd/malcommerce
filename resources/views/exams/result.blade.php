@extends('layouts.app')

@section('title', 'Exam Result')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="rounded-xl border bg-white p-8 shadow-lg text-center">
        @if($attempt->passed)
        <div class="text-green-600 text-5xl mb-4">✓</div>
        <h1 class="text-2xl font-bold text-green-700 mb-2 font-bangla">অভিনন্দন! আপনি পাস করেছেন</h1>
        @else
        <div class="text-red-500 text-5xl mb-4">✗</div>
        <h1 class="text-2xl font-bold text-red-700 mb-2 font-bangla">পাস করতে পারেননি</h1>
        @endif

        <p class="text-gray-700 mb-6 font-bangla">স্কোর: <strong>{{ number_format($attempt->score, 2) }}%</strong> (পাস মার্ক {{ $attempt->exam->pass_mark }}%)</p>
        <p class="text-sm text-gray-500 mb-6">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }} সঠিক</p>

        @if($attempt->certificate)
        <a href="{{ route('certificates.show', $attempt->certificate) }}" class="inline-block rounded-lg bg-primary text-white px-6 py-3 font-semibold font-bangla mb-3">সার্টিফিকেট দেখুন</a>
        @endif

        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('exams.show', $attempt->exam) }}" class="text-primary font-bangla">← পরীক্ষায় ফিরুন</a>
            <a href="{{ route('exams.index') }}" class="text-gray-600 font-bangla">সব পরীক্ষা</a>
        </div>
    </div>
</div>
@endsection
