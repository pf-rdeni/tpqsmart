<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
function render_progress_bar($persentase, $height = 25)
{
    $color_class = '';
    if ($persentase <= 40) {
        $color_class = 'bg-danger';
    } elseif ($persentase <= 80) {
        $color_class = 'bg-warning';
    } else {
        $color_class = 'bg-success';
    }

    $html = '<div class="progress" style="height: ' . $height . 'px;">';
    $html .= '<div class="progress-bar ' . $color_class . '" ';
    $html .= 'style="width: ' . $persentase . '%; display: flex; align-items: center; justify-content: center; font-size: 15px;">';
    $html .= $persentase . '%';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}
?>

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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-0 bg-gradient-primary">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>
                                    Informasi Dashboard
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/absensi/index') ?> class="info-box-icon bg-info"><i class="fa-solid fa-clipboard-user"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Absensi</span>
                                                <span class="info-box-number"><?= $TotalSantri ?> Santri dari <?= $JumlahKelasDiajar ?> Kelas </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/prestasi/showPerKelas') ?> class="info-box-icon bg-primary"><i class="fa-solid fa-chart-simple"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Catatan Prestasi</span>
                                                <span class="info-box-number">Hafalan Harian</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/tabungan/showPerkelas') ?> class="info-box-icon bg-success"><i class="fa-solid fa-sack-dollar"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Saldo Tabungan</span>
                                                <span class="info-box-number">Rp. <?= number_format($TotalTabungan, 0, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/iuranBulanan/showPerKelas') ?> class="info-box-icon bg-warning"><i class="fa-solid fa-calendar-days"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Iuran Bulanan</span>
                                                <span class="info-box-number"><?= date('F Y'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card" id="card-semester-ganjil-guru">
                            <div class="card-header border-0 bg-gradient-warning">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Ganjil TA <?= $TahunAjaran ?>
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
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Ganjil</span>
                                        <span class="info-box-number"><?= $TotalSantri ?> Santri dari <?= $JumlahKelasDiajar ?> Kelas </span>
                                        <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah) ?>
                                        <span class="progress-description" style="font-size: smaller;">
                                            Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?>)
                                        </span>
                                        <div class="row">
                                            <?php foreach ($StatusInputNilaiPerKelasGanjil as $item) : ?>
                                                <div class="col-md-6 col-sm-6 col-12">
                                                    <span class="info-box-text"><?= $item['NamaKelas'] ?> <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small></span>
                                                    <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah) ?>
                                                    <span class="progress-description" style="font-size: smaller;">
                                                        Input nilai (<?= $item['StatusInputNilai']->countSudah ?>/<?= $item['StatusInputNilai']->countTotal ?>)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <div class="row">
                                                <?php
                                                $currentMonth = date('n'); // Mendapatkan bulan saat ini (1-12)
                                                if ($currentMonth >= 7 && $currentMonth <= 12):
                                                ?>
                                                    <div class="col-md-3 col-6">
                                                        <a href=<?php echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-app bg-primary">
                                                            <i class="fas fa-edit"></i> Input Nilai
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="col-md-3 col-6">
                                                        <button class="btn btn-app bg-secondary" disabled>
                                                            <i class="fas fa-edit"></i> Input Nilai
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-app bg-success">
                                                        <i class="fas fa-eye"></i> Detail Nilai
                                                    </a>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/nilai/showSumaryPersemester' . '/' . 'Ganjil') ?> class="btn btn-app bg-secondary">
                                                        <i class="fas fa-chart-bar"></i> Ranking
                                                    </a>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/rapor/index' . '/' . 'Ganjil') ?> class="btn btn-app bg-warning">
                                                        <i class="fas fa-file-alt"></i> Raport Nilai
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card" id="card-semester-genap-guru">
                            <div class="card-header border-0 bg-gradient-info">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Genap TA <?= $TahunAjaran ?>
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
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Genap</span>
                                        <span class="info-box-number"><?= $TotalSantri ?> Santri dari <?= $JumlahKelasDiajar ?> Kelas </span>
                                        <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah) ?>
                                        <span class="progress-description" style="font-size: smaller;">
                                            Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?>)
                                        </span>
                                        <div class="row">
                                            <?php foreach ($StatusInputNilaiPerKelasGenap as $item) : ?>
                                                <div class="col-md-6 col-sm-6 col-12">
                                                    <span class="info-box-text"><?= $item['NamaKelas'] ?> <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small></span>
                                                    <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah) ?>
                                                    <span class="progress-description" style="font-size: smaller;">
                                                        Input nilai (<?= $item['StatusInputNilai']->countSudah ?>/<?= $item['StatusInputNilai']->countTotal ?>)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <div class="row">
                                                <?php
                                                $currentMonth = date('n'); // Mendapatkan bulan saat ini (1-12)
                                                if ($currentMonth >= 1 && $currentMonth <= 6):
                                                ?>
                                                    <div class="col-md-3 col-6">
                                                        <a href=<?php echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-primary">
                                                            <i class="fas fa-edit"></i> Input Nilai
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="col-md-3 col-6">
                                                        <button class="btn btn-app bg-secondary" disabled>
                                                            <i class="fas fa-edit"></i> Input Nilai
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-success">
                                                        <i class="fas fa-eye"></i> Detail Nilai
                                                    </a>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/nilai/showSumaryPersemester' . '/' . 'Genap') ?> class="btn btn-app bg-secondary">
                                                        <i class="fas fa-chart-bar"></i> Ranking
                                                    </a>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <a href=<?php echo base_url('backend/rapor/index' . '/' . 'Genap') ?> class="btn btn-app bg-warning">
                                                        <i class="fas fa-file-alt"></i> Raport Nilai
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
                <!-- /.row -->
                <div class="row">
                    <div class="col-sm-12">
                        <!-- Calendar -->
                        <div class="card bg-gradient-scundery">
                            <div class="card-header border-0">

                                <h3 class="card-title">
                                    <i class="far fa-calendar-alt"></i>
                                    Calendar
                                </h3>
                                <!-- tools card -->
                                <div class="card-tools">
                                    <button type="button" class="btn btn-scundery btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <!-- /. tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body pt-0">
                                <!--The calendar -->
                                <div id="calendarnew"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- =========================================================== -->
            </div>
        <?php endif; ?>
        <?php if (in_groups('Admin') || in_groups('Operator')): ?>
            <div class="card-body">
                <!-- =========================================================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-0 bg-gradient-primary">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>
                                    Informasi
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/guru/show') ?> class="info-box-icon bg-primary"><i class="fa-solid fa-user-tie"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Data Guru</span>
                                                <span class="info-box-number"><?= $TotalGuru ?> Guru Active</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/santri/showAturSantriBaru') ?> class="info-box-icon bg-info"><i class="fa-solid fa-user-graduate"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Data Santri</span>
                                                <span class="info-box-number"><?= $TotalSantri ?> Santri <?= $TotalKelas ?> Kelas</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/santri/createEmisStep') ?> class="info-box-icon bg-success"><i class="fa-solid fa-user-plus"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Santri Baru</span>
                                                <span class="info-box-number">Tambah</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/santri/showSantriEmis') ?> class="info-box-icon bg-warning"><i class="fa-solid fa-file"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Data Untuk Emis</span>
                                                <span class="info-box-number"><?= date('F Y'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-0 bg-gradient-warning">
                                <h3 class="card-title">
                                    <i class="fas fa-wrench"></i>
                                    Setting
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
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/guruKelas/show') ?> class="info-box-icon bg-primary"><i class="fa-solid fa-user-tie"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Guru Kelas</span>
                                                <span class="info-box-number"><?= $TotalWaliKelas ?> Wali Kelas</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/kelas/showSantriKelasBaru') ?> class="info-box-icon bg-info"><i class="fa-solid fa-user-graduate"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Santri Baru Registrasi</span>
                                                <span class="info-box-number"><?= $TotalSantriBaru ?> Santri</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/kelas/showListSantriPerKelas') ?> class="info-box-icon bg-warning"><i class="fas fa-building"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Kenaikan Kelas</span>
                                                <span class="info-box-number">T.A Baru <?= date('Y'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <a href=<?php echo base_url('backend/user/index') ?> class="info-box-icon bg-danger"><i class="fa-solid fa-solid fa-gear"></i></a>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Akun</span>
                                                <span class="info-box-number">Guru dan Santri</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card" id="card-semester-ganjil-admin">
                            <div class="card-header border-0 bg-gradient-warning">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Ganjil TA <?= $TahunAjaran ?>
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
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Ganjil</span>
                                        <span class="info-box-number"><?= $TotalKelas ?> Kelas </span>
                                        <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah) ?>
                                        <span class="progress-description" style="font-size: smaller;">
                                            Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?>)
                                        </span>
                                        <div class="row">
                                            <?php foreach ($StatusInputNilaiPerKelasGanjil as $item) : ?>
                                                <div class="col-md-6 col-sm-6 col-12">
                                                    <span class="info-box-text"><?= $item['NamaKelas'] ?> <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small></span>
                                                    <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah) ?>
                                                    <span class="progress-description" style="font-size: smaller;">
                                                        Input nilai (<?= $item['StatusInputNilai']->countSudah ?>/<?= $item['StatusInputNilai']->countTotal ?>)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-app bg-success">
                                                <i class="fas fa-eye"></i> Detail Nilai
                                            </a>
                                            <a href=<?php echo base_url('backend/rapor/index' . '/' . 'Ganjil') ?> class="btn btn-app bg-primary">
                                                <i class="fas fa-file-alt"></i> Raport Nilai
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="card" id="card-semester-genap-admin">
                            <div class="card-header border-0 bg-gradient-info">
                                <h3 class="card-title">
                                    <i class="fas fa-award"></i>
                                    Semester Genap TA <?= $TahunAjaran ?>
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
                                    <div class="info-box-content">
                                        <span class="info-box-text">Info Semester Genap</span>
                                        <span class="info-box-number"><?= $TotalKelas ?> Kelas </span>
                                        <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah) ?>
                                        <span class="progress-description" style="font-size: smaller;">
                                            Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?>)
                                        </span>
                                        <div class="row">
                                            <?php foreach ($StatusInputNilaiPerKelasGenap as $item) : ?>
                                                <div class="col-md-6 col-sm-6 col-12">
                                                    <span class="info-box-text"><?= $item['NamaKelas'] ?> <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small></span>
                                                    <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah) ?>
                                                    <span class="progress-description" style="font-size: smaller;">
                                                        Input nilai (<?= $item['StatusInputNilai']->countSudah ?>/<?= $item['StatusInputNilai']->countTotal ?>)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="card-body" style="padding-left: 0; padding-right: 0;">
                                            <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-app bg-success">
                                                <i class="fas fa-eye"></i> Detail Nilai
                                            </a>
                                            <a href=<?php echo base_url('backend/rapor/index' . '/' . 'Genap') ?> class="btn btn-app bg-primary">
                                                <i class="fas fa-file-alt"></i> Raport Nilai
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
                    <div class="col-sm-12">
                        <!-- Calendar -->
                        <div class="card bg-gradient-scundery">
                            <div class="card-header border-0">

                                <h3 class="card-title">
                                    <i class="far fa-calendar-alt"></i>
                                    Calendar
                                </h3>
                                <!-- tools card -->
                                <div class="card-tools">
                                    <button type="button" class="btn btn-scundery btn-sm" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <!-- /. tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body pt-0">
                                <!--The calendar -->
                                <div id="calendarnew"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- =========================================================== -->
            </div>
        <?php endif; ?>
        <!-- /.card-header -->
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

