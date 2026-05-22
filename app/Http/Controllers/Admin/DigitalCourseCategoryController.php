<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalCourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DigitalCourseCategoryController extends Controller
{
    public function index()
    {
        $categories = DigitalCourseCategory::query()
            ->withCount('courses')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.digital-courses.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.digital-courses.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_course_categories,slug',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->uniqueSlug($slug);

        DigitalCourseCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.digital-course-categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(DigitalCourseCategory $digitalCourseCategory)
    {
        return view('admin.digital-courses.categories.edit', ['category' => $digitalCourseCategory]);
    }

    public function update(Request $request, DigitalCourseCategory $digitalCourseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_course_categories,slug,' . $digitalCourseCategory->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        if ($slug !== $digitalCourseCategory->slug) {
            $slug = $this->uniqueSlug($slug, $digitalCourseCategory->id);
        }

        $digitalCourseCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.digital-course-categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(DigitalCourseCategory $digitalCourseCategory)
    {
        if ($digitalCourseCategory->courses()->exists()) {
            return back()->with('error', 'Cannot delete a category that has courses.');
        }

        $digitalCourseCategory->delete();

        return redirect()->route('admin.digital-course-categories.index')
            ->with('success', 'Category deleted.');
    }

    protected function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $base = $slug;
        $i = 0;
        while (DigitalCourseCategory::where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $i++;
            $slug = $base . '-' . $i;
        }

        return $slug;
    }
}
