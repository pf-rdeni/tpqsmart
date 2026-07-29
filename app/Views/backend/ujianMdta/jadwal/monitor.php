<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .live-timer-badge {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 1px;
    }
    .live-timer-badge.bg-success,
    body.dark-mode .live-timer-badge.bg-success,
    .dark-mode .live-timer-badge.bg-success {
        background-color: #16a34a !important;
        color: #ffffff !important;
    }
    .live-timer-badge.bg-warning,
    body.dark-mode .live-timer-badge.bg-warning,
    .dark-mode .live-timer-badge.bg-warning {
        background-color: #ca8a04 !important;
        color: #000000 !important;
    }
    .live-timer-badge.bg-danger,
    body.dark-mode .live-timer-badge.bg-danger,
    .dark-mode .live-timer-badge.bg-danger {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }
    .badge-pulse {
        animation: pulse-animation 1.5s infinite !important;
    }
    @keyframes pulse-animation {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold d-flex align-items-center flex-wrap gap-2">
                    <span>
                        <span class="spinner-grow spinner-grow-sm text-danger me-2" role="status"></span>
                        <?= esc($jadwal['NamaUjian']) ?>
                    </span>
                    <span class="badge text-white px-3 py-1 fs-6" style="background-color: <?= ($attemptKeFilter ?? 1) == 1 ? '#0d6efd' : '#6f42c1' ?>;">
                        <i class="fas <?= ($attemptKeFilter ?? 1) == 1 ? 'fa-pen' : 'fa-redo' ?> me-1"></i> <?= esc($namaAttemptFilter ?? 'Ujian Utama') ?>
                    </span>
                </h4>
                <small class="text-muted">Pemantauan real-time & Kontrol Pengawas (Auto-sync tiap 5 detik)</small>
            </div>
            <!-- Standard AdminLTE Integrated Action Group -->
            <div class="btn-group btn-group-sm shadow-xs" role="group" aria-label="Monitor Toolbar">
                <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>

                <div class="btn-group btn-group-sm" role="group">
                    <button id="syncIntervalBtn" type="button" class="btn btn-outline-secondary dropdown-toggle fw-bold text-primary" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ubah interval auto-sync">
                        <i class="fas fa-sync-alt me-1"></i><span id="syncIntervalLabel">5s</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" id="syncIntervalSelectMenu">
                        <a class="dropdown-item active fw-semibold" href="javascript:void(0)" onclick="selectSyncInterval(5, '5s')"><i class="fas fa-bolt text-warning me-2"></i> 5 Detik</a>
                        <a class="dropdown-item fw-semibold" href="javascript:void(0)" onclick="selectSyncInterval(10, '10s')"><i class="fas fa-clock text-info me-2"></i> 10 Detik</a>
                        <a class="dropdown-item fw-semibold" href="javascript:void(0)" onclick="selectSyncInterval(15, '15s')"><i class="fas fa-clock text-info me-2"></i> 15 Detik</a>
                        <a class="dropdown-item fw-semibold" href="javascript:void(0)" onclick="selectSyncInterval(30, '30s')"><i class="fas fa-clock text-secondary me-2"></i> 30 Detik</a>
                        <a class="dropdown-item fw-semibold" href="javascript:void(0)" onclick="selectSyncInterval(60, '1m')"><i class="fas fa-hourglass-half text-secondary me-2"></i> 1 Menit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item fw-semibold text-danger" href="javascript:void(0)" onclick="selectSyncInterval(0, 'Off')"><i class="fas fa-pause-circle me-2"></i> Matikan (Off)</a>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-secondary text-dark fw-mono" id="syncCountdownBtn" style="pointer-events: none; opacity: 1; min-width: 42px;" title="Hitung mundur sync">
                    <span id="syncCountdownText">5s</span>
                </button>

                <button type="button" class="btn btn-primary fw-bold" onclick="triggerManualSync()" title="Sinkronkan Data Sekarang">
                    <i class="fas fa-sync-alt me-1"></i> Sync
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Ringkasan Dashboard (Native AdminLTE 3 Small Boxes) -->
    <div class="row g-2 mb-4">
        <!-- Card 1: Terdaftar -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-indigo shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3 id="statTerdaftar"><?= count($sesiList) ?></h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Terdaftar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Sedang Mengerjakan -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-warning shadow-sm mb-0 rounded-3">
                <div class="inner text-dark">
                    <h3 id="statSedang">0</h3>
                    <p class="mb-0 text-uppercase fw-bold text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sedang Ujian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Di-Jeda (Pause) -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-info shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3 id="statPause">0</h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Di-Jeda (Pause)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Belum Memulai -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-secondary shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3 id="statBelum">0</h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Belum Mulai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <!-- Card 5: Selesai / Timeout -->
        <div class="col-lg col-md-4 col-12">
            <div class="small-box bg-success shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3 id="statSelesai">0</h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Selesai / Timeout</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Kontrol Serentak / Masal -->
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header bg-white py-2">
            <h6 class="card-title mb-0 fw-bold text-primary">
                <i class="fas fa-sliders-h me-2"></i> Kontrol Masal (Serentak Semua Santri)
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" onclick="openModalTambahWaktuSemua()">
                    <i class="fas fa-plus-circle me-1"></i> +Waktu Semua
                </button>
                <button type="button" class="btn btn-info btn-sm text-white fw-semibold" onclick="pauseSemua()">
                    <i class="fas fa-pause-circle me-1"></i> Pause Semua
                </button>
                <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="resumeSemua()">
                    <i class="fas fa-play-circle me-1"></i> Lanjutkan Semua
                </button>
                <button type="button" class="btn btn-danger btn-sm fw-semibold" onclick="stopSemua()">
                    <i class="fas fa-stop-circle me-1"></i> Stop (Submit) Semua
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSemuaMonitor()">
                    <i class="fas fa-undo me-1"></i> Reset Semua Santri
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Bar Monitoring -->
    <div class="card card-default card-outline card-success shadow-sm mb-3">
        <div class="card-body p-2">
            <div class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-md-4 col-12">
                    <div class="input-group input-group-sm">
                        <input type="text" id="filterMonitorSearch" class="form-control form-control-sm" placeholder="Cari Nama / ID Santri..." onkeyup="filterMonitorData()">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default btn-sm" onclick="filterMonitorData()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Status Ujian -->
                <div class="col-md-3 col-6">
                    <select id="filterMonitorStatus" class="form-control form-control-sm" onchange="filterMonitorData()">
                        <option value="">-- Semua Status Ujian --</option>
                        <option value="sedang">Sedang Mengerjakan</option>
                        <option value="pause">Di-Pause (Jeda)</option>
                        <option value="selesai">Selesai</option>
                        <option value="timeout">Timeout</option>
                        <option value="belum">Belum Memulai</option>
                    </select>
                </div>

                <!-- Filter TPQ (Khusus Admin) -->
                <?php if (!empty($isUserAdmin)): ?>
                    <div class="col-md-4 col-6">
                        <select id="filterMonitorTpq" class="form-control form-control-sm" onchange="filterMonitorData()">
                            <option value="">-- Semua Lembaga / TPQ --</option>
                            <?php
                            $tpqNames = array_unique(array_filter(array_column($sesiList, 'NamaTpq')));
                            sort($tpqNames);
                            foreach ($tpqNames as $tpqName):
                            ?>
                                <option value="<?= esc($tpqName) ?>"><?= esc($tpqName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Reset Button -->
                <div class="col-md-1 col-12 text-end ms-auto">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="resetMonitorFilters()" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Live Status Santri -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-success fw-bold">
                <i class="fas fa-users me-2"></i>Daftar Seluruh Santri & Kontrol Pengawas
            </h5>
            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1 small">
                Total: <?= count($sesiList) ?> Santri
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($sesiList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-clock fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum ada santri terdaftar untuk kelas jadwal ini</h5>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableMonitor">
                        <thead class="table-light">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>ID Santri</th>
                                <th>Nama Santri</th>
                                <?php if (!empty($isUserAdmin)): ?>
                                    <th>Lembaga / TPQ</th>
                                <?php endif; ?>
                                <th class="text-center">Status</th>
                                <th class="text-center">Progress Jawaban</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th class="text-center">Sisa Waktu Live</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center" width="230">Aksi Pengawas</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyMonitor">
                            <!-- Rendered via JS dynamically -->
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
const idJadwal    = <?= (int)$jadwal['id'] ?>;
const durasiMenit = <?= (int)$jadwal['DurasiMenit'] ?>;
const jumlahSoal  = <?= (int)$jadwal['JumlahSoal'] ?>;
const minNilai    = <?= (float)$jadwal['NilaiMinimum'] ?>;

let initialSesiList = <?= json_encode($sesiList) ?>;
let currentMonitorSesiList = initialSesiList;

function formatTime(totalSeconds) {
    if (totalSeconds <= 0) return '00:00:00';
    const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
    const s = String(totalSeconds % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function filterMonitorData() {
    renderMonitorTable(currentMonitorSesiList);
}

function resetMonitorFilters() {
    const searchEl = document.getElementById('filterMonitorSearch');
    const statusEl = document.getElementById('filterMonitorStatus');
    const tpqEl    = document.getElementById('filterMonitorTpq');
    if (searchEl) searchEl.value = '';
    if (statusEl) statusEl.value = '';
    if (tpqEl)    tpqEl.value    = '';
    renderMonitorTable(currentMonitorSesiList);
}

function renderMonitorTable(sesiList) {
    currentMonitorSesiList = sesiList || [];
    const tbody = document.getElementById('tbodyMonitor');
    if (!tbody) return;

    let cSedang = 0, cPause = 0, cBelum = 0, cSelesai = 0;

    // 1. Calculate counter stats for ALL santri
    currentMonitorSesiList.forEach((s) => {
        const st = String(s.StatusSesi || 'belum').toLowerCase().trim();
        if (st === 'sedang') cSedang++;
        else if (st === 'pause') cPause++;
        else if (st === 'selesai' || st === 'timeout') cSelesai++;
        else cBelum++;
    });

    // Update Counter Stats Header
    document.getElementById('statSedang').textContent  = cSedang;
    document.getElementById('statPause').textContent   = cPause;
    document.getElementById('statBelum').textContent   = cBelum;
    document.getElementById('statSelesai').textContent = cSelesai;

    // 2. Client-side filtering
    const keyword  = (document.getElementById('filterMonitorSearch')?.value || '').toLowerCase().trim();
    const statusF  = (document.getElementById('filterMonitorStatus')?.value || '').toLowerCase().trim();
    const tpqF     = (document.getElementById('filterMonitorTpq')?.value || '').toLowerCase().trim();

    const filtered = currentMonitorSesiList.filter(s => {
        const name   = String(s.NamaSantri || '').toLowerCase();
        const idS    = String(s.IdSantri || '').toLowerCase();
        const st     = String(s.StatusSesi || 'belum').toLowerCase().trim();
        const tpq    = String(s.NamaTpq || '').toLowerCase();

        if (keyword && !name.includes(keyword) && !idS.includes(keyword)) return false;
        if (statusF && st !== statusF) return false;
        if (tpqF && !tpq.includes(tpqF)) return false;
        return true;
    });

    // 3. Destroy DataTables if previously initialized
    if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#tableMonitor')) {
        $('#tableMonitor').DataTable().destroy();
    }

    // 4. Render HTML rows into tbody
    let html = '';
    filtered.forEach((s, idx) => {
        const idSesi     = s.idSesi || s.id || null;
        const status     = String(s.StatusSesi || 'belum').toLowerCase().trim();
        const totalJwb   = parseInt(s.TotalDijawab || 0);
        const sisaDetik  = parseInt(s.SisaDetik || 0);
        const namaSantri = (s.NamaSantri || `Santri #${s.IdSantri}`).replace(/'/g, "\\'");

        // Status Badge (Utama vs Remedial)
        const attemptKe      = parseInt(s.AttemptKe || 1);
        const isRemedialSesi = attemptKe > 1 || parseInt(s.IsRemedial || 0) === 1;
        const attemptBadge   = isRemedialSesi 
            ? `<span class="badge text-white px-2 py-1 ms-1" style="background-color: #6f42c1;"><i class="fas fa-redo me-1"></i> Remedial Ke-${attemptKe - 1}</span>`
            : `<span class="badge bg-secondary px-2 py-1 ms-1"><i class="fas fa-file-alt me-1"></i> Utama</span>`;

        let statusBadge = '';
        if (status === 'sedang') {
            statusBadge = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-spinner fa-spin me-1"></i> Sedang Mengerjakan</span>' + attemptBadge;
        } else if (status === 'pause') {
            statusBadge = '<span class="badge bg-info text-white px-2 py-1"><i class="fas fa-pause me-1"></i> Di-Pause</span>' + attemptBadge;
        } else if (status === 'selesai') {
            statusBadge = '<span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Selesai</span>' + attemptBadge;
        } else if (status === 'timeout') {
            statusBadge = '<span class="badge bg-danger px-2 py-1"><i class="fas fa-clock me-1"></i> Timeout</span>' + attemptBadge;
        } else {
            statusBadge = '<span class="badge bg-secondary px-2 py-1"><i class="fas fa-user-clock me-1"></i> Belum Memulai</span>';
        }

        // Progress Bar
        const pctProgress = jumlahSoal > 0 ? Math.min(100, Math.round((totalJwb / jumlahSoal) * 100)) : 0;
        const progressHtml = status !== 'belum' ? `
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: ${pctProgress}%;"></div>
                </div>
                <small class="fw-bold text-muted">${totalJwb}/${jumlahSoal}</small>
            </div>
        ` : '<span class="text-muted small">-</span>';

        // Sisa Waktu Display Dinamis (100%-50% Hijau, 50%-10% Kuning, 10%-0% Merah)
        let timerHtml = '-';
        if (status === 'sedang') {
            const totalDurasiDetik = (parseInt(durasiMenit || 60) * 60) + parseInt(s.TambahanWaktuDetik || 0);
            const pctSisa = totalDurasiDetik > 0 ? (sisaDetik / totalDurasiDetik) * 100 : 0;

            let timerBgCls = 'bg-success text-white';
            if (pctSisa < 10) {
                timerBgCls = 'bg-danger text-white badge-pulse';
            } else if (pctSisa < 50) {
                timerBgCls = 'bg-warning text-dark';
            }

            timerHtml = `<span class="badge ${timerBgCls} live-timer-badge px-2 py-1">${formatTime(sisaDetik)}</span>`;
        } else if (status === 'pause') {
            timerHtml = `<span class="badge bg-info text-white live-timer-badge px-2 py-1 badge-pulse"><i class="fas fa-pause me-1"></i> ${formatTime(sisaDetik)}</span>`;
        } else if (status === 'selesai' || status === 'timeout') {
            timerHtml = '<span class="badge bg-light text-secondary border">00:00:00</span>';
        }

        // Nilai
        let nilaiHtml = '<span class="text-muted">-</span>';
        if (s.NilaiAkhir !== null && s.NilaiAkhir !== undefined) {
            const numNilai = parseFloat(s.NilaiAkhir);
            const colorCls = numNilai >= minNilai ? 'text-success' : 'text-danger';
            nilaiHtml = `<span class="fw-bold ${colorCls}">${numNilai.toFixed(2)}</span>`;
        }

        // Action Buttons
        let actionButtons = '<span class="text-muted small">Belum Memulai</span>';
        if (status === 'sedang') {
            actionButtons = `
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-purple btn-sm" onclick="openModalKoreksiEsai(${idSesi}, '${namaSantri}')" title="Koreksi Esai"><i class="fas fa-pen-fancy"></i></button>
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="openModalTambahWaktu(${idSesi}, '${namaSantri}')" title="Tambah Waktu"><i class="fas fa-plus"></i> Waktu</button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="pauseSesi('${s.IdSantri}', ${idSesi || 0}, '${namaSantri}')" title="Pause Ujian"><i class="fas fa-pause"></i> Pause</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="stopSesi(${idSesi}, '${namaSantri}')" title="Hentikan & Submit Paksa"><i class="fas fa-stop"></i> Stop</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSesi(${idSesi}, '${namaSantri}')" title="Reset ke Awal"><i class="fas fa-undo"></i> Reset</button>
                </div>
            `;
        } else if (status === 'pause') {
            actionButtons = `
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-success btn-sm fw-bold" onclick="resumeSesi('${s.IdSantri}', ${idSesi || 0}, '${namaSantri}')" title="Lanjutkan Ujian"><i class="fas fa-play me-1"></i> Lanjutkan</button>
                    <button type="button" class="btn btn-outline-purple btn-sm" onclick="openModalKoreksiEsai(${idSesi}, '${namaSantri}')" title="Koreksi Esai"><i class="fas fa-pen-fancy"></i> Koreksi</button>
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="openModalTambahWaktu(${idSesi}, '${namaSantri}')" title="Tambah Waktu"><i class="fas fa-plus"></i> Waktu</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSesi(${idSesi}, '${namaSantri}')" title="Reset ke Awal"><i class="fas fa-undo"></i> Reset</button>
                </div>
            `;
        } else if (status === 'selesai' || status === 'timeout') {
            actionButtons = `
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="openModalKoreksiEsai(${idSesi}, '${namaSantri}')" title="Koreksi Jawaban Esai"><i class="fas fa-pen-fancy me-1"></i> Koreksi Esai</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetSesi(${idSesi}, '${namaSantri}')" title="Reset Sesi"><i class="fas fa-undo"></i> Reset</button>
                </div>
            `;
        } else {
            actionButtons = '<span class="text-muted small">-</span>';
        }

        const strMulai   = s.WaktuMulai ? new Date(s.WaktuMulai).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'}) : '-';
        const strSelesai = s.WaktuSelesai ? new Date(s.WaktuSelesai).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'}) : '-';
        const isUserAdmin = <?= !empty($isUserAdmin) ? 'true' : 'false' ?>;
        const tpqTd = isUserAdmin ? `<td><span class="badge bg-light text-dark border"><i class="fas fa-building text-primary me-1"></i>${s.NamaTpq || 'TPQ #' + (s.IdTpq || '-')}</span></td>` : '';

        html += `
            <tr>
                <td class="text-center">${idx + 1}</td>
                <td><code>${s.IdSantri}</code></td>
                <td class="fw-bold">${s.NamaSantri || 'Santri #' + s.IdSantri}</td>
                ${tpqTd}
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">${progressHtml}</td>
                <td><small>${strMulai}</small></td>
                <td><small>${strSelesai}</small></td>
                <td class="text-center">${timerHtml}</td>
                <td class="text-center">${nilaiHtml}</td>
                <td class="text-center">${actionButtons}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    // 5. Re-initialize DataTables cleanly
    if (typeof $.fn.DataTable !== 'undefined' && filtered.length > 0) {
        $('#tableMonitor').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ santri",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ santri",
                "infoEmpty": "Menampilkan 0 santri",
                "infoFiltered": "(disaring dari _MAX_ total santri)",
                "zeroRecords": "Tidak ada data santri yang sesuai filter",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
}

function fetchMonitorData() {
    fetch(`<?= site_url("backend/ujian-mdta/jadwal/get-monitor-ajax/{$jadwal['id']}/" . ($attemptKeFilter ?? 1)) ?>`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(r => {
        if (r.success && r.sesiList) {
            renderMonitorTable(r.sesiList);
        }
    })
    .catch(e => {
        // Silently ignore transient network errors during background polling
    });
}

// SweetAlert2 AJAX Helper
function fetchAjax(url, successMsg) {
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(r => r.json())
    .then(r => {
        if (r.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: r.message || successMsg || 'Perubahan berhasil disimpan.',
                timer: 1800,
                showConfirmButton: false
            });
            fetchMonitorData();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: r.message || 'Terjadi kesalahan.' });
        }
    })
    .catch(e => {
        console.error('AJAX Error:', e);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    });
}

function submitTambahWaktuAjax(idSesi, menit) {
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    const fd = new FormData();
    fd.append('menit', menit);
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const url = (idSesi === 'ALL')
        ? `<?= site_url("backend/ujian-mdta/jadwal/tambahWaktuSemua/{$jadwal['id']}") ?>`
        : `<?= site_url("backend/ujian-mdta/jadwal/tambahWaktuSesi/") ?>${idSesi}`;

    fetch(url, {
        method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(r => {
        if (r.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: r.message || `Tambahan waktu ${menit} menit berhasil disimpan.`,
                timer: 1800,
                showConfirmButton: false
            });
            fetchMonitorData();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: r.message });
        }
    })
    .catch(e => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    });
}

// SweetAlert2 Interactive Prompts (Individual)
function openModalTambahWaktu(idSesi, namaSantri) {
    Swal.fire({
        title: '➕ Tambah Waktu Ujian',
        html: `Masukkan jumlah tambahan waktu (menit) untuk <strong>${namaSantri || 'Santri'}</strong>:`,
        input: 'number',
        inputValue: 5,
        inputAttributes: { min: 1, max: 180, step: 1 },
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-clock me-1"></i> Simpan Tambahan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b',
        inputValidator: (val) => {
            if (!val || val <= 0) return 'Masukkan jumlah menit yang valid!';
        }
    }).then((res) => {
        if (res.isConfirmed) {
            submitTambahWaktuAjax(idSesi, res.value);
        }
    });
}

function pauseSesi(idSantri, idSesi, namaSantri) {
    Swal.fire({
        title: '⏸️ Jeda (Pause) Ujian?',
        html: `Layar ujian <strong>${namaSantri}</strong> akan terkunci dan timer dihentikan sementara.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-pause me-1"></i> Ya, Pause Sesi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0ea5e9'
    }).then((res) => {
        if (res.isConfirmed) {
            const fd = new FormData();
            fd.append('idSantri', idSantri || '');
            fd.append('idSesi', idSesi || '');
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            fetch(`<?= site_url("backend/ujian-mdta/jadwal/pauseSesi/{$jadwal['id']}") ?>`, {
                method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(r => {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: r.message, timer: 1800, showConfirmButton: false });
                    fetchMonitorData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: r.message });
                }
            });
        }
    });
}

function resumeSesi(idSantri, idSesi, namaSantri) {
    Swal.fire({
        title: '▶️ Lanjutkan Ujian?',
        html: `Buka kuncian layar ujian <strong>${namaSantri}</strong> dan lanjutkan timer?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-play me-1"></i> Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981'
    }).then((res) => {
        if (res.isConfirmed) {
            const fd = new FormData();
            fd.append('idSantri', idSantri || '');
            fd.append('idSesi', idSesi || '');
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            fetch(`<?= site_url("backend/ujian-mdta/jadwal/resumeSesi/{$jadwal['id']}") ?>`, {
                method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(r => {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: r.message, timer: 1800, showConfirmButton: false });
                    fetchMonitorData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: r.message });
                }
            });
        }
    });
}

function stopSesi(idSesi, namaSantri) {
    Swal.fire({
        title: '⏹️ Stop & Submit Paksa?',
        html: `Ujian <strong>${namaSantri}</strong> akan dihentikan paksa dan jawaban tersimpan akan langsung dinilai.`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-stop me-1"></i> Ya, Stop Ujian',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/stopSesi/") ?>${idSesi}`);
        }
    });
}

