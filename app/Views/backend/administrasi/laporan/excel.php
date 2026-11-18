<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Administrasi Surat</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
        }
        .header-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header-sub {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .meta-table {
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 2px 5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        table.data-table th {
            background-color: #e2efda;
            color: #000;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
        }
        table.data-table td {
            border: 1px solid #000;
            padding: 5px 7px;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            margin-top: 15px;
            font-size: 10pt;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-title">REKAPITULASI ADMINISTRASI SURAT</div>
    <div class="header-sub"><?= esc($namaLembaga) ?></div>

    <table class="meta-table">
        <tr>
            <td><strong>Periode</strong></td>
            <td>: <?= !empty($filters['tanggal_mulai']) ? date('d/m/Y', strtotime($filters['tanggal_mulai'])) : '-' ?> s.d. <?= !empty($filters['tanggal_selesai']) ? date('d/m/Y', strtotime($filters['tanggal_selesai'])) : '-' ?></td>
        </tr>
        <tr>
            <td><strong>Jenis Surat</strong></td>
            <td>: <?= esc($filters['jenis']) ?></td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>: <?= esc($filters['status']) ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Unduh</strong></td>
            <td>: <?= esc($tglCetak) ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 90px;">Jenis Surat</th>
                <th>Nomor Surat</th>
                <th>No Agenda</th>
                <th style="width: 95px;">Tanggal</th>
                <th>Kategori</th>
                <th>Pengirim (Asal)</th>
                <th>Tujuan (Penerima)</th>
                <th>Perihal</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($suratList)): ?>
                <tr>
                    <td colspan="10" class="text-center" style="padding: 15px;">Tidak ada data surat yang ditemukan pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php 
                    $no = 1; 
                    $totalMasuk = 0;
                    $totalKeluar = 0;
                    foreach ($suratList as $s): 
                        if ($s['JenisSurat'] === 'Masuk') $totalMasuk++;
                        if ($s['JenisSurat'] === 'Keluar') $totalKeluar++;
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= esc($s['JenisSurat']) ?></td>
                        <td><?= esc($s['NomorSurat'] ?: '-') ?></td>
                        <td class="text-center"><?= esc($s['NoAgenda'] ?: '-') ?></td>
                        <td class="text-center"><?= !empty($s['Tanggal']) ? date('d/m/Y', strtotime($s['Tanggal'])) : '-' ?></td>
                        <td><?= esc($s['NamaKategori'] ?? 'Umum') ?></td>
                        <td><?= ($s['JenisSurat'] === 'Masuk') ? esc($s['AsalSurat'] ?: '-') : esc($s['NamaTpq'] ?? 'Internal') ?></td>
                        <td><?= ($s['JenisSurat'] === 'Keluar') ? esc($s['TujuanNama'] ?: ($s['TujuanInstansi'] ?? '-')) : esc($s['NamaTpq'] ?? '-') ?></td>
                        <td><?= esc($s['Hal']) ?></td>
                        <td class="text-center"><?= esc($s['Status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary-box">
        <p>Total Arsip: <?= count($suratList) ?> dokumen (Surat Keluar: <?= $totalKeluar ?? 0 ?> | Surat Masuk: <?= $totalMasuk ?? 0 ?>)</p>
    </div>

</body>
</html>
