<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Rekapitulasi Penempatan Juri dan Ruangan'); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 0;
            background-color: #fff;
            font-size: 9.5pt;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #1e3a8a;
            padding-bottom: 6px;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .kop-table td {
            text-align: center;
            vertical-align: middle;
        }

        .kop-title h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .kop-title h3 {
            margin: 2px 0 0 0;
            font-size: 10.5pt;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
        }

        .kop-title p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #4b5563;
        }

        /* Document Header */
        .doc-header {
            text-align: center;
            margin: 10px 0 14px 0;
        }

        .doc-header h4 {
            margin: 0;
            font-size: 12pt;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-header p {
            margin: 3px 0 0 0;
            font-size: 8.5pt;
            color: #64748b;
        }

        /* Summary Stats */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            table-layout: fixed;
        }

        .stats-table td {
            padding: 8px 12px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }

        .stats-table td:last-child {
            border-right: none;
        }

        .stats-num {
            font-size: 14pt;
            font-weight: 800;
            color: #1e3a8a;
        }

        .stats-lbl {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 14px;
        }

        .data-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 9pt;
            font-weight: 700;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            overflow: hidden;
            word-wrap: break-word;
        }

        .data-table td {
            padding: 6px 8px;
            font-size: 9pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            overflow: hidden;
            word-wrap: break-word;
        }

        .room-tag {
            font-weight: 900;
            color: #14532d;
            background: #dcfce7;
            border: 1.5px solid #86efac;
            padding: 3px 6px;
            border-radius: 4px;
            display: inline-block;
            font-size: 12pt;
            letter-spacing: 0.5px;
        }

        .materi-tag {
            font-weight: 700;
            color: #1e40af;
        }

        .juri-item {
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px dotted #e2e8f0;
        }

        .juri-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .sig-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }

        .sig-space {
            height: 50px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT DINAMIS -->
        <table class="kop-table">
            <tr>
                <td class="kop-title">
                    <?php if ($typeUjian == 'munaqosah'): ?>
                        <h2>PANITIA PELAKSANA MUNAQOSAH SANTRI TPQ</h2>
                        <h3>FORUM KOMUNIKASI PENDIDIKAN AL-QUR'AN (FKPQ)</h3>
                        <p>Kecamatan Seri Kuala Lobam - Kabupaten Bintan | Tahun Ajaran <?= esc($idTahunAjaran); ?></p>
                    <?php else: ?>
                        <h2>PANITIA PELAKSANA PRA-MUNAQOSAH SANTRI</h2>
                        <h3><?= !empty($tpqInfo['NamaTpq']) ? esc($tpqInfo['NamaTpq']) : 'LEMBAGA PENDIDIKAN AL-QUR\'AN'; ?></h3>
                        <p><?= !empty($tpqInfo['Alamat']) ? esc($tpqInfo['Alamat']) : 'Kecamatan Seri Kuala Lobam - Kabupaten Bintan'; ?> | Tahun Ajaran <?= esc($idTahunAjaran); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-header">
            <h4>REKAPITULASI PEMETAAN RUANGAN & PENEMPATAN DEWAN JURI</h4>
            <p>Daftar Distribusi Penguji Ujian Munaqosah Berdasarkan Ruangan dan Materi Uji</p>
        </div>

        <!-- RINGKASAN STATISTIK -->
        <?php 
        $totalJuri = 0;
        foreach ($ruanganList as $r) {
            $totalJuri += count($r['JuriList']);
        }
        ?>
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-num"><?= count($ruanganList); ?></div>
                    <div class="stats-lbl">Total Ruangan</div>
                </td>
                <td>
                    <div class="stats-num"><?= $totalJuri; ?></div>
                    <div class="stats-lbl">Total Juri Terplot</div>
                </td>
                <td>
                    <div class="stats-num"><?= count($materiList); ?></div>
                    <div class="stats-lbl">Materi Ujian</div>
                </td>
            </tr>
        </table>

        <!-- TABEL PEMETAAN RUANGAN -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 15%;">Ruangan</th>
                    <th style="width: 25%; text-align: left;">Materi Ujian</th>
                    <th style="width: 34%; text-align: left;">Dewan Juri Penguji</th>
                    <th style="width: 20%; text-align: left;">Asal Lembaga</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ruanganList)): ?>
                    <?php 
                    $no = 1;
                    foreach ($ruanganList as $roomNo => $rData): 
                        $juriList = $rData['JuriList'];
                    ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?= $no++; ?></td>
                            <td style="text-align: center;">
                                <span class="room-tag"><?= esc($roomNo); ?></span>
                            </td>
                            <td>
                                <div class="materi-tag"><?= esc($rData['NamaMateriGrup']); ?></div>
                            </td>
                            <td>
                                <?php if (!empty($juriList)): ?>
                                    <?php foreach ($juriList as $j): ?>
                                        <div class="juri-item">
                                            <strong><?= esc($j['NamaJuri'] ?? $j['UsernameJuri']); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-style: italic;">(Belum ada juri terplot)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($juriList)): ?>
                                    <?php foreach ($juriList as $j): ?>
                                        <div class="juri-item">
                                            <?= esc($j['NamaTpq'] ?? '-'); ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 15px;">
                            Belum ada data pemetaan ruangan juri.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TANDA TANGAN RESMI -->
        <table class="sig-table">
            <tr>
                <td>
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold;">Ketua Panitia Pelaksana</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                </td>
                <td>
                    <div>Seri Kuala Lobam, ..................... <?= date('Y'); ?></div>
                    <div style="font-weight: bold;">Sekretaris Panitia</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
