<?php
helper('nilai');
?>
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
            font-size: 10px;
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
        <div style="background-color: red; color: white; padding: 10px; text-align: center;">
            Dokumen ini masih dalam pengembangan.
        </div>
        <h2>RAPOR SANTRI</h2>
        <h3><?= $tpq['NamaTpq'] ?></h3>
        <p>Tahun Ajaran <?= $tahunAjaran ?></p>
    </div>

    <!-- Data Santri -->
    <div class="data-santri">
        <table style="font-size: 12px;">
            <tr>
                <td width="150">Nama Santri</td>
                <td>: <?= htmlspecialchars(toTitleCase($santri['NamaSantri'])) ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: <?= $santri['IdSantri'] ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= $nilai[0]->NamaKelas ?? $santri['IdKelas'] ?></td>
            </tr>
        </table>
    </div>

    <!-- Nilai Semester -->
    <div class="semester-title">Nilai Semester <?= $semester ?></div>
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Materi</th>
                <th width="15%">Kategori</th>
                <th width="10%">Nilai</th>
                <th width="10%">Huruf</th>
                <th width="10%">Rata Kelas</th>
                <th width="20%">Terbilang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($nilai as $n) :
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars(toTitleCase($n->NamaMateri)) ?></td>
                    <td><?= htmlspecialchars(toTitleCase($n->Kategori)) ?></td>
                    <td><?= $n->Nilai ?></td>
                    <td><?= konversiNilaiHuruf($n->Nilai) ?></td>
                    <td><?= number_format($n->RataKelas, 2) ?></td>
                    <td><?= formatTerbilang($n->Nilai) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php
            // Hitung total dan rata-rata
            $total = 0;
            $totalRataKelas = 0;
            $count = count($nilai);
            foreach ($nilai as $n) {
                $total += floatval($n->Nilai);
                $totalRataKelas += floatval($n->RataKelas);
            }
            $rata_rata = $count > 0 ? $total / $count : 0;
            $rata_rata = number_format($rata_rata, 1);
            $rata_rata_kelas = $count > 0 ? $totalRataKelas / $count : 0;
            ?>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total Nilai:</td>
                <td><?= number_format($total, 0) ?></td>
                <td></td>
                <td></td>
                <td><?= formatTerbilang($total) ?></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Rata-Rata:</td>
                <td><?= $rata_rata ?></td>
                <td><?= konversiNilaiHuruf($rata_rata) ?></td>
                <td><?= number_format($rata_rata_kelas, 1) ?></td>
                <td><?= formatTerbilang($rata_rata) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan Layout Gambar Tabel -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 50px; font-size: 12px; page-break-inside: avoid;">
        <tr>
            <td colspan="5" style="width: 50%; padding: 5px; text-align: right;"> Diberikan di Seri Kuala Lobam Tanggal: <?= $tanggal ?></td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">Kepala TPQ</td>
            <td style="width: 50%; padding: 5px;"></td>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">Wali Kelas</td>
        </tr>
        <tr>
            <td colspan="2" style="height: 50px; text-align: center;">
                <?php
                $qrPath = FCPATH . 'uploads/qr/68406ef85f725.svg';
                if (file_exists($qrPath)) {
                    $qrContent = file_get_contents($qrPath);
                    echo '<img src="data:image/svg+xml;base64,' . base64_encode($qrContent) . '" alt="QR Code" style="width: 80px; height: 80px;">';
                }
                ?>
            </td>
            <td></td>
            <td colspan="2" style="height: 50px; text-align: center;">
                <?php
                $qrPath = FCPATH . 'uploads/qr/683f13e3909ae.svg';
                if (file_exists($qrPath)) {
                    $qrContent = file_get_contents($qrPath);
                    echo '<img src="data:image/svg+xml;base64,' . base64_encode($qrContent) . '" alt="QR Code" style="width: 80px; height: 80px;">';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 15px 5px;text-align: center;">( <?= htmlspecialchars(toTitleCase($tpq['KepalaSekolah'])) ?> )</td>
            <td></td>
            <td colspan="2" style="width: 50%; padding: 15px 5px; text-align: center;">( <?= htmlspecialchars(toTitleCase($santri['WaliKelas'])) ?> )</td>
        </tr>
        <tr>
            <td colspan="5" style="padding: 15px 5px; text-align: center;">Mengetahui Orang Tua/Wali Santri</td>
        </tr>
        <tr>
            <td colspan="5" style="height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="5" style="padding: 15px 5px; text-align: center;">( <?= $santri['StatusAyah'] == 'Masih Hidup' ? htmlspecialchars(toTitleCase($santri['NamaAyah'])) : ($santri['NamaWali'] ? htmlspecialchars(toTitleCase($santri['NamaWali'])) : '...........................') ?> )</td>
        </tr>
    </table>
</body>

</html>