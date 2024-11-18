<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Santri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .foto-santri {
            width: 150px;
            height: auto;
            margin: 10px auto;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            width: 30%;
        }

        .section-title {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DATA SANTRI</h2>
    </div>

    <?php if ($fotoSantri): ?>
        <img src="<?= $fotoSantri ?>" class="foto-santri">
    <?php endif; ?>

    <!-- Data Santri -->
    <div class="section-title">Data Pribadi Santri</div>
    <table>
        <tr>
            <th>Nama TPQ</th>
            <td><?= $data['printIdTpq'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Nama Kelas</th>
            <td><?= $data['printIdKelas'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>NIK Santri</th>
            <td><?= $data['printNikSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>No. KK Santri</th>
            <td><?= $data['printNoKkSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Nama Santri</th>
            <td><?= $data['printNamaSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Tempat, Tanggal Lahir</th>
            <td><?= $data['printTempatTTL'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Jenis Kelamin</th>
            <td><?= $data['printJenisKelamin'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Anak Ke</th>
            <td><?= $data['printAnakKe'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Jumlah Saudara</th>
            <td><?= $data['printJumlahSaudara'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Hobi</th>
            <td><?= $data['printHobi'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Cita-cita</th>
            <td><?= $data['printCitaCita'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Nama Ayah</th>
            <td><?= $data['printNamaAyah'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Nama Ibu</th>
            <td><?= $data['printNamaIbu'] ?? '-' ?></td>
        </tr>
    </table>

    <!-- Data Alamat -->
    <div class="section-title">Data Alamat</div>
    <table>
        <tr>
            <th>Alamat Santri</th>
            <td><?= $data['printAlamatSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>RT</th>
            <td><?= $data['printRTSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>RW</th>
            <td><?= $data['printRWSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Desa/Kelurahan</th>
            <td><?= $data['printKelurahanDesaSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Kecamatan</th>
            <td><?= $data['printKecamatanSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Kabupaten/Kota</th>
            <td><?= $data['printKabupatenKotaSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Provinsi</th>
            <td><?= $data['printProvinsiSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Kode Pos</th>
            <td><?= $data['printKodePosSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Jarak Ke Lembaga</th>
            <td><?= $data['printJarakTempuhSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Transportasi</th>
            <td><?= $data['printTransportasiSantri'] ?? '-' ?></td>
        </tr>
        <tr>
            <th>Waktu Tempuh</th>
            <td><?= $data['printWaktuTempuhSantri'] ?? '-' ?></td>
        </tr>

    </table>
</body>

</html>