<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Ujian Per Materi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000; padding: 6px 8px; font-size: 10pt; }
        th { background-color: #F2F2F2; font-weight: bold; text-align: center; vertical-align: middle; }
        th.vertical-header {
            mso-rotate: 90;
            white-space: nowrap;
            height: 180px;
            vertical-align: bottom;
            text-align: center;
            font-size: 9pt;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h3 style="text-align: center; margin-bottom: 5px;">REKAPITULASI NILAI UJIAN MDTA PER MATERI</h3>
    <?php 
        $namaLembagaHeader = !empty($santriList[0]['NamaTpq']) ? $santriList[0]['NamaTpq'] : '';
        $namaKelasHeader   = !empty($santriList[0]['NamaKelas']) ? $santriList[0]['NamaKelas'] : '';
    ?>
    <?php if (!empty($namaLembagaHeader) || !empty($namaKelasHeader)): ?>
        <p style="text-align: center; margin-top: 0; margin-bottom: 3px; font-weight: bold; font-size: 11pt;">
            <?= esc($namaLembagaHeader) ?><?= (!empty($namaLembagaHeader) && !empty($namaKelasHeader)) ? ' — ' : '' ?><?= esc($namaKelasHeader) ?>
        </p>
    <?php endif; ?>
    <p style="text-align: center; margin-top: 0; font-size: 10pt;">Tanggal Export: <?= date('d-m-Y H:i') ?></p>

    
    <table>
        <thead>
            <tr>
                <th width="40">NO</th>
                <th width="110">NO PESERTA</th>
                <th width="220">NAMA PESERTA</th>
                <th width="100">KELAS</th>
                <th width="150">LEMBAGA</th>
                <th width="110">TIPE UJIAN</th>
                <?php foreach ($materiList as $m): ?>
                    <th class="vertical-header" width="60"><?= strtoupper(esc($m['NamaMateri'])) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($santriList)): ?>
                <tr>
                    <td colspan="<?= 6 + count($materiList) ?>" class="text-center">Tidak ada data santri</td>
                </tr>
            <?php else: ?>
                <?php foreach ($santriList as $idx => $s): ?>
                    <?php 
                    $idSantri = $s['IdSantri']; 
                    $noPeserta = $s['IdSantri'] ?? '-';
                    $tipeUjian = $tipeUjianMap[$idSantri] ?? 'Utama';
                    ?>



                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="text-center" style="mso-number-format:'\@';"><?= esc($noPeserta) ?></td>
                        <td><?= esc($s['NamaSantri']) ?></td>
                        <td class="text-center"><?= esc($s['NamaKelas'] ?? 'KELAS 6') ?></td>
                        <td class="text-center"><?= esc($s['NamaTpq'] ?? '-') ?></td>
                        <td class="text-center"><?= esc($tipeUjian) ?></td>
                        <?php foreach ($materiList as $m): ?>
                            <?php 
                            $idMateri = $m['IdMateri'];
                            $score = isset($scoresMap[$idSantri][$idMateri]) ? $scoresMap[$idSantri][$idMateri] : null;
                            ?>
                            <td class="text-center fw-bold">
                                <?= $score !== null ? (float)$score : '' ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
