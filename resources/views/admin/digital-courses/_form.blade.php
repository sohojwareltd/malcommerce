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
    $courseSmsStatuses = \App\Services\DigitalCourseSmsService::statusLabels();
    $courseSmsDefaults = \App\Services\DigitalCourseSmsService::defaultTemplates();
    $courseSmsStored = old('sms_templates', $course?->sms_templates ?? []);
    $courseSmsTemplates = [];
    foreach ($courseSmsStatuses as $statusKey => $statusLabel) {
        $stored = $courseSmsStored[$statusKey] ?? null;
        $courseSmsTemplates[$statusKey] = is_string($stored) && trim($stored) !== ''
            ? $stored
            : ($courseSmsDefaults[$statusKey] ?? '');
    }
    $inputClass = 'w-full rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
    $labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
    $hintClass = 'mt-1 text-xs text-neutral-500';
    $existingThumb = $course?->thumbnail_url ?? '';
    $previewYoutube = old('preview_youtube_url', $course?->preview_youtube_url ?? '');
@endphp

<div class="rounded-xl border border-neutral-200 bg-white shadow-sm"
     x-data="{
        lessons: {{ json_encode(array_values($lessonRows)) }},
        previewYoutube: @js($previewYoutube),
        existingThumbUrl: @js($existingThumb),
        thumbnailPreview: @js($existingThumb),
        thumbnailFileName: '',
        thumbDragOver: false,
        addLesson() {
            this.lessons.push({ title: '', youtube_url: '', sort_order: this.lessons.length, is_active: true, is_free: false });
        },
        removeLesson(i) { this.lessons.splice(i, 1); },
        youtubeVideoId(url) {
            if (!url) return null;
            const m = String(url).match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
            return m ? m[1] : null;
        },
        youtubeThumb(url) {
            const id = this.youtubeVideoId(url);
            return id ? 'https://img.youtube.com/vi/' + id + '/mqdefault.jpg' : null;
        },
        onThumbnailChange(e) {
            const file = e.target.files?.[0];
            if (!file) return;
            this.thumbnailFileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { this.thumbnailPreview = ev.target.result; };
            reader.readAsDataURL(file);
        },
        clearThumbnail() {
            this.thumbnailPreview = this.existingThumbUrl || '';
            this.thumbnailFileName = '';
            const input = this.$refs.thumbnailInput;
            if (input) input.value = '';
        },
        onThumbnailDrop(e) {
            this.thumbDragOver = false;
            const file = e.dataTransfer?.files?.[0];
            if (!file || !file.type.startsWith('image/')) return;
            const input = this.$refs.thumbnailInput;
            if (input) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
            this.onThumbnailChange({ target: { files: [file] } });
        }
     }">
    <form method="POST"
          action="{{ $course ? route('admin.digital-courses.update', $course) : route('admin.digital-courses.store') }}"
          enctype="multipart/form-data"
          class="divide-y divide-neutral-200">
        @csrf
        @if($course) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-8 p-6 lg:grid-cols-2 lg:p-8">
            {{-- Left: course details --}}
            <div class="space-y-8">
                <section>
                    <h2 class="mb-4 text-base font-bold text-neutral-900">Basic details</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="{{ $labelClass }}">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $course?->title) }}" required class="{{ $inputClass }}">
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="slug" class="{{ $labelClass }}">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $course?->slug) }}" placeholder="auto-from-title" class="{{ $inputClass }} font-mono text-xs">
                            <p class="{{ $hintClass }}">Leave empty to generate from title.</p>
                        </div>
                        <div>
                            <label for="category_id" class="{{ $labelClass }}">Category</label>
                            <select name="category_id" id="category_id" class="{{ $inputClass }}">
                                <option value="">— None —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $course?->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="{{ $labelClass }}">Price (৳) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-neutral-500">৳</span>
                                    <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $course?->price ?? 0) }}" required class="{{ $inputClass }} pl-8">
                                </div>
                                @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="compare_at_price" class="{{ $labelClass }}">Compare at price</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-neutral-500">৳</span>
                                    <input type="number" name="compare_at_price" id="compare_at_price" step="0.01" min="0" value="{{ old('compare_at_price', $course?->compare_at_price) }}" class="{{ $inputClass }} pl-8">
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="checkout_form_title" class="{{ $labelClass }}">Checkout form title</label>
                                <input type="text" name="checkout_form_title" id="checkout_form_title" value="{{ old('checkout_form_title', $course?->checkout_form_title) }}" placeholder="কোর্স কিনুন" class="{{ $inputClass }}">
                                <p class="{{ $hintClass }}">Used on custom sales pages. Leave empty for default.</p>
                            </div>
                            <div>
                                <label for="checkout_button_text" class="{{ $labelClass }}">Checkout button text</label>
                                <input type="text" name="checkout_button_text" id="checkout_button_text" value="{{ old('checkout_button_text', $course?->checkout_button_text) }}" placeholder="bKash দিয়ে কিনুন" class="{{ $inputClass }}">
                            </div>
                        </div>
                        <div>
                            <label for="short_description" class="{{ $labelClass }}">Short description</label>
                            <textarea name="short_description" id="short_description" rows="2" class="{{ $inputClass }} resize-y">{{ old('short_description', $course?->short_description) }}</textarea>
                        </div>
                        <div>
                            <label for="description" class="{{ $labelClass }}">Description</label>
                            <textarea name="description" id="description" rows="5" class="{{ $inputClass }} resize-y">{{ old('description', $course?->description) }}</textarea>
                        </div>
                        <div class="flex flex-wrap gap-6 rounded-lg border border-neutral-200 bg-neutral-50/80 px-4 py-3">
                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $course?->is_active ?? true)) class="h-4 w-4 rounded border-neutral-300 text-primary focus:ring-primary/30">
                                <span class="text-sm font-medium text-neutral-700">Active</span>
                            </label>
                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $course?->is_featured)) class="h-4 w-4 rounded border-neutral-300 text-primary focus:ring-primary/30">
                                <span class="text-sm font-medium text-neutral-700">Featured on home page</span>
                            </label>
                        </div>
                        <div class="max-w-xs">
                            <label for="sort_order" class="{{ $labelClass }}">Sort order</label>
                            <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', $course?->sort_order ?? 0) }}" class="{{ $inputClass }}">
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="mb-1 text-base font-bold text-neutral-900">Media</h2>
                    <p class="{{ $hintClass }} mb-4">Preview video and cover image for the course catalog.</p>

                    <div class="space-y-5">
                        {{-- YouTube preview URL --}}
                        <div>
                            <label for="preview_youtube_url" class="{{ $labelClass }}">Preview YouTube URL</label>
                            <div class="flex overflow-hidden rounded-lg border border-neutral-300 bg-white shadow-sm focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                <span class="flex shrink-0 items-center border-r border-neutral-200 bg-red-50 px-3 text-red-600" title="YouTube">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.5 31.5 0 0 0 24 12a31.5 31.5 0 0 0-.5-5.8zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                                    </svg>
                                </span>
                                <input type="url"
                                       name="preview_youtube_url"
                                       id="preview_youtube_url"
                                       x-model="previewYoutube"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-sm focus:outline-none focus:ring-0">
                            </div>
                            <p class="{{ $hintClass }}">Used on the course page if no custom thumbnail is uploaded.</p>
                            @error('preview_youtube_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                            <div x-show="youtubeThumb(previewYoutube)" x-cloak class="mt-3 overflow-hidden rounded-lg border border-neutral-200 bg-neutral-900">
                                <div class="flex items-center gap-2 border-b border-neutral-700 bg-neutral-800 px-3 py-1.5">
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-neutral-400">Preview</span>
                                </div>
                                <img :src="youtubeThumb(previewYoutube)" alt="YouTube preview" class="aspect-video w-full object-cover">
                            </div>
                        </div>

                        {{-- Thumbnail upload --}}
                        <div>
                            <label class="{{ $labelClass }}">Course thumbnail</label>
                            <p class="{{ $hintClass }} mb-3">JPG, PNG or WebP. Max 4 MB. Overrides YouTube preview when set.</p>

                            <div x-show="thumbnailPreview" x-cloak class="mb-3 flex items-start gap-4">
                                <div class="relative overflow-hidden rounded-lg border border-neutral-200 shadow-sm">
                                    <img :src="thumbnailPreview" alt="Thumbnail preview" class="h-28 w-44 object-cover sm:h-32 sm:w-52">
                                </div>
                                <button type="button"
                                        x-show="thumbnailFileName"
                                        @click="clearThumbnail()"
                                        class="rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-50">
                                    Undo upload
                                </button>
                            </div>

                            <label class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-4 py-8 text-center transition"
                                   :class="thumbDragOver ? 'border-primary bg-primary/5' : (thumbnailPreview ? 'border-solid border-neutral-200 bg-neutral-50/50 py-5' : 'border-neutral-300 bg-neutral-50/50 hover:border-primary/50 hover:bg-primary/5')"
                                   @dragover.prevent="thumbDragOver = true"
                                   @dragleave.prevent="thumbDragOver = false"
                                   @drop.prevent="onThumbnailDrop($event)">
                                <input type="file"
                                       name="thumbnail"
                                       accept="image/jpeg,image/png,image/webp,image/*"
                                       class="sr-only"
                                       x-ref="thumbnailInput"
                                       @change="onThumbnailChange($event)">
                                <span class="mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-neutral-200 group-hover:ring-primary/30">
                                    <svg class="h-6 w-6 text-neutral-400 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span class="text-sm font-semibold text-neutral-700 group-hover:text-primary" x-text="thumbnailFileName || 'Click to upload or drag an image'"></span>
                                <span class="mt-0.5 text-xs text-neutral-500">PNG, JPG, WebP up to 4MB</span>
                            </label>
                            @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
            </div>

            {{-- Right: lessons --}}
            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900">Lessons</h2>
                        <p class="{{ $hintClass }}">Add YouTube links for each lesson in order.</p>
                    </div>
                    <button type="button"
                            @click="addLesson()"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add lesson
                    </button>
                </div>

                <div class="max-h-[calc(100vh-12rem)] space-y-3 overflow-y-auto pr-1 lg:max-h-none">
                    <template x-for="(lesson, index) in lessons" :key="index">
                        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                            <input type="hidden" :name="'lessons['+index+'][id]'" x-model="lesson.id">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center rounded-md bg-neutral-100 px-2 py-0.5 text-xs font-bold text-neutral-600" x-text="'Lesson ' + (index + 1)"></span>
                                <button type="button"
                                        @click="removeLesson(index)"
                                        class="text-xs font-semibold text-red-600 hover:text-red-700"
                                        x-show="lessons.length > 1">
                                    Remove
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-neutral-600">Title</label>
                                    <input type="text"
                                           :name="'lessons['+index+'][title]'"
                                           x-model="lesson.title"
                                           placeholder="e.g. Introduction"
                                           class="{{ $inputClass }}"
                                           required>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-medium text-neutral-600">YouTube URL</label>
                                    <div class="flex overflow-hidden rounded-lg border border-neutral-300 bg-white shadow-sm focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                        <span class="flex shrink-0 items-center border-r border-neutral-200 bg-red-50 px-2.5 text-red-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.5 31.5 0 0 0 24 12a31.5 31.5 0 0 0-.5-5.8zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
                                        </span>
                                        <input type="url"
                                               :name="'lessons['+index+'][youtube_url]'"
                                               x-model="lesson.youtube_url"
                                               placeholder="https://youtu.be/..."
                                               class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm focus:outline-none"
                                               required>
                                    </div>
                                    <div x-show="youtubeThumb(lesson.youtube_url)" class="mt-2 flex items-center gap-2">
                                        <img :src="youtubeThumb(lesson.youtube_url)" alt="" class="h-10 w-16 rounded border border-neutral-200 object-cover">
                                        <span class="text-[10px] text-neutral-500">Thumbnail detected</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-end gap-3">
                                    <div class="w-24">
                                        <label class="mb-1 block text-xs font-medium text-neutral-600">Order</label>
                                        <input type="number"
                                               :name="'lessons['+index+'][sort_order]'"
                                               x-model="lesson.sort_order"
                                               min="0"
                                               class="{{ $inputClass }} py-2">
                                    </div>
                                    <div class="flex flex-1 flex-wrap gap-4 pb-0.5">
                                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-neutral-700">
                                            <input type="hidden" :name="'lessons['+index+'][is_active]'" :value="lesson.is_active ? 1 : 0">
                                            <input type="checkbox" x-model="lesson.is_active" class="h-4 w-4 rounded border-neutral-300 text-primary focus:ring-primary/30">
                                            Active
                                        </label>
                                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-neutral-700">
                                            <input type="hidden" :name="'lessons['+index+'][is_free]'" :value="lesson.is_free ? 1 : 0">
                                            <input type="checkbox" x-model="lesson.is_free" class="h-4 w-4 rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500/30">
                                            Free preview
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        <div class="space-y-4 p-6 lg:p-8">
            <div>
                <h2 class="mb-1 text-base font-bold text-neutral-900">SMS messages</h2>
                <p class="{{ $hintClass }} mb-4">Default messages are shown below. Edit any field to customize for this course.</p>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach($courseSmsStatuses as $statusKey => $statusLabel)
                    <div class="rounded-lg border border-neutral-200 bg-neutral-50/50 p-4">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <label class="text-sm font-semibold text-neutral-800">{{ $statusLabel }}</label>
                            <span class="rounded bg-neutral-200 px-1.5 py-0.5 text-[10px] font-mono uppercase text-neutral-600">{{ $statusKey }}</span>
                        </div>
                        <textarea name="sms_templates[{{ $statusKey }}]" rows="4" class="{{ $inputClass }} font-mono text-xs leading-relaxed">{{ $courseSmsTemplates[$statusKey] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
            <p class="{{ $hintClass }}">
                Placeholders: <code class="rounded bg-neutral-100 px-1">{order_number}</code>,
                <code class="rounded bg-neutral-100 px-1">{customer_name}</code>,
                <code class="rounded bg-neutral-100 px-1">{customer_phone}</code>,
                <code class="rounded bg-neutral-100 px-1">{course_title}</code>,
                <code class="rounded bg-neutral-100 px-1">{total_price}</code>,
                <code class="rounded bg-neutral-100 px-1">{my_courses_url}</code>,
                <code class="rounded bg-neutral-100 px-1">{login_url}</code>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-neutral-200 bg-neutral-50/50 px-6 py-4 lg:px-8">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save course
            </button>
            <a href="{{ route('admin.digital-courses.index') }}" class="rounded-lg border border-neutral-300 bg-white px-5 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