function resetSesi(idSesi, namaSantri) {
    Swal.fire({
        title: '🔄 Reset Sesi Ujian?',
        html: `Seluruh jawaban & timer untuk <strong>${namaSantri}</strong> akan dihapus dan santri dapat memulai ujian dari awal kembali.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Reset Sesi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#6b7280'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/resetSesiIndividual/") ?>${idSesi}`);
        }
    });
}

// SweetAlert2 Interactive Prompts (Mass / Serentak)
function openModalTambahWaktuSemua() {
    Swal.fire({
        title: '➕ Tambah Waktu (SEMUA SANTRI)',
        html: 'Masukkan jumlah menit tambahan waktu serentak untuk <strong>SELURUH SANTRI AKTIF</strong>:',
        input: 'number',
        inputValue: 10,
        inputAttributes: { min: 1, max: 180, step: 1 },
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-clock me-1"></i> Tambah Masal',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b',
        inputValidator: (val) => {
            if (!val || val <= 0) return 'Masukkan jumlah menit yang valid!';
        }
    }).then((res) => {
        if (res.isConfirmed) {
            submitTambahWaktuAjax('ALL', res.value);
        }
    });
}

function pauseSemua() {
    Swal.fire({
        title: '⏸️ PAUSE SEMUA SANTRI?',
        text: 'Layar ujian SELURUH SANTRI yang sedang mengerjakan akan terkunci dan timer dihentikan serentak!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-pause-circle me-1"></i> Ya, Pause Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0ea5e9'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/pauseSemua/{$jadwal['id']}") ?>`);
        }
    });
}

