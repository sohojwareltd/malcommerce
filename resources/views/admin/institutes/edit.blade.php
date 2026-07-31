@extends('layouts.admin')

@section('title', 'Edit Institute')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Edit institute</h1></div>
@include('admin.institutes._form', ['institute' => $institute])
@endsection
