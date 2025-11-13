<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard Panitia Sertifikasi
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert" style="background-color: #17a2b8; color: #fff; border-color: #17a2b8;">
                            <h5 style="color: #fff;"><i class="icon fas fa-info-circle" style="color: #fff;"></i> Selamat Datang!</h5>
                            <p style="color: #fff;">Assalamu'alaikum, <strong><?= esc(user()->username ?? 'Panitia Sertifikasi') ?></strong>!</p>
                            <p style="color: #fff;">Anda login sebagai <strong>Panitia Sertifikasi</strong>. Dashboard ini menampilkan statistik dan monitoring keseluruhan proses sertifikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Utama -->
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
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= number_format($peserta_sudah_test) ?></h3>
                        <p>Sudah Test</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= number_format($peserta_belum_test) ?></h3>
                        <p>Belum Test</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= $total_peserta > 0 ? number_format(($peserta_sudah_test / $total_peserta) * 100, 1) : 0 ?>%</h3>
                        <p>Persentase Sudah Test</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Tambahan -->
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?= number_format($total_juri) ?></h3>
                        <p>Total Juri</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= number_format($total_nilai) ?></h3>
                        <p>Total Nilai Terinput</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-indigo">
                    <div class="inner">
                        <h3><?= number_format($total_materi) ?></h3>
                        <p>Total Materi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akses Cepat -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Akses Cepat
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-12 mb-3">
                                <a href="<?= base_url('backend/sertifikasi/listPesertaSertifikasi') ?>" class="small-box bg-info" style="display: block; text-decoration: none; color: white;">
                                    <div class="inner">
                                        <h3 style="font-size: 2.2rem; font-weight: bold; margin: 0;">
                                            <i class="fas fa-list"></i>
                                        </h3>
                                        <p style="font-size: 1.1rem; margin: 10px 0 0 0;">List Peserta</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <a href="<?= base_url('backend/sertifikasi/listNilaiSertifikasi') ?>" class="small-box bg-success" style="display: block; text-decoration: none; color: white;">
                                    <div class="inner">
                                        <h3 style="font-size: 2.2rem; font-weight: bold; margin: 0;">
                                            <i class="fas fa-chart-line"></i>
                                        </h3>
                                        <p style="font-size: 1.1rem; margin: 10px 0 0 0;">List Nilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <a href="<?= base_url('backend/sertifikasi/listJuriSertifikasi') ?>" class="small-box bg-warning" style="display: block; text-decoration: none; color: white;">
                                    <div class="inner">
                                        <h3 style="font-size: 2.2rem; font-weight: bold; margin: 0;">
                                            <i class="fas fa-user-check"></i>
                                        </h3>
                                        <p style="font-size: 1.1rem; margin: 10px 0 0 0;">Data Juri</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Per Materi -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Statistik Per Materi
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($statistik_per_materi)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Materi</th>
                                            <th>Nama Materi</th>
                                            <th>Jumlah Peserta Sudah Dinilai</th>
                                            <th>Persentase</th>
                                            <th>Progress Bar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($statistik_per_materi as $stat): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= esc($stat['IdMateri']) ?></strong></td>
                                                <td><?= esc($stat['NamaMateri']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">
                                                        <?= number_format($stat['jumlahPesertaSudahDinilai']) ?> / <?= number_format($total_peserta) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= number_format($stat['persentase'], 1) ?>%</strong>
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar <?= $stat['persentase'] >= 70 ? 'bg-success' : ($stat['persentase'] >= 40 ? 'bg-warning' : 'bg-danger') ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= $stat['persentase'] ?>%" 
                                                             aria-valuenow="<?= $stat['persentase'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?= number_format($stat['persentase'], 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada data materi.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Per Juri -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-check"></i>
                            Statistik Per Juri
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($statistik_per_juri)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username Juri</th>
                                            <th>Group Materi</th>
                                            <th>Jumlah Peserta Dinilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($statistik_per_juri as $juri): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= esc($juri['usernameJuri'] ?? '-') ?></strong></td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?= esc($juri['NamaGroupMateri'] ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">
                                                        <?= number_format($juri['jumlahPesertaDinilai']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada data juri.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Peserta Terakhir Dinilai -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            10 Peserta Terakhir Dinilai
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($peserta_terakhir)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Peserta</th>
                                            <th>Nama Guru</th>
                                            <th>Juri</th>
                                            <th>Waktu Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($peserta_terakhir as $peserta): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= esc($peserta['NoPeserta']) ?></strong></td>
                                                <td><?= esc($peserta['NamaGuru'] ?? '-') ?></td>
                                                <td>
                                                    <small><?= esc($peserta['usernameJuri'] ?? '-') ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($peserta['updated_at'])) {
                                                        $date = new \DateTime($peserta['updated_at']);
                                                        echo $date->format('d/m/Y H:i:s');
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                Belum ada peserta yang dinilai.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- /.content -->

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Auto refresh setiap 5 menit
        setInterval(function() {
            location.reload();
        }, 300000); // 5 menit = 300000 ms
    });
</script>
<?= $this->endSection(); ?>

