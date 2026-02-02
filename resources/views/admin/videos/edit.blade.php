@extends('layouts.admin')

@section('title', 'Edit Video')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Edit Video</h1>
    <p class="text-neutral-600 mt-1">Update video details</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="youtube_link" class="block text-sm font-medium text-neutral-700 mb-2">YouTube Link <span class="text-red-500">*</span></label>
                <input type="url" name="youtube_link" id="youtube_link" value="{{ old('youtube_link', $video->youtube_link) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('youtube_link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Current Thumbnail</label>
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-48 h-28 object-cover rounded mb-2">
            </div>

            <div>
                <label for="thumbnail" class="block text-sm font-medium text-neutral-700 mb-2">Replace Thumbnail (Upload)</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="thumbnail_url" class="block text-sm font-medium text-neutral-700 mb-2">Or Thumbnail URL</label>
                <input type="url" name="thumbnail_url" id="thumbnail_url" value="{{ old('thumbnail_url', str_starts_with($video->thumbnail ?? '', 'http') ? $video->thumbnail : '') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('thumbnail_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-neutral-700 mb-2">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $video->title) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-neutral-700 mb-2">Category <span class="text-red-500">*</span></label>
                <input type="text" name="category" id="category" value="{{ old('category', $video->category) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $video->sort_order) }}" min="0" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="flex gap-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $video->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="ml-2 text-sm font-medium text-neutral-700">Featured (show on home page)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $video->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold">Update Video</button>
                <a href="{{ route('admin.videos.index') }}" class="flex-1 bg-neutral-200 text-neutral-700 px-6 py-3 rounded-lg hover:bg-neutral-300 transition font-semibold text-center">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
