<?php

namespace App\Http\Controllers;

use App\Models\DigitalCourse;
use App\Models\DigitalCourseEnrollment;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{
    public function index()
    {
        $enrollments = DigitalCourseEnrollment::query()
            ->where('user_id', auth()->id())
            ->with([
                'course' => fn ($q) => $q->with('category')->withCount('activeLessons as lessons_count'),
                'order:id,order_number,payment_completed_at,total_price',
            ])
            ->orderByDesc('granted_at')
            ->get();

        return view('my-courses.index', compact('enrollments'));
    }

    public function show(DigitalCourse $course)
    {
        $enrollment = DigitalCourseEnrollment::where('user_id', auth()->id())
            ->where('digital_course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You do not have access to this course.');
        }

        $course->load(['activeLessons', 'category']);

        $lessonIndex = (int) request('lesson', 0);
        $lessons = $course->activeLessons;
        $activeLesson = $lessons->get($lessonIndex) ?? $lessons->first();

        return view('my-courses.show', compact('course', 'enrollment', 'lessons', 'activeLesson', 'lessonIndex'));
    }
}
