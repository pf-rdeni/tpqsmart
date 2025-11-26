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
                    <div class="card-header bg-gradient-purple">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield"></i> Dashboard Ujian Semester - Kepala TPQ
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-purple btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-purple alert-dismissible">
                                    <h5><i class="icon fas fa-user-shield"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...!
                                        Selamat datang di dashboard ujian semester sebagai <strong>Kepala TPQ</strong>.
                                        Dashboard ini memberikan overview lengkap tentang kondisi akademik dan administrasi TPQ Anda.</p>
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
                                    <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="small-box-footer">
                                        Tambah Data <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Kehadiran Per Kelas (2 Minggu) -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-success card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-line"></i> Fluktuasi Kehadiran Per Kelas (2 Minggu)
                                        </h3>
                                        <div class="card-tools">
                                            <a href="<?= base_url('backend/absensi/statistikKehadiran') ?>" class="btn btn-success btn-sm mr-2">
                                                <i class="fas fa-chart-bar"></i> <span class="d-none d-sm-inline">Detail Statistik</span>
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-info-circle"></i> 
                                                    Grafik ini menampilkan fluktuasi kehadiran (Hadir) per kelas selama 2 minggu terakhir 
                                                    (1 minggu sebelum + 1 minggu saat ini). Setiap garis mewakili satu kelas.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <canvas id="kehadiranPerKelasChart" style="max-height: 400px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
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
                                                    <i class="fas fa-user-check"></i><br>Registrasi Santri
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/showSantriEmis') ?>" class="btn btn-block btn-warning btn-lg">
                                                    <i class="fas fa-file-alt"></i><br>Data EMIS
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
                        // Tentukan semester saat ini berdasarkan bulan
                        // Semester Ganjil: Juli-Desember (bulan 7-12)
                        // Semester Genap: Januari-Juni (bulan 1-6)
                        $currentMonth = date('n');
                        $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                        $isSemesterGenap = ($currentMonth >= 1 && $currentMonth <= 6);
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
</section>

<style>
    .bg-gradient-purple {
        background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);
        color: white;
    }

    .alert-purple {
        background-color: #e9d5ff;
        border-color: #d8b4fe;
        color: #6b21a8;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(document).ready(function() {
        let guruChart = null;
        let kehadiranPerKelasChart = null;

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

        // ===== Multi-Line Chart untuk Kehadiran Per Kelas =====
        function loadKehadiranPerKelasChart() {
            // Hitung periode 2 minggu: 1 minggu sebelum + 1 minggu saat ini
            const today = new Date();
            const dayOfWeek = today.getDay(); // 0 = Minggu, 1 = Senin, dst
            const mondayOffset = (dayOfWeek == 0) ? -6 : (1 - dayOfWeek);
            
            // Minggu saat ini (Senin - Minggu)
            const currentWeekMonday = new Date(today);
            currentWeekMonday.setDate(today.getDate() + mondayOffset);
            const currentWeekSunday = new Date(currentWeekMonday);
            currentWeekSunday.setDate(currentWeekMonday.getDate() + 6);
            
            // Minggu sebelumnya (7 hari sebelum Senin minggu ini)
            const previousWeekMonday = new Date(currentWeekMonday);
            previousWeekMonday.setDate(currentWeekMonday.getDate() - 7);
            const previousWeekSunday = new Date(previousWeekMonday);
            previousWeekSunday.setDate(previousWeekMonday.getDate() + 6);
            
            // Start date: Senin minggu sebelumnya
            // End date: Minggu minggu saat ini
            const startDate = previousWeekMonday.toISOString().split('T')[0];
            const endDate = currentWeekSunday.toISOString().split('T')[0];

            // Load data via AJAX
            $.ajax({
                url: '<?= base_url("backend/absensi/getKehadiranPerKelasPerHari") ?>',
                type: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        updateKehadiranPerKelasChart(response);
                    } else {
                        console.error('[KEPALA TPQ] Error loading kehadiran per kelas:', response);
                        // Tampilkan chart kosong dengan pesan error
                        showEmptyChart('Data tidak tersedia');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[KEPALA TPQ] AJAX Error:', error);
                    showEmptyChart('Terjadi kesalahan saat memuat data');
                }
            });
        }

        function updateKehadiranPerKelasChart(data) {
            const canvasId = 'kehadiranPerKelasChart';
            const ctx = document.getElementById(canvasId);

            if (!ctx) {
                console.error('[KEPALA TPQ] Canvas not found:', canvasId);
                return;
            }

            // Hancurkan chart lama jika ada
            if (kehadiranPerKelasChart) {
                kehadiranPerKelasChart.destroy();
            }

            // Generate warna untuk setiap kelas
            const colors = [
                '#28a745', // Hijau
                '#007bff', // Biru
                '#ffc107', // Kuning
                '#dc3545', // Merah
                '#6f42c1', // Ungu
                '#20c997', // Teal
                '#fd7e14', // Orange
                '#e83e8c', // Pink
                '#17a2b8', // Cyan
                '#6c757d'  // Gray
            ];

            // Siapkan datasets untuk chart
            const datasets = data.datasets.map(function(dataset, index) {
                const colorIndex = index % colors.length;
                const color = colors[colorIndex];
                
                return {
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: color,
                    backgroundColor: color + '20', // Tambahkan opacity
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1, // Smooth line
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                };
            });

            kehadiranPerKelasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y || 0;
                                    return label + ': ' + value + ' santri';
                                }
                            }
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Tanggal'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Jumlah Kehadiran (Hadir)'
                            },
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        function showEmptyChart(message) {
            const canvasId = 'kehadiranPerKelasChart';
            const ctx = document.getElementById(canvasId);

            if (!ctx) {
                return;
            }

            if (kehadiranPerKelasChart) {
                kehadiranPerKelasChart.destroy();
            }

            kehadiranPerKelasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: message,
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            });
        }

        // Load chart saat page ready
        loadKehadiranPerKelasChart();

        // Reload chart saat card di-expand (jika collapsed)
        const kehadiranCard = $('#kehadiranPerKelasChart').closest('.card');
        kehadiranCard.on('expanded.lte.cardwidget', function() {
            setTimeout(function() {
                if (!kehadiranPerKelasChart || kehadiranPerKelasChart.data.datasets.length === 0) {
                    loadKehadiranPerKelasChart();
                } else {
                    kehadiranPerKelasChart.resize();
                }
            }, 350);
        });
    });
</script>
<?= $this->endSection(); ?>