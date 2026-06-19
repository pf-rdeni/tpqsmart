<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Survey — <?= esc($survey['title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-table td.label {
            font-weight: bold;
            color: #555;
            width: 20%;
        }
        .question-block {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .question-title {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 6px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-answers {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 8px 12px;
            max-height: 250px;
            overflow: hidden;
        }
        .text-answer-item {
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }
        .text-answer-item:last-child {
            border-bottom: none;
        }
        .footer-print {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 50px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; background: #fff3cd; padding: 10px; border: 1px solid #ffeeba; border-radius: 4px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 15px; font-weight: bold; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cetak Laporan</button>
        <button onclick="window.close()" style="padding: 6px 15px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Ringkasan Hasil Survey</h1>
        <p>Aplikasi TPQSmart</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Judul Survey</td>
            <td>: <?= esc($survey['title']) ?></td>
            <td class="label">Tanggal Cetak</td>
            <td>: <?= $generated_at ?></td>
        </tr>
        <tr>
            <td class="label">Deskripsi</td>
            <td>: <?= $survey['description'] ?: '-' ?></td>
            <td class="label">Total Respon</td>
            <td>: <strong><?= $total_response ?> Responden</strong></td>
        </tr>
        <tr>
            <td class="label">Target Responden</td>
            <td class="text-uppercase">: <?= esc($survey['target_type']) ?></td>
            <td class="label">Status Survey</td>
            <td class="text-uppercase">: <?= esc($survey['status']) ?></td>
        </tr>
    </table>

    <?php if ($total_response === 0): ?>
        <div style="text-align: center; padding: 50px; color: #666;">
            <h3>Belum Ada Data Tanggapan</h3>
            <p>Tidak dapat mencetak laporan karena belum ada responden yang mengisi survey.</p>
        </div>
    <?php else: ?>
        <!-- Loop questions to generate tabular summaries -->
        <?php foreach ($questions as $idx => $q): ?>
            <?php 
            if (in_array($q['question_type'], ['image_display', 'video_display'])) continue; 
            $qId = $q['id'];
            $qSum = $summary[$qId] ?? [];
            ?>
            <div class="question-block">
                <div class="question-title">
                    <?= $idx + 1 ?>. <?= $q['question_text'] ?> 
                    <span style="font-size: 10px; font-weight: normal; color: #888;">(Tipe: <?= esc($q['question_type']) ?>)</span>
                </div>

                <?php if (in_array($q['question_type'], ['text_short', 'text_paragraph'])): ?>
                    <div class="text-answers">
                        <?php if (empty($qSum['answers'])): ?>
                            <em class="small text-muted">Tidak ada respon.</em>
                        <?php else: ?>
                            <?php foreach ($qSum['answers'] as $ans): ?>
                                <div class="text-answer-item small">- <?= esc($ans) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                <?php elseif (in_array($q['question_type'], ['multiple_choice', 'checkbox', 'dropdown', 'linear_scale', 'rating'])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Opsi Pilihan</th>
                                <th style="width: 20%" class="text-center">Jumlah Respon</th>
                                <th style="width: 20%" class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $labels = $qSum['labels'] ?? [];
                            $counts = $qSum['counts'] ?? [];
                            $totalQ = array_sum($counts) ?: 1;
                            ?>
                            <?php foreach ($labels as $i => $label): ?>
                                <?php 
                                $cnt = $counts[$i] ?? 0;
                                $pct = round(($cnt / $totalQ) * 100, 1);
                                ?>
                                <tr>
                                    <td><?= esc($label) ?></td>
                                    <td class="text-center font-weight-bold"><?= $cnt ?></td>
                                    <td class="text-center"><?= $pct ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php elseif (in_array($q['question_type'], ['grid_multiple', 'grid_checkbox'])): ?>
                    <table class="data-table text-center">
                        <thead>
                            <tr class="bg-light">
                                <th style="text-align: left;">Pernyataan Baris</th>
                                <?php foreach ($qSum['columns'] ?? [] as $col): ?>
                                    <th><?= esc($col) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($qSum['rows'] ?? [] as $row => $colStats): ?>
                                <tr>
                                    <td style="text-align: left; font-weight: bold;"><?= esc($row) ?></td>
                                    <?php foreach ($qSum['columns'] ?? [] as $col): ?>
                                        <td><?= $colStats[$col] ?? 0 ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php elseif ($q['question_type'] === 'file_upload'): ?>
                    <div style="background: #fcfcfc; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 11px;">
                        <strong><?= count($qSum['files'] ?? []) ?> file terunggah.</strong> File-file tersebut dikelola di server.
                    </div>

                <?php elseif (in_array($q['question_type'], ['master_tpq', 'master_guru', 'master_santri'])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Pemilihan Terkait</th>
                                <th style="width: 25%" class="text-center">Frekuensi Terpilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($qSum['counts'])): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada respon terkait data master ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                arsort($qSum['counts']);
                                ?>
                                <?php foreach ($qSum['counts'] as $name => $cnt): ?>
                                    <tr>
                                        <td><?= esc($name) ?></td>
                                        <td class="text-center font-weight-bold"><?= $cnt ?> kali</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="footer-print">
        Laporan ini digenerate secara otomatis oleh Aplikasi TPQSmart pada <?= $generated_at ?>.
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
