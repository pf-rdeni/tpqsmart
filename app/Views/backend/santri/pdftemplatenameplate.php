<?php
$labelWidth = isset($labelWidth) ? (float) $labelWidth : 10.0;
$labelHeight = isset($labelHeight) ? (float) $labelHeight : 3.8;
$colsPerPage = isset($colsPerPage) ? max(1, (int) $colsPerPage) : 2;
$rowsPerPage = isset($rowsPerPage) ? max(1, (int) $rowsPerPage) : 5;
$variant = isset($variant) ? max(1, min(5, (int) $variant)) : 1;

$boxWidth = round(max(4.5, $labelWidth - 0.05), 2);
$boxHeight = round(max(2.8, $labelHeight - 0.05), 2);

// Gunakan ukuran foto dari pengaturan (default/rekomendasi 3 x 4 cm, aman terhadap tinggi & lebar kotak)
$defaultFotoH = round(min(4.0, max(1.6, $boxHeight - 0.65)), 2);
$fotoHeight = isset($fotoHeight) ? round(min($boxHeight - 0.4, max(1.6, (float) $fotoHeight)), 2) : $defaultFotoH;
if ($variant === 2) {
    $fotoHeight = round(min($fotoHeight, max(1.6, $boxHeight - 0.85)), 2);
}
$fotoWidth = isset($fotoWidth) ? round(min($boxWidth * 0.45, max(1.2, (float) $fotoWidth)), 2) : round($fotoHeight * 0.75, 2);
$colFotoWidth = round($fotoWidth + 0.35, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Name Plate Santri</title>
    <style>
        @page {
            margin: 0.5cm 0.4cm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #111827;
        }

        .sheet-table {
            border-collapse: separate;
            border-spacing: 0.2cm 0.25cm;
        }

        .sheet-cell {
            width: <?= $labelWidth ?>cm;
            height: <?= $labelHeight ?>cm;
            vertical-align: top;
            padding: 0;
        }

        .nameplate-box {
            width: <?= $boxWidth ?>cm;
            height: <?= $boxHeight ?>cm;
            border: 1px dashed #9ca3af;
            background-color: #ffffff;
            overflow: hidden;
        }

        .nameplate-inner {
            width: 100%;
            height: <?= $boxHeight ?>cm;
            border-collapse: collapse;
        }

        .col-foto {
            width: <?= $colFotoWidth ?>cm;
            vertical-align: middle;
            text-align: center;
            padding: 0.2cm 0.12cm 0.2cm 0.2cm;
        }

        .foto-frame {
            width: <?= $fotoWidth ?>cm;
            height: <?= $fotoHeight ?>cm;
            border: 1px solid #9ca3af;
            margin: 0 auto;
            text-align: center;
            overflow: hidden;
            background-color: #f8fafc;
        }

        .foto-frame img {
            width: <?= $fotoWidth ?>cm;
            height: <?= $fotoHeight ?>cm;
            object-fit: cover;
            display: block;
        }

        .foto-placeholder {
            width: <?= $fotoWidth ?>cm;
            height: <?= $fotoHeight ?>cm;
            border-collapse: collapse;
        }

        .foto-placeholder td {
            vertical-align: middle;
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            color: #64748b;
            line-height: 1.4;
        }

        .col-info {
            vertical-align: middle;
            padding: 0.25cm 0.3cm 0.25cm 0.16cm;
        }

        /* Variant 1: Formal Minimalis Beraksen Garis */
        .v1-box {
            border-left: 5px solid #047857;
        }
        .v1-id {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #065f46;
            margin-bottom: 0.08cm;
        }
        .v1-nama {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            line-height: 1.25;
            padding-bottom: 0.14cm;
            border-bottom: 1px solid #d1d5db;
        }
        .v1-alamat-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9ca3af;
            margin-top: 0.16cm;
            margin-bottom: 0.04cm;
        }
        .v1-alamat-val {
            font-size: 8.2pt;
            color: #374151;
            line-height: 1.38;
        }

        /* Variant 2: Classic Double-Bordered Resmi */
        .v2-outer-pad {
            padding: 0.12cm;
        }
        .v2-frame-1 {
            width: 100%;
            height: <?= round($boxHeight - 0.28, 2) ?>cm;
            border: 1.5px solid #1f2937;
            border-collapse: collapse;
        }
        .v2-frame-2 {
            border: 1px solid #6b7280;
            padding: 0.1cm 0.14cm;
        }
        .v2-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7280;
            border-bottom: 1px solid #1f2937;
            padding-bottom: 0.06cm;
            margin-bottom: 0.1cm;
        }
        .v2-nama {
            font-size: 9.8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            line-height: 1.22;
            margin-bottom: 0.1cm;
        }
        .v2-meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            color: #1f2937;
            line-height: 1.3;
        }
        .v2-meta td {
            vertical-align: top;
            padding: 1px 0;
        }

        /* Variant 3: Modern Soft Split */
        .v3-col-foto {
            background-color: #f1f5f9;
            border-right: 1px solid #cbd5e1;
        }
        .v3-badge {
            display: inline-block;
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7.2pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            margin-bottom: 0.12cm;
        }
        .v3-nama {
            font-size: 10.2pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            line-height: 1.25;
        }
        .v3-alamat {
            margin-top: 0.16cm;
            padding-top: 0.14cm;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #475569;
            line-height: 1.35;
        }

        /* Variant 4: Executive Navy & Gold */
        .v4-foto-frame {
            border: 1.5px solid #1e3a5f;
        }
        .v4-strip {
            background-color: #1e3a5f;
            border-bottom: 2px solid #d97706;
            color: #ffffff;
            padding: 3px 6px;
            margin-bottom: 0.16cm;
        }
        .v4-strip-table {
            width: 100%;
            border-collapse: collapse;
        }
        .v4-strip-left {
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #ffffff;
        }
        .v4-strip-right {
            font-size: 7.5pt;
            font-weight: bold;
            color: #fcd34d;
            text-align: right;
        }
        .v4-nama {
            font-size: 10.2pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a5f;
            line-height: 1.25;
            margin-bottom: 0.14cm;
        }
        .v4-alamat {
            border-top: 1px solid #e5e7eb;
            padding-top: 0.12cm;
            font-size: 8pt;
            color: #374151;
            line-height: 1.35;
        }

        /* Variant 5: Monochrome Clean Typographic */
        .v5-foto-frame {
            border: 1px solid #111827;
        }
        .v5-header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #111827;
            margin-bottom: 0.14cm;
        }
        .v5-header td {
            padding-bottom: 0.08cm;
        }
        .v5-header-left {
            font-size: 7.2pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
        }
        .v5-header-right {
            font-size: 8pt;
            font-weight: bold;
            color: #111827;
            text-align: right;
        }
        .v5-nama {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #030712;
            line-height: 1.25;
            margin-bottom: 0.14cm;
        }
        .v5-alamat {
            font-size: 8.2pt;
            color: #374151;
            line-height: 1.38;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <?php
    $perPage = $rowsPerPage * $colsPerPage;
    $pages = array_chunk($items ?? [], $perPage);
    $totalPages = count($pages);
    ?>

    <?php foreach ($pages as $pageIndex => $pageItems) : ?>
        <?php $rows = array_chunk($pageItems, $colsPerPage); ?>
        <table class="sheet-table">
            <?php foreach ($rows as $rowItems) : ?>
                <tr>
                    <?php for ($col = 0; $col < $colsPerPage; $col++) : ?>
                        <td class="sheet-cell">
                            <?php if (isset($rowItems[$col])) : ?>
                                <?php $item = $rowItems[$col]; ?>
                                <?php
                                $fotoClass = ($variant === 4) ? 'foto-frame v4-foto-frame' : (($variant === 5) ? 'foto-frame v5-foto-frame' : 'foto-frame');
                                $fotoColClass = ($variant === 3) ? 'col-foto v3-col-foto' : 'col-foto';
                                ?>
                                <div class="nameplate-box <?= ($variant === 1) ? 'v1-box' : '' ?>">
                                    <?php if ($variant === 2) : ?>
                                        <div class="v2-outer-pad">
                                            <table class="v2-frame-1">
                                                <tr>
                                                    <td class="v2-frame-2">
                                                        <table style="width: 100%; border-collapse: collapse;">
                                                            <tr>
                                                                <td class="col-foto" style="padding: 0.05cm 0.14cm 0.05cm 0;">
                                                                    <div class="foto-frame">
                                                                        <?php if (!empty($item['fotoBase64'])) : ?>
                                                                            <img src="<?= $item['fotoBase64'] ?>" alt="Foto Santri">
                                                                        <?php else : ?>
                                                                            <table class="foto-placeholder"><tr><td>Foto<br>3 x 4</td></tr></table>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <td class="col-info" style="padding: 0.05cm 0.05cm 0.05cm 0.1cm;">
                                                                    <div class="v2-title">Identitas Pemilik Raport</div>
                                                                    <div class="v2-nama"><?= esc($item['namaSantri']) ?></div>
                                                                    <table class="v2-meta">
                                                                        <tr>
                                                                            <td style="width: 1.35cm; font-weight: bold;">ID Santri</td>
                                                                            <td style="width: 0.2cm;">:</td>
                                                                            <td style="font-weight: bold;"><?= esc($item['idSantri']) ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="font-weight: bold;">Alamat</td>
                                                                            <td>:</td>
                                                                            <td><?= esc($item['alamat']) ?></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php else : ?>
                                        <table class="nameplate-inner">
                                            <tr>
                                                <td class="<?= $fotoColClass ?>">
                                                    <div class="<?= $fotoClass ?>">
                                                        <?php if (!empty($item['fotoBase64'])) : ?>
                                                            <img src="<?= $item['fotoBase64'] ?>" alt="Foto Santri">
                                                        <?php else : ?>
                                                            <table class="foto-placeholder"><tr><td>Foto<br>3 x 4</td></tr></table>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="col-info">
                                                    <?php if ($variant === 1) : ?>
                                                        <div class="v1-id">ID Santri : <?= esc($item['idSantri']) ?></div>
                                                        <div class="v1-nama"><?= esc($item['namaSantri']) ?></div>
                                                        <div class="v1-alamat-label">Alamat</div>
                                                        <div class="v1-alamat-val"><?= esc($item['alamat']) ?></div>
                                                    <?php elseif ($variant === 3) : ?>
                                                        <div><span class="v3-badge">ID: <?= esc($item['idSantri']) ?></span></div>
                                                        <div class="v3-nama"><?= esc($item['namaSantri']) ?></div>
                                                        <div class="v3-alamat"><strong>Alamat:</strong> <?= esc($item['alamat']) ?></div>
                                                    <?php elseif ($variant === 4) : ?>
                                                        <div class="v4-strip">
                                                            <table class="v4-strip-table">
                                                                <tr>
                                                                    <td class="v4-strip-left">Identitas Santri</td>
                                                                    <td class="v4-strip-right"><?= esc($item['idSantri']) ?></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="v4-nama"><?= esc($item['namaSantri']) ?></div>
                                                        <div class="v4-alamat"><strong>Alamat:</strong> <?= esc($item['alamat']) ?></div>
                                                    <?php else : ?>
                                                        <table class="v5-header">
                                                            <tr>
                                                                <td class="v5-header-left">ID Santri</td>
                                                                <td class="v5-header-right"><?= esc($item['idSantri']) ?></td>
                                                            </tr>
                                                        </table>
                                                        <div class="v5-nama"><?= esc($item['namaSantri']) ?></div>
                                                        <div class="v5-alamat"><strong>Alamat :</strong> <?= esc($item['alamat']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if ($pageIndex < $totalPages - 1) : ?>
            <div class="page-break"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>
