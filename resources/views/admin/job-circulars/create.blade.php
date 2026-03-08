@extends('layouts.admin')

@section('title', 'Add Job Circular')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Add Job Circular</h1>
            <p class="text-neutral-600 mt-1 text-sm">Create a new job posting</p>
        </div>
        <a href="{{ route('admin.job-circulars.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm">← Back</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.job-circulars.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-neutral-700 mb-2">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('title') border-red-500 @enderror">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Auto-generated if empty"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('slug') border-red-500 @enderror">
            @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="thumbnail" class="block text-sm font-medium text-neutral-700 mb-2">Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('thumbnail') border-red-500 @enderror">
            <p class="mt-1 text-xs text-neutral-500">jpeg, png, jpg, gif, webp — max 10MB</p>
            @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="5" class="rich-editor"
                data-editor="description">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="requirements" class="block text-sm font-medium text-neutral-700 mb-2">Requirements</label>
            <textarea name="requirements" id="requirements" rows="4" class="rich-editor"
                data-editor="requirements">{{ old('requirements') }}</textarea>
            @error('requirements')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="deadline" class="block text-sm font-medium text-neutral-700 mb-2">Application Deadline</label>
            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('deadline') border-red-500 @enderror">
            @error('deadline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium text-neutral-700">Active (visible to applicants)</span>
            </label>
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium text-neutral-700">Featured (show on homepage)</span>
            </label>
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                class="w-full max-w-[120px] px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('sort_order') border-red-500 @enderror">
            <p class="mt-1 text-xs text-neutral-500">Lower numbers appear first</p>
            @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">Create</button>
            <a href="{{ route('admin.job-circulars.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.ql-container { min-height: 200px; font-size: 14px; }
.ql-editor { min-height: 200px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editors = {};
    document.querySelectorAll('.rich-editor').forEach(function(textarea) {
        const id = textarea.id;
        const container = document.createElement('div');
        container.className = 'rich-editor-container border border-neutral-300 rounded-lg bg-white';
        container.style.minHeight = '200px';
        textarea.parentNode.insertBefore(container, textarea);
        textarea.style.display = 'none';

        const q = new Quill(container, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
        q.root.innerHTML = textarea.value || '';
        editors[id] = q;
    });

    var form = document.querySelector('.rich-editor')?.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            Object.keys(editors).forEach(function(id) {
                const input = document.getElementById(id);
                if (input && editors[id]) {
                    input.value = editors[id].root.innerHTML;
                }
            });
        });
    }
});
</script>
@endpush
