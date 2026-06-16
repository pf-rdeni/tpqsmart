<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Pilih Kegiatan Lucky Draw</h1>
                        <p class="mb-4">Silakan pilih kegiatan mana yang ingin Anda kelola saat ini.</p>
                    </div>

                    <?php if (empty($kegiatan)): ?>
                        <div class="alert alert-warning text-center">
                            Tidak ada kegiatan yang ditugaskan kepada Anda atau belum ada kegiatan yang dibuat.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($kegiatan as $k): ?>
                                <a href="<?= base_url('backend/luckydraw/pilih/set/' . $k->id) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= esc($k->nama_kegiatan) ?></strong><br>
                                        <small class="text-muted"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($k->tanggal_kegiatan)) ?></small>
                                    </div>
                                    <i class="fas fa-chevron-right text-primary"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
