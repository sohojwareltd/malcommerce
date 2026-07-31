@extends('layouts.admin')

@section('title', 'Institutes')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">Institutes</h1>
    @can('institutes.create')
    <a href="{{ route('admin.institutes.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Add institute</a>
    @endcan
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-lg border px-3 py-2 text-sm">
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="trashed" value="1" @checked(request('trashed'))> Trashed</label>
    <button class="rounded-lg border px-3 py-2 text-sm">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 text-left"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Students</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y">
            @forelse($institutes as $institute)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $institute->name }}</td>
                <td class="px-4 py-3">{{ $institute->students_count }}</td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.institutes.edit', $institute) }}" class="text-primary">Edit</a></td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-neutral-500">No institutes yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $institutes->links() }}</div>
@endsection
