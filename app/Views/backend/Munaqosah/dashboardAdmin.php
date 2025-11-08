<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Admin</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Tahun Ajaran -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-calendar"></i> Tahun Ajaran: <?= esc(convertTahunAjaran($current_tahun_ajaran)) ?></h5>
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
                                    <a href="<?= $menu_items['data_juri'] ?>" class="small-box-footer">
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

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Konfigurasi & Setup -->
                                            <div class="col-md-6 mb-4">
                                                <h5><i class="fas fa-cog"></i> Konfigurasi & Setup</h5>
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['kategori_materi'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tags"></i> Kategori Materi
                                                    </a>
                                                    <a href="<?= $menu_items['kategori_kesalahan'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-exclamation-triangle"></i> Kategori Kesalahan
                                                    </a>
                                                    <a href="<?= $menu_items['grup_materi_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-layer-group"></i> Grup Materi Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['materi_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-book"></i> Materi Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['bobot_nilai'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-percentage"></i> Bobot Nilai
                                                    </a>
                                                    <a href="<?= $menu_items['konfigurasi'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-sliders-h"></i> Konfigurasi
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Data & Monitoring -->
                                            <div class="col-md-6 mb-4">
                                                <h5><i class="fas fa-chart-bar"></i> Data & Monitoring</h5>
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tachometer-alt"></i> Dashboard Monitoring
                                                    </a>
                                                    <a href="<?= $menu_items['monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-eye"></i> Monitoring Munaqosah
                                                    </a>
                                                    <a href="<?= $menu_items['kelulusan'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-certificate"></i> Kelulusan Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['data_juri'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-tie"></i> Data Juri
                                                    </a>
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-users"></i> Daftar Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-plus"></i> Registrasi Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['jadwal_peserta_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-calendar-alt"></i> Jadwal Peserta Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['antrian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-list"></i> Antrian Ujian
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
    </div>
</section>
<?= $this->endSection(); ?>