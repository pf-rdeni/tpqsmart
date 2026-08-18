<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Cetak Ujian MDTA Manual') ?> - <?= esc($jadwal['NamaUjian'] ?? '') ?></title>

    <!-- Font & FontAwesome -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --primary-color: #1b5e20;
            --primary-light: #e8f5e9;
            --border-color: #222;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        /* Container Toolbar untuk Cetak */
        .no-print-bar {
            background: #ffffff;
            border-bottom: 2px solid #28a745;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-print {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 13px;
            border-radius: 6px;
        }

        /* Lembar Ujian Container */
        .paper-container {
            width: 210mm; /* Standard A4 width */
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 15mm 18mm;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            page-break-after: always;
            break-after: page;
            position: relative;
        }

        /* Corner Fiducial Markers Tepat di 4 Sudut Setiap Sub-tabel LJK */
        .table-corner-mark {
            width: 16px;
            height: 16px;
            background-color: #000000 !important;
            position: absolute;
            z-index: 999;
            border-radius: 0px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .mark-tl { top: -8px; left: -8px; }
        .mark-tr { top: -8px; right: -8px; }
        .mark-bl { bottom: -8px; left: -8px; }
        .mark-br { bottom: -8px; right: -8px; }

        /* Timing Track Row Marks (Garis Penanda Baris Hitam Sejajar Soal) */
        .timing-mark-bar {
            width: 18px;
            height: 10px;
            background-color: #000000 !important;
            margin: 0 auto;
            border-radius: 1px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Header Kop Surat Ujian */
        .header-kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #111;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-kop img {
            max-height: 65px;
            width: auto;
            margin-right: 15px;
        }

        .header-kop .kop-text {
            flex: 1;
            text-align: center;
        }

        .header-kop h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-kop h3 {
            margin: 2px 0 0 0;
            font-size: 11pt;
            font-weight: 600;
        }

        .header-kop p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #444;
        }

        /* Table Identitas Santri */
        .identity-card {
            border: 1.5px solid #222;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
            background-color: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .identity-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .identity-table td.label {
            font-weight: 600;
            width: 110px;
        }

        .qr-code-box {
            text-align: center;
            padding-left: 12px;
            border-left: 1px dashed #ccc;
            min-width: 90px;
        }

        .qr-code-box img {
            width: 70px;
            height: 70px;
        }

        .qr-code-box small {
            display: block;
            font-size: 7pt;
            color: #555;
            margin-top: 2px;
        }

        /* Petunjuk Ujian */
        .instructions-box {
            font-size: 8.5pt;
            background: #fff8e1;
            border: 1px solid #ffe082;
            padding: 6px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Layout Format A: Jawaban Langsung pada Soal */
        .soal-item {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .soal-header {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .soal-number {
            min-width: 24px;
        }

        .soal-text {
            flex: 1;
        }

        .soal-text p {
            margin: 0 0 6px 0;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 15pt;
            line-height: 1.8;
            direction: rtl;
            text-align: right;
        }

        /* Grid Pilihan Jawaban Langsung di Soal */
        .options-list-direct {
            list-style: none;
            padding-left: 28px;
            margin: 4px 0 8px 0;
        }

        .option-item-direct {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .bubble-direct {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border: 1.8px solid #000;
            border-radius: 50%;
            font-weight: 700;
            font-size: 8.5pt;
            margin-right: 10px;
            background-color: #fff;
            flex-shrink: 0;
        }

        .option-text {
            font-size: 10pt;
        }

        /* Layout Format B: Side-by-Side (Menyatu per Halaman) */
        .side-by-side-grid {
            display: flex;
            gap: 15px;
        }

        .col-soal {
            flex: 1;
        }

        .col-ljk-side {
            width: 220px;
            border-left: 2px dashed #444;
            padding-left: 12px;
            flex-shrink: 0;
        }

        .ljk-grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            border: 2px solid #000;
        }

        .ljk-grid-table th, .ljk-grid-table td {
            border: 1.5px solid #000;
            padding: 5px 3px;
            text-align: center;
            vertical-align: middle;
        }

        .ljk-grid-table th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: 700;
            border-bottom: 2px solid #000;
        }

        .bubble-grid {
            width: 22px;
            height: 22px;
            border: 1.8px solid #000;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 8.5pt;
            color: #222;
            background-color: #fff;
            vertical-align: middle;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Layout Format C: LJK Terpisah (Separate Sheet) */
        .separate-ljk-page {
            page-break-before: always;
            break-before: page;
        }

        .ljk-full-header {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .ljk-full-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        /* Media Print Rules */
        @media print {
            body {
                background: #fff;
            }
            .no-print-bar {
                display: none !important;
            }
            .paper-container {
                box-shadow: none;
                margin: 0;
                padding: 10mm 12mm;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Atas (Hanya Tampak di Layar, Tersembunyi saat Cetak) -->
    <div class="no-print-bar">
        <div>
            <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Jadwal
            </a>
            <span class="ms-3 font-weight-bold">
                <i class="fas fa-file-alt text-success me-1"></i> Mode Cetak: 
                <span class="badge bg-success text-white px-2 py-1">
                    <?php if ($formatCetak === 'langsung_soal'): ?>
                        Format A: Jawaban Langsung pada Soal (Silang/Lingkari)
                    <?php elseif ($formatCetak === 'ljk_menyatu'): ?>
                        Format B: LJK Menyatu Side-by-Side per Halaman
                    <?php else: ?>
                        Format C: LJK Terpisah (Separate Sheet)
                    <?php endif; ?>
                </span>
            </span>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Ujian (<?= count($santriExamList) ?> Santri)
            </button>
        </div>
    </div>

    <!-- Loop Setiap Santri (Cetak Naskah Unik Acak per Santri) -->
    <?php foreach ($santriExamList as $idx => $exam): ?>
        <?php
        $santri     = $exam['santri'];
        $distribusi = $exam['distribusi'];
        $idSesi     = $exam['idSesi'];
        $qrData     = "JADWAL:" . $jadwal['id'] . "|SANTRI:" . $santri['IdSantri'] . "|SESI:" . $idSesi;
        $qrUrl      = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
        ?>

        <div class="paper-container">
            <!-- Kop Header Ujian -->
            <div class="header-kop">
                <?php if (!empty($tpqInfo['LogoTpq'])): ?>
                    <img src="<?= base_url($tpqInfo['LogoTpq']) ?>" alt="Logo">
                <?php endif; ?>
                <div class="kop-text">
                    <h2>NASKAH UJIAN MDTA / MADRASAH</h2>
                    <h3><?= esc($jadwal['NamaUjian']) ?></h3>
                    <p><?= esc($tpqInfo['NamaTpq'] ?? 'TPQ / MADRASAH DINIYAH TAKMILIYAH AWALIYAH') ?> — TAHUN AJARAN <?= esc($jadwal['IdTahunAjaran'] ?? '') ?></p>
                </div>
            </div>

            <!-- Card Identitas Peserta & QR Code -->
            <div class="identity-card">
                <table class="identity-table">
                    <tr>
                        <td class="label">Nama Santri</td>
                        <td>: <strong><?= esc($santri['NamaSantri']) ?></strong></td>
                        <td class="label">Kelas</td>
                        <td>: <?= esc($santri['NamaKelas'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="label">NIS / NISN</td>
                        <td>: <?= esc($santri['NISN'] ?: $santri['IdSantri']) ?></td>
                        <td class="label">Durasi Ujian</td>
                        <td>: <?= esc($jadwal['DurasiMenit']) ?> Menit</td>
                    </tr>
                    <tr>
                        <td class="label">Mata Pelajaran</td>
                        <td>: <?= esc($paket['NamaMateri'] ?? 'MDTA') ?></td>
                        <td class="label">Kode Ujian</td>
                        <td>: MDTA-JADWAL-<?= $jadwal['id'] ?></td>
                    </tr>
                </table>
                <div class="qr-code-box">
                    <img src="<?= $qrUrl ?>" alt="QR Code Identitas Santri">
                    <small>TOKEN: <?= substr(md5($qrData), 0, 8) ?></small>
                </div>
            </div>

            <!-- Box Petunjuk Ujian -->
            <div class="instructions-box">
                <strong>PETUNJUK PENGERJAAN:</strong>
                <?php if ($formatCetak === 'langsung_soal'): ?>
                    Isilah jawaban dengan <strong>menyilang (X)</strong> atau <strong>melingkari (O)</strong> bulatan huruf pilihan <strong>[ A ] [ B ] [ C ] [ D ]</strong> langsung pada naskah soal di bawah ini.
                <?php elseif ($formatCetak === 'ljk_menyatu'): ?>
                    Isilah jawaban dengan menghitamkan atau menyilang bulatan huruf pilihan pada <strong>Kolom LJK di Sebelah Kanan</strong> pada nomor soal yang bersesuaian.
                <?php else: ?>
                    Isilah jawaban pada <strong>Lembar Jawaban Komputer (LJK) Khusus</strong> yang berada di lembar tersendiri.
                <?php endif; ?>
            </div>

            <?php
            $maxPilihan      = (int)($jadwal['JumlahPilihan'] ?? 4);
            $hurufPositional = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            ?>

            <!-- RENDERING FORMAT SOAL -->

            <?php if ($formatCetak === 'langsung_soal'): ?>
                <!-- FORMAT A: JAWABAN LANGSUNG PADA SOAL (Rekomendasi Kelas 3-4 SD) -->
                <div class="soal-container-direct">
                    <?php foreach ($distribusi as $nomor => $soal): ?>
                        <div class="soal-item">
                            <div class="soal-header">
                                <span class="soal-number"><?= $nomor + 1 ?>.</span>
                                <div class="soal-text">
                                    <?= $soal['UraianSoal'] ?>
                                </div>
                            </div>

                            <ul class="options-list-direct">
                                <?php 
                                foreach ($soal['pilihan'] as $pIdx => $pil): 
                                    if ($maxPilihan > 0 && $pIdx >= $maxPilihan) break;
                                    $labelHuruf = $hurufPositional[$pIdx] ?? chr(65 + $pIdx);
                                ?>
                                    <li class="option-item-direct">
                                        <span class="bubble-direct"><?= esc($labelHuruf) ?></span>
                                        <span class="option-text"><?= $pil['TeksPilihan'] ?? $pil['UraianPilihan'] ?? '' ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($formatCetak === 'ljk_menyatu'): ?>
                <!-- FORMAT B: LJK MENYATU SIDE-BY-SIDE (Rekomendasi Kelas 4-5 SD) -->
                <div class="side-by-side-grid">
                    <!-- Kolom Naskah Soal (Kiri) -->
                    <div class="col-soal">
                        <?php foreach ($distribusi as $nomor => $soal): ?>
                            <div class="soal-item">
                                <div class="soal-header">
                                    <span class="soal-number"><?= $nomor + 1 ?>.</span>
                                    <div class="soal-text">
                                        <?= $soal['UraianSoal'] ?>
                                    </div>
                                </div>

                                <ul class="options-list-direct">
                                    <?php 
                                    foreach ($soal['pilihan'] as $pIdx => $pil): 
                                        if ($maxPilihan > 0 && $pIdx >= $maxPilihan) break;
                                        $labelHuruf = $hurufPositional[$pIdx] ?? chr(65 + $pIdx);
                                    ?>
                                        <li class="option-item-direct">
                                            <strong style="margin-right: 6px;"><?= esc($labelHuruf) ?>.</strong>
                                            <span class="option-text"><?= $pil['TeksPilihan'] ?? $pil['UraianPilihan'] ?? '' ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Kolom LJK Jawaban (Kanan) -->
                    <div class="col-ljk-side" style="position: relative;">
                        <!-- 4 Corner Fiducial Markers -->
                        <div class="table-corner-mark mark-tl"></div>
                        <div class="table-corner-mark mark-tr"></div>
                        <div class="table-corner-mark mark-bl"></div>
                        <div class="table-corner-mark mark-br"></div>

                        <h4 style="margin: 0 0 8px 0; font-size: 9.5pt; text-align: center; text-transform: uppercase;">LEMBAR JAWABAN</h4>
                        <table class="ljk-grid-table">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">NO</th>
                                    <th>PILIHAN JAWABAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($distribusi as $nomor => $soal): ?>
                                    <tr>
                                        <td><strong><?= $nomor + 1 ?></strong></td>
                                        <td>
                                            <?php 
                                            foreach ($soal['pilihan'] as $pIdx => $pil): 
                                                if ($maxPilihan > 0 && $pIdx >= $maxPilihan) break;
                                                $labelHuruf = $hurufPositional[$pIdx] ?? chr(65 + $pIdx);
                                            ?>
                                                <span style="display: inline-block; margin: 0 4px;">
                                                    <span class="bubble-grid"><?= esc($labelHuruf) ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <!-- FORMAT C: LJK TERPISAH (Rekomendasi Kelas 5-6 SD) -->
                <div class="soal-container-separate">
                    <?php foreach ($distribusi as $nomor => $soal): ?>
                        <div class="soal-item">
                            <div class="soal-header">
                                <span class="soal-number"><?= $nomor + 1 ?>.</span>
                                <div class="soal-text">
                                    <?= $soal['UraianSoal'] ?>
                                </div>
                            </div>

                            <ul class="options-list-direct">
                                <?php 
                                foreach ($soal['pilihan'] as $pIdx => $pil): 
                                    if ($maxPilihan > 0 && $pIdx >= $maxPilihan) break;
                                    $labelHuruf = $hurufPositional[$pIdx] ?? chr(65 + $pIdx);
                                ?>
                                    <li class="option-item-direct">
                                        <strong style="margin-right: 6px;"><?= esc($labelHuruf) ?>.</strong>
                                        <span class="option-text"><?= $pil['TeksPilihan'] ?? $pil['UraianPilihan'] ?? '' ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

        </div><!-- End Paper Container Naskah Soal -->

        <!-- LEMBAR JAWABAN TERPISAH (NEW PAGE / NEW PAPER CONTAINER) -->
        <div class="paper-container separate-ljk-paper" style="page-break-before: always; break-before: page;">
            <!-- Header Kop & Identitas Santri pada Lembar LJK -->
            <div class="header-kop">
                <?php if (!empty($tpqInfo['LogoTpq'])): ?>
                    <img src="<?= base_url($tpqInfo['LogoTpq']) ?>" alt="Logo">
                <?php endif; ?>
                <div class="kop-text">
                    <h2>LEMBAR JAWABAN KOMPUTER / MANUAL (LJK)</h2>
                    <h3><?= esc($jadwal['NamaUjian']) ?></h3>
                    <p><?= esc($tpqInfo['NamaTpq'] ?? 'TPQ / MADRASAH DINIYAH TAKMILIYAH AWALIYAH') ?></p>
                </div>
            </div>

            <div class="identity-card">
                <table class="identity-table">
                    <tr>
                        <td class="label">Nama Santri</td>
                        <td>: <strong><?= esc($santri['NamaSantri']) ?></strong></td>
                        <td class="label">Kelas</td>
                        <td>: <?= esc($santri['NamaKelas'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="label">NIS / NISN</td>
                        <td>: <?= esc($santri['NISN'] ?: $santri['IdSantri']) ?></td>
                        <td class="label">Mata Pelajaran</td>
                        <td>: <?= esc($paket['NamaMateri'] ?? 'MDTA') ?></td>
                    </tr>
                </table>
                <div class="qr-code-box">
                    <img src="<?= $qrUrl ?>" alt="QR Code Identitas Santri">
                    <small>TOKEN: <?= substr(md5($qrData), 0, 8) ?></small>
                </div>
            </div>

            <div class="ljk-full-grid">
                <?php 
                $chunks = array_chunk($distribusi, ceil(count($distribusi) / 2));
                foreach ($chunks as $chunkIdx => $chunkSoal): 
                ?>
                    <div style="position: relative; padding: 6px;">
                        <!-- 4 Table Corner Fiducial Markers per Sub-tabel untuk Presisi OMR -->
                        <div class="table-corner-mark mark-tl"></div>
                        <div class="table-corner-mark mark-tr"></div>
                        <div class="table-corner-mark mark-bl"></div>
                        <div class="table-corner-mark mark-br"></div>

                        <table class="ljk-grid-table">
                            <thead>
                                <tr>
                                    <th style="width: 25px; text-align: center;"></th>
                                    <th style="width: 35px;">NO</th>
                                    <th>PILIHAN JAWABAN SANTRI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chunkSoal as $soal): ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle; padding: 2px 0;">
                                            <div class="timing-mark-bar"></div>
                                        </td>
                                        <td><strong><?= $soal['UrutanSoal'] ?></strong></td>
                                        <td style="padding: 6px 4px;">
                                            <?php 
                                            foreach ($soal['pilihan'] as $pIdx => $pil): 
                                                if ($maxPilihan > 0 && $pIdx >= $maxPilihan) break;
                                                $labelHuruf = $hurufPositional[$pIdx] ?? chr(65 + $pIdx);
                                            ?>
                                                <span style="display: inline-block; margin: 0 5px;">
                                                    <span class="bubble-grid"><?= esc($labelHuruf) ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
            <?php endif; ?>

        <?php if ($formatCetak !== 'ljk_terpisah'): ?>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

</body>
</html>
