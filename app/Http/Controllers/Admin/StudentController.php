<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Services\ImageResizeService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['institute', 'course']);

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('institute_id')) {
            $query->where('institute_id', $request->institute_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $institutes = Institute::orderBy('name')->get();

        return view('admin.students.index', compact('students', 'institutes'));
    }

    public function create()
    {
        [$institutes, $courses] = $this->formOptions();

        return view('admin.students.create', compact('institutes', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateStudent($request);
        $validated['photo'] = $this->storePhoto($request);
        $validated['issue_date'] = $validated['issue_date'] ?? now()->toDateString();

        $student = Student::create($validated);

        return redirect()->route('admin.students.show', $student)->with('success', 'Student created.');
    }

    public function show(Student $student)
    {
        $student->load(['institute', 'course']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load(['institute', 'course']);
        [$institutes, $courses] = $this->formOptions($student);

        return view('admin.students.edit', compact('student', 'institutes', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $this->validateStudent($request, $student->id);
        $validated['photo'] = $this->storePhoto($request) ?? $student->photo;

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student deleted.');
    }

    public function restore(int $student)
    {
        Student::onlyTrashed()->findOrFail($student)->restore();

        return redirect()->route('admin.students.index', ['trashed' => 1])->with('success', 'Student restored.');
    }

    protected function formOptions(?Student $student = null): array
    {
        $institutes = Institute::active()->orderBy('name')->get(['id', 'name']);
        $courses = StudentCourse::orderBy('title')->get(['id', 'title']);

        $instituteId = old('institute_id', $student?->institute_id);
        if ($instituteId && !$institutes->contains('id', (int) $instituteId)) {
            $institute = Institute::find($instituteId);
            if ($institute) {
                $institutes->push($institute);
                $institutes = $institutes->sortBy('name')->values();
            }
        }

        $courseId = old('student_course_id', $student?->student_course_id);
        if ($courseId && !$courses->contains('id', (int) $courseId)) {
            $course = StudentCourse::find($courseId);
            if ($course) {
                $courses->push($course);
                $courses = $courses->sortBy('title')->values();
            }
        }

        return [$institutes, $courses];
    }

    protected function validateStudent(Request $request, ?int $ignoreId = null): array
    {
        $request->merge([
            'gender' => $request->input('gender') ?: null,
            'examinee_type' => $request->input('examinee_type') ?: null,
            'institute_id' => $request->input('institute_id') ?: null,
            'student_course_id' => $request->input('student_course_id') ?: null,
            'cgpa' => $request->filled('cgpa') ? $request->input('cgpa') : null,
            'marks_written' => $request->filled('marks_written') ? $request->input('marks_written') : null,
            'marks_internship' => $request->filled('marks_internship') ? $request->input('marks_internship') : null,
            'marks_viva' => $request->filled('marks_viva') ? $request->input('marks_viva') : null,
            'marks_full' => $request->filled('marks_full') ? $request->input('marks_full') : null,
        ]);

        return $request->validate([
            'registration_number' => 'nullable|string|max:50',
            'roll_number' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'institute_id' => 'required|integer|exists:institutes,id',
            'student_course_id' => 'required|integer|exists:student_courses,id',
            'session' => 'nullable|string|max:100',
            'course_duration' => 'nullable|string|max:100',
            'student_thana' => 'nullable|string|max:100',
            'student_district' => 'nullable|string|max:100',
            'examinee_type' => 'nullable|in:regular,irregular',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'letter_grade' => 'nullable|string|max:5',
            'exam_month' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'marks_written' => 'nullable|integer|min:0|max:999',
            'marks_internship' => 'nullable|integer|min:0|max:999',
            'marks_viva' => 'nullable|integer|min:0|max:999',
            'marks_full' => 'nullable|integer|min:0|max:999',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'name.required' => 'Please enter the student name.',
            'name.max' => 'Student name cannot be longer than 255 characters.',
            'institute_id.required' => 'Please select an institute.',
            'institute_id.exists' => 'The selected institute is not valid.',
            'student_course_id.required' => 'Please select a course.',
            'student_course_id.exists' => 'The selected course is not valid.',
            'gender.in' => 'Please choose a valid gender.',
            'examinee_type.in' => 'Please choose a valid examinee type.',
            'cgpa.numeric' => 'CGPA must be a number.',
            'cgpa.min' => 'CGPA cannot be less than 0.',
            'cgpa.max' => 'CGPA cannot be greater than 4.',
            'issue_date.date' => 'Issue date must be a valid date.',
            'marks_written.integer' => 'Written marks must be a whole number.',
            'marks_internship.integer' => 'Internship marks must be a whole number.',
            'marks_viva.integer' => 'Viva marks must be a whole number.',
            'marks_full.integer' => 'Full marks must be a whole number.',
            'photo.image' => 'Photo must be an image file.',
            'photo.max' => 'Photo cannot be larger than 5 MB.',
        ]);
    }

    protected function storePhoto(Request $request): ?string
    {
        if (!$request->hasFile('photo')) {
            return null;
        }

        return ImageResizeService::resizeAndStore($request->file('photo'), 'students', 400, 500);
    }
}
