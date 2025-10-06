<?php $d = $data; ?>
<html>
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11.5px; color:#222; }
        .header { width:100%; }
        .header td { vertical-align: middle; }
        .brand { font-size: 18px; font-weight: bold; }
        .muted { color:#666; font-size: 11px; }
        .divider { height:2px; background:#000; margin:8px 0 12px; }

        .section { border:1px solid #999; border-radius:6px; padding:10px 12px; margin-bottom:10px; }
        .section-title { font-weight:bold; font-size:12.5px; margin-bottom:8px; text-transform:uppercase; }
        table.kv { width:100%; border-collapse: collapse; }
        table.kv td { padding:5px 6px; vertical-align: top; }
        table.kv td.k { width:30%; color:#444; }

        .grid { width:100%; }
        .grid td { vertical-align: top; }
        .photo-box { text-align:center; border:1px solid #999; width: 120px; height: 160px; display:inline-block; border-radius:4px; }
        .photo-box img { max-width: 120px; max-height: 160px; }

        .sign-area { width:100%; margin-top:6px; }
        .sign-col { width:50%; text-align:center; }
        .sign-placeholder { height:60px; }
        .sign-name { margin-top:8px; display:inline-block; border-top:1px solid #333; padding-top:3px; min-width:180px; }

        .small { font-size:10.5px; color:#666; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width:70%;">
                <div class="brand">Profil Santri</div>
                <div class="muted">TPQ <?= htmlspecialchars($d['printNamaTpq']); ?> — Kelas <?= htmlspecialchars($d['printNamaKelas']); ?></div>
            </td>
            <td style="width:30%; text-align:right;">
                <div class="small">Tanggal cetak: <?= date('d-m-Y H:i'); ?></div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <table class="grid">
        <tr>
            <td style="width:100%; padding-right:0;">
                <div class="section">
                    <div class="section-title">Biodata Santri</div>
                    <table class="kv">
                        <tr><td class="k">Nama</td><td>: <?= htmlspecialchars($d['printNamaSantri']); ?></td></tr>
                        <tr><td class="k">NIK</td><td>: <?= htmlspecialchars($d['printNikSantri']); ?></td></tr>
                        <tr><td class="k">Jenis Kelamin</td><td>: <?= htmlspecialchars($d['printJenisKelamin']); ?></td></tr>
                        <tr><td class="k">Tempat & Tanggal Lahir</td><td>: <?= htmlspecialchars($d['printTempatTTL']); ?></td></tr>
                        <tr><td class="k">Alamat</td><td>: <?= htmlspecialchars($d['printAlamatSantri']); ?></td></tr>
                        <tr><td class="k">Kabupaten/Kota</td><td>: <?= htmlspecialchars($d['printKabupatenKotaSantri']); ?></td></tr>
                        <tr><td class="k">Kecamatan</td><td>: <?= htmlspecialchars($d['printKecamatanSantri']); ?></td></tr>
                        <tr><td class="k">Desa/Kelurahan</td><td>: <?= htmlspecialchars($d['printKelurahanDesaSantri']); ?></td></tr>
                        <tr><td class="k">RT / RW</td><td>: <?= htmlspecialchars($d['printRtSantri']); ?> / <?= htmlspecialchars($d['printRwSantri']); ?></td></tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Informasi Kesiswaan</div>
                    <table class="kv">
                        <tr><td class="k">TPQ</td><td>: <?= htmlspecialchars($d['printNamaTpq']); ?></td></tr>
                        <tr><td class="k">Kelas</td><td>: <?= htmlspecialchars($d['printNamaKelas']); ?></td></tr>
                        <tr><td class="k">Tanggal Diterima</td><td>: <?= htmlspecialchars($d['printTanggalDiterima']); ?></td></tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Data Orang Tua</div>
                    <table class="kv">
                        <tr><td class="k">Nama Ayah</td><td>: <?= htmlspecialchars($d['printNamaAyah']); ?></td></tr>
                        <tr><td class="k">Pekerjaan Ayah</td><td>: <?= htmlspecialchars($d['printPekerjaanAyah']); ?></td></tr>
                        <tr><td class="k">Nama Ibu</td><td>: <?= htmlspecialchars($d['printNamaIbu']); ?></td></tr>
                        <tr><td class="k">Pekerjaan Ibu</td><td>: <?= htmlspecialchars($d['printPekerjaanIbu']); ?></td></tr>
                        <tr><td class="k">Telepon Kontak</td><td>: <?= htmlspecialchars($d['printTelp']); ?></td></tr>
                    </table>
                </div>
                
                <!-- Baris bawah: tanda tangan kiri (Kepala TPQ + barcode) dan foto 3x4 kanan -->
                <table class="grid" style="margin-top:6px;">
                    <tr>
                        <td style="width:35%; text-align:center;">
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
                        <td style="width:65%;">
                            <div class="section" style="text-align:center;">
                                <div class="section-title">Kepala TPQ</div>
                                <div class="sign-placeholder">
                                    <?php
                                        // Coba ambil QR dari folder uploads/qr, gunakan file pertama yang ditemukan
                                        $qrBase = FCPATH . 'uploads/qr/';
                                        $qrImg = null;
                                        if (is_dir($qrBase)) {
                                            $files = glob($qrBase . '*.svg');
                                            if (!$files) { $files = glob($qrBase . '*.png'); }
                                            if (!$files) { $files = glob($qrBase . '*.jpg'); }
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
                                <div class="sign-name">( <?= htmlspecialchars($d['printKepalaTpq'] ?? 'Kepala TPQ') ?> )</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="small">Catatan: Ini ringkasan profil untuk keperluan administrasi/rapor.</div>
</body>
</html>


