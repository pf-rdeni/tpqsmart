<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Sesi Ujian MDTA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000; padding: 6px 8px; font-size: 10pt; }
        th { background-color: #E2EFDA; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">REKAPITULASI SESI UJIAN MDTA</h3>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA SANTRI</th>
                <th>NIS</th>
                <th>UJIAN / MATERI</th>
                <th>KELAS</th>
                <th>LEMBAGA (TPQ)</th>
                <th>WAKTU MULAI</th>
                <th>WAKTU SELESAI</th>
                <th>STATUS</th>
                <th>NILAI AKHIR</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sesiList as $idx => $s): ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td><?= esc($s['NamaSantri']) ?></td>
                    <td class="text-center" style="mso-number-format:'\@';"><?= esc($s['IdSantri'] ?? '-') ?></td>


                    <td><?= esc($s['NamaUjian'] ?? '-') ?></td>
                    <td class="text-center"><?= esc($s['NamaKelas'] ?? '-') ?></td>
                    <td><?= esc($s['NamaTpq'] ?? '-') ?></td>
                    <td class="text-center"><?= !empty($s['WaktuMulai']) ? date('d/m/Y H:i', strtotime($s['WaktuMulai'])) : '-' ?></td>
                    <td class="text-center"><?= !empty($s['WaktuSelesai']) ? date('d/m/Y H:i', strtotime($s['WaktuSelesai'])) : '-' ?></td>
                    <td class="text-center"><?= strtoupper(esc($s['StatusSesi'] ?? 'BELUM')) ?></td>
                    <td class="text-center" style="font-weight:bold;"><?= $s['NilaiAkhir'] !== null ? number_format((float)$s['NilaiAkhir'], 2) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
