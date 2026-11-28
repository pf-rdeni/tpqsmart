<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-3x text-warning mb-3"></i>
            <h4 class="font-weight-bold"><?= esc($modul ?? 'Fitur Penghargaan') ?></h4>
            <p class="text-muted mb-4">
                Fitur ini dijadwalkan untuk diimplementasikan pada <strong><?= esc($phase ?? 'phase berikutnya') ?></strong> sesuai rencana bertahap.
            </p>
            <a href="<?= base_url('backend/penghargaan/dashboard') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Penghargaan
            </a>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
