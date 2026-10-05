<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Label Pintu Ruangan Munaqosah'); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.5cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page-card {
            border: 4px solid #1e3a8a;
            border-radius: 12px;
            padding: 12px 18px;
            background: #ffffff;
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px double #1e3a8a;
            padding-bottom: 4px;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        .kop-table td {
            vertical-align: middle;
            text-align: center;
        }

        .kop-title h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-title h3 {
            margin: 2px 0 0 0;
            font-size: 11.5pt;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
        }

        .kop-title p {
            margin: 1px 0 0 0;
            font-size: 8.5pt;
            color: #4b5563;
        }

        /* Focus Room Section */
        .room-hero {
            text-align: center;
            margin: 6px 0 8px 0;
            padding: 8px 12px;
            background: #f0fdf4;
            border: 3px dashed #16a34a;
            border-radius: 10px;
        }

        .room-label {
            font-size: 13pt;
            font-weight: 800;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 2px;
        }

        .room-number {
            font-size: 150pt;
            font-weight: 900;
            color: #166534;
            line-height: 1.0;
            letter-spacing: 2px;
            margin: 0;
            text-shadow: 2px 2px 0px #bbf7d0;
        }

        /* Materi Box */
        .materi-box {
            text-align: center;
            background: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 6px 12px;
            margin-bottom: 8px;
        }

        .materi-label {
            font-size: 20pt;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .materi-name {
            font-size: 70pt;
            font-weight: 900;
            color: #1e3a8a;
            margin-top: 1px;
        }

        /* Juri Table */
        .juri-section {
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .juri-header {
            font-size: 10pt;
            font-weight: 800;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #9ca3af;
            padding-bottom: 2px;
            margin-bottom: 4px;
            text-align: center;
        }

        .juri-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .juri-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-size: 9pt;
            font-weight: 700;
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .juri-table td {
            padding: 4px 8px;
            font-size: 10pt;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
        }

        .juri-name {
            font-weight: 800;
            color: #111827;
            font-size: 10.5pt;
        }

        .juri-username {
            font-size: 8pt;
            color: #6b7280;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }

        .juri-tpq {
            font-weight: 600;
            color: #374151;
            font-size: 10pt;
        }

        /* Footer */
        .footer-note {
            width: 100%;
            margin-top: 4px;
            padding-top: 2px;
            border-top: 1px dotted #cbd5e1;
            font-size: 7.5pt;
            color: #9ca3af;
            table-layout: fixed;
        }
    </style>
</head>
<body>
    <?php 
    $index = 0;
    $totalRooms = count($ruanganList);
    foreach ($ruanganList as $roomNo => $rData): 
        $index++;
        $hasJuri = !empty($rData['JuriList']);
    ?>
        <div class="page-card <?= ($index < $totalRooms) ? 'page-break' : ''; ?>">
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

            <!-- FOKUS NOMOR RUANGAN RAKSASA (80pt) -->
            <div class="room-hero">
                <div class="room-label">-- RUANGAN UJIAN / ROOM --</div>
                <div class="room-number"><?= esc($roomNo); ?></div>
            </div>

            <!-- MATERI UJIAN BESAR -->
            <div class="materi-box">
                <div class="materi-label">Materi Yang Diujikan:</div>
                <div class="materi-name">
                    <?= !empty($rData['NamaMateriGrup']) ? esc($rData['NamaMateriGrup']) : 'Ujian Munaqosah'; ?>
                </div>
            </div>

            <!-- DEWAN JURI PENGUJI (HANYA DITAMPILKAN JIKA ADA JURI) -->
            <?php if ($hasJuri): ?>
                <div class="juri-section">
                    <div class="juri-header">DEWAN JURI / PENGUJI RUANGAN</div>
                    <table class="juri-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 55%; text-align: left;">Nama Penguji / Juri</th>
                                <th style="width: 37%; text-align: left;">Asal Lembaga / TPQ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $noJuri = 1;
                            foreach ($rData['JuriList'] as $juri): 
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: bold;"><?= $noJuri++; ?></td>
                                    <td>
                                        <div class="juri-name"><?= esc($juri['NamaJuri'] ?? $juri['UsernameJuri']); ?></div>
                                        <?php if ($showUsername && !empty($juri['UsernameJuri'])): ?>
                                            <div class="juri-username"><small>ID: @<?= esc($juri['UsernameJuri']); ?></small></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="juri-tpq"><?= esc($juri['NamaTpq'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- FOOTER INFO -->
            <table class="footer-note">
                <tr>
                    <td style="text-align: left; width: 50%;">TPQSmart System - Label Ruangan Munaqosah</td>
                    <td style="text-align: right; width: 50%;">Tahun Ajaran: <?= esc($idTahunAjaran); ?> | Dokumen Resmi Panitia</td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>
</body>
</html>
