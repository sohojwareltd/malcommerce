<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalCourse;
use App\Models\DigitalCourseCategory;
use App\Models\DigitalCourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = DigitalCourse::query()->with('category');

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $courses = $query->withCount('lessons')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = DigitalCourseCategory::orderBy('name')->get();

        return view('admin.digital-courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = DigitalCourseCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.digital-courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        $slug = $this->uniqueCourseSlug($slug);

        $thumbnailPath = $this->storeThumbnail($request);

        $course = DigitalCourse::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'thumbnail' => $thumbnailPath,
            'preview_youtube_url' => $validated['preview_youtube_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'checkout_form_title' => $validated['checkout_form_title'] ?? null,
            'checkout_button_text' => $validated['checkout_button_text'] ?? null,
            'sms_templates' => $this->normalizeSmsTemplates($request->input('sms_templates')),
        ]);

        $this->syncLessons($course, $request->input('lessons', []));

        return redirect()->route('admin.digital-courses.edit', $course)
            ->with('success', 'Course created.');
    }

    public function edit(DigitalCourse $digitalCourse)
    {
        $digitalCourse->load('lessons');
        $categories = DigitalCourseCategory::orderBy('name')->get();

        return view('admin.digital-courses.edit', [
            'course' => $digitalCourse,
            'categories' => $categories,
        ]);
    }

    public function builder(DigitalCourse $digitalCourse)
    {
        return view('admin.digital-courses.builder', ['course' => $digitalCourse]);
    }

    public function update(Request $request, DigitalCourse $digitalCourse)
    {
        $validated = $this->validateCourse($request, $digitalCourse->id);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        if ($slug !== $digitalCourse->slug) {
            $slug = $this->uniqueCourseSlug($slug, $digitalCourse->id);
        }

        $thumbnailPath = $this->storeThumbnail($request) ?? $digitalCourse->thumbnail;
        $isLayoutOnlySave = $request->filled('page_layout');

        $updateData = [
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'thumbnail' => $thumbnailPath,
            'preview_youtube_url' => $validated['preview_youtube_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'checkout_form_title' => $validated['checkout_form_title'] ?? null,
            'checkout_button_text' => $validated['checkout_button_text'] ?? null,
        ];

        if (!$isLayoutOnlySave) {
            $updateData['sms_templates'] = $this->normalizeSmsTemplates($request->input('sms_templates'));
        }

        if (array_key_exists('page_layout', $validated)) {
            if (isset($validated['page_layout']) && is_string($validated['page_layout']) && $validated['page_layout'] !== '') {
                $decoded = json_decode($validated['page_layout'], true);
                $updateData['page_layout'] = is_array($decoded) ? $decoded : null;
            } elseif ($request->has('page_layout')) {
                $updateData['page_layout'] = null;
            }
        }

        $digitalCourse->update($updateData);

        if (!$isLayoutOnlySave && $request->has('lessons')) {
            $this->syncLessons($digitalCourse, $request->input('lessons', []));
        }

        if ($request->has('page_layout')) {
            return redirect()->route('admin.digital-courses.builder', $digitalCourse)
                ->with('success', 'Layout saved successfully!');
        }

        return redirect()->route('admin.digital-courses.edit', $digitalCourse)
            ->with('success', 'Course updated.');
    }

    public function destroy(DigitalCourse $digitalCourse)
    {
        $digitalCourse->delete();

        return redirect()->route('admin.digital-courses.index')
            ->with('success', 'Course deleted.');
    }

    public function restore(int $digitalCourse)
    {
        $course = DigitalCourse::onlyTrashed()->findOrFail($digitalCourse);
        $course->restore();

        return redirect()->route('admin.digital-courses.index')
            ->with('success', 'Course restored.');
    }

    protected function validateCourse(Request $request, ?int $courseId = null): array
    {
        return $request->validate([
            'category_id' => 'nullable|exists:digital_course_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_courses,slug,' . ($courseId ?? 'NULL'),
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|image|max:4096',
            'preview_youtube_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'checkout_form_title' => 'nullable|string|max:255',
            'checkout_button_text' => 'nullable|string|max:255',
            'page_layout' => 'nullable',
            'lessons' => 'nullable|array',
            'lessons.*.id' => 'nullable|integer',
            'lessons.*.title' => 'required_with:lessons.*.youtube_url|string|max:255',
            'lessons.*.youtube_url' => 'required_with:lessons.*.title|string|max:500',
            'lessons.*.sort_order' => 'nullable|integer|min:0',
            'lessons.*.is_active' => 'nullable|boolean',
            'lessons.*.is_free' => 'nullable|boolean',
            'lessons.*._delete' => 'nullable|boolean',
            'sms_templates' => 'nullable|array',
            'sms_templates.*' => 'nullable|string|max:1000',
        ]);
    }

    protected function normalizeSmsTemplates(?array $templates): ?array
    {
        if (!is_array($templates)) {
            return null;
        }

        $filtered = array_filter(
            array_map(fn ($value) => is_string($value) ? trim($value) : '', $templates),
            fn ($value) => $value !== ''
        );

        return $filtered === [] ? null : $filtered;
    }

    protected function storeThumbnail(Request $request): ?string
    {
        if ($request->hasFile('thumbnail')) {
            return $request->file('thumbnail')->store('digital-courses', 'public');
        }

        return null;
    }

    protected function syncLessons(DigitalCourse $course, array $lessons): void
    {
        $keptIds = [];

        foreach ($lessons as $index => $row) {
            if (!empty($row['_delete'])) {
                if (!empty($row['id'])) {
                    DigitalCourseLesson::where('digital_course_id', $course->id)
                        ->where('id', $row['id'])
                        ->delete();
                }
                continue;
            }

            if (empty($row['title']) || empty($row['youtube_url'])) {
                continue;
            }

            $data = [
                'title' => $row['title'],
                'youtube_url' => $row['youtube_url'],
                'sort_order' => (int) ($row['sort_order'] ?? $index),
                'is_active' => !empty($row['is_active']),
                'is_free' => !empty($row['is_free']),
            ];

            if (!empty($row['id'])) {
                $lesson = DigitalCourseLesson::where('digital_course_id', $course->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($lesson) {
                    $lesson->update($data);
                    $keptIds[] = $lesson->id;
                }
            } else {
                $lesson = $course->lessons()->create($data);
                $keptIds[] = $lesson->id;
            }
        }
    }

    protected function uniqueCourseSlug(string $slug, ?int $exceptId = null): string
    {
        $base = $slug;
        $i = 0;
        while (DigitalCourse::withTrashed()->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $i++;
            $slug = $base . '-' . $i;
        }

        return $slug;
    }
}
