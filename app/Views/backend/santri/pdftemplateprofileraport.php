<?php $d = $data; ?>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
        }

        .header td {
            vertical-align: middle;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #666;
            font-size: 11px;
        }

        .kop-lembaga {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 2px solid #000;
        }

        .kop-lembaga.has-kop-image {
            margin-top: 0;
            padding-top: 0;
            padding-bottom: 10px;
        }

        .kop-lembaga .nama-lembaga {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .kop-lembaga .alamat-lembaga {
            font-size: 12px;
            color: #666;
            margin-bottom: 3px;
        }

        .kop-lembaga .kontak-lembaga {
            font-size: 11px;
            color: #888;
        }

        .divider {
            height: 2px;
            background: #000;
            margin: 8px 0 12px;
        }

        .section {
            border: none;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .section-bottom {
            border: none;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
            min-height: 200px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        table.kv {
            width: 100%;
            border-collapse: collapse;
        }

        table.kv td {
            padding: 5px 6px;
            vertical-align: top;
        }

        table.kv td.k {
            width: 200px;
            color: #444;
        }

        .grid {
            width: 100%;
        }

        .grid td {
            vertical-align: top;
        }

        .photo-box {
            text-align: center;
            border: 1px solid #999;
            width: 120px;
            height: 160px;
            display: inline-block;
            border-radius: 4px;
        }

        .photo-box img {
            max-width: 120px;
            max-height: 160px;
        }

        .sign-area {
            width: 100%;
            margin-top: 6px;
        }

        .sign-col {
            width: 50%;
            text-align: center;
        }

        .sign-placeholder {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sign-name {
            margin-top: 3px;
            text-align: left;
            padding-top: 3px;
            min-width: 180px;
        }

        .small {
            font-size: 10.5px;
            color: #666;
        }

        @page {
            margin-top: 5mm;
            margin-bottom: 5mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }
    </style>
</head>

<body>
    <!-- Kop Lembaga -->
    <div class="kop-lembaga <?= !empty($d['printKopLembaga']) ? 'has-kop-image' : ''; ?>" style="<?= !empty($d['printKopLembaga']) ? 'border-bottom: none;' : ''; ?>">
        <?php if (!empty($d['printKopLembaga'])): ?>
            <!-- Kop Lembaga dari gambar -->
            <?php
            $kopPath = FCPATH . 'uploads/kop/' . $d['printKopLembaga'];
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
                // Fallback jika file tidak ditemukan
                echo '<div class="nama-lembaga">' . htmlspecialchars($d['printNamaTpq']) . '</div>';
                echo '<div class="alamat-lembaga">' . htmlspecialchars($d['printAlamatTpq']) . '</div>';
                echo '<div class="alamat-lembaga">' . htmlspecialchars($d['printKelurahanDesaTpq']) . ', ' . htmlspecialchars($d['printKecamatanTpq']) . ', ' . htmlspecialchars($d['printKabupatenKotaTpq']) . '</div>';
                echo '<div class="alamat-lembaga">' . htmlspecialchars($d['printProvinsiTpq']) . ' ' . htmlspecialchars($d['printKodePosTpq']) . '</div>';
                echo '<div class="kontak-lembaga">Telp: ' . htmlspecialchars($d['printTelpTpq']) . ' | Email: ' . htmlspecialchars($d['printEmailTpq']) . '</div>';
            }
            ?>
        <?php else: ?>
            <!-- Fallback jika tidak ada kop lembaga -->
            <div class="nama-lembaga"><?= htmlspecialchars($d['printNamaTpq']); ?></div>
            <div class="alamat-lembaga"><?= htmlspecialchars($d['printAlamatTpq']); ?></div>
            <div class="alamat-lembaga"><?= htmlspecialchars($d['printKelurahanDesaTpq']); ?>, <?= htmlspecialchars($d['printKecamatanTpq']); ?>, <?= htmlspecialchars($d['printKabupatenKotaTpq']); ?></div>
            <div class="alamat-lembaga"><?= htmlspecialchars($d['printProvinsiTpq']); ?> <?= htmlspecialchars($d['printKodePosTpq']); ?></div>
            <div class="kontak-lembaga">Telp: <?= htmlspecialchars($d['printTelpTpq']); ?> | Email: <?= htmlspecialchars($d['printEmailTpq']); ?></div>
        <?php endif; ?>
    </div>

    <table class="header">
        <tr>
            <td style="width:70%;">
                <div class="brand">Profil Santri</div>
                <div class="muted">Kelas <?= htmlspecialchars($d['printNamaKelas']); ?></div>
            </td>
        </tr>
    </table>
    <?php if (empty($d['printKopLembaga'])): ?>
        <div class="divider"></div>
    <?php endif; ?>

    <table class="grid">
        <tr>
            <td style="width:75%; padding-right:10px;">
                <div class="section">
                    <div class="section-title">Biodata Santri</div>
                    <table class="kv">
                        <tr>
                            <td class="k">Nama</td>
                            <td>: <?= htmlspecialchars($d['printNamaSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">NIK</td>
                            <td>: <?= htmlspecialchars($d['printNikSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Jenis Kelamin</td>
                            <td>: <?= htmlspecialchars($d['printJenisKelamin']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Tempat & Tanggal Lahir</td>
                            <td>: <?= htmlspecialchars($d['printTempatTTL']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Alamat</td>
                            <td>: <?= htmlspecialchars($d['printAlamatSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Kabupaten/Kota</td>
                            <td>: <?= htmlspecialchars($d['printKabupatenKotaSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Kecamatan</td>
                            <td>: <?= htmlspecialchars($d['printKecamatanSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Desa/Kelurahan</td>
                            <td>: <?= htmlspecialchars($d['printKelurahanDesaSantri']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">RT / RW</td>
                            <td>: <?= htmlspecialchars($d['printRtSantri']); ?> / <?= htmlspecialchars($d['printRwSantri']); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width:25%; text-align:center; vertical-align:top;">
                <div class="section">
                    <div class="section-title">Foto</div>
                    <div class="photo-box" style="margin:auto;">
                        <?php if (!empty($d['printFotoSantri'])): ?>
                            <img src="<?= $d['printFotoSantri']; ?>" />
                        <?php endif; ?>
                    </div>
                    <div class="small" style="margin-top:6px;">Ukuran 3x4</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td style="width:100%; padding-right:0;">
                <div class="section">
                    <div class="section-title">Informasi Kesiswaan</div>
                    <table class="kv">
                        <tr>
                            <td class="k">TPQ</td>
                            <td>: <?= htmlspecialchars($d['printNamaTpq']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Kelas</td>
                            <td>: <?= htmlspecialchars($d['printNamaKelas']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Tanggal Diterima</td>
                            <td>: <?= htmlspecialchars($d['printTanggalDiterima']); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Data Orang Tua</div>
                    <table class="kv">
                        <tr>
                            <td class="k">Nama Ayah</td>
                            <td>: <?= htmlspecialchars($d['printNamaAyah']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Pekerjaan Ayah</td>
                            <td>: <?= htmlspecialchars($d['printPekerjaanAyah']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Nama Ibu</td>
                            <td>: <?= htmlspecialchars($d['printNamaIbu']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Pekerjaan Ibu</td>
                            <td>: <?= htmlspecialchars($d['printPekerjaanIbu']); ?></td>
                        </tr>
                        <tr>
                            <td class="k">Telepon Kontak</td>
                            <td>: <?= htmlspecialchars($d['printTelp']); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Baris bawah: tanda tangan Kepala TPQ -->
    <table class="grid" style="margin-top:6px;">
        <tr>
            <td style="width:50%; text-align:left;">
                <div class="section-bottom" style="text-align:left;">
                    <div class="section-title">Kepala <?= htmlspecialchars(($d['printLembagaType'] ?? 'TPQ') === 'MDA' ? 'MDA' : 'TPQ') ?></div>
                    <div class="sign-placeholder">
                        <?php
                        // Coba ambil QR dari folder uploads/qr, gunakan file pertama yang ditemukan
                        $qrBase = FCPATH . 'uploads/qr/';
                        $qrImg = null;
                        if (is_dir($qrBase)) {
                            $files = glob($qrBase . '*.svg');
                            if (!$files) {
                                $files = glob($qrBase . '*.png');
                            }
                            if (!$files) {
                                $files = glob($qrBase . '*.jpg');
                            }
                            if ($files && file_exists($files[0])) {
                                $ext = pathinfo($files[0], PATHINFO_EXTENSION);
                                $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . strtolower($ext);
                                $qrContent = file_get_contents($files[0]);
                                $qrImg = 'data:' . $mime . ';base64,' . base64_encode($qrContent);
                            }
                        }
                        ?>
                        <?php if (!empty($qrImg)): ?>
                            <img src="<?= $qrImg; ?>" alt="QR" style="width:80px;height:80px;" />
                        <?php endif; ?>
                    </div>
                    <div class="sign-name">( <?= htmlspecialchars($d['printKepalaTpq'] ?? (($d['printLembagaType'] ?? 'TPQ') === 'MDA' ? 'Kepala MDA' : 'Kepala TPQ')) ?> )</div>
                </div>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    </table>
</body>

</html>