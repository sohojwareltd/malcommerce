@extends('student-documents.layout', ['orientation' => 'landscape', 'title' => 'Admit Card'])

@section('document')
<div class="doc-sheet">
    <div class="doc-content">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6mm;">
            <div style="flex:1;">
                <p class="doc-serial">Serial No. : {{ $student->serial_number }}</p>
                <table class="doc-field-table">
                    <tr><td>Institute Name</td><td>{{ $student->institute->name }}</td></tr>
                    <tr><td>Name of the Examinee</td><td><span class="doc-handwrite">{{ $student->name }}</span></td></tr>
                    <tr><td>Father's Name</td><td>{{ $student->father_name ?? '—' }}</td></tr>
                    <tr><td>Mother's Name</td><td>{{ $student->mother_name ?? '—' }}</td></tr>
                    <tr><td>Roll No</td><td>{{ $student->roll_number ?? '—' }}</td></tr>
                    <tr><td>Reg No</td><td>{{ $student->registration_number ?? '—' }}</td></tr>
                    <tr><td>Course</td><td>{{ $student->course->title }}</td></tr>
                    <tr><td>Session</td><td>{{ $student->session ?? '—' }}</td></tr>
                    <tr><td>Type of the Examinee</td><td>{{ $student->examinee_type_label }}</td></tr>
                </table>
            </div>
            <div style="text-align:center;width:32mm;">
                <div class="doc-photo-box" style="margin:0 auto 3mm;">
                    @if($student->photo_url)
                        <img src="{{ $student->photo_url }}" alt="">
                    @endif
                </div>
                <img src="{{ $student->qr_code_url }}" alt="QR" class="doc-qr" style="margin:0 auto;">
            </div>
        </div>
    </div>
</div>
@endsection
