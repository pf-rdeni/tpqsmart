<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Verifikasi Data Santri</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #4caf50;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 3px;
        }
        
        .header .subtitle {
            font-size: 9pt;
            color: #666;
        }
        
        .data-section {
            margin: 10px 0;
        }
        
        .data-row {
            margin: 4px 0;
            display: table;
            width: 100%;
        }
        
        .data-label {
            display: table-cell;
            width: 140px;
            font-weight: bold;
            vertical-align: top;
            font-size: 8pt;
            padding-right: 8px;
        }
        
        .data-value {
            display: table-cell;
            vertical-align: top;
            font-size: 8pt;
        }
        
        .section {
            margin-bottom: 10px;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #4caf50;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 2px solid #4caf50;
        }
        
        .step {
            margin-bottom: 6px;
            display: table;
            width: 100%;
        }
        
        .step-number-cell {
            display: table-cell;
            width: 25px;
            vertical-align: top;
            padding-right: 8px;
        }
        
        .step-number {
            width: 20px;
            height: 20px;
            background-color: #4caf50;
            color: white;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            line-height: 20px;
            display: inline-block;
        }
        
        .step-content {
            display: table-cell;
            vertical-align: top;
        }
        
        .step-content h4 {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
        }
        
        .step-content p {
            font-size: 8pt;
            margin-bottom: 3px;
            line-height: 1.4;
        }
        
        .step-content ul {
            margin-left: 15px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        
        .step-content li {
            font-size: 8pt;
            margin-bottom: 1px;
            line-height: 1.4;
        }
        
        .url-box {
            background-color: #e3f2fd;
            border: 1px solid #2196F3;
            border-left: 4px solid #2196F3;
            padding: 5px;
            margin: 5px 0;
            border-radius: 3px;
        }
        
        .url-box strong {
            color: #1565C0;
            font-size: 8pt;
        }
        
        .url-box code {
            font-family: 'Courier New', monospace;
            font-size: 7pt;
            color: #1976D2;
            background-color: #fff;
            padding: 2px 4px;
            border-radius: 2px;
            word-break: break-all;
            display: block;
            margin-top: 3px;
        }
        
        .qr-container {
            display: table;
            width: 100%;
            margin-top: 8px;
        }
        
        .qr-code-cell {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
            padding-right: 10px;
        }
        
        .qr-code-cell img {
            width: 70px;
            height: 70px;
            border: 2px solid #2196F3;
            padding: 3px;
            background-color: #fff;
        }
        
        .qr-text-cell {
            display: table-cell;
            vertical-align: middle;
        }
        
        .highlight-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 5px;
            margin: 5px 0;
            border-radius: 3px;
        }
        
        .highlight-box strong {
            color: #856404;
            font-size: 8pt;
        }
        
        .highlight-box ul {
            margin-left: 15px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        
        .highlight-box li {
            font-size: 8pt;
            margin-bottom: 1px;
            line-height: 1.4;
        }
        
        .warning-box {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            border-left: 4px solid #dc3545;
            padding: 5px;
            margin: 5px 0;
            border-radius: 3px;
        }
        
        .warning-box strong {
            color: #721c24;
            font-size: 8pt;
        }
        
        .warning-box ul {
            margin-left: 15px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        
        .warning-box li {
            font-size: 8pt;
            margin-bottom: 1px;
            line-height: 1.4;
        }
        
        .success-box {
            background-color: #d4edda;
            border: 1px solid #28a745;
            border-left: 4px solid #28a745;
            padding: 5px;
            margin: 5px 0;
            border-radius: 3px;
        }
        
        .success-box strong {
            color: #155724;
            font-size: 8pt;
        }
        
        .success-box ul {
            margin-left: 15px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        
        .success-box li {
            font-size: 8pt;
            margin-bottom: 1px;
            line-height: 1.4;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }
        
        .print-date {
            margin-top: 10px;
            font-size: 7pt;
            color: #666;
            text-align: center;
            line-height: 1.4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        
        table.data-table {
            border: 1px solid #ddd;
        }
        
        table.data-table td {
            padding: 4px 6px;
            font-size: 8pt;
            border: 1px solid #ddd;
        }
        
        table.data-table td:first-child {
            font-weight: bold;
            width: 35%;
            background-color: #f5f5f5;
            color: #555;
        }
        
        table.data-table td:last-child {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PANDUAN VERIFIKASI DATA SANTRI <br> DAN MENGAKSES STATUS MUNAQOSAH DAN KELULUSAN</h1>
        <div class="subtitle">Untuk Santri dan Orang Tua/Wali</div>
    </div>

    <div class="data-section">
        <div style="font-size: 10pt; font-weight: bold; color: #2196F3; margin-bottom: 6px;">Informasi Santri</div>
        <table class="data-table">
            <tr>
                <td>Nama</td>
                <td><?= esc($peserta['NamaSantri'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td><?= esc($peserta['JenisKelamin'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Tempat, Tgl Lahir</td>
                <td>
                    <?= esc($peserta['TempatLahirSantri'] ?? '-') ?>,
                    <?= !empty($peserta['TanggalLahirSantri']) ? formatTanggalIndonesia($peserta['TanggalLahirSantri'], 'd F Y') : '-' ?>
                </td>
            </tr>
            <tr>
                <td>Nama Ayah</td>
                <td><?= esc($peserta['NamaAyah'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>TPQ</td>
                <td><?= isset($tpqData['NamaTpq']) ? ucwords(strtolower($tpqData['NamaTpq'])) : '-' ?></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td><?= convertTahunAjaran($tahunAjaran) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Langkah-langkah Verifikasi</div>
        
        <div class="step">
            <div class="step-number-cell">
                <span class="step-number">1</span>
            </div>
            <div class="step-content">
                <h4>Akses Halaman Verifikasi</h4>
                <p>Buka link verifikasi dengan cara scan QR code di bawah ini.</p>
                <?php if (!empty($statusUrl)): ?>
                <div class="url-box">
                    <strong>Link Verifikasi Anda:</strong>
                    <div class="qr-container">
                        <?php if (!empty($qrCodeBase64)): ?>
                        <div class="qr-code-cell">
                            <img src="<?= esc($qrCodeBase64) ?>" alt="QR Code Verifikasi">
                        </div>
                        <?php endif; ?>
                        <div class="qr-text-cell">
                            <code><?= esc($statusUrl) ?></code>
                            <p style="font-size: 7pt; margin-top: 4px; color: #666;">Scan QR code di atas untuk mengakses halaman verifikasi</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="warning-box">
                    <strong>Catatan:</strong> Link akan diberikan oleh operator/panitia TPQ.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="step">
            <div class="step-number-cell">
                <span class="step-number">2</span>
            </div>
            <div class="step-content">
                <h4>Periksa Data</h4>
                <p>Pastikan data yang ditampilkan (Nama, Jenis Kelamin, Tempat/Tanggal Lahir, Nama Ayah, TPQ) sudah benar.</p>
            </div>
        </div>

        <div class="step">
            <div class="step-number-cell">
                <span class="step-number">3</span>
            </div>
            <div class="step-content">
                <h4>Pilih Tindakan</h4>
                <div class="highlight-box">
                    <strong>Opsi A: Data Benar</strong>
                    <ul>
                        <li>Centang kotak konfirmasi</li>
                        <li>Klik "Ya, Data Benar"</li>
                    </ul>
                </div>
                <div class="warning-box">
                    <strong>Opsi B: Perlu Perbaikan</strong>
                    <ul>
                        <li>Centang kotak konfirmasi</li>
                        <li>Klik "Tidak, Perlu Perbaikan"</li>
                        <li>Isi form perbaikan (hanya field yang salah)</li>
                        <li>Klik "Kirim Permintaan"</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-number-cell">
                <span class="step-number">4</span>
            </div>
            <div class="step-content">
                <h4>Hasil Verifikasi</h4>
                <ul>
                    <li><strong>Data benar:</strong> Status langsung "Valid", dapat melihat status munaqosah dan kelulusan.</li>
                    <li><strong>Perlu perbaikan:</strong> Admin akan meninjau, status menjadi "Valid" setelah dikonfirmasi.</li>
                </ul>
            </div>
        </div>

        <div class="step">
            <div class="step-number-cell">
                <span class="step-number">5</span>
            </div>
            <div class="step-content">
                <h4>Akses Status & Kelulusan</h4>
                <p>Setelah status "Valid", klik tombol "Lihat Status Munaqosah" atau "Lihat Kelulusan" di halaman verifikasi.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Catatan Penting</div>
        <div class="warning-box">
            <strong>Perhatian:</strong>
            <ul>
                <li>Gunakan link resmi dari TPQ, jangan bagikan ke orang lain</li>
                <li>Data yang diverifikasi digunakan untuk munaqosah dan kelulusan</li>
                <li>Ajukan perbaikan segera jika ada kesalahan</li>
            </ul>
        </div>
        <div class="success-box">
            <strong>Tips:</strong>
            <ul>
                <li>Simpan link verifikasi dengan aman</li>
                <li>Periksa data dengan teliti sebelum konfirmasi</li>
                <li>Hubungi operator/panitia TPQ jika butuh bantuan</li>
            </ul>
        </div>
    </div>

    <div class="footer">
    
    <div class="print-date">
        Dicetak pada: <?= esc(formatTanggalIndonesia(date('Y-m-d'), 'd F Y')) ?> <?= esc(date('H:i:s')) ?><br>
        Dokumen ini dihasilkan otomatis dari sistem web informasi https://tpqsmart.simpedis.com
    </div>
</body>
</html>
