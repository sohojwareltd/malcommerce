@extends('layouts.app')

@section('title', 'Verify Student Document')

@section('content')
@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Shop'));
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">

<style>
    .sv-page {
        --sv-navy: #1a3a6b;
        --sv-navy-deep: #0f2748;
        --sv-gold: #c9a227;
        --sv-gold-soft: #e8d48a;
        --sv-ink: #1f2937;
        --sv-muted: #64748b;
        --sv-ok: #0f766e;
        --sv-ok-bg: #ecfdf8;
        --sv-bad: #b91c1c;
        --sv-bad-bg: #fef2f2;
        font-family: 'Libre Baskerville', Georgia, serif;
        color: var(--sv-ink);
        position: relative;
        isolation: isolate;
        overflow: hidden;
        padding: 2.5rem 1rem 4rem;
        background:
            radial-gradient(ellipse 80% 50% at 50% -10%, rgba(26, 58, 107, 0.18), transparent 55%),
            radial-gradient(ellipse 60% 40% at 100% 100%, rgba(201, 162, 39, 0.12), transparent 50%),
            linear-gradient(180deg, #e8eef5 0%, #f4f6f9 45%, #eef2f7 100%);
    }

    .sv-page::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        opacity: 0.35;
        background-image:
            linear-gradient(rgba(26, 58, 107, 0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(26, 58, 107, 0.04) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 20%, transparent 75%);
    }

    .sv-shell {
        max-width: 40rem;
        margin: 0 auto;
    }

    .sv-brand {
        text-align: center;
        margin-bottom: 1.75rem;
        animation: sv-rise 0.7s ease-out both;
    }

    .sv-brand-kicker {
        font-family: Montserrat, sans-serif;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--sv-navy);
        opacity: 0.75;
        margin-bottom: 0.4rem;
    }

    .sv-brand-name {
        font-family: Montserrat, sans-serif;
        font-size: clamp(1.65rem, 4vw, 2.15rem);
        font-weight: 700;
        color: var(--sv-navy-deep);
        letter-spacing: 0.04em;
        line-height: 1.15;
    }

    .sv-brand-sub {
        margin-top: 0.55rem;
        font-size: 0.95rem;
        color: var(--sv-muted);
        font-style: italic;
    }

    .sv-result {
        position: relative;
        background: #fffef9;
        border: 1px solid color-mix(in srgb, var(--sv-gold) 55%, #d1d5db);
        box-shadow:
            0 1px 0 color-mix(in srgb, var(--sv-gold) 40%, transparent),
            0 22px 50px rgba(15, 39, 72, 0.12);
        padding: 1.75rem 1.5rem 1.5rem;
        animation: sv-rise 0.75s 0.08s ease-out both;
    }

    .sv-result::before,
    .sv-result::after {
        content: '';
        position: absolute;
        inset: 0.55rem;
        pointer-events: none;
    }

    .sv-result::before {
        border: 2px solid var(--sv-gold);
        outline: 1px solid color-mix(in srgb, var(--sv-gold-soft) 80%, white);
        outline-offset: 3px;
    }

    .sv-result--invalid {
        border-color: color-mix(in srgb, var(--sv-bad) 35%, #d1d5db);
    }

    .sv-result--invalid::before {
        border-color: color-mix(in srgb, var(--sv-bad) 45%, #fca5a5);
        outline-color: #fecaca;
    }

    .sv-inner {
        position: relative;
        z-index: 1;
        padding: 0.35rem 0.25rem;
    }

    .sv-status {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.65rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.35rem;
        border-bottom: 1px solid color-mix(in srgb, var(--sv-gold) 35%, #e5e7eb);
    }

    .sv-seal {
        width: 4.25rem;
        height: 4.25rem;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-family: Montserrat, sans-serif;
        font-weight: 700;
        font-size: 0.65rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        line-height: 1.15;
        animation: sv-seal 0.9s 0.2s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .sv-seal--ok {
        color: var(--sv-ok);
        background:
            radial-gradient(circle at 30% 30%, #fff 0%, var(--sv-ok-bg) 55%),
            var(--sv-ok-bg);
        border: 2px solid color-mix(in srgb, var(--sv-ok) 55%, white);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--sv-ok) 12%, transparent);
    }

    .sv-seal--bad {
        color: var(--sv-bad);
        background: var(--sv-bad-bg);
        border: 2px solid color-mix(in srgb, var(--sv-bad) 40%, white);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--sv-bad) 10%, transparent);
    }

    .sv-seal i {
        font-size: 1.35rem;
        display: block;
        margin-bottom: 0.15rem;
    }

    .sv-status-title {
        font-family: Montserrat, sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--sv-navy);
    }

    .sv-status-title--bad {
        color: var(--sv-bad);
    }

    .sv-serial {
        font-family: Montserrat, sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.14em;
        color: var(--sv-muted);
        background: color-mix(in srgb, var(--sv-navy) 5%, white);
        border: 1px dashed color-mix(in srgb, var(--sv-navy) 18%, #d1d5db);
        padding: 0.4rem 0.85rem;
        display: inline-block;
    }

    .sv-serial span {
        color: var(--sv-navy);
        letter-spacing: 0.08em;
    }

    .sv-profile {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 480px) {
        .sv-profile {
            grid-template-columns: auto 1fr;
            align-items: start;
        }
    }

    .sv-photo {
        width: 6.5rem;
        height: 7.75rem;
        border: 1px solid #9ca3af;
        background: #f3f4f6;
        overflow: hidden;
        margin: 0 auto;
        box-shadow: 0 8px 18px rgba(15, 39, 72, 0.08);
    }

    .sv-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .sv-institute {
        text-align: center;
    }

    @media (min-width: 480px) {
        .sv-institute {
            text-align: left;
        }
        .sv-photo {
            margin: 0;
        }
    }

    .sv-institute-label {
        font-family: Montserrat, sans-serif;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--sv-muted);
        margin-bottom: 0.35rem;
    }

    .sv-institute-name {
        font-family: Montserrat, sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--sv-navy);
        letter-spacing: 0.03em;
        line-height: 1.25;
        text-transform: uppercase;
    }

    .sv-student-name {
        margin-top: 0.55rem;
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--sv-ink);
        line-height: 1.2;
    }

    .sv-course {
        margin-top: 0.35rem;
        font-size: 0.95rem;
        color: var(--sv-muted);
        font-style: italic;
    }

    .sv-fields {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
        border-top: 1px solid #e5e7eb;
    }

    @media (min-width: 480px) {
        .sv-fields {
            grid-template-columns: 1fr 1fr;
        }
    }

    .sv-field {
        display: grid;
        gap: 0.15rem;
        padding: 0.85rem 0.15rem;
        border-bottom: 1px dotted #d1d5db;
        animation: sv-fade 0.55s ease-out both;
    }

    .sv-field:nth-child(1) { animation-delay: 0.18s; }
    .sv-field:nth-child(2) { animation-delay: 0.24s; }
    .sv-field:nth-child(3) { animation-delay: 0.3s; }
    .sv-field:nth-child(4) { animation-delay: 0.36s; }
    .sv-field:nth-child(5) { animation-delay: 0.42s; }
    .sv-field:nth-child(6) { animation-delay: 0.48s; }

    .sv-field-label {
        font-family: Montserrat, sans-serif;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--sv-navy);
        opacity: 0.8;
    }

    .sv-field-value {
        font-size: 0.98rem;
        color: var(--sv-ink);
        word-break: break-word;
    }

    .sv-footnote {
        margin-top: 1.35rem;
        text-align: center;
        font-family: Montserrat, sans-serif;
        font-size: 0.72rem;
        line-height: 1.55;
        color: var(--sv-muted);
        letter-spacing: 0.02em;
    }

    .sv-invalid-copy {
        text-align: center;
        max-width: 22rem;
        margin: 0.25rem auto 0;
        color: var(--sv-muted);
        font-size: 0.95rem;
        line-height: 1.55;
    }

    @keyframes sv-rise {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes sv-seal {
        from { opacity: 0; transform: scale(0.72) rotate(-8deg); }
        to { opacity: 1; transform: scale(1) rotate(0deg); }
    }

    @keyframes sv-fade {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .sv-brand,
        .sv-result,
        .sv-seal,
        .sv-field {
            animation: none !important;
        }
    }
</style>

<div class="sv-page">
    <div class="sv-shell">
        <header class="sv-brand">
            <p class="sv-brand-kicker">Document authenticity</p>
            <h1 class="sv-brand-name">{{ $siteName }}</h1>
            <p class="sv-brand-sub">Official student document verification</p>
        </header>

        @if(!$student)
            <article class="sv-result sv-result--invalid" aria-live="polite">
                <div class="sv-inner">
                    <div class="sv-status">
                        <div class="sv-seal sv-seal--bad" aria-hidden="true">
                            <i class="fa-solid fa-xmark"></i>
                            Invalid
                        </div>
                        <h2 class="sv-status-title sv-status-title--bad">No record found</h2>
                        <p class="sv-serial">Serial <span>{{ $serialNumber }}</span></p>
                    </div>
                    <p class="sv-invalid-copy">
                        This serial number does not match any issued student document.
                        Check the code on the certificate and try again.
                    </p>
                    <p class="sv-footnote">Verification checked against the live student registry.</p>
                </div>
            </article>
        @else
            <article class="sv-result" aria-live="polite">
                <div class="sv-inner">
                    <div class="sv-status p-5">
                        <div class="sv-seal sv-seal--ok " aria-hidden="true">
                            <i class="fa-solid fa-check"></i>
                            Verified
                        </div>
                        <h2 class="sv-status-title">Valid student record</h2>
                        <p class="sv-serial">Serial <span>{{ $student->serial_number }}</span></p>
                    </div>

                    <div class="sv-profile">
                        @if($student->photo_url)
                            <div class="sv-photo">
                                <img src="{{ $student->photo_url }}" alt="Photo of {{ $student->name }}">
                            </div>
                        @endif
                        <div class="sv-institute">
                            <p class="sv-institute-label">Institute</p>
                            <p class="sv-institute-name">{{ $student->institute->name }}</p>
                            <p class="sv-student-name">{{ $student->name }}</p>
                            <p class="sv-course">{{ $student->course->title }}</p>
                        </div>
                    </div>

                    <dl class="sv-fields">
                        <div class="sv-field">
                            <dt class="sv-field-label">Roll number</dt>
                            <dd class="sv-field-value">{{ $student->roll_number ?? '—' }}</dd>
                        </div>
                        <div class="sv-field">
                            <dt class="sv-field-label">Registration</dt>
                            <dd class="sv-field-value">{{ $student->registration_number ?? '—' }}</dd>
                        </div>
                        @if($student->session)
                        <div class="sv-field">
                            <dt class="sv-field-label">Session</dt>
                            <dd class="sv-field-value">{{ $student->session }}</dd>
                        </div>
                        @endif
                        @if($student->cgpa)
                        <div class="sv-field">
                            <dt class="sv-field-label">CGPA</dt>
                            <dd class="sv-field-value">{{ number_format($student->cgpa, 2) }}@if($student->letter_grade) <span style="color: var(--sv-muted); font-size: 0.9em;">({{ $student->letter_grade }})</span>@endif</dd>
                        </div>
                        @elseif($student->letter_grade)
                        <div class="sv-field">
                            <dt class="sv-field-label">Grade</dt>
                            <dd class="sv-field-value">{{ $student->letter_grade }}</dd>
                        </div>
                        @endif
                        @if($student->exam_month)
                        <div class="sv-field">
                            <dt class="sv-field-label">Exam month</dt>
                            <dd class="sv-field-value">{{ $student->exam_month }}</dd>
                        </div>
                        @endif
                        @if($student->issue_date)
                        <div class="sv-field">
                            <dt class="sv-field-label">Issue date</dt>
                            <dd class="sv-field-value">{{ $student->issue_date->format('d M Y') }}</dd>
                        </div>
                        @endif
                    </dl>

                    <p class="sv-footnote">
                        This page confirms the document serial against the official registry.
                        Details match the printed student record.
                    </p>
                </div>
            </article>
        @endif
    </div>
</div>
@endsection
