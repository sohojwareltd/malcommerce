@extends('layouts.admin')

@section('title', 'Edit Exam')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">Edit exam</h1>
    <a href="{{ route('exams.show', $exam) }}" target="_blank" class="text-sm text-primary hover:underline">View public page</a>
</div>
@include('admin.exams._form', ['exam' => $exam])
@endsection
