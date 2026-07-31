@extends('layouts.admin')

@section('title', 'Edit Student Course')

@section('content')
<div class="max-w-lg rounded-xl border bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.student-courses.update', $studentCourse) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="mb-1.5 block text-sm font-medium text-neutral-700">Title *</label>
            <input type="text" name="title" value="{{ old('title', $studentCourse->title) }}" required class="w-full rounded-lg border border-neutral-300 px-4 py-2.5 text-sm">
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.student-courses.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Back</a>
        </div>
    </form>

    @can('studentCourses.delete')
    @if(!$studentCourse->students()->exists())
    <form method="POST" action="{{ route('admin.student-courses.destroy', $studentCourse) }}" class="mt-6" onsubmit="return confirm('Delete this course?')">
        @csrf @method('DELETE')
        <button class="text-sm text-red-600">Delete course</button>
    </form>
    @endif
    @endcan
</div>
@endsection