function resumeSemua() {
    Swal.fire({
        title: '▶️ LANJUTKAN SEMUA SANTRI?',
        text: 'Layar ujian SELURUH SANTRI yang di-jeda akan dibuka kembali dan timer dilanjutkan serentak!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-play-circle me-1"></i> Ya, Lanjutkan Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/resumeSemua/{$jadwal['id']}") ?>`);
        }
    });
}

function stopSemua() {
    Swal.fire({
        title: '⏹️ STOP & SUBMIT SEMUA SANTRI?',
        text: 'Ujian SELURUH SANTRI akan dihentikan secara paksa dan seluruh jawaban tersimpan akan langsung dinilai!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-stop-circle me-1"></i> Ya, Stop Semua Ujian',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/stopSemua/{$jadwal['id']}") ?>`);
        }
    });
}

function resetSemuaMonitor() {
    Swal.fire({
        title: '🔄 RESET MASAL SELURUH SANTRI?',
        text: 'Seluruh sesi, timer, dan jawaban santri di kelas ini akan dihapus total sehingga seluruh santri dapat memulai ujian dari awal!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-restore me-1"></i> Ya, Reset Total Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#6b7280'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujian-mdta/jadwal/resetSemuaMonitor/{$jadwal['id']}") ?>`);
        }
    });
}

