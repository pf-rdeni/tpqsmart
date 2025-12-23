<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
$tpqData = $tpqData ?? [];

// Normalisasi TypeUjian untuk display
$typeUjian = strtolower(trim($peserta['TypeUjian'] ?? 'munaqosah'));
if ($typeUjian === 'pramunaqsah' || $typeUjian === 'pra-munaqosah') {
    $typeUjian = 'pra-munaqosah';
    $typeUjianLabel = 'Pra-Munaqosah';
} else {
    $typeUjian = 'munaqosah';
    $typeUjianLabel = 'Munaqosah';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai Kelulusan <?= esc($peserta['NoPeserta'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2,
        h3 {
            margin: 0 0 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #4472C4;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #D9E2F3;
        }

        .summary-row {
            background-color: #D9E2F3 !important;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }

        .badge-lulus {
            background-color: #28a745;
        }

        .badge-belum {
            background-color: #dc3545;
        }

        .nilai-0 {
            background-color: #f8d7da !important;
            color: #dc3545;
            font-weight: bold;
        }

        .meta-table td {
            border: none;
            padding: 4px 0;
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

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
        }

        .print-date {
            margin-top: 20px;
            font-size: 11px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
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
            }
            ?>
        <?php endif; ?>
        <h2>Laporan Nilai Kelulusan</h2>
        <h3><?= esc($peserta['NamaSantri'] ?? '-') ?></h3>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong><?= esc(($tpqData['LembagaType'] ?? 'TPQ') === 'MDA' ? 'MDTA' : 'TPQ') ?></strong></td>
            <td>: <?= esc($peserta['NamaTpq'] ?? '-') ?></td>
            <td><strong>Tahun Ajaran</strong></td>
            <td>: <?= esc(convertTahunAjaran($peserta['IdTahunAjaran'] ?? '')) ?></td>
        </tr>
        <tr>
            <td><strong>Type Ujian</strong></td>
            <td>: <?= esc($peserta['TypeUjian'] ?? '-') ?></td>
            <td><strong>Status</strong></td>
            <td>:
                <?php $passed = !empty($peserta['KelulusanMet']); ?>
                <span class="badge <?= $passed ? 'badge-lulus' : 'badge-belum' ?>">
                    <?= esc($peserta['KelulusanStatus'] ?? '-') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td><strong>Total Nilai Bobot</strong></td>
            <td>: <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?></td>
            <td><strong>Nilai Minimal Kelulusan</strong></td>
            <td>: <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?></td>
        </tr>
        <tr>
            <td><strong>Selisih</strong></td>
            <td>: <?= number_format((float)($peserta['KelulusanDifference'] ?? 0), 2) ?></td>
            <td><strong>No Peserta</strong></td>
            <td>: <?= esc($peserta['NoPeserta'] ?? '-') ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>KATEGORI</th>
                <th>MATERI</th>
                <th style="text-align:center">NILAI</th>
                <th style="text-align:center">NILAI BOBOT</th>
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
                $juriScores = $detail['juri_scores'] ?? [];
                $materis = $detail['materi'] ?? [];

                // Gunakan average yang sudah dihitung di controller (sudah memfilter berdasarkan maxJuri)
                $average = (float)($detail['average'] ?? 0);

                // Jika average masih 0, coba hitung dari juri_scores yang sudah difilter
                if ($average == 0 && !empty($juriScores)) {
                    $validScores = array_filter($juriScores, function ($score) {
                        return isset($score['Nilai']) && (float)$score['Nilai'] > 0;
                    });
                    $validScores = array_values($validScores);

                    if (count($validScores) === 1) {
                        $average = (float)$validScores[0]['Nilai'];
                    } elseif (count($validScores) > 1) {
                        $sum = array_sum(array_column($validScores, 'Nilai'));
                        $average = round($sum / count($validScores), 2);
                    }
                }

                $weighted = (float)($detail['weighted'] ?? 0);
                $totalNilai += $average;
                $totalBobot += $weighted;

                // Ambil materi pertama, atau gabungkan jika ada beberapa
                $materiName = '-';
                if (!empty($materis)) {
                    $materi = $materis[0];
                    $materiName = esc($materi['NamaMateri'] ?? '-', 'html');
                    // Hapus link ayat untuk kesederhanaan
                }

                // Jika kategori adalah TULIS AL-QURAN dan materi kosong, set default
                $categoryName = $category['name'] ?? '';
                if (stripos($categoryName, 'TULIS') !== false && stripos($categoryName, 'AL-QURAN') !== false && $materiName === '-') {
                    $materiName = 'SURAH AL-FATIHAH';
                }
                ?>
                <tr>
                    <td><?= esc($category['name'] ?? '-') ?></td>
                    <td><?= $materiName ?></td>
                    <td style="text-align:center" class="<?= $average == 0 ? 'nilai-0' : '' ?>"><?= number_format($average, 1) ?></td>
                    <td style="text-align:center" class="<?= $weighted == 0 ? 'nilai-0' : '' ?>"><?= number_format($weighted, 1) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="summary-row">
                <td></td>
                <td><strong>JUMLAH</strong></td>
                <td style="text-align:center"><strong><?= number_format($totalNilai, 1) ?></strong></td>
                <td style="text-align:center"><strong><?= number_format($totalBobot, 1) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-left">
            <!-- Kosong, sesuai format formal -->
        </div>
        <div class="footer-right">
            <div style="margin-top: 0; text-align: right;">
                <?php
                // Untuk Munaqosah, gunakan Kecamatan dari FKPQ, jika tidak ada gunakan AlamatTpq
                if ($typeUjian === 'munaqosah' && !empty($tpqData['KecamatanFkpq'])) {
                    $tempatTandaTangan = esc(toTitleCase($tpqData['KecamatanFkpq']));
                } else {
                    $tempatTandaTangan = esc(toTitleCase($tpqData['AlamatTpq'] ?? 'TPQ'));
                }
                ?>
                <?= $tempatTandaTangan ?>, <?= esc(formatTanggalIndonesia($generated_at ?? date('Y-m-d'), 'd F Y')) ?>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                <?php
                $lembagaType = $tpqData['LembagaType'] ?? 'TPQ';

                if ($typeUjian === 'pra-munaqosah') {
                    if ($lembagaType === 'MDA') {
                        $jabatanTandaTangan = 'Ketua MDTA';
                        $namaTandaTangan = $tpqData['KepalaSekolah'] ?? '';
                    } else {
                        $jabatanTandaTangan = 'Kepala TPQ';
                        $namaTandaTangan = $tpqData['KepalaSekolah'] ?? '';
                    }
                } else {
                    // Untuk Munaqosah, menggunakan Ketua FKPQ
                    $jabatanTandaTangan = 'Ketua FKPQ';
                    $namaTandaTangan = $tpqData['KetuaFkpq'] ?? ''; // Ambil dari KetuaFkpq
                }
                ?>
                Mengetahui <?= esc($jabatanTandaTangan) ?>
            </div>
            <?php if (!empty($namaTandaTangan)): ?>
                <div class="signature-name" style="text-align: right; margin-top: 80px;">
                    <?= esc($namaTandaTangan) ?>
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