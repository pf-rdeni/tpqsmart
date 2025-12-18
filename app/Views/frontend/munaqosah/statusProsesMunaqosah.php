<?php
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
$peserta = $peserta ?? [];
$statusGrup = $statusGrup ?? [];
$typeUjian = $typeUjian ?? 'munaqosah';

// Tentukan label dan icon berdasarkan type ujian
$typeUjianLabel = ($typeUjian === 'pra-munaqosah') ? 'Pra-Munaqosah' : 'Munaqosah';
$typeUjianIcon = ($typeUjian === 'pra-munaqosah') ? 'fa-book-reader' : 'fa-graduation-cap';
$pageTitle = 'Status Proses ' . $typeUjianLabel;
?>

<?= $this->extend($templatePath); ?>
<?= $this->section('content'); ?>

<style>
    body {
        background-color: #f5f5f5;
    }

    .status-card {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .title-container {
        position: relative;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 3px solid #4caf50;
    }

    .title-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .blue-line-vertical {
        width: 5px;
        height: 40px;
        background: linear-gradient(180deg, #2196F3 0%, #1976D2 100%);
        border-radius: 3px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(33, 150, 243, 0.3);
    }

    .title-icon {
        font-size: 32px;
        color: #2196F3;
        flex-shrink: 0;
    }

    .card-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.3;
        text-transform: none;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .info-section {
        margin-bottom: 25px;
        padding: 15px 15px 15px 20px;
        background-color: #f9f9f9;
        border-radius: 6px;
        border-left: 5px solid #2196F3;
        position: relative;
    }

    .info-section::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15px;
        width: 5px;
        height: 20px;
        background: linear-gradient(180deg, #2196F3 0%, #1976D2 100%);
        border-radius: 0 3px 3px 0;
    }

    .info-label {
        font-weight: 600;
        color: #555;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        color: #000;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 24px;
        }

        .blue-line-vertical {
            height: 35px;
            width: 4px;
        }

        .title-icon {
            font-size: 28px;
        }
    }

    .status-list {
        margin-top: 30px;
    }

    .status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border-left: 4px solid #ddd;
        transition: all 0.3s;
    }

    .status-item:hover {
        background-color: #f0f0f0;
    }

    .status-item.completed {
        border-left-color: #4caf50;
        background-color: #e8f5e9;
    }

    .status-item.pending {
        border-left-color: #f44336;
        background-color: #ffebee;
    }

    .grup-info {
        flex: 1;
    }

    .grup-name {
        font-size: 18px;
        font-weight: 600;
        color: #000;
        margin-bottom: 5px;
    }

    .grup-detail {
        font-size: 14px;
        color: #666;
    }

    .status-icon {
        font-size: 32px;
        margin-left: 20px;
    }

    .status-icon.check {
        color: #4caf50;
    }

    .status-icon.cross {
        color: #f44336;
    }

    .btn-back {
        margin-top: 30px;
        text-align: center;
    }

    .btn-back a {
        display: inline-block;
        padding: 10px 30px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .btn-back a:hover {
        background-color: #5a6268;
    }

    .note-section {
        margin-top: 40px;
        padding: 20px;
        background-color: #e3f2fd;
        border-left: 5px solid #2196F3;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .note-title {
        font-size: 16px;
        font-weight: 700;
        color: #1565C0;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .note-title i {
        font-size: 18px;
    }

    .note-content {
        font-size: 14px;
        color: #424242;
        line-height: 1.7;
        margin: 0;
        text-align: justify;
    }

    .note-content strong {
        color: #1565C0;
        font-weight: 600;
    }
</style>

<div class="status-card">
    <div class="title-container">
        <div class="title-wrapper">
            <div class="blue-line-vertical"></div>
            <i class="fas <?= $typeUjianIcon ?> title-icon"></i>
            <h2 class="card-title">Status Proses <?= esc($typeUjianLabel) ?></h2>
        </div>
    </div>

    <div class="info-section">
        <div class="info-label">Nama Santri:</div>
        <div class="info-value"><?= esc($peserta['NamaSantri'] ?? '-') ?></div>
    </div>

    <div class="info-section">
        <div class="info-label">TPQ:</div>
        <div class="info-value"><?= esc($peserta['NamaTpq'] ?? '-') ?></div>
    </div>

    <div class="status-list">
        <?php if (empty($statusGrup)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada data grup materi ujian untuk peserta ini.
            </div>
        <?php else: ?>
            <?php foreach ($statusGrup as $item): ?>
                <?php
                $grup = $item['grup'];
                $selesai = $item['selesai'];
                $jumlahMateri = $item['jumlah_materi'];
                $jumlahNilai = $item['jumlah_nilai'];
                $statusClass = $selesai ? 'completed' : 'pending';
                $iconClass = $selesai ? 'check' : 'cross';
                $iconSymbol = $selesai ? '✓' : '✗';
                $statusText = $selesai ? 'Selesai' : 'Belum Selesai';
                ?>
                <div class="status-item <?= $statusClass ?>">
                    <div class="grup-info">
                        <div class="grup-name"><?= esc($grup['NamaMateriGrup'] ?? $grup['IdGrupMateriUjian'] ?? '-') ?></div>
                        <div class="grup-detail">
                            Materi: <?= $jumlahMateri ?> | Nilai: <?= $jumlahNilai ?> / <?= $jumlahMateri ?> | Status: <?= $statusText ?>
                        </div>
                    </div>
                    <div class="status-icon <?= $iconClass ?>">
                        <?php if ($selesai): ?>
                            <i class="fas fa-check-circle"></i>
                        <?php else: ?>
                            <i class="fas fa-times-circle"></i>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php
    // Cek apakah ada grup yang belum selesai
    $adaYangBelumSelesai = false;
    if (!empty($statusGrup)) {
        foreach ($statusGrup as $item) {
            if (!$item['selesai']) {
                $adaYangBelumSelesai = true;
                break;
            }
        }
    }
    ?>
    
    <?php if ($adaYangBelumSelesai): ?>
        <div class="note-section">
            <div class="note-title">
                <i class="fas fa-info-circle"></i>
                <span>Catatan Penting</span>
            </div>
            <p class="note-content">
                Apabila Ananda melihat status silang (belum selesai) pada grup materi ujian di atas, hal tersebut dapat berarti bahwa proses input nilai belum sepenuhnya selesai dilakukan karena memerlukan waktu dalam memproses data. Ananda dapat melihat dan memperbarui status secara berkala melalui halaman ini. Selain itu, Ananda juga dapat merujuk pada <strong>manual checklist dari panitia pada kartu peserta ujian</strong> yang menunjukkan bahwa Ananda sudah melakukan proses ujian munaqosah untuk memastikan kelengkapan data.
            </p>
        </div>
    <?php endif; ?>

    <div class="btn-back">
        <a href="<?= base_url('munaqosah/konfirmasi-data') ?>">
            <i class="fas fa-arrow-left"></i> Kembali ke Konfirmasi Data
        </a>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Animasi fade in untuk status items
        $('.status-item').each(function(index) {
            $(this).delay(index * 100).fadeIn();
        });
    });
</script>
<?= $this->endSection(); ?>