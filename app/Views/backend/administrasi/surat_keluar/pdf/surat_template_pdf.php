<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($surat['NomorSurat'] ?? 'Surat') ?> - <?= esc(function_exists('formatNamaGelar') ? formatNamaGelar($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? 'Dokumen') : ($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? 'Dokumen')) ?></title>
    <?php
        $rawKertas = strtoupper(trim($template['UkuranKertas'] ?? 'A4'));
        $rawOrient = strtolower(trim($template['Orientasi'] ?? 'portrait'));
        $isLandscape = ($rawOrient === 'landscape' || $rawOrient === 'l');

        if (in_array($rawKertas, ['F4', 'FOLIO'])) {
            $paperWidthMm  = $isLandscape ? 330 : 215;
            $paperHeightMm = $isLandscape ? 215 : 330;
        } elseif ($rawKertas === 'A5') {
            $paperWidthMm  = $isLandscape ? 210 : 148;
            $paperHeightMm = $isLandscape ? 148 : 210;
        } elseif ($rawKertas === 'LEGAL') {
            $paperWidthMm  = $isLandscape ? 356 : 216;
            $paperHeightMm = $isLandscape ? 216 : 356;
        } else { // Default A4
            $paperWidthMm  = $isLandscape ? 297 : 210;
            $paperHeightMm = $isLandscape ? 210 : 297;
        }

        $marginTopMm    = !empty($template['MarginAtas']) ? (float)$template['MarginAtas'] : 20;
        $marginBottomMm = !empty($template['MarginBawah']) ? (float)$template['MarginBawah'] : 20;
        $marginLeftMm   = !empty($template['MarginKiri']) ? (float)$template['MarginKiri'] : 25;
        $marginRightMm  = !empty($template['MarginKanan']) ? (float)$template['MarginKanan'] : 15;
    ?>
    <style>
        @page {
            size: <?= $paperWidthMm ?>mm <?= $paperHeightMm ?>mm;
            margin-top: <?= $marginTopMm ?>mm;
            margin-bottom: <?= $marginBottomMm ?>mm;
            margin-left: <?= $marginLeftMm ?>mm;
            margin-right: <?= $marginRightMm ?>mm;
        }

        @media screen {
            html, body {
                background-color: #525659;
                margin: 0;
                padding: 15px 0;
                display: flex;
                justify-content: center;
                min-height: 100%;
            }
            .page-sheet {
                background: #ffffff;
                width: <?= $paperWidthMm ?>mm;
                min-height: <?= $paperHeightMm ?>mm;
                padding: <?= $marginTopMm ?>mm <?= $marginRightMm ?>mm <?= $marginBottomMm ?>mm <?= $marginLeftMm ?>mm;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                box-sizing: border-box;
                margin: 0 auto;
            }
        }

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

        .ttd-col-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .ttd-col-table td {
            border: none;
            padding: 0;
            text-align: center;
        }

        .ttd-qr-cell {
            text-align: center;
            height: 65pt;
            vertical-align: middle;
        }

        .ttd-qr-link {
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .ttd-qr-link:hover .ttd-qr-img {
            opacity: 0.88;
            outline: 2px dashed #007bff;
            border-radius: 4px;
        }

        .ttd-qr-img {
            width: 52pt;
            height: 52pt;
            display: block;
            margin: 0 auto;
            border: 0;
            cursor: pointer;
        }

        .ttd-space-manual {
            height: 52pt;
        }
    </style>
</head>
<body>
    <div class="page-sheet">

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
            <div><strong><?= esc(function_exists('formatNamaGelar') ? formatNamaGelar($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? '-') : ($penerima['NamaPenerima'] ?? $surat['TujuanNama'] ?? '-')) ?></strong></div>
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

    <!-- 8. BAGIAN TANDA TANGAN (DINAMIS & MULTI-SIGNER) -->
    <?php
    if (!function_exists('renderSignerPdfCell')) {
        function renderSignerPdfCell($s, $surat) {
            $namaDisplay = $s['nama'] ?? '____________________';
            if ($namaDisplay !== '____________________' && function_exists('formatNamaGelar')) {
                $namaDisplay = formatNamaGelar($namaDisplay);
            }

            $html = '<table class="ttd-col-table" style="width: 100%; border-collapse: collapse; margin: 0 auto;">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; font-weight: bold; padding-bottom: 2pt;">' . esc($s['jabatan'] ?: 'Penanda Tangan') . '</td>';
            $html .= '</tr>';

            if (!empty($s['show_instansi']) && !empty($s['instansi'])) {
                $html .= '<tr>';
                $html .= '<td style="text-align: center; font-size: 8.5pt; color: #444; padding-bottom: 4pt;">' . esc($s['instansi']) . '</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr><td style="height: 3pt;"></td></tr>';
            }

            $html .= '<tr>';
            $html .= '<td class="ttd-qr-cell" style="text-align: center; height: 58pt; vertical-align: middle;">';
            if (($s['mode'] ?? '') === 'QR' && !empty($s['qrSrc'])) {
                $url = !empty($s['validation_url']) 
                    ? $s['validation_url'] 
                    : (!empty($s['token_10']) 
                        ? base_url('signature/verify/' . $s['token_10']) 
                        : (!empty($surat['Id']) 
                            ? base_url('signature/validateSurat/' . $surat['Id']) 
                            : base_url('signature/verify/preview')));

                if (!empty($url) && strpos($url, '.') === false) {
                    $url .= (strpos($url, '?') !== false ? '&' : '?') . 'v=1.0';
                }

                $titleTooltip = 'Verifikasi Keaslian Dokumen: ' . $url;
                $html .= '<a href="' . esc($url) . '" target="_blank" title="' . esc($titleTooltip) . '" class="ttd-qr-link" style="text-decoration: none; display: inline-block; cursor: pointer;">';
                $html .= '<img src="' . $s['qrSrc'] . '" class="ttd-qr-img" style="width: 50pt; height: 50pt; border: 0; cursor: pointer; display: block;" alt="QR Code">';
                $html .= '</a>';
            } else {
                $html .= '<div class="ttd-space-manual" style="height: 48pt;"></div>';
            }
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td style="text-align: center; padding-top: 6pt; font-weight: bold; text-decoration: underline;">( ' . esc($namaDisplay) . ' )</td>';
            $html .= '</tr>';

            if (!empty($s['show_nip']) && !empty($s['nip'])) {
                $html .= '<tr>';
                $html .= '<td style="text-align: center; font-size: 8.5pt; padding-top: 2pt;">NIP/ID: ' . esc($s['nip']) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
            return $html;
        }
    }
    ?>

    <?php if (!empty($renderedSigners)): ?>
        <?php
        // Pisahkan baris 1 (pelaksana / penanggung jawab) & baris 2 (mengetahui)
        $baris1 = [];
        $baris2 = [];
        foreach ($renderedSigners as $s) {
            if (strpos($s['posisi'], 'mengetahui') !== false) {
                $baris2[] = $s;
            } else {
                $baris1[] = $s;
            }
        }

        // Urutkan baris 1: kiri, tengah, kanan
        $posOrder1 = ['kiri' => 1, 'tengah' => 2, 'kanan' => 3];
        usort($baris1, function($a, $b) use ($posOrder1) {
            return ($posOrder1[$a['posisi']] ?? 9) <=> ($posOrder1[$b['posisi']] ?? 9);
        });

        // Urutkan baris 2: mengetahui_kiri, mengetahui_tengah, mengetahui_kanan
        $posOrder2 = ['mengetahui_kiri' => 1, 'mengetahui_tengah' => 2, 'mengetahui_kanan' => 3];
        usort($baris2, function($a, $b) use ($posOrder2) {
            return ($posOrder2[$a['posisi']] ?? 9) <=> ($posOrder2[$b['posisi']] ?? 9);
        });
        ?>

        <div class="ttd-section">
            <!-- BARIS 1 -->
            <?php if (!empty($baris1)): ?>
                <table class="ttd-table" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <?php
                        $b1Count = count($baris1);
                        if ($b1Count === 1) {
                            $singlePos = $baris1[0]['posisi'] ?? 'kanan';
                            if ($singlePos === 'kanan') {
                                echo '<td style="width: 52%;"></td>';
                                echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                                echo renderSignerPdfCell($baris1[0], $surat);
                                echo '</td>';
                            } elseif ($singlePos === 'tengah') {
                                echo '<td style="width: 25%;"></td>';
                                echo '<td style="width: 50%; vertical-align: top; text-align: center;">';
                                echo renderSignerPdfCell($baris1[0], $surat);
                                echo '</td>';
                                echo '<td style="width: 25%;"></td>';
                            } else {
                                echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                                echo renderSignerPdfCell($baris1[0], $surat);
                                echo '</td>';
                                echo '<td style="width: 52%;"></td>';
                            }
                        } elseif ($b1Count === 2) {
                            echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                            echo renderSignerPdfCell($baris1[0], $surat);
                            echo '</td>';
                            echo '<td style="width: 4%;"></td>';
                            echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                            echo renderSignerPdfCell($baris1[1], $surat);
                            echo '</td>';
                        } elseif ($b1Count >= 3) {
                            foreach ($baris1 as $idx => $s) {
                                if ($idx > 0) {
                                    echo '<td style="width: 2%;"></td>';
                                }
                                echo '<td style="width: 32%; vertical-align: top; text-align: center;">';
                                echo renderSignerPdfCell($s, $surat);
                                echo '</td>';
                            }
                        }
                        ?>
                    </tr>
                </table>
            <?php endif; ?>

            <!-- BARIS 2 (MENGETAHUI) -->
            <?php if (!empty($baris2)): ?>
                <div style="text-align: center; font-size: 10pt; margin-top: 14pt; margin-bottom: 6pt;">
                    <?= esc($baris2[0]['label_mengetahui'] ?? 'Mengetahui,') ?>
                </div>

                <?php
                $b2Count = count($baris2);
                if ($b2Count === 1) {
                    echo '<table style="width: 50%; margin: 0 auto; border-collapse: collapse;">';
                    echo '<tr>';
                    echo '<td style="text-align: center; vertical-align: top;">';
                    echo renderSignerPdfCell($baris2[0], $surat);
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                } else {
                    echo '<table class="ttd-table" style="width: 100%; border-collapse: collapse;">';
                    echo '<tr>';
                    echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                    echo renderSignerPdfCell($baris2[0], $surat);
                    echo '</td>';
                    echo '<td style="width: 4%;"></td>';
                    echo '<td style="width: 48%; vertical-align: top; text-align: center;">';
                    echo renderSignerPdfCell($baris2[1], $surat);
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                }
                ?>
            <?php endif; ?>
        </div>
    <?php elseif (!isset($template['ShowMengetahui']) || !empty($template['ShowMengetahui']) || !empty($ttd1) || !empty($ttd2)): ?>
        <!-- Fallback Surat Lama (Backward Compatibility) -->
        <div class="ttd-section">
            <table class="ttd-table" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <?php if (!empty($ttd1)): ?>
                        <td style="width: <?= !empty($ttd2) ? '50%' : '100%' ?>; vertical-align: top; text-align: center;">
                            <table class="ttd-col-table" style="width: 100%; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 2pt;">Mengetahui,</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; font-weight: bold; padding-bottom: 8pt;"><?= esc($surat['Jabatan1'] ?? $ttd1['jabatan'] ?? 'Kepala Lembaga') ?></td>
                                </tr>
                                <tr>
                                    <td class="ttd-qr-cell" style="text-align: center; height: 65pt; vertical-align: middle;">
                                        <?php if (($surat['ModeTtd1'] ?? 'Manual') === 'QR' && !empty($ttd1['qrSrc'])): ?>
                                            <?php 
                                                $urlTtd1 = !empty($ttd1['validation_url']) 
                                                    ? $ttd1['validation_url'] 
                                                    : (!empty($surat['Id']) 
                                                        ? base_url('signature/validateSurat/' . $surat['Id']) 
                                                        : base_url('signature/verify/preview'));
                                                if (!empty($urlTtd1) && strpos($urlTtd1, '.') === false) {
                                                    $urlTtd1 .= (strpos($urlTtd1, '?') !== false ? '&' : '?') . 'v=1.0';
                                                }
                                            ?>
                                            <a href="<?= esc($urlTtd1) ?>" target="_blank" title="Verifikasi Keaslian Dokumen: <?= esc($urlTtd1) ?>" class="ttd-qr-link" style="text-decoration: none; display: inline-block; cursor: pointer;">
                                                <img src="<?= $ttd1['qrSrc'] ?>" class="ttd-qr-img" style="width: 52pt; height: 52pt; border: 0; cursor: pointer; display: block;" alt="QR Code Tanda Tangan Digital">
                                            </a>
                                        <?php else: ?>
                                            <div class="ttd-space-manual" style="height: 52pt;"></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <?php 
                                        $namaTtd1Display = $ttd1['nama'] ?? '____________________';
                                        if ($namaTtd1Display !== '____________________' && function_exists('formatNamaGelar')) {
                                            $namaTtd1Display = formatNamaGelar($namaTtd1Display);
                                        }
                                    ?>
                                    <td style="text-align: center; padding-top: 10pt; font-weight: bold; text-decoration: underline;">( <?= esc($namaTtd1Display) ?> )</td>
                                </tr>
                                <?php if (!empty($ttd1['nip'])): ?>
                                    <tr>
                                        <td style="text-align: center; font-size: 9pt; padding-top: 2pt;">NIP/ID: <?= esc($ttd1['nip']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    <?php endif; ?>

                    <?php if (!empty($ttd2)): ?>
                        <td style="width: <?= !empty($ttd1) ? '50%' : '100%' ?>; vertical-align: top; text-align: center;">
                            <table class="ttd-col-table" style="width: 100%; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 2pt;">Hormat kami,</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; font-weight: bold; padding-bottom: 8pt;"><?= esc($surat['Jabatan2'] ?? $ttd2['jabatan'] ?? 'Sekretaris / Pembuat') ?></td>
                                </tr>
                                <tr>
                                    <td class="ttd-qr-cell" style="text-align: center; height: 65pt; vertical-align: middle;">
                                        <?php if (($surat['ModeTtd2'] ?? 'Manual') === 'QR' && !empty($ttd2['qrSrc'])): ?>
                                            <?php 
                                                $urlTtd2 = !empty($ttd2['validation_url']) 
                                                    ? $ttd2['validation_url'] 
                                                    : (!empty($surat['Id']) 
                                                        ? base_url('signature/validateSurat/' . $surat['Id']) 
                                                        : base_url('signature/verify/preview'));
                                                if (!empty($urlTtd2) && strpos($urlTtd2, '.') === false) {
                                                    $urlTtd2 .= (strpos($urlTtd2, '?') !== false ? '&' : '?') . 'v=1.0';
                                                }
                                            ?>
                                            <a href="<?= esc($urlTtd2) ?>" target="_blank" title="Verifikasi Keaslian Dokumen: <?= esc($urlTtd2) ?>" class="ttd-qr-link" style="text-decoration: none; display: inline-block; cursor: pointer;">
                                                <img src="<?= $ttd2['qrSrc'] ?>" class="ttd-qr-img" style="width: 52pt; height: 52pt; border: 0; cursor: pointer; display: block;" alt="QR Code Tanda Tangan Digital">
                                            </a>
                                        <?php else: ?>
                                            <div class="ttd-space-manual" style="height: 52pt;"></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <?php 
                                        $namaTtd2Display = $ttd2['nama'] ?? '____________________';
                                        if ($namaTtd2Display !== '____________________' && function_exists('formatNamaGelar')) {
                                            $namaTtd2Display = formatNamaGelar($namaTtd2Display);
                                        }
                                    ?>
                                    <td style="text-align: center; padding-top: 10pt; font-weight: bold; text-decoration: underline;">( <?= esc($namaTtd2Display) ?> )</td>
                                </tr>
                                <?php if (!empty($ttd2['nip'])): ?>
                                    <tr>
                                        <td style="text-align: center; font-size: 9pt; padding-top: 2pt;">NIP/ID: <?= esc($ttd2['nip']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    <?php endif; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    </div>
</body>
</html>
