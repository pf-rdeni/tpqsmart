<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
$tpqData = $tpqData ?? [];
$generated_at = $generated_at ?? date('Y-m-d');
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
            margin-top: 60px;
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
        <?php if (!empty($tpqData['KopLembaga'])): ?>
            <!-- Kop Lembaga -->
            <?php
            $kopPath = FCPATH . 'uploads/kop/' . $tpqData['KopLembaga'];
            if (file_exists($kopPath)) {
                // Cek ukuran file untuk optimasi
                $fileSize = filesize($kopPath);
                if ($fileSize > 2 * 1024 * 1024) { // Jika file > 2MB
                    // Resize image untuk performa yang lebih baik
                    $imageInfo = getimagesize($kopPath);
                    if ($imageInfo) {
                        $width = $imageInfo[0];
                        $height = $imageInfo[1];
                        $mimeType = $imageInfo['mime'];

                        // Resize jika terlalu besar
                        if ($width > 1200) {
                            $newWidth = 1200;
                            $newHeight = ($height * $newWidth) / $width;

                            // Buat image resource berdasarkan tipe
                            switch ($mimeType) {
                                case 'image/jpeg':
                                    $source = imagecreatefromjpeg($kopPath);
                                    break;
                                case 'image/png':
                                    $source = imagecreatefrompng($kopPath);
                                    break;
                                case 'image/gif':
                                    $source = imagecreatefromgif($kopPath);
                                    break;
                                default:
                                    $source = false;
                            }

                            if ($source) {
                                $resized = imagecreatetruecolor($newWidth, $newHeight);
                                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                
                                ob_start();
                                switch ($mimeType) {
                                    case 'image/jpeg':
                                        imagejpeg($resized, null, 85);
                                        break;
                                    case 'image/png':
                                        imagepng($resized, null, 8);
                                        break;
                                    case 'image/gif':
                                        imagegif($resized);
                                        break;
                                }
                                $kopContent = ob_get_clean();
                                imagedestroy($source);
                                imagedestroy($resized);
                            } else {
                                $kopContent = file_get_contents($kopPath);
                            }
                        } else {
                            $kopContent = file_get_contents($kopPath);
                        }
                    } else {
                        $kopContent = file_get_contents($kopPath);
                    }
                } else {
                    $kopContent = file_get_contents($kopPath);
                }

                $kopBase64 = base64_encode($kopContent);
                $kopMimeType = mime_content_type($kopPath);
                echo '<img src="data:' . $kopMimeType . ';base64,' . $kopBase64 . '" alt="Kop Lembaga" style="width: 100%; max-width: 800px; height: auto; display: block; margin: 0 auto;">';
            } else {
                // Fallback jika file tidak ditemukan
                echo '<div class="header-title">' . esc($tpqData['NamaTpq'] ?? 'TPQ SMART SYSTEM') . '</div>';
                if (!empty($tpqData['AlamatTpq'])) {
                    echo '<div class="header-subtitle">' . esc($tpqData['AlamatTpq']) . '</div>';
                }
                if (!empty($tpqData['NoTelp'])) {
                    echo '<div class="header-subtitle">Telp: ' . esc($tpqData['NoTelp']) . '</div>';
                }
            }
            ?>
        <?php else: ?>
            <!-- Fallback jika tidak ada kop lembaga -->
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

        <p style="margin-top: 30px; text-align: justify;">
            Berdasarkan hasil evaluasi ujian munaqosah yang telah dilaksanakan pada tahun ajaran <?= esc($peserta['IdTahunAjaran'] ?? '-') ?>, oleh lembaga Formum Komunikasi Pendidikan Al-Quran (FKPQ) Kec. Seri Kuala Lobam Kab. Bintan Bintan, <?php if (!empty($peserta['KelulusanMet'])): ?>
                dengan ini dinyatakan bahwa <strong><?= esc($peserta['NamaSantri']) ?></strong> telah <strong>LULUS</strong> dalam ujian munaqosah dengan memperoleh total nilai bobot <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?>, yang telah memenuhi atau melebihi standar nilai minimal kelulusan sebesar <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?> sesuai dengan ketentuan yang berlaku.
            <?php else: ?>
                dengan ini dinyatakan bahwa <strong><?= esc($peserta['NamaSantri']) ?></strong> <strong>BELUM LULUS</strong> dalam ujian munaqosah dengan memperoleh total nilai bobot <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?>, yang belum memenuhi standar nilai minimal kelulusan sebesar <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?> sesuai dengan ketentuan yang berlaku.
            <?php endif; ?>
        </p>

        <p style="margin-top: 20px; text-align: justify;">
            Surat keterangan ini dibuat dengan sebenarnya berdasarkan data yang ada dalam sistem dan dapat digunakan untuk keperluan administrasi serta dokumentasi resmi terkait hasil ujian munaqosah yang bersangkutan.
        </p>
    </div>

    <div class="footer">
        <div class="footer-left">
            <!-- Kosong, sesuai format formal -->
        </div>
        <div class="footer-right">
            <div style="margin-top: 0; text-align: right;">
                <?= esc($tpqData['NamaTpq'] ?? 'TPQ') ?>, <?= esc(formatTanggalIndonesia($generated_at ?? date('Y-m-d'), 'd F Y')) ?>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                Mengetahui Kepala TPQ
            </div>
            <?php if (!empty($tpqData['NamaKepalaTpq'])): ?>
                <div class="signature-name" style="text-align: right; margin-top: 80px;">
                    <?= esc($tpqData['NamaKepalaTpq']) ?>
                </div>
            <?php else: ?>
                <div class="signature-name" style="text-align: right; margin-top: 80px;">
                    (_____________________)
                </div>
            <?php endif; ?>
        </div>
    </div>
    <br><br>
    <div class="print-date">
        Dicetak pada: <?= esc(formatTanggalIndonesia($generated_at ?? date('Y-m-d'), 'd F Y')) ?> <?= esc(date('H:i:s')) ?><br>
        Dokumen ini dihasilkan otomatis dari sistem http://tpqsmart.simpedis.com.
    </div>
</body>

</html>