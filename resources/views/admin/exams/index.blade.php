@extends('layouts.admin')

@section('title', 'Exams')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">Exams</h1>
    @can('exams.create')
    <a href="{{ route('admin.exams.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Add exam</a>
    @endcan
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="trashed" value="1" @checked(request('trashed'))> Trashed</label>
    <button class="rounded-lg border px-3 py-2 text-sm">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 text-left text-neutral-600">
            <tr>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Access</th>
                <th class="px-4 py-3">Questions</th>
                <th class="px-4 py-3">Enrolled</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($exams as $exam)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-semibold">{{ $exam->title }}</div>
                    <div class="text-xs text-neutral-500">{{ $exam->slug }}</div>
                </td>
                <td class="px-4 py-3">{{ \App\Models\Exam::accessTypeLabels()[$exam->access_type] ?? $exam->access_type }}</td>
                <td class="px-4 py-3">{{ $exam->questions_count }}</td>
                <td class="px-4 py-3">{{ $exam->enrollments_count }}</td>
                <td class="px-4 py-3">{{ $exam->is_active ? 'Active' : 'Inactive' }}</td>
                <td class="px-4 py-3 text-right">
                    @can('exams.update')
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="text-primary hover:underline">Edit</a>
                    @endcan
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-neutral-500">No exams yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $exams->links() }}</div>
@endsection
