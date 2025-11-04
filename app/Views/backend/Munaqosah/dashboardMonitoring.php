<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title">Dashboard Monitoring Munaqosah</h3>
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>" readonly>
                            </div>
                            <?php if (empty($session_id_tpq)): ?>
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">TPQ</label>
                                <select id="filterTpq" class="form-control form-control-sm">
                                    <option value="0">Semua TPQ</option>
                                    <?php if (!empty($tpqDropdown)) : foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= esc($tpq['IdTpq']) ?>" <?= ($selected_tpq == $tpq['IdTpq']) ? 'selected' : '' ?>><?= esc($tpq['NamaTpq']) ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">Type</label>
                                <select id="filterType" class="form-control form-control-sm">
                                    <?php if (!empty($types)) : foreach ($types as $key => $label): ?>
                                            <option value="<?= esc($key) ?>" <?= ($selected_type == $key) ? 'selected' : '' ?>><?= esc($label) ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">Refresh Interval</label>
                                <select id="filterRefreshInterval" class="form-control form-control-sm">
                                    <option value="5">5 Detik</option>
                                    <option value="10" selected>10 Detik</option>
                                    <option value="15">15 Detik</option>
                                    <option value="30">30 Detik</option>
                                    <option value="60">1 Menit</option>
                                </select>
                            </div>
                            <div class="align-self-end mb-1">
                                <button id="btnReload" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                            <div class="align-self-end ml-2 mb-1 d-flex align-items-center">
                                <span class="text-muted mr-1"><i class="fas fa-clock"></i></span>
                                <span class="text-muted mr-1">Refresh:</span>
                                <span id="countdownTimer" class="font-weight-bold text-primary" style="font-size: 1rem; min-width: 55px; display: inline-block;">--:--</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Hidden input untuk role user -->
                        <input type="hidden" id="userRole" value="<?= (in_groups('Operator') || (!in_groups('Admin') && session()->get('IdTpq'))) ? 'operator' : 'admin' ?>">

                        <!-- Statistik Monitoring Munaqosah -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0"><i class="fas fa-chart-line"></i> Statistik Monitoring Munaqosah</h4>
                            <?php 
                            $monitoringLengkapUrl = 'backend/munaqosah/monitoring';
                            $monitoringParams = [];
                            if (!empty($selected_type)) {
                                $monitoringParams[] = 'type=' . urlencode($selected_type);
                            }
                            if (!empty($selected_tpq)) {
                                $monitoringParams[] = 'tpq=' . urlencode($selected_tpq);
                            }
                            if (!empty($monitoringParams)) {
                                $monitoringLengkapUrl .= '?' . implode('&', $monitoringParams);
                            }
                            ?>
                            <a href="<?= base_url($monitoringLengkapUrl) ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-chart-bar"></i> Monitoring Lengkap
                            </a>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Peserta</span>
                                        <span class="info-box-number" id="statTotalPeserta">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:100%"></div>
                                        </div>
                                        <span class="progress-description">Terregistrasi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sudah Dinilai</span>
                                        <span class="info-box-number" id="statSudah">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barSudah" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descSudah">0% selesai</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Belum Dinilai</span>
                                        <span class="info-box-number" id="statBelum">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barBelum" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descBelum">0% pending</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Progress</span>
                                        <span class="info-box-number" id="statProgress">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barProgress" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description">Tingkat penyelesaian</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monitoring Antrian per Grup -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0"><i class="fas fa-tasks"></i> Monitoring Antrian per Grup Materi</h4>
                            <?php 
                            $antrianLengkapUrl = 'backend/munaqosah/antrian';
                            $antrianParams = [];
                            if (!empty($selected_type)) {
                                $antrianParams[] = 'type=' . urlencode($selected_type);
                            }
                            if (!empty($antrianParams)) {
                                $antrianLengkapUrl .= '?' . implode('&', $antrianParams);
                            }
                            ?>
                            <a href="<?= base_url($antrianLengkapUrl) ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-list"></i> Antrian Lengkap
                            </a>
                        </div>
                        <div id="antrianContainer">
                            <?php if (!empty($antrianData)): ?>
                                <?php foreach ($antrianData as $antrian): ?>
                                    <div class="card mb-3">
                                        <div class="card-header bg-secondary d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0 flex-grow-1">
                                                <i class="fas fa-layer-group"></i> <?= esc($antrian['grup']['NamaMateriGrup']) ?>
                                                <small class="text-muted">(<?= esc($antrian['grup']['IdGrupMateriUjian']) ?>)</small>
                                            </h5>
                                            <div class="d-flex align-items-center">
                                                <?php 
                                                $inputAntrianUrl = 'backend/munaqosah/input-registrasi-antrian';
                                                $inputAntrianParams = [];
                                                $inputAntrianParams[] = 'tahun=' . urlencode($current_tahun_ajaran);
                                                if (!empty($selected_type)) {
                                                    $inputAntrianParams[] = 'type=' . urlencode($selected_type);
                                                }
                                                $inputAntrianParams[] = 'group=' . urlencode($antrian['grup']['IdGrupMateriUjian']);
                                                if (!empty($selected_tpq)) {
                                                    $inputAntrianParams[] = 'tpq=' . urlencode($selected_tpq);
                                                }
                                                $inputAntrianUrl .= '?' . implode('&', $inputAntrianParams);
                                                ?>
                                                <a href="<?= base_url($inputAntrianUrl) ?>" 
                                                   class="btn btn-sm btn-success mr-2 flex-shrink-0">
                                                    <i class="fas fa-plus"></i> Input Antrian
                                                </a>
                                                <a href="<?= base_url('backend/munaqosah/monitoring-status-antrian?group=' . urlencode($antrian['grup']['IdGrupMateriUjian']) . ($selected_type ? '&type=' . urlencode($selected_type) : '') . ($selected_tpq ? '&tpq=' . urlencode($selected_tpq) : '')) ?>" 
                                                   class="btn btn-sm btn-warning flex-shrink-0">
                                                    <i class="fas fa-eye"></i> Lihat Detail Antrian
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Statistik Antrian -->
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="info-box bg-info">
                                                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Total</span>
                                                                    <span class="info-box-number"><?= $antrian['statistics']['total'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="info-box bg-success">
                                                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Selesai</span>
                                                                    <span class="info-box-number"><?= $antrian['statistics']['completed'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="info-box bg-warning">
                                                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Menunggu</span>
                                                                    <span class="info-box-number"><?= $antrian['statistics']['waiting'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="info-box bg-danger">
                                                                <span class="info-box-icon"><i class="fas fa-spinner"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Sedang Ujian</span>
                                                                    <span class="info-box-number"><?= $antrian['statistics']['in_progress'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <strong>Progress: </strong>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $antrian['statistics']['progress'] ?>%">
                                                                <?= $antrian['statistics']['progress'] ?>%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status Ruangan -->
                                                <div class="col-md-4">
                                                    <strong>Status Ruangan:</strong>
                                                    <div class="mt-2">
                                                        <?php if (!empty($antrian['rooms'])): ?>
                                                            <?php 
                                                            $occupiedCount = 0;
                                                            $fullCount = 0;
                                                            $totalParticipants = 0;
                                                            foreach ($antrian['rooms'] as $room) {
                                                                if ($room['occupied'] ?? false) $occupiedCount++;
                                                                if ($room['is_full'] ?? false) $fullCount++;
                                                                $totalParticipants += ($room['participant_count'] ?? 0);
                                                            }
                                                            ?>
                                                            <div class="d-flex flex-wrap">
                                                                <?php foreach ($antrian['rooms'] as $room): ?>
                                                                    <?php 
                                                                    $isFull = $room['is_full'] ?? false;
                                                                    $isOccupied = $room['occupied'] ?? false;
                                                                    $participantCount = $room['participant_count'] ?? 0;
                                                                    $maxCapacity = $room['max_capacity'] ?? 1;
                                                                    ?>
                                                                    <span class="badge <?= $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success') ?> mr-1 mb-1" title="Kapasitas: <?= $participantCount ?>/<?= $maxCapacity ?>">
                                                                        <i class="fas fa-<?= $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open') ?>"></i> 
                                                                        R<?= $room['RoomId'] ?> (<?= $participantCount ?>/<?= $maxCapacity ?>)
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <small class="text-muted">
                                                                <?= $totalParticipants ?> peserta di <?= $occupiedCount ?> ruangan (<?= $fullCount ?> penuh)
                                                            </small>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Tidak ada ruangan</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Belum ada data antrian untuk grup materi aktif.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .info-box {
        min-height: 80px;
    }
    .info-box-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .info-box-content {
        padding: 10px;
    }
    .info-box-number {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .info-box-text {
        font-size: 0.9rem;
    }
</style>
<script>
    let autoRefreshInterval = null;
    let countdownInterval = null;
    let remainingSeconds = 0;

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    function startCountdown(intervalSeconds) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        remainingSeconds = intervalSeconds;

        countdownInterval = setInterval(function() {
            remainingSeconds--;

            if (remainingSeconds <= 0) {
                remainingSeconds = 0;
                $('#countdownTimer').text('00:00');
                clearInterval(countdownInterval);
            } else {
                $('#countdownTimer').text(formatTime(remainingSeconds));
            }
        }, 1000);
    }

    function startAutoRefresh(intervalSeconds) {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }

        const intervalMs = intervalSeconds * 1000;

        autoRefreshInterval = setInterval(function() {
            loadMonitoring();
        }, intervalMs);

        startCountdown(intervalSeconds);
    }

    function getRefreshInterval() {
        return parseInt($('#filterRefreshInterval').val()) || 10;
    }

    function loadMonitoring() {
        const th = $('#filterTahunAjaran').val().trim();
        const tpq = $('#filterTpq').val() || '0';
        const ty = $('#filterType').val() || 'pra-munaqosah';
        const url = '<?= base_url("backend/munaqosah/monitoring-data") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat data:', resp.message);
                return;
            }
            const data = resp.data || {
                rows: []
            };

            // Hitung statistik dari data yang tampil
            const totalPeserta = data.rows.length;
            let sudah = 0;
            const headerCategories = data.categories || [];
            
            data.rows.forEach(r => {
                let doneAll = true;
                headerCategories.forEach(cat => {
                    const key = cat.id || cat.IdKategoriMateri || cat;
                    const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
                    const sc = r.nilai[key] || [];

                    let hasValue = false;
                    for (let i = 0; i < maxJuri; i++) {
                        if ((sc[i] || 0) > 0) {
                            hasValue = true;
                            break;
                        }
                    }

                    if (!hasValue) {
                        doneAll = false;
                    }
                });
                if (doneAll) sudah++;
            });
            
            const belum = totalPeserta - sudah;
            const pct = totalPeserta > 0 ? Math.round((sudah / totalPeserta) * 100) : 0;
            const pctBelum = totalPeserta > 0 ? Math.round((belum / totalPeserta) * 100) : 0;
            
            $('#statTotalPeserta').text(totalPeserta);
            $('#statSudah').text(sudah);
            $('#barSudah').css('width', pct + '%');
            $('#descSudah').text(pct + '% selesai');
            $('#statBelum').text(belum);
            $('#barBelum').css('width', pctBelum + '%');
            $('#descBelum').text(pctBelum + '% pending');
            $('#statProgress').text(pct + '%');
            $('#barProgress').css('width', pct + '%');

            // Reset countdown setelah data selesai dimuat
            if (autoRefreshInterval) {
                const intervalSeconds = getRefreshInterval();
                startCountdown(intervalSeconds);
            }
        }).fail(function() {
            console.error('Error koneksi saat memuat data monitoring');
        });
    }

    $(function() {
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';

        // Jika TPQ hanya satu, set otomatis
        const $tpqSel = $('#filterTpq');
        if ($tpqSel.length) {
            const realTpqOptions = $tpqSel.find('option').filter(function() {
                return $(this).val() !== '0';
            });
            if (realTpqOptions.length === 1) {
                const onlyId = $(realTpqOptions[0]).val();
                $tpqSel.val(onlyId).prop('disabled', true);
            }
        }

        $('#btnReload').on('click', function() {
            window.location.reload();
        });

        $('#filterTpq').on('change', function() {
            const tpq = $(this).val();
            const params = new URLSearchParams(window.location.search);
            if (tpq && tpq !== '0') {
                params.set('tpq', tpq);
            } else {
                params.delete('tpq');
            }
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        $('#filterType').on('change', function() {
            const type = $(this).val();
            const params = new URLSearchParams(window.location.search);
            if (type) {
                params.set('type', type);
            } else {
                params.delete('type');
            }
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        // Handler untuk perubahan interval refresh
        $('#filterRefreshInterval').on('change', function() {
            const intervalSeconds = getRefreshInterval();
            startAutoRefresh(intervalSeconds);
        });

        // Mulai auto-refresh dengan interval default
        const defaultInterval = getRefreshInterval();
        startAutoRefresh(defaultInterval);

        // Load monitoring pertama kali (countdown akan di-reset setelah load selesai)
        loadMonitoring();

        // Bersihkan interval saat halaman ditutup
        $(window).on('beforeunload', function() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        });
    });
</script>
<?= $this->endSection(); ?>
