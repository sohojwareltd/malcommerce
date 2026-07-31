<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Document' }} - {{ $student->name }}</title>
    @include('student-documents._styles')
    <style>
        @media print {
            @page {
                size: A4 {{ ($orientation ?? 'portrait') === 'landscape' ? 'landscape' : 'portrait' }};
                margin: 0;
            }
        }
    </style>
    @stack('doc-styles')
</head>
<body class="doc-body doc-{{ $orientation ?? 'portrait' }}">
    <div class="doc-toolbar no-print">
        <button type="button" onclick="window.print()" class="doc-btn">Print / Save PDF</button>
        <a href="{{ route('admin.students.show', $student) }}" class="doc-btn doc-btn-secondary">Back to student</a>
    </div>

    <div class="doc-page-wrap">
        @yield('document')
    </div>
</body>
</html>
