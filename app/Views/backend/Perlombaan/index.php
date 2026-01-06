<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-12">
                <?php if (in_groups('Admin') || in_groups('Operator')): ?>
                    <a href="<?= base_url('backend/perlombaan/listLomba') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Lomba Baru
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        // Get current operator's TPQ
        $myIdTpq = session()->get('IdTpq');
        $isAdmin = in_groups('Admin');
        ?>

        <div class="row">
            <?php if (empty($lomba_list)): ?>
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada perlombaan yang tersedia.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($lomba_list as $lomba): ?>
                    <?php 
                    // Check ownership
                    $isAdminCreated = empty($lomba['IdTpq']); // Admin created (no TPQ)
                    $isMyLomba = !empty($lomba['IdTpq']) && $lomba['IdTpq'] == $myIdTpq; // My TPQ's lomba
                    $canFullAccess = $isAdmin || $isMyLomba; // Admin or owner has full access
                    ?>
                    <div class="col-md-4">
                        <div class="card card-outline <?= $lomba['Status'] === 'aktif' ? 'card-primary' : ($lomba['Status'] === 'selesai' ? 'card-success' : 'card-secondary') ?>">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-trophy"></i> <?= esc($lomba['NamaLomba']) ?>
                                </h3>
                                <div class="card-tools">
                                    <?php if ($isAdminCreated): ?>
                                        <span class="badge badge-dark mr-1" title="Lomba dari Pusat">
                                            <i class="fas fa-globe"></i> Pusat
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info mr-1" title="Lomba TPQ">
                                            <i class="fas fa-mosque"></i> TPQ
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge badge-<?= $lomba['Status'] === 'aktif' ? 'primary' : ($lomba['Status'] === 'selesai' ? 'success' : 'secondary') ?>">
                                        <?= ucfirst($lomba['Status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><?= esc($lomba['Deskripsi'] ?: 'Tidak ada deskripsi') ?></p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-calendar"></i> Mulai: <?= $lomba['TanggalMulai'] ? date('d M Y', strtotime($lomba['TanggalMulai'])) : '-' ?></li>
                                    <li><i class="fas fa-calendar-check"></i> Selesai: <?= $lomba['TanggalSelesai'] ? date('d M Y', strtotime($lomba['TanggalSelesai'])) : '-' ?></li>
                                    <li><i class="fas fa-sitemap"></i> Cabang: <strong><?= $lomba['total_cabang'] ?? 0 ?></strong></li>
                                    <li><i class="fas fa-users"></i> Peserta: <strong><?= $lomba['total_peserta'] ?? 0 ?></strong></li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group btn-group-sm w-100">
                                    <?php if ($canFullAccess): ?>
                                        <!-- Full access: Edit, Cabang, Pendaftaran, Peringkat -->
                                        <a href="<?= base_url('backend/perlombaan/listLomba/' . $lomba['id']) ?>" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('backend/perlombaan/setCabang/' . $lomba['id']) ?>" class="btn btn-info" title="Cabang">
                                            <i class="fas fa-sitemap"></i>
                                        </a>
                                    <?php endif; ?>
                                    <!-- Pendaftaran and Peringkat always available for operators -->
                                    <a href="<?= base_url('backend/perlombaan/pendaftaran') ?>?lomba_id=<?= $lomba['id'] ?>" class="btn btn-success" title="Pendaftaran">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                    <a href="<?= base_url('backend/perlombaan/peringkat') ?>?lomba_id=<?= $lomba['id'] ?>" class="btn btn-secondary" title="Peringkat">
                                        <i class="fas fa-medal"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
