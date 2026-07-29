<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ujian MDTA — <?= esc($jadwal['NamaUjian']) ?></title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #198754; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #198754; }
        .header p { margin: 4px 0 0 0; color: #666; font-size: 10pt; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; font-size: 10pt; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ccc; padding: 6px 8px; font-size: 9pt; }
        .data-table th { background-color: #e8f5e9; color: #198754; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-lulus { color: green; font-weight: bold; }
        .badge-gagal { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN HASIL UJIAN ONLINE MDTA</h2>
        <p><?= esc($jadwal['NamaUjian']) ?> | TPQSmart</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Ujian</strong></td>
            <td width="35%">: <?= esc($jadwal['NamaUjian']) ?></td>
            <td width="15%"><strong>Durasi</strong></td>
            <td width="35%">: <?= $jadwal['DurasiMenit'] ?> Menit</td>
        </tr>
        <tr>
            <td><strong>Jumlah Soal</strong></td>
            <td>: <?= $jadwal['JumlahSoal'] ?> Soal</td>
            <td><strong>Nilai KKM</strong></td>
            <td>: <?= $jadwal['NilaiMinimum'] ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Dibuat</strong></td>
            <td>: <?= date('d/m/Y H:i', strtotime($jadwal['created_at'])) ?></td>
            <td><strong>Total Peserta</strong></td>
            <td>: <?= count($sesiList) ?> Santri</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="100">ID Santri</th>
                <th>Nama Santri</th>
                <th width="90" class="text-center">Tipe Ujian</th>
                <th width="120">Waktu Selesai</th>
                <th width="70" class="text-center">Nilai</th>
                <th width="80" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sesiList as $idx => $s): ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td><?= esc($s['IdSantri']) ?></td>
                    <td><?= esc($s['NamaSantri'] ?? 'Santri #' . $s['IdSantri']) ?></td>
                    <td class="text-center">
                        <?= (int)($s['AttemptKe'] ?? 1) === 1 ? 'Ujian Utama' : 'Remedial #' . ((int)$s['AttemptKe'] - 1) ?>
                    </td>
                    <td><?= $s['WaktuSelesai'] ? date('d/m/Y H:i', strtotime($s['WaktuSelesai'])) : '-' ?></td>
                    <td class="text-center font-weight-bold">
                        <?= $s['NilaiAkhir'] !== null ? number_format($s['NilaiAkhir'], 2) : '-' ?>
                    </td>
                    <td class="text-center">
                        <?php if ($s['NilaiAkhir'] !== null): ?>
                            <?php if ($s['NilaiAkhir'] >= $jadwal['NilaiMinimum']): ?>
                                <span class="badge-lulus">LULUS</span>
                            <?php else: ?>
                                <span class="badge-gagal">TIDAK LULUS</span>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