function aktifkanRemedialSesi(idSesi, namaSantri) {
    Swal.fire({
        title: '🔄 AKTIFKAN REMEDIAL SANTRI?',
        html: `Apakah Anda yakin ingin mengaktifkan sesi remedial untuk <strong>${namaSantri}</strong>?<br><small class="text-muted">Tombol Mulai Remedial akan langsung aktif di dashboard santri.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-redo me-1"></i> Ya, Aktifkan Remedial',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujianMdta/jadwal/aktifkanRemedialSesi/") ?>${idSesi}`, 'Akses remedial untuk santri berhasil diaktifkan!');
        }
    });
}

function aktifkanRemedialSemua() {
    Swal.fire({
        title: '🔄 AKTIFKAN REMEDIAL MASAL?',
        html: 'Akses ujian remedial akan diaktifkan untuk <strong>SELURUH SANTRI</strong> yang belum mencapai Nilai Minimum pada kelas ini!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-redo me-1"></i> Ya, Aktifkan Remedial Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b'
    }).then((res) => {
        if (res.isConfirmed) {
            fetchAjax(`<?= site_url("backend/ujianMdta/jadwal/aktifkanRemedialSemua/{$jadwal['id']}") ?>`);
        }
    });
}

// Penilaian & Koreksi Jawaban Esai
let currentKoreksiIdSesi = null;

