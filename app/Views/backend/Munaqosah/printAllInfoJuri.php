<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Login Juri Munaqosah</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 3px;
        }

        .info-box {
            background-color: #f9f9f9;
            border: 2px solid #333;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #333;
            font-size: 12px;
        }

        .info-value {
            flex: 1;
            font-size: 12px;
            color: #000;
            font-weight: bold;
        }

        .password-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 5px;
            padding: 12px;
            margin: 12px 0;
            text-align: center;
        }

        .password-box .label {
            font-size: 11px;
            color: #856404;
            margin-bottom: 5px;
        }

        .password-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #856404;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .instructions {
            background-color: #e7f3ff;
            border: 2px solid #0066cc;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }

        .instructions h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 3px;
        }

        .instructions ol {
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            line-height: 1.5;
            font-size: 11px;
        }

        .instructions li strong {
            color: #0066cc;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .info-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <?php foreach ($juriList as $index => $item): ?>
        <?php 
        $juri = $item['juri'];
        $password = $item['password'];
        ?>
        <div class="container">
            <div class="header">
                <h1>INFORMASI LOGIN JURI <?= (!empty($juri['IdTpq']) && $juri['TypeUjian'] == 'pra-munaqosah') ? 'PRA MUNAQOSAH' : 'MUNAQOSAH' ?></h1>
                <h2>Sistem Informasi Munaqosah TPQ Smart</h2>
            </div>

            <div class="info-section">
                <h3>Data Akun Juri</h3>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">ID Juri:</span>
                        <span class="info-value"><?= esc($juri['IdJuri']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Username:</span>
                        <span class="info-value"><?= esc($juri['UsernameJuri']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Grup Materi:</span>
                        <span class="info-value"><?= esc($juri['NamaMateriGrup'] ?? '-') ?></span>
                    </div>
                    <?php if (!empty($juri['NamaTpq'])): ?>
                    <div class="info-row">
                        <span class="info-label">TPQ:</span>
                        <span class="info-value"><?= esc($juri['NamaTpq']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($juri['RoomId'])): ?>
                    <div class="info-row">
                        <span class="info-label">Room ID:</span>
                        <span class="info-value"><?= esc($juri['RoomId']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="password-box">
                    <div class="label">PASSWORD LOGIN</div>
                    <div class="value"><?= esc($password) ?></div>
                </div>
            </div>

            <div class="instructions">
                <h3>Tata Cara Login</h3>
                <ol>
                    <li>
                        <strong>Akses Website:</strong> Buka browser dan kunjungi <strong style="font-family: 'Courier New', monospace; color: #0066cc;">https://tpqsmart.simpedis.com</strong>
                    </li>
                    <li>
                        <strong>Login:</strong> Masukkan username dan password pada halaman login:
                        <br>
                        <strong>Username:</strong> <span style="font-family: 'Courier New', monospace; color: #0066cc;"><?= esc($juri['UsernameJuri']) ?></span>
                        <br>
                        <strong>Password:</strong> <span style="font-family: 'Courier New', monospace; color: #0066cc;"><?= esc($password) ?></span>
                    </li>
                    <li>
                        <strong>Klik Login:</strong> Setelah data diisi, klik tombol <strong>"Login"</strong> untuk masuk ke dashboard juri munaqosah.
                    </li>
                </ol>
            </div>

            <div class="footer">
                <p>Dokumen ini dicetak pada: <?= date('d F Y H:i:s') ?></p>
                <p>Simpan dokumen ini dengan baik dan jangan bagikan informasi login kepada pihak yang tidak berwenang.</p>
            </div>
        </div>
        
        <?php if ($index < count($juriList) - 1): ?>
            <div style="page-break-after: always;"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</body>

</html>

