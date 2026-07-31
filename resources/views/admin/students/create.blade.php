@extends('layouts.admin')

@section('title', 'Add Student')

@section('content')
@include('admin.students._form', ['student' => null])
@endsection
