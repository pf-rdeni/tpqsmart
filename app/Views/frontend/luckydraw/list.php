<?= $this->extend('frontend/template/publicTemplate'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="font-weight-bold"><i class="fas fa-list-alt text-primary mr-2"></i>Daftar Pemenang Lucky Draw</h2>
            <p class="text-muted">Berikut adalah daftar nomor undian yang telah mendapatkan hadiah.</p>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="py-3 px-4 text-center border-0" width="20%">No. Undian</th>
                                    <th class="py-3 px-4 border-0">Hadiah Didapat</th>
                                    <th class="py-3 px-4 text-center border-0" width="25%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pemenang)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 d-block text-light"></i>
                                            Belum ada daftar pemenang.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pemenang as $p) : ?>
                                        <tr>
                                            <td class="text-center py-3 px-4 align-middle">
                                                <span class="badge badge-light border border-secondary shadow-sm" style="font-size: 1.1rem; letter-spacing: 1px;">
                                                    <?= $p->no_undian ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 align-middle">
                                                <div class="font-weight-bold text-dark"><?= $p->nama_barang ?></div>
                                                <small class="text-muted">Kategori: <?= $p->kategori ?></small>
                                            </td>
                                            <td class="text-center py-3 px-4 align-middle">
                                                <?php if($p->status_diambil == 1): ?>
                                                    <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fas fa-check mr-1"></i>Sudah Diambil</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-clock mr-1"></i>Belum Diambil</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('luckydraw') ?>" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-search mr-2"></i>Cek Nomor Undian Saya
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
