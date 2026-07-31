@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Edit student</h1></div>
@include('admin.students._form', ['student' => $student])
@endsection
