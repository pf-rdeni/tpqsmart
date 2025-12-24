<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Tidak Berstatus ASN</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-line {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            width: 100%;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            margin: 8px 0;
            text-align: justify;
        }

        .data-row {
            margin: 5px 0;
        }

        .data-label {
            display: inline-block;
            width: 180px;
        }

        .data-value {
            display: inline-block;
        }

        .signature-section {
            margin-top: 50px;
            text-align: right;
        }

        .signature-line {
            margin-top: 60px;
        }

        .materai-container {
            text-align: right;
            margin-top: 25px;
            margin-bottom: 25px;
            padding-right: 100px;
        }

        .signature-container-right {
            text-align: right;
            margin-top: 10px;
        }

        .materai-text {
            font-size: 5px;
            color: #999;
            line-height: 1.2;
            border: 0.3px solid #999;
            padding: 15px 8px;
            display: inline-block;
        }

        @page {
            margin: 20mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SURAT PERNYATAAN TIDAK BERSTATUS ASN</h1>
        <div class="header-line"></div>
    </div>

    <div class="content">
        <p>Yang bertanda tangan dibawah ini :</p>

        <div class="data-row">
            <span class="data-label">Nama</span>
            <span class="data-value">: <?= esc(ucwords(strtolower($guru['Nama'] ?? '-'))) ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">NIK</span>
            <span class="data-value">: <?= esc($guru['IdGuru'] ?? '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Tempat, Tanggal Lahir</span>
            <span class="data-value">: <?= esc(ucwords(strtolower($guru['TempatLahir'] ?? '-'))) ?>, <?= esc($tanggalLahirFormatted ?? '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Penerima Insentif</span>
            <span class="data-value">: <?= esc($guru['JenisPenerimaInsentif'] ?? '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Telp/ Hp</span>
            <span class="data-value">: <?= esc($guru['NoHp'] ?? '-') ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Alamat</span>
            <span class="data-value">: <?= esc(ucwords(strtolower($alamatLengkap ?? '-'))) ?></span>
        </div>

        <p style="margin-top: 20px;">Dengan ini saya menyatakan dengan sesungguhnya bahwa saya tidak berstatus Aparatur Sipil Negara dimanapun.</p>

        <p>Demikian surat pernyataan ini saya buat dengan sebenarnya dan apabila dikemudian hari pernyataan saya tidak benar, saya siap menerima konsekuensinya sesuai dengan peraturan perundang-undangan yang berlaku.</p>
    </div>

    <div class="signature-section">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 33.33%; text-align: left;">
                    <!-- Kolom 1: Kosong -->
                </td>
                <td style="width: 33.33%;">
                    <!-- Kolom 2: Kosong -->
                </td>
                <td style="width: 45%;">
                    <p style="text-align: center;">Seri Kuala Lobam, <?= esc($tanggalSurat) ?></p>
                    <p style="margin-top: 1.5px; text-align: center;">Yang membuat pernyataan</p>
                    <div style="text-align: left; margin-top: 20px;">
                        <span class="materai-text">Materai Rp. 10.000,-</span>
                    </div>
                    <p style="margin-top: 20px; text-align: center;">(<?= esc(ucwords(strtolower($guru['Nama'] ?? ''))) ?>)</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>