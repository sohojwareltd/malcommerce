@extends('layouts.admin')

@section('title', 'Add Course Category')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Add category</h1></div>
@include('admin.digital-courses.categories._form', ['category' => null])
@endsection
