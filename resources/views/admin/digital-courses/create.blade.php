@extends('layouts.admin')

@section('title', 'Add Digital Course')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Add digital course</h1></div>
@include('admin.digital-courses._form', ['course' => null])
@endsection
