<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Juri</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Juri -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-user"></i> Informasi Juri</h5>
                                    <p class="mb-1"><strong>Username:</strong> <?= esc($juri_data->UsernameJuri) ?></p>
                                    <p class="mb-1"><strong>ID Juri:</strong> <?= esc($juri_data->IdJuri) ?></p>
                                    <p class="mb-1"><strong>Grup Materi:</strong> <?= esc($juri_data->NamaMateriGrup ?? '-') ?></p>
                                    <p class="mb-1"><strong>Ruangan:</strong> <?= esc($juri_data->RoomId ?? '-') ?></p>
                                    <p class="mb-1"><strong>Type Ujian:</strong>
                                        <?php if ($type_ujian == 'munaqosah'): ?>
                                            <span class="badge badge-primary">Munaqosah</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pra-Munaqosah</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-0"><strong>Tahun Ajaran:</strong> <?= esc($current_tahun_ajaran) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <a href="<?= base_url('backend/munaqosah/input-nilai-juri') ?>" class="btn btn-primary btn-block">
                                                    <i class="fas fa-edit"></i><br>Input Nilai Juri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <a href="<?= base_url('backend/munaqosah/data-nilai-juri') ?>" class="btn btn-info btn-block">
                                                    <i class="fas fa-list"></i><br>Data Nilai Juri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <a href="<?= base_url('backend/munaqosah/monitoring-antrian-peserta-ruangan-juri') ?>" target="_blank" class="btn btn-warning btn-block">
                                                    <i class="fas fa-tasks"></i><br>Antrian Peserta
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <a href="<?= base_url('backend/munaqosah/dashboard-monitoring') ?>" class="btn btn-success btn-block">
                                                    <i class="fas fa-chart-line"></i><br>Dashboard Monitoring
                                                </a>
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
                                        <h3><?= number_format($total_peserta_terdaftar) ?></h3>
                                        <p>Total Peserta Terdaftar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= base_url('backend/munaqosah/input-nilai-juri') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= number_format($total_peserta_sudah_dinilai_juri_ini) ?> / <?= number_format($total_peserta_sudah_dinilai) ?></h3>
                                        <p>Peserta Sudah Dinilai Juri Ini</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="<?= base_url('backend/munaqosah/data-nilai-juri') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= number_format($total_peserta_belum_dinilai) ?></h3>
                                        <p>Peserta Belum Dinilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="<?= base_url('backend/munaqosah/input-nilai-juri') ?>" class="small-box-footer">
                                        Input Nilai <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?= number_format($total_antrian) ?></h3>
                                        <p>Total Antrian</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <a href="<?= base_url('backend/munaqosah/monitoring-antrian-peserta-ruangan-juri') ?>" target="_blank" class="small-box-footer">
                                        Lihat Antrian <i class="fas fa-arrow-circle-right"></i>
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
                                        $progressPercent = $total_peserta_terdaftar > 0
                                            ? round(($total_peserta_sudah_dinilai / $total_peserta_terdaftar) * 100)
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
                                            <strong><?= $total_peserta_sudah_dinilai ?></strong> dari <strong><?= $total_peserta_terdaftar ?></strong> peserta telah dinilai
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

                        <!-- Peserta Terakhir Dinilai -->
                        <?php if (!empty($peserta_terakhir)): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-history"></i> Peserta Terakhir Dinilai</h3>
                                        </div>
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-hover text-nowrap" id="tablePesertaTerakhir">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>No Peserta</th>
                                                        <th>Nama Santri</th>
                                                        <th>Waktu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1;
                                                    foreach ($peserta_terakhir as $peserta): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= esc($peserta['NoPeserta'] ?? '-') ?></td>
                                                            <td><?= esc($peserta['NamaSantri'] ?? '-') ?></td>
                                                            <td><?= esc($peserta['updated_at'] ?? '-') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
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

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#tablePesertaTerakhir').DataTable();
    });
</script>
<?= $this->endSection(); ?>