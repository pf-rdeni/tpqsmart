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

                        <!-- Semester Cards -->
                        <?php
                        // Tentukan semester saat ini berdasarkan bulan
                        // Semester Ganjil: Juli-Desember (bulan 7-12)
                        // Semester Genap: Januari-Juni (bulan 1-6)
                        $currentMonth = date('n');
                        $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                        $isSemesterGenap = ($currentMonth >= 1 && $currentMonth <= 6);
                        ?>
                        <div class="row mt-3 mt-md-4">
                            <!-- Semester Ganjil -->
                            <div class="col-12 mb-3">
                                <div class="card card-secondary card-outline <?= !$isSemesterGanjil ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title mobile-card-title">
                                            <i class="fas fa-book-reader"></i>
                                            <span class="d-none d-sm-inline">Semester Ganjil </span>
                                            <span class="d-inline d-sm-none">Sem. Ganjil </span>
                                            <span class="d-none d-md-inline">TA <?= esc($TahunAjaran ?? '') ?></span>
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
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number mobile-info-number">
                                                    <?= $TotalSantri ?? 0 ?> Santri
                                                    <span class="d-none d-sm-inline">dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                    <span class="d-inline d-sm-none">(<?= $JumlahKelasDiajar ?? 0 ?> Kls)</span>
                                                </span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah ?? 0) ?>
                                                <span class="progress-description mobile-progress-desc">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?? 0 ?>)
                                                </span>

                                                <!-- Progress per Kelas -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGanjil ?? [] as $item): ?>
                                                        <div class="col-12 col-md-6 mb-2">
                                                            <span class="info-box-text mobile-kelas-text">
                                                                <?= esc($item['NamaKelas']) ?>
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small class="mobile-small-text">Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-2 mt-md-3">
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
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="col-12">
                                <div class="card card-info card-outline <?= !$isSemesterGenap ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title mobile-card-title">
                                            <i class="fas fa-book-reader"></i>
                                            <span class="d-none d-sm-inline">Semester Genap </span>
                                            <span class="d-inline d-sm-none">Sem. Genap </span>
                                            <span class="d-none d-md-inline">TA <?= esc($TahunAjaran ?? '') ?></span>
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
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number mobile-info-number">
                                                    <?= $TotalSantri ?? 0 ?> Santri
                                                    <span class="d-none d-sm-inline">dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                    <span class="d-inline d-sm-none">(<?= $JumlahKelasDiajar ?? 0 ?> Kls)</span>
                                                </span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah ?? 0) ?>
                                                <span class="progress-description mobile-progress-desc">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?? 0 ?>)
                                                </span>

                                                <!-- Progress per Kelas -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGenap ?? [] as $item): ?>
                                                        <div class="col-12 col-md-6 mb-2">
                                                            <span class="info-box-text mobile-kelas-text">
                                                                <?= esc($item['NamaKelas']) ?>
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small class="mobile-small-text">Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-2 mt-md-3">
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
        </div>
    </div>
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
</script>
<?= $this->endSection(); ?>