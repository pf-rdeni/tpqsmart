<?php
    $peserta = $peserta ?? [];
    $categoryDetails = $categoryDetails ?? [];
    $meta = $meta ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelulusan Peserta <?= esc($peserta['NoPeserta'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2, h3 {
            margin: 0 0 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th, td {
            border: 1px solid #666;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
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

        .meta-table td {
            border: none;
            padding: 4px 0;
        }

        .footer {
            margin-top: 24px;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Kelulusan Peserta</h2>
        <h3><?= esc($peserta['NamaSantri'] ?? '-') ?> &ndash; <?= esc($peserta['NoPeserta'] ?? '-') ?></h3>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>TPQ</strong></td>
            <td>: <?= esc($peserta['NamaTpq'] ?? '-') ?></td>
            <td><strong>Tahun Ajaran</strong></td>
            <td>: <?= esc($peserta['IdTahunAjaran'] ?? '-') ?></td>
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
            <td><strong>Threshold</strong></td>
            <td>: <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?></td>
        </tr>
        <tr>
            <td><strong>Selisih</strong></td>
            <td>: <?= number_format((float)($peserta['KelulusanDifference'] ?? 0), 2) ?></td>
            <td><strong>Sumber Bobot</strong></td>
            <td>: <?= esc($meta['bobot_source'] ?? '-') ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Kategori</th>
                <th rowspan="2" style="text-align:center">Bobot (%)</th>
                <th colspan="3" style="text-align:center">Penilaian Juri</th>
                <th rowspan="2" style="text-align:center">Rata</th>
                <th rowspan="2" style="text-align:center">Bobot Nilai</th>
                <th rowspan="2">Materi</th>
            </tr>
            <tr>
                <th style="text-align:center">Juri</th>
                <th style="text-align:center">Nilai</th>
                <th style="text-align:center">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categoryDetails as $detail): ?>
                <?php
                $category = $detail['category'] ?? [];
                $juriScores = $detail['juri_scores'] ?? [];
                $materis = $detail['materi'] ?? [];
                $weight = $category['weight'] ?? 0;
                $materiList = [];
                foreach ($materis as $materi) {
                    $materiName = esc($materi['NamaMateri'] ?? '-', 'html');
                    if (!empty($materi['WebLinkAyat'])) {
                        $materiName .= ' <small>(' . esc($materi['WebLinkAyat'], 'html') . ')</small>';
                    }
                    $materiList[] = $materiName;
                }
                if (empty($materiList)) {
                    $materiList[] = '-';
                }
                $rowspan = max(count($juriScores), 1);
                ?>
                <tr>
                    <td rowspan="<?= $rowspan ?>"><?= esc($category['name'] ?? '-') ?></td>
                    <td rowspan="<?= $rowspan ?>" style="text-align:center"><?= number_format((float)$weight, 2) ?></td>
                    <?php if (!empty($juriScores)): ?>
                        <?php $first = array_shift($juriScores); ?>
                        <td><?= esc($first['label'] ?? 'Juri') ?><?= !empty($first['UsernameJuri']) ? ' (' . esc($first['UsernameJuri']) . ')' : '' ?></td>
                        <td style="text-align:center"><?= number_format((float)($first['Nilai'] ?? 0), 2) ?></td>
                        <td><?= nl2br(esc($first['Catatan'] ?? '-')) ?></td>
                    <?php else: ?>
                        <td colspan="3" style="text-align:center">Belum ada penilaian</td>
                    <?php endif; ?>
                    <td rowspan="<?= $rowspan ?>" style="text-align:center"><?= number_format((float)($detail['average'] ?? 0), 2) ?></td>
                    <td rowspan="<?= $rowspan ?>" style="text-align:center"><?= number_format((float)($detail['weighted'] ?? 0), 2) ?></td>
                    <td rowspan="<?= $rowspan ?>"><?= implode('<br>', $materiList) ?></td>
                </tr>
                <?php if (!empty($juriScores)): ?>
                    <?php foreach ($juriScores as $score): ?>
                        <tr>
                            <td><?= esc($score['label'] ?? 'Juri') ?><?= !empty($score['UsernameJuri']) ? ' (' . esc($score['UsernameJuri']) . ')' : '' ?></td>
                            <td style="text-align:center"><?= number_format((float)($score['Nilai'] ?? 0), 2) ?></td>
                            <td><?= nl2br(esc($score['Catatan'] ?? '-')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak pada: <?= esc($generated_at ?? date('Y-m-d H:i:s')) ?></div>
        <div>Dokumen ini dihasilkan otomatis dari sistem TPQSMART.</div>
    </div>
</body>

</html>

