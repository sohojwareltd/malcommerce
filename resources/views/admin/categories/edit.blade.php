@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Edit Category</h1>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="mt-1 text-xs text-neutral-500">Leave empty to auto-generate from name</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description', $category->description) }}</textarea>
            </div>

            @if($category->image)
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Current Image</label>
                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-lg border border-neutral-300">
            </div>
            @endif

            <div>
                <label for="image" class="block text-sm font-medium text-neutral-700 mb-2">{{ $category->image ? 'Replace Image' : 'Category Image' }}</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                <label for="is_active" class="ml-2 text-sm font-medium text-neutral-700">Active</label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold">
                    Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="flex-1 bg-neutral-200 text-neutral-700 px-6 py-3 rounded-lg hover:bg-neutral-300 transition font-semibold text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection




