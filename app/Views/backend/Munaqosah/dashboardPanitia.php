<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Panitia</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Tahun Ajaran & Type Ujian -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0 py-2 d-flex align-items-center">
                                    <i class="fas fa-calendar mr-2" style="font-size: 1.1em;"></i>
                                    <strong>Tahun Ajaran:</strong> <span class="ml-1"><?= esc(convertTahunAjaran($current_tahun_ajaran)) ?></span>
                                    <span class="mx-3">|</span>
                                    <i class="fas fa-graduation-cap mr-2" style="font-size: 1.1em;"></i>
                                    <strong>Type Ujian:</strong> <span class="badge badge-primary ml-1"><?= esc($type_ujian) ?></span>
                                </div>
                            </div>
                        </div>
                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-users text-primary"></i> Daftar Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-plus text-success"></i> Registrasi Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['jadwal_peserta_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-calendar-alt text-info"></i> Jadwal Peserta Ujian
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['antrian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-list text-warning"></i> Antrian Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tachometer-alt text-secondary"></i> Dashboard Monitoring
                                                    </a>
                                                    <a href="<?= $menu_items['monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-eye text-dark"></i> Monitoring Munaqosah
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Card -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= number_format($total_peserta) ?></h3>
                                        <p>Total Peserta</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= number_format($total_sudah_dinilai) ?></h3>
                                        <p>Sudah Dinilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="<?= $menu_items['monitoring'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= number_format($total_belum_dinilai) ?></h3>
                                        <p>Belum Dinilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="small-box-footer">
                                        Registrasi Peserta <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?= number_format($total_juri) ?></h3>
                                        <p>Total Juri Aktif</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Penilaian -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Progress Penilaian</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $progressPercent = $total_peserta > 0
                                            ? round(($total_sudah_dinilai / $total_peserta) * 100)
                                            : 0;
                                        ?>
                                        <div class="progress mb-3" style="height: 30px;">
                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                style="width: <?= $progressPercent ?>%"
                                                aria-valuenow="<?= $progressPercent ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                <?= $progressPercent ?>%
                                            </div>
                                        </div>
                                        <p class="text-center">
                                            <strong><?= $total_sudah_dinilai ?></strong> dari <strong><?= $total_peserta ?></strong> peserta telah dinilai
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Antrian -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Antrian Selesai</span>
                                        <span class="info-box-number"><?= number_format($antrian_selesai) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Antrian Menunggu</span>
                                        <span class="info-box-number"><?= number_format($antrian_menunggu) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-spinner"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sedang Ujian</span>
                                        <span class="info-box-number"><?= number_format($antrian_proses) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Antrian per Grup Materi -->
                        <?php if (!empty($antrian_data)): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Statistik Antrian per Grup Materi</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Grup Materi</th>
                                                            <th>Total</th>
                                                            <th>Menunggu</th>
                                                            <th>Sedang Ujian</th>
                                                            <th>Selesai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; ?>
                                                        <?php foreach ($antrian_data as $grupId => $data): ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= esc($data['nama']) ?></td>
                                                                <td><span class="badge badge-info"><?= number_format($data['total']) ?></span></td>
                                                                <td><span class="badge badge-warning"><?= number_format($data['menunggu']) ?></span></td>
                                                                <td><span class="badge badge-danger"><?= number_format($data['proses']) ?></span></td>
                                                                <td><span class="badge badge-success"><?= number_format($data['selesai']) ?></span></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>