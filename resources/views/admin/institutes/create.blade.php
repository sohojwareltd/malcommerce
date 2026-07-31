@extends('layouts.admin')

@section('title', 'Add Institute')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Add institute</h1></div>
@include('admin.institutes._form', ['institute' => null])
@endsection
