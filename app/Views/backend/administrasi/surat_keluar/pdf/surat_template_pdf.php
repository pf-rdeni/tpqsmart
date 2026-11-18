<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($surat['NomorSurat'] ?? 'Surat') ?> - <?= esc($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? 'Dokumen') ?></title>
    <style>
        body {
            font-family: <?= !empty($template['FontFamily']) ? esc($template['FontFamily']) : 'Times New Roman' ?>, serif;
            font-size: <?= !empty($template['FontSize']) ? (int)$template['FontSize'] : 12 ?>pt;
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header-kop {
            text-align: center;
            margin-bottom: 0px;
            width: 100%;
        }

        .header-kop img {
            width: 100%;
            max-height: 140px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .kop-text-fallback {
            text-align: center;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        .kop-text-fallback .lembaga-nama {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text-fallback .lembaga-alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 3px;
        }

        .garis-kop {
            border: 0;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .meta-surat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: inherit;
        }

        .meta-surat-table td {
            vertical-align: top;
            padding: 1px 0;
        }

        .tujuan-section {
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .salam-pembuka {
            margin-bottom: 10px;
            font-style: italic;
        }

        .isi-surat {
            text-align: justify;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .isi-surat p {
            margin: 6px 0;
            text-indent: 25px;
        }

        .isi-surat table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .isi-surat table, .isi-surat th, .isi-surat td {
            border: 1px solid #444;
            padding: 5px 8px;
        }

        .salam-penutup {
            margin-top: 15px;
            margin-bottom: 15px;
            font-style: italic;
        }

        .ttd-section {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            vertical-align: top;
            text-align: center;
        }

        .ttd-space-manual {
            height: 65px;
        }

        .ttd-qr img {
            width: 75px;
            height: 75px;
            display: inline-block;
            margin: 5px auto;
        }
    </style>
</head>
<body>

    <!-- 1. KOP SURAT -->
    <?php if (!empty($template['ShowKopLembaga']) && $template['ShowKopLembaga'] == 1 && !empty($kopImageSrc)): ?>
        <div class="header-kop">
            <img src="<?= $kopImageSrc ?>" alt="Kop Surat">
        </div>
    <?php elseif (!empty($template['ShowKopLembaga']) && $template['ShowKopLembaga'] == 1 && !empty($kopText)): ?>
        <div class="kop-text-fallback">
            <div class="lembaga-nama"><?= esc($kopText['nama']) ?></div>
            <?php if (!empty($kopText['alamat'])): ?>
                <div class="lembaga-alamat"><?= esc($kopText['alamat']) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 2. GARIS PEMISAH KOP -->
    <?php if (!empty($template['ShowGarisPemisah']) && $template['ShowGarisPemisah'] == 1 && !empty($template['ShowKopLembaga'])): ?>
        <div class="garis-kop"></div>
    <?php endif; ?>

    <!-- 3. HEADER SURAT (Nomor, Sifat, Lampiran, Hal) & Tanggal Kanan -->
    <table class="meta-surat-table">
        <tr>
            <td style="width: 58%;">
                <table style="width: 100%;">
                    <?php if (!empty($template['ShowNomorSurat'])): ?>
                        <tr>
                            <td style="width: 75px;">Nomor</td>
                            <td style="width: 10px;">:</td>
                            <td><strong><?= esc($surat['NomorSurat'] ?? '-') ?></strong></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($template['ShowLampiran']) && !empty($surat['Lampiran'])): ?>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td><?= esc($surat['Lampiran']) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($template['ShowHal'])): ?>
                        <tr>
                            <td>Perihal</td>
                            <td>:</td>
                            <td><strong><?= esc($surat['Hal'] ?? '-') ?></strong></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </td>
            <td style="width: 42%; text-align: right; vertical-align: top;">
                <?php if (!empty($template['ShowTempatTanggal'])): ?>
                    <?= esc($surat['TempatTtd'] ?? 'Bintan') ?>, <?= !empty($surat['Tanggal']) ? esc(formatTanggalIndonesia($surat['Tanggal'])) : esc(formatTanggalIndonesia(date('Y-m-d'))) ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- 4. TUJUAN SURAT -->
    <?php if (!empty($template['ShowTujuan'])): ?>
        <div class="tujuan-section">
            <div>Kepada Yth.</div>
            <div><strong><?= esc($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? '-') ?></strong></div>
            <?php if (!empty($penerima['JabatanPenerima'] ?? $surat['TujuanJabatan'])): ?>
                <div><?= esc($penerima['JabatanPenerima'] ?? $surat['TujuanJabatan']) ?></div>
            <?php endif; ?>
            <?php if (!empty($surat['TujuanInstansi'])): ?>
                <div><?= esc($surat['TujuanInstansi']) ?></div>
            <?php endif; ?>
            <?php if (!empty($surat['TujuanAlamat'])): ?>
                <div><?= esc($surat['TujuanAlamat']) ?></div>
            <?php endif; ?>
            <div>di -</div>
            <div style="text-indent: 15px;"><strong><?= esc($surat['TujuanKota'] ?? 'Tempat') ?></strong></div>
        </div>
    <?php endif; ?>

    <!-- 5. SALAM PEMBUKA -->
    <?php if (!empty($template['ShowSalam']) && !empty($surat['SalamPembuka'])): ?>
        <div class="salam-pembuka"><?= esc($surat['SalamPembuka']) ?>,</div>
    <?php endif; ?>

    <!-- 6. ISI SURAT -->
    <?php if (!empty($template['ShowIsi'])): ?>
        <div class="isi-surat">
            <?php if (!empty($surat['MukadimahIsi'])): ?>
                <p><?= esc($surat['MukadimahIsi']) ?></p>
            <?php endif; ?>

            <div>
                <?= $renderedIsiSurat ?>
            </div>

            <?php if (!empty($template['ShowPenutup']) && !empty($surat['Penutup'])): ?>
                <p><?= esc($surat['Penutup']) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 7. SALAM PENUTUP -->
    <?php if (!empty($template['ShowSalam']) && !empty($surat['SalamPenutup'])): ?>
        <div class="salam-penutup"><?= esc($surat['SalamPenutup']) ?></div>
    <?php endif; ?>

    <!-- 8. BAGIAN TANDA TANGAN -->
    <?php if (!isset($template['ShowMengetahui']) || !empty($template['ShowMengetahui']) || !empty($ttd1) || !empty($ttd2)): ?>
        <div class="ttd-section">
            <table class="ttd-table">
                <tr>
                    <?php if (!empty($ttd1)): ?>
                        <td style="width: <?= !empty($ttd2) ? '50%' : '100%' ?>;">
                            <div>Mengetahui,</div>
                            <div style="font-weight: bold;"><?= esc($surat['Jabatan1'] ?? $ttd1['jabatan'] ?? 'Kepala Lembaga') ?></div>

                            <?php if (($surat['ModeTtd1'] ?? 'Manual') === 'QR' && !empty($ttd1['qrSrc'])): ?>
                                <div class="ttd-qr">
                                    <a href="<?= !empty($surat['Id']) ? base_url('signature/validateSurat/' . $surat['Id']) : '#' ?>" target="_blank">
                                        <img src="<?= $ttd1['qrSrc'] ?>" width="75" height="75" alt="QR Code Tanda Tangan Digital">
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="ttd-space-manual"></div>
                            <?php endif; ?>

                            <div style="font-weight: bold; text-decoration: underline;">( <?= esc($ttd1['nama'] ?? '____________________') ?> )</div>
                        </td>
                    <?php endif; ?>

                    <?php if (!empty($ttd2)): ?>
                        <td style="width: 50%;">
                            <div>Hormat kami,</div>
                            <div style="font-weight: bold;"><?= esc($surat['Jabatan2'] ?? $ttd2['jabatan'] ?? 'Sekretaris / Pembuat') ?></div>

                            <?php if (($surat['ModeTtd2'] ?? 'Manual') === 'QR' && !empty($ttd2['qrSrc'])): ?>
                                <div class="ttd-qr">
                                    <a href="<?= !empty($surat['Id']) ? base_url('signature/validateSurat/' . $surat['Id']) : '#' ?>" target="_blank">
                                        <img src="<?= $ttd2['qrSrc'] ?>" width="75" height="75" alt="QR Code Tanda Tangan Digital">
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="ttd-space-manual"></div>
                            <?php endif; ?>

                            <div style="font-weight: bold; text-decoration: underline;">( <?= esc($ttd2['nama'] ?? '____________________') ?> )</div>
                        </td>
                    <?php endif; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>

</body>
</html>
