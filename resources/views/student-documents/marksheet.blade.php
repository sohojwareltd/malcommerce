@extends('student-documents.layout', ['orientation' => 'portrait', 'title' => 'Exam Result Sheet'])

@section('document')
<div class="doc-sheet">
    <div class="doc-content">
        <p class="doc-serial">Serial No : {{ $student->serial_number }}</p>

        <table class="doc-field-table">
            <tr><td>Name of Student</td><td><span class="doc-handwrite">{{ $student->name }}</span></td></tr>
            <tr><td>Father's Name</td><td>{{ $student->father_name ?? '—' }}</td></tr>
            <tr><td>Mother's Name</td><td>{{ $student->mother_name ?? '—' }}</td></tr>
            <tr><td>Roll</td><td>{{ $student->roll_number ?? '—' }}</td></tr>
            <tr><td>Reg. Number</td><td>{{ $student->registration_number ?? '—' }}</td></tr>
            <tr><td>Institute</td><td>{{ $student->institute->name }}</td></tr>
            <tr><td>Technology</td><td>{{ $student->course->title }}</td></tr>
            <tr><td>Course Duration</td><td>{{ $student->course_duration ?? '—' }}</td></tr>
            <tr><td>Session</td><td>{{ $student->session ?? '—' }}</td></tr>
            @if($student->cgpa)
            <tr><td>Final CGPA</td><td>CGPA {{ number_format($student->cgpa, 2) }} (IN THE SCALE OF 4.00)</td></tr>
            @endif
            @if($student->letter_grade)
            <tr><td>Letter Grade</td><td>{{ $student->letter_grade }}</td></tr>
            @endif
        </table>

        <table class="doc-marks-table">
            <thead>
                <tr>
                    <th>Written</th>
                    <th>Internship</th>
                    <th>Viva</th>
                    <th>Total</th>
                    <th>Full Mark</th>
                    <th>Letter Grade</th>
                    <th>CGPA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $student->marks_written ?? '—' }}</td>
                    <td>{{ $student->marks_internship ?? '—' }}</td>
                    <td>{{ $student->marks_viva ?? '—' }}</td>
                    <td>{{ $student->marks_total ?? '—' }}</td>
                    <td>{{ $student->marks_full ?? '—' }}</td>
                    <td>{{ $student->letter_grade ?? '—' }}</td>
                    <td>{{ $student->cgpa ? number_format($student->cgpa, 2) : '—' }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top:8mm;">
            <img src="{{ $student->qr_code_url }}" alt="QR" class="doc-qr">
        </div>
    </div>
</div>
@endsection
