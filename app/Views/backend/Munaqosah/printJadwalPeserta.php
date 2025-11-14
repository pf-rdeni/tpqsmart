<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Peserta Ujian Munaqosah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }

        thead {
            background-color: #28a745;
            color: white;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            font-size: 10px;
        }

        td {
            font-size: 9px;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .subtotal-row {
            background-color: #ffc107;
            font-weight: bold;
        }

        .total-row {
            background-color: #ffc107;
            font-weight: bold;
        }

        tbody tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>JADWAL PESERTA UJIAN MUNAQOSAH</h2>
        <p>Tahun Ajaran: <?= esc($tahunAjaran) ?> | Type Ujian: <?= esc(ucfirst(str_replace('-', ' ', $typeUjian ?? 'munaqosah'))) ?></p>
        <?php if (isset($isFilteredTpq) && $isFilteredTpq && isset($namaTpq) && $namaTpq): ?>
            <p>TPQ: <?= esc($namaTpq) ?></p>
        <?php endif; ?>
        <p>Dicetak pada: <?= esc($generated_at) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Group</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Waktu</th>
                <th style="width: 25%;">Nama TPQ</th>
                <th style="width: 20%;">Desa/Kelurahan</th>
                <th style="width: 10%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $prevTanggal = null;
            $prevJam = null;
            $prevJamTime = null;
            $subtotalPagi = 0; // Subtotal untuk jam < 13:00
            $subtotalSiang = 0; // Subtotal untuk jam >= 13:00
            $subtotalPerTanggal = 0; // Subtotal untuk satu tanggal
            $allSubTotal = []; // Array untuk menyimpan Sub Total per tanggal
            $grandTotalCalc = 0;

            foreach ($jadwal as $group):
                if (empty($group['rows'])) continue;

                $currentTanggal = $group['Tanggal'];
                $currentJam = $group['Jam'];
                $currentJamTime = $currentJam ? (int)explode(':', $currentJam)[0] : 0;

                // Jika tanggal berubah, hitung Sub Total untuk tanggal sebelumnya
                if ($prevTanggal !== null && $prevTanggal !== $currentTanggal):
                    // Tambahkan Sub untuk jam < 13:00 jika ada
                    if ($subtotalPagi > 0):
            ?>
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                            <td class="text-center" style="font-weight: bold;"><?= $subtotalPagi ?></td>
                        </tr>
                    <?php
                        $subtotalPerTanggal += $subtotalPagi;
                    endif;
                    // Tambahkan Sub untuk jam >= 13:00 jika ada
                    if ($subtotalSiang > 0):
                    ?>
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                            <td class="text-center" style="font-weight: bold;"><?= $subtotalSiang ?></td>
                        </tr>
                    <?php
                        $subtotalPerTanggal += $subtotalSiang;
                    endif;
                    // Tambahkan Sub Total untuk tanggal sebelumnya
                    if ($subtotalPerTanggal > 0):
                    ?>
                        <tr class="total-row" style="background-color: #90EE90;">
                            <td colspan="5" class="text-right" style="font-weight: bold;">Sub Total</td>
                            <td class="text-center" style="font-weight: bold;"><?= $subtotalPerTanggal ?></td>
                        </tr>
                        <?php
                        $allSubTotal[] = $subtotalPerTanggal;
                        $grandTotalCalc += $subtotalPerTanggal;
                    endif;
                    // Reset untuk tanggal baru
                    $subtotalPagi = 0;
                    $subtotalSiang = 0;
                    $subtotalPerTanggal = 0;
                endif;

                // Jika jam berubah dalam tanggal yang sama
                if ($prevTanggal === $currentTanggal && $prevJam !== $currentJam && $prevJamTime !== null):
                    if ($prevJamTime < 13 && $currentJamTime >= 13):
                        // Akhiri Sub untuk pagi
                        if ($subtotalPagi > 0):
                        ?>
                            <tr class="subtotal-row">
                                <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                                <td class="text-center" style="font-weight: bold;"><?= $subtotalPagi ?></td>
                            </tr>
                        <?php
                            $subtotalPerTanggal += $subtotalPagi;
                            $subtotalPagi = 0;
                        endif;
                    elseif ($prevJamTime >= 13 && $currentJamTime < 13):
                        // Akhiri Sub untuk siang
                        if ($subtotalSiang > 0):
                        ?>
                            <tr class="subtotal-row">
                                <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                                <td class="text-center" style="font-weight: bold;"><?= $subtotalSiang ?></td>
                            </tr>
                        <?php
                            $subtotalPerTanggal += $subtotalSiang;
                            $subtotalSiang = 0;
                        endif;
                    endif;
                endif;

                // Format tanggal
                $tanggalObj = new \DateTime($currentTanggal);
                $tanggalFormatted = $tanggalObj->format('l d F Y');
                // Convert to Indonesian day names
                $days = [
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu'
                ];
                $months = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];

                $dayName = $days[$tanggalObj->format('l')] ?? $tanggalObj->format('l');
                $monthName = $months[$tanggalObj->format('F')] ?? $tanggalObj->format('F');
                $tanggalFormatted = $dayName . ' ' . $tanggalObj->format('d') . ' ' . $monthName . ' ' . $tanggalObj->format('Y');

                // Format jam
                $jamFormatted = $currentJam ? 'Jam ' . $currentJam . ' s/d Selesai' : '-';

                // Render rows untuk group ini
                foreach ($group['rows'] as $rowIndex => $row):
                    if ($rowIndex === 0):
                        ?>
                        <tr>
                            <td rowspan="<?= count($group['rows']) ?>" class="text-center" style="vertical-align: middle;"><?= esc($row['GroupPeserta']) ?></td>
                            <td rowspan="<?= count($group['rows']) ?>" class="text-center" style="vertical-align: middle;"><?= esc($tanggalFormatted) ?></td>
                            <td rowspan="<?= count($group['rows']) ?>" class="text-center" style="vertical-align: middle;"><?= esc($jamFormatted) ?></td>
                            <td class="text-left"><?= esc($row['NamaTpq']) ?></td>
                            <td class="text-left"><?= esc($row['KelurahanDesa']) ?></td>
                            <td class="text-center"><?= esc($row['Jumlah']) ?></td>
                        </tr>
                    <?php
                    else:
                    ?>
                        <tr>
                            <td class="text-left"><?= esc($row['NamaTpq']) ?></td>
                            <td class="text-left"><?= esc($row['KelurahanDesa']) ?></td>
                            <td class="text-center"><?= esc($row['Jumlah']) ?></td>
                        </tr>
                    <?php
                    endif;
                    // Tambahkan ke subtotal berdasarkan jam
                    $jumlah = (int)$row['Jumlah'];
                    if ($currentJamTime < 13) {
                        $subtotalPagi += $jumlah;
                    } else {
                        $subtotalSiang += $jumlah;
                    }
                endforeach;

                $prevTanggal = $currentTanggal;
                $prevJam = $currentJam;
                $prevJamTime = $currentJamTime;
            endforeach;

            // Tambahkan Sub untuk tanggal terakhir
            if (!empty($jadwal)):
                // Tambahkan Sub untuk jam < 13:00 jika ada
                if ($subtotalPagi > 0):
                    ?>
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                        <td class="text-center" style="font-weight: bold;"><?= $subtotalPagi ?></td>
                    </tr>
                <?php
                    $subtotalPerTanggal += $subtotalPagi;
                endif;
                // Tambahkan Sub untuk jam >= 13:00 jika ada
                if ($subtotalSiang > 0):
                ?>
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-right" style="font-weight: bold;">Sub</td>
                        <td class="text-center" style="font-weight: bold;"><?= $subtotalSiang ?></td>
                    </tr>
                <?php
                    $subtotalPerTanggal += $subtotalSiang;
                endif;
                // Tambahkan Sub Total untuk tanggal terakhir
                if ($subtotalPerTanggal > 0):
                ?>
                    <tr class="total-row" style="background-color: #90EE90;">
                        <td colspan="5" class="text-right" style="font-weight: bold;">Sub Total</td>
                        <td class="text-center" style="font-weight: bold;"><?= $subtotalPerTanggal ?></td>
                    </tr>
            <?php
                    $allSubTotal[] = $subtotalPerTanggal;
                    $grandTotalCalc += $subtotalPerTanggal;
                endif;
            endif;
            ?>

            <!-- Grand Total -->
            <tr class="total-row" style="background-color: #90EE90;">
                <td colspan="5" class="text-right" style="font-weight: bold;">Grand Total</td>
                <td class="text-center" style="font-weight: bold;"><?= $grandTotalCalc ?></td>
            </tr>
        </tbody>
    </table>
</body>

</html>