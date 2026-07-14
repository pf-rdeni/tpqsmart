<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemenang Lucky Draw</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 10pt;
            color: #555;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }
        .meta-info td {
            padding: 3px 0;
        }
        .table-pemenang {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        .table-pemenang th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-weight: bold;
            text-align: center;
        }
        .table-pemenang td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
            font-size: 10pt;
        }
        .signature-space {
            height: 70px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Pemenang Lucky Draw</h2>
        <p>Sistem Manajemen Undian Terintegrasi</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%"><strong>Nama Kegiatan</strong></td>
            <td width="3%">:</td>
            <td width="47%"><?= esc($kegiatan->nama_kegiatan) ?></td>
            <td width="15%"><strong>Tanggal Cetak</strong></td>
            <td width="3%">:</td>
            <td width="17%"><?= date('d-m-Y H:i') ?></td>
        </tr>
        <tr>
            <td><strong>Tempat</strong></td>
            <td>:</td>
            <td><?= esc($kegiatan->tempat_pelaksanaan) ?></td>
            <td><strong>Filter Laporan</strong></td>
            <td>:</td>
            <td><?= esc($statusLabel) ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Acara</strong></td>
            <td>:</td>
            <td><?= date('d-m-Y', strtotime($kegiatan->tanggal_kegiatan)) ?></td>
            <td><strong>Total Record</strong></td>
            <td>:</td>
            <td><?= count($pemenang) ?> pemenang</td>
        </tr>
    </table>

    <table class="table-pemenang">
        <thead>
            <tr>
                <th width="8%">No.</th>
                <th width="15%">No. Undian</th>
                <th width="40%">Barang Hadiah / Kategori</th>
                <th width="17%">Status Hadiah</th>
                <th width="20%">Waktu Diambil</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pemenang)): ?>
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #777;">
                        Tidak ada data pemenang yang sesuai dengan filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($pemenang as $p): ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td class="text-center" style="font-weight: bold; font-size: 11pt;"><?= esc($p->no_undian) ?></td>
                        <td>
                            <strong><?= esc($p->nama_barang) ?></strong><br>
                            <span style="font-size: 8.5pt; color: #666;">Kategori: <?= esc($p->kategori) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($p->status_diambil == 1): ?>
                                <span class="badge badge-success">Diambil</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="font-size: 9pt; color: #555;">
                            <?= $p->waktu_diambil ? date('d-m-Y H:i', strtotime($p->waktu_diambil)) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Panitia Pelaksana,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">( ___________________ )</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem Manajemen Undian.
    </div>

</body>
</html>
