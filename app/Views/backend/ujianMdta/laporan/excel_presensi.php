<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presensi Ujian MDTA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000; padding: 6px 8px; font-size: 10pt; }
        th { background-color: #D9E1F2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">DAFTAR PRESENSI UJIAN MDTA</h3>
    <p><b>Nama Ujian:</b> <?= esc($jadwal['NamaUjian']) ?><br>
       <b>Waktu:</b> <?= date('d/m/Y H:i', strtotime($jadwal['TanggalMulai'])) ?></p>

    <table>
        <thead>
            <tr>
                <th width="40">NO</th>
                <th width="120">NIS / NO PESERTA</th>
                <th width="220">NAMA SANTRI</th>
                <th width="150">STATUS SESI</th>
                <th width="150">TANDA TANGAN / KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sesiList as $idx => $s): ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td class="text-center" style="mso-number-format:'\@';"><?= esc($s['IdSantri'] ?? '-') ?></td>

                    <td><?= esc($s['NamaSantri']) ?></td>
                    <td class="text-center"><?= strtoupper(esc($s['StatusSesi'] ?? 'HADIR')) ?></td>
                    <td class="text-center"><?= ($idx + 1) . '. .........' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
