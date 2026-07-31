<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentCourse::withCount('students');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $courses = $query->orderBy('title')->paginate(20)->withQueryString();

        return view('admin.student-courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.student-courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:student_courses,title',
        ]);

        $course = StudentCourse::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $course->id,
                'title' => $course->title,
                'message' => 'Course created.',
            ], 201);
        }

        return redirect()->route('admin.student-courses.index')->with('success', 'Course created.');
    }

    public function edit(StudentCourse $studentCourse)
    {
        return view('admin.student-courses.edit', compact('studentCourse'));
    }

    public function update(Request $request, StudentCourse $studentCourse)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:student_courses,title,' . $studentCourse->id,
        ]);

        $studentCourse->update($validated);

        return redirect()->route('admin.student-courses.index')->with('success', 'Course updated.');
    }

    public function destroy(StudentCourse $studentCourse)
    {
        if ($studentCourse->students()->exists()) {
            return redirect()->route('admin.student-courses.index')
                ->with('error', 'Cannot delete a course that is assigned to students.');
        }

        $studentCourse->delete();

        return redirect()->route('admin.student-courses.index')->with('success', 'Course deleted.');
    }
}
