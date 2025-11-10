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
                    Dashboard Sertifikasi Guru
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert" style="background-color: #ffc107; color: #000; border-color: #ffc107;">
                            <h5 style="color: #000;"><i class="icon fas fa-info-circle" style="color: #000;"></i> Selamat Datang!</h5>
                            <p style="color: #000;">Assalamu'alaikum, <strong><?= esc(user()->username ?? 'Juri Sertifikasi') ?></strong>!</p>
                            <p style="color: #000;">Anda login sebagai <strong>Juri Sertifikasi</strong> untuk <strong><?= esc($group_materi['NamaMateri'] ?? 'Sertifikasi Guru') ?></strong>.</p>
                        </div>
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
                            <div class="col-lg-6 col-12 mb-3">
                                <a href="<?= base_url('backend/sertifikasi/inputNilaiSertifikasi') ?>" class="small-box bg-primary" style="display: block; text-decoration: none; color: white;">
                                    <div class="inner">
                                        <h3 style="font-size: 2.2rem; font-weight: bold; margin: 0;">
                                            <i class="fas fa-edit"></i>
                                        </h3>
                                        <p style="font-size: 1.1rem; margin: 10px 0 0 0;">Input Nilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <a href="<?= base_url('backend/sertifikasi/nilaiPesertaSertifikasi') ?>" class="small-box bg-success" style="display: block; text-decoration: none; color: white;">
                                    <div class="inner">
                                        <h3 style="font-size: 2.2rem; font-weight: bold; margin: 0;">
                                            <i class="fas fa-list"></i>
                                        </h3>
                                        <p style="font-size: 1.1rem; margin: 10px 0 0 0;">Nilai Peserta</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Juri dan Materi -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-check"></i>
                            Informasi Juri
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">ID Juri</th>
                                <td><?= esc($juri_data['IdJuri'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td><?= esc($juri_data['usernameJuri'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Group Materi</th>
                                <td>
                                    <span class="badge badge-primary">
                                        <?= esc($group_materi['NamaMateri'] ?? '-') ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book-open"></i>
                            Materi Penilaian
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($materi_list)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($materi_list as $materi): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-check-circle text-success"></i>
                                            <?= esc($materi['NamaMateri']) ?>
                                        </span>
                                        <span class="badge badge-info badge-pill">
                                            <?= esc($materi['IdMateri']) ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">Tidak ada materi penilaian</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peserta Terakhir Dinilai -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Peserta Terakhir Dinilai
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistik -->
                        <div class="row mb-4">
                            <div class="col-lg-6 col-12 mb-3">
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
                            <div class="col-lg-6 col-12 mb-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= number_format($total_peserta_dinilai_oleh_juri_ini) ?></h3>
                                        <p>Total Peserta yang Sudah Dinilai oleh Juri Ini</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($peserta_terakhir)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Peserta</th>
                                            <th>Nama Guru</th>
                                            <th>Waktu Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($peserta_terakhir as $peserta): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= esc($peserta['noTest']) ?></strong></td>
                                                <td><?= esc($peserta['NamaGuru'] ?? '-') ?></td>
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
                                                <td>
                                                    <a href="<?= base_url('backend/sertifikasi/inputNilaiSertifikasi') ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Input Lagi
                                                    </a>
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

