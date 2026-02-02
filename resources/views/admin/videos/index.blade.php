@extends('layouts.admin')

@section('title', 'Videos')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Videos</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Manage YouTube videos</p>
    </div>
    <a href="{{ route('admin.videos.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
        + Add Video
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
@endif

<!-- Search & Filter Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.videos.index') }}" class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title or category..." class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
            </div>
            <div class="sm:w-48">
                <label for="category" class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
                <select name="category" id="category" onchange="this.form.submit()" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base">Search</button>
                @if(request('search') || request('category'))
                    <a href="{{ route('admin.videos.index') }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base">Clear</a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Thumbnail</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Sort</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($videos as $video)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-16 h-10 object-cover rounded">
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-neutral-900 line-clamp-2">{{ $video->title }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">{{ $video->category }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($video->is_featured)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Featured</span>
                        @else
                            <span class="text-neutral-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $video->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $video->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $video->sort_order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.videos.edit', $video) }}" class="text-primary hover:text-primary-light font-medium">Edit</a>
                            <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-neutral-500">No videos found. <a href="{{ route('admin.videos.create') }}" class="text-primary hover:underline">Add one now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($videos as $video)
        <div class="p-4 hover:bg-neutral-50 transition-colors">
            <div class="flex gap-4">
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-24 h-14 object-cover rounded flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-neutral-900 line-clamp-2">{{ $video->title }}</h3>
                    <p class="text-xs text-neutral-600 mt-1">{{ $video->category }}</p>
                    <div class="flex gap-2 mt-2">
                        @if($video->is_featured)<span class="text-xs text-amber-600">Featured</span>@endif
                        <span class="text-xs {{ $video->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $video->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="flex gap-3 mt-3">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="text-primary font-medium text-sm">Edit</a>
                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline" onsubmit="return confirm('Delete this video?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 font-medium text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">No videos found. <a href="{{ route('admin.videos.create') }}" class="text-primary hover:underline">Add one now</a></div>
        @endforelse
    </div>
</div>

@if($videos->hasPages())
<div class="mt-4">{{ $videos->links() }}</div>
@endif
@endsection
