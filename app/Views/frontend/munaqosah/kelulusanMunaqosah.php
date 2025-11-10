<?php
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
$peserta = $peserta ?? [];
$totalBobot = $totalBobot ?? 0;
$threshold = $threshold ?? 65;
$lulus = $lulus ?? false;
$status = $status ?? 'Belum Lulus';
$noPeserta = $noPeserta ?? '';
$typeUjian = $typeUjian ?? 'munaqosah';

// Tentukan label dan icon berdasarkan type ujian
$typeUjianLabel = ($typeUjian === 'pra-munaqosah') ? 'Pra-Munaqosah' : 'Munaqosah';
$typeUjianIcon = ($typeUjian === 'pra-munaqosah') ? 'fa-book-reader' : 'fa-graduation-cap';
$pageTitle = 'Hasil Kelulusan ' . $typeUjianLabel;
?>

<?= $this->extend($templatePath); ?>
<?= $this->section('content'); ?>

<style>
    body {
        background-color: #f5f5f5;
    }

    .kelulusan-card {
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

    .green-line-vertical {
        width: 5px;
        height: 40px;
        background: linear-gradient(180deg, #4caf50 0%, #45a049 100%);
        border-radius: 3px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .title-icon {
        font-size: 32px;
        color: #4caf50;
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

        .green-line-vertical {
            height: 35px;
            width: 4px;
        }

        .title-icon {
            font-size: 28px;
        }
    }

    .status-section {
        text-align: center;
        margin: 40px 0;
        padding: 30px;
        border-radius: 12px;
    }

    .status-section.lulus {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border: 3px solid #4caf50;
    }

    .status-section.tidak-lulus {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        border: 3px solid #f44336;
    }

    .status-icon {
        font-size: 80px;
        margin-bottom: 20px;
    }

    .status-icon.lulus {
        color: #4caf50;
    }

    .status-icon.tidak-lulus {
        color: #f44336;
    }

    .status-text {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .status-text.lulus {
        color: #2e7d32;
    }

    .status-text.tidak-lulus {
        color: #c62828;
    }

    .score-section {
        margin-top: 30px;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .score-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .score-row:last-child {
        border-bottom: none;
    }

    .score-label {
        font-size: 16px;
        color: #555;
        font-weight: 500;
    }

    .score-value {
        font-size: 20px;
        font-weight: bold;
    }

    .score-value.lulus {
        color: #4caf50;
    }

    .score-value.tidak-lulus {
        color: #f44336;
    }

    .motivation-section {
        margin: 30px 0;
        padding: 25px;
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
    }

    .motivation-title {
        font-size: 18px;
        font-weight: bold;
        color: #856404;
        margin-bottom: 10px;
    }

    .motivation-text {
        font-size: 16px;
        color: #856404;
        line-height: 1.6;
        margin: 0;
    }

    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn-action {
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-pdf {
        background-color: #f44336;
        color: white;
    }

    .btn-pdf:hover {
        background-color: #d32f2f;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
    }

    .btn-back:hover {
        background-color: #5a6268;
    }
</style>

<div class="kelulusan-card">
    <div class="title-container">
        <div class="title-wrapper">
            <div class="green-line-vertical"></div>
            <i class="fas <?= $typeUjianIcon ?> title-icon"></i>
            <h2 class="card-title">Hasil Kelulusan <?= esc($typeUjianLabel) ?></h2>
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

    <div class="status-section <?= $lulus ? 'lulus' : 'tidak-lulus' ?>">
        <div class="status-icon <?= $lulus ? 'lulus' : 'tidak-lulus' ?>">
            <?php if ($lulus): ?>
                <i class="fas fa-check-circle"></i>
            <?php else: ?>
                <i class="fas fa-times-circle"></i>
            <?php endif; ?>
        </div>
        <div class="status-text <?= $lulus ? 'lulus' : 'tidak-lulus' ?>"><?= esc($status) ?></div>

        <div class="score-section">
            <div class="score-row">
                <span class="score-label">Total Nilai Bobot:</span>
                <span class="score-value <?= $lulus ? 'lulus' : 'tidak-lulus' ?>"><?= number_format($totalBobot, 2) ?></span>
            </div>
            <div class="score-row">
                <span class="score-label">Nilai Minimal Kelulusan:</span>
                <span class="score-value"><?= number_format($threshold, 2) ?></span>
            </div>
            <div class="score-row">
                <span class="score-label">Selisih:</span>
                <span class="score-value <?= $lulus ? 'lulus' : 'tidak-lulus' ?>"><?= number_format($totalBobot - $threshold, 2) ?></span>
            </div>
        </div>
    </div>

    <div class="motivation-section">
        <div class="motivation-title">
            <?php if ($lulus): ?>
                <i class="fas fa-star"></i> Selamat! Pesan untuk Kelulusan
            <?php else: ?>
                <i class="fas fa-heart"></i> Pesan Motivasi
            <?php endif; ?>
        </div>
        <p class="motivation-text">
            <?php if ($lulus): ?>
                Alhamdulillah, selamat atas kelulusan Anda dalam ujian munaqosah! Pencapaian ini adalah hasil dari kerja keras, ketekunan, dan kesungguhan Anda dalam belajar. Teruslah semangat belajar dan jangan pernah berhenti untuk meningkatkan kemampuan membaca Al-Qur'an Anda. Keberhasilan ini adalah awal yang baik untuk langkah selanjutnya dalam perjalanan keilmuan Islam Anda. Semoga Allah SWT selalu memberkati setiap langkah Anda. Barakallahu fiik!
            <?php else: ?>
                Terima kasih atas usaha yang telah Anda lakukan dalam ujian munaqosah ini. Meskipun hasil belum sesuai harapan, janganlah berkecil hati. Kegagalan adalah bagian dari proses pembelajaran. Gunakan pengalaman ini sebagai motivasi untuk terus belajar dan meningkatkan kemampuan membaca Al-Qur'an Anda. Ketekunan dan kesabaran adalah kunci kesuksesan. Teruslah semangat dan jangan pernah menyerah! InsyaAllah dengan usaha yang lebih keras lagi, kesuksesan akan menghampiri Anda. Semangat!
            <?php endif; ?>
        </p>
    </div>

    <div class="button-group">
        <a href="<?= base_url('munaqosah/generate-surat-kelulusan?typeUjian=' . urlencode($typeUjian)) ?>" class="btn-action btn-pdf" target="_blank">
            <i class="fas fa-file-pdf"></i> Surat Keterangan Kelulusan
        </a>
        <a href="<?= base_url('munaqosah/konfirmasi-data') ?>" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Animasi fade in
        $('.status-section').hide().fadeIn(500);
        $('.motivation-section').hide().delay(300).fadeIn(500);
    });
</script>
<?= $this->endSection(); ?>