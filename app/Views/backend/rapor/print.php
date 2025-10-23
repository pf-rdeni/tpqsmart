<?php
helper('nilai');



?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rapor Santri</title>
    <style>
        body {
            font-family: 'Arial Unicode MS', 'Times New Roman', 'DejaVu Sans', Arial, sans-serif;
            margin: 20px;
        }

        /* CSS untuk memastikan karakter Arab tidak terpisah */
        .arabic-text {
            font-family: 'Arial Unicode MS', 'Times New Roman', 'DejaVu Sans', serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
            white-space: nowrap;
            font-feature-settings: "liga" 1, "calt" 1;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Kelas bantu untuk teks Arab (RTL) */
        .arabic {
            font-family: 'Arial Unicode MS', 'Times New Roman', 'DejaVu Sans', serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
            font-feature-settings: "liga" 1, "calt" 1;
        }

        /* CSS khusus untuk kolom terbilang Arab */
        .terbilang-arabic {
            font-family: 'Arial Unicode MS', 'Times New Roman', 'DejaVu Sans', serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
            writing-mode: horizontal-tb;
            font-feature-settings: "liga" 1, "calt" 1;
            white-space: nowrap;
            word-spacing: normal;
            letter-spacing: normal;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 16px;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .data-santri {
            margin-bottom: 20px;
        }

        .data-santri table {
            width: 100%;
        }

        .data-santri td {
            padding: 3px;
        }

        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        .nilai-table th {
            background-color: #f0f0f0;
        }

        .semester-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        /* CSS untuk kop lembaga */
        .kop-lembaga {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop-lembaga img {
            max-width: 100%;
            width: 100%;
            max-height: 200px;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Media query untuk print */
        @media print {
            .kop-lembaga img {
                max-height: 150px;
            }
        }
    </style>
</head>

<body>
    <!-- Header Rapor -->
    <div class="header">
        <?php if (!empty($tpq['KopLembaga'])): ?>
            <!-- Kop Lembaga -->
            <div class="kop-lembaga">
                <?php
                $kopPath = FCPATH . 'uploads/kop/' . $tpq['KopLembaga'];
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

                                    // Output ke buffer
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
                                    $kopContent = ob_get_contents();
                                    ob_end_clean();

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
                    echo '<div style="text-align: center; padding: 20px; border: 1px dashed #ccc;">Kop lembaga tidak ditemukan</div>';
                }
                ?>
            </div>
            <h2>RAPOR SANTRI</h2>
            <p>Tahun Ajaran <?= $tahunAjaran ?> - Semester <?= $semester ?></p>
        <?php else: ?>
            <!-- Fallback jika tidak ada kop lembaga -->
            <h2>RAPOR SANTRI</h2>
            <p>Tahun Ajaran <?= $tahunAjaran ?> Semester <?= $semester ?></p>
        <?php endif; ?>
    </div>

    <!-- Data Santri -->
    <div class="data-santri">
        <table style="font-size: 12px;">
            <tr>
                <td width="150">Nama Santri</td>
                <td>: <?= htmlspecialchars(toTitleCase($santri['NamaSantri'])) ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: <?= $santri['IdSantri'] ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= $nilai[0]->NamaKelas ?? $santri['IdKelas'] ?></td>
            </tr>
        </table>
    </div>

    <!-- Nilai Semester -->
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Materi</th>
                <th width="15%">Kategori</th>
                <th width="10%">Nilai</th>
                <th width="10%">Huruf</th>
                <th width="10%">Rata Kelas</th>
                <th width="20%">Terbilang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($nilai as $n) :
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars(toTitleCase($n->NamaMateri)) ?></td>
                    <td><?= htmlspecialchars(toTitleCase($n->Kategori)) ?></td>
                    <td><?= konversiNilaiAngkaArabic($n->Nilai) ?></td>
                    <td><?= konversiHurufArabic(konversiNilaiHuruf($n->Nilai)) ?></td>
                    <td><?= konversiNilaiAngkaArabic(number_format($n->RataKelas, 2)) ?></td>
                    <td class="terbilang-arabic"><?= konversiTerbilangArabic($n->Nilai) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php
            // Hitung total dan rata-rata
            $total = 0;
            $totalRataKelas = 0;
            $count = count($nilai);
            foreach ($nilai as $n) {
                $total += floatval($n->Nilai);
                $totalRataKelas += floatval($n->RataKelas);
            }
            $rata_rata = $count > 0 ? $total / $count : 0;
            $rata_rata = number_format($rata_rata, 1);
            $rata_rata_kelas = $count > 0 ? $totalRataKelas / $count : 0;
            ?>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total Nilai:</td>
                <td><?= konversiNilaiAngkaArabic(number_format($total, 0)) ?></td>
                <td></td>
                <td></td>
                <td class="terbilang-arabic"><?= konversiTerbilangArabic($total) ?></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Rata-Rata:</td>
                <td><?= konversiNilaiAngkaArabic($rata_rata) ?></td>
                <td><?= konversiHurufArabic(konversiNilaiHuruf($rata_rata)) ?></td>
                <td><?= konversiNilaiAngkaArabic(number_format($rata_rata_kelas, 1)) ?></td>
                <td class="terbilang-arabic"><?= konversiTerbilangArabic($rata_rata) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- tabel catatan-->
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="30%">Catatan</th>
            </tr>
            <tr>
                <td>Ananda menunjukkan kemampuan akademik yang sangat baik di seluruh mata pelajaran. Ia selalu aktif, memiliki rasa ingin tahu yang tinggi, dan bertanggung jawab penuh dalam setiap tugas yang diberikan. Pertahankan terus semangat belajarnya!"</td>
            </tr>
        </thead>
    </table>
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="30%">Guru Pendamping</th>
            </tr>
            <?php if (!empty($santri['GuruPendamping'])): ?>
                <?php foreach ($santri['GuruPendamping'] as $key => $guru): ?>
                    <tr>
                        <td><?= $key + 1 ?>. <?= htmlspecialchars(toTitleCase($guru->Nama)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>-</td>
                </tr>
            <?php endif; ?>
        </thead>
    </table>
    <!-- Tanda Tangan Layout Gambar Tabel -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 50px; font-size: 12px; page-break-inside: avoid;">
        <tr>
            <td colspan="5" style="width: 50%; padding: 5px; text-align: right;"> Diberikan di Seri Kuala Lobam Tanggal: <?= $tanggal ?></td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">Kepala TPQ</td>
            <td style="width: 50%; padding: 5px;"></td>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">Wali Kelas</td>
        </tr>
        <tr>
            <td colspan="2" style="height: 50px; text-align: center;">
                <?php
                // Cari signature untuk kepala sekolah berdasarkan posisi
                $kepsekSignature = null;
                foreach ($signatures as $signature) {
                    if (isset($signature['NamaJabatan']) && $signature['NamaJabatan'] == 'Kepala TPQ' && isset($signature['QrCode']) && !empty($signature['QrCode'])) {
                        $kepsekSignature = $signature;
                        break;
                    }
                }

                if ($kepsekSignature && !empty($kepsekSignature['QrCode'])) {
                    $qrPath = FCPATH . 'uploads/qr/' . $kepsekSignature['QrCode'];
                    if (file_exists($qrPath)) {
                        $qrContent = file_get_contents($qrPath);
                        echo '<img src="data:image/svg+xml;base64,' . base64_encode($qrContent) . '" alt="QR Code Kepala Sekolah" style="width: 80px; height: 80px;">';
                    } else {
                        echo '<div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">QR Code<br>tidak ditemukan</div>';
                    }
                } else {
                    echo '<div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Belum ada<br>tanda tangan</div>';
                }
                ?>
            </td>
            <td></td>
            <td colspan="2" style="height: 50px; text-align: center;">
                <?php
                // Cari signature untuk wali kelas berdasarkan posisi
                $walasSignature = null;
                foreach ($signatures as $signature) {
                    if (isset($signature['NamaJabatan']) && $signature['NamaJabatan'] == 'Wali Kelas' && isset($signature['QrCode']) && !empty($signature['QrCode'])) {
                        $walasSignature = $signature;
                        break;
                    }
                }

                if ($walasSignature && !empty($walasSignature['QrCode'])) {
                    $qrPath = FCPATH . 'uploads/qr/' . $walasSignature['QrCode'];
                    if (file_exists($qrPath)) {
                        $qrContent = file_get_contents($qrPath);
                        echo '<img src="data:image/svg+xml;base64,' . base64_encode($qrContent) . '" alt="QR Code Wali Kelas" style="width: 80px; height: 80px;">';
                    } else {
                        echo '<div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">QR Code<br>tidak ditemukan</div>';
                    }
                } else {
                    echo '<div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Belum ada<br>tanda tangan</div>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 15px 5px;text-align: center;">( <?= htmlspecialchars(toTitleCase($tpq['KepalaSekolah'])) ?> )</td>
            <td></td>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">( <?= htmlspecialchars(toTitleCase($santri['WaliKelas'])) ?> )</td>
        </tr>
        <tr>
            <td colspan="5" style="padding: 15px 5px; text-align: center;">Mengetahui Orang Tua/Wali Santri</td>
        </tr>
        <tr>
            <td colspan="5" style="height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="5" style="padding: 15px 5px; text-align: center;">( <?= $santri['StatusAyah'] == 'Masih Hidup' ? htmlspecialchars(toTitleCase($santri['NamaAyah'])) : ($santri['NamaWali'] ? htmlspecialchars(toTitleCase($santri['NamaWali'])) : '...........................') ?> )</td>
        </tr>
    </table>
</body>

</html>