function openModalKoreksiEsai(idSesi, namaSantri) {
    currentKoreksiIdSesi = idSesi;
    document.getElementById('koreksi_idSesi').value = idSesi;
    document.getElementById('koreksi_namaSantri').textContent = namaSantri;
    document.getElementById('koreksi_nilaiAkhir').textContent = '...';

    const container = document.getElementById('containerKoreksiEsai');
    container.innerHTML = `
        <div class="text-center py-4 text-muted">
            <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary d-block"></i> Memuat jawaban esai santri...
        </div>
    `;

    if (window.jQuery && $('#modalKoreksiEsai').length) {
        $('#modalKoreksiEsai').modal('show');
    }

    fetch(`<?= site_url("backend/ujianMdta/jadwal/getDetailJawabanEsai/") ?>${idSesi}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            container.innerHTML = `<div class="alert alert-danger p-3">${res.message || 'Gagal memuat data.'}</div>`;
            return;
        }

        document.getElementById('koreksi_nilaiAkhir').textContent = res.nilaiAkhir;

        if (!res.listEsai || res.listEsai.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info p-3 text-center mb-0">
                    <i class="fas fa-info-circle me-1"></i> Tidak ada soal uraian / esai dalam paket soal ujian ini.
                </div>
            `;
            return;
        }

        let html = '';
        res.listEsai.forEach((item) => {
            const idJawaban  = item.idJawaban;
            const textJwb    = item.JawabanEsai ? item.JawabanEsai : '<em class="text-muted">(Santri belum mengisi jawaban)</em>';
            const currScore  = item.NilaiEsai !== null ? parseFloat(item.NilaiEsai) : '';
            const pembahasan = item.Pembahasan ? item.Pembahasan : '-';

            html += `
                <div class="card border mb-3 rounded-3 shadow-xs">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <strong class="text-primary"><i class="fas fa-pen-fancy me-1"></i> Soal Esai #${item.NomorSoal}</strong>
                        <span class="badge bg-secondary">Nomor Soal ${item.NomorSoal}</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3 text-dark fw-medium fs-6">
                            ${item.UraianSoal}
                        </div>

                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <span class="text-muted small fw-bold d-block mb-1"><i class="fas fa-comment-alt me-1 text-primary"></i> Jawaban Santri:</span>
                            <div class="text-dark bg-white p-2.5 rounded border leading-relaxed fs-6" style="white-space: pre-wrap;">${textJwb}</div>
                        </div>

                        ${item.Pembahasan ? `
                            <div class="p-2.5 bg-info-subtle border border-info-subtle rounded-3 mb-3 small">
                                <strong class="text-info-emphasis d-block mb-1"><i class="fas fa-lightbulb me-1"></i> Kunci / Panduan Jawaban:</strong>
                                <span class="text-dark">${pembahasan}</span>
                            </div>
                        ` : ''}

                        <div class="row align-items-center">
                            <label class="col-sm-5 col-form-label fw-bold text-dark">Input Skor / Nilai Esai (0 - 100):</label>
                            <div class="col-sm-4">
                                <input type="number" min="0" max="100" step="1"
                                       class="form-control form-control-sm fw-bold border-primary text-primary fs-6"
                                       name="scores[${idJawaban}]"
                                       value="${currScore}"
                                       placeholder="Nilai 0 - 100" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    })
    .catch(e => {
        container.innerHTML = `<div class="alert alert-danger p-3">Gagal terhubung ke server.</div>`;
    });
}

function submitPenilaianEsai(e) {
    e.preventDefault();
    if (!currentKoreksiIdSesi) return;

    const form = document.getElementById('formPenilaianEsai');
    const fd   = new FormData(form);
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    Swal.fire({ title: 'Menyimpan Penilaian...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

    fetch(`<?= site_url("backend/ujianMdta/jadwal/simpanPenilaianEsai/") ?>${currentKoreksiIdSesi}`, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: '🎉 Penilaian Tersimpan!',
                text: res.message,
                timer: 2000,
                showConfirmButton: false
            });
            if (window.jQuery && $('#modalKoreksiEsai').length) {
                $('#modalKoreksiEsai').modal('hide');
            }
            fetchMonitorData();
        } else {
            Swal.fire('Gagal!', res.message || 'Gagal menyimpan penilaian.', 'error');
        }
    })
    .catch(e => {
        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
    });
}

// Dynamic Auto-Sync Interval Manager
let syncIntervalSeconds = 5;
let syncTimer = null;
let countdownTimer = null;
let currentCountdown = 5;

function selectSyncInterval(val, labelText) {
    const labelEl = document.getElementById('syncIntervalLabel');
    if (labelEl) labelEl.textContent = labelText;
    changeSyncInterval(val);
}

function changeSyncInterval(val) {
    syncIntervalSeconds = parseInt(val, 10);
    if (syncTimer) clearInterval(syncTimer);
    if (countdownTimer) clearInterval(countdownTimer);

    const text = document.getElementById('syncCountdownText');
    const btn  = document.getElementById('syncCountdownBtn');

    if (!text || !btn) return;

    if (syncIntervalSeconds === 0) {
        text.innerHTML = 'Off';
        btn.className = 'btn btn-outline-secondary text-muted fw-bold';
    } else {
        btn.className = 'btn btn-outline-secondary text-dark fw-mono';
        currentCountdown = syncIntervalSeconds;
        text.textContent = `${currentCountdown}s`;

        countdownTimer = setInterval(() => {
            currentCountdown--;
            if (currentCountdown <= 0) {
                currentCountdown = syncIntervalSeconds;
            }
            text.textContent = `${currentCountdown}s`;
        }, 1000);

        syncTimer = setInterval(() => {
            fetchMonitorData();
        }, syncIntervalSeconds * 1000);
    }
}

function triggerManualSync() {
    fetchMonitorData();
    if (syncIntervalSeconds > 0) {
        currentCountdown = syncIntervalSeconds;
        const text = document.getElementById('syncCountdownText');
        if (text) text.textContent = `${currentCountdown}s`;
    }
}

// Initial render & live sync interval
document.addEventListener('DOMContentLoaded', function () {
    renderMonitorTable(initialSesiList);
    fetchMonitorData();
    changeSyncInterval(5); // Start with default 5 seconds
});
</script>

<!-- Modal Koreksi Jawaban Esai -->
<div class="modal fade" id="modalKoreksiEsai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fas fa-pen-fancy me-2"></i> Penilaian & Koreksi Jawaban Esai / Uraian
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPenilaianEsai" onsubmit="submitPenilaianEsai(event)">
                <input type="hidden" id="koreksi_idSesi" name="idSesi" value="">
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                        <div>
                            <span class="text-muted small d-block">Nama Santri:</span>
                            <h5 class="fw-bold text-dark mb-0" id="koreksi_namaSantri">-</h5>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Nilai Akhir Sesi:</span>
                            <span class="badge bg-success fs-6 fw-bold" id="koreksi_nilaiAkhir">0.00</span>
                        </div>
                    </div>

                    <div id="containerKoreksiEsai">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i> Memuat jawaban esai santri...
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-semibold" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Penilaian Esai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
