<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Disposisi - <?= esc($surat['NoAgenda'] ?? 'Surat Masuk') ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.4;
            padding: 10px;
        }

        .header-title {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-title h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-title p {
            margin: 3px 0 0;
            font-size: 10pt;
            color: #333;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-data td, .table-data th {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: top;
        }

        .label-cell {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 150px;
        }

        .box-instruksi {
            border: 1px solid #000;
            padding: 10px;
            min-height: 140px;
            margin-bottom: 15px;
        }

        .ttd-box {
            float: right;
            text-align: center;
            width: 220px;
            margin-top: 20px;
        }

        .ttd-space {
            height: 60px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 18px; font-weight: bold; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak Lembar Disposisi
        </button>
        <button onclick="window.close()" style="padding: 8px 18px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="header-title">
        <h2>LEMBAR DISPOSISI</h2>
        <p><?= esc($surat['NamaTpq'] ?? 'LEMBAGA PENDIDIKAN AL-QUR\'AN') ?></p>
        <?php if (!empty($surat['AlamatTpq'])): ?>
            <p style="font-size: 8pt;"><?= esc($surat['AlamatTpq']) ?></p>
        <?php endif; ?>
    </div>

    <table class="table-data">
        <tr>
            <td class="label-cell">Surat Dari</td>
            <td colspan="3"><strong><?= esc($surat['AsalSurat'] ?: '-') ?></strong></td>
        </tr>
        <tr>
            <td class="label-cell">Nomor Surat</td>
            <td><?= esc($surat['NomorSurat'] ?: '-') ?></td>
            <td class="label-cell" style="width: 130px;">Tanggal Surat</td>
            <td><?= !empty($surat['Tanggal']) ? date('d-m-Y', strtotime($surat['Tanggal'])) : '-' ?></td>
        </tr>
        <tr>
            <td class="label-cell">No. Agenda</td>
            <td><strong style="font-size: 12pt;"><?= esc($surat['NoAgenda'] ?: '-') ?></strong></td>
            <td class="label-cell">Diterima Tanggal</td>
            <td><?= !empty($surat['TanggalTerima']) ? date('d-m-Y', strtotime($surat['TanggalTerima'])) : '-' ?></td>
        </tr>
        <tr>
            <td class="label-cell">Sifat / Prioritas</td>
            <td colspan="3">
                [ ] Sangat Segera &nbsp;&nbsp;&nbsp;&nbsp; [ ] Segera &nbsp;&nbsp;&nbsp;&nbsp; [✓] Biasa
            </td>
        </tr>
        <tr>
            <td class="label-cell">Perihal</td>
            <td colspan="3"><strong><?= esc($surat['Hal'] ?: '-') ?></strong></td>
        </tr>
    </table>

    <table class="table-data" style="margin-top: 10px;">
        <tr style="background: #f2f2f2;">
            <th style="width: 50%;">DITERUSKAN KEPADA</th>
            <th style="width: 50%;">INSTRUKSI / DISPOSISI PIMPINAN</th>
        </tr>
        <tr>
            <td style="min-height: 200px; vertical-align: top;">
                <?php if (!empty($disposisiList)): ?>
                    <ol style="margin: 0; padding-left: 20px;">
                        <?php foreach ($disposisiList as $d): ?>
                            <li style="margin-bottom: 6px;">
                                <strong><?= esc($d['KeUsername'] ?? 'Staf') ?></strong>
                                <?php if (!empty($d['Deadline'])): ?>
                                    <span style="font-size: 9pt; color: #d00;">(Deadline: <?= date('d-m-Y', strtotime($d['Deadline'])) ?>)</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p style="margin: 0; color: #777;">1. __________________________________<br><br>2. __________________________________<br><br>3. __________________________________</p>
                <?php endif; ?>
            </td>
            <td style="min-height: 200px; vertical-align: top;">
                <?php if (!empty($disposisiList)): ?>
                    <?php foreach ($disposisiList as $d): ?>
                        <div style="margin-bottom: 10px; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">
                            <em>"<?= nl2br(esc($d['Instruksi'])) ?>"</em>
                            <div style="font-size: 8pt; color: #555; margin-top: 3px;">
                                Oleh: <?= esc($d['DariUsername'] ?? 'Pimpinan') ?> (<?= date('d-m-Y', strtotime($d['CreatedAt'])) ?>)
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="margin: 0; color: #777;">
                        [ ] Tindak lanjuti / Pelajari<br>
                        [ ] Siapkan konsep jawaban<br>
                        [ ] Hadiri / Wakili<br>
                        [ ] Koordinasikan dengan pihak terkait<br>
                        [ ] Arsipkan<br><br>
                        Catatan: _________________________________
                    </p>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div class="ttd-box">
        <div>Bintan, <?= date('d F Y') ?></div>
        <div style="font-weight: bold; margin-top: 2px;">Kepala Lembaga</div>
        <div class="ttd-space"></div>
        <div style="font-weight: bold; text-decoration: underline;">( ______________________ )</div>
    </div>

</body>
</html>
