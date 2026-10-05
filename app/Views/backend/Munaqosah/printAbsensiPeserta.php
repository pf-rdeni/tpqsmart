<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Absensi Santri dan Orang Tua/Wali'); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.8cm 1.2cm 0.8cm 1.2cm;
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
            font-size: 9.5pt;
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
            margin-bottom: 2px;
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

        .kop-text {
            text-align: center;
        }

        .kop-line-1 {
            font-size: 11pt;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .kop-line-2 {
            font-size: 15pt;
            font-weight: 900;
            color: #002060;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .kop-line-3 {
            font-size: 8.5pt;
            color: #111827;
            margin-bottom: 1px;
        }

        .kop-line-4 {
            font-size: 7.5pt;
            color: #1f2937;
        }

        .kop-divider {
            border-top: 2.5px solid #000000;
            margin-top: 3px;
            margin-bottom: 10px;
        }

        /* Judul Formulir */
        .title-section {
            text-align: center;
            margin-bottom: 12px;
        }

        .title-main {
            font-size: 13.5pt;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.25;
        }

        .title-sub {
            font-size: 11pt;
            font-weight: 500;
            color: #000000;
            margin: 3px 0 0 0;
            line-height: 1.25;
        }

        /* Tabel Absensi */
        .absensi-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1.5px solid #000000;
        }

        .absensi-table thead {
            display: table-header-group;
        }

        .absensi-table tr {
            page-break-inside: avoid;
        }

        .absensi-table th {
            background-color: #ffffff;
            color: #000000;
            font-size: 10.5pt;
            font-weight: 800;
            padding: 6px 4px;
            border: 1.5px solid #000000;
            text-align: center;
            vertical-align: middle;
        }

        .absensi-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 9.5pt;
            vertical-align: middle;
            height: 38px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-no {
            width: 6%;
            text-align: center;
            font-weight: 700;
            font-size: 10pt;
        }

        .col-santri {
            width: 34%;
            font-weight: 600;
            color: #000000;
        }

        .col-ortu {
            width: 38%;
            color: #111827;
            font-size: 9pt;
        }

        .col-paraf-1, .col-paraf-2 {
            width: 11%;
            vertical-align: top !important;
            padding: 2px 4px !important;
        }

        .paraf-num {
            font-size: 8.5pt;
            font-weight: 700;
            color: #000000;
            display: block;
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
            <h1 class="title-main">ABSENSI SANTRI DAN ORANG TUA/WALI</h1>
            <div class="title-sub">
                <?= ($typeUjian == 'munaqosah' ? 'Munaqosah' : 'Pra-Munaqosah'); ?> tahun ajaran <?= convertTahunAjaran($idTahunAjaran); ?>
            </div>
        </div>

        <!-- TABEL ABSENSI DENGAN PARAF ZIG-ZAG BERSILANG -->
        <table class="absensi-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-santri">Nama Santri</th>
                    <th class="col-ortu">Wali Santri (Bapak/Ibu)</th>
                    <th colspan="2" style="width: 22%;">Paraf</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($pesertaList as $santri): 
                    $isGanjil = ($no % 2 !== 0);

                    // Susun nama Bapak dan Ibu secara lengkap
                    $namaAyah = !empty($santri['NamaAyah']) ? trim($santri['NamaAyah']) : '';
                    $namaIbu = !empty($santri['NamaIbu']) ? trim($santri['NamaIbu']) : '';
                    $namaWali = !empty($santri['NamaWali']) ? trim($santri['NamaWali']) : '';
                    $namaKK = !empty($santri['NamaKepalaKeluarga']) ? trim($santri['NamaKepalaKeluarga']) : '';

                    $ortuParts = [];
                    if (!empty($namaAyah)) {
                        $ortuParts[] = 'Bpk. ' . $namaAyah;
                    }
                    if (!empty($namaIbu)) {
                        $ortuParts[] = 'Ibu ' . $namaIbu;
                    }

                    if (!empty($ortuParts)) {
                        $displayOrtu = implode(' / ', $ortuParts);
                    } elseif (!empty($namaWali)) {
                        $displayOrtu = 'Wali: ' . $namaWali;
                    } elseif (!empty($namaKK)) {
                        $displayOrtu = $namaKK;
                    } elseif (!empty($santri['NamaOrtuWali']) && $santri['NamaOrtuWali'] !== '-') {
                        $displayOrtu = $santri['NamaOrtuWali'];
                    } else {
                        $displayOrtu = '';
                    }
                ?>
                    <tr>
                        <td class="col-no"><?= $no; ?></td>
                        <td class="col-santri"><?= esc($santri['NamaSantri'] ?? ''); ?></td>
                        <td class="col-ortu"><?= esc($displayOrtu); ?></td>
                        
                        <?php if ($isGanjil): ?>
                            <!-- Baris Ganjil: Angka nomor paraf di kolom kanan -->
                            <td class="col-paraf-1"></td>
                            <td class="col-paraf-2">
                                <span class="paraf-num"><?= $no; ?></span>
                            </td>
                        <?php else: ?>
                            <!-- Baris Genap: Angka nomor paraf di kolom kiri -->
                            <td class="col-paraf-1">
                                <span class="paraf-num"><?= $no; ?></span>
                            </td>
                            <td class="col-paraf-2"></td>
                        <?php endif; ?>
                    </tr>
                <?php 
                    $no++;
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
