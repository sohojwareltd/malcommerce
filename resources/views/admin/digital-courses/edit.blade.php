@extends('layouts.admin')

@section('title', 'Edit Digital Course')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-2xl font-bold">Edit: {{ $course->title }}</h1>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('courses.show', $course) }}" target="_blank" class="px-4 py-2 rounded-lg border border-neutral-300 text-sm font-semibold text-neutral-700 hover:bg-neutral-50 transition">
            View course
        </a>
        <a href="{{ route('admin.digital-courses.builder', $course) }}" class="px-4 py-2 rounded-lg bg-sky-700 text-white text-sm font-semibold hover:bg-sky-800 transition">
            Page Builder
        </a>
    </div>
</div>
@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif
@include('admin.digital-courses._form', ['course' => $course])
@endsection
