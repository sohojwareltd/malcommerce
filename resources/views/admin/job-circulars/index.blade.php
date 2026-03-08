@extends('layouts.admin')

@section('title', 'Job Circulars')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Job Circulars</h1>
        <p class="text-neutral-600 mt-1 text-sm">Manage job postings and view applications</p>
    </div>
    @can('jobCirculars.create')
    <a href="{{ route('admin.job-circulars.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm text-center">
        + Add Job Circular
    </a>
    @endcan
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4">
    <form method="GET" action="{{ route('admin.job-circulars.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Title or slug..."
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="w-40">
            <label for="active" class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
            <select name="active" id="active" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
        <a href="{{ route('admin.job-circulars.index') }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-300">Clear</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Deadline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Applications</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse($jobCirculars as $job)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.job-circulars.show', $job) }}" class="font-medium text-primary hover:underline">{{ $job->title }}</a>
                        @if($job->is_featured)<span class="ml-1 text-amber-600 text-xs">★</span>@endif
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600">{{ $job->sort_order ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600">{{ $job->deadline?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $job->applications_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-xs rounded {{ $job->is_active ? 'bg-green-100 text-green-800' : 'bg-neutral-200 text-neutral-700' }}">
                            {{ $job->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($job->is_featured)<span class="ml-1 px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800">Featured</span>@endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @can('jobCirculars.view')
                        <a href="{{ route('admin.job-circulars.show', $job) }}" class="text-primary hover:underline text-sm">View</a>
                        @endcan
                        @can('jobCirculars.update')
                        <a href="{{ route('admin.job-circulars.edit', $job) }}" class="text-neutral-600 hover:underline text-sm">Edit</a>
                        @endcan
                        @can('jobCirculars.delete')
                        <form action="{{ route('admin.job-circulars.destroy', $job) }}" method="POST" class="inline" onsubmit="return confirm('Delete this job circular?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">No job circulars found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">{{ $jobCirculars->links() }}</div>
</div>
@endsection
