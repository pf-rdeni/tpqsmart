<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran KTP dan Rekening BPR - <?= esc($guru['Nama'] ?? '') ?></title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 0;
            margin: 0;
            height: auto;
            overflow: visible;
        }

        .page {
            width: 210mm;
            padding: 10mm;
            margin: 0 auto;
            background: #fff;
            page-break-inside: avoid;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .info-guru {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .info-guru table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-guru table td {
            padding: 5px;
            font-size: 11px;
        }

        .info-guru table td:first-child {
            font-weight: bold;
            width: 30%;
        }

        .image-container {
            margin: 10px 0;
            text-align: center;
        }

        .image-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        .image-wrapper {
            border: 2px solid #000;
            padding: 5px;
            background: #fff;
            display: inline-block;
            max-width: 100%;
        }

        .image-wrapper img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* KTP Section - Bagian Atas */
        .ktp-section {
            margin-bottom: 20px;
        }

        .ktp-section .image-wrapper {
            max-width: 85mm; /* Ukuran KTP standar Indonesia */
        }

        .ktp-section .image-wrapper img {
            width: 85mm;
            height: auto;
        }

        /* BPR Section - Bagian Bawah */
        .bpr-section {
            margin-top: 20px;
        }

        .bpr-section .image-wrapper {
            max-width: 120mm; /* Lebar untuk BPR */
        }

        .bpr-section .image-wrapper img {
            width: 100%;
            max-width: 150mm;
            height: auto;
        }

        @media print {
            .page {
                margin: 0;
                padding: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h2>LAMPIRAN BERKAS</h2>
            <p>KTP dan Rekening Bank BPR</p>
        </div>

        <!-- KTP Section -->
        <div class="ktp-section">
            <div class="image-label">1. KARTU TANDA PENDUDUK (KTP)</div>
            <div class="image-container">
                <div class="image-wrapper">
                    <img src="<?= $ktpDataUri ?>" alt="KTP - <?= esc($guru['Nama'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- BPR Section -->
        <div class="bpr-section">
            <div class="image-label">2. BUKU REKENING BANK BPR</div>
            <div class="image-container">
                <div class="image-wrapper">
                    <img src="<?= $bprDataUri ?>" alt="Rekening BPR - <?= esc($guru['Nama'] ?? '') ?>">
                </div>
            </div>
        </div>

    </div>
</body>
</html>

