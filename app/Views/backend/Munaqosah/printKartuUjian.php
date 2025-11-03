<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Ujian Munaqosah</title>
    <style>
        @page {
            size: A4;
            margin: 0.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.2;
        }

        .kartu {
            width: 5.8cm;
            height: 6.5cm;
            border: 2px solid #000;
            padding: 0.2cm;
            margin: 0.1cm;
            display: inline-block;
            vertical-align: top;
            background: white;
            position: relative;
            page-break-inside: avoid;
        }

        .kartu-header {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 0.2cm;
            border-bottom: 1px solid #000;
            padding-bottom: 0.1cm;
        }

        .kartu-content {
            height: calc(100% - 0.5cm);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .nomor-peserta {
            font-size: 40px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0.1cm;
        }

        .qr-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 0.1cm;
        }

        .qr-code {
            width: 40px;
            height: 40px;
            display: inline-block;
            margin: 0 auto;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
        }

        .nama-peserta {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 0.2cm;
            word-wrap: break-word;
        }

        .tabel-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.1cm;
            font-size: 8px;
        }

        .tabel-nilai th,
        .tabel-nilai td {
            border: 1px solid #000;
            padding: 0.05cm;
            text-align: center;
            font-size: 8px;
        }

        .tabel-nilai th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .tabel-nilai td {
            height: 0.8cm;
            vertical-align: middle;
            border: 1px solid #000;
            color: rgba(0, 0, 0, 0.3);
        }

        .footer {
            position: relative;
            width: 100%;
            height: 20px;
            margin-top: 0.2cm;
            font-size: 7.5px;
        }

        .footer-qr {
            position: absolute;
            left: 0;
            top: 70%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-text {
            position: absolute;
            left: 35px;
            top: 50%;
            transform: translateY(-50%);
        }

        .footer-qr img {
            width: 110%;
            height: 110%;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <?php foreach ($peserta as $p): ?>
        <div class="kartu">
            <!-- Header -->
            <div class="kartu-header">
                No peserta <?= $p['TypeUjian'] ?>
            </div>

            <!-- Content -->
            <div class="kartu-content">
                <!-- Nomor Peserta -->
                <div class="nomor-peserta">
                    <?= $p['NoPeserta'] ?>
                </div>

                <!-- QR Code -->
                <div class="qr-wrapper">
                    <div class="qr-code">
                        <?= $p['qrCode'] ?? '<div style="font-size: 8px; text-align: center; border: 1px solid #000; padding: 2px; background: #f0f0f0;">QR<br>' . $p['NoPeserta'] . '</div>' ?>
                    </div>
                </div>

                <!-- Nama Peserta -->
                <div class="nama-peserta">
                    <?= htmlspecialchars($p['NamaSantri']) ?>
                </div>

                <!-- Tabel Nilai -->
                <table class="tabel-nilai">
                    <thead>
                        <tr>
                            <th>Praktek Wudhu</th>
                            <th>Baca Alquran</th>
                            <th>Sholat & Hafalan</th>
                            <th>Tulis Alquran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Paraf</td>
                            <td>Paraf</td>
                            <td>Paraf</td>
                            <td>Paraf</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Footer -->
                <div class="footer">
                    <div class="footer-qr">
                        <?= $p['footerQrCode'] ?? '<div style="font-size: 6px; text-align: center; border: 1px solid #000; padding: 1px; background: #f0f0f0;">QR</div>' ?>
                    </div>
                    <div class="footer-text"> <br> Scan QR untuk hasil ujian:<br>Website: https://tpqsmart.simpedis.com/munaqosah/cek-status<br> HasKey: <b><?= $p['HasKey'] ?></b> </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>