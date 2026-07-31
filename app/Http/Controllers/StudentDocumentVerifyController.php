<?php

namespace App\Http\Controllers;

use App\Models\Student;

class StudentDocumentVerifyController extends Controller
{
    public function __invoke(string $serialNumber)
    {
        $student = Student::with(['institute', 'course'])
            ->where('serial_number', $serialNumber)
            ->first();

        return view('student-documents.verify', compact('student', 'serialNumber'));
    }
}
