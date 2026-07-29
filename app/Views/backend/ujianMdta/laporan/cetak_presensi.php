<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir / Presensi Ujian</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11pt; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h3 { margin: 0; font-size: 14pt; }
        .header p { margin: 3px 0 0 0; font-size: 10pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 7px 10px; font-size: 10pt; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h3>DAFTAR HADIR PESERTA UJIAN MDTA</h3>
        <p><strong>Ujian:</strong> <?= esc($jadwal['NamaUjian']) ?> | <strong>KKM:</strong> <?= number_format($jadwal['Nilminimum'] ?? 70, 2) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="120">No. Peserta / NIS</th>
                <th>Nama Santri</th>
                <th width="100">Status</th>
                <th width="140">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sesiList)): ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada peserta terdaftar</td>
                </tr>
            <?php else: ?>
                <?php foreach ($sesiList as $idx => $s): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="text-center"><?= esc($s['IdSantri'] ?? '-') ?></td>


                        <td><?= esc($s['NamaSantri']) ?></td>
                        <td class="text-center"><?= strtoupper(esc($s['StatusSesi'] ?? 'Hadir')) ?></td>
                        <td><?= ($idx + 1) ?>. .......................</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
