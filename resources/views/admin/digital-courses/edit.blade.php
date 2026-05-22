@extends('layouts.admin')

@section('title', 'Edit Digital Course')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Edit: {{ $course->title }}</h1></div>
@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif
@include('admin.digital-courses._form', ['course' => $course])
@endsection
