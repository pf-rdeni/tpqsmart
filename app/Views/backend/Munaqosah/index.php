<?= $this->extend('backend/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sistem Penilaian Munaqosah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Munaqosah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $statistik['total_peserta'] ?></h3>
                            <p>Total Peserta</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?= base_url('backend/munaqosah/peserta') ?>" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $statistik['sudah_dinilai'] ?></h3>
                            <p>Sudah Dinilai</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-checkmark"></i>
                        </div>
                        <a href="<?= base_url('backend/munaqosah/nilai') ?>" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $statistik['dalam_antrian'] ?></h3>
                            <p>Dalam Antrian</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clock"></i>
                        </div>
                        <a href="<?= base_url('backend/munaqosah/antrian') ?>" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $statistik['belum_dinilai'] ?></h3>
                            <p>Belum Dinilai</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-alert"></i>
                        </div>
                        <a href="<?= base_url('backend/munaqosah/nilai') ?>" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Menu Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Data Nilai</h5>
                            <p class="card-text">Kelola data penilaian munaqosah</p>
                            <a href="<?= base_url('backend/munaqosah/nilai') ?>" class="btn btn-primary">Kelola</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-list-ol fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Antrian Ujian</h5>
                            <p class="card-text">Kelola antrian peserta ujian</p>
                            <a href="<?= base_url('backend/munaqosah/antrian') ?>" class="btn btn-success">Kelola</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Peserta Munaqosah</h5>
                            <p class="card-text">Kelola data peserta ujian</p>
                            <a href="<?= base_url('backend/munaqosah/peserta') ?>" class="btn btn-info">Kelola</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Materi Ujian</h5>
                            <p class="card-text">Kelola materi ujian munaqosah</p>
                            <a href="<?= base_url('backend/munaqosah/materi') ?>" class="btn btn-warning">Kelola</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bobot Nilai -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Bobot Nilai</h5>
                            <p class="card-text">Kelola bobot nilai per kategori</p>
                            <a href="<?= base_url('backend/munaqosah/bobot') ?>" class="btn btn-danger">Kelola</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fa-3x text-secondary mb-3"></i>
                            <h5 class="card-title">Laporan</h5>
                            <p class="card-text">Lihat laporan penilaian</p>
                            <a href="<?= base_url('backend/munaqosah/laporan') ?>" class="btn btn-secondary">Lihat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
