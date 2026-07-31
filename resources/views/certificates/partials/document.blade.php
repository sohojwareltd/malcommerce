@php
    use App\Models\Setting;

    $colorPalette = json_decode(Setting::get('color_palette', '{}'), true);

    $logoUrl = null;
    $ogImageSetting = Setting::get('og_image');
    if ($ogImageSetting) {
        if (filter_var($ogImageSetting, FILTER_VALIDATE_URL)) {
            $logoUrl = $ogImageSetting;
        } else {
            $logoUrl = str_starts_with($ogImageSetting, '/')
                ? url($ogImageSetting)
                : url('/' . ltrim($ogImageSetting, '/'));
        }
    }

    $verifyUrl = route('certificates.verify', ['code' => $certificate->verification_code]);
    $issuedDate = $certificate->issued_at->format('F j, Y');
    $brandDomain = 'caregiver.com.bd';
    $companyInitial = mb_strtoupper(mb_substr(trim($brandDomain), 0, 1));
@endphp

<div id="certificate" class="certificate-sheet">
    <div class="certificate-accent-bar certificate-accent-bar-top" aria-hidden="true"></div>
    <div class="certificate-accent-bar certificate-accent-bar-bottom" aria-hidden="true"></div>

    <div class="certificate-watermark" aria-hidden="true">
        <span>{{ $brandDomain }}</span>
    </div>

    <div class="certificate-texture" aria-hidden="true"></div>

    <div class="certificate-frame certificate-frame-outer"></div>
    <div class="certificate-frame certificate-frame-middle"></div>
    <div class="certificate-frame certificate-frame-inner"></div>

    <div class="certificate-corner certificate-corner-tl" aria-hidden="true"></div>
    <div class="certificate-corner certificate-corner-tr" aria-hidden="true"></div>
    <div class="certificate-corner certificate-corner-bl" aria-hidden="true"></div>
    <div class="certificate-corner certificate-corner-br" aria-hidden="true"></div>

    <div class="certificate-content">
        <header class="certificate-header">
            <div class="certificate-brand">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $brandDomain }}" class="certificate-logo">
                @else
                    <div class="certificate-logo-fallback">{{ $companyInitial }}</div>
                @endif
                <div class="certificate-brand-text">
                    <p class="certificate-company-name">{{ $brandDomain }}</p>
                    <p class="certificate-company-tagline">Professional Training & Certification</p>
                </div>
            </div>
            <div class="certificate-seal" aria-hidden="true">
                <svg viewBox="0 0 120 120" class="certificate-seal-svg">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="60" cy="60" r="46" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="2 3"/>
                    <circle cx="60" cy="60" r="36" fill="currentColor" opacity="0.08" stroke="currentColor" stroke-width="1"/>
                    <path d="M60 18 L63 28 L73 28 L65 34 L68 44 L60 38 L52 44 L55 34 L47 28 L57 28 Z" fill="currentColor" opacity="0.85"/>
                    <text x="60" y="66" text-anchor="middle" class="certificate-seal-text">CERTIFIED</text>
                </svg>
            </div>
        </header>

        <div class="certificate-divider" aria-hidden="true">
            <span class="certificate-divider-line"></span>
            <span class="certificate-divider-ornament">
                <svg viewBox="0 0 120 24" aria-hidden="true">
                    <path d="M0 12 H42 M78 12 H120" stroke="currentColor" stroke-width="1"/>
                    <path d="M48 12 L54 6 L60 12 L54 18 Z" fill="currentColor"/>
                    <circle cx="60" cy="12" r="2.5" fill="currentColor"/>
                    <path d="M66 12 L72 6 L78 12 L72 18 Z" fill="currentColor"/>
                </svg>
            </span>
            <span class="certificate-divider-line"></span>
        </div>

        <p class="certificate-kicker">Certificate of Achievement</p>

        <div class="certificate-main">
            <p class="certificate-lead">This is to certify that</p>

            <h1 class="certificate-recipient">{{ $certificate->student_name }}</h1>
            <span class="certificate-recipient-rule" aria-hidden="true"></span>

            <p class="certificate-body">
                has successfully completed and passed the examination
            </p>

            <div class="certificate-exam-badge">
                <span class="certificate-exam-badge-label">Examination</span>
                <h2 class="certificate-exam-title">{{ $certificate->exam_title }}</h2>
            </div>
        </div>

        <footer class="certificate-footer">
            <div class="certificate-footer-block">
                <span class="certificate-footer-label">Date of issue</span>
                <span class="certificate-footer-value">{{ $issuedDate }}</span>
            </div>

            <div class="certificate-footer-block certificate-footer-signature">
                <span class="certificate-signature-script">{{ $brandDomain }}</span>
                <span class="certificate-signature-line"></span>
                <span class="certificate-footer-label">Authorized signatory</span>
            </div>

            <div class="certificate-footer-block certificate-footer-id">
                <span class="certificate-footer-label">Certificate ID</span>
                <span class="certificate-footer-value certificate-id">{{ $certificate->verification_code }}</span>
            </div>
        </footer>

        <p class="certificate-verify">
            Verify at <strong>{{ $brandDomain }}</strong> · <span>{{ $certificate->verification_code }}</span>
        </p>
    </div>
</div>
