@extends('student-documents.layout', ['orientation' => 'landscape', 'title' => 'Certificate'])

@section('document')
<div class="doc-sheet">
    <div class="doc-content">
        <div class="doc-meta-row">
            <span>Serial No: {{ $student->serial_number }}</span>
            <span>Reg. No: {{ $student->registration_number ?? '—' }}</span>
            <span>Session: {{ $student->session ?? '—' }}</span>
        </div>

        <div style="display:grid;grid-template-columns:42mm 1fr;gap:6mm;align-items:start;">
            <div>
                <img src="{{ $student->qr_code_url }}" alt="QR" class="doc-qr" style="width:100%;height:auto;">
                @if($student->issue_date)
                <p style="margin-top:4mm;font-size:2.8mm;font-family:Montserrat,sans-serif;">
                    Issue Date: {{ $student->issue_date->format('d F Y') }}
                </p>
                @endif
            </div>

            <div>
                <p class="doc-narrative">
                    This is to certify that <span class="hl">{{ $student->name }}</span>
                    Son/daughter of <span class="hl">{{ $student->father_name ?? '—' }}</span> (Father) and
                    <span class="hl">{{ $student->mother_name ?? '—' }}</span> (Mother) of
                    <strong>{{ $student->institute->name }}</strong> bearing
                    <strong>Roll No. {{ $student->roll_number ?? '—' }}</strong> duly passed the
                    <strong>{{ $student->course_duration ?? '' }} {{ $student->course->title }}</strong>
                    Course Examination held in the month of <strong>{{ $student->exam_month ?? '—' }}</strong>
                    @if($student->cgpa)
                    and he/she secured <strong>CGPA {{ number_format($student->cgpa, 2) }}</strong> on the scale of 4.00
                    @endif
                    under the Education Program of {{ $student->institute->name }}.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
