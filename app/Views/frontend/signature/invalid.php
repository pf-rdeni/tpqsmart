<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Validasi Dokumen') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5" style="max-width: 650px;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-<?= esc($type ?? 'danger') ?> text-white py-3">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas <?= ($type ?? 'danger') === 'info' ? 'fa-info-circle' : (($type ?? 'danger') === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle') ?> me-2"></i>
                    <?= esc($title ?? 'Tanda Tangan Tidak Valid') ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-<?= esc($type ?? 'danger') ?> mb-3">
                    <?= esc($message) ?>
                </div>
                <p class="text-muted small mb-0">Silakan hubungi administrator lembaga TPQ / FKPQ untuk informasi dan konfirmasi lebih lanjut.</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>