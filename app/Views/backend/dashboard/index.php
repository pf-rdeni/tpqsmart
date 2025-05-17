<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<!-- Main content -->
<section class="content">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="m-0">Bismillahirrahmanirrahim</h4>
                </div>
                <div class="col-sm-12">
                    <p>Assalamu'alaikum! Selamat datang di aplikasi TPQ ini. Semoga Allah senantiasa memberkahi langkah kita dalam menuntut ilmu dan mendidik generasi penerus.</p>
                </div>
            </div>
        </div>
        <?php if (in_groups('Guru')): ?>
            <div class="card-body">
                <!-- =========================================================== -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <a href=<?php echo base_url('backend/absensi/index') ?> class="info-box-icon bg-info"><i class="fa-solid fa-clipboard-user"></i></a>
                            <div class="info-box-content">
                                <span class="info-box-text">Absensi</span>
                                <span class="info-box-number">35 Santri</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <a href=<?php echo base_url('backend/tabungan/showPerkelas') ?> class="info-box-icon bg-success"><i class="fa-solid fa-sack-dollar"></i></a>

                            <div class="info-box-content">
                                <span class="info-box-text">Tabungan Santri</span>
                                <span class="info-box-number">Rp. 1.165.000</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <a href=<?php echo base_url('backend/iuranBulanan/showPerKelas') ?> class="info-box-icon bg-warning"><i class="far fa-copy"></i></a>

                            <div class="info-box-content">
                                <span class="info-box-text">Iuran Bulanan</span>
                                <span class="info-box-number">Juni 2025</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fa-solid fa-users"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Santri</span>
                                <span class="info-box-number">1 Kelas</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header border-0 bg-gradient-warning">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Ganjil
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-warning btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-box bg-gradient-warning">
                                    <span class="info-box-icon"><i class="fas fa-award"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Ganjil</span>
                                        <span class="info-box-number">35 Santri</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            100% nilai sudah diinput
                                        </span>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <a href=<?php echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-app bg-primary">
                                                <i class="fas fa-edit"></i> Input Nilai
                                            </a>
                                            <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-app bg-success">
                                                <i class="fas fa-eye"></i> Detail Nilai
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header border-0 bg-gradient-info">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Genap
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-box bg-gradient-info">
                                    <span class="info-box-icon"><i class="fas fa-award"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Genap</span>
                                        <span class="info-box-number">35 Santri</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 30%"></div>
                                        </div>
                                        <span class="progress-description">
                                            30% nilai sudah diinput
                                        </span>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <a href=<?php echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-primary">
                                                <i class="fas fa-edit"></i> Input Nilai
                                            </a>
                                            <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-success">
                                                <i class="fas fa-eye"></i> Detail Nilai
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <!-- Calendar -->
                        <div class="card bg-gradient-success">
                            <div class="card-header border-0">

                                <h3 class="card-title">
                                    <i class="far fa-calendar-alt"></i>
                                    Calendar
                                </h3>
                                <!-- tools card -->
                                <div class="card-tools">
                                    <!-- button with a dropdown -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                                            <i class="fas fa-bars"></i>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a href="#" class="dropdown-item">Add new event</a>
                                            <a href="#" class="dropdown-item">Clear events</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item">View calendar</a>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <!-- /. tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body pt-0">
                                <!--The calendar -->
                                <div id="calendar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- =========================================================== -->
            </div>
        <?php endif; ?>
        <!-- /.card-body -->
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-6">
                    <h5>Selamat datang di aplikasi TPQ</h5>
                    <p>Teruslah berjuang dan berusaha, karena setiap langkah yang kita ambil akan membawa kita lebih dekat dengan tujuan.</p>
                </div>
                <div class="col-sm-6">
                    <h5>Informasi</h5>
                    <p>Untuk informasi lebih lanjut, silakan hubungi admin lembaga TPQ.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<?= $this->endSection(); ?>