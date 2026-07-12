<?= $this->extend('frontend/template/publicTemplateNoMenu'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-center"><i class="fas fa-gift"></i> Pilih Kegiatan Lucky Draw</h5>
                </div>
                <div class="card-body">
                    <p class="text-center mb-4">Saat ini ada beberapa kegiatan Lucky Draw yang sedang berlangsung. Silakan pilih kegiatan yang ingin Anda cek.</p>
                    
                    <div class="list-group">
                        <?php foreach($kegiatan as $k): ?>
                            <a href="<?= base_url('luckydraw/pilih/set/'.$k->id) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-primary font-weight-bold"><?= esc($k->nama_kegiatan) ?></h6>
                                    <small class="text-muted"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($k->tanggal_kegiatan)) ?></small>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
