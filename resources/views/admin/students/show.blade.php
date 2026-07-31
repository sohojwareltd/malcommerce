@extends('layouts.admin')

@section('title', $student->name)

@section('content')
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div class="flex items-start gap-4">
        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-neutral-100 shadow-md ring-1 ring-neutral-200">
            @if($student->photo_url)
                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
            @else
                <span class="text-2xl font-bold text-neutral-400">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 sm:text-3xl">{{ $student->name }}</h1>
            <p class="mt-1 text-sm text-neutral-500">Serial <span class="font-mono font-medium text-neutral-700">{{ $student->serial_number }}</span></p>
            <div class="mt-3 flex flex-wrap gap-2">
                @if($student->cgpa)
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                    CGPA {{ number_format($student->cgpa, 2) }}
                </span>
                @endif
                @if($student->letter_grade)
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                    Grade {{ $student->letter_grade }}
                </span>
                @endif
                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                    {{ $student->examinee_type_label }}
                </span>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('students.update')
        <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 shadow-sm hover:bg-neutral-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        @endcan
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center rounded-lg bg-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-300">
            ← Back
        </a>
    </div>
</div>

@can('downloadDocuments', $student)
<div class="mb-6 rounded-xl border border-primary/20 bg-gradient-to-r from-primary/5 to-primary/10 p-5 shadow-sm">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-primary">Download documents</h2>
    <p class="mt-1 text-sm text-neutral-600">Open any document and use Print / Save PDF from the browser.</p>
    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['route' => 'admin.students.documents.registration', 'label' => 'Registration card', 'desc' => 'Portrait', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'admin.students.documents.admit', 'label' => 'Admit card', 'desc' => 'Landscape', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['route' => 'admin.students.documents.certificate', 'label' => 'Certificate', 'desc' => 'Landscape', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['route' => 'admin.students.documents.marksheet', 'label' => 'Marksheet', 'desc' => 'Portrait', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as $doc)
        <a href="{{ route($doc['route'], $student) }}" target="_blank"
           class="group flex items-center gap-3 rounded-xl border border-white/80 bg-white p-4 shadow-sm transition hover:border-primary/30 hover:shadow-md">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $doc['icon'] }}"/></svg>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-neutral-900 group-hover:text-primary">{{ $doc['label'] }}</p>
                <p class="text-xs text-neutral-500">{{ $doc['desc'] }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endcan

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="xl:col-span-2 space-y-6">
        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Personal information</h2>
            </div>
            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">
                @foreach([
                    ['Registration no.', $student->registration_number],
                    ['Roll no.', $student->roll_number],
                    ['Father\'s name', $student->father_name],
                    ['Mother\'s name', $student->mother_name],
                    ['Gender', $student->gender ? $student->gender_label : null],
                    ['Thana', $student->student_thana],
                    ['District', $student->student_district],
                ] as [$label, $value])
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">{{ $label }}</dt>
                    <dd class="mt-1 font-medium text-neutral-900">{{ $value ?: '—' }}</dd>
                </div>
                @endforeach
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Academic details</h2>
            </div>
            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">
                @foreach([
                    ['Institute', $student->institute->name],
                    ['Course', $student->course->title],
                    ['Course duration', $student->course_duration],
                    ['Session', $student->session],
                    ['Exam month', $student->exam_month],
                    ['Issue date', $student->issue_date?->format('d F Y')],
                ] as [$label, $value])
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">{{ $label }}</dt>
                    <dd class="mt-1 font-medium text-neutral-900">{{ $value ?: '—' }}</dd>
                </div>
                @endforeach
            </div>
        </div>

        @if($student->cgpa || $student->marks_written !== null || $student->marks_internship !== null || $student->marks_viva !== null)
        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Result & marks</h2>
            </div>
            <div class="p-6">
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @if($student->cgpa)
                    <div class="rounded-xl bg-emerald-50 px-4 py-3 text-center">
                        <p class="text-xs font-semibold uppercase text-emerald-700">CGPA</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-900">{{ number_format($student->cgpa, 2) }}</p>
                    </div>
                    @endif
                    @if($student->letter_grade)
                    <div class="rounded-xl bg-amber-50 px-4 py-3 text-center">
                        <p class="text-xs font-semibold uppercase text-amber-700">Grade</p>
                        <p class="mt-1 text-2xl font-bold text-amber-900">{{ $student->letter_grade }}</p>
                    </div>
                    @endif
                    @if($student->marks_total !== null)
                    <div class="rounded-xl bg-blue-50 px-4 py-3 text-center">
                        <p class="text-xs font-semibold uppercase text-blue-700">Total marks</p>
                        <p class="mt-1 text-2xl font-bold text-blue-900">{{ $student->marks_total }}@if($student->marks_full)<span class="text-base font-medium text-blue-700">/{{ $student->marks_full }}</span>@endif</p>
                    </div>
                    @endif
                </div>
                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-neutral-50 text-left">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-neutral-600">Written</th>
                                <th class="px-4 py-3 font-semibold text-neutral-600">Internship</th>
                                <th class="px-4 py-3 font-semibold text-neutral-600">Viva</th>
                                <th class="px-4 py-3 font-semibold text-neutral-600">Total</th>
                                <th class="px-4 py-3 font-semibold text-neutral-600">Full mark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium">{{ $student->marks_written ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $student->marks_internship ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $student->marks_viva ?? '—' }}</td>
                                <td class="px-4 py-3 font-semibold text-primary">{{ $student->marks_total ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $student->marks_full ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        @if($student->photo_url)
        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Photo</h2>
            </div>
            <div class="flex justify-center p-6">
                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" class="max-h-80 w-full rounded-xl object-contain shadow-inner ring-1 ring-neutral-200">
            </div>
        </div>
        @endif

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Verification</h2>
            </div>
            <div class="p-6 text-center">
                <img src="{{ $student->qr_code_url }}" alt="QR code" class="mx-auto h-32 w-32 rounded-lg border bg-white p-2 shadow-sm">
                <p class="mt-4 text-xs text-neutral-500">Scan to verify this student's documents</p>
                <div class="mt-4 rounded-lg bg-neutral-50 p-3 text-left">
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Verify link</p>
                    <a href="{{ $student->verify_url }}" target="_blank" class="mt-1 block break-all text-sm font-medium text-primary hover:underline">{{ $student->verify_url }}</a>
                </div>
                <button type="button" onclick="navigator.clipboard.writeText(@js($student->verify_url)); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy link', 2000)"
                    class="mt-4 w-full rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
                    Copy link
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="border-b bg-neutral-50 px-6 py-4">
                <h2 class="font-bold text-neutral-900">Record info</h2>
            </div>
            <dl class="space-y-4 p-6 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Created</dt>
                    <dd class="mt-1 font-medium text-neutral-900">{{ $student->created_at->format('d M Y, h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Last updated</dt>
                    <dd class="mt-1 font-medium text-neutral-900">{{ $student->updated_at->format('d M Y, h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
