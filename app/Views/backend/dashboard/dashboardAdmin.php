<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-danger">
                        <h3 class="card-title">
                            <i class="fas fa-crown"></i> Dashboard Ujian Semester - Admin
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Jadwal Sholat -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-mosque"></i> Jadwal Sholat Hari Ini
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" id="openLocationSettings" class="btn btn-tool" title="Pengaturan Lokasi">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="prayerScheduleLoading" class="text-center py-3">
                                            <i class="fas fa-spinner fa-spin"></i> Mengambil lokasi GPS...
                                        </div>
                                        <div id="prayerScheduleError" class="alert alert-warning" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i> <span id="prayerScheduleErrorMsg"></span>
                                        </div>
                                        <div id="prayerScheduleContent" style="display: none;">
                                            <?= prayer_schedule_css() ?>
                                            <div class="row mb-3">
                                                <div class="col-12 text-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span id="prayerLocation">Lokasi: -</span>
                                                    </small>
                                                    <div class="mt-1 text-secondary">
                                                        <i class="far fa-clock"></i>
                                                        <span id="currentDateTime">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row align-items-stretch">
                                                <div class="col-12 col-lg-9">
                                                    <div class="row">
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="fajr">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Subuh
                                                                </div>
                                                                <div class="h5 mb-0" id="time-fajr">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="shurooq">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Syuruq
                                                                </div>
                                                                <div class="h5 mb-0" id="time-shurooq">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="dhuhr">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Dzuhur
                                                                </div>
                                                                <div class="h5 mb-0" id="time-dhuhr">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="asr">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Ashar
                                                                </div>
                                                                <div class="h5 mb-0" id="time-asr">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="maghrib">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Maghrib
                                                                </div>
                                                                <div class="h5 mb-0" id="time-maghrib">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="isha">
                                                                <div class="prayer-title text-muted">
                                                                    <i class="fas fa-mosque prayer-icon"></i>Isya
                                                                </div>
                                                                <div class="h5 mb-0" id="time-isha">-</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-3 d-flex">
                                                    <div class="prayer-card text-center w-100 bg-info text-white">
                                                        <div class="prayer-title" id="nextPrayerName">-</div>
                                                        <div class="h5 mb-0" id="countdown">-</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <small class="text-muted d-block mb-1">Jadwal Besok <span id="nextDayDate">-</span></small>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 text-center align-middle">
                                                            <thead class="bg-light">
                                                                <tr class="text-muted" style="font-size: 0.5rem;">
                                                                    <th class="py-1">Subuh</th>
                                                                    <th class="py-1">Syuruq</th>
                                                                    <th class="py-1">Dzuhur</th>
                                                                    <th class="py-1">Ashar</th>
                                                                    <th class="py-1">Maghrib</th>
                                                                    <th class="py-1">Isya</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="small font-weight-bold">
                                                                    <td class="py-1" id="nextday-time-fajr">-</td>
                                                                    <td class="py-1" id="nextday-time-shurooq">-</td>
                                                                    <td class="py-1" id="nextday-time-dhuhr">-</td>
                                                                    <td class="py-1" id="nextday-time-asr">-</td>
                                                                    <td class="py-1" id="nextday-time-maghrib">-</td>
                                                                    <td class="py-1" id="nextday-time-isha">-</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?= prayer_schedule_modal() ?>

                        <!-- Welcome Message -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <h5><i class="icon fas fa-crown"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...!
                                        Selamat datang di dashboard ujian semester sebagai <strong>Administrator</strong>.
                                        Dashboard ini memberikan akses penuh untuk mengelola seluruh data akademik dan administrasi sistem.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Overview -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= $TotalGuru ?? 0 ?></h3>
                                        <p>Total Guru</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <a href="<?= base_url('backend/guru/show') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= $TotalSantri ?? 0 ?></h3>
                                        <p>Total Santri</p>
                                        <small><?= $TotalKelas ?? 0 ?> Kelas</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <a href="<?= base_url('backend/santri/showAturSantriBaru') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <?php if (isset($IsAdmin) && $IsAdmin): ?>
                                            <h3><?= $TotalTpq ?? 0 ?></h3>
                                            <p>Jumlah TPQ</p>
                                        <?php else: ?>
                                            <h3><?= $TotalWaliKelas ?? 0 ?></h3>
                                            <p>Wali Kelas</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="icon">
                                        <?php if (isset($IsAdmin) && $IsAdmin): ?>
                                            <i class="fas fa-building"></i>
                                        <?php else: ?>
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= isset($IsAdmin) && $IsAdmin ? base_url('backend/tpq/show') : base_url('backend/guruKelas/show') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3><?= $TotalSantriBaru ?? 0 ?></h3>
                                        <p>Santri Baru</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <a href="<?= base_url('backend/kelas/showSantriKelasBaru') ?>" class="small-box-footer">
                                        Tambah Data <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-danger card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions - Admin</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-block btn-success btn-lg">
                                                    <i class="fas fa-user-plus"></i><br>Tambah Santri
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/guru/show') ?>" class="btn btn-block btn-primary btn-lg">
                                                    <i class="fas fa-user-tie"></i><br>Data Guru
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/guruKelas/show') ?>" class="btn btn-block btn-info btn-lg">
                                                    <i class="fas fa-users-cog"></i><br>Guru Kelas
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/kelas/showSantriKelasBaru') ?>" class="btn btn-block btn-warning btn-lg">
                                                    <i class="fas fa-user-check"></i><br>Registrasi
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/kelas/showListSantriPerKelas') ?>" class="btn btn-block btn-purple btn-lg">
                                                    <i class="fas fa-graduation-cap"></i><br>Naik Kelas
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/user/index') ?>" class="btn btn-block btn-dark btn-lg">
                                                    <i class="fas fa-cog"></i><br>Akun
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Statistik Santri dan Guru -->
                        <div class="row mt-4">
                            <!-- Statistik Santri -->
                            <div class="col-md-6">
                                <div class="card card-success card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user-graduate"></i> Statistik Santri
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-success">
                                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Santri</span>
                                                        <span class="info-box-number"><?= $StatistikSantri['total'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-info">
                                                    <span class="info-box-icon"><i class="fas fa-male"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Laki-Laki</span>
                                                        <span class="info-box-number"><?= $StatistikSantri['laki_laki'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-pink">
                                                    <span class="info-box-icon"><i class="fas fa-female"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Perempuan</span>
                                                        <span class="info-box-number"><?= $StatistikSantri['perempuan'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Statistik Per Kelas -->
                                        <div class="mt-3">
                                            <h5><i class="fas fa-list"></i> Statistik Per Kelas</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Kelas</th>
                                                            <th class="text-center">Laki-Laki</th>
                                                            <th class="text-center">Perempuan</th>
                                                            <th class="text-center">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikSantri['per_kelas'])): ?>
                                                            <?php foreach ($StatistikSantri['per_kelas'] as $kelas): ?>
                                                                <tr>
                                                                    <td><?= esc($kelas['NamaKelas']) ?></td>
                                                                    <td class="text-center"><?= $kelas['LakiLaki'] ?? 0 ?></td>
                                                                    <td class="text-center"><?= $kelas['Perempuan'] ?? 0 ?></td>
                                                                    <td class="text-center"><strong><?= $kelas['Total'] ?? 0 ?></strong></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistik Guru -->
                            <div class="col-md-6">
                                <div class="card card-primary card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user-tie"></i> Statistik Guru
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-primary">
                                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Guru</span>
                                                        <span class="info-box-number"><?= $StatistikGuru['total'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-info">
                                                    <span class="info-box-icon"><i class="fas fa-male"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Laki-Laki</span>
                                                        <span class="info-box-number"><?= $StatistikGuru['laki_laki'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-gradient-pink">
                                                    <span class="info-box-icon"><i class="fas fa-female"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Perempuan</span>
                                                        <span class="info-box-number"><?= $StatistikGuru['perempuan'] ?? 0 ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Diagram Lingkaran (Pie Chart) -->
                                        <div class="mt-3">
                                            <h5><i class="fas fa-chart-pie"></i> Diagram Distribusi Guru</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <canvas id="guruPieChart" style="max-height: 300px;"></canvas>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge badge-info" style="width: 20px; height: 20px; display: inline-block;"></span>
                                                            <span class="ml-2">Laki-Laki: <strong><?= $StatistikGuru['laki_laki'] ?? 0 ?></strong> (<?= $StatistikGuru['total'] > 0 ? round(($StatistikGuru['laki_laki'] / $StatistikGuru['total']) * 100, 1) : 0 ?>%)</span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-danger" style="width: 20px; height: 20px; display: inline-block;"></span>
                                                            <span class="ml-2">Perempuan: <strong><?= $StatistikGuru['perempuan'] ?? 0 ?></strong> (<?= $StatistikGuru['total'] > 0 ? round(($StatistikGuru['perempuan'] / $StatistikGuru['total']) * 100, 1) : 0 ?>%)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Per TPQ (Hanya untuk Admin) -->
                        <?php if (!empty($StatistikSantriPerTpq) || !empty($StatistikGuruPerTpq)): ?>
                            <div class="row mt-4">
                                <!-- Statistik Santri Per TPQ -->
                                <div class="col-md-6">
                                    <div class="card card-success card-outline collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-user-graduate"></i> Statistik Santri Per TPQ
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-striped table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Nama TPQ</th>
                                                            <th class="text-center">Laki-Laki</th>
                                                            <th class="text-center">Perempuan</th>
                                                            <th class="text-center">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikSantriPerTpq)): ?>
                                                            <?php foreach ($StatistikSantriPerTpq as $tpq): ?>
                                                                <tr>
                                                                    <td><strong><?= esc($tpq['NamaTpq']) ?></strong></td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-info"><?= $tpq['LakiLaki'] ?? 0 ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-danger"><?= $tpq['Perempuan'] ?? 0 ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong><?= $tpq['Total'] ?? 0 ?></strong>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot class="thead-light">
                                                        <tr>
                                                            <th>Total</th>
                                                            <th class="text-center">
                                                                <span class="badge badge-info">
                                                                    <?= array_sum(array_column($StatistikSantriPerTpq, 'LakiLaki')) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <span class="badge badge-danger">
                                                                    <?= array_sum(array_column($StatistikSantriPerTpq, 'Perempuan')) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <strong><?= array_sum(array_column($StatistikSantriPerTpq, 'Total')) ?></strong>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistik Guru Per TPQ -->
                                <div class="col-md-6">
                                    <div class="card card-primary card-outline collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-user-tie"></i> Statistik Guru Per TPQ
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-striped table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Nama TPQ</th>
                                                            <th class="text-center">Laki-Laki</th>
                                                            <th class="text-center">Perempuan</th>
                                                            <th class="text-center">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikGuruPerTpq)): ?>
                                                            <?php foreach ($StatistikGuruPerTpq as $tpq): ?>
                                                                <tr>
                                                                    <td><strong><?= esc($tpq['NamaTpq']) ?></strong></td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-info"><?= $tpq['LakiLaki'] ?? 0 ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-danger"><?= $tpq['Perempuan'] ?? 0 ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong><?= $tpq['Total'] ?? 0 ?></strong>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot class="thead-light">
                                                        <tr>
                                                            <th>Total</th>
                                                            <th class="text-center">
                                                                <span class="badge badge-info">
                                                                    <?= array_sum(array_column($StatistikGuruPerTpq, 'LakiLaki')) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <span class="badge badge-danger">
                                                                    <?= array_sum(array_column($StatistikGuruPerTpq, 'Perempuan')) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <strong><?= array_sum(array_column($StatistikGuruPerTpq, 'Total')) ?></strong>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Statistik Per TPQ (Hanya untuk Admin) -->
                        <?php if (!empty($StatistikTpqDenganRasio)): ?>
                            <div class="row mt-4">
                                <!-- Tabel List TPQ dengan Rasio Guru:Santri -->
                                <div class="col-12">
                                    <div class="card card-info card-outline collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-list"></i> List TPQ dengan Rasio Guru:Santri
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="tblTpqDenganRasio" class="table table-sm table-bordered table-striped table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Nama TPQ</th>
                                                            <th>Alamat (Kelurahan/Desa)</th>
                                                            <th class="text-center">Jumlah Guru</th>
                                                            <th class="text-center">Jumlah Santri</th>
                                                            <th class="text-center">Rasio (Guru:Santri)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($StatistikTpqDenganRasio as $tpq): ?>
                                                            <tr>
                                                                <td><strong><?= esc($tpq['NamaTpq']) ?></strong></td>
                                                                <td><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary"><?= number_format($tpq['TotalGuru']) ?></span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-success"><?= number_format($tpq['TotalSantri']) ?></span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if ($tpq['Rasio'] != '-'): ?>
                                                                        <span class="badge badge-<?= esc($tpq['BadgeColor']) ?>"><?= esc($tpq['Rasio']) ?></span>
                                                                        <small class="text-muted d-block">(1 guru : <?= number_format($tpq['RasioNumeric'], 1) ?> santri)</small>
                                                                    <?php else: ?>
                                                                        <span class="badge badge-secondary">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot class="thead-light">
                                                        <tr>
                                                            <th>Total</th>
                                                            <th></th>
                                                            <th class="text-center">
                                                                <span class="badge badge-primary">
                                                                    <?= number_format(array_sum(array_column($StatistikTpqDenganRasio, 'TotalGuru'))) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <span class="badge badge-success">
                                                                    <?= number_format(array_sum(array_column($StatistikTpqDenganRasio, 'TotalSantri'))) ?>
                                                                </span>
                                                            </th>
                                                            <th class="text-center">
                                                                <?php
                                                                $totalGuru = array_sum(array_column($StatistikTpqDenganRasio, 'TotalGuru'));
                                                                $totalSantri = array_sum(array_column($StatistikTpqDenganRasio, 'TotalSantri'));
                                                                if ($totalSantri > 0 && $totalGuru > 0) {
                                                                    $rasioTotal = $totalSantri / $totalGuru;
                                                                    $rasioTotalCeil = (int)ceil($rasioTotal);
                                                                    // Tentukan warna badge untuk total
                                                                    $badgeColorTotal = 'secondary';
                                                                    if ($rasioTotalCeil < 9) {
                                                                        $badgeColorTotal = 'danger'; // Merah - rasio < 9
                                                                    } else if ($rasioTotalCeil >= 9 && $rasioTotalCeil <= 11) {
                                                                        $badgeColorTotal = 'success'; // Hijau - rasio >= 9 dan <= 11
                                                                    } else {
                                                                        $badgeColorTotal = 'info'; // Biru - rasio > 11
                                                                    }
                                                                    echo '<span class="badge badge-' . $badgeColorTotal . '">1:' . $rasioTotalCeil . '</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-secondary">-</span>';
                                                                }
                                                                ?>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Statistik Santri Per TPQ dan Per Kelas -->
                        <?php if (!empty($StatistikSantriPerTpqPerKelas) && !empty($StatistikSantriPerTpqPerKelas['data'])): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card card-primary card-outline collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-table"></i> Statistik Santri Per TPQ dan Per Kelas
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="tblStatistikSantriPerTpqPerKelas" class="table table-bordered table-striped table-hover table-sm" style="font-size: 0.9rem;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th rowspan="2" class="align-middle text-center" style="min-width: 200px;">Nama TPQ</th>
                                                            <th rowspan="2" class="align-middle text-center" style="min-width: 150px;">Alamat (Kelurahan/Desa)</th>
                                                            <?php foreach ($StatistikSantriPerTpqPerKelas['kelasList'] as $kelas): ?>
                                                                <th class="text-center vertical-text"><?= esc($kelas['NamaKelas']) ?></th>
                                                            <?php endforeach; ?>
                                                            <th rowspan="2" class="align-middle text-center bg-info" style="min-width: 80px;">Total</th>
                                                        </tr>
                                                        <tr>
                                                            <?php foreach ($StatistikSantriPerTpqPerKelas['kelasList'] as $kelas): ?>
                                                                <th></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($StatistikSantriPerTpqPerKelas['data'] as $tpq): ?>
                                                            <tr>
                                                                <td><strong><?= esc($tpq['NamaTpq']) ?></strong></td>
                                                                <td><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                <?php foreach ($StatistikSantriPerTpqPerKelas['kelasList'] as $kelas): ?>
                                                                    <?php $jumlah = $tpq['Kelas'][$kelas['IdKelas']]['Jumlah'] ?? 0; ?>
                                                                    <td class="text-center"><?= $jumlah > 0 ? number_format($jumlah) : '-' ?></td>
                                                                <?php endforeach; ?>
                                                                <td class="text-center bg-light"><strong><?= number_format($tpq['Total']) ?></strong></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot class="thead-light">
                                                        <tr>
                                                            <th class="bg-info text-white"><strong>Total</strong></th>
                                                            <th class="bg-info text-white"></th>
                                                            <?php foreach ($StatistikSantriPerTpqPerKelas['kelasList'] as $kelas): ?>
                                                                <th class="text-center bg-info text-white"><strong><?= number_format($StatistikSantriPerTpqPerKelas['totalPerKelas'][$kelas['IdKelas']] ?? 0) ?></strong></th>
                                                            <?php endforeach; ?>
                                                            <th class="text-center bg-info text-white"><strong><?= number_format($StatistikSantriPerTpqPerKelas['grandTotal'] ?? 0) ?></strong></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Statistik Progress Penilaian per TPQ -->
                        <?php if (!empty($StatistikProgressNilaiPerTpq) && ($IsAdmin ?? false)): ?>
                            <?php
                            // Tentukan semester saat ini
                            $currentMonth = date('n');
                            $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card card-warning card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-chart-line"></i> Statistik Progress Penilaian per TPQ
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-warning btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-3">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Informasi:</strong> Tabel ini menampilkan progress pengisian nilai per TPQ dan per kelas. Klik pada baris TPQ untuk melihat detail per kelas.
                                            </p>

                                            <?php
                                            // Tentukan semester saat ini menggunakan helper function
                                            $isSemesterGanjil = isSemesterGanjil();
                                            $isSemesterGenap = isSemesterGenap();
                                            ?>

                                            <!-- Semester Ganjil -->
                                            <div class="mb-4">
                                                <div class="card card-outline card-secondary <?= !$isSemesterGanjil ? 'collapsed-card' : '' ?>">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-book-reader"></i> Semester Ganjil TA <?= esc($TahunAjaran ?? '') ?>
                                                        </h5>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                                <i class="fas <?= !$isSemesterGanjil ? 'fa-plus' : 'fa-minus' ?>"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="tabelProgressNilaiGanjil" class="table table-bordered table-striped table-hover">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th class="text-center" style="width: 50px;">No</th>
                                                                        <th style="width: 50px;"></th>
                                                                        <th>Nama TPQ</th>
                                                                        <th>Kelurahan/Desa</th>
                                                                        <th class="text-center">Total Santri</th>
                                                                        <th class="text-center">Sudah Dinilai</th>
                                                                        <th class="text-center">Belum Dinilai</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (!empty($StatistikProgressNilaiPerTpq['Ganjil'])): ?>
                                                                        <?php $no = 1; ?>
                                                                        <?php foreach ($StatistikProgressNilaiPerTpq['Ganjil'] as $tpq): ?>
                                                                            <?php
                                                                            $tpqKey = md5($tpq['IdTpq']);
                                                                            $hasDetail = !empty($tpq['Kelas']) && count($tpq['Kelas']) > 0;
                                                                            ?>
                                                                            <!-- Row TPQ (Parent) -->
                                                                            <tr class="tpq-row" data-tpq-key="<?= $tpqKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                                <td class="text-center"><?= $no++ ?></td>
                                                                                <td class="text-center">
                                                                                    <?php if ($hasDetail): ?>
                                                                                        <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                                                                                    <?php else: ?>
                                                                                        <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <div>
                                                                                        <strong><?= esc($tpq['NamaTpq']) ?></strong>
                                                                                    </div>
                                                                                    <?php if (empty($tpq['StatusTpq'])): ?>
                                                                                        <div class="mt-2">
                                                                                            <div class="progress" style="height: 25px;">
                                                                                                <div class="progress-bar <?= $tpq['PersentaseSudah'] < 50 ? 'bg-danger' : ($tpq['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                    style="width: <?= $tpq['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center;">
                                                                                                    <?= $tpq['PersentaseSudah'] ?>%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="mt-1">
                                                                                            <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                                <td class="text-center">
                                                                                    <span class="badge badge-info"><?= number_format($tpq['TotalSantri']) ?></span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                        <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                    <?php elseif ($tpq['TotalSudahDinilai'] > 0): ?>
                                                                                        <span class="badge badge-success"><?= number_format($tpq['TotalSudahDinilai']) ?></span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-secondary">-</span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                        <span class="badge badge-secondary">-</span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-danger"><?= number_format($tpq['TotalBelumDinilai']) ?></span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                            <!-- Detail Kelas (Child rows - hidden by default) -->
                                                                            <?php if ($hasDetail): ?>
                                                                                <?php foreach ($tpq['Kelas'] as $kelas): ?>
                                                                                    <?php
                                                                                    $kelasKey = md5($tpq['IdTpq'] . '_' . $kelas['IdKelas']);
                                                                                    $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;
                                                                                    ?>
                                                                                    <tr class="kelas-row detail-<?= $tpqKey ?>" data-kelas-key="<?= $kelasKey ?>" style="display: none; background-color: #ffffff; cursor: pointer;">
                                                                                        <td></td>
                                                                                        <td class="text-center">
                                                                                            <?php if ($hasSantri): ?>
                                                                                                <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                                            <?php else: ?>
                                                                                                <i class="fas fa-angle-right text-muted"></i>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td colspan="2" style="padding-left: 40px;">
                                                                                            <div>
                                                                                                <strong><?= esc($kelas['NamaKelas']) ?></strong>
                                                                                            </div>
                                                                                            <?php if (empty($kelas['StatusKelas'])): ?>
                                                                                                <div class="mt-2">
                                                                                                    <div class="progress" style="height: 20px;">
                                                                                                        <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                            style="width: <?= $kelas['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                                                                            <?= $kelas['PersentaseSudah'] ?>%
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php else: ?>
                                                                                                <div class="mt-1">
                                                                                                    <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge badge-info"><?= number_format($kelas['TotalSantri']) ?></span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                            <?php elseif ($kelas['SudahDinilai'] > 0): ?>
                                                                                                <span class="badge badge-success"><?= number_format($kelas['SudahDinilai']) ?></span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary">-</span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                <span class="badge badge-secondary">-</span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-danger"><?= number_format($kelas['BelumDinilai']) ?></span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <!-- Detail Santri (Grandchild rows - hidden by default) -->
                                                                                    <?php if ($hasSantri): ?>
                                                                                        <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                                            <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;">
                                                                                                <td></td>
                                                                                                <td class="text-center">
                                                                                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Ganjil') ?>" style="text-decoration: none; cursor: pointer;" title="Input Nilai">
                                                                                                        <?php
                                                                                                        // Ambil foto profil santri
                                                                                                        $thumbnailPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/');
                                                                                                        $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                                                                                        $photoProfil = $santri['PhotoProfil'] ?? null;
                                                                                                        if (!empty($photoProfil)) {
                                                                                                            $thumbnailFile = 'thumb_' . $photoProfil;
                                                                                                            $thumbnailFullPath = FCPATH . 'uploads/santri/thumbnails/' . $thumbnailFile;
                                                                                                            if (file_exists($thumbnailFullPath)) {
                                                                                                                $photoUrl = $thumbnailPath . $thumbnailFile;
                                                                                                            } else {
                                                                                                                // Fallback ke foto asli jika thumbnail tidak ada
                                                                                                                $photoFullPath = FCPATH . 'uploads/santri/' . $photoProfil;
                                                                                                                if (file_exists($photoFullPath)) {
                                                                                                                    $photoUrl = $uploadPath . $photoProfil;
                                                                                                                } else {
                                                                                                                    $photoUrl = base_url('images/no-photo.jpg');
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            $photoUrl = base_url('images/no-photo.jpg');
                                                                                                        }
                                                                                                        ?>
                                                                                                        <img src="<?= $photoUrl ?>"
                                                                                                            alt="Foto <?= esc($santri['NamaSantri']) ?>"
                                                                                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; cursor: pointer;"
                                                                                                            onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                                                                                                    </a>
                                                                                                </td>
                                                                                                <td colspan="2" style="padding-left: 80px;">
                                                                                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Ganjil') ?>" style="color: inherit; text-decoration: none; cursor: pointer;" title="Input Nilai">
                                                                                                        <div>
                                                                                                            <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                                                        </div>
                                                                                                        <div>
                                                                                                            <span class="text-muted small">ID: <?= esc($santri['IdSantri']) ?></span>
                                                                                                        </div>
                                                                                                        <div class="mt-2">
                                                                                                            <div class="progress" style="height: 18px;">
                                                                                                                <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                                    style="width: <?= min(100, $santri['PersentaseSudah']) ?>%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                                                                                                                    <?= $santri['PersentaseSudah'] ?>%
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                                        <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                                    </span>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <span class="badge badge-info"><?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?></span>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                                        <span class="badge badge-warning"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                                    <?php else: ?>
                                                                                                        <span class="badge badge-success">Lengkap</span>
                                                                                                    <?php endif; ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php endforeach; ?>
                                                                                    <?php endif; ?>
                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">Tidak ada data untuk semester Ganjil</td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Semester Genap -->
                                            <div class="mb-4">
                                                <div class="card card-outline card-secondary <?= !$isSemesterGenap ? 'collapsed-card' : '' ?>">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-book-reader"></i> Semester Genap TA <?= esc($TahunAjaran ?? '') ?>
                                                        </h5>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                                <i class="fas <?= !$isSemesterGenap ? 'fa-plus' : 'fa-minus' ?>"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table id="tabelProgressNilaiGenap" class="table table-bordered table-striped table-hover">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th class="text-center" style="width: 50px;">No</th>
                                                                        <th style="width: 50px;"></th>
                                                                        <th>Nama TPQ</th>
                                                                        <th>Kelurahan/Desa</th>
                                                                        <th class="text-center">Total Santri</th>
                                                                        <th class="text-center">Sudah Dinilai</th>
                                                                        <th class="text-center">Belum Dinilai</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (!empty($StatistikProgressNilaiPerTpq['Genap'])): ?>
                                                                        <?php $no = 1; ?>
                                                                        <?php foreach ($StatistikProgressNilaiPerTpq['Genap'] as $tpq): ?>
                                                                            <?php
                                                                            $tpqKey = md5($tpq['IdTpq']);
                                                                            $hasDetail = !empty($tpq['Kelas']) && count($tpq['Kelas']) > 0;
                                                                            ?>
                                                                            <!-- Row TPQ (Parent) -->
                                                                            <tr class="tpq-row" data-tpq-key="<?= $tpqKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                                <td class="text-center"><?= $no++ ?></td>
                                                                                <td class="text-center">
                                                                                    <?php if ($hasDetail): ?>
                                                                                        <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                                                                                    <?php else: ?>
                                                                                        <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <div>
                                                                                        <strong><?= esc($tpq['NamaTpq']) ?></strong>
                                                                                    </div>
                                                                                    <?php if (empty($tpq['StatusTpq'])): ?>
                                                                                        <div class="mt-2">
                                                                                            <div class="progress" style="height: 25px;">
                                                                                                <div class="progress-bar <?= $tpq['PersentaseSudah'] < 50 ? 'bg-danger' : ($tpq['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                    style="width: <?= $tpq['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center;">
                                                                                                    <?= $tpq['PersentaseSudah'] ?>%
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="mt-1">
                                                                                            <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                                <td class="text-center">
                                                                                    <span class="badge badge-info"><?= number_format($tpq['TotalSantri']) ?></span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                        <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                    <?php elseif ($tpq['TotalSudahDinilai'] > 0): ?>
                                                                                        <span class="badge badge-success"><?= number_format($tpq['TotalSudahDinilai']) ?></span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-secondary">-</span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                        <span class="badge badge-secondary">-</span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge badge-danger"><?= number_format($tpq['TotalBelumDinilai']) ?></span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                            <!-- Detail Kelas (Child rows - hidden by default) -->
                                                                            <?php if ($hasDetail): ?>
                                                                                <?php foreach ($tpq['Kelas'] as $kelas): ?>
                                                                                    <?php
                                                                                    $kelasKey = md5($tpq['IdTpq'] . '_' . $kelas['IdKelas']);
                                                                                    $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;
                                                                                    ?>
                                                                                    <tr class="kelas-row detail-<?= $tpqKey ?>" data-kelas-key="<?= $kelasKey ?>" style="display: none; background-color: #ffffff; cursor: pointer;">
                                                                                        <td></td>
                                                                                        <td class="text-center">
                                                                                            <?php if ($hasSantri): ?>
                                                                                                <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                                            <?php else: ?>
                                                                                                <i class="fas fa-angle-right text-muted"></i>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td colspan="2" style="padding-left: 40px;">
                                                                                            <div>
                                                                                                <strong><?= esc($kelas['NamaKelas']) ?></strong>
                                                                                            </div>
                                                                                            <?php if (empty($kelas['StatusKelas'])): ?>
                                                                                                <div class="mt-2">
                                                                                                    <div class="progress" style="height: 20px;">
                                                                                                        <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                            style="width: <?= $kelas['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                                                                            <?= $kelas['PersentaseSudah'] ?>%
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php else: ?>
                                                                                                <div class="mt-1">
                                                                                                    <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge badge-info"><?= number_format($kelas['TotalSantri']) ?></span>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                            <?php elseif ($kelas['SudahDinilai'] > 0): ?>
                                                                                                <span class="badge badge-success"><?= number_format($kelas['SudahDinilai']) ?></span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-secondary">-</span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                <span class="badge badge-secondary">-</span>
                                                                                            <?php else: ?>
                                                                                                <span class="badge badge-danger"><?= number_format($kelas['BelumDinilai']) ?></span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <!-- Detail Santri (Grandchild rows - hidden by default) -->
                                                                                    <?php if ($hasSantri): ?>
                                                                                        <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                                            <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;">
                                                                                                <td></td>
                                                                                                <td class="text-center">
                                                                                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Genap') ?>" style="text-decoration: none; cursor: pointer;" title="Input Nilai">
                                                                                                        <?php
                                                                                                        // Ambil foto profil santri
                                                                                                        $thumbnailPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/');
                                                                                                        $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                                                                                        $photoProfil = $santri['PhotoProfil'] ?? null;
                                                                                                        if (!empty($photoProfil)) {
                                                                                                            $thumbnailFile = 'thumb_' . $photoProfil;
                                                                                                            $thumbnailFullPath = FCPATH . 'uploads/santri/thumbnails/' . $thumbnailFile;
                                                                                                            if (file_exists($thumbnailFullPath)) {
                                                                                                                $photoUrl = $thumbnailPath . $thumbnailFile;
                                                                                                            } else {
                                                                                                                // Fallback ke foto asli jika thumbnail tidak ada
                                                                                                                $photoFullPath = FCPATH . 'uploads/santri/' . $photoProfil;
                                                                                                                if (file_exists($photoFullPath)) {
                                                                                                                    $photoUrl = $uploadPath . $photoProfil;
                                                                                                                } else {
                                                                                                                    $photoUrl = base_url('images/no-photo.jpg');
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            $photoUrl = base_url('images/no-photo.jpg');
                                                                                                        }
                                                                                                        ?>
                                                                                                        <img src="<?= $photoUrl ?>"
                                                                                                            alt="Foto <?= esc($santri['NamaSantri']) ?>"
                                                                                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; cursor: pointer;"
                                                                                                            onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                                                                                                    </a>
                                                                                                </td>
                                                                                                <td colspan="2" style="padding-left: 80px;">
                                                                                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Genap') ?>" style="color: inherit; text-decoration: none; cursor: pointer;" title="Input Nilai">
                                                                                                        <div>
                                                                                                            <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                                                        </div>
                                                                                                        <div>
                                                                                                            <span class="text-muted small">ID: <?= esc($santri['IdSantri']) ?></span>
                                                                                                        </div>
                                                                                                        <div class="mt-2">
                                                                                                            <div class="progress" style="height: 18px;">
                                                                                                                <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                                    style="width: <?= min(100, $santri['PersentaseSudah']) ?>%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                                                                                                                    <?= $santri['PersentaseSudah'] ?>%
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                                        <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                                    </span>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <span class="badge badge-info"><?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?></span>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                                        <span class="badge badge-warning"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                                    <?php else: ?>
                                                                                                        <span class="badge badge-success">Lengkap</span>
                                                                                                    <?php endif; ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php endforeach; ?>
                                                                                    <?php endif; ?>
                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">Tidak ada data untuk semester Genap</td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
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

<?= $this->section('styles'); ?>
<style>
    /* Style untuk header kolom kelas vertikal */
    #tblStatistikSantriPerTpqPerKelas th.vertical-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        white-space: nowrap;
        height: 120px;
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        vertical-align: middle;
        text-align: center;
    }

    /* Pastikan sel data juga memiliki lebar yang sesuai dengan header */
    #tblStatistikSantriPerTpqPerKelas tbody td {
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        text-align: center;
    }

    /* Style untuk footer kolom kelas */
    #tblStatistikSantriPerTpqPerKelas tfoot th {
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        text-align: center;
    }

    /* Style untuk kolom Nama TPQ dan Alamat tetap normal */
    #tblStatistikSantriPerTpqPerKelas th:first-child,
    #tblStatistikSantriPerTpqPerKelas th:nth-child(2),
    #tblStatistikSantriPerTpqPerKelas td:first-child,
    #tblStatistikSantriPerTpqPerKelas td:nth-child(2),
    #tblStatistikSantriPerTpqPerKelas tfoot th:first-child,
    #tblStatistikSantriPerTpqPerKelas tfoot th:nth-child(2) {
        width: auto;
        min-width: auto;
        max-width: none;
        writing-mode: horizontal-tb;
        padding: 8px;
    }

    /* Style untuk kolom Total */
    #tblStatistikSantriPerTpqPerKelas th:last-child,
    #tblStatistikSantriPerTpqPerKelas td:last-child,
    #tblStatistikSantriPerTpqPerKelas tfoot th:last-child {
        width: 60px;
        min-width: 60px;
        max-width: 60px;
        writing-mode: horizontal-tb;
        padding: 8px 5px;
    }

    /* Pastikan semua kolom memiliki alignment yang sama */
    #tblStatistikSantriPerTpqPerKelas th,
    #tblStatistikSantriPerTpqPerKelas td,
    #tblStatistikSantriPerTpqPerKelas tfoot th {
        vertical-align: middle;
    }

    /* Pastikan tfoot memiliki width yang sama dengan body */
    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFoot {
        width: 100% !important;
    }

    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFootInner {
        width: 100% !important;
    }

    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFootInner table {
        width: 100% !important;
        margin: 0 !important;
    }

    /* Style untuk tabel progress penilaian */
    .tpq-row {
        transition: background-color 0.2s ease;
        user-select: none;
    }

    .tpq-row:hover {
        background-color: #e9ecef !important;
    }

    .tpq-row.expanded {
        background-color: #e9ecef;
    }

    .detail-row {
        transition: all 0.3s ease;
    }

    .detail-row td {
        border-top: 1px solid #dee2e6;
    }

    .expand-icon {
        color: #007bff;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .tpq-row:hover .expand-icon {
        color: #0056b3;
    }

    /* Styling untuk badge di detail row */
    .detail-row .badge {
        font-weight: 500;
    }

    /* Style untuk kelas-row */
    .kelas-row {
        transition: background-color 0.2s ease;
        user-select: none;
    }

    .kelas-row:hover {
        background-color: #f0f0f0 !important;
    }

    .kelas-row.expanded {
        background-color: #f0f0f0;
    }

    .expand-icon-kelas {
        color: #007bff;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .kelas-row:hover .expand-icon-kelas {
        color: #0056b3;
    }

    /* Style untuk santri-row */
    .santri-row {
        transition: all 0.3s ease;
    }

    .santri-row td {
        border-top: 1px solid #dee2e6;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable for List TPQ dengan Rasio
        if ($('#tblTpqDenganRasio').length > 0) {
            initializeDataTableUmum('#tblTpqDenganRasio', true, true, [], {
                "pageLength": 25,
                "order": [
                    [3, "asc"]
                ], // Sort by rasio
                "columnDefs": [{
                    "targets": [2, 3, 4], // Jumlah Guru, Jumlah Santri, Rasio
                    "className": "text-center"
                }]
            });
        }

        // Initialize DataTable for Statistik Santri Per TPQ Per Kelas
        if ($('#tblStatistikSantriPerTpqPerKelas').length > 0) {
            var tableSantriPerKelas = initializeDataTableScrollX('#tblStatistikSantriPerTpqPerKelas', [], {
                "pageLength": 25,
                "lengthChange": true,
                "order": [],
                "columnDefs": [{
                        "targets": [0, 1], // Nama TPQ dan Alamat
                        "orderable": true
                    },
                    {
                        "targets": "_all",
                        "className": "text-center"
                    }
                ]
            });

            // Fix alignment setelah init - pastikan columns di-adjust
            $('#tblStatistikSantriPerTpqPerKelas').on('init.dt', function() {
                // Multiple attempts untuk memastikan alignment benar
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                        tableSantriPerKelas.columns.adjust();
                    }
                }, 100);

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                        tableSantriPerKelas.columns.adjust();
                    }
                }, 300);
            });

            // Fix alignment setelah draw
            $('#tblStatistikSantriPerTpqPerKelas').on('draw.dt', function() {
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                        tableSantriPerKelas.columns.adjust();
                    }
                }, 50);
            });

            // Fix alignment ketika card di-expand (karena card ini collapsed-card)
            var statistikSantriCard = $('#tblStatistikSantriPerTpqPerKelas').closest('.card');
            statistikSantriCard.on('expanded.lte.cardwidget', function() {
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                        tableSantriPerKelas.columns.adjust();
                    }
                }, 350);

                // Double check setelah card fully expanded
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                        tableSantriPerKelas.columns.adjust();
                    }
                }, 600);
            });
        }

        let guruChart = null;

        function initGuruPieChart() {
            const ctxGuru = document.getElementById('guruPieChart');
            if (ctxGuru) {
                // Destroy existing chart if it exists
                if (guruChart) {
                    guruChart.destroy();
                    guruChart = null;
                }

                const guruLaki = <?= $StatistikGuru['laki_laki'] ?? 0 ?>;
                const guruPerempuan = <?= $StatistikGuru['perempuan'] ?? 0 ?>;
                const guruTotal = <?= $StatistikGuru['total'] ?? 0 ?>;

                guruChart = new Chart(ctxGuru, {
                    type: 'pie',
                    data: {
                        labels: ['Laki-Laki', 'Perempuan'],
                        datasets: [{
                            label: 'Jumlah Guru',
                            data: [guruLaki, guruPerempuan],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)', // Info blue
                                'rgba(220, 53, 69, 0.8)' // Danger red
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Initialize chart when card is expanded (using AdminLTE card widget events)
        const statistikGuruCard = $('#guruPieChart').closest('.card');

        // Listen for expanded event
        statistikGuruCard.on('expanded.lte.cardwidget', function() {
            setTimeout(function() {
                initGuruPieChart();
                // Resize chart after initialization
                if (guruChart) {
                    setTimeout(function() {
                        guruChart.resize();
                    }, 100);
                }
            }, 350);
        });

        // Initialize immediately if card is not collapsed
        if (statistikGuruCard.length && !statistikGuruCard.hasClass('collapsed-card')) {
            setTimeout(function() {
                initGuruPieChart();
            }, 100);
        }

        // Key untuk localStorage
        const storageKeyCard = 'dashboardAdmin_cardExpand';
        const storageKeyTpq = 'dashboardAdmin_tpqExpand';
        const storageKeyKelas = 'dashboardAdmin_kelasExpand';

        // Fungsi untuk menyimpan status expand card ke localStorage
        function saveCardExpandState(cardId, isExpanded) {
            var states = JSON.parse(localStorage.getItem(storageKeyCard) || '{}');
            states[cardId] = isExpanded;
            localStorage.setItem(storageKeyCard, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand card dari localStorage
        function loadCardExpandState(cardId) {
            var states = JSON.parse(localStorage.getItem(storageKeyCard) || '{}');
            return states[cardId] !== undefined ? states[cardId] : null;
        }

        // Fungsi untuk menyimpan status expand TPQ ke localStorage
        function saveTpqExpandState(tpqKey, semester, isExpanded) {
            var key = semester + '_' + tpqKey;
            var states = JSON.parse(localStorage.getItem(storageKeyTpq) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeyTpq, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand TPQ dari localStorage
        function loadTpqExpandState(tpqKey, semester) {
            var key = semester + '_' + tpqKey;
            var states = JSON.parse(localStorage.getItem(storageKeyTpq) || '{}');
            return states[key] === true;
        }

        // Fungsi untuk menyimpan status expand kelas ke localStorage
        function saveKelasExpandState(kelasKey, semester, isExpanded) {
            var key = semester + '_' + kelasKey;
            var states = JSON.parse(localStorage.getItem(storageKeyKelas) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeyKelas, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand kelas dari localStorage
        function loadKelasExpandState(kelasKey, semester) {
            var key = semester + '_' + kelasKey;
            var states = JSON.parse(localStorage.getItem(storageKeyKelas) || '{}');
            return states[key] === true;
        }

        // Fungsi helper untuk mendapatkan card identifier
        function getCardIdentifier($card) {
            var cardId = $card.attr('id');
            if (cardId) {
                return cardId;
            }
            var headerText = $card.find('.card-header h3, .card-header h5').text().trim();
            if (headerText) {
                return headerText.replace(/[^a-zA-Z0-9]/g, '_');
            }
            return 'card_' + $card.index();
        }

        // Fungsi helper untuk menentukan semester berdasarkan tabel
        function getSemesterFromTable($table) {
            var tableId = $table.attr('id') || '';
            if (tableId.includes('Ganjil')) {
                return 'Ganjil';
            } else if (tableId.includes('Genap')) {
                return 'Genap';
            }
            var cardHeaderText = $table.closest('.card').find('.card-header').text();
            if (cardHeaderText.includes('Ganjil')) {
                return 'Ganjil';
            } else if (cardHeaderText.includes('Genap')) {
                return 'Genap';
            }
            return '';
        }

        // Handle expand/collapse untuk card semester (Ganjil/Genap)
        $(document).on('expanded.lte.cardwidget', function(event) {
            var $card = $(event.target).closest('.card');
            var cardId = getCardIdentifier($card);
            saveCardExpandState(cardId, true);
        });

        $(document).on('collapsed.lte.cardwidget', function(event) {
            var $card = $(event.target).closest('.card');
            var cardId = getCardIdentifier($card);
            saveCardExpandState(cardId, false);
        });

        // Load status expand card saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.card').each(function() {
                    var $card = $(this);
                    if ($card.find('[data-card-widget="collapse"]').length > 0) {
                        var cardId = getCardIdentifier($card);
                        var savedState = loadCardExpandState(cardId);
                        if (savedState !== null) {
                            if (savedState && $card.hasClass('collapsed-card')) {
                                $card.removeClass('collapsed-card');
                                $card.find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
                                $card.find('.card-body, .card-footer').slideDown();
                            } else if (!savedState && !$card.hasClass('collapsed-card')) {
                                $card.addClass('collapsed-card');
                                $card.find('[data-card-widget="collapse"] i').removeClass('fa-minus').addClass('fa-plus');
                                $card.find('.card-body, .card-footer').slideUp();
                            }
                        }
                    }
                });
            }, 100);
        });

        // Handle expand/collapse untuk tabel progress penilaian
        $(document).on('click', '.tpq-row', function() {
            var tpqKey = $(this).data('tpq-key');
            var $table = $(this).closest('table');
            var detailRows = $table.find('.detail-' + tpqKey);
            var expandIcon = $(this).find('.expand-icon');
            var semester = getSemesterFromTable($table);

            if (detailRows.length > 0) {
                if (detailRows.is(':visible')) {
                    // Collapse - tutup semua level kelas dan santri yang terkait
                    detailRows.each(function() {
                        var $kelasRow = $(this);
                        var kelasKey = $kelasRow.data('kelas-key');

                        // Jika ini adalah kelas-row yang expanded, collapse juga santri-rows-nya
                        if ($kelasRow.hasClass('kelas-row') && kelasKey) {
                            var santriRows = $table.find('.detail-' + kelasKey);
                            if (santriRows.length > 0 && santriRows.is(':visible')) {
                                santriRows.slideUp(200);
                                var kelasExpandIcon = $kelasRow.find('.expand-icon-kelas');
                                kelasExpandIcon.css('transform', 'rotate(0deg)');
                                $kelasRow.removeClass('expanded');
                                if (semester) {
                                    saveKelasExpandState(kelasKey, semester, false);
                                }
                            }
                        }
                    });

                    // Collapse semua detail rows (kelas) - pastikan semua santri rows juga tertutup
                    detailRows.slideUp(300, function() {
                        // Setelah kelas rows tertutup, pastikan semua santri rows juga tertutup
                        var allSantriRows = $table.find('.santri-row');
                        allSantriRows.hide();
                    });
                    expandIcon.css('transform', 'rotate(0deg)');
                    $(this).removeClass('expanded');
                    if (semester) {
                        saveTpqExpandState(tpqKey, semester, false);
                    }
                } else {
                    // Expand
                    detailRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                    if (semester) {
                        saveTpqExpandState(tpqKey, semester, true);
                    }
                }
            }
        });

        // Load status expand TPQ saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.tpq-row').each(function() {
                    var tpqKey = $(this).data('tpq-key');
                    var $table = $(this).closest('table');
                    var detailRows = $table.find('.detail-' + tpqKey);
                    var expandIcon = $(this).find('.expand-icon');
                    var semester = getSemesterFromTable($table);

                    if (semester && detailRows.length > 0) {
                        var isExpanded = loadTpqExpandState(tpqKey, semester);
                        if (isExpanded) {
                            detailRows.show();
                            expandIcon.css('transform', 'rotate(90deg)');
                            $(this).addClass('expanded');
                        }
                    }
                });
            }, 200);
        });

        // Hover effect untuk tpq-row
        $(document).on('mouseenter', '.tpq-row', function() {
            if ($('.detail-' + $(this).data('tpq-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f0f0f0');
            }
        }).on('mouseleave', '.tpq-row', function() {
            if ($('.detail-' + $(this).data('tpq-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f8f9fa');
            }
        });

        // Handle expand/collapse untuk kelas-row ke santri
        $(document).on('click', '.kelas-row', function(e) {
            e.stopPropagation(); // Prevent triggering tpq-row click
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table'); // Ambil tabel parent (Ganjil atau Genap)
            var santriRows = $table.find('.detail-' + kelasKey); // Cari hanya dalam tabel yang sama
            var expandIcon = $(this).find('.expand-icon-kelas');
            var semester = getSemesterFromTable($table);

            if (santriRows.length > 0) {
                if (santriRows.is(':visible')) {
                    // Collapse
                    santriRows.slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $(this).removeClass('expanded');
                    if (semester) {
                        saveKelasExpandState(kelasKey, semester, false);
                    }
                } else {
                    // Expand
                    santriRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                    if (semester) {
                        saveKelasExpandState(kelasKey, semester, true);
                    }
                }
            }
        });

        // Load status expand kelas saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.kelas-row').each(function() {
                    var kelasKey = $(this).data('kelas-key');
                    var $table = $(this).closest('table');
                    var santriRows = $table.find('.detail-' + kelasKey);
                    var expandIcon = $(this).find('.expand-icon-kelas');
                    var semester = getSemesterFromTable($table);

                    if (semester && santriRows.length > 0) {
                        var isExpanded = loadKelasExpandState(kelasKey, semester);
                        if (isExpanded) {
                            santriRows.show();
                            expandIcon.css('transform', 'rotate(90deg)');
                            $(this).addClass('expanded');
                        }
                    }
                });
            }, 300);
        });

        // Hover effect untuk kelas-row
        $(document).on('mouseenter', '.kelas-row', function() {
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table');
            if ($table.find('.detail-' + kelasKey).is(':visible')) {
                $(this).css('background-color', '#f0f0f0');
            } else {
                $(this).css('background-color', '#f8f9fa');
            }
        }).on('mouseleave', '.kelas-row', function() {
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table');
            if ($table.find('.detail-' + kelasKey).is(':visible')) {
                $(this).css('background-color', '#f0f0f0');
            } else {
                $(this).css('background-color', '#ffffff');
            }
        });

        // Jadwal Sholat
        let prayerTimes = {};
        let currentPrayerIndex = -1;
        let nextPrayerIndex = -1;
        let countdownInterval = null;
        let nextPrayerTimeStr = null;
        let nextIsTomorrow = false;
        let clockInterval = null;
        let nextDayPrayerTimes = {};
        let lastLocationSetting = null;
        let manualInputTimeout = null;
        let citySuggestionsTimeout = null;
        let currentSuggestionIndex = -1;
        let currentLocation = null; // Track current location untuk detect perubahan
        let isInitialLoad = true; // Flag untuk initial load
        const DEFAULT_CITY = 'Bintan';
        const LOCATION_SETTING_KEY = 'prayerLocationSetting';
        const LOCATION_CONTROLS_VISIBLE_KEY = 'prayerLocationControlsVisible';

        // List kota populer untuk autocomplete
        const popularCities = [
            'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Makassar', 'Palembang',
            'Depok', 'Tangerang', 'Bekasi', 'Yogyakarta', 'Malang', 'Surakarta', 'Bogor',
            'Batam', 'Pekanbaru', 'Padang', 'Denpasar', 'Banjarmasin', 'Pontianak', 'Bintan',
            'Tanjung Pinang', 'Jakarta Selatan', 'Jakarta Utara', 'Jakarta Timur', 'Jakarta Barat',
            'Jakarta Pusat', 'Bandung Barat', 'Surabaya Utara', 'Surabaya Selatan', 'Medan Selayang',
            'Semarang Tengah', 'Makassar Utara', 'Palembang Ilir', 'Depok Timur', 'Tangerang Selatan',
            'Bekasi Timur', 'Yogyakarta Utara', 'Malang Utara', 'Bogor Selatan', 'Batam Center',
            'Pekanbaru Barat', 'Padang Barat', 'Denpasar Selatan', 'Banjarmasin Utara', 'Pontianak Utara'
        ];

        // Prayer order
        const prayerOrder = ['fajr', 'shurooq', 'dhuhr', 'asr', 'maghrib', 'isha'];
        const prayerNames = {
            'fajr': 'Subuh',
            'shurooq': 'Syuruq',
            'dhuhr': 'Dzuhur',
            'asr': 'Ashar',
            'maghrib': 'Maghrib',
            'isha': 'Isya'
        };

        // Fetch prayer times by city name
        function fetchPrayerTimesByCity(cityName, isDefault = false) {
            const loadingEl = document.getElementById('prayerScheduleLoading');
            const errorEl = document.getElementById('prayerScheduleError');
            const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
            const contentEl = document.getElementById('prayerScheduleContent');

            // Update location display
            if (isDefault) {
                document.getElementById('prayerLocation').textContent =
                    `Lokasi: ${cityName} (Default)`;
            } else {
                document.getElementById('prayerLocation').textContent =
                    `Lokasi: ${cityName}`;
            }

            // Fetch prayer times by city
            fetch(`<?= base_url('backend/jadwal-sholat') ?>/${encodeURIComponent(cityName)}?format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.prayer_times) {
                        prayerTimes = data.prayer_times;
                        displayPrayerTimes();
                        updateCurrentAndNextPrayer();
                        fetchNextDayPrayerTimes({
                            mode: 'city',
                            city: cityName,
                            isDefault
                        });
                        startCountdown();
                        loadingEl.style.display = 'none';
                        contentEl.style.display = 'block';

                        // Show success notification hanya jika lokasi berubah (bukan initial load)
                        const locationText = isDefault ? `${cityName} (Default)` : cityName;
                        const newLocation = `${isDefault ? 'default' : 'city'}:${cityName}`;

                        if (!isInitialLoad && currentLocation !== newLocation) {
                            showSuccessNotification(`Jadwal sholat untuk ${locationText} berhasil diperbarui`);
                        }

                        // Update current location
                        currentLocation = newLocation;
                        isInitialLoad = false;

                        // Keep info message visible if it's default location
                        if (isDefault && errorEl.style.display === 'block') {
                            // Info message already shown, keep it visible
                        }
                    } else {
                        // If city not found and not already default, fallback to default
                        if (!isDefault) {
                            const originalCity = cityName;
                            showWarningNotification(`Kota "${originalCity}" tidak ditemukan. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                            // Fallback to default city
                            fetchPrayerTimesByCity(DEFAULT_CITY, true);
                        } else {
                            showError(data.error || 'Gagal mengambil jadwal sholat');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // If not already default, fallback to default
                    if (!isDefault) {
                        showWarningNotification(`Terjadi kesalahan saat mengambil jadwal sholat. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                        fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    } else {
                        showError('Terjadi kesalahan saat mengambil jadwal sholat');
                    }
                });
        }

        function loadLocationSetting() {
            try {
                const saved = localStorage.getItem(LOCATION_SETTING_KEY);
                if (!saved) return {
                    mode: 'gps',
                    city: DEFAULT_CITY
                };
                const parsed = JSON.parse(saved);
                if (!parsed.mode) return {
                    mode: 'gps',
                    city: DEFAULT_CITY
                };
                return {
                    mode: parsed.mode || 'gps',
                    city: parsed.city || DEFAULT_CITY
                };
            } catch (e) {
                return {
                    mode: 'gps',
                    city: DEFAULT_CITY
                };
            }
        }

        function saveLocationSetting(setting) {
            lastLocationSetting = setting;
            localStorage.setItem(LOCATION_SETTING_KEY, JSON.stringify(setting));
        }

        // Function untuk refresh prayer times dari modal (dipanggil oleh helper)
        window.refreshPrayerTimesByModeFromModal = function(mode, manualCity) {
            setLoadingState();

            if (mode === 'gps') {
                saveLocationSetting({
                    mode: 'gps',
                    city: DEFAULT_CITY
                });
                getPrayerTimes();
                return;
            }

            if (mode === 'default') {
                saveLocationSetting({
                    mode: 'default',
                    city: DEFAULT_CITY
                });
                fetchPrayerTimesByCity(DEFAULT_CITY, true);
                return;
            }

            // manual
            const targetCity = manualCity || DEFAULT_CITY;
            saveLocationSetting({
                mode: 'manual',
                city: targetCity
            });
            fetchPrayerTimesByCity(targetCity, false);
        }

        function toggleManualInputVisibility(mode) {
            const manualInputContainer = document.getElementById('manualCityContainer');
            const manualInput = document.getElementById('manualCity');
            const suggestionsEl = document.getElementById('citySuggestions');

            if (mode === 'manual') {
                manualInputContainer.style.display = 'block';
                manualInput.focus();
            } else {
                manualInputContainer.style.display = 'none';
                if (suggestionsEl) {
                    suggestionsEl.style.display = 'none';
                }
            }
        }

        // Filter dan tampilkan suggestion kota
        function showCitySuggestions(query) {
            const suggestionsEl = document.getElementById('citySuggestions');
            if (!suggestionsEl) return;

            // Clear previous timeout
            if (citySuggestionsTimeout) {
                clearTimeout(citySuggestionsTimeout);
            }

            // Jika query kosong atau kurang dari 2 karakter, sembunyikan suggestion
            if (!query || query.trim().length < 2) {
                suggestionsEl.style.display = 'none';
                currentSuggestionIndex = -1;
                return;
            }

            // Filter kota berdasarkan query (case insensitive)
            const queryLower = query.toLowerCase().trim();
            const filtered = popularCities.filter(city =>
                city.toLowerCase().includes(queryLower)
            ).slice(0, 10); // Maksimal 10 suggestion

            // Jika tidak ada hasil, sembunyikan
            if (filtered.length === 0) {
                suggestionsEl.style.display = 'none';
                currentSuggestionIndex = -1;
                return;
            }

            // Tampilkan suggestion
            suggestionsEl.innerHTML = '';
            filtered.forEach((city, index) => {
                const item = document.createElement('div');
                item.className = 'city-suggestion-item';
                item.textContent = city;
                item.dataset.city = city;

                // Highlight matching text
                const regex = new RegExp(`(${query})`, 'gi');
                item.innerHTML = city.replace(regex, '<strong>$1</strong>');

                // Click handler
                item.addEventListener('click', function() {
                    selectCity(city);
                });

                suggestionsEl.appendChild(item);
            });

            suggestionsEl.style.display = 'block';
            currentSuggestionIndex = -1;
        }

        // Pilih kota dari suggestion
        function selectCity(city) {
            const manualInput = document.getElementById('manualCity');
            const suggestionsEl = document.getElementById('citySuggestions');

            if (manualInput) {
                manualInput.value = city;
            }

            if (suggestionsEl) {
                suggestionsEl.style.display = 'none';
            }

            currentSuggestionIndex = -1;

            // Trigger refresh setelah memilih kota
            const modeEl = document.getElementById('locationMode');
            if (modeEl && modeEl.value === 'manual') {
                // Clear intervals
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                if (clockInterval) {
                    clearInterval(clockInterval);
                }
                refreshPrayerTimesByMode();
            }
        }


        // Function to show success notification
        function showSuccessNotification(message) {
            // Check if toastr is available (AdminLTE)
            if (typeof toastr !== 'undefined') {
                toastr.success(message, '', {
                    timeOut: 3000,
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    preventDuplicates: true,
                    newestOnTop: true
                });
            } else {
                // Fallback: create simple notification
                const notification = document.createElement('div');
                notification.className = 'alert alert-success alert-dismissible fade show';
                notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px; max-width: 350px; font-size: 0.85rem; padding: 10px 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);';
                notification.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle" style="font-size: 1rem;"></i>
                        <span style="flex: 1;">${message}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="opacity: 0.7; font-size: 1.2rem;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                document.body.appendChild(notification);

                // Auto close after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        }

        // Function to show warning/info notification
        function showWarningNotification(message) {
            // Check if toastr is available (AdminLTE)
            if (typeof toastr !== 'undefined') {
                toastr.warning(message, 'Peringatan', {
                    timeOut: 4000,
                    closeButton: true,
                    progressBar: true
                });
            } else {
                // Fallback: create simple notification
                const notification = document.createElement('div');
                notification.className = 'alert alert-warning alert-dismissible fade show';
                notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notification.innerHTML = `
                    <strong>Peringatan!</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                document.body.appendChild(notification);

                // Auto close after 4 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 4000);
            }
        }

        function setLoadingState() {
            const loadingEl = document.getElementById('prayerScheduleLoading');
            const errorEl = document.getElementById('prayerScheduleError');
            const contentEl = document.getElementById('prayerScheduleContent');
            loadingEl.style.display = 'block';
            errorEl.style.display = 'none';
            contentEl.style.display = 'none';
        }

        function refreshPrayerTimesByMode() {
            // Gunakan settingan dari localStorage
            const saved = loadLocationSetting();
            const mode = saved.mode || 'gps';
            const manualCity = saved.city || DEFAULT_CITY;

            setLoadingState();

            if (mode === 'gps') {
                getPrayerTimes();
                return;
            }

            if (mode === 'default') {
                fetchPrayerTimesByCity(DEFAULT_CITY, true);
                return;
            }

            // manual
            const targetCity = manualCity || DEFAULT_CITY;
            fetchPrayerTimesByCity(targetCity, false);
        }

        function initLocationControls() {
            const saved = loadLocationSetting();
            lastLocationSetting = saved;

            // Jalankan awal sesuai setting terakhir
            refreshPrayerTimesByMode();
        }

        // Fetch prayer times by coordinates
        function fetchPrayerTimesByCoordinate(lat, lng) {
            const loadingEl = document.getElementById('prayerScheduleLoading');
            const errorEl = document.getElementById('prayerScheduleError');
            const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
            const contentEl = document.getElementById('prayerScheduleContent');

            // Update location display
            document.getElementById('prayerLocation').textContent =
                `Lokasi: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;

            // Fetch prayer times
            fetch(`<?= base_url('backend/jadwal-sholat') ?>/${lat}/${lng}?format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.prayer_times) {
                        prayerTimes = data.prayer_times;
                        displayPrayerTimes();
                        updateCurrentAndNextPrayer();
                        fetchNextDayPrayerTimes({
                            mode: 'coordinate',
                            lat,
                            lng,
                            isDefault: false
                        });
                        startCountdown();
                        loadingEl.style.display = 'none';
                        contentEl.style.display = 'block';

                        // Show success notification hanya jika lokasi berubah (bukan initial load)
                        const newLocation = `gps:${lat.toFixed(4)},${lng.toFixed(4)}`;

                        if (!isInitialLoad && currentLocation !== newLocation) {
                            showSuccessNotification('Jadwal sholat berdasarkan lokasi GPS berhasil diperbarui');
                        }

                        // Update current location
                        currentLocation = newLocation;
                        isInitialLoad = false;
                    } else {
                        // If coordinate fetch fails, fallback to default city
                        console.warn('Gagal mengambil jadwal berdasarkan koordinat, menggunakan default ' + DEFAULT_CITY);
                        showWarningNotification(`Gagal mengambil jadwal berdasarkan koordinat. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                        fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback to Bintan on error
                    showWarningNotification(`Terjadi kesalahan saat mengambil jadwal. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                    fetchPrayerTimesByCity(DEFAULT_CITY, true);
                });
        }

        // Get GPS location and fetch prayer times
        function getPrayerTimes() {
            const loadingEl = document.getElementById('prayerScheduleLoading');
            const errorEl = document.getElementById('prayerScheduleError');
            const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
            const contentEl = document.getElementById('prayerScheduleContent');

            loadingEl.style.display = 'block';
            errorEl.style.display = 'none';
            contentEl.style.display = 'none';

            if (!navigator.geolocation) {
                // Geolocation not supported, use default city
                console.warn('Geolocation tidak didukung, menggunakan default: ' + DEFAULT_CITY);
                const errorEl = document.getElementById('prayerScheduleError');
                const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
                errorEl.className = 'alert alert-info';
                errorEl.style.display = 'block';
                errorMsgEl.innerHTML = '<i class="fas fa-info-circle"></i> Geolocation tidak didukung oleh browser. Menggunakan lokasi default: ' + DEFAULT_CITY;
                showWarningNotification(`Geolocation tidak didukung oleh browser. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                fetchPrayerTimesByCity(DEFAULT_CITY, true);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    fetchPrayerTimesByCoordinate(lat, lng);
                },
                function(error) {
                    // GPS error, use default city (Bintan)
                    console.warn('Tidak dapat mengakses lokasi GPS, menggunakan default: ' + DEFAULT_CITY);
                    let errorMessage = '';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Akses lokasi ditolak. Menggunakan lokasi default: ' + DEFAULT_CITY;
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Informasi lokasi tidak tersedia. Menggunakan lokasi default: ' + DEFAULT_CITY;
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Waktu permintaan lokasi habis. Menggunakan lokasi default: ' + DEFAULT_CITY;
                            break;
                        default:
                            errorMessage = 'Error tidak diketahui. Menggunakan lokasi default: ' + DEFAULT_CITY;
                            break;
                    }
                    // Show info message but still load prayer times with default location
                    const errorEl = document.getElementById('prayerScheduleError');
                    const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
                    errorEl.className = 'alert alert-info';
                    errorEl.style.display = 'block';
                    errorMsgEl.innerHTML = '<i class="fas fa-info-circle"></i> ' + errorMessage;

                    // Show warning notification
                    showWarningNotification(errorMessage);

                    // Fetch with default city
                    fetchPrayerTimesByCity(DEFAULT_CITY, true);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function showError(message) {
            const loadingEl = document.getElementById('prayerScheduleLoading');
            const errorEl = document.getElementById('prayerScheduleError');
            const errorMsgEl = document.getElementById('prayerScheduleErrorMsg');
            const contentEl = document.getElementById('prayerScheduleContent');

            loadingEl.style.display = 'none';
            errorEl.style.display = 'block';
            errorMsgEl.textContent = message;
            contentEl.style.display = 'none';
        }

        function displayPrayerTimes() {
            prayerOrder.forEach(prayer => {
                const timeEl = document.getElementById(`time-${prayer}`);
                if (timeEl && prayerTimes[prayer]) {
                    timeEl.textContent = formatTime24(prayerTimes[prayer]);
                }
            });
        }

        function displayNextDayPrayerTimes() {
            prayerOrder.forEach(prayer => {
                const el = document.getElementById(`nextday-time-${prayer}`);
                if (el && nextDayPrayerTimes[prayer]) {
                    el.textContent = formatTime24(nextDayPrayerTimes[prayer]);
                } else if (el) {
                    el.textContent = '-';
                }
            });
        }

        function formatTime24(timeString) {
            const parsed = parseTime(timeString);
            if (!parsed) return timeString || '-';
            const hh = parsed.hours.toString().padStart(2, '0');
            const mm = parsed.minutes.toString().padStart(2, '0');
            return `${hh}:${mm}`;
        }

        function getTomorrowDates() {
            const t = new Date();
            t.setDate(t.getDate() + 1);
            const y = t.getFullYear();
            const m = String(t.getMonth() + 1).padStart(2, '0');
            const d = String(t.getDate()).padStart(2, '0');
            const iso = `${y}-${m}-${d}`; // for API
            const display = t.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            return {
                iso,
                display
            };
        }

        function fetchNextDayPrayerTimes(params) {
            const {
                mode,
                city,
                lat,
                lng,
                isDefault
            } = params;
            const {
                iso,
                display
            } = getTomorrowDates();
            const nextDayDateEl = document.getElementById('nextDayDate');
            if (nextDayDateEl) {
                nextDayDateEl.textContent = display;
            }
            let url = '';
            if (mode === 'city') {
                url = `<?= base_url('backend/jadwal-sholat') ?>/${encodeURIComponent(city)}?format=json&date=${iso}`;
            } else {
                url = `<?= base_url('backend/jadwal-sholat') ?>/${lat}/${lng}?format=json&date=${iso}`;
            }

            fetch(url)
                .then(resp => resp.json())
                .then(data => {
                    if (data.success && data.prayer_times) {
                        nextDayPrayerTimes = data.prayer_times;
                        displayNextDayPrayerTimes();
                    } else if (isDefault) {
                        // fallback already default, keep previous display
                        displayNextDayPrayerTimes();
                    } else {
                        // if city/coord fail, try default city for next day
                        fetchNextDayPrayerTimes({
                            mode: 'city',
                            city: DEFAULT_CITY,
                            isDefault: true
                        });
                    }
                })
                .catch(() => {
                    if (!isDefault) {
                        fetchNextDayPrayerTimes({
                            mode: 'city',
                            city: DEFAULT_CITY,
                            isDefault: true
                        });
                    }
                });
        }

        function parseTime(timeString) {
            if (!timeString) return null;

            // Handle 12-hour format with AM/PM
            const trimmed = timeString.trim().toLowerCase();
            const hasAm = trimmed.includes('am');
            const hasPm = trimmed.includes('pm');

            // Remove AM/PM from string for parsing
            const timeOnly = trimmed.replace(/\s*(am|pm)\s*/gi, '');
            const parts = timeOnly.split(':');
            if (parts.length !== 2) return null;

            let hours = parseInt(parts[0]);
            const minutes = parseInt(parts[1]);
            if (isNaN(hours) || isNaN(minutes)) return null;

            // Convert to 24-hour format
            if (hasPm && hours !== 12) {
                hours += 12; // PM: add 12 hours (except 12 PM which stays 12)
            } else if (hasAm && hours === 12) {
                hours = 0; // 12 AM becomes 00:00
            }
            // If no AM/PM specified, assume 24-hour format

            const result = {
                hours,
                minutes
            };
            return result;
        }

        function getCurrentTime() {
            const now = new Date();
            return {
                hours: now.getHours(),
                minutes: now.getMinutes(),
                seconds: now.getSeconds(),
                totalMinutes: now.getHours() * 60 + now.getMinutes()
            };
        }

        function timeToMinutes(timeObj) {
            if (!timeObj) return null;
            return timeObj.hours * 60 + timeObj.minutes;
        }

        function updateCurrentAndNextPrayer() {
            const now = getCurrentTime();
            const currentTotalMinutes = now.totalMinutes;

            // Reset all highlights
            document.querySelectorAll('.prayer-time').forEach(el => {
                el.classList.remove('bg-success', 'bg-warning', 'text-white', 'font-weight-bold');
            });

            currentPrayerIndex = -1;
            nextPrayerIndex = -1;

            // Find the most recent prayer that has passed (current prayer window)
            // and the next upcoming prayer
            let lastPassedIndex = -1;

            for (let i = 0; i < prayerOrder.length; i++) {
                const prayer = prayerOrder[i];
                const timeStr = prayerTimes[prayer];
                if (!timeStr) continue;

                const prayerTime = parseTime(timeStr);
                if (!prayerTime) continue;

                const prayerTotalMinutes = timeToMinutes(prayerTime);
                const prayerEl = document.querySelector(`[data-prayer="${prayer}"]`);
                const hasPassed = prayerTotalMinutes <= currentTotalMinutes;

                if (hasPassed) {
                    // This prayer has passed
                    lastPassedIndex = i;
                } else {
                    // This is a future prayer
                    if (nextPrayerIndex === -1) {
                        nextPrayerIndex = i;
                        if (prayerEl) {
                            prayerEl.classList.add('bg-warning', 'font-weight-bold');
                        }
                    }
                }
            }

            // Set current prayer as the last passed prayer (aktif sampai masuk waktu berikutnya)
            if (lastPassedIndex >= 0) {
                const lastPrayer = prayerOrder[lastPassedIndex];
                currentPrayerIndex = lastPassedIndex;
                const prayerEl = document.querySelector(`[data-prayer="${lastPrayer}"]`);
                if (prayerEl) {
                    prayerEl.classList.add('bg-success', 'text-white', 'font-weight-bold');
                }
            }

            // If no next prayer found (all prayers passed), next is tomorrow's first prayer
            if (nextPrayerIndex === -1) {
                nextPrayerIndex = 0; // Next is tomorrow's fajr
                const prayer = prayerOrder[0];
                const prayerEl = document.querySelector(`[data-prayer="${prayer}"]`);
                if (prayerEl) {
                    prayerEl.classList.add('bg-warning', 'font-weight-bold');
                }
            }

            // Update next prayer info
            updateNextPrayerInfo();
        }

        function updateNextPrayerInfo() {
            if (nextPrayerIndex < 0) {
                nextPrayerIndex = 0;
            }

            const nextPrayer = prayerOrder[nextPrayerIndex];
            const nextPrayerName = prayerNames[nextPrayer] || nextPrayer;
            const nextPrayerTime = prayerTimes[nextPrayer];

            document.getElementById('nextPrayerName').textContent = 'Waktu Berikutnya: ' + nextPrayerName || '-';

            // Check if next prayer is tomorrow's fajr
            const now = getCurrentTime();
            const lastPrayer = prayerOrder[prayerOrder.length - 1]; // Isya
            const lastPrayerTime = parseTime(prayerTimes[lastPrayer]);
            nextIsTomorrow = nextPrayerIndex === 0 && lastPrayerTime &&
                now.totalMinutes > timeToMinutes(lastPrayerTime);
            nextPrayerTimeStr = nextPrayerTime;

            updateCountdown(nextPrayerTime, nextIsTomorrow);
        }

        function updateCountdown(nextPrayerTimeStr, isTomorrow = false) {
            const countdownEl = document.getElementById('countdown');
            if (!nextPrayerTimeStr || !countdownEl) {
                if (countdownEl) countdownEl.textContent = '-';
                return;
            }

            const nextPrayerTime = parseTime(nextPrayerTimeStr);
            if (!nextPrayerTime) {
                countdownEl.textContent = '-';
                return;
            }

            const now = new Date();
            const target = new Date();
            target.setHours(nextPrayerTime.hours, nextPrayerTime.minutes, 0, 0);
            if (isTomorrow || target.getTime() <= now.getTime()) {
                target.setDate(target.getDate() + 1); // roll to next day
            }

            let diffSeconds = Math.floor((target.getTime() - now.getTime()) / 1000);
            if (diffSeconds < 0) diffSeconds = 0;

            const hours = Math.floor(diffSeconds / 3600);
            const minutes = Math.floor((diffSeconds % 3600) / 60);
            const seconds = diffSeconds % 60;

            const hh = String(hours).padStart(2, '0');
            const mm = String(minutes).padStart(2, '0');
            const ss = String(seconds).padStart(2, '0');
            countdownEl.textContent = `${hh}:${mm}:${ss}`;
        }

        function startCountdown() {
            // Clear existing interval
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            if (clockInterval) {
                clearInterval(clockInterval);
            }

            // Update immediately
            updateCurrentAndNextPrayer();
            updateCurrentDateTime();

            // Update countdown every second
            countdownInterval = setInterval(function() {
                updateCurrentAndNextPrayer();
            }, 1000); // Every second

            // Update current time display every second
            clockInterval = setInterval(function() {
                updateCurrentDateTime();
            }, 1000);
        }

        function updateCurrentDateTime() {
            const now = new Date();
            const optionsDate = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const optionsTime = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const dateStr = now.toLocaleDateString('id-ID', optionsDate);
            const timeStr = now.toLocaleTimeString('id-ID', optionsTime);
            const el = document.getElementById('currentDateTime');
            if (el) {
                el.textContent = `${dateStr} | ${timeStr}`;
            }
        }

        // Initialize on page load dengan setting lokasi tersimpan
        initLocationControls();
    });
</script>
<?= prayer_schedule_settings_js(base_url('backend/jadwal-sholat')) ?>
<?= $this->endSection(); ?>