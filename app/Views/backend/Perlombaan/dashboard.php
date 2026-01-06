<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Statistik Ringkas -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['total_lomba'] ?></h3>
                        <p>Lomba Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan') ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['total_cabang'] ?></h3>
                        <p>Cabang Lomba</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan') ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $stats['total_peserta'] ?></h3>
                        <p>Peserta Terdaftar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan/pendaftaran') ?>" class="small-box-footer">Kelola Peserta <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['total_juri'] ?></h3>
                        <p>Total Juri</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="small-box-footer" style="height: 30px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Navigator -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-th"></i> Menu Cepat</h3>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan') ?>" class="btn btn-outline-primary btn-block p-3">
                                    <i class="fas fa-trophy fa-2x d-block mb-1"></i>
                                    <small>Master Lomba</small>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/pendaftaran') ?>" class="btn btn-outline-info btn-block p-3">
                                    <i class="fas fa-user-plus fa-2x d-block mb-1"></i>
                                    <small>Pendaftaran</small>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/pengundian') ?>" class="btn btn-outline-success btn-block p-3">
                                    <i class="fas fa-random fa-2x d-block mb-1"></i>
                                    <small>Pengundian</small>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/monitorNilai') ?>" class="btn btn-outline-warning btn-block p-3">
                                    <i class="fas fa-chart-bar fa-2x d-block mb-1"></i>
                                    <small>Monitoring</small>
                                </a>
                            </div>
                            <?php if (in_groups('Admin')): ?>
                                <div class="col-6 mb-2">
                                    <a href="<?= base_url('backend/perlombaan/viewHasil') ?>" class="btn btn-outline-secondary btn-block p-3">
                                        <i class="fas fa-poll fa-2x d-block mb-1"></i>
                                        <small>Hasil Akhir</small>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/peringkat') ?>" class="btn btn-outline-danger btn-block p-3">
                                    <i class="fas fa-medal fa-2x d-block mb-1"></i>
                                    <small>Juara & Skor</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Lomba Summary -->
            <div class="col-md-8">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-running"></i> Perlombaan Sedang Berjalan</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Lomba</th>
                                    <th class="text-center">Cabang</th>
                                    <th class="text-center">Peserta</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lomba_list)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada lomba aktif</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lomba_list as $l): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($l['NamaLomba']) ?></strong><br>
                                                <small class="text-muted">
                                                    <?= $l['NamaTpq'] ? esc($l['NamaTpq']) . ' (' . esc($l['KelurahanDesa']) . ')' : 'Lomba Umum/Pusat' ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $l['total_cabang'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success"><?= $l['total_peserta'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary"><?= ucfirst($l['Status']) ?></span>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= base_url('backend/perlombaan/setCabang/' . $l['id']) ?>" class="btn btn-xs btn-info" title="Detail Cabang">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
