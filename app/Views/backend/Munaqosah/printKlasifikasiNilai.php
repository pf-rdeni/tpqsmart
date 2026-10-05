<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Pedoman Standar Range Nilai Munaqosah'); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
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
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #1e3a8a;
            padding-bottom: 6px;
            margin-bottom: 12px;
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

        /* Tables & Groups */
        .materi-group-block {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .materi-header-banner {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 5px 10px;
            font-size: 10pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
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
        }

        .data-table td {
            padding: 5px 8px;
            font-size: 9pt;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .section-badge {
            background-color: #e0f2fe;
            font-weight: bold;
            color: #0369a1;
            padding: 4px 8px;
        }

        .range-badge {
            font-weight: 800;
            color: #b91c1c;
            text-align: center;
            background-color: #fef2f2;
            border-radius: 4px;
            padding: 2px 4px;
        }

        /* Guidelines Note */
        .notes-box {
            border: 1px solid #93c5fd;
            background-color: #eff6ff;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 14px;
            margin-bottom: 16px;
            font-size: 8.5pt;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .notes-box h5 {
            margin: 0 0 4px 0;
            color: #1e40af;
            font-size: 9pt;
            font-weight: 700;
        }

        .notes-box ol {
            margin: 0;
            padding-left: 18px;
        }

        .notes-box li {
            margin-bottom: 2px;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 15px;
            padding-top: 4px;
            border-top: 1px dotted #94a3b8;
            font-size: 7.5pt;
            color: #64748b;
            table-layout: fixed;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
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
            <h4>PEDOMAN STANDAR KLASIFIKASI & RANGE NILAI KESALAHAN</h4>
            <p>Panduan Resmi Penilaian Dewan Juri Penguji Pelaksanaan Ujian Munaqosah</p>
        </div>

        <!-- TABEL RANGE NILAI PER MATERI PENGUJIAN -->
        <?php if (!empty($kategoriKesalahan)): ?>
            <?php 
            $groupedKategori = [];
            foreach ($kategoriKesalahan as $kk) {
                $materi = !empty($kk['NamaKategoriMateri']) ? $kk['NamaKategoriMateri'] : 'Materi Umum / Lainnya';
                $groupedKategori[$materi][] = $kk;
            }
            ?>

            <?php foreach ($groupedKategori as $namaMateri => $items): ?>
                <div class="materi-group-block">
                    <div class="materi-header-banner">
                        Materi Pengujian: <?= esc($namaMateri); ?>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 25%;">Range Nilai</th>
                                <th style="width: 67%; text-align: left;">Kriteria Poin Penilaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($items as $item): 
                                $rangeText = esc($item['NilaiMin']) . ' - ' . esc($item['NilaiMax']);
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: bold;"><?= $no++; ?></td>
                                    <td style="text-align: center;">
                                        <span class="range-badge"><?= $rangeText; ?></span>
                                    </td>
                                    <td><?= esc($item['NamaKategoriKesalahan']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="materi-group-block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th style="width: 25%;">Range Nilai</th>
                            <th style="width: 67%; text-align: left;">Kriteria Poin Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8; padding: 15px;">
                                Belum ada data kategori kesalahan yang tersimpan dalam sistem.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- CATATAN & PETUNJUK DEWAN JURI -->
        <div class="notes-box">
            <h5><i class="fas fa-info-circle"></i> Ketentuan dan Tata Tertib Penilaian:</h5>
            <ol>
                <li>Dewan Juri wajib melakukan penilaian secara objektif, adil, dan berpedoman pada rentang nilai kesalahan di atas.</li>
                <li>Setiap santri memulai dengan skor dasar maksimal (<strong><?= esc($nilaiMaximal ?? 99); ?> poin</strong>) pada setiap materi uji dengan batas nilai minimal <strong><?= esc($nilaiMinimal ?? 40); ?> poin</strong>.</li>
                <li>Standar threshold kelulusan munaqosah minimal adalah <strong><?= esc($nilaiKelulusan ?? 65); ?> poin</strong>.</li>
                <li>Pengurangan nilai dilakukan berdasarkan akumulasi kesalahan ringan, sedang, maupun berat yang terjadi selama pengujian.</li>
                <li>Nilai akhir diinputkan ke dalam sistem TPQSmart oleh masing-masing juri ruangan.</li>
            </ol>
        </div>

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
                    <div style="font-weight: bold;">Koordinator Dewan Juri</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ............................................ )</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER INFO STANDAR -->
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">Dokumen Resmi Panitia Pelaksana | TPQSmart System</td>
                <td style="text-align: right;">Dicetak pada: <?= date('d/m/Y H:i'); ?> WIB</td>
            </tr>
        </table>
    </div>
</body>
</html>