<?= $this->section('scripts'); ?>
<!-- Include FullCalendar CSS and JS -->
<!-- Page specific script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendarnew');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            themeSystem: 'bootstrap',
            height: 'auto',
            events: function(info, successCallback, failureCallback) {
                // Mendapatkan tahun saat ini
                var year = new Date().getFullYear();

                // Array hari libur nasional Indonesia
                var holidays = [{
                        date: year + '-01-01',
                        title: 'Tahun Baru Masehi',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-01-22',
                        title: 'Tahun Baru Imlek',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-02-08',
                        title: 'Isra Mikraj Nabi Muhammad SAW',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-03-11',
                        title: 'Hari Suci Nyepi',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-03-31',
                        title: 'Wafat Isa Al Masih',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-04-10',
                        title: 'Hari Raya Idul Fitri',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-04-11',
                        title: 'Hari Raya Idul Fitri',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-05-01',
                        title: 'Hari Buruh Internasional',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-05-12',
                        title: 'Hari Raya Waisak',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-05-29',
                        title: 'Kenaikan Isa Al Masih',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-05-26',
                        title: 'Ujian Akhir Semester kelas 5',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-05-26',
                        title: 'Ujian Akhir Semester kelas 6',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-06-01',
                        title: 'Hari Lahir Pancasila',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-06-7',
                        title: 'Hari Raya Idul Adha',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-07-07',
                        title: 'Tahun Baru Islam',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-08-17',
                        title: 'Hari Kemerdekaan RI',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-09-28',
                        title: 'Maulid Nabi Muhammad SAW',
                        color: '#dc3545'
                    },
                    {
                        date: year + '-12-25',
                        title: 'Hari Raya Natal',
                        color: '#dc3545'
                    }
                ];

                // Filter hari libur berdasarkan rentang tanggal yang ditampilkan
                var filteredHolidays = holidays.filter(function(holiday) {
                    var holidayDate = new Date(holiday.date);
                    return holidayDate >= info.start && holidayDate <= info.end;
                });

                successCallback(filteredHolidays);
            },
            eventDidMount: function(info) {
                // Menambahkan tooltip untuk setiap event
                $(info.el).tooltip({
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();

        // AUTO COLLAPSE SEMESTER GANJIL JIKA BUKAN BULAN 7-12
        var currentMonth = new Date().getMonth() + 1; // getMonth() 0-11
        if (currentMonth < 7 || currentMonth > 12) {
            var cardGanjil = document.getElementById('card-semester-ganjil-guru')
            // Jika cardGanjil ditemukan, trigger collapse
            if (cardGanjil) {
                // Jika pakai AdminLTE, trigger collapse
                $(cardGanjil).find('[data-card-widget="collapse"]').trigger('click');
            }

        }

        if (currentMonth < 1 && currentMonth > 6) {
            var cardGenap = document.getElementById('card-semester-genap-guru')
            // Jika cardGenap ditemukan, trigger collapse
            if (cardGenap) {
                // Jika pakai AdminLTE, trigger collapse
                $(cardGenap).find('[data-card-widget="collapse"]').trigger('click');
            }
        }
    });
</script>
<?= $this->endSection(); ?>
<style>
    @media (max-width: 576px) {

        .progress,
        .progress-bar {
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .info-box-content {
            overflow-x: hidden;
        }
    }
</style>
<?= $this->endSection(); ?>