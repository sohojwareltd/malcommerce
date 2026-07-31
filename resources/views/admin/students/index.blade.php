@extends('layouts.admin')

@section('title', 'Students')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">Students</h1>
    @can('students.create')
    <a href="{{ route('admin.students.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Add student</a>
    @endcan
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, roll, reg..." class="rounded-lg border px-3 py-2 text-sm">
    <select name="institute_id" class="rounded-lg border px-3 py-2 text-sm">
        <option value="">All institutes</option>
        @foreach($institutes as $institute)
        <option value="{{ $institute->id }}" @selected(request('institute_id') == $institute->id)>{{ $institute->name }}</option>
        @endforeach
    </select>
    <button class="rounded-lg border px-3 py-2 text-sm">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 text-left"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Roll</th><th class="px-4 py-3">Institute</th><th class="px-4 py-3">Course</th><th class="px-4 py-3">CGPA</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y">
            @foreach($students as $student)
            <tr>
                <td class="px-4 py-3"><div class="font-semibold">{{ $student->name }}</div><div class="text-xs text-neutral-500">{{ $student->serial_number }}</div></td>
                <td class="px-4 py-3">{{ $student->roll_number ?? '—' }}</td>
                <td class="px-4 py-3">{{ $student->institute->name }}</td>
                <td class="px-4 py-3">{{ $student->course->title }}</td>
                <td class="px-4 py-3">{{ $student->cgpa ? number_format($student->cgpa, 2) : '—' }}</td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.students.show', $student) }}" class="text-primary">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection
