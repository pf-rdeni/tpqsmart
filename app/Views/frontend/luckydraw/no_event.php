<?= $this->extend('frontend/template/publicTemplateNoMenu'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-left-warning">
                <div class="card-body text-center p-5">
                    <i class="fas fa-info-circle fa-4x text-warning mb-4"></i>
                    <h4 class="text-gray-800 mb-3">Tidak Ada Kegiatan Aktif</h4>
                    <p class="text-gray-600">Mohon maaf, saat ini belum ada acara pengundian Lucky Draw yang sedang berlangsung. Harap nantikan <i>event-event</i> menarik kami selanjutnya!</p>
                    <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
