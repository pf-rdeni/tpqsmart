<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Formulir Penilaian Praktek Tulis Ayat Al-Quran'); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.6cm 1.4cm 0.6cm 1.4cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000000;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-logo {
            width: 65px;
            text-align: left;
        }

        .kop-logo img {
            max-width: 60px;
            max-height: 60px;
        }

        .kop-text {
            text-align: center;
        }

        .kop-line-1 {
            font-size: 10.5pt;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .kop-line-2 {
            font-size: 14pt;
            font-weight: 900;
            color: #002060;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .kop-line-3 {
            font-size: 8pt;
            color: #111827;
            margin-bottom: 1px;
        }

        .kop-line-4 {
            font-size: 7.5pt;
            color: #1f2937;
        }

        .kop-divider {
            border-top: 2px solid #000000;
            margin-top: 2px;
            margin-bottom: 6px;
        }

        /* Judul Formulir */
        .title-section {
            text-align: center;
            margin-bottom: 8px;
        }

        .title-1 {
            font-size: 12pt;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .title-2 {
            font-size: 12pt;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1px 0 0 0;
            line-height: 1.2;
        }

        .title-sub {
            font-size: 10pt;
            font-weight: 500;
            color: #000000;
            margin: 1px 0 0 0;
            line-height: 1.2;
        }

        /* Biodata Peserta */
        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10pt;
            font-weight: 600;
            color: #000000;
        }

        .bio-table td {
            padding: 1px 0;
            vertical-align: middle;
        }

        .bio-label {
            width: 70px;
        }

        .bio-colon {
            width: 16px;
        }

        .bio-value {
            /* Kosong untuk diisi */
        }

        /* Area Menulis - 17 Garis Line Lebih Besar */
        .write-lines-container {
            width: 100%;
            margin-bottom: 14px;
        }

        .write-line-row {
            width: 100%;
            height: 50px;
            border-bottom: 1.2px solid #000000;
        }

        /* Paraf Juri Dinamis */
        .paraf-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        .paraf-table td {
            text-align: center;
            vertical-align: top;
            padding: 0 15px;
        }

        .paraf-title {
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 30px;
        }

        .paraf-name {
            font-size: 9pt;
            font-weight: 600;
            color: #000000;
            border-top: 1px dotted #000000;
            display: inline-block;
            min-width: 180px;
            padding-top: 10px;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 8px;
            padding-top: 3px;
            border-top: 1px dotted #94a3b8;
            font-size: 7.5pt;
            color: #64748b;
            table-layout: fixed;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT DINAMIS -->
        <table class="kop-table">
            <tr>
                <?php if (!empty($logoBase64)): ?>
                    <td class="kop-logo">
                        <img src="<?= $logoBase64; ?>" alt="Logo">
                    </td>
                <?php endif; ?>
                <td class="kop-text">
                    <?php if ($typeUjian == 'munaqosah'): ?>
                        <div class="kop-line-1">PANITIA PELAKSANA MUNAQOSAH SANTRI TPQ</div>
                        <div class="kop-line-2">FORUM KOMUNIKASI PENDIDIKAN AL-QUR'AN (FKPQ)</div>
                        <div class="kop-line-3">Kecamatan Seri Kuala Lobam - Kabupaten Bintan</div>
                    <?php else: ?>
                        <?php if (!empty($tpqInfo) && !empty($tpqInfo['NamaTpq'])): ?>
                            <div class="kop-line-1">TAMAN PENDIDIKAN AL -QUR’AN (TPQ)</div>
                            <div class="kop-line-2"><?= esc($tpqInfo['NamaTpq']); ?></div>
                            <?php if (!empty($tpqInfo['Alamat'])): ?>
                                <div class="kop-line-3"><?= esc($tpqInfo['Alamat']); ?></div>
                            <?php endif; ?>
                            <?php 
                            $kontakParts = [];
                            if (!empty($tpqInfo['NoHp'])) $kontakParts[] = 'Hp. ' . esc($tpqInfo['NoHp']);
                            if (!empty($tpqInfo['Email'])) $kontakParts[] = 'E-Mail: ' . esc($tpqInfo['Email']);
                            if (!empty($tpqInfo['NoIzinOperasional'])) $kontakParts[] = 'Izin Operasional: ' . esc($tpqInfo['NoIzinOperasional']);
                            if (!empty($tpqInfo['NoStatistik'])) $kontakParts[] = 'No Statistik: ' . esc($tpqInfo['NoStatistik']);
                            ?>
                            <?php if (!empty($kontakParts)): ?>
                                <div class="kop-line-4"><?= implode(' | ', $kontakParts); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="kop-line-1">PANITIA PELAKSANA PRA-MUNAQOSAH SANTRI</div>
                            <div class="kop-line-2">LEMBAGA PENDIDIKAN AL-QUR'AN (LPQ / TPQ)</div>
                            <div class="kop-line-3">Kecamatan Seri Kuala Lobam - Kabupaten Bintan</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <div class="kop-divider"></div>

        <!-- JUDUL FORMULIR -->
        <div class="title-section">
            <h1 class="title-1">FORMULIR PENILAIAN</h1>
            <h2 class="title-2">PRAKTEK TULIS AYAT AL-QURAN</h2>
            <div class="title-sub">
                <?= ($typeUjian == 'munaqosah' ? 'Munaqosah' : 'Pra-Munaqosah'); ?> tahun ajaran <?= convertTahunAjaran($idTahunAjaran); ?>
            </div>
        </div>

        <!-- BIODATA PESERTA -->
        <table class="bio-table">
            <tr>
                <td class="bio-label">No Uji</td>
                <td class="bio-colon">:</td>
                <td class="bio-value"></td>
            </tr>
            <tr>
                <td class="bio-label">Nama</td>
                <td class="bio-colon">:</td>
                <td class="bio-value"></td>
            </tr>
        </table>

        <!-- AREA MENULIS AYAT (15 GARIS LINE POLOS TANPA NOMOR) -->
        <div class="write-lines-container">
            <?php for ($i = 1; $i <= 15; $i++): ?>
                <div class="write-line-row"></div>
            <?php endfor; ?>
        </div>

        <!-- PARAF JURI DINAMIS SESUAI JUMLAH JURI -->
        <?php 
        $countJuri = count($juriList);
        $colWidth = ($countJuri > 0) ? floor(100 / $countJuri) . '%' : '50%';
        ?>
        <table class="paraf-table">
            <tr>
                <?php 
                $juriIdx = 1;
                foreach ($juriList as $juri): 
                    $namaJuri = !empty($juri['NamaJuri']) ? esc($juri['NamaJuri']) : '................................................';
                ?>
                    <td style="width: <?= $colWidth; ?>;">
                        <div class="paraf-title">Paraf Juri <?= ($countJuri > 1) ? $juriIdx : ''; ?></div>
                        <div class="paraf-name">( <?= $namaJuri; ?> )</div>
                    </td>
                <?php 
                    $juriIdx++;
                endforeach; 
                ?>
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
