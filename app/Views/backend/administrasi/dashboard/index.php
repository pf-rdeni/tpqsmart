<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/administrasi/surat-keluar') ?>" class="text-dark">
                <div class="info-box shadow-sm elevation-1">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-paper-plane"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Surat Keluar (Bln Ini)</span>
                        <span class="info-box-number font-weight-bold"><?= esc($stats['TotalKeluarBulanIni'] ?? 0) ?></span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/administrasi/surat-keluar?status=Draft') ?>" class="text-dark">
                <div class="info-box shadow-sm elevation-1">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Surat Draft</span>
                        <span class="info-box-number font-weight-bold"><?= esc($stats['TotalDraft'] ?? 0) ?></span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/administrasi/surat-masuk') ?>" class="text-dark">
                <div class="info-box shadow-sm elevation-1">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-inbox"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Surat Masuk</span>
                        <span class="info-box-number font-weight-bold"><?= esc($stats['TotalMasuk'] ?? 0) ?></span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/administrasi/template') ?>" class="text-dark">
                <div class="info-box shadow-sm elevation-1">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-layer-group"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Template Aktif</span>
                        <span class="info-box-number font-weight-bold"><?= esc($stats['TotalTemplateAktif'] ?? 0) ?></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Peringatan & Reminder Disposisi Aktif (Fase 4) -->
    <?php if (!empty($disposisiReminders)): ?>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card card-warning card-outline shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-bell text-warning mr-2"></i> Perhatian: Disposisi Memerlukan Tindak Lanjut
                            <span class="badge badge-danger ml-2"><?= count($disposisiReminders) ?> Perlu Diproses</span>
                        </h3>
                        <a href="<?= base_url('backend/administrasi/surat-masuk') ?>" class="btn btn-tool text-primary font-weight-bold">
                            Lihat Semua Surat Masuk <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Surat Masuk</th>
                                        <th>Dari</th>
                                        <th>Penerima (Staf)</th>
                                        <th>Instruksi / Catatan</th>
                                        <th>Prioritas</th>
                                        <th>Batas Waktu</th>
                                        <th>Status</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($disposisiReminders as $disp): ?>
                                        <?php 
                                            $isOverdue = (!empty($disp['Deadline']) && strtotime($disp['Deadline']) < strtotime(date('Y-m-d')));
                                            $isToday   = (!empty($disp['Deadline']) && $disp['Deadline'] === date('Y-m-d'));
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($disp['NomorSurat'] ?: '-') ?></strong>
                                                <div class="text-muted small"><?= esc($disp['Hal'] ?: '-') ?></div>
                                            </td>
                                            <td><?= esc($disp['DariUsername'] ?: 'Pimpinan') ?></td>
                                            <td><strong><?= esc($disp['KeUsername'] ?: 'Semua') ?></strong></td>
                                            <td><?= esc($disp['Instruksi'] ?: '-') ?></td>
                                            <td>
                                                <?php if ($disp['Prioritas'] === 'Sangat Segera'): ?>
                                                    <span class="badge badge-danger">Sangat Segera</span>
                                                <?php elseif ($disp['Prioritas'] === 'Segera'): ?>
                                                    <span class="badge badge-warning">Segera</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Biasa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($disp['Deadline'])): ?>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="badge badge-danger" title="Telah lewat batas waktu!">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i><?= date('d/m/Y', strtotime($disp['Deadline'])) ?>
                                                        </span>
                                                    <?php elseif ($isToday): ?>
                                                        <span class="badge badge-warning">Hari Ini (<?= date('d/m/Y', strtotime($disp['Deadline'])) ?>)</span>
                                                    <?php else: ?>
                                                        <span class="text-muted"><?= date('d/m/Y', strtotime($disp['Deadline'])) ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (empty($disp['StatusBaca'])): ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-envelope mr-1"></i> Belum Dibaca</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border"><i class="fas fa-envelope-open mr-1"></i> Dibaca</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('backend/administrasi/surat-masuk/detail/' . $disp['IdSurat']) ?>" class="btn btn-primary btn-xs" title="Buka Detail Surat & Tanggapi Disposisi">
                                                    <i class="fas fa-reply mr-1"></i> Proses
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Navigation / Module Shortcuts -->
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-th-large mr-2"></i>Menu Administrasi Surat</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/surat-keluar/buat') ?>" class="btn btn-primary btn-block text-left p-3 shadow-sm">
                                <i class="fas fa-paper-plane fa-2x float-right text-white opacity-75"></i>
                                <div class="font-weight-bold text-white">Buat Surat Keluar</div>
                                <small class="text-white-50">Wizard 8 langkah cetak PDF</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/surat-keluar') ?>" class="btn btn-outline-success btn-block text-left p-3 shadow-sm">
                                <i class="fas fa-archive fa-2x float-right text-success opacity-50"></i>
                                <div class="font-weight-bold">Arsip Surat Keluar</div>
                                <small class="text-muted">Riwayat & unduh ulang PDF</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/surat-masuk') ?>" class="btn btn-outline-info btn-block text-left p-3 shadow-sm">
                                <i class="fas fa-inbox fa-2x float-right text-info opacity-50"></i>
                                <div class="font-weight-bold">Surat Masuk & Disposisi</div>
                                <small class="text-muted">Catat agenda & alur staf</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/template') ?>" class="btn btn-outline-primary btn-block text-left p-3">
                                <i class="fas fa-file-invoice fa-2x float-right text-primary opacity-50"></i>
                                <div class="font-weight-bold">Template Surat</div>
                                <small class="text-muted">Kelola master template surat</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/tujuan') ?>" class="btn btn-outline-info btn-block text-left p-3">
                                <i class="fas fa-address-book fa-2x float-right text-info opacity-50"></i>
                                <div class="font-weight-bold">Database Tujuan</div>
                                <small class="text-muted">Master tujuan & pejabat</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/kop-custom') ?>" class="btn btn-outline-secondary btn-block text-left p-3">
                                <i class="fas fa-stamp fa-2x float-right text-secondary opacity-50"></i>
                                <div class="font-weight-bold">Kop Kepanitiaan</div>
                                <small class="text-muted">Upload kop kegiatan khusus</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/laporan') ?>" class="btn btn-outline-success btn-block text-left p-3">
                                <i class="fas fa-file-excel fa-2x float-right text-success opacity-50"></i>
                                <div class="font-weight-bold">Laporan & Rekap</div>
                                <small class="text-muted">Filter & ekspor Excel</small>
                            </a>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/kategori') ?>" class="btn btn-outline-secondary btn-block text-left p-3">
                                <i class="fas fa-tags fa-2x float-right text-secondary opacity-50"></i>
                                <div class="font-weight-bold">Kategori Surat</div>
                                <small class="text-muted">Konfigurasi jenis surat</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="<?= base_url('backend/administrasi/penomoran') ?>" class="btn btn-outline-warning btn-block text-left p-3">
                                <i class="fas fa-sort-numeric-down fa-2x float-right text-warning opacity-50"></i>
                                <div class="font-weight-bold">Format Penomoran</div>
                                <small class="text-muted">Auto increment nomor surat</small>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Statistik Surat (Fase 3) -->
    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-chart-bar mr-2"></i>Aktivitas Surat Keluar & Masuk (<?= esc($monthlyChart['year']) ?>)</h3>
                        <span class="badge badge-light border">Januari - Desember</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-2" style="height: 250px;">
                        <canvas id="monthlyChart" height="250"></canvas>
                    </div>
                    <div class="d-flex flex-row justify-content-end text-sm">
                        <span class="mr-3"><i class="fas fa-square text-primary"></i> Surat Keluar</span>
                        <span><i class="fas fa-square text-success"></i> Surat Masuk</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card card-outline card-info shadow-sm h-100">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-chart-pie mr-2"></i>Distribusi Kategori Surat</h3>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-2" style="height: 250px;">
                        <canvas id="categoryChart" height="250"></canvas>
                    </div>
                    <div class="text-center text-muted text-xs mt-2">
                        Berdasarkan seluruh arsip surat aktif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Surat Terbaru Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-2"></i>Daftar Surat Terbaru</h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary"><?= count($suratTerbaru) ?> Surat</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th>Perihal / Hal</th>
                                <th>Tujuan</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($suratTerbaru)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Belum ada surat yang dibuat. Buat surat baru dengan tombol di atas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($suratTerbaru as $surat): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= esc($surat['NomorSurat'] ?: '-') ?></strong></td>
                                        <td><?= esc($surat['Hal'] ?: '-') ?></td>
                                        <td><?= esc($surat['TujuanNama'] ?: '-') ?></td>
                                        <td>
                                            <span class="badge badge-<?= esc($surat['WarnaKategori'] ?: 'secondary') ?>">
                                                <?= esc($surat['NamaKategori'] ?: '-') ?>
                                            </span>
                                        </td>
                                        <td><?= !empty($surat['Tanggal']) ? date('d-m-Y', strtotime($surat['Tanggal'])) : '-' ?></td>
                                        <td>
                                            <?php if ($surat['Status'] === 'Final'): ?>
                                                <span class="badge badge-success"><i class="fas fa-check"></i> Final</span>
                                            <?php elseif ($surat['Status'] === 'Draft'): ?>
                                                <span class="badge badge-warning"><i class="fas fa-edit"></i> Draft</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger"><?= esc($surat['Status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('backend/administrasi/surat-keluar/cetak/' . $surat['Id']) ?>" target="_blank" class="btn btn-outline-danger btn-xs">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('plugins/chart.js/Chart.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Bar Chart
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Surat Keluar',
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        data: <?= json_encode($monthlyChart['keluar']) ?>
                    },
                    {
                        label: 'Surat Masuk',
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        data: <?= json_encode($monthlyChart['masuk']) ?>
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{ gridLines: { display: false } }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                legend: { display: false }
            }
        });
    }

    // 2. Category Pie / Doughnut Chart
    var categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        var catLabels = <?= json_encode($categoryChart['labels']) ?>;
        var catData = <?= json_encode($categoryChart['data']) ?>;
        var defaultColors = ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545', '#6c757d', '#6610f2'];

        if (catData.length === 0) {
            catLabels = ['Belum ada surat'];
            catData = [1];
            defaultColors = ['#e9ecef'];
        }

        new Chart(categoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: defaultColors.slice(0, catLabels.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, fontSize: 11 }
                }
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>
