<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title><?= esc($paket['NamaPaket']) ?> — MS Word Export</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body {
            font-family: 'Times New Roman', 'Traditional Arabic', 'Amiri', serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.4;
        }
        /* Formatting Gambar Preservasi Ukuran Editor */
        img {
            max-width: 100%;
            display: inline-block;
            margin: 4px 0;
        }
        figure.image {
            margin: 5px 0;
            max-width: 100%;
        }
        figure.image img {
            max-width: 100%;
        }
        h2 {
            text-align: center;
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
        }
        h3 {
            text-align: center;
            font-size: 12pt;
            margin: 2px 0 10px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .meta-table td {
            padding: 2px 4px;
            font-size: 10pt;
        }
        .soal-box {
            margin-bottom: 15px;
        }
        .pilihan-table {
            margin-left: 20px;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .pilihan-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .kunci-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .kunci-table th, .kunci-table td {
            border: 1pt solid #000000;
            padding: 5px;
            text-align: center;
            font-size: 10pt;
        }
        .kunci-table th {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        .pembahasan-text {
            background-color: #fef9c3;
            border: 1pt solid #ca8a04;
            padding: 5px 8px;
            margin-top: 5px;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>

    <h2>MADRASAH DINIYAH TAKMILIYAH AWALIYAH (MDTA)</h2>
    <h3>PAKET SOAL: <?= esc($paket['NamaPaket']) ?></h3>

    <table class="meta-table">
        <tr>
            <td width="120"><strong>Mata Pelajaran</strong></td>
            <td>: <?= esc($paket['NamaMateri'] ?? '-') ?></td>
            <td width="100"><strong>Jumlah Soal</strong></td>
            <td>: <?= count($soalList) ?> Soal</td>
        </tr>
        <tr>
            <td><strong>Kelas / Jenjang</strong></td>
            <td>: <?= esc($paket['NamaKelas'] ?? '-') ?></td>
            <td><strong>Dokumen Mode</strong></td>
            <td>: <strong><?= strtoupper(esc($mode)) ?> (MS WORD)</strong></td>
        </tr>
    </table>
    <hr style="border: 1pt solid #000000; margin-bottom: 15px;">

    <?php if ($mode === 'jawaban'): ?>
        <!-- KUNCI JAWABAN SAJA -->
        <h3 style="text-align:center;">KUNCI JAWABAN & PEMBAHASAN</h3>
        <table class="kunci-table">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th width="100">Jenis Soal</th>
                    <th width="100">Kunci Jawaban</th>
                    <th>Pembahasan / Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soalList as $idx => $soal): ?>
                    <?php
                    $kunci = '-';
                    if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'pilihan_ganda' && !empty($soal['pilihan'])) {
                        foreach ($soal['pilihan'] as $p) {
                            if ($p['IsBenar'] == 1) {
                                $kunci = $p['HurufPilihan'];
                                break;
                            }
                        }
                    } else if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai') {
                        $kunci = 'Esai / Uraian';
                    }
                    ?>
                    <tr>
                        <td><strong><?= $idx + 1 ?></strong></td>
                        <td><?= ($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai' ? 'Esai' : 'Pilihan Ganda' ?></td>
                        <td><strong style="color: #0284c7; font-size: 12pt;"><?= $kunci ?></strong></td>
                        <td style="text-align:left;"><?= $soal['Pembahasan'] ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <!-- LEMBAR SOAL / SOAL DAN JAWABAN -->
        <?php foreach ($soalList as $idx => $soal): ?>
            <div class="soal-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td width="25" style="vertical-align: top;"><strong><?= $idx + 1 ?>.</strong></td>
                        <td style="vertical-align: top;">
                            <div><?= $soal['UraianSoal'] ?></div>

                            <?php if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai'): ?>
                                <div style="margin-top: 5px; font-style: italic; color: #6b21a8;">
                                    [ Soal Uraian / Esai ]
                                </div>
                            <?php else: ?>
                                <?php if (!empty($soal['pilihan'])): ?>
                                    <table class="pilihan-table">
                                        <?php foreach ($soal['pilihan'] as $p): ?>
                                            <?php $isKunci = ($mode === 'semua' && $p['IsBenar'] == 1); ?>
                                            <tr>
                                                <td width="20"><strong><?= esc($p['HurufPilihan']) ?>.</strong></td>
                                                <td>
                                                    <?= $p['TeksPilihan'] ?>
                                                    <?php if ($isKunci): ?>
                                                        <strong style="color: #0284c7;"> — [KUNCI JAWABAN]</strong>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($mode === 'semua' && !empty($soal['Pembahasan'])): ?>
                                <div class="pembahasan-text">
                                    <strong>Pembahasan:</strong> <?= $soal['Pembahasan'] ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
