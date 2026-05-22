@php
    $lessonRows = old('lessons');
    if ($lessonRows === null && isset($course)) {
        $lessonRows = $course->lessons->map(fn ($l) => [
            'id' => $l->id,
            'title' => $l->title,
            'youtube_url' => $l->youtube_url,
            'sort_order' => $l->sort_order,
            'is_active' => $l->is_active,
            'is_free' => $l->is_free,
        ])->values()->all();
    }
    $lessonRows = $lessonRows ?: [['title' => '', 'youtube_url' => '', 'sort_order' => 0, 'is_active' => true, 'is_free' => false]];
@endphp

<div class="bg-white rounded-lg shadow-md p-6" x-data="{
    lessons: {{ json_encode(array_values($lessonRows)) }},
    addLesson() { this.lessons.push({ title: '', youtube_url: '', sort_order: this.lessons.length, is_active: true, is_free: false }); },
    removeLesson(i) { this.lessons.splice(i, 1); }
}">
    <form method="POST" action="{{ $course ? route('admin.digital-courses.update', $course) : route('admin.digital-courses.store') }}" enctype="multipart/form-data">
        @csrf
        @if($course) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $course?->title) }}" required class="w-full rounded-lg border-neutral-300">
                    @error('title')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $course?->slug) }}" class="w-full rounded-lg border-neutral-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-neutral-300">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $course?->category_id) == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Price (৳) *</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $course?->price ?? 0) }}" required class="w-full rounded-lg border-neutral-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Compare at price</label>
                        <input type="number" name="compare_at_price" step="0.01" min="0" value="{{ old('compare_at_price', $course?->compare_at_price) }}" class="w-full rounded-lg border-neutral-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Short description</label>
                    <textarea name="short_description" rows="2" class="w-full rounded-lg border-neutral-300">{{ old('short_description', $course?->short_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="5" class="w-full rounded-lg border-neutral-300">{{ old('description', $course?->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Preview YouTube URL</label>
                    <input type="url" name="preview_youtube_url" value="{{ old('preview_youtube_url', $course?->preview_youtube_url) }}" placeholder="https://youtube.com/..." class="w-full rounded-lg border-neutral-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Thumbnail</label>
                    @if($course?->thumbnail)
                        <img src="{{ $course->thumbnail_url }}" alt="" class="w-32 h-20 object-cover rounded mb-2 border">
                    @endif
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sort order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $course?->sort_order ?? 0) }}" class="w-full rounded-lg border-neutral-300">
                </div>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $course?->is_active ?? true)) class="rounded text-primary"> Active</label>
                <label class="inline-flex items-center gap-2 ml-4"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $course?->is_featured)) class="rounded text-primary"> Featured</label>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold">Lessons</h2>
                    <button type="button" @click="addLesson()" class="text-sm bg-neutral-100 hover:bg-neutral-200 px-3 py-1.5 rounded-lg font-semibold">+ Add lesson</button>
                </div>
                <template x-for="(lesson, index) in lessons" :key="index">
                    <div class="border border-neutral-200 rounded-lg p-4 mb-3 bg-neutral-50/50">
                        <input type="hidden" :name="'lessons['+index+'][id]'" x-model="lesson.id">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold text-neutral-500" x-text="'Lesson ' + (index + 1)"></span>
                            <button type="button" @click="removeLesson(index)" class="text-red-600 text-xs font-semibold">Remove</button>
                        </div>
                        <input type="text" :name="'lessons['+index+'][title]'" x-model="lesson.title" placeholder="Lesson title" class="w-full rounded-lg border-neutral-300 text-sm mb-2" required>
                        <input type="url" :name="'lessons['+index+'][youtube_url]'" x-model="lesson.youtube_url" placeholder="YouTube URL" class="w-full rounded-lg border-neutral-300 text-sm mb-2" required>
                        <input type="number" :name="'lessons['+index+'][sort_order]'" x-model="lesson.sort_order" min="0" class="w-24 rounded-lg border-neutral-300 text-sm mb-2">
                        <div class="flex flex-wrap gap-3 mt-1">
                            <label class="inline-flex items-center gap-1 text-xs">
                                <input type="hidden" :name="'lessons['+index+'][is_active]'" :value="lesson.is_active ? 1 : 0">
                                <input type="checkbox" x-model="lesson.is_active" class="rounded text-primary"> Active
                            </label>
                            <label class="inline-flex items-center gap-1 text-xs">
                                <input type="hidden" :name="'lessons['+index+'][is_free]'" :value="lesson.is_free ? 1 : 0">
                                <input type="checkbox" x-model="lesson.is_free" class="rounded text-emerald-600"> Free preview (watch without purchase)
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-lg font-semibold">Save course</button>
            <a href="{{ route('admin.digital-courses.index') }}" class="px-5 py-2 rounded-lg border border-neutral-300">Cancel</a>
        </div>
    </form>
</div>
