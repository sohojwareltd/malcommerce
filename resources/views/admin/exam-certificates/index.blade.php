@extends('layouts.admin')

@section('title', 'Exam Certificates')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Exam certificates</h1>
<form method="GET" class="mb-4"><select name="exam_id" class="rounded-lg border px-3 py-2 text-sm"><option value="">All exams</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->title }}</option>@endforeach</select><button class="ml-2 rounded-lg border px-3 py-2 text-sm">Filter</button></form>
<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm"><thead class="bg-neutral-50"><tr><th class="px-4 py-3 text-left">Student</th><th class="px-4 py-3 text-left">Exam</th><th class="px-4 py-3 text-left">Score</th><th class="px-4 py-3 text-left">Code</th><th class="px-4 py-3 text-left">Issued</th></tr></thead>
    <tbody class="divide-y">@foreach($certificates as $certificate)<tr><td class="px-4 py-3">{{ $certificate->student_name }}</td><td class="px-4 py-3">{{ $certificate->exam_title }}</td><td class="px-4 py-3">{{ number_format($certificate->score, 2) }}%</td><td class="px-4 py-3 font-mono text-xs">{{ $certificate->verification_code }}</td><td class="px-4 py-3">{{ $certificate->issued_at->format('Y-m-d') }}</td></tr>@endforeach</tbody></table>
</div>
<div class="mt-4">{{ $certificates->links() }}</div>
@endsection
