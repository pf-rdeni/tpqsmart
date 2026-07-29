<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= esc($paket['NamaPaket']) ?> — PDF Export</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Amiri', 'Traditional Arabic', sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.5;
            margin: 10px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header-kop h3 {
            margin: 2px 0;
            font-size: 13px;
            font-weight: normal;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .info-table td {
            padding: 3px 5px;
        }
        .info-table td.label {
            font-weight: bold;
            width: 120px;
        }
        .soal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .soal-table td {
            vertical-align: top;
            padding: 2px;
        }
        .pilihan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .pilihan-table td {
            vertical-align: top;
            padding: 3px 2px;
        }
        .jawaban-kunci {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: bold;
        }
        .kunci-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .kunci-table th, .kunci-table td {
            border: 1px solid #374151;
            padding: 6px 8px;
            text-align: center;
        }
        .kunci-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .pembahasan-box {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 6px 10px;
            margin-top: 6px;
            font-size: 11px;
            border-radius: 4px;
        }
        .badge-esai {
            background-color: #8b5cf6;
            color: #fff;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        img {
            display: inline-block;
            vertical-align: middle;
        }
        .pilihan-table img {
            max-width: 120px !important;
            max-height: 120px !important;
            width: auto !important;
            height: auto !important;
        }
        .soal-table img {
            max-width: 200px !important;
            max-height: 200px !important;
        }
    </style>
</head>
<body>

    <!-- KOP HEADER -->
    <div class="header-kop">
        <h2>MADRASAH DINIYAH TAKMILIYAH AWALIYAH (MDTA)</h2>
        <h3>NAMA PAKET: <?= esc($paket['NamaPaket']) ?></h3>
        <small>Mata Pelajaran: <?= esc($paket['NamaMateri'] ?? 'Materi') ?> | Kelas: <?= esc($paket['NamaKelas'] ?? 'Semua Kelas') ?></small>
    </div>

    <!-- METADATA INFO -->
    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td>: <?= esc($paket['NamaMateri'] ?? '-') ?></td>
            <td class="label">Total Soal</td>
            <td>: <?= count($soalList) ?> Soal</td>
        </tr>
        <tr>
            <td class="label">Kelas / Jenjang</td>
            <td>: <?= esc($paket['NamaKelas'] ?? '-') ?></td>
            <td class="label">Dokumen Mode</td>
            <td>: <strong><?= strtoupper(esc($mode)) ?></strong></td>
        </tr>
    </table>
    <hr style="border: 0.5px solid #d1d5db; margin-bottom: 15px;">

    <?php if ($mode === 'jawaban'): ?>
        <!-- KUNCI JAWABAN SAJA (MODE = JAWABAN) -->
        <h3 style="text-align:center; margin-bottom: 10px;">KUNCI JAWABAN & PEMBAHASAN</h3>
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
                        <td><?= ($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai' ? '<span class="badge-esai">Esai</span>' : 'Pilihan Ganda' ?></td>
                        <td><strong style="color: #0284c7; font-size: 14px;"><?= $kunci ?></strong></td>
                        <td style="text-align:left;"><?= $soal['Pembahasan'] ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <!-- LEMBAR SOAL (MODE = SOAL ATAU SEMUA) -->
        <?php foreach ($soalList as $idx => $soal): ?>
            <table class="soal-table">
                <tr>
                    <td width="25" style="font-weight: bold;"><?= $idx + 1 ?>.</td>
                    <td>
                        <div><?= $soal['UraianSoal'] ?></div>

                        <?php if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai'): ?>
                            <div style="margin-top: 5px; margin-bottom: 10px; font-style: italic; color: #6b21a8;">
                                [ Soal Uraian / Esai ]
                            </div>
                        <?php else: ?>
                            <?php if (!empty($soal['pilihan'])): ?>
                                <table class="pilihan-table">
                                    <?php foreach ($soal['pilihan'] as $p): ?>
                                        <?php $isKunci = ($mode === 'semua' && $p['IsBenar'] == 1); ?>
                                        <tr class="<?= $isKunci ? 'jawaban-kunci' : '' ?>">
                                            <td width="20" style="font-weight: bold;"><?= esc($p['HurufPilihan']) ?>.</td>
                                            <td>
                                                <span><?= $p['TeksPilihan'] ?></span>
                                                <?php if ($isKunci): ?>
                                                    <strong style="color: #0284c7;"> (Kunci Jawaban - Skor 1)</strong>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($mode === 'semua' && !empty($soal['Pembahasan'])): ?>
                            <div class="pembahasan-box">
                                <strong>Pembahasan:</strong> <?= $soal['Pembahasan'] ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
