<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">

    <!-- Card Filter Header Sticky & Kontrol Tanggal -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-filter text-primary mr-1"></i> Filter & Kontrol Periode Presensi
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" data-toggle="modal" data-target="#modalThresholdSetting" title="Konfigurasi Ambang Batas Santri At-Risk">
                        <i class="fas fa-sliders-h text-warning mr-1"></i> <span class="d-none d-sm-inline">Atur Batas Warning</span>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="row align-items-end">
                <?php if ($isAdmin): ?>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <label class="small font-weight-bold mb-1"><i class="fas fa-mosque text-muted"></i> Pilih TPQ:</label>
                        <select class="form-control form-control-sm select2" id="filterTpq" style="width: 100%;">
                            <option value="">-- Semua TPQ --</option>
                            <?php foreach ($dataTpq as $tpq): ?>
                                <option value="<?= $tpq['IdTpq'] ?>" <?= ($tpq['IdTpq'] == $currentIdTpq) ? 'selected' : '' ?>>
                                    <?= esc($tpq['NamaTpq']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="<?= $isAdmin ? 'col-lg-2' : 'col-lg-3' ?> col-md-6 mb-2">
                    <label class="small font-weight-bold mb-1"><i class="fas fa-check-circle text-muted"></i> Status Kehadiran:</label>
                    <select class="form-control form-control-sm custom-select" id="filterStatusKehadiran">
                        <option value="">Semua Status</option>
                        <option value="Hadir">Hadir</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                        <option value="Alpa">Alpa</option>
                    </select>
                </div>

                <div class="<?= $isAdmin ? 'col-lg-2' : 'col-lg-3' ?> col-md-6 mb-2">
                    <label class="small font-weight-bold mb-1"><i class="fas fa-chalkboard text-muted"></i> Filter Kelas:</label>
                    <select class="form-control form-control-sm select2" id="filterKelas" multiple data-placeholder="Semua Kelas" style="width: 100%;">
                        <?php foreach ($dataKelas as $kelas): ?>
                            <option value="<?= $kelas['IdKelas'] ?>">
                                <?= esc($kelas['NamaKelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isGuru): ?>
                        <small class="text-muted"><i class="fas fa-lock text-info"></i> Kelas ampu</small>
                    <?php endif; ?>
                </div>

                <div class="<?= $isAdmin ? 'col-lg-2' : 'col-lg-2' ?> col-md-6 mb-2">
                    <label class="small font-weight-bold mb-1"><i class="fas fa-calendar-alt text-muted"></i> Tahun Ajaran:</label>
                    <select class="form-control form-control-sm" id="filterTahunAjaran">
                        <?php foreach ($tahunAjaranList as $ta): ?>
                            <option value="<?= $ta ?>" <?= ($ta == $currentIdTahunAjaran) ? 'selected' : '' ?>>
                                <?= convertTahunAjaran($ta) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="<?= $isAdmin ? 'col-lg-4' : 'col-lg-4' ?> col-md-6 mb-2">
                    <label class="small font-weight-bold mb-1"><i class="fas fa-calendar-day text-muted"></i> Rentang Tanggal:</label>
                    <div class="input-group input-group-sm">
                        <?php
                        // Hitung hari Senin minggu ini sebagai awal default
                        $defaultSenin = date('Y-m-d', strtotime('monday this week'));
                        $defaultAhad = date('Y-m-d', strtotime('sunday this week'));
                        ?>
                        <input type="date" class="form-control form-control-sm" id="filterStartDate" value="<?= $defaultSenin ?>">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text font-weight-bold">s/d</span>
                        </div>
                        <input type="date" class="form-control form-control-sm" id="filterEndDate" value="<?= $defaultAhad ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="btnApplyFilter" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Range Buttons Bar -->
                <div class="col-12 mt-2 pt-2 border-top d-flex flex-wrap align-items-center justify-content-between">
                    <div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        <button type="button" class="btn btn-outline-primary btn-quick-range" data-range="today">Hari Ini</button>
                        <button type="button" class="btn btn-outline-primary btn-quick-range active" data-range="this_week">Minggu Ini (Sen - Min)</button>
                        <button type="button" class="btn btn-outline-primary btn-quick-range" data-range="last_week">Minggu Lalu (Sen - Min)</button>
                        <button type="button" class="btn btn-outline-primary btn-quick-range" data-range="this_month">Bulan Ini</button>
                        <button type="button" class="btn btn-outline-primary btn-quick-range" data-range="semester">Semester Ini</button>
                    </div>
                    <div>
                        <span class="badge badge-light border text-muted py-1 px-2" id="labelActiveFilterRange">
                            <i class="far fa-clock mr-1"></i> Memuat data...
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1: KPI SCORECARDS (Ringkasan Kehadiran) -->
    <div class="row">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box bg-gradient-info elevation-2">
                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">Persentase Kehadiran</span>
                    <span class="info-box-number" id="kpiPersentase" style="font-size: 1.6rem;">0%</span>
                    <div class="progress">
                        <div class="progress-bar" id="kpiPersentaseBar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description text-xs" id="kpiTotalAbsenDesc">
                        0 total pencatatan presensi
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box bg-gradient-success elevation-2">
                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">Total Hadir</span>
                    <span class="info-box-number" id="kpiHadir" style="font-size: 1.6rem;">0</span>
                    <div class="progress">
                        <div class="progress-bar" id="kpiHadirBar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description text-xs" id="kpiHadirDesc">
                        0% dari total catatan
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box bg-gradient-warning elevation-2">
                <span class="info-box-icon text-white"><i class="fas fa-notes-medical"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold text-white">Sakit & Izin</span>
                    <span class="info-box-number text-white" id="kpiSakitIzin" style="font-size: 1.6rem;">0</span>
                    <div class="progress">
                        <div class="progress-bar bg-white" id="kpiSakitIzinBar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description text-xs text-white" id="kpiSakitIzinDesc">
                        Sakit: 0 | Izin: 0
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box bg-gradient-danger elevation-2">
                <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">Alpa / Tanpa Keterangan</span>
                    <span class="info-box-number" id="kpiAlpa" style="font-size: 1.6rem;">0</span>
                    <div class="progress">
                        <div class="progress-bar" id="kpiAlpaBar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description text-xs" id="kpiAlpaDesc">
                        0% dari total catatan
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: GRAFIK UTAMA (Tren Waktu & Komposisi Donut) -->
    <div class="row">
        <div class="col-lg-8 col-12">
            <div class="card card-outline card-primary shadow-sm mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-chart-line text-primary mr-1"></i> Tren Kehadiran Waktu (Time-Series)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartTrendHarian"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card card-outline card-info shadow-sm mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-chart-pie text-info mr-1"></i> Komposisi Presensi
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartDonutKomposisi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: ANALISIS KELAS (Fluktuasi Per Kelas & Ranking Kelas) -->
    <div class="row">
        <div class="col-lg-8 col-12">
            <div class="card card-outline card-success shadow-sm mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-wave-square text-success mr-1"></i> Fluktuasi Kehadiran Per Kelas
                    </h3>
                    <div class="card-tools">
                        <small class="text-muted mr-2 d-none d-md-inline"><i class="fas fa-info-circle"></i> Klik nama kelas di legenda untuk toggle</small>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 330px;">
                        <canvas id="chartFluktuasiKelas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card card-outline card-warning shadow-sm mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-trophy text-warning mr-1"></i> Ranking Kehadiran Kelas
                    </h3>
                    <div class="card-tools">
                        <small class="text-muted mr-1 d-none d-sm-inline" title="Skor mempertimbangkan kombinasi persentase kehadiran dan jumlah hari aktif/efektif presensi"><i class="fas fa-info-circle"></i> Berbobot Hari Efektif</small>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 330px;">
                        <canvas id="chartRankingKelas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: EARLY WARNING RADAR (SANTRI AT-RISK) -->
    <div class="card card-outline card-danger shadow-sm mb-3">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold text-danger">
                <i class="fas fa-exclamation-triangle mr-1"></i> Radar Early Warning: Santri Butuh Perhatian (At-Risk)
            </h3>
            <div class="card-tools d-flex align-items-center">
                <span class="badge badge-danger mr-2" id="badgeTotalAtRisk">0 Santri</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="px-3 py-2 bg-light border-bottom text-muted small d-flex justify-content-between flex-wrap">
                <span>Kriteria Santri Berisiko: Kehadiran &lt; <strong id="lblThresholdPct">75%</strong> ATAU Alpa &gt;= <strong id="lblThresholdAlpa">3x</strong> pada periode ini.</span>
                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalThresholdSetting" class="text-primary font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Ubah Ambang Batas
                </a>
            </div>
            <div class="table-responsive" style="max-height: 280px;">
                <table class="table table-hover table-striped table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>IdSantri</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>TPQ</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center text-danger">Alpa</th>
                            <th class="text-center">% Hadir</th>
                            <th class="text-center" width="15%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAtRisk">
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">Memuat data santri...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ROW 5: DATA MATRIX REKAP DETAIL SANTRI (DataTable) -->
    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold text-dark">
                <i class="fas fa-table mr-1 text-secondary"></i> Matriks Rekapitulasi Presensi Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblStatistikSantri" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th width="4%">No</th>
                            <th>IdSantri</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>TPQ</th>
                            <th class="text-center bg-light">Total</th>
                            <th class="text-center text-success">H</th>
                            <th class="text-center text-info">S</th>
                            <th class="text-center text-warning">I</th>
                            <th class="text-center text-danger">A</th>
                            <th class="text-center">% Hadir</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySantriRecap">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Konfigurasi Ambang Batas (Stored in LocalStorage) -->
<div class="modal fade" id="modalThresholdSetting" tabindex="-1" role="dialog" aria-labelledby="thresholdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient-primary text-white py-2">
                <h5 class="modal-title" id="thresholdModalLabel">
                    <i class="fas fa-sliders-h mr-1"></i> Pengaturan Ambang Batas Santri At-Risk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    Atur kriteria santri yang masuk ke dalam radar peringatan <strong>Early Warning (At-Risk)</strong>. Pengaturan ini tersimpan di browser Anda (LocalStorage).
                </p>
                <div class="form-group">
                    <label for="inputThresholdPct" class="font-weight-bold">Batas Minimal Persentase Kehadiran (%):</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="inputThresholdPct" min="10" max="95" value="75">
                        <div class="input-group-append">
                            <span class="input-group-text font-weight-bold">%</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">Santri dengan kehadiran di bawah angka ini akan ditandai berisiko.</small>
                </div>
                <div class="form-group">
                    <label for="inputThresholdAlpa" class="font-weight-bold">Batas Maksimal Jumlah Alpa (Kali):</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="inputThresholdAlpa" min="1" max="20" value="3">
                        <div class="input-group-append">
                            <span class="input-group-text font-weight-bold">Kali</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">Santri dengan alpa sama atau melebihi angka ini akan ditandai berisiko.</small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetThreshold">Reset Default</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveThreshold">Simpan Pengaturan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Drill-down Riwayat Absensi Santri -->
<div class="modal fade" id="modalRiwayatSantri" tabindex="-1" role="dialog" aria-labelledby="riwayatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient-info text-white py-2">
                <h5 class="modal-title" id="riwayatModalLabel">
                    <i class="fas fa-calendar-check mr-1"></i> Riwayat Presensi: <span id="lblModalNamaSantri">-</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row mb-3 pb-2 border-bottom">
                    <div class="col-md-6">
                        <strong>IdSantri:</strong> <span id="lblModalIdSantri">-</span><br>
                        <strong>Kelas:</strong> <span id="lblModalKelas">-</span>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <strong>Total Rekap:</strong> 
                        <span class="badge badge-success px-2 mr-1" id="badgeModalHadir">H: 0</span>
                        <span class="badge badge-info px-2 mr-1" id="badgeModalSakit">S: 0</span>
                        <span class="badge badge-warning px-2 mr-1" id="badgeModalIzin">I: 0</span>
                        <span class="badge badge-danger px-2" id="badgeModalAlpa">A: 0</span>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 380px;">
                    <table class="table table-bordered table-striped table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status Kehadiran</th>
                                <th>Guru Pencatat</th>
                                <th>Keterangan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayatDetail">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Memuat riwayat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js CDN jika belum terpasang di template -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Kunci LocalStorage
    const KEY_THRESHOLD_PCT = 'santri_absensi_threshold_pct';
    const KEY_THRESHOLD_ALPA = 'santri_absensi_threshold_alpa';
    const DEFAULT_THRESHOLD_PCT = 75;
    const DEFAULT_THRESHOLD_ALPA = 3;

    // Inisialisasi threshold setting
    let currentThresholdPct = parseInt(localStorage.getItem(KEY_THRESHOLD_PCT)) || DEFAULT_THRESHOLD_PCT;
    let currentThresholdAlpa = parseInt(localStorage.getItem(KEY_THRESHOLD_ALPA)) || DEFAULT_THRESHOLD_ALPA;

    document.getElementById('lblThresholdPct').innerText = currentThresholdPct + '%';
    document.getElementById('lblThresholdAlpa').innerText = currentThresholdAlpa + 'x';
    document.getElementById('inputThresholdPct').value = currentThresholdPct;
    document.getElementById('inputThresholdAlpa').value = currentThresholdAlpa;

    // Instance Chart.js
    let chartTrend = null;
    let chartDonut = null;
    let chartFluktuasi = null;
    let chartRanking = null;
    let dataTableInstance = null;

    // Palette warna kelas
    const colorPalette = [
        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', 
        '#6610f2', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
    ];

    // Inisialisasi Select2
    if ($('.select2').length > 0) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Helper untuk mendapatkan hari Senin dalam minggu tertentu
    function getMondayOfWeek(d) {
        const date = new Date(d);
        const day = date.getDay();
        // day: 0 (Min), 1 (Sen), 2 (Sel), dst.
        // Jika Minggu (0), mundur 6 hari untuk dapat Senin minggu ini
        const diff = date.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(date.setDate(diff));
    }

    // Handle Quick Range Buttons
    $('.btn-quick-range').on('click', function() {
        $('.btn-quick-range').removeClass('active');
        $(this).addClass('active');

        const range = $(this).data('range');
        const today = new Date();
        let startDate = new Date();
        let endDate = new Date();

        if (range === 'today') {
            // startDate & endDate = today
        } else if (range === 'this_week') {
            // Dimulai dari hari Senin minggu ini s/d Minggu
            startDate = getMondayOfWeek(today);
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);
        } else if (range === 'last_week') {
            // Minggu lalu (Senin s/d Minggu)
            const prevWeek = new Date(today);
            prevWeek.setDate(today.getDate() - 7);
            startDate = getMondayOfWeek(prevWeek);
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);
        } else if (range === 'this_month') {
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        } else if (range === 'semester') {
            const month = today.getMonth() + 1;
            if (month >= 7 && month <= 12) {
                startDate = new Date(today.getFullYear(), 6, 1); // 1 Juli
                endDate = new Date(today.getFullYear(), 11, 31); // 31 Des
            } else {
                startDate = new Date(today.getFullYear(), 0, 1); // 1 Jan
                endDate = new Date(today.getFullYear(), 5, 30); // 30 Juni
            }
        }

        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        $('#filterStartDate').val(formatDate(startDate));
        $('#filterEndDate').val(formatDate(endDate));

        loadAnalyticsData();
    });

    // Handle Setting Threshold Modal
    $('#btnSaveThreshold').on('click', function() {
        const pct = parseInt($('#inputThresholdPct').val()) || DEFAULT_THRESHOLD_PCT;
        const alpa = parseInt($('#inputThresholdAlpa').val()) || DEFAULT_THRESHOLD_ALPA;

        currentThresholdPct = pct;
        currentThresholdAlpa = alpa;
        localStorage.setItem(KEY_THRESHOLD_PCT, pct);
        localStorage.setItem(KEY_THRESHOLD_ALPA, alpa);

        $('#lblThresholdPct').text(pct + '%');
        $('#lblThresholdAlpa').text(alpa + 'x');
        $('#modalThresholdSetting').modal('hide');

        Swal.fire({
            icon: 'success',
            title: 'Tersimpan',
            text: 'Pengaturan ambang batas berhasil diperbarui!',
            timer: 1200,
            showConfirmButton: false
        });

        loadAnalyticsData();
    });

    $('#btnResetThreshold').on('click', function() {
        $('#inputThresholdPct').val(DEFAULT_THRESHOLD_PCT);
        $('#inputThresholdAlpa').val(DEFAULT_THRESHOLD_ALPA);
    });

    // Trigger Filter Click
    $('#btnApplyFilter').on('click', function() {
        loadAnalyticsData();
    });

    $('#filterTpq, #filterStatusKehadiran, #filterKelas, #filterTahunAjaran').on('change', function() {
        loadAnalyticsData();
    });

    // Main Function to Fetch & Render Analytics
    function loadAnalyticsData() {
        const idTpq = $('#filterTpq').val() || '';
        const statusKehadiran = $('#filterStatusKehadiran').val() || '';
        const idKelas = $('#filterKelas').val() || [];
        const tahunAjaran = $('#filterTahunAjaran').val() || '';
        const startDate = $('#filterStartDate').val() || '';
        const endDate = $('#filterEndDate').val() || '';

        $('#labelActiveFilterRange').html(`<i class="fas fa-spinner fa-spin mr-1"></i> Memuat data: ${startDate} s/d ${endDate}...`);

        const params = new URLSearchParams();
        if (idTpq) params.append('id_tpq', idTpq);
        if (statusKehadiran) params.append('status_kehadiran', statusKehadiran);
        if (idKelas && idKelas.length > 0) params.append('id_kelas', idKelas.join(','));
        if (tahunAjaran) params.append('id_tahun_ajaran', tahunAjaran);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        fetch(`<?= base_url('backend/santri/getStatistikAbsensiAjax') ?>?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Gagal memuat data analitik');
            }
            const data = response.data;
            $('#labelActiveFilterRange').html(`<i class="far fa-calendar-check text-success mr-1"></i> Periode: <strong>${startDate}</strong> s/d <strong>${endDate}</strong>`);

            renderKPI(data.kpi);
            renderChartTrend(data.trend_harian);
            renderChartDonut(data.kpi);
            renderChartFluktuasi(data.fluktuasi_kelas);
            renderChartRanking(data.ranking_kelas);
            renderAtRiskSantri(data.santri_recap);
            renderDataTableSantri(data.santri_recap);
        })
        .catch(err => {
            console.error(err);
            $('#labelActiveFilterRange').html(`<i class="fas fa-exclamation-circle text-danger mr-1"></i> Gagal memuat data.`);
        });
    }

    // 1. Render KPI Scorecards
    function renderKPI(kpi) {
        const total = parseInt(kpi.total_record) || 0;
        const hadir = parseInt(kpi.total_hadir) || 0;
        const sakit = parseInt(kpi.total_sakit) || 0;
        const izin = parseInt(kpi.total_izin) || 0;
        const alpa = parseInt(kpi.total_alpa) || 0;

        const pctHadir = total > 0 ? ((hadir / total) * 100).toFixed(1) : 0;
        const pctAlpa = total > 0 ? ((alpa / total) * 100).toFixed(1) : 0;
        const pctSakitIzin = total > 0 ? (((sakit + izin) / total) * 100).toFixed(1) : 0;

        $('#kpiPersentase').text(pctHadir + '%');
        $('#kpiPersentaseBar').css('width', Math.min(100, pctHadir) + '%');
        $('#kpiTotalAbsenDesc').text(`${total.toLocaleString()} total pencatatan presensi`);

        $('#kpiHadir').text(hadir.toLocaleString());
        $('#kpiHadirBar').css('width', Math.min(100, pctHadir) + '%');
        $('#kpiHadirDesc').text(`${pctHadir}% dari total catatan`);

        $('#kpiSakitIzin').text((sakit + izin).toLocaleString());
        $('#kpiSakitIzinBar').css('width', Math.min(100, pctSakitIzin) + '%');
        $('#kpiSakitIzinDesc').text(`Sakit: ${sakit} | Izin: ${izin}`);

        $('#kpiAlpa').text(alpa.toLocaleString());
        $('#kpiAlpaBar').css('width', Math.min(100, pctAlpa) + '%');
        $('#kpiAlpaDesc').text(`${pctAlpa}% dari total catatan`);
    }

    // 2. Render Tren Harian (Line Chart)
    function renderChartTrend(trendData) {
        const ctx = document.getElementById('chartTrendHarian');
        if (!ctx) return;

        const labels = trendData.map(item => item.Tanggal);
        const hadirData = trendData.map(item => parseInt(item.hadir) || 0);
        const sakitIzinData = trendData.map(item => (parseInt(item.sakit) || 0) + (parseInt(item.izin) || 0));
        const alpaData = trendData.map(item => parseInt(item.alpa) || 0);

        if (chartTrend) chartTrend.destroy();

        chartTrend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadirData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2
                    },
                    {
                        label: 'Sakit & Izin',
                        data: sakitIzinData,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.3,
                        fill: false,
                        borderWidth: 2
                    },
                    {
                        label: 'Alpa',
                        data: alpaData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3,
                        fill: false,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // 3. Render Komposisi Donut Chart
    function renderChartDonut(kpi) {
        const ctx = document.getElementById('chartDonutKomposisi');
        if (!ctx) return;

        const hadir = parseInt(kpi.total_hadir) || 0;
        const sakit = parseInt(kpi.total_sakit) || 0;
        const izin = parseInt(kpi.total_izin) || 0;
        const alpa = parseInt(kpi.total_alpa) || 0;

        if (chartDonut) chartDonut.destroy();

        chartDonut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
                datasets: [{
                    data: [hadir, sakit, izin, alpa],
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 4. Render Fluktuasi Kehadiran Per Kelas (Multi-line Chart)
    function renderChartFluktuasi(fluktuasiData) {
        const ctx = document.getElementById('chartFluktuasiKelas');
        if (!ctx) return;

        // Ambil list unik tanggal & list unik kelas
        const dateSet = new Set();
        const classMap = new Map();

        fluktuasiData.forEach(row => {
            dateSet.add(row.Tanggal);
            if (!classMap.has(row.IdKelas)) {
                classMap.set(row.IdKelas, row.NamaKelas || `Kelas ${row.IdKelas}`);
            }
        });

        const sortedDates = Array.from(dateSet).sort();
        const datasets = [];
        let colorIdx = 0;

        classMap.forEach((className, classId) => {
            const dataPoints = sortedDates.map(tgl => {
                const found = fluktuasiData.find(d => d.Tanggal === tgl && d.IdKelas == classId);
                if (found && parseInt(found.total) > 0) {
                    const pct = ((parseInt(found.hadir) / parseInt(found.total)) * 100).toFixed(1);
                    return parseFloat(pct);
                }
                return null;
            });

            const color = colorPalette[colorIdx % colorPalette.length];
            colorIdx++;

            datasets.push({
                label: className,
                data: dataPoints,
                borderColor: color,
                backgroundColor: color,
                spanGaps: true,
                tension: 0.25,
                borderWidth: 2,
                pointRadius: 3
            });
        });

        if (chartFluktuasi) chartFluktuasi.destroy();

        chartFluktuasi = new Chart(ctx, {
            type: 'line',
            data: {
                labels: sortedDates,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y !== null ? context.parsed.y + '%' : 'Tidak ada data'}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(val) { return val + '%'; }
                        }
                    }
                }
            }
        });
    }

    // 5. Render Ranking Kelas (Horizontal Bar Chart)
    function renderChartRanking(rankingData) {
        const ctx = document.getElementById('chartRankingKelas');
        if (!ctx) return;

        // Label kelas disertai jumlah hari efektif presensinya
        const labels = rankingData.map(r => `${r.NamaKelas || 'Kelas ' + r.IdKelas} (${r.jumlah_hari_efektif || 0} Hari)`);
        // Nilai bar menggunakan Skor Indeks Performa (berbobot hari efektif)
        const values = rankingData.map(r => parseFloat(r.skor_performa !== undefined ? r.skor_performa : r.persentase_hadir) || 0);

        if (chartRanking) chartRanking.destroy();

        chartRanking = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Indeks',
                    data: values,
                    backgroundColor: values.map(v => v >= 80 ? '#28a745' : (v >= 65 ? '#17a2b8' : (v >= 50 ? '#ffc107' : '#dc3545'))),
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const r = rankingData[context.dataIndex];
                                return [
                                    ` Skor Indeks: ${r.skor_performa !== undefined ? r.skor_performa : context.parsed.x} Poin`,
                                    ` % Hadir Murni: ${r.persentase_hadir}% (${r.hadir || 0}/${r.total || 0} presensi)`,
                                    ` Hari Efektif: ${r.jumlah_hari_efektif || 0} Hari (Max Periode: ${r.max_hari_efektif || '-'} Hari)`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: v => v + ' Poin' }
                    }
                }
            }
        });
    }

    // 6. Render Santri At-Risk (Early Warning Radar)
    function renderAtRiskSantri(santriList) {
        const tbody = document.getElementById('tbodyAtRisk');
        if (!tbody) return;

        // Filter santri sesuai setting threshold dinamis
        const atRiskList = santriList.filter(s => {
            const pct = parseFloat(s.persentase_hadir) || 0;
            const alpa = parseInt(s.total_alpa) || 0;
            return pct < currentThresholdPct || alpa >= currentThresholdAlpa;
        });

        $('#badgeTotalAtRisk').text(`${atRiskList.length} Santri`);

        if (atRiskList.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center text-success py-3">
                        <i class="fas fa-check-circle mr-1"></i> Tidak ada santri yang berstatus at-risk pada ambang batas saat ini.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        atRiskList.forEach((s, idx) => {
            const noHp = (s.NoHpOrtu || '').replace(/[^0-9]/g, '');
            let waBtn = '';
            if (noHp) {
                let formattedPhone = noHp;
                if (formattedPhone.startsWith('0')) {
                    formattedPhone = '62' + formattedPhone.substring(1);
                }
                const msg = encodeURIComponent(`Assalamu'alaikum wr. wb., Kami dari pengurus TPQ menginformasikan perihal presensi Ananda ${s.NamaSantri} (Kelas ${s.NamaKelas}) yang tercatat memiliki ${s.total_alpa} kali alpa pada periode ini. Mohon konfirmasinya. Terima kasih.`);
                waBtn = `<a href="https://wa.me/${formattedPhone}?text=${msg}" target="_blank" class="btn btn-xs btn-success mr-1" title="Hubungi Wali via WhatsApp"><i class="fab fa-whatsapp"></i> WA</a>`;
            }

            html += `
                <tr>
                    <td class="text-center font-weight-bold">${idx + 1}</td>
                    <td><code>${s.IdSantri}</code></td>
                    <td class="font-weight-bold">${s.NamaSantri}</td>
                    <td>${s.NamaKelas}</td>
                    <td>${s.NamaTpq || '-'}</td>
                    <td class="text-center">${s.total_hadir}</td>
                    <td class="text-center">${s.total_sakit}</td>
                    <td class="text-center">${s.total_izin}</td>
                    <td class="text-center font-weight-bold text-danger">${s.total_alpa}</td>
                    <td class="text-center"><span class="badge badge-danger">${s.persentase_hadir}%</span></td>
                    <td class="text-center">
                        ${waBtn}
                        <button type="button" class="btn btn-xs btn-info btn-view-riwayat" data-id="${s.IdSantri}" data-nama="${s.NamaSantri}" data-kelas="${s.NamaKelas}" title="Lihat Riwayat Presensi">
                            <i class="fas fa-history"></i> Riwayat
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // 7. Render DataTable Rekap Santri
    function renderDataTableSantri(santriList) {
        const tbody = document.getElementById('tbodySantriRecap');
        if (!tbody) return;

        if ($.fn.DataTable.isDataTable('#tblStatistikSantri')) {
            $('#tblStatistikSantri').DataTable().clear().destroy();
        }

        let rowsHtml = '';
        santriList.forEach((s, idx) => {
            const pct = parseFloat(s.persentase_hadir) || 0;
            let badgeClass = 'badge-success';
            let statusText = 'Sangat Baik';

            if (pct < currentThresholdPct || parseInt(s.total_alpa) >= currentThresholdAlpa) {
                badgeClass = 'badge-danger';
                statusText = 'Kritis / At-Risk';
            } else if (pct < 85) {
                badgeClass = 'badge-warning';
                statusText = 'Cukup';
            }

            rowsHtml += `
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td><code>${s.IdSantri}</code></td>
                    <td class="font-weight-bold">${s.NamaSantri}</td>
                    <td>${s.NamaKelas}</td>
                    <td>${s.NamaTpq || '-'}</td>
                    <td class="text-center font-weight-bold">${s.total_absen}</td>
                    <td class="text-center text-success">${s.total_hadir}</td>
                    <td class="text-center text-info">${s.total_sakit}</td>
                    <td class="text-center text-warning">${s.total_izin}</td>
                    <td class="text-center text-danger font-weight-bold">${s.total_alpa}</td>
                    <td class="text-center font-weight-bold">${s.persentase_hadir}%</td>
                    <td class="text-center"><span class="badge ${badgeClass}">${statusText}</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-primary btn-view-riwayat" data-id="${s.IdSantri}" data-nama="${s.NamaSantri}" data-kelas="${s.NamaKelas}" title="Lihat Riwayat Presensi">
                            <i class="fas fa-search mr-1"></i>Detail
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = rowsHtml;

        // Inisialisasi DataTable dengan tombol export
        dataTableInstance = $('#tblStatistikSantri').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pageLength: 25,
            dom: "<'row'<'col-md-6'l><'col-md-6'f>><'row'<'col-md-12'B>><'row'<'col-md-12'tr>><'row'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'copy', className: 'btn-sm btn-secondary' },
                { extend: 'excel', className: 'btn-sm btn-success', title: 'Statistik_Presensi_Santri' },
                { extend: 'pdf', className: 'btn-sm btn-danger', title: 'Statistik_Presensi_Santri' },
                { extend: 'print', className: 'btn-sm btn-info' },
                { extend: 'colvis', className: 'btn-sm btn-light' }
            ],
            language: {
                search: "Cari Santri:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ santri",
                infoEmpty: "Tidak ada data santri",
                zeroRecords: "Santri tidak ditemukan",
                paginate: { first: "Awal", last: "Akhir", next: "&rarr;", previous: "&larr;" }
            }
        });
    }

    // Event Delegasi: Tombol Detail Riwayat Absensi Santri
    $(document).on('click', '.btn-view-riwayat', function() {
        const idSantri = $(this).data('id');
        const namaSantri = $(this).data('nama');
        const kelasSantri = $(this).data('kelas');

        $('#lblModalNamaSantri').text(namaSantri);
        $('#lblModalIdSantri').text(idSantri);
        $('#lblModalKelas').text(kelasSantri);

        const startDate = $('#filterStartDate').val();
        const endDate = $('#filterEndDate').val();
        const tahunAjaran = $('#filterTahunAjaran').val();

        $('#tbodyRiwayatDetail').html(`<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data riwayat...</td></tr>`);
        $('#modalRiwayatSantri').modal('show');

        fetch(`<?= base_url('backend/santri/getRiwayatSantriAbsensiAjax') ?>/${idSantri}?start_date=${startDate}&end_date=${endDate}&id_tahun_ajaran=${tahunAjaran}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error('Gagal mengambil data');

            const riwayat = data.riwayat || [];
            let h = 0, s = 0, i = 0, a = 0;

            if (riwayat.length === 0) {
                $('#tbodyRiwayatDetail').html(`<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada catatan presensi pada rentang tanggal ini.</td></tr>`);
            } else {
                let html = '';
                riwayat.forEach((row, idx) => {
                    let badge = 'badge-success';
                    if (row.Kehadiran === 'Hadir') { h++; badge = 'badge-success'; }
                    else if (row.Kehadiran === 'Sakit') { s++; badge = 'badge-info'; }
                    else if (row.Kehadiran === 'Izin') { i++; badge = 'badge-warning'; }
                    else if (row.Kehadiran === 'Alpa') { a++; badge = 'badge-danger'; }

                    html += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td>${row.Tanggal}</td>
                            <td class="text-center"><span class="badge ${badge} px-2 py-1">${row.Kehadiran}</span></td>
                            <td>${row.NamaGuru || '-'}</td>
                            <td>${row.Keterangan || '-'}</td>
                        </tr>
                    `;
                });
                $('#tbodyRiwayatDetail').html(html);
            }

            $('#badgeModalHadir').text('H: ' + h);
            $('#badgeModalSakit').text('S: ' + s);
            $('#badgeModalIzin').text('I: ' + i);
            $('#badgeModalAlpa').text('A: ' + a);
        })
        .catch(err => {
            console.error(err);
            $('#tbodyRiwayatDetail').html(`<tr><td colspan="5" class="text-center text-danger py-3">Gagal mengambil data riwayat.</td></tr>`);
        });
    });

    // Inisialisasi awal saat halaman dimuat
    loadAnalyticsData();

});
</script>

<?= $this->endSection(); ?>

