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

    $html = '<div class="progress mobile-progress" style="height: ' . $height . 'px;">';
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
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span class="d-none d-sm-inline">Dashboard Ujian Semester - </span>
                            <?= esc($PeranLogin ?? 'Guru') ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-3 mb-md-4">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible">
                                    <div class="d-flex align-items-start">
                                        <?php
                                        // Ambil foto profil user yang sedang login
                                        $userImage = null;
                                        $hasValidImage = false;

                                        if (function_exists('user') && user()) {
                                            $userModel = new \App\Models\UserModel();
                                            $userId = null;
                                            if (function_exists('user_id')) {
                                                $userId = user_id();
                                            } else {
                                                $userId = user()->id ?? null;
                                            }
                                            if ($userId) {
                                                $userData = $userModel->getUser($userId);
                                                $userImage = $userData['user_image'] ?? null;

                                                // Cek apakah file benar-benar ada dan bukan default.svg
                                                if (!empty($userImage) && $userImage !== 'default.svg') {
                                                    $imagePath = FCPATH . 'uploads/profil/user/' . $userImage;
                                                    if (file_exists($imagePath)) {
                                                        $hasValidImage = true;
                                                    }
                                                }
                                            }
                                        }

                                        $photoUrl = $hasValidImage
                                            ? base_url('uploads/profil/user/' . $userImage)
                                            : base_url('images/no-photo.jpg');
                                        ?>
                                        <div class="mr-3" style="flex-shrink: 0;">
                                            <img id="profilePhoto"
                                                src="<?= $photoUrl ?>"
                                                alt="Foto Profil"
                                                style="width: 90px; height: 120px; object-fit: cover; cursor: pointer; border: 2px solid #dee2e6; border-radius: 8px;"
                                                title="Double click untuk mengubah foto profil"
                                                onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-2 mb-md-3"><i class="icon fas fa-info-circle"></i> Bismillahirrahmanirrahim</h5>
                                            <p class="mb-0 small-text-mobile">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...!
                                                Selamat datang di aplikasi TPQ Smart, Anda login sebagai <strong><?= esc($PeranLogin ?? 'Guru') ?></strong>
                                                <?php if (($PeranLogin ?? '') === 'Wali Kelas' && !empty($WaliKelasNamaKelas ?? '')): ?>
                                                    - <strong><?= esc($WaliKelasNamaKelas) ?></strong>
                                                    <span class="d-none d-sm-inline">(Tahun Ajaran <?= esc($TahunAjaran ?? '') ?>)</span>
                                                    <?php endif; ?>.
                                                    <span class="d-none d-md-inline">Semoga Allah senantiasa memberkahi langkah kita dalam menuntut ilmu dan mendidik generasi penerus.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?= prayer_schedule_widget() ?>

                        <!-- Quick Access Cards -->
                        <?php
                        // Tentukan semester saat ini berdasarkan bulan untuk Quick Access Cards
                        $currentMonth = date('n');
                        $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                        $isSemesterGenap = ($currentMonth >= 1 && $currentMonth <= 6);
                        $semesterAktif = $isSemesterGanjil ? 'Ganjil' : 'Genap';
                        ?>
                        <div class="row">
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 class="mobile-h3"><?= $TotalSantri ?? 0 ?></h3>
                                        <p class="mb-1">Total Santri</p>
                                        <small class="d-block"><?= $JumlahKelasDiajar ?? 0 ?> Kelas</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= base_url('backend/absensi/index') ?>" class="small-box-footer">
                                        <span>Absensi </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 class="mobile-h3"><i class="fas fa-edit"></i></h3>
                                        <p class="mb-1">Ubah Absensi</p>
                                        <small class="d-block">Edit Status Kehadiran</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <a href="<?= base_url('backend/absensi/ubahAbsensi') ?>" class="small-box-footer">
                                        <span>Ubah Absensi </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 class="mobile-h3"><i class="fas fa-edit"></i></h3>
                                        <p class="mb-1">Input Nilai</p>
                                        <small class="d-block">Input Nilai Santri</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                    <a href="<?= base_url('backend/nilai/showSantriPerKelas/' . $semesterAktif) ?>" class="small-box-footer">
                                        <span>Input Nilai </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3 class="mobile-h3">Prestasi</h3>
                                        <p class="mb-1">Catatan Prestasi</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <a href="<?= base_url('backend/prestasi/showPerKelas') ?>" class="small-box-footer">
                                        <span>Lihat Detail </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Kehadiran Per Minggu -->
                        <div class="row mt-3 mt-md-4">
                            <div class="col-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title mobile-card-title">
                                            <i class="fas fa-chart-bar"></i> Kehadiran Per Minggu
                                        </h3>
                                        <div class="card-tools">
                                            <a href="<?= base_url('backend/absensi/statistikKehadiran') ?>" class="btn btn-primary btn-sm mr-2">
                                                <i class="fas fa-info-circle"></i> <span class="d-none d-sm-inline">Detail</span>
                                            </a>
                                            <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Diagram Batang -->
                                            <div class="col-md-6 mb-3">
                                                <h5 class="text-center mb-3">Kehadiran Per Hari (Minggu Ini)</h5>
                                                <canvas id="dashboardBarChart" style="max-height: 300px;"></canvas>
                                            </div>
                                            <!-- Diagram Pie -->
                                            <div class="col-md-6 mb-3">
                                                <h5 class="text-center mb-3">Perbandingan Kehadiran</h5>
                                                <canvas id="dashboardPieChart" style="max-height: 300px;"></canvas>
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
    <?php if (!empty($StatistikProgressNilaiPerKelas)): ?>
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
                                    <strong>Informasi:</strong> Tabel ini menampilkan progress pengisian nilai per kelas yang Anda ajar. Klik pada baris kelas untuk melihat detail per santri.
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
                                                            <th style="width: 50px;"></th>
                                                            <th>Nama Kelas</th>
                                                            <th class="text-center">Total Santri</th>
                                                            <th class="text-center">Sudah Dinilai</th>
                                                            <th class="text-center">Belum Dinilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikProgressNilaiPerKelas['Ganjil'])): ?>
                                                            <?php foreach ($StatistikProgressNilaiPerKelas['Ganjil'] as $kelas): ?>
                                                                <?php
                                                                $kelasKey = md5($kelas['IdKelas']);
                                                                $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;

                                                                // Hitung total materi dan materi terisi per kelas
                                                                $totalMateriKelas = 0;
                                                                $materiTerisiKelas = 0;
                                                                if ($hasSantri) {
                                                                    foreach ($kelas['Santri'] as $santri) {
                                                                        $totalMateriKelas += ($santri['TotalMateri'] ?? 0);
                                                                        $materiTerisiKelas += ($santri['MateriTerisi'] ?? 0);
                                                                    }
                                                                }
                                                                $persentaseMateriKelas = $totalMateriKelas > 0 ? round(($materiTerisiKelas / $totalMateriKelas) * 100, 1) : 0;
                                                                ?>
                                                                <!-- Row Kelas (Parent) - Tertutup secara default -->
                                                                <tr class="kelas-row" data-kelas-key="<?= $kelasKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                    <td class="text-center">
                                                                        <?php if ($hasSantri): ?>
                                                                            <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                        <?php else: ?>
                                                                            <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <div>
                                                                            <strong><?= esc($kelas['NamaKelas']) ?></strong>
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <?php if (empty($kelas['StatusKelas'])): ?>
                                                                                <div class="progress" style="height: 20px; margin-bottom: 5px; position: relative;">
                                                                                    <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= $kelas['PersentaseSudah'] ?>%;">
                                                                                    </div>
                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                        <?= $kelas['PersentaseSudah'] ?>% (Santri)
                                                                                    </span>
                                                                                </div>
                                                                            <?php else: ?>
                                                                                <div style="margin-bottom: 5px;">
                                                                                    <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <?php if ($hasSantri && $totalMateriKelas > 0): ?>
                                                                                <div class="progress" style="height: 20px; position: relative;">
                                                                                    <div class="progress-bar <?= $persentaseMateriKelas < 50 ? 'bg-danger' : ($persentaseMateriKelas < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= $persentaseMateriKelas ?>%;">
                                                                                    </div>
                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                        <?= $persentaseMateriKelas ?>% (Materi)
                                                                                    </span>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
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
                                                                <!-- Detail Santri (Child rows - Tertutup secara default) -->
                                                                <?php if ($hasSantri): ?>
                                                                    <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                        <?php
                                                                        $santriKey = md5($santri['IdSantri'] . '_' . $kelasKey);
                                                                        $hasMateri = ($santri['TotalMateri'] ?? 0) > 0;
                                                                        ?>
                                                                        <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;" data-santri-key="<?= $santriKey ?>" data-santri-id="<?= $santri['IdSantri'] ?>" data-kelas-id="<?= $kelas['IdKelas'] ?>" data-semester="Ganjil">
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
                                                                            <td style="padding-left: 40px; cursor: pointer;" class="santri-expand-cell">
                                                                                <?php if ($hasMateri): ?>
                                                                                    <i class="fas fa-chevron-right expand-icon-santri" style="transition: transform 0.3s; color: #007bff; margin-right: 8px;"></i>
                                                                                <?php else: ?>
                                                                                    <i class="fas fa-minus" style="color: #ccc; margin-right: 8px;"></i>
                                                                                <?php endif; ?>
                                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Ganjil') ?>" style="color: inherit; text-decoration: none; cursor: pointer;" title="Input Nilai" onclick="event.stopPropagation();">
                                                                                    <div>
                                                                                        <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                                    </div>
                                                                                    <div>
                                                                                        <span class="text-muted small">ID: <?= esc($santri['IdSantri']) ?></span>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <div class="progress" style="height: 18px; position: relative;">
                                                                                            <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                style="width: <?= min(100, $santri['PersentaseSudah']) ?>%;">
                                                                                            </div>
                                                                                            <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 11px; font-weight: 500; line-height: 18px; pointer-events: none;">
                                                                                                <?= $santri['PersentaseSudah'] ?>%
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                    <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                </span>
                                                                            </td>
                                                                            <td colspan="2" class="text-center">
                                                                                <span class="badge badge-info">
                                                                                    <?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?>
                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                        <span class="ml-1"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    <?php endif; ?>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- Group Sudah Dinilai dan Belum Dinilai (Tertutup secara default) -->
                                                                        <?php if ($hasMateri): ?>
                                                                            <!-- Group Sudah Dinilai -->
                                                                            <?php if (($santri['MateriTerisi'] ?? 0) > 0): ?>
                                                                                <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #e8f5e9;" data-group-type="sudah" data-santri-key="<?= $santriKey ?>">
                                                                                    <td></td>
                                                                                    <td style="padding-left: 60px; cursor: pointer;" class="group-expand-cell">
                                                                                        <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #28a745; margin-right: 8px;"></i>
                                                                                        <span class="badge badge-success">Sudah Dinilai</span>
                                                                                        <span class="text-muted small ml-2"><?= number_format($santri['MateriTerisi']) ?> Materi</span>
                                                                                    </td>
                                                                                    <td colspan="3"></td>
                                                                                </tr>
                                                                                <!-- Individual Materi Sudah Dinilai (akan di-load via AJAX) -->
                                                                                <tr class="materi-container detail-<?= $santriKey ?>-sudah" style="display: none;">
                                                                                    <td></td>
                                                                                    <td colspan="4" style="padding: 0;">
                                                                                        <div class="materi-loading-<?= $santriKey ?>-sudah" style="text-align: center; padding: 20px;">
                                                                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                        </div>
                                                                                        <div class="materi-content-<?= $santriKey ?>-sudah" style="display: none;"></div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                            <!-- Group Belum Dinilai -->
                                                                            <?php if (($santri['MateriBelum'] ?? 0) > 0): ?>
                                                                                <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #ffebee;" data-group-type="belum" data-santri-key="<?= $santriKey ?>">
                                                                                    <td></td>
                                                                                    <td style="padding-left: 60px; cursor: pointer;" class="group-expand-cell">
                                                                                        <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #dc3545; margin-right: 8px;"></i>
                                                                                        <span class="badge badge-danger">Belum Dinilai</span>
                                                                                        <span class="text-muted small ml-2"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    </td>
                                                                                    <td colspan="3"></td>
                                                                                </tr>
                                                                                <!-- Individual Materi Belum Dinilai (akan di-load via AJAX) -->
                                                                                <tr class="materi-container detail-<?= $santriKey ?>-belum" style="display: none;">
                                                                                    <td></td>
                                                                                    <td colspan="4" style="padding: 0;">
                                                                                        <div class="materi-loading-<?= $santriKey ?>-belum" style="text-align: center; padding: 20px;">
                                                                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                        </div>
                                                                                        <div class="materi-content-<?= $santriKey ?>-belum" style="display: none;"></div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center">Tidak ada data untuk semester Ganjil</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="row mt-3">
                                                <?php if ($isSemesterGanjil): ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-primary btn-sm mobile-btn">
                                                            <i class="fas fa-edit"></i>
                                                            <span class="d-none d-sm-inline">Input Nilai</span>
                                                            <span class="d-inline d-sm-none">Input</span>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <button class="btn btn-block btn-secondary btn-sm mobile-btn" disabled>
                                                            <i class="fas fa-edit"></i>
                                                            <span class="d-none d-sm-inline">Input Nilai</span>
                                                            <span class="d-inline d-sm-none">Input</span>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-success btn-sm mobile-btn">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="d-none d-sm-inline">Detail</span>
                                                        <span class="d-inline d-sm-none">Detail</span>
                                                    </a>
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/nilai/showRanking/Ganjil') ?>" class="btn btn-block btn-info btn-sm mobile-btn">
                                                        <i class="fas fa-trophy"></i>
                                                        <span class="d-none d-sm-inline">Rangking</span>
                                                        <span class="d-inline d-sm-none">Rangking</span>
                                                    </a>
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/rapor/index/Ganjil') ?>" class="btn btn-block btn-warning btn-sm mobile-btn">
                                                        <i class="fas fa-file-alt"></i>
                                                        <span class="d-none d-sm-inline">Raport</span>
                                                        <span class="d-inline d-sm-none">Raport</span>
                                                    </a>
                                                </div>
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
                                                            <th style="width: 50px;"></th>
                                                            <th>Nama Kelas</th>
                                                            <th class="text-center">Total Santri</th>
                                                            <th class="text-center">Sudah Dinilai</th>
                                                            <th class="text-center">Belum Dinilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($StatistikProgressNilaiPerKelas['Genap'])): ?>
                                                            <?php foreach ($StatistikProgressNilaiPerKelas['Genap'] as $kelas): ?>
                                                                <?php
                                                                $kelasKey = md5($kelas['IdKelas']);
                                                                $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;

                                                                // Hitung total materi dan materi terisi per kelas
                                                                $totalMateriKelas = 0;
                                                                $materiTerisiKelas = 0;
                                                                if ($hasSantri) {
                                                                    foreach ($kelas['Santri'] as $santri) {
                                                                        $totalMateriKelas += ($santri['TotalMateri'] ?? 0);
                                                                        $materiTerisiKelas += ($santri['MateriTerisi'] ?? 0);
                                                                    }
                                                                }
                                                                $persentaseMateriKelas = $totalMateriKelas > 0 ? round(($materiTerisiKelas / $totalMateriKelas) * 100, 1) : 0;
                                                                ?>
                                                                <!-- Row Kelas (Parent) - Tertutup secara default -->
                                                                <tr class="kelas-row" data-kelas-key="<?= $kelasKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                    <td class="text-center">
                                                                        <?php if ($hasSantri): ?>
                                                                            <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                        <?php else: ?>
                                                                            <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <div>
                                                                            <strong><?= esc($kelas['NamaKelas']) ?></strong>
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <?php if (empty($kelas['StatusKelas'])): ?>
                                                                                <div class="progress" style="height: 20px; margin-bottom: 5px; position: relative;">
                                                                                    <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= $kelas['PersentaseSudah'] ?>%;">
                                                                                    </div>
                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                        <?= $kelas['PersentaseSudah'] ?>% (Santri)
                                                                                    </span>
                                                                                </div>
                                                                            <?php else: ?>
                                                                                <div style="margin-bottom: 5px;">
                                                                                    <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <?php if ($hasSantri && $totalMateriKelas > 0): ?>
                                                                                <div class="progress" style="height: 20px; position: relative;">
                                                                                    <div class="progress-bar <?= $persentaseMateriKelas < 50 ? 'bg-danger' : ($persentaseMateriKelas < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                        style="width: <?= $persentaseMateriKelas ?>%;">
                                                                                    </div>
                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                        <?= $persentaseMateriKelas ?>% (Materi)
                                                                                    </span>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
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
                                                                <!-- Detail Santri (Child rows - Tertutup secara default) -->
                                                                <?php if ($hasSantri): ?>
                                                                    <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                        <?php
                                                                        $santriKey = md5($santri['IdSantri'] . '_' . $kelasKey);
                                                                        $hasMateri = ($santri['TotalMateri'] ?? 0) > 0;
                                                                        ?>
                                                                        <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;" data-santri-key="<?= $santriKey ?>" data-santri-id="<?= $santri['IdSantri'] ?>" data-kelas-id="<?= $kelas['IdKelas'] ?>" data-semester="Genap">
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
                                                                            <td style="padding-left: 40px; cursor: pointer;" class="santri-expand-cell">
                                                                                <?php if ($hasMateri): ?>
                                                                                    <i class="fas fa-chevron-right expand-icon-santri" style="transition: transform 0.3s; color: #007bff; margin-right: 8px;"></i>
                                                                                <?php else: ?>
                                                                                    <i class="fas fa-minus" style="color: #ccc; margin-right: 8px;"></i>
                                                                                <?php endif; ?>
                                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/Genap') ?>" style="color: inherit; text-decoration: none; cursor: pointer;" title="Input Nilai" onclick="event.stopPropagation();">
                                                                                    <div>
                                                                                        <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                                    </div>
                                                                                    <div>
                                                                                        <span class="text-muted small">ID: <?= esc($santri['IdSantri']) ?></span>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <div class="progress" style="height: 18px; position: relative;">
                                                                                            <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                style="width: <?= min(100, $santri['PersentaseSudah']) ?>%;">
                                                                                            </div>
                                                                                            <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 11px; font-weight: 500; line-height: 18px; pointer-events: none;">
                                                                                                <?= $santri['PersentaseSudah'] ?>%
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                    <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                </span>
                                                                            </td>
                                                                            <td colspan="2" class="text-center">
                                                                                <span class="badge badge-info">
                                                                                    <?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?>
                                                                                    <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                        <span class="ml-1"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    <?php endif; ?>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- Group Sudah Dinilai dan Belum Dinilai (Tertutup secara default) -->
                                                                        <?php if ($hasMateri): ?>
                                                                            <!-- Group Sudah Dinilai -->
                                                                            <?php if (($santri['MateriTerisi'] ?? 0) > 0): ?>
                                                                                <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #e8f5e9;" data-group-type="sudah" data-santri-key="<?= $santriKey ?>">
                                                                                    <td></td>
                                                                                    <td style="padding-left: 60px; cursor: pointer;" class="group-expand-cell">
                                                                                        <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #28a745; margin-right: 8px;"></i>
                                                                                        <span class="badge badge-success">Sudah Dinilai</span>
                                                                                        <span class="text-muted small ml-2"><?= number_format($santri['MateriTerisi']) ?> Materi</span>
                                                                                    </td>
                                                                                    <td colspan="3"></td>
                                                                                </tr>
                                                                                <!-- Individual Materi Sudah Dinilai (akan di-load via AJAX) -->
                                                                                <tr class="materi-container detail-<?= $santriKey ?>-sudah" style="display: none;">
                                                                                    <td></td>
                                                                                    <td colspan="4" style="padding: 0;">
                                                                                        <div class="materi-loading-<?= $santriKey ?>-sudah" style="text-align: center; padding: 20px;">
                                                                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                        </div>
                                                                                        <div class="materi-content-<?= $santriKey ?>-sudah" style="display: none;"></div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                            <!-- Group Belum Dinilai -->
                                                                            <?php if (($santri['MateriBelum'] ?? 0) > 0): ?>
                                                                                <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #ffebee;" data-group-type="belum" data-santri-key="<?= $santriKey ?>">
                                                                                    <td></td>
                                                                                    <td style="padding-left: 60px; cursor: pointer;" class="group-expand-cell">
                                                                                        <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #dc3545; margin-right: 8px;"></i>
                                                                                        <span class="badge badge-danger">Belum Dinilai</span>
                                                                                        <span class="text-muted small ml-2"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                    </td>
                                                                                    <td colspan="3"></td>
                                                                                </tr>
                                                                                <!-- Individual Materi Belum Dinilai (akan di-load via AJAX) -->
                                                                                <tr class="materi-container detail-<?= $santriKey ?>-belum" style="display: none;">
                                                                                    <td></td>
                                                                                    <td colspan="4" style="padding: 0;">
                                                                                        <div class="materi-loading-<?= $santriKey ?>-belum" style="text-align: center; padding: 20px;">
                                                                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                        </div>
                                                                                        <div class="materi-content-<?= $santriKey ?>-belum" style="display: none;"></div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center">Tidak ada data untuk semester Genap</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="row mt-3">
                                                <?php if ($isSemesterGenap): ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSantriPerKelas/Genap') ?>" class="btn btn-block btn-primary btn-sm mobile-btn">
                                                            <i class="fas fa-edit"></i>
                                                            <span class="d-none d-sm-inline">Input Nilai</span>
                                                            <span class="d-inline d-sm-none">Input</span>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <button class="btn btn-block btn-secondary btn-sm mobile-btn" disabled>
                                                            <i class="fas fa-edit"></i>
                                                            <span class="d-none d-sm-inline">Input Nilai</span>
                                                            <span class="d-inline d-sm-none">Input</span>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap') ?>" class="btn btn-block btn-success btn-sm mobile-btn">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="d-none d-sm-inline">Detail</span>
                                                        <span class="d-inline d-sm-none">Detail</span>
                                                    </a>
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/nilai/showRanking/Genap') ?>" class="btn btn-block btn-info btn-sm mobile-btn">
                                                        <i class="fas fa-trophy"></i>
                                                        <span class="d-none d-sm-inline">Rangking</span>
                                                        <span class="d-inline d-sm-none">Rangking</span>
                                                    </a>
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <a href="<?= base_url('backend/rapor/index/Genap') ?>" class="btn btn-block btn-warning btn-sm mobile-btn">
                                                        <i class="fas fa-file-alt"></i>
                                                        <span class="d-none d-sm-inline">Raport</span>
                                                        <span class="d-inline d-sm-none">Raport</span>
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
    <?php endif; ?>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(document).ready(function() {
        // Hitung tanggal minggu ini (Senin - Minggu)
        var today = new Date();
        var dayOfWeek = today.getDay(); // 0 = Minggu, 1 = Senin, dst
        var mondayOffset = (dayOfWeek == 0) ? -6 : (1 - dayOfWeek);
        var startDate = new Date(today);
        startDate.setDate(today.getDate() + mondayOffset);
        var endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + 6);

        var startDateStr = startDate.toISOString().split('T')[0];
        var endDateStr = endDate.toISOString().split('T')[0];

        // Ambil IdKelas dari session atau dari data yang ada
        // Coba ambil dari StatusInputNilaiPerKelasGanjil atau Genap
        var idKelas = null;

        // Coba ambil dari semester ganjil
        <?php if (!empty($StatusInputNilaiPerKelasGanjil ?? [])): ?>
            var kelasGanjil = <?= json_encode($StatusInputNilaiPerKelasGanjil ?? []) ?>;
            if (kelasGanjil && kelasGanjil.length > 0 && kelasGanjil[0].IdKelas) {
                idKelas = kelasGanjil[0].IdKelas;
            }
        <?php endif; ?>

        // Jika masih null, coba dari semester genap
        if (!idKelas) {
            <?php if (!empty($StatusInputNilaiPerKelasGenap ?? [])): ?>
                var kelasGenap = <?= json_encode($StatusInputNilaiPerKelasGenap ?? []) ?>;
                if (kelasGenap && kelasGenap.length > 0 && kelasGenap[0].IdKelas) {
                    idKelas = kelasGenap[0].IdKelas;
                }
            <?php endif; ?>
        }

        // Jika masih tidak ada idKelas, tidak bisa load statistik
        if (!idKelas || idKelas == 0) {
            console.warn('[DASHBOARD] IdKelas tidak ditemukan, statistik kehadiran tidak dapat dimuat');
            return;
        }

        // Load data statistik untuk minggu ini
        loadDashboardStatistik(idKelas, startDateStr, endDateStr);
    });

    var dashboardBarChart = null;
    var dashboardPieChart = null;

    function loadDashboardStatistik(idKelas, startDate, endDate) {
        $.ajax({
            url: '<?= base_url("backend/absensi/getStatistikData") ?>',
            type: 'GET',
            data: {
                IdKelas: idKelas,
                startDate: startDate,
                endDate: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    updateDashboardCharts(response);
                } else {
                    console.error('[DASHBOARD] Error loading statistik:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('[DASHBOARD] AJAX Error:', error);
            }
        });
    }

    function updateDashboardCharts(data) {
        var kehadiran = data.kehadiran || {};
        var hariLabels = data.hari_labels || [];
        var hariData = data.hari_data || [];

        // Update Pie Chart
        updateDashboardPieChart(kehadiran);

        // Update Bar Chart
        updateDashboardBarChart(hariLabels, hariData);
    }

    function updateDashboardPieChart(kehadiran) {
        var canvasId = 'dashboardPieChart';
        var ctx = document.getElementById(canvasId);

        if (!ctx) {
            console.error('[DASHBOARD] Canvas not found:', canvasId);
            return;
        }

        // Hancurkan chart lama jika ada
        if (dashboardPieChart) {
            dashboardPieChart.destroy();
        }

        dashboardPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                    data: [
                        parseInt(kehadiran.hadir || 0),
                        parseInt(kehadiran.izin || 0),
                        parseInt(kehadiran.sakit || 0),
                        parseInt(kehadiran.alfa || 0)
                    ],
                    backgroundColor: [
                        '#28a745', // Hadir - Hijau
                        '#17a2b8', // Izin - Biru
                        '#ffc107', // Sakit - Kuning
                        '#dc3545' // Alfa - Merah
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    function updateDashboardBarChart(hariLabels, hariData) {
        var canvasId = 'dashboardBarChart';
        var ctx = document.getElementById(canvasId);

        if (!ctx) {
            console.error('[DASHBOARD] Canvas not found:', canvasId);
            return;
        }

        // Hancurkan chart lama jika ada
        if (dashboardBarChart) {
            dashboardBarChart.destroy();
        }

        // Siapkan data untuk grafik
        var hadirData = [];
        var izinData = [];
        var sakitData = [];
        var alfaData = [];

        hariLabels.forEach(function(label, index) {
            if (hariData[index]) {
                hadirData.push(hariData[index].hadir || 0);
                izinData.push(hariData[index].izin || 0);
                sakitData.push(hariData[index].sakit || 0);
                alfaData.push(hariData[index].alfa || 0);
            } else {
                hadirData.push(0);
                izinData.push(0);
                sakitData.push(0);
                alfaData.push(0);
            }
        });

        dashboardBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hariLabels,
                datasets: [{
                    label: 'Hadir',
                    data: hadirData,
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1
                }, {
                    label: 'Izin',
                    data: izinData,
                    backgroundColor: '#17a2b8',
                    borderColor: '#17a2b8',
                    borderWidth: 1
                }, {
                    label: 'Sakit',
                    data: sakitData,
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1
                }, {
                    label: 'Alfa',
                    data: alfaData,
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    /* Mobile-friendly styles untuk Dashboard Guru */

    /* Responsive font sizes untuk mobile */
    @media (max-width: 576px) {
        .small-text-mobile {
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .mobile-h3 {
            font-size: 1.5rem !important;
        }

        .mobile-card-title {
            font-size: 0.95rem !important;
        }

        .mobile-info-number {
            font-size: 1.1rem !important;
        }

        .mobile-progress-desc {
            font-size: 0.8rem !important;
        }

        .mobile-kelas-text {
            font-size: 0.85rem !important;
        }

        .mobile-small-text {
            font-size: 0.75rem !important;
        }

        .mobile-btn {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Small box adjustments untuk mobile */
        .small-box .inner h3 {
            font-size: 1.8rem !important;
        }

        .small-box .inner p {
            font-size: 0.9rem !important;
            margin-bottom: 0.25rem !important;
        }

        .small-box .inner small {
            font-size: 0.75rem !important;
        }

        /* Card header adjustments */
        .card-header .card-title {
            font-size: 0.95rem !important;
        }

        /* Info box adjustments */
        .info-box-content .info-box-text {
            font-size: 0.85rem !important;
        }

        .info-box-content .info-box-number {
            font-size: 1.1rem !important;
        }

        /* Progress bar adjustments */
        .mobile-progress {
            height: 20px !important;
        }

        .mobile-progress .progress-bar {
            font-size: 0.7rem !important;
            min-height: 20px;
        }

        /* Progress bar untuk height kecil (progress per kelas) */
        .mobile-progress[style*="height: 20px"] .progress-bar {
            font-size: 0.65rem !important;
        }

        /* Button spacing untuk mobile */
        .row .col-6.mb-2 {
            margin-bottom: 0.5rem !important;
        }

        /* Card body padding untuk mobile */
        .card-body {
            padding: 0.75rem !important;
        }

        /* Alert adjustments */
        .alert h5 {
            font-size: 1rem !important;
        }

        /* Icon size untuk mobile */
        .small-box .icon {
            font-size: 3rem !important;
        }

        /* Small box footer untuk mobile - prevent overflow */
        .small-box-footer {
            padding: 0.4rem 0.5rem !important;
            font-size: 0.8rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            overflow: hidden !important;
            min-height: 2.5rem;
        }

        .small-box-footer span {
            flex: 1 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            margin-right: 0.4rem !important;
            max-width: calc(100% - 2rem);
        }

        .small-box-footer i {
            flex-shrink: 0 !important;
            font-size: 0.9rem !important;
        }
    }

    /* Tablet adjustments */
    @media (min-width: 577px) and (max-width: 768px) {
        .mobile-h3 {
            font-size: 1.8rem !important;
        }

        .mobile-card-title {
            font-size: 1rem !important;
        }

        .mobile-btn {
            font-size: 0.8rem !important;
        }
    }

    /* Pastikan button touch-friendly di mobile */
    @media (max-width: 768px) {
        .mobile-btn {
            min-height: 38px;
            padding: 0.375rem 0.75rem;
        }

        /* Pastikan card tidak terlalu lebar di mobile */
        .card {
            margin-bottom: 1rem;
        }

        /* Spacing untuk progress per kelas */
        .row .col-12.col-md-6.mb-2 {
            margin-bottom: 0.75rem;
        }
    }

    /* Optimasi untuk layar sangat kecil */
    @media (max-width: 375px) {
        .small-box .inner h3 {
            font-size: 1.5rem !important;
        }

        .mobile-h3 {
            font-size: 1.3rem !important;
        }

        .mobile-btn {
            font-size: 0.7rem !important;
            padding: 0.2rem 0.4rem !important;
        }

        .card-header {
            padding: 0.5rem 0.75rem !important;
        }

        /* Small box footer untuk layar sangat kecil */
        .small-box-footer {
            font-size: 0.75rem !important;
            padding: 0.35rem 0.4rem !important;
        }

        .small-box-footer span {
            font-size: 0.75rem !important;
            margin-right: 0.3rem !important;
        }

        .small-box-footer i {
            font-size: 0.85rem !important;
        }
    }

    /* Perbaikan spacing umum untuk mobile */
    @media (max-width: 768px) {

        /* Margin bottom untuk row */
        .row.mt-3,
        .row.mt-4 {
            margin-top: 1rem !important;
        }

        /* Padding untuk card body */
        .card-body {
            padding: 1rem !important;
        }

        /* Info box padding */
        .info-box {
            margin-bottom: 0.5rem;
        }

        /* Small box footer */
        .small-box-footer {
            padding: 0.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
        }

        .small-box-footer span {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-right: 0.5rem;
        }

        .small-box-footer i {
            flex-shrink: 0;
        }

        /* Card spacing */
        .card {
            margin-bottom: 1rem;
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Double click handler untuk edit foto profil
        let clickCount = 0;
        let clickTimer;

        $('#profilePhoto').on('click', function() {
            clickCount++;

            if (clickCount === 1) {
                clickTimer = setTimeout(function() {
                    clickCount = 0;
                }, 300);
            } else if (clickCount === 2) {
                clearTimeout(clickTimer);
                clickCount = 0;
                // Redirect ke halaman profil untuk edit foto
                window.location.href = '<?= base_url('backend/pages/profil') ?>';
            }
        });
    });

    // Key untuk localStorage
    const storageKeyCard = 'dashboardGuru_cardExpand';
    const storageKeyKelas = 'dashboardGuru_kelasExpand';

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
        // Coba ambil dari ID card
        var cardId = $card.attr('id');
        if (cardId) {
            return cardId;
        }
        // Coba ambil dari text header
        var headerText = $card.find('.card-header h3, .card-header h5').text().trim();
        if (headerText) {
            // Buat identifier dari text header (hapus spasi dan karakter khusus)
            return headerText.replace(/[^a-zA-Z0-9]/g, '_');
        }
        // Fallback: gunakan index
        return 'card_' + $card.index();
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
        // Tunggu sebentar untuk memastikan AdminLTE sudah initialize
        setTimeout(function() {
            $('.card').each(function() {
                var $card = $(this);
                // Hanya proses card yang memiliki collapse widget
                if ($card.find('[data-card-widget="collapse"]').length > 0) {
                    var cardId = getCardIdentifier($card);
                    var savedState = loadCardExpandState(cardId);
                    if (savedState !== null) {
                        if (savedState && $card.hasClass('collapsed-card')) {
                            // Expand card jika tersimpan sebagai expanded
                            $card.removeClass('collapsed-card');
                            $card.find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
                            $card.find('.card-body, .card-footer').slideDown();
                        } else if (!savedState && !$card.hasClass('collapsed-card')) {
                            // Collapse card jika tersimpan sebagai collapsed
                            $card.addClass('collapsed-card');
                            $card.find('[data-card-widget="collapse"] i').removeClass('fa-minus').addClass('fa-plus');
                            $card.find('.card-body, .card-footer').slideUp();
                        }
                    }
                }
            });
        }, 100);
    });

    // Fungsi helper untuk menentukan semester berdasarkan tabel
    function getSemesterFromTable($table) {
        var tableId = $table.attr('id') || '';
        if (tableId.includes('Ganjil')) {
            return 'Ganjil';
        } else if (tableId.includes('Genap')) {
            return 'Genap';
        }
        // Fallback: cek dari card header
        var cardHeaderText = $table.closest('.card').find('.card-header').text();
        if (cardHeaderText.includes('Ganjil')) {
            return 'Ganjil';
        } else if (cardHeaderText.includes('Genap')) {
            return 'Genap';
        }
        return '';
    }

    // Handle expand/collapse untuk kelas (kelas tertutup secara default)
    $(document).on('click', '.kelas-row', function() {
        var kelasKey = $(this).data('kelas-key');
        var $table = $(this).closest('table'); // Ambil tabel parent (Ganjil atau Genap)
        var detailRows = $table.find('.detail-' + kelasKey); // Cari hanya dalam tabel yang sama
        var expandIcon = $(this).find('.expand-icon-kelas');

        // Tentukan semester berdasarkan tabel parent
        var semester = getSemesterFromTable($table);

        if (detailRows.length > 0) {
            if (detailRows.is(':visible')) {
                // Collapse kelas dan semua child rows
                var $kelasRow = $(this);

                // Tutup semua child rows (santri, group nilai, dan materi) terlebih dahulu
                detailRows.each(function() {
                    var $santriRow = $(this);
                    if ($santriRow.hasClass('santri-row')) {
                        var santriKey = $santriRow.data('santri-key');
                        if (santriKey) {
                            // Cari dalam scope tabel yang sama
                            var $santriTable = $santriRow.closest('table');

                            // Tutup materi container dalam tabel yang sama
                            var $materiContainers = $santriTable.find('.materi-container.detail-' + santriKey + '-sudah, .materi-container.detail-' + santriKey + '-belum');
                            $materiContainers.slideUp(200);

                            // Tutup group nilai dalam tabel yang sama
                            var $groupRows = $santriTable.find('.group-nilai-row.detail-' + santriKey);
                            $groupRows.slideUp(200);
                            $groupRows.removeClass('expanded');
                            $groupRows.find('.expand-icon-group').css('transform', 'rotate(0deg)');

                            // Reset icon expand santri
                            $santriRow.find('.expand-icon-santri').css('transform', 'rotate(0deg)');
                            $santriRow.removeClass('expanded');

                            // Update localStorage untuk santri dan group
                            var santriSemester = $santriRow.data('semester') || semester;
                            saveSantriExpandState(santriKey, santriSemester, false);
                            saveGroupExpandState(santriKey, 'sudah', santriSemester, false);
                            saveGroupExpandState(santriKey, 'belum', santriSemester, false);
                        }
                    }
                });

                // Setelah semua child rows tertutup, tutup row santri
                setTimeout(function() {
                    detailRows.slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $kelasRow.removeClass('expanded');

                    // Simpan status ke localStorage
                    if (semester) {
                        saveKelasExpandState(kelasKey, semester, false);
                    }
                }, 250);
            } else {
                detailRows.slideDown(300);
                expandIcon.css('transform', 'rotate(90deg)');
                $(this).addClass('expanded');
                // Simpan status ke localStorage
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
                var detailRows = $table.find('.detail-' + kelasKey);
                var expandIcon = $(this).find('.expand-icon-kelas');

                // Tentukan semester berdasarkan tabel parent
                var semester = getSemesterFromTable($table);

                if (semester && detailRows.length > 0) {
                    var isExpanded = loadKelasExpandState(kelasKey, semester);
                    if (isExpanded) {
                        // Expand kelas jika tersimpan sebagai expanded
                        detailRows.show();
                        expandIcon.css('transform', 'rotate(90deg)');
                        $(this).addClass('expanded');
                    }
                }
            });
        }, 200);
    });

    // Key untuk localStorage santri dan group
    const storageKeySantri = 'dashboardGuru_santriExpand';
    const storageKeyGroup = 'dashboardGuru_groupExpand';

    // Fungsi untuk menyimpan status expand santri ke localStorage
    function saveSantriExpandState(santriKey, semester, isExpanded) {
        var key = semester + '_' + santriKey;
        var states = JSON.parse(localStorage.getItem(storageKeySantri) || '{}');
        states[key] = isExpanded;
        localStorage.setItem(storageKeySantri, JSON.stringify(states));
    }

    // Fungsi untuk memuat status expand santri dari localStorage
    function loadSantriExpandState(santriKey, semester) {
        var key = semester + '_' + santriKey;
        var states = JSON.parse(localStorage.getItem(storageKeySantri) || '{}');
        return states[key] === true;
    }

    // Fungsi untuk menyimpan status expand group ke localStorage
    function saveGroupExpandState(santriKey, groupType, semester, isExpanded) {
        var key = semester + '_' + santriKey + '_' + groupType;
        var states = JSON.parse(localStorage.getItem(storageKeyGroup) || '{}');
        states[key] = isExpanded;
        localStorage.setItem(storageKeyGroup, JSON.stringify(states));
    }

    // Fungsi untuk memuat status expand group dari localStorage
    function loadGroupExpandState(santriKey, groupType, semester) {
        var key = semester + '_' + santriKey + '_' + groupType;
        var states = JSON.parse(localStorage.getItem(storageKeyGroup) || '{}');
        return states[key] === true;
    }

    // Handle expand/collapse untuk santri (level 2)
    $(document).on('click', '.santri-expand-cell', function(e) {
        e.stopPropagation();
        var $santriRow = $(this).closest('.santri-row');
        var santriKey = $santriRow.data('santri-key');
        var semester = $santriRow.data('semester');
        var $table = $santriRow.closest('table'); // Ambil tabel parent untuk membedakan semester
        // Cari group rows dalam tabel yang sama
        var $groupRows = $table.find('.group-nilai-row.detail-' + santriKey);
        var expandIcon = $(this).find('.expand-icon-santri');

        if ($groupRows.length > 0) {
            if ($groupRows.is(':visible')) {
                // Collapse semua group dan materi dalam tabel yang sama
                $groupRows.slideUp(300);
                $table.find('.materi-container.detail-' + santriKey + '-sudah, .materi-container.detail-' + santriKey + '-belum').slideUp(300);
                expandIcon.css('transform', 'rotate(0deg)');
                $santriRow.removeClass('expanded');
                saveSantriExpandState(santriKey, semester, false);
            } else {
                // Expand group rows
                $groupRows.slideDown(300);
                expandIcon.css('transform', 'rotate(90deg)');
                $santriRow.addClass('expanded');
                saveSantriExpandState(santriKey, semester, true);
            }
        }
    });

    // Handle expand/collapse untuk group nilai (level 3)
    $(document).on('click', '.group-expand-cell', function(e) {
        e.stopPropagation();
        var $groupRow = $(this).closest('.group-nilai-row');
        var groupType = $groupRow.data('group-type');
        var santriKey = $groupRow.data('santri-key');
        var $table = $groupRow.closest('table'); // Ambil tabel parent untuk membedakan semester
        // Cari santri row dalam tabel yang sama dan dengan santriKey yang sama
        var $santriRow = $table.find('.santri-row[data-santri-key="' + santriKey + '"]');
        var semester = $santriRow.data('semester');
        // Cari materi container dalam tabel yang sama
        var $materiContainer = $table.find('.materi-container.detail-' + santriKey + '-' + groupType);
        var expandIcon = $(this).find('.expand-icon-group');

        if ($materiContainer.length > 0) {
            if ($materiContainer.is(':visible')) {
                // Collapse materi
                $materiContainer.slideUp(300);
                expandIcon.css('transform', 'rotate(0deg)');
                $groupRow.removeClass('expanded');
                saveGroupExpandState(santriKey, groupType, semester, false);
            } else {
                // Expand materi dan load data jika belum di-load
                $materiContainer.slideDown(300);
                expandIcon.css('transform', 'rotate(90deg)');
                $groupRow.addClass('expanded');
                saveGroupExpandState(santriKey, groupType, semester, true);

                // Load data materi via AJAX jika belum di-load
                // Gunakan selector dalam scope tabel yang sama
                var $loadingDiv = $table.find('.materi-loading-' + santriKey + '-' + groupType);
                var $contentDiv = $table.find('.materi-content-' + santriKey + '-' + groupType);

                // Load data jika belum pernah di-load
                if ($contentDiv.is(':empty') || $contentDiv.data('loaded') !== true) {
                    var idSantri = $santriRow.data('santri-id');
                    var idKelas = $santriRow.data('kelas-id');

                    $loadingDiv.show();
                    $contentDiv.hide();

                    $.ajax({
                        url: '<?= base_url('backend/dashboard/getMateriPerSantri') ?>',
                        type: 'GET',
                        data: {
                            IdSantri: idSantri,
                            IdKelas: idKelas,
                            Semester: semester
                        },
                        dataType: 'json',
                        success: function(response) {
                            $loadingDiv.hide();
                            if (response && response.success) {
                                var materiList = groupType === 'sudah' ? response.data.sudahDinilai : response.data.belumDinilai;
                                var htmlContent = '';

                                if (materiList.length > 0) {
                                    materiList.forEach(function(materi) {
                                        var badgeClass = materi.Nilai > 0 ? 'badge-success' : 'badge-danger';
                                        var nilaiText = materi.Nilai > 0 ? materi.Nilai : '0';
                                        var badgeHtml = '<span class="badge ' + badgeClass + '">' + nilaiText + '</span>';
                                        htmlContent += '<div style="padding: 8px 0; padding-left: 41px; border-bottom: 1px solid #dee2e6;">Nilai ' + badgeHtml + ' - ' + materi.NamaMateri + '</div>';
                                    });
                                } else {
                                    htmlContent = '<div style="padding: 8px 0; padding-left: 41px; text-align: center; color: #6c757d;">Tidak ada data</div>';
                                }

                                $contentDiv.html(htmlContent).show().data('loaded', true);
                            } else {
                                $contentDiv.html('<div class="alert alert-warning" style="margin: 10px 80px;">Gagal memuat data materi</div>').show();
                            }
                        },
                        error: function(xhr, status, error) {
                            $loadingDiv.hide();
                            $contentDiv.html('<div class="alert alert-danger" style="margin: 10px 80px;">Error: ' + error + '</div>').show();
                            console.error('[DASHBOARD] AJAX Error loading materi:', error);
                        }
                    });
                } else {
                    $contentDiv.show();
                }
            }
        }
    });

    // Load status expand santri dan group saat page load
    $(document).ready(function() {
        setTimeout(function() {
            $('.santri-row').each(function() {
                var $santriRow = $(this);
                var santriKey = $santriRow.data('santri-key');
                var semester = $santriRow.data('semester');
                var $table = $santriRow.closest('table'); // Ambil tabel parent untuk membedakan semester

                if (santriKey && semester) {
                    var isExpanded = loadSantriExpandState(santriKey, semester);
                    if (isExpanded) {
                        // Cari group rows dalam tabel yang sama
                        var $groupRows = $table.find('.group-nilai-row.detail-' + santriKey);
                        var expandIcon = $santriRow.find('.expand-icon-santri');

                        if ($groupRows.length > 0) {
                            $groupRows.show();
                            expandIcon.css('transform', 'rotate(90deg)');
                            $santriRow.addClass('expanded');

                            // Group nilai selalu tertutup saat refresh (tidak load dari localStorage)
                            // Pastikan semua materi container tertutup, tapi group rows tetap visible
                            $groupRows.each(function() {
                                var $groupRow = $(this);
                                var groupType = $groupRow.data('group-type');
                                var $materiContainer = $table.find('.materi-container.detail-' + santriKey + '-' + groupType);

                                // Pastikan materi container tertutup (level 4)
                                $materiContainer.hide();
                                $groupRow.removeClass('expanded');
                                $groupRow.find('.expand-icon-group').css('transform', 'rotate(0deg)');

                                // Reset data loaded untuk memastikan data di-reload saat expand
                                $table.find('.materi-content-' + santriKey + '-' + groupType).data('loaded', false);
                            });
                        }
                    }
                }
            });
        }, 300);
    });
</script>
<?= prayer_schedule_js(base_url('backend/jadwal-sholat')) ?>
<?= prayer_schedule_settings_js(base_url('backend/jadwal-sholat')) ?>
<?= $this->endSection(); ?>