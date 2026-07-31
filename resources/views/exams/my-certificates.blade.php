@extends('layouts.app')

@section('title', 'My Certificates')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6 font-bangla">আমার সার্টিফিকেট</h1>
    <div class="space-y-4">
        @forelse($certificates as $certificate)
        <a href="{{ route('certificates.show', $certificate) }}" class="block rounded-xl border bg-white p-5 shadow-sm hover:shadow-md">
            <div class="font-semibold">{{ $certificate->exam_title }}</div>
            <div class="text-sm text-gray-600">Score: {{ number_format($certificate->score, 2) }}% · {{ $certificate->issued_at->format('d M Y') }}</div>
        </a>
        @empty
        <p class="text-gray-500 font-bangla">এখনও কোনো সার্টিফিকেট নেই।</p>
        @endforelse
    </div>
</div>
@endsection
