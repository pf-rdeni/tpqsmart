<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$isPortrait = ($template['Orientasi'] ?? 'landscape') === 'portrait';
$canvasW    = $isPortrait ? 794 : 1123;
$canvasH    = $isPortrait ? 1123 : 794;
?>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold"><i class="fas fa-award text-warning mr-2"></i>Preview Sertifikat Saya</h3>
            <div class="ml-auto">
                <a href="<?= base_url('backend/penghargaan/portal-santri/download/' . $penerima['Id']) ?>" class="btn btn-danger btn-sm mr-1" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                </a>
                <a href="<?= base_url('backend/penghargaan/portal-santri') ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body bg-dark text-center p-3" style="overflow: auto;">
            <div style="position: relative; display: inline-block; width: <?= $canvasW ?>px; height: <?= $canvasH ?>px; background: #fff url('<?= base_url($template['BackgroundFile']) ?>') center/cover no-repeat; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                <?php foreach ($fields as $f): ?>
                    <?php
                    $key         = $f['NamaField'];
                    $val         = $values[$key] ?? '';
                    $isHtmlField = in_array($key, ['LOGO_LEMBAGA', 'TTD_1_QR', 'TTD_2_QR', 'TTD_3_QR'], true);
                    ?>
                    <div style="
                        position: absolute;
                        left: <?= (int) $f['PosisiX'] ?>px;
                        top: <?= (int) $f['PosisiY'] ?>px;
                        width: <?= (int) $f['Lebar'] ?>px;
                        height: <?= (int) $f['Tinggi'] ?>px;
                        font-size: <?= (int) $f['FontSize'] ?>px;
                        font-family: <?= esc($f['FontFamily']) ?>, sans-serif;
                        font-weight: <?= !empty($f['FontBold']) ? 'bold' : 'normal' ?>;
                        color: <?= esc($f['FontColor']) ?>;
                        text-align: <?= esc($f['TextAlign']) ?>;
                        display: flex;
                        align-items: center;
                        justify-content: <?= $f['TextAlign'] === 'left' ? 'flex-start' : ($f['TextAlign'] === 'right' ? 'flex-end' : 'center') ?>;
                    ">
                        <?= $isHtmlField ? $val : esc($val) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
