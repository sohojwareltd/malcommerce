@extends('layouts.app')

@section('title', 'Certificate')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Great+Vibes&family=Montserrat:wght@400;500;600;700&family=Pinyon+Script&display=swap" rel="stylesheet">
@endpush

@php
    $colorPalette = json_decode(\App\Models\Setting::get('color_palette', '{}'), true);
    $primaryColor = $colorPalette['primary'] ?? '#1e3a5f';
    $secondaryColor = $colorPalette['secondary'] ?? '#64748b';
    $accentColor = $colorPalette['accent'] ?? '#c9a227';
@endphp

@section('content')
<div class="certificate-page">
    <div class="certificate-preview-wrap">
        @include('certificates.partials.document', ['certificate' => $certificate])
    </div>

    <div class="certificate-actions no-print">
        <button type="button" onclick="window.print()" class="certificate-btn certificate-btn-primary font-bangla">
            Print / Save PDF
        </button>
        <a href="{{ route('my-certificates.index') }}" class="certificate-btn certificate-btn-secondary font-bangla">
            My certificates
        </a>
        <a href="{{ route('certificates.verify', ['code' => $certificate->verification_code]) }}" class="certificate-btn certificate-btn-secondary">
            Verify online
        </a>
    </div>
</div>

@push('styles')
<style>
    .certificate-page {
        --cert-primary: {{ $primaryColor }};
        --cert-secondary: {{ $secondaryColor }};
        --cert-accent: {{ $accentColor }};
        --cert-paper: #faf8f4;
        --cert-paper-deep: #f3efe8;
        --cert-ink: #162033;
        --cert-muted: #5e6778;
        --cert-gold: {{ $accentColor }};
    }

    .certificate-preview-wrap {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem 1rem;
        background:
            radial-gradient(circle at 50% 0%, rgba(255,255,255,0.95), transparent 50%),
            linear-gradient(180deg, #e8edf3 0%, #d5dce6 100%);
    }

    .certificate-sheet {
        position: relative;
        width: 297mm;
        height: 210mm;
        min-height: 210mm;
        max-height: 210mm;
        background:
            linear-gradient(135deg, rgba(255,255,255,0.55) 0%, transparent 45%),
            linear-gradient(180deg, var(--cert-paper) 0%, var(--cert-paper-deep) 100%);
        color: var(--cert-ink);
        overflow: hidden;
        box-shadow:
            0 30px 60px rgba(15, 23, 42, 0.16),
            0 0 0 1px rgba(15, 23, 42, 0.05);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .certificate-accent-bar {
        position: absolute;
        left: 0;
        right: 0;
        height: 3mm;
        z-index: 4;
        background: linear-gradient(
            90deg,
            var(--cert-primary) 0%,
            color-mix(in srgb, var(--cert-primary) 70%, var(--cert-gold)) 50%,
            var(--cert-primary) 100%
        );
    }

    .certificate-accent-bar-top { top: 0; }
    .certificate-accent-bar-bottom { bottom: 0; }

    .certificate-texture {
        position: absolute;
        inset: 0;
        opacity: 0.35;
        background-image:
            radial-gradient(circle at 20% 15%, rgba(255,255,255,0.8) 0, transparent 28%),
            radial-gradient(circle at 80% 85%, rgba(255,255,255,0.65) 0, transparent 30%),
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 11px,
                rgba(22, 32, 51, 0.015) 11px,
                rgba(22, 32, 51, 0.015) 12px
            );
        pointer-events: none;
        z-index: 0;
    }

    .certificate-watermark {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        pointer-events: none;
        z-index: 0;
    }

    .certificate-watermark span {
        font-family: 'Montserrat', sans-serif;
        font-size: 15mm;
        font-weight: 700;
        letter-spacing: 0.12em;
        line-height: 1;
        text-transform: lowercase;
        color: var(--cert-primary);
        opacity: 0.035;
        transform: rotate(-8deg);
    }

    .certificate-frame {
        position: absolute;
        pointer-events: none;
        z-index: 1;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .certificate-frame-outer {
        inset: 9mm;
        border: 2px solid var(--cert-primary);
    }

    .certificate-frame-middle {
        inset: 11mm;
        border: 0.5px solid color-mix(in srgb, var(--cert-gold) 55%, white);
    }

    .certificate-frame-inner {
        inset: 13.5mm;
        border: 1px solid color-mix(in srgb, var(--cert-primary) 18%, white);
    }

    .certificate-corner {
        position: absolute;
        width: 16mm;
        height: 16mm;
        z-index: 2;
        pointer-events: none;
    }

    .certificate-corner::before,
    .certificate-corner::after {
        content: '';
        position: absolute;
        background: var(--cert-gold);
    }

    .certificate-corner-tl { top: 6.5mm; left: 6.5mm; }
    .certificate-corner-tr { top: 6.5mm; right: 6.5mm; }
    .certificate-corner-bl { bottom: 6.5mm; left: 6.5mm; }
    .certificate-corner-br { bottom: 6.5mm; right: 6.5mm; }

    .certificate-corner-tl::before,
    .certificate-corner-tr::before,
    .certificate-corner-bl::before,
    .certificate-corner-br::before { width: 100%; height: 2px; }

    .certificate-corner-tl::after,
    .certificate-corner-tr::after,
    .certificate-corner-bl::after,
    .certificate-corner-br::after { width: 2px; height: 100%; }

    .certificate-corner-tl::before { top: 0; left: 0; }
    .certificate-corner-tl::after { top: 0; left: 0; }
    .certificate-corner-tr::before { top: 0; right: 0; }
    .certificate-corner-tr::after { top: 0; right: 0; }
    .certificate-corner-bl::before { bottom: 0; left: 0; }
    .certificate-corner-bl::after { bottom: 0; left: 0; }
    .certificate-corner-br::before { bottom: 0; right: 0; }
    .certificate-corner-br::after { bottom: 0; right: 0; }

    .certificate-content {
        position: relative;
        z-index: 3;
        box-sizing: border-box;
        height: 100%;
        min-height: 210mm;
        padding: 18mm 20mm 14mm;
        display: grid;
        grid-template-rows: auto auto auto minmax(0, 1fr) auto auto;
        align-items: stretch;
        text-align: center;
    }

    .certificate-header {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 3mm;
        border-bottom: 1px solid color-mix(in srgb, var(--cert-primary) 10%, transparent);
    }

    .certificate-brand {
        display: flex;
        align-items: center;
        gap: 4mm;
        text-align: left;
        min-width: 0;
    }

    .certificate-logo {
        width: 15mm;
        height: 15mm;
        object-fit: contain;
        flex-shrink: 0;
    }

    .certificate-logo-fallback {
        width: 15mm;
        height: 15mm;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: linear-gradient(145deg, var(--cert-primary), color-mix(in srgb, var(--cert-primary) 75%, black));
        color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 6.5mm;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(22, 32, 51, 0.15);
    }

    .certificate-company-name {
        font-family: 'Montserrat', sans-serif;
        font-size: 4mm;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: lowercase;
        color: var(--cert-primary);
        line-height: 1.2;
    }

    .certificate-company-tagline {
        margin-top: 0.8mm;
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 3.2mm;
        font-style: italic;
        color: var(--cert-muted);
        line-height: 1.3;
    }

    .certificate-seal {
        width: 20mm;
        height: 20mm;
        color: color-mix(in srgb, var(--cert-gold) 80%, var(--cert-primary));
        flex-shrink: 0;
    }

    .certificate-seal-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 1px 2px rgba(22, 32, 51, 0.12));
    }

    .certificate-seal-text {
        font-family: 'Montserrat', sans-serif;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 0.14em;
        fill: currentColor;
    }

    .certificate-divider {
        display: flex;
        align-items: center;
        gap: 3mm;
        width: 78%;
        margin: 3mm auto 1mm;
    }

    .certificate-divider-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--cert-gold) 55%, transparent), transparent);
    }

    .certificate-divider-ornament {
        width: 28mm;
        color: var(--cert-gold);
    }

    .certificate-divider-ornament svg {
        width: 100%;
        height: auto;
        display: block;
    }

    .certificate-kicker {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.8mm;
        font-weight: 600;
        letter-spacing: 0.38em;
        text-transform: uppercase;
        color: var(--cert-primary);
        margin: 1mm 0 0;
    }

    .certificate-main {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 0;
        padding: 1mm 0;
    }

    .certificate-lead,
    .certificate-body {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 5.2mm;
        font-style: italic;
        color: var(--cert-muted);
        line-height: 1.45;
        margin: 0;
    }

    .certificate-recipient {
        font-family: 'Great Vibes', 'Pinyon Script', 'Noto Sans Bengali', cursive;
        font-size: 17mm;
        font-weight: 400;
        line-height: 1.1;
        color: var(--cert-primary);
        margin: 3mm 0 1.5mm;
        max-width: 90%;
        padding: 0 4mm;
        word-break: break-word;
    }

    .certificate-recipient-rule {
        display: block;
        width: 52mm;
        height: 2px;
        margin: 0 auto 3mm;
        background: linear-gradient(
            90deg,
            transparent,
            var(--cert-gold) 18%,
            var(--cert-gold) 82%,
            transparent
        );
        position: relative;
    }

    .certificate-recipient-rule::after {
        content: '✦';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -52%);
        font-size: 3.5mm;
        color: var(--cert-gold);
        background: var(--cert-paper);
        padding: 0 2mm;
        line-height: 1;
    }

    .certificate-exam-badge {
        margin-top: 2mm;
        padding: 3mm 8mm;
        border: 1px solid color-mix(in srgb, var(--cert-primary) 14%, white);
        border-radius: 2mm;
        background: color-mix(in srgb, white 72%, var(--cert-paper));
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.85);
        max-width: 88%;
    }

    .certificate-exam-badge-label {
        display: block;
        font-family: 'Montserrat', sans-serif;
        font-size: 2.4mm;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--cert-muted);
        margin-bottom: 1mm;
    }

    .certificate-exam-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 7mm;
        font-weight: 700;
        color: var(--cert-ink);
        margin: 0;
        line-height: 1.25;
    }

    .certificate-footer {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        gap: 8mm;
        align-items: end;
        margin-top: 1mm;
        padding-top: 3mm;
        border-top: 1px solid color-mix(in srgb, var(--cert-primary) 12%, transparent);
    }

    .certificate-footer-block {
        display: flex;
        flex-direction: column;
        gap: 1.2mm;
    }

    .certificate-footer-signature {
        align-items: center;
    }

    .certificate-footer-id {
        align-items: flex-end;
        text-align: right;
    }

    .certificate-footer-label {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.4mm;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--cert-muted);
    }

    .certificate-footer-value {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 4.1mm;
        font-weight: 600;
        color: var(--cert-ink);
    }

    .certificate-id {
        font-family: 'Montserrat', sans-serif;
        font-size: 3mm;
        letter-spacing: 0.1em;
    }

    .certificate-signature-script {
        font-family: 'Pinyon Script', 'Great Vibes', cursive;
        font-size: 7mm;
        line-height: 1;
        color: var(--cert-primary);
        margin-bottom: 1mm;
    }

    .certificate-signature-line {
        display: block;
        width: 40mm;
        height: 1px;
        background: var(--cert-ink);
        margin-bottom: 1.5mm;
        opacity: 0.45;
    }

    .certificate-verify {
        margin-top: 1.5mm;
        font-family: 'Montserrat', sans-serif;
        font-size: 2.3mm;
        letter-spacing: 0.06em;
        color: var(--cert-muted);
    }

    .certificate-verify strong {
        color: var(--cert-primary);
        font-weight: 600;
    }

    .certificate-verify span {
        font-family: 'Montserrat', monospace;
        letter-spacing: 0.08em;
        color: color-mix(in srgb, var(--cert-primary) 75%, black);
    }

    .certificate-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.5rem 1rem 3rem;
    }

    .certificate-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem 1.25rem;
        border-radius: 0.65rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .certificate-btn-primary {
        background: var(--cert-primary);
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .certificate-btn-secondary {
        background: #fff;
        color: var(--cert-primary);
        border: 1px solid color-mix(in srgb, var(--cert-primary) 20%, white);
    }

    .certificate-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    @media (max-width: 1100px) {
        .certificate-preview-wrap { overflow-x: auto; }
        .certificate-sheet {
            transform: scale(0.72);
            transform-origin: top center;
            margin-bottom: -58mm;
        }
    }

    @media (max-width: 768px) {
        .certificate-sheet {
            transform: scale(0.52);
            margin-bottom: -100mm;
        }
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            width: 297mm !important;
            height: 210mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow: hidden !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print,
        body > .min-h-full > header,
        body > .min-h-full > footer,
        body > .min-h-full > div.bg-primary,
        body > a.fixed,
        #scrollToTopBtn {
            display: none !important;
            visibility: hidden !important;
        }

        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 297mm !important;
            height: 210mm !important;
        }

        .certificate-page,
        .certificate-preview-wrap {
            margin: 0 !important;
            padding: 0 !important;
            width: 297mm !important;
            height: 210mm !important;
            background: #fff !important;
        }

        .certificate-preview-wrap {
            display: block !important;
        }

        .certificate-sheet {
            position: relative !important;
            width: 297mm !important;
            height: 210mm !important;
            min-height: 210mm !important;
            max-height: 210mm !important;
            margin: 0 !important;
            transform: none !important;
            box-shadow: none !important;
            overflow: hidden !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .certificate-content {
            display: grid !important;
            grid-template-rows: auto auto auto minmax(0, 1fr) auto auto !important;
            height: 210mm !important;
            min-height: 210mm !important;
            max-height: 210mm !important;
            padding: 18mm 20mm 14mm !important;
            box-sizing: border-box !important;
        }

        .certificate-main {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
        }
    }
</style>
@endpush
@endsection
