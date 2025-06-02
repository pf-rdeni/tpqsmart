<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rapor Santri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
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
            background-color: #f0f0f0;
        }

        .semester-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <!-- Header Rapor -->
    <div class="header">
        <h2>RAPOR SANTRI</h2>
        <h3><?= $tpq['NamaTpq'] ?></h3>
        <p>Tahun Ajaran <?= $tahunAjaran ?></p>
    </div>

    <!-- Data Santri -->
    <div class="data-santri">
        <table>
            <tr>
                <td width="150">Nama Santri</td>
                <td>: <?= $santri['NamaSantri'] ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: <?= $santri['IdSantri'] ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= $santri['IdKelas'] ?></td>
            </tr>
        </table>
    </div>

    <!-- Nilai Semester -->
    <div class="semester-title">Nilai Semester <?= $semester ?></div>
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
                    <td><?= $n->NamaMateri ?></td>
                    <td><?= $n->Kategori ?></td>
                    <td><?= $n->Nilai ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>