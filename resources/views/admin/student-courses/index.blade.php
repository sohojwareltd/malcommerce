@extends('layouts.admin')

@section('title', 'Student Courses')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">Student courses</h1>
    @can('studentCourses.create')
    <a href="{{ route('admin.student-courses.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Add course</a>
    @endcan
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..." class="rounded-lg border px-3 py-2 text-sm">
</form>

<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 text-left"><tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Students</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y">
            @forelse($courses as $course)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $course->title }}</td>
                <td class="px-4 py-3">{{ $course->students_count }}</td>
                <td class="px-4 py-3 text-right">
                    @can('studentCourses.update')
                    <a href="{{ route('admin.student-courses.edit', $course) }}" class="text-primary">Edit</a>
                    @endcan
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-neutral-500">No courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
