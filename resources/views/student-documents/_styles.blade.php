<style>
    @import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;500;600;700&family=Great+Vibes&display=swap');

    :root {
        --doc-ink: #111827;
        --doc-muted: #374151;
        --doc-line: #9ca3af;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }

    .doc-body {
        font-family: 'Libre Baskerville', Georgia, serif;
        background: #f3f4f6;
        color: var(--doc-ink);
    }

    .doc-toolbar {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
    }

    .doc-btn {
        padding: 0.65rem 1.2rem;
        border-radius: 0.5rem;
        border: none;
        background: #111827;
        color: #fff;
        font-family: Montserrat, sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .doc-btn-secondary {
        background: #fff;
        color: #111827;
        border: 1px solid #d1d5db;
    }

    .doc-page-wrap {
        display: flex;
        justify-content: center;
        padding: 1.5rem 1rem 3rem;
    }

    .doc-sheet {
        position: relative;
        background: #fff;
        overflow: hidden;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .doc-portrait .doc-sheet {
        width: 210mm;
        min-height: 297mm;
    }

    .doc-landscape .doc-sheet {
        width: 297mm;
        min-height: 210mm;
    }

    /* Decorative chrome is printed on the physical paper — hide if present. */
    .doc-border,
    .doc-border-inner,
    .doc-watermark {
        display: none !important;
    }

    .doc-content {
        position: relative;
        z-index: 1;
        padding: 14mm 16mm;
    }

    .doc-title-text {
        font-family: Montserrat, sans-serif;
        font-size: 5mm;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--doc-ink);
        text-align: center;
        margin: 3mm 0 5mm;
    }

    .doc-serial {
        font-family: Montserrat, sans-serif;
        font-size: 3mm;
        color: var(--doc-muted);
        margin-bottom: 3mm;
    }

    .doc-field-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 3.6mm;
    }

    .doc-field-table td {
        padding: 1.8mm 2mm;
        vertical-align: top;
        border-bottom: 1px dotted #d1d5db;
    }

    .doc-field-table td:first-child {
        width: 38%;
        font-weight: 700;
        color: var(--doc-ink);
        font-family: Montserrat, sans-serif;
        font-size: 3.2mm;
    }

    .doc-handwrite {
        font-family: 'Great Vibes', cursive;
        font-size: 5.5mm;
        color: var(--doc-ink);
    }

    .doc-photo-box {
        width: 28mm;
        height: 34mm;
        border: 1px solid var(--doc-line);
        background: #fff;
        overflow: hidden;
        flex-shrink: 0;
    }

    .doc-photo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doc-qr {
        width: 22mm;
        height: 22mm;
        border: 1px solid #d1d5db;
    }

    .doc-marks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4mm;
        font-size: 3.2mm;
    }

    .doc-marks-table th,
    .doc-marks-table td {
        border: 1px solid #374151;
        padding: 2mm;
        text-align: center;
    }

    .doc-marks-table th {
        background: #fff;
        font-family: Montserrat, sans-serif;
        font-size: 2.8mm;
    }

    .doc-narrative {
        font-size: 4mm;
        line-height: 1.8;
        text-align: justify;
        margin: 4mm 0;
    }

    .doc-narrative .hl {
        font-family: 'Great Vibes', cursive;
        font-size: 6mm;
        color: var(--doc-ink);
    }

    .doc-two-col {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 6mm;
    }

    .doc-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 4mm;
        font-family: Montserrat, sans-serif;
        font-size: 3mm;
        color: var(--doc-muted);
        margin-bottom: 4mm;
        flex-wrap: wrap;
    }

    @media print {
        html, body {
            background: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print { display: none !important; }

        .doc-page-wrap {
            padding: 0 !important;
        }

        .doc-sheet {
            box-shadow: none !important;
            margin: 0 !important;
            background: #fff !important;
        }

        .doc-portrait .doc-sheet {
            width: 210mm !important;
            min-height: 297mm !important;
        }

        .doc-landscape .doc-sheet {
            width: 297mm !important;
            min-height: 210mm !important;
        }
    }
</style>
