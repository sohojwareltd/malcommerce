@extends('layouts.admin')

@section('title', 'Edit Course Category')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Edit category</h1></div>
@include('admin.digital-courses.categories._form', ['category' => $category])
@endsection
