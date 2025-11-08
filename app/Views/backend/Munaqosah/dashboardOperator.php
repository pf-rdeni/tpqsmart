<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Operator</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info TPQ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-building"></i> Informasi TPQ</h5>
                                    <p class="mb-1"><strong>Nama TPQ:</strong> <?= esc($data_tpq->NamaTpq ?? '-') ?></p>
                                    <p class="mb-1"><strong>ID TPQ:</strong> <?= esc($id_tpq) ?></p>
                                    <p class="mb-0"><strong>Tahun Ajaran:</strong> <?= esc($current_tahun_ajaran) ?></p>
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
                                        <p>Juri Aktif</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <a href="<?= $menu_items['data_juri'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Type Ujian -->
                        <div class="row">
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?= number_format($statistik_munaqosah) ?></h3>
                                        <p>Munaqosah</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= number_format($statistik_pra_munaqosah) ?></h3>
                                        <p>Pra-Munaqosah</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
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

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="btn btn-primary btn-block btn-lg">
                                                    <i class="fas fa-tachometer-alt"></i><br>Dashboard Monitoring
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['monitoring'] ?>" class="btn btn-info btn-block btn-lg">
                                                    <i class="fas fa-eye"></i><br>Monitoring Munaqosah
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['kelulusan'] ?>" class="btn btn-success btn-block btn-lg">
                                                    <i class="fas fa-certificate"></i><br>Kelulusan Ujian
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['konfigurasi'] ?>" class="btn btn-warning btn-block btn-lg">
                                                    <i class="fas fa-sliders-h"></i><br>Konfigurasi
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['data_juri'] ?>" class="btn btn-secondary btn-block btn-lg">
                                                    <i class="fas fa-user-tie"></i><br>Data Juri
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['daftar_peserta'] ?>" class="btn btn-dark btn-block btn-lg">
                                                    <i class="fas fa-users"></i><br>Daftar Peserta
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['registrasi_peserta'] ?>" class="btn btn-success btn-block btn-lg">
                                                    <i class="fas fa-user-plus"></i><br>Registrasi Peserta
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['antrian'] ?>" class="btn btn-info btn-block btn-lg">
                                                    <i class="fas fa-list"></i><br>Antrian Ujian
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

