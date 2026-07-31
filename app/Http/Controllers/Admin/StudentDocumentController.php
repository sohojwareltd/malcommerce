<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentDocumentController extends Controller
{
    public function registration(Student $student)
    {
        return $this->render($student, 'registration', 'Registration Card', 'portrait');
    }

    public function admit(Student $student)
    {
        return $this->render($student, 'admit', 'Admit Card', 'landscape');
    }

    public function certificate(Student $student)
    {
        return $this->render($student, 'certificate', 'Certificate', 'landscape');
    }

    public function marksheet(Student $student)
    {
        return $this->render($student, 'marksheet', 'Exam Result Sheet', 'portrait');
    }

    protected function render(Student $student, string $template, string $title, string $orientation)
    {
        $this->authorize('downloadDocuments', $student);

        $student->load(['institute', 'course']);

        return view('student-documents.' . $template, compact('student', 'title', 'orientation'));
    }
}
