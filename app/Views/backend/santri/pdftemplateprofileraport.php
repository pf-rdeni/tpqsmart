<?php $d = $data; ?>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #222;
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
            width: 30%;
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
    </style>
</head>

<body>
    <!-- Kop Lembaga -->
    <div class="kop-lembaga">
        <div class="nama-lembaga"><?= htmlspecialchars($d['printNamaTpq']); ?></div>
        <div class="alamat-lembaga">Alamat TPQ</div>
        <div class="alamat-lembaga">Kelurahan/Desa, Kecamatan, Kabupaten/Kota</div>
        <div class="kontak-lembaga">Telp: - | Email: -</div>
    </div>

    <table class="header">
        <tr>
            <td style="width:70%;">
                <div class="brand">Profil Santri</div>
                <div class="muted">Kelas <?= htmlspecialchars($d['printNamaKelas']); ?></div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <table class="grid">
        <tr>
            <td style="width:70%; padding-right:10px;">
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
            <td style="width:30%; text-align:center; vertical-align:top;">
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

                <!-- Baris bawah: tanda tangan Kepala TPQ -->
                <table class="grid" style="margin-top:6px;">
                    <tr>
                        <td style="width:50%; text-align:left;">
                            <div class="section-bottom" style="text-align:left;">
                                <div class="section-title">Kepala TPQ</div>
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
                                <div class="sign-name">( <?= htmlspecialchars($d['printKepalaTpq'] ?? 'Kepala TPQ') ?> )</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>