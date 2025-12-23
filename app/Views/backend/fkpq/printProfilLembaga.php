<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
        }

        .header td {
            vertical-align: middle;
        }

        .brand {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #666;
            font-size: 11px;
        }

        .logo-section {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 20px;
            padding: 15px;
        }

        .logo-section img {
            max-width: 150px;
            max-height: 150px;
            margin-bottom: 10px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #222;
            letter-spacing: 1px;
            line-height: 1;
        }

        .header-section h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #222;
            letter-spacing: 1px;
            line-height: 1;
        }

        .header-section h3 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #222;
            letter-spacing: 1px;
            line-height: 1;
        }

        .divider {
            height: 2px;
            background: #000;
            margin: 8px 0 12px;
        }

        .section {
            border: none;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        table.kv {
            width: 100%;
            border-collapse: collapse;
        }

        table.kv td {
            padding: 5px 6px;
            vertical-align: top;
        }

        table.kv td.k {
            width: 200px;
            color: #444;
        }

        .grid {
            width: 100%;
        }

        .grid td {
            vertical-align: top;
        }

        .visi-misi-content {
            padding: 5px 0;
            line-height: 1.6;
        }

        .visi-misi-content p {
            margin: 3px 0;
        }

        .visi-misi-list {
            margin: 5px 0;
            padding-left: 20px;
        }

        .visi-misi-list li {
            margin: 3px 0;
        }

        @page {
            margin-top: 5mm;
            margin-bottom: 5mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }
    </style>
</head>

<body>
    <!-- Logo Section -->
    <?php if (!empty($logoPath)) : ?>
        <div class="logo-section">
            <img src="<?= $logoPath ?>" alt="Logo Lembaga" />
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="header-section">
        <h1>PROFIL LEMBAGA</h1>
        <h2>FORUM KOMUNIKASI PENDIDIKAN AL-QURAN (FKPQ)</h2>
    </div>

    <table class="header">
        <tr>
            <td style="width:70%;">
                <div class="brand">Profil Lembaga FKPQ</div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <!-- Informasi Lembaga Section -->
    <table class="grid">
        <tr>
            <td style="width:100%; padding-right:0;">
                <div class="section">
                    <div class="section-title">Informasi Lembaga</div>
                    <table class="kv">
                        <tr>
                            <td class="k">Nama Lembaga</td>
                            <td>: <?= htmlspecialchars($fkpq['NamaFkpq'] ?? '-') ?></td>
                        </tr>
                        <?php if (!empty($fkpq['IdFkpq'])) : ?>
                            <tr>
                                <td class="k">ID FKPQ</td>
                                <td>: <?= htmlspecialchars($fkpq['IdFkpq']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['Alamat'])) : ?>
                            <tr>
                                <td class="k">Alamat</td>
                                <td>: <?= htmlspecialchars($fkpq['Alamat']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['Kecamatan'])) : ?>
                            <tr>
                                <td class="k">Kecamatan</td>
                                <td>: <?= htmlspecialchars($fkpq['Kecamatan']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['KetuaFkpq'])) : ?>
                            <tr>
                                <td class="k">Ketua FKPQ</td>
                                <td>: <?= htmlspecialchars($fkpq['KetuaFkpq']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['TahunBerdiri'])) : ?>
                            <tr>
                                <td class="k">Tahun Berdiri</td>
                                <td>: <?= htmlspecialchars($fkpq['TahunBerdiri']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['NoHp'])) : ?>
                            <tr>
                                <td class="k">No. HP</td>
                                <td>: <?= htmlspecialchars($fkpq['NoHp']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($fkpq['TempatBelajar'])) : ?>
                            <tr>
                                <td class="k">Tempat Belajar</td>
                                <td>: <?= htmlspecialchars($fkpq['TempatBelajar']) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Visi dan Misi Section -->
    <?php if (!empty($fkpq['Visi']) || !empty($fkpq['Misi'])) : ?>
        <table class="grid">
            <tr>
                <td style="width:100%; padding-right:0;">
                    <?php if (!empty($fkpq['Visi'])) : ?>
                        <div class="section">
                            <div class="section-title">Visi</div>
                            <div class="visi-misi-content">
                                <?= formatVisi($fkpq['Visi']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($fkpq['Misi'])) : ?>
                        <div class="section">
                            <div class="section-title">Misi</div>
                            <div class="visi-misi-content">
                                <?= formatMisi($fkpq['Misi']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>
</body>

</html>