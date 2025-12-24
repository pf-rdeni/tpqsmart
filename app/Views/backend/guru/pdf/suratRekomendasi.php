<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi Guru TPQ</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        body.has-kop-header {
            padding-top: 0.2px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        .header .organization {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .header .organization-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .address {
            font-size: 10px;
            margin-bottom: 15px;
        }

        .letter-info {
            margin-bottom: 20px;
        }

        .letter-number {
            font-size: 11px;
            margin-bottom: 20px;
        }

        .letter-subject {
            font-size: 11px;
            margin-bottom: 20px;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            margin: 8px 0;
            text-align: justify;
        }

        .greeting {
            margin: 15px 0;
        }

        .data-row {
            margin: 5px 0;
        }

        .data-label {
            display: inline-block;
            width: 120px;
        }

        .data-value {
            display: inline-block;
        }

        .closing {
            margin-top: 15px;
        }

        .footer-info {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 6px;
            color: #666;
            text-align: center;
            line-height: 1.5;
        }

        @page {
            margin-top: 8mm;
            margin-bottom: 5mm;
            margin-left: 20mm;
            margin-right: 10mm;
        }
    </style>
</head>

<body class="<?= !empty($fkpqData['KopLembaga']) ? 'has-kop-header' : '' ?>">
    <div class="header <?= !empty($fkpqData['KopLembaga']) ? 'has-kop' : '' ?>">
        <?php if (!empty($fkpqData['KopLembaga'])): ?>
            <!-- Kop Lembaga FKPQ -->
            <?php
            $kopPath = FCPATH . 'uploads/kop/' . $fkpqData['KopLembaga'];
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
                                $imageData = ob_get_clean();
                                imagedestroy($source);
                                imagedestroy($resized);

                                echo '<div class="kop-lembaga"><img src="data:' . $mimeType . ';base64,' . base64_encode($imageData) . '" alt="Kop FKPQ"></div>';
                            } else {
                                // Jika gagal resize, gunakan file asli
                                $imageData = file_get_contents($kopPath);
                                $mime = mime_content_type($kopPath);
                                echo '<div class="kop-lembaga"><img src="data:' . $mime . ';base64,' . base64_encode($imageData) . '" alt="Kop FKPQ"></div>';
                            }
                        } else {
                            // File tidak terlalu besar, gunakan langsung
                            $imageData = file_get_contents($kopPath);
                            $mime = mime_content_type($kopPath);
                            echo '<div class="kop-lembaga"><img src="data:' . $mime . ';base64,' . base64_encode($imageData) . '" alt="Kop FKPQ"></div>';
                        }
                    } else {
                        // Jika getimagesize gagal, coba gunakan file langsung
                        $imageData = file_get_contents($kopPath);
                        $mime = mime_content_type($kopPath);
                        echo '<div class="kop-lembaga"><img src="data:' . $mime . ';base64,' . base64_encode($imageData) . '" alt="Kop FKPQ"></div>';
                    }
                } else {
                    // File kecil, gunakan langsung
                    $imageData = file_get_contents($kopPath);
                    $mime = mime_content_type($kopPath);
                    echo '<div class="kop-lembaga"><img src="data:' . $mime . ';base64,' . base64_encode($imageData) . '" alt="Kop FKPQ"></div>';
                }
            } else {
                // Fallback jika file tidak ditemukan
                echo '<div class="organization-name">' . esc($fkpqData['NamaFkpq'] ?? 'FORUM KOMUNIKASI PENDIDIKAN AL-QUR\'AN (FKPQ)') . '</div>';
                if (!empty($fkpqData['Alamat'])) {
                    echo '<div class="address">' . esc($fkpqData['Alamat']) . '</div>';
                }
            }
            ?>
        <?php else: ?>
            <!-- Fallback jika tidak ada kop lembaga -->
            <div class="organization">PERUMA KOMUNIKASI PENDIDIKAN AL-QUR'AN KABUPATEN BINTAN - SERI KUALA LOBAM</div>
            <div class="organization-name">PENGURUS ANAK CABANG</div>
            <div class="organization-name">FORUM KOMUNIKASI PENDIDIKAN AL - QUR'AN (PAC FKPQ) KECAMATAN SERI KUALA LOBAM</div>
            <div class="address">
                Sekretariat: Masjid AL-HIKMAH Teluk Sasah HP. 08127071325 Email: fkpq_skl@outlook.com Kabupaten Bintan (29151) Provinsi Kepulauan Riau
            </div>
        <?php endif; ?>
    </div>

    <div class="letter-info">
        <div class="letter-number">
            Nomor : 013/FKPQ-SKL/XII/2025<br>
            Perihal : Rekomendasi Guru TPQ
        </div>
        <div class="letter-subject">
            Kepada Yth.<br>
            <strong>Bupati Bintan</strong><br>
            cq. <strong>Bagian Kesejahteraan Rakyat (KESRA)</strong><br>
            Kabupaten Bintan<br>
            di –<br>
            Tempat
        </div>
    </div>

    <div class="content">
        <p class="greeting">Dengan hormat,</p>

        <p>Yang bertanda tangan di bawah ini, Ketua Forum Komunikasi Pendidikan Al-Qur'an (FKPQ) Kecamatan Seri Kuala Lobam, dengan ini memberikan rekomendasi kepada:</p>

        <div class="data-row">
            <span class="data-label">Nama</span>
            <span class="data-value">: <?= esc(ucwords(strtolower($guru['Nama'] ?? '-'))) ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tempat Tugas</span>
            <span class="data-value">: TPQ <?= esc(ucwords(strtolower($guru['TempatTugas'] ?? '-'))) ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Alamat</span>
            <span class="data-value">: <?= esc(ucwords(strtolower($alamatLengkap ?? '-'))) ?></span>
        </div>

        <p style="margin-top: 15px;">Yang bersangkutan adalah guru TPQ yang aktif mengajar, memiliki dedikasi dalam pembinaan pendidikan Al-Qur'an, serta hingga saat ini masih melaksanakan tugas sebagai guru ngaji di wilayah Kecamatan Seri Kuala Lobam.</p>

        <p>Berdasarkan hasil pemantauan dan verifikasi FKPQ Kecamatan Seri Kuala Lobam, yang bersangkutan diusulkan sebagai Penerima Insentif Guru Ngaji Kabupaten Bintan Usulan Tahun 2026.</p>

        <p class="closing">Demikian surat rekomendasi ini kami sampaikan untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan kebijakan Bapak, kami ucapkan terima kasih.</p>
    </div>

    <div class="signature-section">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 33.33%;">
                    <!-- Kolom 1: Kosong -->
                </td>
                <td style="width: 33.33%;">
                    <!-- Kolom 2: Kosong -->
                </td>
                <td style="width: 45%; text-align: center;">
                    <p>Seri Kuala Lobam, <?= esc($tanggalSurat) ?></p>
                    <p style="margin-top: 1px;">Ketua FKPQ</p>
                    <div style="text-align: center; margin-top: 1px;">
                        <?php
                        // Tampilkan QR code jika ada signature untuk Ketua FKPQ
                        if (!empty($signatureKetuaFkpq) && !empty($signatureKetuaFkpq['QrCode'])) {
                            $qrPath = FCPATH . 'uploads/qr/' . $signatureKetuaFkpq['QrCode'];
                            if (file_exists($qrPath)) {
                                $qrContent = file_get_contents($qrPath);
                                $ext = pathinfo($signatureKetuaFkpq['QrCode'], PATHINFO_EXTENSION);
                                $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . strtolower($ext);

                                // Buat URL validasi dari token
                                $validationUrl = '';
                                if (!empty($signatureKetuaFkpq['Token'])) {
                                    $validationUrl = base_url("signature/validateSignature/{$signatureKetuaFkpq['Token']}");
                                }

                                // Tampilkan QR code
                                if (!empty($validationUrl)) {
                                    echo '<a href="' . htmlspecialchars($validationUrl) . '" target="_blank" style="display: inline-block; margin-bottom: 10px;">';
                                }
                                echo '<img src="data:' . $mime . ';base64,' . base64_encode($qrContent) . '" alt="QR Code Ketua FKPQ" style="width: 70px; height: 70px; cursor: pointer;">';
                                if (!empty($validationUrl)) {
                                    echo '</a>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <p style="margin-top: 1px;">(Sudarno)</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-info">
        <Web>Surat ini ditandatangani secara digital melalui aplikasi <strong>www.tpqsmart.simpedis.com</strong> Web resmi FKPQ Kec. Seri Kuala Lobam Kab. Bintan Dicetak pada: <?= esc(formatTanggalIndonesia(date('Y-m-d'), 'd F Y')) ?> <?= esc(date('H:i:s')) ?></p>
    </div>
</body>

</html>