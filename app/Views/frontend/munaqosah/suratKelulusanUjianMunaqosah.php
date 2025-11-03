<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
$tpqData = $tpqData ?? [];
$generated_at = $generated_at ?? date('d F Y H:i:s');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kelulusan Munaqosah - <?= esc($peserta['NoPeserta'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header-logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .surat-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 20px 0;
            text-decoration: underline;
        }

        .content {
            margin: 20px 0;
            line-height: 1.8;
            text-align: justify;
        }

        .data-section {
            margin: 20px 0;
        }

        .data-row {
            margin: 8px 0;
            display: table;
            width: 100%;
        }

        .data-label {
            display: table-cell;
            width: 200px;
            font-weight: bold;
            vertical-align: top;
        }

        .data-value {
            display: table-cell;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background-color: #4472C4;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary-row {
            background-color: #D9E2F3 !important;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }

        .status-lulus {
            background-color: #28a745;
        }

        .status-belum {
            background-color: #dc3545;
        }

        .footer {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
        }

        .print-date {
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <?php if (!empty($tpqData['Logo'])): ?>
            <img src="<?= base_url('public/uploads/logo/' . $tpqData['Logo']) ?>" alt="Logo" class="header-logo">
        <?php endif; ?>
        <div class="header-title"><?= esc($tpqData['NamaTpq'] ?? 'TPQ SMART SYSTEM') ?></div>
        <div class="header-subtitle">
            <?php if (!empty($tpqData['AlamatTpq'])): ?>
                <?= esc($tpqData['AlamatTpq']) ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($tpqData['NoTelp'])): ?>
            <div class="header-subtitle">Telp: <?= esc($tpqData['NoTelp']) ?></div>
        <?php endif; ?>
    </div>

    <div class="surat-title">
        SURAT KETERANGAN KELULUSAN<br>
        UJIAN MUNAQOSAH
    </div>

    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, Kepala <?= esc($tpqData['NamaTpq'] ?? 'TPQ') ?>, dengan ini menerangkan bahwa:
        </p>

        <div class="data-section">
            <div class="data-row">
                <span class="data-label">Nama Santri</span>
                <span class="data-value">: <?= esc($peserta['NamaSantri'] ?? '-') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Nomor Peserta</span>
                <span class="data-value">: <?= esc($peserta['NoPeserta'] ?? '-') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Tahun Ajaran</span>
                <span class="data-value">: <?= esc($peserta['IdTahunAjaran'] ?? '-') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Type Ujian</span>
                <span class="data-value">: <?= esc($peserta['TypeUjian'] ?? '-') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">TPQ</span>
                <span class="data-value">: <?= esc($peserta['NamaTpq'] ?? '-') ?></span>
            </div>
        </div>

        <p style="margin-top: 20px;">
            Berdasarkan hasil evaluasi ujian munaqosah yang telah dilaksanakan, dengan rincian sebagai berikut:
        </p>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">KATEGORI</th>
                    <th style="width: 25%; text-align:center">NILAI</th>
                    <th style="width: 25%; text-align:center">NILAI BOBOT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalNilai = 0;
                $totalBobot = 0;
                ?>
                <?php foreach ($categoryDetails as $detail): ?>
                    <?php
                    $category = $detail['category'] ?? [];
                    $average = (float)($detail['average'] ?? 0);
                    $weighted = (float)($detail['weighted'] ?? 0);
                    $totalNilai += $average;
                    $totalBobot += $weighted;
                    ?>
                    <tr>
                        <td><?= esc($category['name'] ?? '-') ?></td>
                        <td style="text-align:center"><?= number_format($average, 1) ?></td>
                        <td style="text-align:center"><?= number_format($weighted, 1) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="summary-row">
                    <td><strong>JUMLAH</strong></td>
                    <td style="text-align:center"><strong><?= number_format($totalNilai, 1) ?></strong></td>
                    <td style="text-align:center"><strong><?= number_format($totalBobot, 1) ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="data-section" style="margin-top: 20px;">
            <div class="data-row">
                <span class="data-label">Total Nilai Bobot</span>
                <span class="data-value">: <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Nilai Minimal Kelulusan</span>
                <span class="data-value">: <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Status Kelulusan</span>
                <span class="data-value">: 
                    <?php $passed = !empty($peserta['KelulusanMet']); ?>
                    <span class="status-badge <?= $passed ? 'status-lulus' : 'status-belum' ?>">
                        <?= esc($peserta['KelulusanStatus'] ?? 'Belum Lulus') ?>
                    </span>
                </span>
            </div>
        </div>

        <p style="margin-top: 30px;">
            <?php if (!empty($peserta['KelulusanMet'])): ?>
                Dengan ini dinyatakan bahwa <strong><?= esc($peserta['NamaSantri']) ?></strong> telah <strong>LULUS</strong> dalam ujian munaqosah dengan total nilai bobot <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?> yang melebihi nilai minimal kelulusan <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?>.
            <?php else: ?>
                Dengan ini dinyatakan bahwa <strong><?= esc($peserta['NamaSantri']) ?></strong> <strong>BELUM LULUS</strong> dalam ujian munaqosah dengan total nilai bobot <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?> yang belum mencapai nilai minimal kelulusan <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?>.
            <?php endif; ?>
        </p>

        <p>
            Surat keterangan ini dibuat dengan sebenarnya dan dapat digunakan untuk keperluan yang sesuai.
        </p>
    </div>

    <div class="footer">
        <div class="footer-left">
            <div style="margin-top: 40px;">
                Mengetahui,<br>
                Kepala TPQ
            </div>
            <?php if (!empty($tpqData['NamaKepalaTpq'])): ?>
                <div class="signature-name">
                    <?= esc($tpqData['NamaKepalaTpq']) ?>
                </div>
            <?php else: ?>
                <div class="signature-name" style="margin-top: 60px;">
                    (_____________________)
                </div>
            <?php endif; ?>
        </div>
        <div class="footer-right">
            <div style="margin-top: 40px;">
                <?= esc($tpqData['NamaTpq'] ?? 'TPQ') ?>, <?= esc($generated_at) ?>
            </div>
            <div style="margin-top: 40px;">
                Sistem TPQSMART
            </div>
            <div class="signature-name" style="margin-top: 60px;">
                <br>
            </div>
        </div>
    </div>

    <div class="print-date">
        Dicetak pada: <?= esc($generated_at) ?><br>
        Dokumen ini dihasilkan otomatis dari sistem TPQSMART.
    </div>
</body>

</html>

