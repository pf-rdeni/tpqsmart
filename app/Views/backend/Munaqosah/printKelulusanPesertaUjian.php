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

        h2,
        h3 {
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

        th,
        td {
            border: 1px solid #666;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #4472C4;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #D9E2F3;
        }

        .summary-row {
            background-color: #D9E2F3 !important;
            font-weight: bold;
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
                <th>KATEGORI</th>
                <th>MATERI</th>
                <th style="text-align:center">NILAI</th>
                <th style="text-align:center">NILAI BOBOT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalNilai = 0;
            $totalBobot = 0;
            ?>
            <?php foreach ($categoryDetails as $detail): ?>
                <?php
                $category = $detail['category'] ?? [];
                $juriScores = $detail['juri_scores'] ?? [];
                $materis = $detail['materi'] ?? [];

                // Gunakan average yang sudah dihitung di controller (sudah memfilter berdasarkan maxJuri)
                $average = (float)($detail['average'] ?? 0);

                // Jika average masih 0, coba hitung dari juri_scores yang sudah difilter
                if ($average == 0 && !empty($juriScores)) {
                    $validScores = array_filter($juriScores, function ($score) {
                        return isset($score['Nilai']) && (float)$score['Nilai'] > 0;
                    });
                    $validScores = array_values($validScores);

                    if (count($validScores) === 1) {
                        $average = (float)$validScores[0]['Nilai'];
                    } elseif (count($validScores) > 1) {
                        $sum = array_sum(array_column($validScores, 'Nilai'));
                        $average = round($sum / count($validScores), 2);
                    }
                }

                $weighted = (float)($detail['weighted'] ?? 0);
                $totalNilai += $average;
                $totalBobot += $weighted;

                // Ambil materi pertama, atau gabungkan jika ada beberapa
                $materiName = '-';
                if (!empty($materis)) {
                    $materi = $materis[0];
                    $materiName = esc($materi['NamaMateri'] ?? '-', 'html');
                    // Hapus link ayat untuk kesederhanaan
                }
                ?>
                <tr>
                    <td><?= esc($category['name'] ?? '-') ?></td>
                    <td><?= $materiName ?></td>
                    <td style="text-align:center"><?= number_format($average, 1) ?></td>
                    <td style="text-align:center"><?= number_format($weighted, 1) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="summary-row">
                <td></td>
                <td><strong>JUMLAH</strong></td>
                <td style="text-align:center"><strong><?= number_format($totalNilai, 1) ?></strong></td>
                <td style="text-align:center"><strong><?= number_format($totalBobot, 1) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak pada: <?= esc($generated_at ?? date('Y-m-d H:i:s')) ?></div>
        <div>Dokumen ini dihasilkan otomatis dari sistem TPQSMART.</div>
    </div>
</body>

</html>