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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-success">
                        <h3 class="card-title">
                            <i class="fas fa-user-cog"></i> Dashboard Ujian Semester - Operator
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible">
                                    <h5><i class="icon fas fa-info-circle"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...!
                                        Selamat datang di dashboard aplikasi TPQ Smart untuk Operator TPQ.</p>
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
                                        <h3><?= $TotalWaliKelas ?? 0 ?></h3>
                                        <p>Wali Kelas</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <a href="<?= base_url('backend/guruKelas/show') ?>" class="small-box-footer">
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
                                        Registrasi Santri Baru <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-block btn-success btn-lg">
                                                    <i class="fas fa-user-plus"></i><br>Tambah Santri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/guruKelas/show') ?>" class="btn btn-block btn-primary btn-lg">
                                                    <i class="fas fa-users-cog"></i><br>Guru Kelas
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/kelas/showSantriKelasBaru') ?>" class="btn btn-block btn-info btn-lg">
                                                    <i class="fas fa-user-check"></i><br>Registrasi Santri Baru
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/showSantriEmis') ?>" class="btn btn-block btn-warning btn-lg">
                                                    <i class="fas fa-file-alt"></i><br>Copy Data Santri untukEMIS
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/guru/show') ?>" class="btn btn-block btn-secondary btn-lg">
                                                    <i class="fas fa-user-tie"></i><br>Daftar Guru
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/showProfilSantri') ?>" class="btn btn-block btn-info btn-lg">
                                                    <i class="fas fa-user-circle"></i><br>Profil Santri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/showAturSantriBaru') ?>" class="btn btn-block btn-warning btn-lg">
                                                    <i class="fas fa-user-edit"></i><br>Ubah Santri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/showSantriBaruPerkelasTpq') ?>" class="btn btn-block btn-success btn-lg">
                                                    <i class="fas fa-users"></i><br>Santri Per Kelas
                                                </a>
                                            </div>
                                            <?php
                                            // Tentukan semester saat ini untuk quick action
                                            $currentMonth = date('n');
                                            $currentSemester = ($currentMonth >= 7 && $currentMonth <= 12) ? 'Ganjil' : 'Genap';
                                            ?>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/nilai/showSantriPerKelas/' . $currentSemester) ?>" class="btn btn-block btn-primary btn-lg">
                                                    <i class="fas fa-edit"></i><br>Input Nilai
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/rapor/index/' . $currentSemester) ?>" class="btn btn-block btn-danger btn-lg">
                                                    <i class="fas fa-book"></i><br>Rapor Nilai
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/tpq/profilLembaga') ?>" class="btn btn-block btn-secondary btn-lg">
                                                    <i class="fas fa-building"></i><br>Profil Lembaga
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/tools/index') ?>" class="btn btn-block btn-dark btn-lg">
                                                    <i class="fas fa-cog"></i><br>Pengaturan Umum
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

                        <!-- Semester Progress -->
                        <?php
                        // Tentukan semester saat ini menggunakan helper function
                        $isSemesterGanjil = isSemesterGanjil();
                        $isSemesterGenap = isSemesterGenap();
                        ?>
                        <div class="row mt-4">
                            <!-- Semester Ganjil -->
                            <div class="col-12 mb-3">
                                <div class="card card-secondary card-outline <?= !$isSemesterGanjil ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-book-reader"></i> Semester Ganjil TA <?= esc($TahunAjaran ?? '') ?>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-secondary btn-sm" data-card-widget="collapse">
                                                <i class="fas <?= !$isSemesterGanjil ? 'fa-plus' : 'fa-minus' ?>"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-secondary">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Kelas</span>
                                                <span class="info-box-number"><?= $TotalKelas ?? 0 ?> Kelas</span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah ?? 0) ?>
                                                <span class="progress-description">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?? 0 ?>)
                                                </span>

                                                <!-- Progress per Kelas -->
                                                <div class="row mt-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGanjil ?? [] as $item): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <span class="info-box-text"><?= esc($item['NamaKelas']) ?>
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small>Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-3">
                                                    <?php if ($isSemesterGanjil): ?>
                                                        <div class="col-12 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-12 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm" disabled>
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-12 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-success btn-sm">
                                                            <i class="fas fa-eye"></i> Detail Nilai
                                                        </a>
                                                    </div>
                                                    <div class="col-12">
                                                        <a href="<?= base_url('backend/rapor/index/Ganjil') ?>" class="btn btn-block btn-primary btn-sm">
                                                            <i class="fas fa-file-alt"></i> Raport Nilai
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="col-12">
                                <div class="card card-info card-outline <?= !$isSemesterGenap ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-book-reader"></i> Semester Genap TA <?= esc($TahunAjaran ?? '') ?>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                                <i class="fas <?= !$isSemesterGenap ? 'fa-plus' : 'fa-minus' ?>"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-info">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Kelas</span>
                                                <span class="info-box-number"><?= $TotalKelas ?? 0 ?> Kelas</span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah ?? 0) ?>
                                                <span class="progress-description">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?? 0 ?>)
                                                </span>

                                                <!-- Progress per Kelas -->
                                                <div class="row mt-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGenap ?? [] as $item): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <span class="info-box-text"><?= esc($item['NamaKelas']) ?>
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small>Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-3">
                                                    <?php if ($isSemesterGenap): ?>
                                                        <div class="col-12 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Genap') ?>" class="btn btn-block btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-12 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm" disabled>
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-12 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap') ?>" class="btn btn-block btn-success btn-sm">
                                                            <i class="fas fa-eye"></i> Detail Nilai
                                                        </a>
                                                    </div>
                                                    <div class="col-12">
                                                        <a href="<?= base_url('backend/rapor/index/Genap') ?>" class="btn btn-block btn-primary btn-sm">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Progress Penilaian per Kelas -->
    <?php if (!empty($StatistikProgressNilaiPerTpq)): ?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line"></i> Statistik Progress Penilaian per Kelas
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
                                    <strong>Informasi:</strong> Tabel ini menampilkan progress pengisian nilai per kelas. Klik pada baris kelas untuk melihat detail per santri.
                                </p>

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
                                                            <th>Nama Kelas</th>
                                                            <th class="text-center">Total Santri</th>
                                                            <th class="text-center">Sudah Dinilai</th>
                                                            <th class="text-center">Belum Dinilai</th>
                                                            <th class="text-center">Progress</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikProgressNilaiPerTpq['Ganjil']) && !empty($StatistikProgressNilaiPerTpq['Ganjil'][0]['Kelas'])): ?>
                                                            <?php $no = 1; ?>
                                                            <?php foreach ($StatistikProgressNilaiPerTpq['Ganjil'][0]['Kelas'] as $kelas): ?>
                                                                <?php
                                                                $kelasKey = md5($StatistikProgressNilaiPerTpq['Ganjil'][0]['IdTpq'] . '_' . $kelas['IdKelas']);
                                                                $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;
                                                                ?>
                                                                <!-- Row Kelas (Parent) - Tertutup secara default -->
                                                                <tr class="kelas-row" data-kelas-key="<?= $kelasKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                    <td class="text-center"><?= $no++ ?></td>
                                                                    <td class="text-center">
                                                                        <?php if ($hasSantri): ?>
                                                                            <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                        <?php else: ?>
                                                                            <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <strong><?= esc($kelas['NamaKelas']) ?></strong>
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
                                                                    <td class="text-center">
                                                                        <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                            <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                        <?php else: ?>
                                                                            <div class="progress" style="height: 20px;">
                                                                                <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                    style="width: <?= $kelas['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                                                    <?= $kelas['PersentaseSudah'] ?>%
                                                                                </div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                                <!-- Detail Santri (Child rows - Tertutup secara default) -->
                                                                <?php if ($hasSantri): ?>
                                                                    <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                        <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;">
                                                                            <td></td>
                                                                            <td class="text-center">
                                                                                <i class="fas fa-angle-double-right text-muted"></i>
                                                                            </td>
                                                                            <td style="padding-left: 40px;">
                                                                                <span class="text-muted small"><i class="fas fa-user text-primary mr-1"></i>Santri</span>
                                                                            </td>
                                                                            <td style="padding-left: 20px;">
                                                                                <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                    <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-info">
                                                                                    <?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?>
                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                        <span class="ml-1"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    <?php endif; ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="progress" style="height: 18px;">
                                                                                    <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= min(100, $santri['PersentaseSudah']) ?>%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                                                                                        <?= $santri['PersentaseSudah'] ?>%
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
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
                                                            <th>Nama Kelas</th>
                                                            <th class="text-center">Total Santri</th>
                                                            <th class="text-center">Sudah Dinilai</th>
                                                            <th class="text-center">Belum Dinilai</th>
                                                            <th class="text-center">Progress</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikProgressNilaiPerTpq['Genap']) && !empty($StatistikProgressNilaiPerTpq['Genap'][0]['Kelas'])): ?>
                                                            <?php $no = 1; ?>
                                                            <?php foreach ($StatistikProgressNilaiPerTpq['Genap'][0]['Kelas'] as $kelas): ?>
                                                                <?php
                                                                $kelasKey = md5($StatistikProgressNilaiPerTpq['Genap'][0]['IdTpq'] . '_' . $kelas['IdKelas']);
                                                                $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;
                                                                ?>
                                                                <!-- Row Kelas (Parent) - Tertutup secara default -->
                                                                <tr class="kelas-row" data-kelas-key="<?= $kelasKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                    <td class="text-center"><?= $no++ ?></td>
                                                                    <td class="text-center">
                                                                        <?php if ($hasSantri): ?>
                                                                            <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                        <?php else: ?>
                                                                            <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <strong><?= esc($kelas['NamaKelas']) ?></strong>
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
                                                                    <td class="text-center">
                                                                        <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                            <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                        <?php else: ?>
                                                                            <div class="progress" style="height: 20px;">
                                                                                <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                    style="width: <?= $kelas['PersentaseSudah'] ?>%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                                                    <?= $kelas['PersentaseSudah'] ?>%
                                                                                </div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                                <!-- Detail Santri (Child rows - Tertutup secara default) -->
                                                                <?php if ($hasSantri): ?>
                                                                    <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                        <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;">
                                                                            <td></td>
                                                                            <td class="text-center">
                                                                                <i class="fas fa-angle-double-right text-muted"></i>
                                                                            </td>
                                                                            <td style="padding-left: 40px;">
                                                                                <span class="text-muted small"><i class="fas fa-user text-primary mr-1"></i>Santri</span>
                                                                            </td>
                                                                            <td style="padding-left: 20px;">
                                                                                <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                    <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-info">
                                                                                    <?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?>
                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                        <span class="ml-1"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    <?php endif; ?>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="progress" style="height: 18px;">
                                                                                    <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= min(100, $santri['PersentaseSudah']) ?>%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                                                                                        <?= $santri['PersentaseSudah'] ?>%
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
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
            </div>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
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

        // Handle expand/collapse untuk kelas (kelas tertutup secara default)
        $(document).on('click', '.kelas-row', function() {
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table'); // Ambil tabel parent (Ganjil atau Genap)
            var detailRows = $table.find('.detail-' + kelasKey); // Cari hanya dalam tabel yang sama
            var expandIcon = $(this).find('.expand-icon-kelas');

            if (detailRows.length > 0) {
                if (detailRows.is(':visible')) {
                    detailRows.slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $(this).removeClass('expanded');
                } else {
                    detailRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                }
            }
        });
    });
</script>
<?= $this->endSection(); ?>