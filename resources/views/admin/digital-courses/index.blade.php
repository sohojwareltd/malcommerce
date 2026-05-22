@extends('layouts.admin')

@section('title', 'Digital Products')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Digital products</h1>
        <p class="text-neutral-600 mt-1 text-sm">Paid video courses</p>
        <nav class="flex gap-2 mt-2">
            <a href="{{ route('admin.digital-courses.index') }}" class="px-3 py-1 rounded-lg text-sm font-medium {{ !request('trashed') ? 'bg-primary text-white' : 'bg-neutral-100' }}">Active</a>
            <a href="{{ route('admin.digital-courses.index', ['trashed' => 1]) }}" class="px-3 py-1 rounded-lg text-sm font-medium {{ request('trashed') ? 'bg-neutral-500 text-white' : 'bg-neutral-100' }}">Deleted</a>
        </nav>
    </div>
    @if(!request('trashed'))
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.digital-course-categories.index') }}" class="px-4 py-2 rounded-lg border border-neutral-300 font-semibold text-sm">Categories</a>
        <a href="{{ route('admin.digital-courses.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg font-semibold text-sm">+ Add course</a>
    </div>
    @endif
</div>

@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif

<form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
    @if(request('trashed'))<input type="hidden" name="trashed" value="1">@endif
    <div class="flex-1 min-w-[200px]">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..." class="w-full rounded-lg border-neutral-300 text-sm">
    </div>
    <div>
        <select name="category_id" class="rounded-lg border-neutral-300 text-sm">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full text-sm divide-y divide-neutral-200">
        <thead class="bg-neutral-50">
            <tr>
                <th class="px-4 py-3 text-left">Course</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-right">Price</th>
                <th class="px-4 py-3 text-center">Lessons</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($courses as $course)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($course->thumbnail_url)<img src="{{ $course->thumbnail_url }}" class="w-14 h-9 object-cover rounded" alt="">@endif
                        <div>
                            <div class="font-semibold">{{ $course->title }}</div>
                            <div class="text-xs text-neutral-500 font-mono">{{ $course->slug }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">{{ $course->category?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-right font-semibold">৳{{ number_format($course->price, 0) }}</td>
                <td class="px-4 py-3 text-center">{{ $course->lessons_count }}</td>
                <td class="px-4 py-3 text-center">
                    @if($course->is_active)<span class="text-green-700 text-xs font-semibold">Active</span>@else<span class="text-neutral-500 text-xs">Hidden</span>@endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    @if(request('trashed'))
                        <form method="POST" action="{{ route('admin.digital-courses.restore', $course->id) }}" class="inline">@csrf<button class="text-primary font-semibold">Restore</button></form>
                    @else
                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="text-neutral-600 hover:underline text-xs mr-2">View</a>
                        <a href="{{ route('admin.digital-courses.edit', $course) }}" class="text-primary hover:underline font-semibold">Edit</a>
                        <form method="POST" action="{{ route('admin.digital-courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="text-red-600 font-semibold ml-2">Delete</button></form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-neutral-500">No courses.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($courses->hasPages())<div class="px-4 py-3">{{ $courses->links() }}</div>@endif
</div>
@endsection
