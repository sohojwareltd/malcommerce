@extends('student-documents.layout', ['orientation' => 'portrait', 'title' => 'Registration Card'])

@section('document')
<div class="doc-sheet">
    <div class="doc-content">
        <p class="doc-serial">Serial No : {{ $student->serial_number }}</p>

        <div class="doc-two-col">
            <div>
                <p style="text-align:center;font-weight:700;margin-bottom:3mm;font-family:Montserrat,sans-serif;">{{ $student->course->title }}</p>
                <table class="doc-field-table">
                    <tr><td>Registration Number</td><td>{{ $student->registration_number ?? '—' }}</td></tr>
                    <tr><td>Name of Student</td><td><span class="doc-handwrite">{{ $student->name }}</span></td></tr>
                    <tr><td>Father's Name</td><td>{{ $student->father_name ?? '—' }}</td></tr>
                    <tr><td>Mother's Name</td><td>{{ $student->mother_name ?? '—' }}</td></tr>
                    <tr><td>Gender</td><td>{{ $student->gender_label }}</td></tr>
                    <tr><td>Institute Name</td><td>{{ $student->institute->name }}</td></tr>
                    <tr><td>Student Thana</td><td>{{ $student->student_thana ?? '—' }}</td></tr>
                    <tr><td>Student District</td><td>{{ $student->student_district ?? '—' }}</td></tr>
                    <tr><td>Course Duration</td><td>{{ $student->course_duration ?? '—' }}</td></tr>
                    <tr><td>Session</td><td>{{ $student->session ?? '—' }}</td></tr>
                    @if($student->cgpa)
                    <tr><td>CGPA</td><td>{{ number_format($student->cgpa, 2) }} (Scale of 4.00)</td></tr>
                    @endif
                </table>
            </div>
            <div style="text-align:center;">
                <div class="doc-photo-box" style="margin:0 auto 3mm;">
                    @if($student->photo_url)
                        <img src="{{ $student->photo_url }}" alt="">
                    @endif
                </div>
                <img src="{{ $student->qr_code_url }}" alt="QR" class="doc-qr">
            </div>
        </div>
    </div>
</div>
@endsection
