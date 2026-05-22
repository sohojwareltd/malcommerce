@extends('layouts.admin')

@section('title', 'Course Categories')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Course categories</h1>
        <p class="text-neutral-600 mt-1 text-sm">Categories for digital video courses</p>
    </div>
    <a href="{{ route('admin.digital-course-categories.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light font-semibold text-sm text-center">+ Add category</a>
</div>

@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-neutral-200 text-sm">
        <thead class="bg-neutral-50">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-neutral-600">Name</th>
                <th class="px-4 py-3 text-left font-semibold text-neutral-600">Slug</th>
                <th class="px-4 py-3 text-left font-semibold text-neutral-600">Courses</th>
                <th class="px-4 py-3 text-left font-semibold text-neutral-600">Active</th>
                <th class="px-4 py-3 text-right font-semibold text-neutral-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($categories as $category)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                <td class="px-4 py-3 text-neutral-500 font-mono text-xs">{{ $category->slug }}</td>
                <td class="px-4 py-3">{{ $category->courses_count }}</td>
                <td class="px-4 py-3">{{ $category->is_active ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.digital-course-categories.edit', $category) }}" class="text-primary hover:underline font-semibold">Edit</a>
                    <form method="POST" action="{{ route('admin.digital-course-categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline font-semibold ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-neutral-500">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($categories->hasPages())<div class="px-4 py-3">{{ $categories->links() }}</div>@endif
</div>
@endsection
