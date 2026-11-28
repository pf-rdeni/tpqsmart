<?php
helper('nilai');

$isExcel = ($format === 'excel');
$paperCss = ($paper === 'A4') ? '21cm 29.7cm' : '21cm 33cm';
$orientationCss = ($orientation === 'portrait') ? 'portrait' : 'landscape';
$fontSizePt = in_array((int)$fontSize, [8, 9, 10], true) ? (int)$fontSize : 9;

if (!function_exists('renderVerticalHeader')) {
    function renderVerticalHeader($label, $isExcel)
    {
        $safe = htmlspecialchars($label);
        if ($isExcel) {
            return '<th class="vertical-th">' . $safe . '</th>';
        }
        return '<th class="vertical-th"><div class="vertical-box"><span class="vertical-text">' . $safe . '</span></div></th>';
    }
}
?>
<!DOCTYPE html>
<html<?= $isExcel ? ' xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <title>Detail Nilai Santri - Semester <?= htmlspecialchars($semester) ?></title>
    <?php if ($isExcel): ?>
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Detail Nilai</x:Name>
                        <x:WorksheetOptions>
                            <x:DisplayGridlines/>
                            <x:PageSetup>
                                <x:Layout x:Orientation="<?= ucfirst($orientationCss) ?>"/>
                            </x:PageSetup>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
    <?php endif; ?>
    <style>
        @page {
            size: <?= $paperCss ?> <?= $orientationCss ?>;
            margin: 12mm;
        }
        body {
            font-family: 'Arial Unicode MS', 'DejaVu Sans', Arial, sans-serif;
            font-size: <?= $fontSizePt ?>pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .doc-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-header h2 {
            margin: 0;
            font-size: <?= $fontSizePt + 3 ?>pt;
        }
        .doc-header p {
            margin: 2px 0 0 0;
            font-size: <?= $fontSizePt ?>pt;
        }
        .kelas-block {
            margin-bottom: 14px;
        }
        .kelas-title {
            font-weight: bold;
            font-size: <?= $fontSizePt + 1 ?>pt;
            margin: 6px 0 4px 0;
        }
        table.nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.nilai-table th,
        table.nilai-table td {
            border: 1px solid #333;
            padding: 3px 4px;
            font-size: <?= $fontSizePt ?>pt;
            vertical-align: middle;
        }
        table.nilai-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-danger { color: #c00; }
        .summary-row td,
        .summary-row th {
            background-color: #f4f6f9;
            font-weight: bold;
        }
        .vertical-th {
            height: 130px;
            width: 28px;
            padding: 2px 1px !important;
            vertical-align: bottom !important;
            text-align: center;
            <?php if ($isExcel): ?>
            mso-rotate: 90;
            white-space: normal;
            <?php endif; ?>
        }
        <?php if (!$isExcel): ?>
        .vertical-box {
            width: 26px;
            height: 126px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .vertical-text {
            position: absolute;
            left: 1px;
            top: 122px;
            width: 120px;
            height: 24px;
            line-height: 11px;
            font-size: <?= max(7, $fontSizePt - 1.5) ?>pt;
            white-space: normal;
            word-wrap: break-word;
            text-align: left;
            transform: rotate(-90deg);
            transform-origin: 0 0;
        }
        <?php endif; ?>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <?php
    $kelasKeys = array_keys($selectedKelas);
    $totalKelas = count($kelasKeys);
    $kelasIdx = 0;

    // Tentukan batas kolom vertikal per halaman untuk PDF agar tidak terpotong ke kanan
    if ($orientationCss === 'portrait') {
        $baseMaxCols = 15;
    } elseif ($paper === 'A4') {
        $baseMaxCols = 24;
    } else {
        // F4 Landscape
        $baseMaxCols = 28;
    }

    foreach ($selectedKelas as $kelasId => $namaKelas):
        $kelasIdx++;
        $isAlphabet = getAlphabetKelasSettings($settingNilai, $kelasId)['isAlphabetKelas'] ?? false;

        // Ambil & urutkan materi untuk kelas ini
        $materiSorted = $dataMateri[$kelasId] ?? [];
        if (!empty($materiSorted)) {
            usort($materiSorted, function ($a, $b) {
                return $a->UrutanMateri - $b->UrutanMateri;
            });
        }

        // Ambil santri untuk kelas ini
        $santriList = [];
        foreach ($dataNilai as $row) {
            if (($row['IdKelas'] ?? null) == $kelasId || ($row['Nama Kelas'] ?? '') === $namaKelas) {
                $santriList[] = $row;
            }
        }

        // Hitung kapasitas kolom per halaman untuk model yang dipilih
        $maxCols = $baseMaxCols;
        if ($model === 'a' && $showNis) {
            $maxCols = max(8, $baseMaxCols - 2);
        }

        if ($model === 'a') {
            $summaryColCount = $showSummary ? ($isAlphabet ? 1 : 2) : 0;
            $itemsToChunk = $materiSorted;
        } else {
            $summaryColCount = $showSummary ? 1 : 0;
            $itemsToChunk = $santriList;
        }

        if ($isExcel || empty($itemsToChunk)) {
            $chunks = [$itemsToChunk];
        } else {
            $chunks = array_chunk($itemsToChunk, $maxCols);
            $lastIdx = count($chunks) - 1;
            if ($summaryColCount > 0 && (count($chunks[$lastIdx]) + $summaryColCount) > $maxCols) {
                $overflow = array_splice($chunks[$lastIdx], -$summaryColCount);
                $chunks[] = $overflow;
            }
        }
        $totalChunks = count($chunks);

        // Pre-hitung total keseluruhan untuk Model A dan Model B agar akurat di halaman terakhir
        $santriRowTotals = [];
        $santriRowCounts = [];
        $materiColTotals = [];
        $materiRowTotals = [];
        $santriColTotals = [];

        foreach ($santriList as $sIdx => $santri) {
            $sTot = 0;
            $sCnt = 0;
            foreach ($materiSorted as $mIdx => $materi) {
                $val = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : 0;
                $sTot += $val;
                $sCnt++;
                $materiColTotals[$materi->NamaMateri] = ($materiColTotals[$materi->NamaMateri] ?? 0) + $val;
                $materiRowTotals[$mIdx] = ($materiRowTotals[$mIdx] ?? 0) + $val;
                $santriColTotals[$sIdx] = ($santriColTotals[$sIdx] ?? 0) + $val;
            }
            $santriRowTotals[$sIdx] = $sTot;
            $santriRowCounts[$sIdx] = $sCnt;
        }

        foreach ($chunks as $chunkIdx => $chunkItems):
            $isLastChunk = ($chunkIdx === $totalChunks - 1);
            $isVeryLastBlock = ($kelasIdx === $totalKelas && $isLastChunk);
            $partLabel = ($totalChunks > 1) ? ' (Bagian ' . ($chunkIdx + 1) . '/' . $totalChunks . ')' : '';
    ?>
        <div class="kelas-block <?= (!$isExcel && !$isVeryLastBlock) ? 'page-break' : '' ?>">
            <div class="doc-header">
                <h2><?= !empty($namaTpq) ? htmlspecialchars($namaTpq) . ' - ' : '' ?>DAFTAR NILAI SANTRI</h2>
                <p>Semester <?= htmlspecialchars($semester) ?> • Tahun Ajaran <?= htmlspecialchars(convertTahunAjaran($IdTahunAjaran)) ?></p>
            </div>

            <div class="kelas-title"><?= htmlspecialchars($namaKelas . $partLabel) ?></div>

            <?php if ($model === 'a'): ?>
                <!-- MODEL A: Baris = Santri, Kolom = Materi -->
                <table class="nilai-table">
                    <thead>
                        <tr>
                            <th style="width: 24px;">No</th>
                            <?php if ($showNis): ?>
                                <th>NIS</th>
                            <?php endif; ?>
                            <th>Nama Santri</th>
                            <?php foreach ($chunkItems as $materi): ?>
                                <?= renderVerticalHeader(ucwords(strtolower($materi->NamaMateri)), $isExcel) ?>
                            <?php endforeach; ?>
                            <?php if ($showSummary && $isLastChunk): ?>
                                <?php if ($isAlphabet): ?>
                                    <?= renderVerticalHeader('Rata-Rata Huruf', $isExcel) ?>
                                <?php else: ?>
                                    <?= renderVerticalHeader('Total', $isExcel) ?>
                                    <?= renderVerticalHeader('Rata-Rata', $isExcel) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $rowCount = count($santriList);
                        foreach ($santriList as $sIdx => $santri):
                            $totalNilai = $santriRowTotals[$sIdx] ?? 0;
                            $jumlahMateri = $santriRowCounts[$sIdx] ?? 0;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <?php if ($showNis): ?>
                                    <td class="text-center"><?= htmlspecialchars($santri['IdSantri']) ?></td>
                                <?php endif; ?>
                                <td class="text-left"><?= htmlspecialchars($santri['Nama Santri']) ?></td>
                                <?php foreach ($chunkItems as $materi):
                                    $val = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : 0;
                                    $displayVal = $isAlphabet
                                        ? konversiNilaiHuruf($val, $settingNilai)
                                        : konversiNilaiAngkaArabic($val);
                                ?>
                                    <td class="text-center<?= $val === 0 ? ' text-danger' : '' ?>"><?= htmlspecialchars($displayVal) ?></td>
                                <?php endforeach; ?>
                                <?php if ($showSummary && $isLastChunk): ?>
                                    <?php if ($isAlphabet): ?>
                                        <td class="text-center"><strong><?= $jumlahMateri > 0 ? htmlspecialchars(konversiNilaiHuruf(round($totalNilai / $jumlahMateri, 1), $settingNilai)) : '-' ?></strong></td>
                                    <?php else: ?>
                                        <td class="text-center"><strong><?= htmlspecialchars(konversiNilaiAngkaArabic($totalNilai)) ?></strong></td>
                                        <td class="text-center"><strong><?= $jumlahMateri > 0 ? htmlspecialchars(konversiNilaiAngkaArabic(round($totalNilai / $jumlahMateri, 1))) : '-' ?></strong></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($showSummary && $rowCount > 0): ?>
                            <tr class="summary-row">
                                <td colspan="<?= $showNis ? 3 : 2 ?>" class="text-right">Rata-Rata Kelas</td>
                                <?php foreach ($chunkItems as $materi):
                                    $avgCol = round(($materiColTotals[$materi->NamaMateri] ?? 0) / $rowCount, 1);
                                    $displayAvg = $isAlphabet
                                        ? konversiNilaiHuruf($avgCol, $settingNilai)
                                        : konversiNilaiAngkaArabic($avgCol);
                                ?>
                                    <td class="text-center"><?= htmlspecialchars($displayAvg) ?></td>
                                <?php endforeach; ?>
                                <?php if ($isLastChunk):
                                    $grandTotal = array_sum($materiColTotals);
                                    $materiCount = count($materiSorted);
                                ?>
                                    <?php if ($isAlphabet): ?>
                                        <td class="text-center"><?= $materiCount > 0 ? htmlspecialchars(konversiNilaiHuruf(round(($grandTotal / $materiCount) / $rowCount, 1), $settingNilai)) : '-' ?></td>
                                    <?php else: ?>
                                        <td class="text-center"><?= htmlspecialchars(konversiNilaiAngkaArabic(round($grandTotal / $rowCount, 1))) ?></td>
                                        <td class="text-center"><?= $materiCount > 0 ? htmlspecialchars(konversiNilaiAngkaArabic(round(($grandTotal / $materiCount) / $rowCount, 1))) : '-' ?></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <!-- MODEL B: Baris = Materi, Kolom = Santri -->
                <?php
                // Pertahankan index asli $santriList agar $santriColTotals cocok
                $chunkSantriWithIdx = [];
                $offset = $chunkIdx * $maxCols;
                foreach ($chunkItems as $localIdx => $sItem) {
                    $chunkSantriWithIdx[$offset + $localIdx] = $sItem;
                }
                ?>
                <table class="nilai-table">
                    <thead>
                        <tr>
                            <th style="width: 24px;">No</th>
                            <th>Materi</th>
                            <?php foreach ($chunkSantriWithIdx as $santri): ?>
                                <?= renderVerticalHeader($santri['Nama Santri'], $isExcel) ?>
                            <?php endforeach; ?>
                            <?php if ($showSummary && $isLastChunk): ?>
                                <?= renderVerticalHeader('Rata-Rata', $isExcel) ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $materiCount = count($materiSorted);
                        $santriCount = count($santriList);
                        foreach ($materiSorted as $mIdx => $materi):
                            $materiTotal = $materiRowTotals[$mIdx] ?? 0;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-left"><?= htmlspecialchars(ucwords(strtolower($materi->NamaMateri))) ?></td>
                                <?php foreach ($chunkSantriWithIdx as $sIdx => $santri):
                                    $val = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : 0;
                                    $displayVal = $isAlphabet
                                        ? konversiNilaiHuruf($val, $settingNilai)
                                        : konversiNilaiAngkaArabic($val);
                                ?>
                                    <td class="text-center<?= $val === 0 ? ' text-danger' : '' ?>"><?= htmlspecialchars($displayVal) ?></td>
                                <?php endforeach; ?>
                                <?php if ($showSummary && $isLastChunk):
                                    $avgMateri = $santriCount > 0 ? round($materiTotal / $santriCount, 1) : 0;
                                    $displayAvgMateri = $isAlphabet
                                        ? konversiNilaiHuruf($avgMateri, $settingNilai)
                                        : konversiNilaiAngkaArabic($avgMateri);
                                ?>
                                    <td class="text-center"><strong><?= $santriCount > 0 ? htmlspecialchars($displayAvgMateri) : '-' ?></strong></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($showSummary && $materiCount > 0 && $santriCount > 0): ?>
                            <?php if (!$isAlphabet): ?>
                                <tr class="summary-row">
                                    <td colspan="2" class="text-right">Total Nilai</td>
                                    <?php foreach ($chunkSantriWithIdx as $sIdx => $santri):
                                        $tot = $santriColTotals[$sIdx] ?? 0;
                                    ?>
                                        <td class="text-center"><?= htmlspecialchars(konversiNilaiAngkaArabic($tot)) ?></td>
                                    <?php endforeach; ?>
                                    <?php if ($isLastChunk):
                                        $sumAllTotals = array_sum($santriColTotals);
                                    ?>
                                        <td class="text-center"><?= htmlspecialchars(konversiNilaiAngkaArabic(round($sumAllTotals / $santriCount, 1))) ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            <tr class="summary-row">
                                <td colspan="2" class="text-right">Rata-Rata</td>
                                <?php foreach ($chunkSantriWithIdx as $sIdx => $santri):
                                    $avgSantri = round(($santriColTotals[$sIdx] ?? 0) / $materiCount, 1);
                                    $displayAvgSantri = $isAlphabet
                                        ? konversiNilaiHuruf($avgSantri, $settingNilai)
                                        : konversiNilaiAngkaArabic($avgSantri);
                                ?>
                                    <td class="text-center"><?= htmlspecialchars($displayAvgSantri) ?></td>
                                <?php endforeach; ?>
                                <?php if ($isLastChunk):
                                    $sumAllAvg = 0;
                                    foreach ($santriList as $sIdx => $santri) {
                                        $sumAllAvg += round(($santriColTotals[$sIdx] ?? 0) / $materiCount, 1);
                                    }
                                    $overallAvg = round($sumAllAvg / $santriCount, 1);
                                    $displayOverallAvg = $isAlphabet
                                        ? konversiNilaiHuruf($overallAvg, $settingNilai)
                                        : konversiNilaiAngkaArabic($overallAvg);
                                ?>
                                    <td class="text-center"><?= htmlspecialchars($displayOverallAvg) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php
        endforeach;
    endforeach;
    ?>
</body>
</html>
