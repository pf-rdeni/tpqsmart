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
            width: 100%;
            border-collapse: separate;
            border-spacing: 0.2cm 0.25cm;
        }

        .sheet-cell {
            width: 10cm;
            height: 4.8cm;
            vertical-align: top;
            padding: 0;
        }

        .nameplate-box {
            width: 9.95cm;
            height: 4.75cm;
            border: 1px dashed #9ca3af;
            border-left: 5px solid #047857;
            background-color: #ffffff;
            overflow: hidden;
        }

        .nameplate-inner {
            width: 100%;
            height: 4.75cm;
            border-collapse: collapse;
        }

        .col-foto {
            width: 3.35cm;
            vertical-align: middle;
            text-align: center;
            padding: 0.35cm 0.15cm 0.35cm 0.25cm;
        }

        .foto-frame {
            width: 3cm;
            height: 4cm;
            border: 1px solid #9ca3af;
            margin: 0 auto;
            text-align: center;
            overflow: hidden;
            background-color: #f8fafc;
        }

        .foto-frame img {
            width: 3cm;
            height: 4cm;
            object-fit: cover;
            display: block;
        }

        .foto-placeholder {
            width: 3cm;
            height: 4cm;
            border-collapse: collapse;
        }

        .foto-placeholder td {
            vertical-align: middle;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            color: #64748b;
            line-height: 1.4;
        }

        .col-info {
            vertical-align: middle;
            padding: 0.3cm 0.32cm 0.3cm 0.18cm;
        }

        .info-id {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #065f46;
            margin-bottom: 0.08cm;
        }

        .info-nama {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            line-height: 1.25;
            padding-bottom: 0.15cm;
            border-bottom: 1px solid #d1d5db;
        }

        .alamat-wrap {
            margin-top: 0.18cm;
        }

        .alamat-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9ca3af;
            margin-bottom: 0.04cm;
        }

        .alamat-val {
            font-size: 8.2pt;
            color: #374151;
            line-height: 1.38;
            word-wrap: break-word;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <?php
    $rowsPerPage = $rowsPerPage ?? 5;
    $perPage = $rowsPerPage * 2;
    $pages = array_chunk($items ?? [], $perPage);
    $totalPages = count($pages);
    ?>

    <?php foreach ($pages as $pageIndex => $pageItems) : ?>
        <?php $rows = array_chunk($pageItems, 2); ?>
        <table class="sheet-table">
            <?php foreach ($rows as $rowItems) : ?>
                <tr>
                    <?php for ($col = 0; $col < 2; $col++) : ?>
                        <td class="sheet-cell">
                            <?php if (isset($rowItems[$col])) : ?>
                                <?php $item = $rowItems[$col]; ?>
                                <div class="nameplate-box">
                                    <table class="nameplate-inner">
                                        <tr>
                                            <td class="col-foto">
                                                <div class="foto-frame">
                                                    <?php if (!empty($item['fotoBase64'])) : ?>
                                                        <img src="<?= $item['fotoBase64'] ?>" alt="Foto Santri">
                                                    <?php else : ?>
                                                        <table class="foto-placeholder">
                                                            <tr>
                                                                <td>Foto<br>3 x 4</td>
                                                            </tr>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="col-info">
                                                <div class="info-id">ID Santri : <?= esc($item['idSantri']) ?></div>
                                                <div class="info-nama"><?= esc($item['namaSantri']) ?></div>
                                                <div class="alamat-wrap">
                                                    <div class="alamat-label">Alamat</div>
                                                    <div class="alamat-val"><?= esc($item['alamat']) ?></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
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
