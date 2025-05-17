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
                                <span class="info-box-text">Tabunagn Santri</span>
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
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="fas fa-award"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Semester Ganjil</span>
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
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-award"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Semester Genap</span>
                                <span class="info-box-number">35 Santri</span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 30%"></div>
                                </div>
                                <span class="progress-description" ">
                                30% nilai sudah diinput
                            </span>
                            <div class=" card-body" style="padding-left: 0; padding-right: 0;">
                                    <a href=<?php echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-primary">
                                        <i class="fas fa-edit"></i> Input Nilai
                                    </a>
                                    <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-success">
                                        <i class="fas fa-eye"></i> Detail Nilai
                                    </a>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.row -->
                <!-- =========================================================== -->
            </div>

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