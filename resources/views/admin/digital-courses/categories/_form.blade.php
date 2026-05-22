<div class="bg-white rounded-lg shadow-md p-6 max-w-xl">
    <form method="POST" action="{{ $category ? route('admin.digital-course-categories.update', $category) : route('admin.digital-course-categories.store') }}">
        @csrf
        @if($category) @method('PUT') @endif
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $category?->name) }}" required class="w-full rounded-lg border-neutral-300">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="w-full rounded-lg border-neutral-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" min="0" class="w-full rounded-lg border-neutral-300">
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary">
                <span class="text-sm">Active</span>
            </label>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-lg font-semibold">Save</button>
            <a href="{{ route('admin.digital-course-categories.index') }}" class="px-5 py-2 rounded-lg border border-neutral-300">Cancel</a>
        </div>
    </form>
</div>
