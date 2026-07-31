@extends('layouts.admin')

@section('title', 'Add Exam')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Add exam</h1></div>
@include('admin.exams._form', ['exam' => null])
@endsection
