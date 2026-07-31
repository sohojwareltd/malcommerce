@extends('layouts.app')

@section('title', 'Verify Certificate')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold mb-6">Verify certificate</h1>
    <form method="GET" class="mb-8 flex gap-2">
        <input type="text" name="code" value="{{ $code }}" placeholder="Verification code" class="flex-1 rounded-lg border px-3 py-2 font-mono uppercase">
        <button class="rounded-lg bg-primary text-white px-4 py-2">Verify</button>
    </form>

    @if($code !== '' && !$certificate)
    <div class="rounded-lg bg-red-50 text-red-800 px-4 py-3">Certificate not found.</div>
    @elseif($certificate)
    <div class="rounded-xl border bg-white p-6 shadow-sm space-y-2">
        <p class="text-green-700 font-semibold">Valid certificate</p>
        <p><strong>Student:</strong> {{ $certificate->student_name }}</p>
        <p><strong>Exam:</strong> {{ $certificate->exam_title }}</p>
        <p><strong>Score:</strong> {{ number_format($certificate->score, 2) }}%</p>
        <p><strong>Issued:</strong> {{ $certificate->issued_at->format('Y-m-d') }}</p>
    </div>
    @endif
</div>
@endsection
