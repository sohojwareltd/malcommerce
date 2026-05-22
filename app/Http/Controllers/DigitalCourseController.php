<?php

namespace App\Http\Controllers;

use App\Models\DigitalCourse;
use App\Models\DigitalCourseCategory;
use App\Models\DigitalCourseEnrollment;
use App\Models\DigitalCourseLesson;
use Illuminate\Http\Request;

class DigitalCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = DigitalCourse::query()->active()->with('category');

        if ($request->filled('category')) {
            $category = DigitalCourseCategory::where('slug', $request->category)
                ->where('is_active', true)
                ->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $courses = $query->withCount(['activeLessons as lessons_count'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $categories = DigitalCourseCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(DigitalCourse $course)
    {
        if (!$course->is_active) {
            abort(404);
        }

        $course->load(['category', 'activeLessons']);

        $enrolled = false;
        if (auth()->check()) {
            $enrolled = DigitalCourseEnrollment::where('user_id', auth()->id())
                ->where('digital_course_id', $course->id)
                ->exists();
        }

        return view('courses.show', compact('course', 'enrolled'));
    }

    public function watchLesson(DigitalCourse $course, DigitalCourseLesson $lesson)
    {
        if (!$course->is_active) {
            abort(404);
        }

        if ($lesson->digital_course_id !== $course->id || !$lesson->is_active) {
            abort(404);
        }

        $enrolled = false;
        if (auth()->check()) {
            $enrolled = DigitalCourseEnrollment::where('user_id', auth()->id())
                ->where('digital_course_id', $course->id)
                ->exists();
        }

        if (!$lesson->isWatchable($enrolled)) {
            return redirect()->route('courses.show', $course);
        }

        $course->load(['category', 'activeLessons']);

        return view('courses.watch-lesson', compact('course', 'lesson', 'enrolled'));
    }
}
