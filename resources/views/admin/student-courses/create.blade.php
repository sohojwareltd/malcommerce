@extends('layouts.admin')

@section('title', 'Add Student Course')

@section('content')
<div class="max-w-lg rounded-xl border bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.student-courses.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-neutral-700">Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-neutral-300 px-4 py-2.5 text-sm" placeholder="Diploma in Ayurvedic medicine & Surgery (DAMS)">
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Save</button>
            <a href="{{ route('admin.student-courses.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Back</a>
        </div>
    </form>
</div>
@endsection
