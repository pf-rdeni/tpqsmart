<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
$tpqData = $tpqData ?? [];
$generated_at = $generated_at ?? date('Y-m-d');

// Normalisasi TypeUjian untuk display
$typeUjian = strtolower(trim($peserta['TypeUjian'] ?? 'munaqosah'));
if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
    $typeUjian = 'pra-munaqosah';
    $typeUjianLabel = 'Pra-Munaqosah';
    $typeUjianLabelLower = 'pra-munaqosah';
} else {
    $typeUjian = 'munaqosah';
    $typeUjianLabel = 'Munaqosah';
    $typeUjianLabelLower = 'munaqosah';
}
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

        body.has-kop-header {
            padding-top: 1px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header.has-kop {
            border-bottom: none;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .kop-lembaga {
            width: 100%;
            margin-bottom: 0;
        }

        .kop-lembaga img {
            width: 100%;
            max-width: 800px;
            height: auto;
            display: block;
            margin: 0 auto;
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

        .header.has-kop+.surat-title {
            margin-top: 5px;
        }

        .content {
            margin: 20px 0;
            line-height: 1.8;
            text-align: justify;
        }

        .content p {
            text-indent: 40px;
        }

        .data-section {
            margin: 20px 0;
            margin-left: 40px;
        }

        .data-row {
            margin: 8px 0;
            display: table;
            width: 100%;
        }

        .data-label {
            display: table-cell;
            width: 200px;
            font-weight: normal;
            vertical-align: top;
        }

        .data-value {
            display: table-cell;
            vertical-align: top;
            font-weight: bold;
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

<body class="<?= !empty($tpqData['KopLembaga']) ? 'has-kop-header' : '' ?>">
    <div class="header <?= !empty($tpqData['KopLembaga']) ? 'has-kop' : '' ?>">
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
                echo '<div class="kop-lembaga">';
                echo '<img src="data:' . $kopMimeType . ';base64,' . $kopBase64 . '" alt="Kop Lembaga">';
                echo '</div>';
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
        UJIAN <?= strtoupper($typeUjianLabel) ?>
    </div>

    <div class="content">
        <p>
            <?php
            $lembagaType = $tpqData['LembagaType'] ?? 'TPQ';
            $namaLembaga = $tpqData['NamaTpq'] ?? 'TPQ';

            if ($typeUjian === 'pra-munaqosah') {
                if ($lembagaType === 'MDA') {
                    $jabatanLembaga = 'Ketua MDTA';
                } else {
                    $jabatanLembaga = 'Kepala TPQ';
                }
            } else {
                // Untuk Munaqosah, menggunakan Ketua FKPQ
                $jabatanLembaga = 'Ketua FKPQ';
                $namaLembaga = 'Formum Komunikasi Pendidikan Al-Quran (FKPQ) Kec. Seri Kuala Lobam Kab. Bintan';
            }
            ?>
            Yang bertanda tangan di bawah ini, <?= esc($jabatanLembaga . ' ' . $namaLembaga) ?>, dengan ini menerangkan bahwa:
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
                <span class="data-value">: <?= esc(convertTahunAjaran($peserta['IdTahunAjaran'] ?? '')) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label"><?= esc(($tpqData['LembagaType'] ?? 'TPQ') === 'MDA' ? 'MDTA' : 'TPQ') ?></span>
                <span class="data-value">: <?= esc($peserta['NamaTpq'] ?? '-') ?></span>
            </div>
        </div>

        <p style="margin-top: 30px; text-align: justify;">
            Berdasarkan hasil evaluasi ujian <?= esc($typeUjianLabelLower) ?> yang telah dilaksanakan pada tahun ajaran <?= esc(convertTahunAjaran($peserta['IdTahunAjaran'] ?? '')) ?>, <?php if ($typeUjian === 'pra-munaqosah'): ?>
                <?php
                                                                                                                                                                                                $lembagaType = $tpqData['LembagaType'] ?? 'TPQ';
                                                                                                                                                                                                $namaLembaga = $tpqData['NamaTpq'] ?? 'TPQ/MDTA';
                                                                                                                                                                                                $prefixLembaga = ($lembagaType === 'MDA') ? 'MDTA' : 'TPQ';
                ?>
                oleh <?= esc($prefixLembaga . ' ' . $namaLembaga) ?>,
            <?php else: ?>
                oleh lembaga Formum Komunikasi Pendidikan Al-Quran (FKPQ) Kec. Seri Kuala Lobam Kab. Bintan Bintan,
            <?php endif; ?>
            <?php if (!empty($peserta['KelulusanMet'])): ?>
                dengan ini dinyatakan bahwa <strong><?= esc($peserta['NamaSantri']) ?></strong> telah <strong>LULUS</strong> dalam ujian <?= esc($typeUjianLabelLower) ?> yang telah memenuhi standar kelulusan sesuai dengan ketentuan yang berlaku.
            <?php else: ?>
                dengan ini ananda <strong><?= esc($peserta['NamaSantri']) ?></strong> dinyatakan <strong>Ujian Ulang</strong> dalam ujian <?= esc($typeUjianLabelLower) ?> yang belum memenuhi standar kelulusan sesuai dengan ketentuan yang berlaku, dan santri bersedia mengikuti proses belajar mengajar di TPQnya untuk mengikuti Munaqosah berikutnya.
            <?php endif; ?>
        </p>

        <p style="margin-top: 20px; text-align: justify;">
            Surat keterangan ini dibuat dengan sebenarnya berdasarkan data yang ada dalam sistem dan dapat digunakan untuk keperluan administrasi serta dokumentasi resmi terkait hasil ujian <?= esc($typeUjianLabelLower) ?> yang bersangkutan.
        </p>
    </div>

    <div class="footer">
        <div class="footer-left">
            <!-- Kosong, sesuai format formal -->
        </div>
        <div class="footer-right">
            <div style="margin-top: 0; text-align: center;">
                <?php
                // Normalisasi TypeUjian untuk pengecekan
                $typeUjian = strtolower(trim($peserta['TypeUjian'] ?? 'munaqosah'));
                if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
                    $typeUjian = 'pra-munaqosah';
                } else {
                    $typeUjian = 'munaqosah';
                }

                // Untuk Munaqosah, gunakan Kecamatan dari FKPQ, jika tidak ada gunakan AlamatTpq
                if ($typeUjian === 'munaqosah' && !empty($tpqData['KecamatanFkpq'])) {
                    $tempatTandaTangan = esc(toTitleCase($tpqData['KecamatanFkpq']));
                } else {
                    $tempatTandaTangan = esc(toTitleCase($tpqData['AlamatTpq'] ?? 'TPQ'));
                }
                ?>
                <?= $tempatTandaTangan ?>, <?= esc(formatTanggalIndonesia($generated_at ?? date('Y-m-d'), 'd F Y')) ?>
            </div>
        </div>
    </div>

    <div class="footer" style="margin-top: 10px;">
        <div class="footer-left">
            <!-- Kosong -->
        </div>
        <div class="footer-right" style="text-align: center;">
            <?php
            $lembagaType = $tpqData['LembagaType'] ?? 'TPQ';

            if ($typeUjian === 'pra-munaqosah') {
                if ($lembagaType === 'MDA') {
                    $jabatanTandaTangan = 'Kepala MDTA';
                    $namaTandaTangan = $tpqData['NamaKepalaTpq'] ?? '';
                } else {
                    $jabatanTandaTangan = 'Kepala TPQ';
                    $namaTandaTangan = $tpqData['NamaKepalaTpq'] ?? '';
                }
            } else {
                // Untuk Munaqosah, menggunakan Ketua FKPQ
                $jabatanTandaTangan = 'Ketua FKPQ';
                $namaTandaTangan = $tpqData['KetuaFkpq'] ?? ''; // Ambil dari KetuaFkpq
            }
            ?>
            Mengetahui <?= esc($jabatanTandaTangan) ?>
        </div>
    </div>

    <div class="footer" style="margin-top: 20px;">
        <div class="footer-left">
            <!-- Kosong -->
        </div>
        <div class="footer-right" style="text-align: center;">
            <?php if (!empty($namaTandaTangan)): ?>
                <div class="signature-name">
                    <?= esc($namaTandaTangan) ?>
                </div>
            <?php else: ?>
                <div class="signature-name">
                    (_____________________)
                </div>
            <?php endif; ?>
        </div>
    </div>
    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <div class="print-date" style="margin-top: 20px;">
        Dicetak pada: <?= esc(formatTanggalIndonesia($generated_at ?? date('Y-m-d'), 'd F Y')) ?> <?= esc(date('H:i:s')) ?>
        Dokumen ini dihasilkan otomatis dari sistem http://tpqsmart.simpedis.com.
    </div>
</body>

</html>