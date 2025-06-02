<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Rapor Santri</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 16px;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .data-santri {
            margin-bottom: 20px;
        }

        .data-santri table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-santri td {
            padding: 3px;
        }

        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .nilai-table th {
            background-color: #f2f2f2;
        }

        .semester-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
        }
    </style>
</head>

<body>
    <!-- Header Rapor -->
    <div class="header">
        <h2>RAPOR SANTRI</h2>
        <h3><?= htmlspecialchars($tpq['NamaTpq'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p>Tahun Ajaran <?= htmlspecialchars($tahunAjaran, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <!-- Data Santri -->
    <div class="data-santri">
        <table>
            <tr>
                <td width="150">Nama Santri</td>
                <td>: <?= htmlspecialchars($santri['NamaSantri'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: <?= htmlspecialchars($santri['IdSantri'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= htmlspecialchars($santri['IdKelas'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>
    </div>

    <!-- Nilai Semester -->
    <div class="semester-title">Nilai Semester <?= htmlspecialchars($semester, ENT_QUOTES, 'UTF-8') ?></div>
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Materi</th>
                <th width="25%">Kategori</th>
                <th width="25%">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($nilai as $n) :
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($n->NamaMateri, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($n->Kategori, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($n->Nilai, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>