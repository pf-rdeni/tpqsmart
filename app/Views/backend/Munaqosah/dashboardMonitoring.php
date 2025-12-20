<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" id="cardMainDashboard">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title">Dashboard Monitoring Munaqosah</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="btnRestoreCards" title="Restore Semua Card" style="display: none;"> Refresh Semua Card
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-wrap align-items-center ml-auto">
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>" readonly>
                            </div>
                            <?php if (empty($session_id_tpq) && (!isset($is_juri) || !$is_juri) && empty($is_panitia_tpq ?? false)): ?>
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
                            <?php elseif (!empty($is_panitia_tpq ?? false)): ?>
                                <!-- Jika panitia TPQ, tampilkan TPQ mereka dan disable -->
                                <div class="mr-2 mb-1">
                                    <label class="mb-0 small">TPQ</label>
                                    <input type="hidden" id="filterTpq" value="<?= esc($selected_tpq) ?>">
                                    <select class="form-control form-control-sm" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                        <?php if (!empty($tpqDropdown)) : foreach ($tpqDropdown as $tpq): ?>
                                                <option value="<?= esc($tpq['IdTpq']) ?>" <?= ($selected_tpq == $tpq['IdTpq']) ? 'selected' : '' ?>><?= esc($tpq['NamaTpq']) ?></option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="mr-2 mb-1">
                                <label class="mb-0 small">Type</label>
                                <select id="filterType" class="form-control form-control-sm" <?= (isset($is_juri) && $is_juri) ? 'disabled' : '' ?>>
                                    <?php if (!empty($types)) : foreach ($types as $key => $label): ?>
                                            <option value="<?= esc($key) ?>" <?= ($selected_type == $key) ? 'selected' : '' ?>><?= esc($label) ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                                <?php if (isset($is_juri) && $is_juri): ?>
                                    <input type="hidden" id="filterTypeHidden" value="<?= esc($selected_type) ?>">
                                <?php endif; ?>
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
                        <input type="hidden" id="isJuri" value="<?= (isset($is_juri) && $is_juri) ? 'true' : 'false' ?>">
                        <input type="hidden" id="isAdmin" value="<?= in_groups('Admin') ? 'true' : 'false' ?>">

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

                        <?= prayer_schedule_widget() ?>

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
                        <!-- Statistik Penilaian per Grup Materi Ruangan (Hanya untuk Admin) -->
                        <?php if (in_groups('Admin')): ?>
                            <div class="mb-4 mt-4" id="sectionGrupMateriRuangan">
                                <div class="card" id="cardGrupMateriRuangan">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Statistik Penilaian per Grup Materi (Ruangan)</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="grupMateriRuanganChart" style="max-height: 400px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Statistik Penilaian per Juri (Hanya untuk Admin) -->
                        <?php if (in_groups('Admin')): ?>
                            <div class="mb-4 mt-4" id="sectionJuri">
                                <div class="card" id="cardJuri">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Statistik Penilaian per Juri</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="juriStatistikChart" style="max-height: 400px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Progress Input Nilai per Group Materi -->
                        <div class="mb-4" id="sectionGroupMateriProgress">
                            <div class="card" id="cardGroupMateriProgress">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Progress Input Nilai per Group Materi</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="groupMateriProgressContainer">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Monitoring Antrian per Grup -->
                        <div class="mb-4 mt-4" id="sectionAntrian">
                            <div class="card" id="cardAntrian">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-tasks"></i> Monitoring Antrian per Grup Materi</h5>
                                    <div class="card-tools">
                                        <a href="<?= base_url('backend/munaqosah/antrian' . (!empty($selected_type) ? '?type=' . urlencode($selected_type) : '')) ?>" class="btn btn-sm btn-warning mr-2" title="Antrian Lengkap">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="antrianContainer">
                                        <?php if (!empty($antrianData)): ?>
                                            <div class="row">
                                                <?php foreach ($antrianData as $index => $antrian): ?>
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

                                                    $detailAntrianUrl = 'backend/munaqosah/monitoring-status-antrian?group=' . urlencode($antrian['grup']['IdGrupMateriUjian']) . ($selected_type ? '&type=' . urlencode($selected_type) : '') . ($selected_tpq ? '&tpq=' . urlencode($selected_tpq) : '');

                                                    $progressColor = $antrian['statistics']['progress'] >= 80 ? 'bg-success' : ($antrian['statistics']['progress'] >= 50 ? 'bg-warning' : 'bg-danger');

                                                    // Tentukan background color berdasarkan index (rotasi 8 warna)
                                                    $bgClass = 'card-group-bg-' . (($index % 8) + 1);
                                                    ?>
                                                    <div class="col-md-6 col-lg-4 mb-3">
                                                        <div class="card <?= $bgClass ?>">
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <h6 class="card-title mb-0">
                                                                        <i class="fas fa-layer-group"></i> <?= esc($antrian['grup']['NamaMateriGrup']) ?>
                                                                        <small class="text-muted d-block"><i class="fas fa-tag"></i> <?= esc($antrian['grup']['IdGrupMateriUjian']) ?></small>
                                                                    </h6>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="<?= base_url($inputAntrianUrl) ?>" class="btn btn-success" title="Input Antrian">
                                                                            <i class="fas fa-plus"></i>
                                                                        </a>
                                                                        <a href="<?= base_url($detailAntrianUrl) ?>" class="btn btn-warning" title="Lihat Detail Antrian">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="small">Progress Antrian</span>
                                                                        <span class="small font-weight-bold"><?= $antrian['statistics']['progress'] ?>%</span>
                                                                    </div>
                                                                    <div class="progress" style="height: 20px;">
                                                                        <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $antrian['statistics']['progress'] ?>%">
                                                                            <?= $antrian['statistics']['progress'] ?>%
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row text-center mt-2 mb-2">
                                                                    <div class="col-3">
                                                                        <small class="text-muted d-block">Total</small>
                                                                        <strong><?= $antrian['statistics']['total'] ?></strong>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <small class="text-muted d-block">Selesai</small>
                                                                        <strong class="text-success"><?= $antrian['statistics']['completed'] ?></strong>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <small class="text-muted d-block">Menunggu</small>
                                                                        <strong class="text-warning"><?= $antrian['statistics']['waiting'] ?></strong>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <small class="text-muted d-block">Ujian</small>
                                                                        <strong class="text-danger"><?= $antrian['statistics']['in_progress'] ?></strong>
                                                                    </div>
                                                                </div>
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
                                                                    <div class="mt-2 pt-2 border-top">
                                                                        <small class="text-muted d-block mb-1">Status Ruangan:</small>
                                                                        <div class="d-flex flex-nowrap align-items-center" style="gap: 0.5rem; overflow-x: auto;">
                                                                            <?php foreach ($antrian['rooms'] as $room): ?>
                                                                                <?php
                                                                                $isFull = $room['is_full'] ?? false;
                                                                                $isOccupied = $room['occupied'] ?? false;
                                                                                $participantCount = $room['participant_count'] ?? 0;
                                                                                $maxCapacity = $room['max_capacity'] ?? 1;
                                                                                ?>
                                                                                <span class="badge <?= $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success') ?>" style="white-space: nowrap; flex-shrink: 0;" title="Kapasitas: <?= $participantCount ?>/<?= $maxCapacity ?>">
                                                                                    <i class="fas fa-<?= $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open') ?>"></i>
                                                                                    <?= $room['RoomId'] ?><?= $maxCapacity > 1 ? ' (' . $participantCount . '/' . $maxCapacity . ')' : '' ?>
                                                                                </span>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                        <small class="text-muted">
                                                                            <?= $totalParticipants ?> peserta di <?= $occupiedCount ?> ruangan (<?= $fullCount ?> penuh)
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Belum ada data antrian untuk grup materi aktif.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Statistik Monitoring per Group Peserta (Hanya untuk Admin atau Panitia Umum) -->
                        <?php if (in_groups('Admin') || (in_groups('Panitia') && empty($is_panitia_tpq ?? false))): ?>
                            <div class="mb-4 mt-4" id="sectionGroupPeserta">
                                <div class="card" id="cardGroupPeserta">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-users-cog"></i> Statistik Monitoring per Group Peserta</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="groupPesertaContainer">
                                            <?php if (!empty($statistikGroupPeserta)): ?>
                                                <div class="row">
                                                    <?php foreach ($statistikGroupPeserta as $index => $stat): ?>
                                                        <?php
                                                        $progressPct = $stat['total_peserta'] > 0 ? round(($stat['total_dinilai'] / $stat['total_peserta']) * 100) : 0;
                                                        $progressColor = $progressPct >= 80 ? 'bg-success' : ($progressPct >= 50 ? 'bg-warning' : 'bg-danger');

                                                        // Tentukan background color berdasarkan index (rotasi 8 warna)
                                                        $bgClass = 'card-group-bg-' . (($index % 8) + 1);
                                                        ?>
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="card <?= $bgClass ?>">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">
                                                                            <i class="fas fa-building"></i> <?= esc($stat['NamaTpq']) ?>
                                                                            <small class="text-muted d-block">
                                                                                <i class="fas fa-layer-group"></i> <?= esc($stat['GroupPeserta']) ?>
                                                                            </small>
                                                                        </h6>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <div class="d-flex justify-content-between mb-1">
                                                                            <span class="small">Progress Penilaian</span>
                                                                            <span class="small font-weight-bold"><?= $progressPct ?>%</span>
                                                                        </div>
                                                                        <div class="progress" style="height: 20px;">
                                                                            <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $progressPct ?>%">
                                                                                <?= $progressPct ?>%
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row text-center mt-2 mb-2">
                                                                        <div class="col-3">
                                                                            <small class="text-muted d-block">Total</small>
                                                                            <strong><?= $stat['total_peserta'] ?></strong>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted d-block">Sudah</small>
                                                                            <strong class="text-success"><?= $stat['total_dinilai'] ?></strong>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted d-block">Belum</small>
                                                                            <strong class="text-warning"><?= $stat['total_belum'] ?></strong>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted d-block">Selesai</small>
                                                                            <strong class="text-primary"><?= $stat['total_selesai'] ?></strong>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($stat['total_selesai'] > 0): ?>
                                                                        <div class="mt-2 pt-2 border-top">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-info-circle"></i> <?= $stat['total_selesai'] ?> peserta sudah selesai dinilai untuk semua grup materi
                                                                            </small>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i> Belum ada data statistik untuk Group Peserta.
                                                </div>
                                            <?php endif; ?>
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

    /* Background soft untuk card group - Gradient yang berbeda untuk setiap group */
    .card-group-bg-1 {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 3px solid #6c757d;
    }

    .card-group-bg-2 {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        border-left: 3px solid #dc3545;
    }

    .card-group-bg-3 {
        background: linear-gradient(135deg, #f0f8ff 0%, #e0f0ff 100%);
        border-left: 3px solid #007bff;
    }

    .card-group-bg-4 {
        background: linear-gradient(135deg, #f5fff0 0%, #e8ffe8 100%);
        border-left: 3px solid #28a745;
    }

    .card-group-bg-5 {
        background: linear-gradient(135deg, #fff8f0 0%, #ffe8e0 100%);
        border-left: 3px solid #ffc107;
    }

    .card-group-bg-6 {
        background: linear-gradient(135deg, #f8f0ff 0%, #f0e0ff 100%);
        border-left: 3px solid #6f42c1;
    }

    .card-group-bg-7 {
        background: linear-gradient(135deg, #f0fff8 0%, #e0ffe8 100%);
        border-left: 3px solid #20c997;
    }

    .card-group-bg-8 {
        background: linear-gradient(135deg, #fff0f8 0%, #ffe0f0 100%);
        border-left: 3px solid #e91e63;
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
        $('#countdownTimer').text(formatTime(remainingSeconds));

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
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        const intervalMs = intervalSeconds * 1000;

        // Mulai countdown
        startCountdown(intervalSeconds);

        autoRefreshInterval = setInterval(function() {
            // Reset countdown setiap kali refresh dimulai
            startCountdown(intervalSeconds);
            // Load data monitoring
            loadMonitoring();
        }, intervalMs);
    }

    function getRefreshInterval() {
        return parseInt($('#filterRefreshInterval').val()) || 10;
    }

    function loadMonitoring() {
        const th = $('#filterTahunAjaran').val().trim();
        const isJuri = $('#isJuri').val() === 'true';
        // Jika Juri, gunakan nilai dari hidden input
        const ty = isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah');
        const sessionIdTpq = '<?= $session_id_tpq ?? '' ?>';

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, gunakan IdTpq dari session
        let tpq = '0';
        if ((sessionIdTpq && sessionIdTpq !== '') || isJuri) {
            tpq = sessionIdTpq || '0';
        } else {
            tpq = $('#filterTpq').val() || '0';
        }

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

                    // Cek apakah semua juri (sesuai maxJuri) sudah memberikan nilai
                    // Setiap juri harus memiliki nilai > 0
                    let allJuriHasValue = true;
                    for (let i = 0; i < maxJuri; i++) {
                        const nilaiJuri = (sc[i] || 0);
                        if (nilaiJuri <= 0) {
                            allJuriHasValue = false;
                            break;
                        }
                    }

                    // Jika kategori ini tidak memiliki nilai dari semua juri, maka belum selesai
                    if (!allJuriHasValue) {
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

            // Load statistik Group Peserta (hanya untuk Admin atau Panitia Umum)
            const isAdmin = '<?= in_groups("Admin") ? "true" : "false" ?>' === 'true';
            const isPanitiaUmum = '<?= (in_groups("Panitia") && empty($is_panitia_tpq ?? false)) ? "true" : "false" ?>' === 'true';
            if (isAdmin || isPanitiaUmum) {
                loadStatistikGroupPeserta();
            }

            // Load statistik per Group Materi
            loadStatistikGroupMateri();

            // Load statistik Penilaian hanya untuk Admin
            const isAdminChart = $('#isAdmin').val() === 'true';
            if (isAdminChart) {
                loadStatistikPenilaianPerJuri();
                loadStatistikPenilaianPerGrupMateriRuangan();
            }
        }).fail(function() {
            console.error('Error koneksi saat memuat data monitoring');
        });
    }

    function loadStatistikGroupMateri() {
        const th = $('#filterTahunAjaran').val().trim();
        const isJuri = $('#isJuri').val() === 'true';
        // Jika Juri, gunakan nilai dari hidden input
        const ty = isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah');
        const sessionIdTpq = '<?= $session_id_tpq ?? '' ?>';

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, gunakan IdTpq dari session
        let tpq = '0';
        if ((sessionIdTpq && sessionIdTpq !== '') || isJuri) {
            tpq = sessionIdTpq || '0';
        } else {
            tpq = $('#filterTpq').val() || '0';
        }

        const url = '<?= base_url("backend/munaqosah/get-statistik-per-group-materi") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat statistik Group Materi:', resp.message);
                $('#groupMateriProgressContainer').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data statistik Group Materi.</div>');
                return;
            }

            const data = resp.data || [];
            const container = $('#groupMateriProgressContainer');

            if (data.length === 0) {
                container.html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada data statistik untuk Group Materi.</div>');
                return;
            }

            let html = '<div class="row">';
            data.forEach(function(stat, index) {
                const progressColor = stat.persentase >= 80 ? 'bg-success' : (stat.persentase >= 50 ? 'bg-warning' : 'bg-danger');
                // Tentukan background color berdasarkan index (rotasi 8 warna)
                const bgClass = 'card-group-bg-' + ((index % 8) + 1);

                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card ${bgClass}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-layer-group"></i> ${stat.NamaMateriGrup}
                                        <small class="text-muted d-block"><i class="fas fa-tag"></i> ${stat.IdGrupMateriUjian}</small>
                                    </h6>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Progress Input Nilai</span>
                                        <span class="small font-weight-bold">${stat.persentase}%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar ${progressColor}" role="progressbar" style="width: ${stat.persentase}%">
                                            ${stat.persentase}%
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Total</small>
                                        <strong>${stat.total_peserta}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Sudah</small>
                                        <strong class="text-success">${stat.total_dinilai}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Belum</small>
                                        <strong class="text-warning">${stat.total_belum}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.html(html);
        }).fail(function() {
            console.error('Error koneksi saat memuat statistik Group Materi');
            $('#groupMateriProgressContainer').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error koneksi saat memuat data statistik Group Materi.</div>');
        });
    }

    function loadStatistikGroupPeserta() {
        const th = $('#filterTahunAjaran').val().trim();
        const isJuri = $('#isJuri').val() === 'true';
        // Jika Juri, gunakan nilai dari hidden input
        const ty = isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah');
        const sessionIdTpq = '<?= $session_id_tpq ?? '' ?>';

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, gunakan IdTpq dari session
        let tpq = '0';
        if ((sessionIdTpq && sessionIdTpq !== '') || isJuri) {
            tpq = sessionIdTpq || '0';
        } else {
            tpq = $('#filterTpq').val() || '0';
        }

        const url = '<?= base_url("backend/munaqosah/get-statistik-group-peserta") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat statistik Group Peserta:', resp.message);
                return;
            }

            const data = resp.data || [];
            const container = $('#groupPesertaContainer');

            if (data.length === 0) {
                container.html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada data statistik untuk Group Peserta.</div>');
                return;
            }

            let html = '<div class="row">';
            data.forEach(function(stat, index) {
                const progressPct = stat.total_peserta > 0 ? Math.round((stat.total_dinilai / stat.total_peserta) * 100) : 0;
                const progressColor = progressPct >= 80 ? 'bg-success' : (progressPct >= 50 ? 'bg-warning' : 'bg-danger');
                // Tentukan background color berdasarkan index (rotasi 8 warna)
                const bgClass = 'card-group-bg-' + ((index % 8) + 1);

                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card ${bgClass}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-building"></i> ${stat.NamaTpq}
                                        <small class="text-muted d-block">
                                            <i class="fas fa-layer-group"></i> ${stat.GroupPeserta}
                                        </small>
                                    </h6>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Progress Penilaian</span>
                                        <span class="small font-weight-bold">${progressPct}%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar ${progressColor}" role="progressbar" style="width: ${progressPct}%">
                                            ${progressPct}%
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2 mb-2">
                                    <div class="col-3">
                                        <small class="text-muted d-block">Total</small>
                                        <strong>${stat.total_peserta}</strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Sudah</small>
                                        <strong class="text-success">${stat.total_dinilai}</strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Belum</small>
                                        <strong class="text-warning">${stat.total_belum}</strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Selesai</small>
                                        <strong class="text-primary">${stat.total_selesai}</strong>
                                    </div>
                                </div>
                                ${stat.total_selesai > 0 ? `
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> ${stat.total_selesai} peserta sudah selesai dinilai untuk semua grup materi
                                    </small>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.html(html);
        }).fail(function() {
            console.error('Error koneksi saat memuat statistik Group Peserta');
        });
    }

    let grupMateriRuanganChart = null;
    let juriChart = null;

    function loadStatistikPenilaianPerGrupMateriRuangan() {
        const th = $('#filterTahunAjaran').val().trim();
        const isJuri = $('#isJuri').val() === 'true';
        // Jika Juri, gunakan nilai dari hidden input
        const ty = isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah');
        const sessionIdTpq = '<?= $session_id_tpq ?? '' ?>';

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, gunakan IdTpq dari session
        let tpq = '0';
        if ((sessionIdTpq && sessionIdTpq !== '') || isJuri) {
            tpq = sessionIdTpq || '0';
        } else {
            tpq = $('#filterTpq').val() || '0';
        }

        const url = '<?= base_url("backend/munaqosah/get-statistik-penilaian-per-grup-materi-ruangan") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat statistik Penilaian per Grup Materi Ruangan:', resp.message);
                return;
            }

            const data = resp.data || [];

            if (data.length === 0) {
                if (grupMateriRuanganChart) {
                    grupMateriRuanganChart.destroy();
                    grupMateriRuanganChart = null;
                }
                $('#grupMateriRuanganChart').parent().html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada data statistik penilaian untuk Grup Materi berdasarkan Ruangan.</div>');
                return;
            }

            // Prepare data untuk chart
            const labels = [];
            const values = [];
            const backgroundColors = [];
            const borderColors = [];

            // Generate colors untuk setiap bar
            const colorPalette = [
                'rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)', 'rgba(83, 102, 255, 0.8)', 'rgba(255, 99, 255, 0.8)',
                'rgba(99, 255, 132, 0.8)', 'rgba(255, 132, 99, 0.8)', 'rgba(132, 99, 255, 0.8)'
            ];

            data.forEach(function(stat, index) {
                // Format label: ROOM-1 (baris pertama) dan BACA QURAN (baris kedua)
                const roomId = stat.RoomId || 'N/A';
                const namaGrup = stat.NamaMateriGrup || stat.IdGrupMateriUjian || 'N/A';
                const label = `${roomId}\n${namaGrup}`;
                labels.push(label);
                values.push(parseInt(stat.total_input) || 0);

                const colorIndex = index % colorPalette.length;
                backgroundColors.push(colorPalette[colorIndex]);
                borderColors.push(colorPalette[colorIndex].replace('0.8', '1'));
            });

            // Destroy chart sebelumnya jika ada
            if (grupMateriRuanganChart) {
                grupMateriRuanganChart.destroy();
            }

            // Create chart
            const ctx = document.getElementById('grupMateriRuanganChart').getContext('2d');
            grupMateriRuanganChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Nilai Input',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Nilai Input'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Grup Materi (Ruangan)'
                            },
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const dataIndex = context.dataIndex;
                                    const stat = data[dataIndex];
                                    return `Ruangan: ${stat.RoomId || 'N/A'}`;
                                }
                            }
                        }
                    }
                }
            });
        }).fail(function() {
            console.error('Error koneksi saat memuat statistik Penilaian per Grup Materi Ruangan');
            if (grupMateriRuanganChart) {
                grupMateriRuanganChart.destroy();
                grupMateriRuanganChart = null;
            }
        });
    }

    function loadStatistikPenilaianPerJuri() {
        const th = $('#filterTahunAjaran').val().trim();
        const isJuri = $('#isJuri').val() === 'true';
        // Jika Juri, gunakan nilai dari hidden input
        const ty = isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah');
        const sessionIdTpq = '<?= $session_id_tpq ?? '' ?>';

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, gunakan IdTpq dari session
        let tpq = '0';
        if ((sessionIdTpq && sessionIdTpq !== '') || isJuri) {
            tpq = sessionIdTpq || '0';
        } else {
            tpq = $('#filterTpq').val() || '0';
        }

        const url = '<?= base_url("backend/munaqosah/get-statistik-penilaian-per-juri") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat statistik Penilaian per Juri:', resp.message);
                return;
            }

            const data = resp.data || [];

            if (data.length === 0) {
                if (juriChart) {
                    juriChart.destroy();
                    juriChart = null;
                }
                $('#juriStatistikChart').parent().html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada data statistik penilaian untuk Juri.</div>');
                return;
            }

            // Prepare data untuk chart
            const labels = [];
            const values = [];
            const backgroundColors = [];
            const borderColors = [];

            // Generate colors untuk setiap bar
            const colorPalette = [
                'rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)', 'rgba(83, 102, 255, 0.8)', 'rgba(255, 99, 255, 0.8)',
                'rgba(99, 255, 132, 0.8)', 'rgba(255, 132, 99, 0.8)', 'rgba(132, 99, 255, 0.8)'
            ];

            data.forEach(function(stat, index) {
                // Format label: UsernameJuri saja (nama grup sudah diketahui dari username)
                const label = stat.UsernameJuri;
                labels.push(label);
                values.push(parseInt(stat.total_input) || 0);

                const colorIndex = index % colorPalette.length;
                backgroundColors.push(colorPalette[colorIndex]);
                borderColors.push(colorPalette[colorIndex].replace('0.8', '1'));
            });

            // Destroy chart sebelumnya jika ada
            if (juriChart) {
                juriChart.destroy();
            }

            // Create chart
            const ctx = document.getElementById('juriStatistikChart').getContext('2d');
            juriChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Input Nilai',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Input Nilai'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Username Juri'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const dataIndex = context.dataIndex;
                                    const stat = data[dataIndex];
                                    return `Grup: ${stat.NamaMateriGrup || stat.IdGrupMateriUjian}`;
                                }
                            }
                        }
                    }
                }
            });
        }).fail(function() {
            console.error('Error koneksi saat memuat statistik Penilaian per Juri');
            if (juriChart) {
                juriChart.destroy();
                juriChart = null;
            }
        });
    }

    $(function() {
        // Reset tampilan card saat load pertama kali
        const isFirstLoad = !localStorage.getItem('dashboardCardsInitialized');
        const isAdminUser = $('#isAdmin').val() === 'true';

        if (isFirstLoad) {
            // Clear semua state card dari localStorage
            localStorage.removeItem('cardMainDashboard');
            localStorage.removeItem('cardGroupMateriProgress');
            localStorage.removeItem('cardAntrian');

            // Hanya clear card Admin jika user adalah Admin
            if (isAdminUser) {
                localStorage.removeItem('cardGrupMateriRuangan');
                localStorage.removeItem('cardJuri');
                localStorage.removeItem('cardGroupPeserta');
            }

            // Restore semua card yang collapsed
            $('.card.collapsed-card').each(function() {
                $(this).removeClass('collapsed-card');
                $(this).find('.card-body, .card-footer').slideDown(0).show();
                $(this).find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
            });

            // Show semua section yang mungkin sudah di-hide (jika ada card yang di-remove)
            $('#sectionGroupMateriProgress, #sectionAntrian').show();

            // Hanya show section Admin jika user adalah Admin
            if (isAdminUser) {
                $('#sectionGrupMateriRuangan, #sectionJuri, #sectionGroupPeserta').show();
            }

            // Restore semua card yang mungkin sudah di-remove
            $('#cardMainDashboard, #cardGroupMateriProgress, #cardAntrian').show();

            // Hanya restore card Admin jika user adalah Admin
            if (isAdminUser) {
                $('#cardGrupMateriRuangan, #cardJuri, #cardGroupPeserta').show();
            }

            localStorage.setItem('dashboardCardsInitialized', 'true');
        } else {
            // Restore state dari localStorage jika bukan load pertama
            let cardIds = ['cardMainDashboard', 'cardGroupMateriProgress', 'cardAntrian'];

            // Tambahkan card Admin jika user adalah Admin
            if (isAdminUser) {
                cardIds.push('cardGrupMateriRuangan', 'cardJuri', 'cardGroupPeserta');
            }

            cardIds.forEach(function(cardId) {
                const state = localStorage.getItem(cardId);
                if (state === 'removed') {
                    const section = $('#' + cardId).closest('[id^="section"]');
                    if (section.length) {
                        section.hide();
                    }
                }
            });
        }

        // Handle card collapse/expand events untuk menyimpan state
        $('[data-card-widget="collapse"]').on('click', function() {
            const card = $(this).closest('.card');
            const cardId = card.attr('id');
            setTimeout(function() {
                if (card.hasClass('collapsed-card')) {
                    localStorage.setItem(cardId, 'collapsed');
                } else {
                    localStorage.removeItem(cardId);
                }
            }, 300);
        });

        // Handle card remove events - prevent default dan handle secara custom
        $('[data-card-widget="remove"]').on('click', function(e) {
            e.preventDefault();
            const card = $(this).closest('.card');
            const section = card.closest('[id^="section"]');
            const cardId = card.attr('id');

            // Simpan state removed
            localStorage.setItem(cardId, 'removed');

            // Hide section dengan animasi
            if (section.length) {
                section.slideUp(300, function() {
                    $(this).hide();
                });
            }

            // Tampilkan tombol restore jika ada card yang di-remove
            checkAndShowRestoreButton();
        });

        // Fungsi untuk check apakah ada card yang di-remove dan tampilkan tombol restore
        function checkAndShowRestoreButton() {
            const isAdminUser = $('#isAdmin').val() === 'true';
            let cardIds = ['cardMainDashboard', 'cardGroupMateriProgress', 'cardAntrian'];

            // Tambahkan card Admin jika user adalah Admin
            if (isAdminUser) {
                cardIds.push('cardGrupMateriRuangan', 'cardJuri', 'cardGroupPeserta');
            }

            let hasRemovedCard = false;

            cardIds.forEach(function(cardId) {
                const state = localStorage.getItem(cardId);
                if (state === 'removed') {
                    hasRemovedCard = true;
                }
            });

            if (hasRemovedCard) {
                $('#btnRestoreCards').fadeIn();
            } else {
                $('#btnRestoreCards').fadeOut();
            }
        }

        // Fungsi untuk restore semua card yang di-remove
        function restoreAllCards() {
            const isAdminUser = $('#isAdmin').val() === 'true';
            let cardIds = ['cardMainDashboard', 'cardGroupMateriProgress', 'cardAntrian'];

            // Tambahkan card Admin jika user adalah Admin
            if (isAdminUser) {
                cardIds.push('cardGrupMateriRuangan', 'cardJuri', 'cardGroupPeserta');
            }

            cardIds.forEach(function(cardId) {
                const state = localStorage.getItem(cardId);
                if (state === 'removed') {
                    // Clear state removed
                    localStorage.removeItem(cardId);

                    // Show section dengan animasi
                    const section = $('#' + cardId).closest('[id^="section"]');
                    if (section.length && section.is(':hidden')) {
                        section.slideDown(300);
                    }

                    // Pastikan card visible dan expanded
                    const card = $('#' + cardId);
                    if (card.length) {
                        card.removeClass('collapsed-card');
                        card.find('.card-body, .card-footer').slideDown(0).show();
                        card.find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
                    }
                } else if (state === 'collapsed') {
                    // Expand card yang collapsed
                    const card = $('#' + cardId);
                    if (card.length && card.hasClass('collapsed-card')) {
                        card.removeClass('collapsed-card');
                        card.find('.card-body, .card-footer').slideDown(300);
                        card.find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
                        localStorage.removeItem(cardId);
                    }
                }
            });

            // Hide tombol restore
            $('#btnRestoreCards').fadeOut();

            // Tampilkan notifikasi
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Semua card telah dikembalikan',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Semua card telah dikembalikan');
            }
        }

        // Event handler untuk tombol restore
        $('#btnRestoreCards').on('click', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Restore Semua Card?',
                    text: 'Apakah Anda yakin ingin mengembalikan semua card yang sudah di-remove?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Restore',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        restoreAllCards();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin mengembalikan semua card yang sudah di-remove?')) {
                    restoreAllCards();
                }
            }
        });

        // Check dan tampilkan tombol restore saat load
        checkAndShowRestoreButton();

        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';

        // Fungsi untuk menyimpan filter ke localStorage
        function saveFiltersToLocalStorage() {
            const isJuri = $('#isJuri').val() === 'true';
            const filters = {
                tpq: $('#filterTpq').val() || '0',
                type: isJuri ? ($('#filterTypeHidden').val() || 'pra-munaqosah') : ($('#filterType').val() || 'pra-munaqosah'),
                refreshInterval: $('#filterRefreshInterval').val() || '10'
            };
            localStorage.setItem('dashboardMonitoringFilters', JSON.stringify(filters));
        }

        // Fungsi untuk memuat filter dari localStorage
        function loadFiltersFromLocalStorage() {
            try {
                const savedFilters = localStorage.getItem('dashboardMonitoringFilters');
                if (savedFilters) {
                    const filters = JSON.parse(savedFilters);

                    // Load filter TPQ (jika tidak disabled dan ada di options)
                    const $tpqSel = $('#filterTpq');
                    if ($tpqSel.length && !$tpqSel.prop('disabled')) {
                        const optionExists = $tpqSel.find('option[value="' + filters.tpq + '"]').length > 0;
                        if (optionExists && filters.tpq) {
                            $tpqSel.val(filters.tpq);
                        }
                    }

                    // Load filter Type (jika tidak disabled)
                    const isJuri = $('#isJuri').val() === 'true';
                    if (!isJuri) {
                        const $typeSel = $('#filterType');
                        if ($typeSel.length && !$typeSel.prop('disabled')) {
                            const optionExists = $typeSel.find('option[value="' + filters.type + '"]').length > 0;
                            if (optionExists && filters.type) {
                                $typeSel.val(filters.type);
                            }
                        }
                    }

                    // Load refresh interval
                    const $refreshSel = $('#filterRefreshInterval');
                    if ($refreshSel.length && filters.refreshInterval) {
                        const optionExists = $refreshSel.find('option[value="' + filters.refreshInterval + '"]').length > 0;
                        if (optionExists) {
                            $refreshSel.val(filters.refreshInterval);
                        }
                    }
                }
            } catch (e) {
                console.error('Error loading filters from localStorage:', e);
            }
        }

        // Load filter dari localStorage saat halaman dimuat (hanya jika tidak ada URL parameter)
        const urlParams = new URLSearchParams(window.location.search);
        const hasUrlParams = urlParams.has('tpq') || urlParams.has('type');

        // Jika tidak ada URL parameter, load dari localStorage
        if (!hasUrlParams) {
            loadFiltersFromLocalStorage();
        } else {
            // Jika ada URL parameter, simpan ke localStorage
            saveFiltersToLocalStorage();
        }

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
            saveFiltersToLocalStorage();
            window.location.reload();
        });

        $('#filterTpq').on('change', function() {
            saveFiltersToLocalStorage();
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
            saveFiltersToLocalStorage();
            const isJuri = $('#isJuri').val() === 'true';
            // Jika Juri, gunakan nilai dari hidden input
            const type = isJuri ? $('#filterTypeHidden').val() : $(this).val();
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
            saveFiltersToLocalStorage();
            const intervalSeconds = getRefreshInterval();
            startAutoRefresh(intervalSeconds);
        });

        // Load monitoring pertama kali
        loadMonitoring();

        // Mulai auto-refresh dengan interval default setelah load pertama selesai
        const defaultInterval = getRefreshInterval();
        setTimeout(function() {
            startAutoRefresh(defaultInterval);
        }, 1000);

        // Load statistik Group Peserta pertama kali (hanya untuk Admin atau Panitia Umum)
        const isAdmin = '<?= in_groups("Admin") ? "true" : "false" ?>' === 'true';
        const isPanitiaUmum = '<?= (in_groups("Panitia") && empty($is_panitia_tpq ?? false)) ? "true" : "false" ?>' === 'true';
        if (isAdmin || isPanitiaUmum) {
            loadStatistikGroupPeserta();
        }

        // Load statistik Group Materi pertama kali
        loadStatistikGroupMateri();

        // Load statistik Penilaian hanya untuk Admin
        const isAdminChart = $('#isAdmin').val() === 'true';
        if (isAdminChart) {
            loadStatistikPenilaianPerGrupMateriRuangan();
            loadStatistikPenilaianPerJuri();
        }

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
<?= prayer_schedule_js(base_url('backend/jadwal-sholat')) ?>
<?= prayer_schedule_settings_js(base_url('backend/jadwal-sholat')) ?>
<?= $this->endSection(); ?>