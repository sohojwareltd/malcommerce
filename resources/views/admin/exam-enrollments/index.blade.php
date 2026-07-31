@extends('layouts.admin')

@section('title', 'Exam Enrollments')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Exam enrollments</h1>
<form method="GET" class="mb-4"><select name="exam_id" class="rounded-lg border px-3 py-2 text-sm"><option value="">All exams</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->title }}</option>@endforeach</select><button class="ml-2 rounded-lg border px-3 py-2 text-sm">Filter</button></form>
<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm"><thead class="bg-neutral-50"><tr><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Exam</th><th class="px-4 py-3 text-left">Access</th><th class="px-4 py-3 text-left">Granted</th></tr></thead>
    <tbody class="divide-y">@foreach($enrollments as $enrollment)<tr><td class="px-4 py-3">{{ $enrollment->user->name }}<br><span class="text-xs text-neutral-500">{{ $enrollment->user->phone }}</span></td><td class="px-4 py-3">{{ $enrollment->exam->title }}</td><td class="px-4 py-3">{{ $enrollment->access_type }}</td><td class="px-4 py-3">{{ $enrollment->granted_at->format('Y-m-d H:i') }}</td></tr>@endforeach</tbody></table>
</div>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
