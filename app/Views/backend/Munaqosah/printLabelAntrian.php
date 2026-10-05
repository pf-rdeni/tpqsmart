<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Plang Meja Registrasi & Antrian Ujian'); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.9cm 1.3cm;
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
            border: 4px solid #0f766e;
            border-radius: 12px;
            padding: 14px 20px;
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
            border-bottom: 2.5px double #0f766e;
            padding-bottom: 5px;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-logo {
            width: 70px;
            text-align: left;
        }

        .kop-logo img {
            max-width: 65px;
            max-height: 65px;
        }

        .kop-title {
            text-align: center;
        }

        .kop-title h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-title h3 {
            margin: 2px 0 0 0;
            font-size: 11.5pt;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .kop-title p {
            margin: 1px 0 0 0;
            font-size: 8.5pt;
            color: #4b5563;
        }

        /* Banner Registrasi / Pos Antrian */
        .badge-pos {
            text-align: center;
            background: #f0fdfa;
            border: 2.5px solid #0d9488;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 12px;
        }

        .pos-label {
            font-size: 22pt;
            font-weight: 900;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.2;
        }

        .pos-sub {
            font-size: 10.5pt;
            font-weight: 700;
            color: #334155;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        /* Grup Materi Hero Box (Raksasa & Bersih) */
        .grup-hero {
            text-align: center;
            margin: 10px 0 12px 0;
            padding: 22px 15px;
            background: #f8fafc;
            border: 3.5px solid #1e293b;
            border-radius: 12px;
        }

        .grup-name {
            font-size: 50pt;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 0;
        }

        /* Petunjuk Antrian Peserta */
        .petunjuk-box {
            margin-top: 10px;
            background: #fffbeb;
            border: 1.5px solid #f59e0b;
            border-radius: 8px;
            padding: 8px 16px;
        }

        .petunjuk-title {
            font-size: 9.5pt;
            font-weight: 800;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .petunjuk-list {
            margin: 0;
            padding-left: 20px;
            font-size: 8.5pt;
            color: #78350f;
            line-height: 1.4;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px dotted #cbd5e1;
            font-size: 8pt;
            color: #94a3b8;
            table-layout: fixed;
        }
    </style>
</head>
<body>
    <?php 
    $index = 0;
    $totalGrup = count($grupList);
    foreach ($grupList as $grup): 
        $index++;
        $namaGrup = $grup['NamaMateriGrup'] ?? 'Grup Materi';
    ?>
        <div class="page-card <?= ($index < $totalGrup) ? 'page-break' : ''; ?>">
            <!-- KOP SURAT DINAMIS -->
            <table class="kop-table">
                <tr>
                    <?php if (!empty($logoBase64)): ?>
                        <td class="kop-logo">
                            <img src="<?= $logoBase64; ?>" alt="Logo">
                        </td>
                    <?php endif; ?>
                    <td class="kop-title">
                        <?php if ($typeUjian == 'munaqosah'): ?>
                            <h2>PANITIA PELAKSANA MUNAQOSAH SANTRI TPQ</h2>
                            <h3>FORUM KOMUNIKASI PENDIDIKAN AL-QUR'AN (FKPQ)</h3>
                            <p>Kecamatan Seri Kuala Lobam - Kabupaten Bintan | Tahun Ajaran <?= convertTahunAjaran($idTahunAjaran); ?></p>
                        <?php else: ?>
                            <h2>PANITIA PELAKSANA PRA-MUNAQOSAH SANTRI</h2>
                            <h3><?= !empty($tpqInfo['NamaTpq']) ? esc($tpqInfo['NamaTpq']) : 'LEMBAGA PENDIDIKAN AL-QUR\'AN'; ?></h3>
                            <p><?= !empty($tpqInfo['Alamat']) ? esc($tpqInfo['Alamat']) : 'Kecamatan Seri Kuala Lobam - Kabupaten Bintan'; ?> | Tahun Ajaran <?= convertTahunAjaran($idTahunAjaran); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- BANNER MEJA REGISTRASI / ANTRIAN -->
            <div class="badge-pos">
                <div class="pos-label"><?= esc($labelMeja ?? 'MEJA PENDAFTARAN & ANTRIAN PESERTA'); ?></div>
                <div class="pos-sub">Silakan Serahkan Nomor Tes / Kartu Peserta Untuk Dimasukkan ke Sistem Antrian</div>
            </div>

            <!-- NAMA GRUP MATERI RAKSASA (50pt) -->
            <div class="grup-hero">
                <div class="grup-name"><?= esc($namaGrup); ?></div>
            </div>

            <!-- PETUNJUK TATA TERTIB ANTRIAN -->
            <div class="petunjuk-box">
                <div class="petunjuk-title">Petunjuk & Tata Tertib Antrian Peserta:</div>
                <ol class="petunjuk-list">
                    <li>Peserta wajib menyerahkan <strong>Nomor Tes / Kartu Peserta</strong> kepada Petugas untuk dimasukkan ke dalam sistem antrian.</li>
                    <li>Menunggu panggilan giliran ujian dengan tertib di area ruang tunggu yang telah disediakan.</li>
                    <li>Wajib menjaga ketenangan, adab kesopanan, dan kebersihan di sekitar lokasi pos antrian.</li>
                </ol>
            </div>

            <!-- FOOTER INFO STANDAR -->
            <table class="footer-table">
                <tr>
                    <td style="text-align: left;">Dokumen Resmi Panitia Pelaksana | TPQSmart System</td>
                    <td style="text-align: right;">Dicetak pada: <?= date('d/m/Y H:i'); ?> WIB</td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>
</body>
</html>
