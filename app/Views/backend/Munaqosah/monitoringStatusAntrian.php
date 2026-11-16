<?= $this->extend('backend/template/templateNoSidebarAndNavbar') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">


        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Monitoring Status Antrian</h3>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="d-flex align-items-center mr-2 mb-2 mb-md-0">
                                <select id="autoRefreshInterval" class="form-control form-control-sm mr-2" style="width: auto; min-width: 80px;" title="Pilih Interval Auto Refresh">
                                    <option value="3">3 detik</option>
                                    <option value="5">5 detik</option>
                                    <option value="10">10 detik</option>
                                    <option value="15">15 detik</option>
                                    <option value="30" selected>30 detik</option>
                                    <option value="60">1 menit</option>
                                </select>
                                <span id="autoRefreshStatus" class="badge badge-info mr-2" style="display: none;">
                                    <i class="fas fa-sync-alt fa-spin"></i> Auto Refresh: <span id="autoRefreshCountdown">30</span>s
                                </span>
                                <button type="button" id="btnToggleAutoRefresh" class="btn btn-sm btn-outline-primary" title="Toggle Auto Refresh">
                                    <i class="fas fa-sync-alt"></i> <span id="autoRefreshText">Aktifkan Auto Refresh</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Grup Materi Ujian:</strong>
                            <?php
                            $selectedGroupName = '-';
                            foreach ($groups as $group) {
                                if ($group['IdGrupMateriUjian'] === $selected_group) {
                                    $selectedGroupName = $group['NamaMateriGrup'];
                                    break;
                                }
                            }
                            echo $selectedGroupName;
                            ?>
                            <?php if (empty($session_id_tpq) && empty($is_panitia_tpq ?? false)): ?>
                                | <strong>TPQ:</strong>
                                <?php
                                $selectedTpqName = 'Semua TPQ';
                                foreach ($tpq_list as $tpq) {
                                    $tpqId = is_array($tpq) ? $tpq['IdTpq'] : $tpq->IdTpq;
                                    $tpqNama = is_array($tpq) ? ($tpq['NamaTpq'] ?? '') : ($tpq->NamaTpq ?? '');
                                    if ($tpqId === $selected_tpq) {
                                        $selectedTpqName = $tpqId . ' - ' . $tpqNama;
                                        break;
                                    }
                                }
                                echo $selectedTpqName;
                                ?>
                            <?php elseif (!empty($is_panitia_tpq ?? false)): ?>
                                | <strong>TPQ:</strong>
                                <?php
                                $selectedTpqName = '-';
                                foreach ($tpq_list as $tpq) {
                                    $tpqId = is_array($tpq) ? $tpq['IdTpq'] : $tpq->IdTpq;
                                    $tpqNama = is_array($tpq) ? ($tpq['NamaTpq'] ?? '') : ($tpq->NamaTpq ?? '');
                                    if ($tpqId === $selected_tpq) {
                                        $selectedTpqName = $tpqId . ' - ' . $tpqNama;
                                        break;
                                    }
                                }
                                echo $selectedTpqName;
                                ?>
                            <?php endif; ?>
                            <?php if (empty($session_id_tpq)): ?>
                                | <strong>Type Ujian:</strong> <?= $types[$selected_type] ?? $selected_type ?>
                            <?php endif; ?>
                            | <strong>Waktu Update:</strong> <span id="lastUpdate">-</span>
                        </div>

                        <!-- Card Daftar Antrian dan Info Grafis -->
                        <div class="row mb-3">
                            <?php
                            // Ambil nama grup materi yang dipilih
                            $selectedGroupName = '-';
                            foreach ($groups as $group) {
                                if ($group['IdGrupMateriUjian'] === $selected_group) {
                                    $selectedGroupName = $group['NamaMateriGrup'];
                                    break;
                                }
                            }
                            ?>
                            <div class="col-4">
                                <div class="card card-outline card-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title mb-0">Daftar Antrian</h3>
                                        <span class="badge badge-primary"><?= esc($selectedGroupName) ?></span>
                                    </div>
                                    <div class="card-body">
                                        <!-- Daftar Antrian -->
                                        <div class="table-responsive">
                                            <table id="tableAntrian" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Peserta</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableAntrianBody">
                                                    <?php
                                                    // Hitung jumlah ruangan total
                                                    $totalRooms = count($rooms);

                                                    // Counter untuk menghitung urutan antrian menunggu
                                                    $currentWaitingOrder = 0;
                                                    ?>
                                                    <?php foreach ($queue as $row): ?>
                                                        <?php
                                                        $status = (int) ($row['Status'] ?? 0);
                                                        $statusLabel = 'Menunggu';
                                                        $badgeClass = 'badge-warning';
                                                        if ($status === 1) {
                                                            $statusLabel = 'Sedang Ujian';
                                                            $badgeClass = 'badge-danger';
                                                        } elseif ($status === 2) {
                                                            $statusLabel = 'Selesai';
                                                            $badgeClass = 'badge-success';
                                                        }
                                                        $groupPeserta = $row['GroupPeserta'] ?? 'Group 1';

                                                        // Array warna Bootstrap yang bisa diulang
                                                        $groupColors = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger', 'badge-info', 'badge-dark', 'badge-secondary'];
                                                        // Ambil warna berdasarkan hash sederhana dari nama grup
                                                        $colorIndex = crc32($groupPeserta) % count($groupColors);
                                                        $groupBadgeClass = $groupColors[abs($colorIndex)];

                                                        // Tentukan warna badge No Peserta berdasarkan status dan urutan antrian
                                                        $noPesertaBadgeClass = 'badge-info'; // Default
                                                        $isTopQueue = false; // Flag untuk badge urutan teratas

                                                        if ($status === 0) {
                                                            // Status menunggu - hitung urutan dalam antrian menunggu
                                                            $currentWaitingOrder++;
                                                            if ($totalRooms > 0) {
                                                                if ($currentWaitingOrder <= $totalRooms) {
                                                                    // Urutan 1 sampai jumlah ruangan = hijau (akan segera dipanggil)
                                                                    $noPesertaBadgeClass = 'badge-success';
                                                                    // Urutan pertama (1) akan berkedip
                                                                    if ($currentWaitingOrder === 1) {
                                                                        $isTopQueue = true;
                                                                    }
                                                                } elseif ($currentWaitingOrder <= ($totalRooms * 2)) {
                                                                    // Urutan (jumlah_ruangan + 1) sampai (jumlah_ruangan * 2) = kuning (bersiap)
                                                                    $noPesertaBadgeClass = 'badge-warning';
                                                                } else {
                                                                    // Lainnya = default
                                                                    $noPesertaBadgeClass = 'badge-info';
                                                                }
                                                            }
                                                        } elseif ($status === 1) {
                                                            // Sedang ujian = merah
                                                            $noPesertaBadgeClass = 'badge-danger';
                                                        } elseif ($status === 2) {
                                                            // Selesai = abu-abu
                                                            $noPesertaBadgeClass = 'badge-secondary';
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div style="display: flex; align-items: flex-start;">
                                                                    <span class="badge <?= $noPesertaBadgeClass ?> <?= $isTopQueue ? 'badge-blink' : '' ?>" style="font-size: 2em; margin-right: 8px;"><?= $row['NoPeserta'] ?></span>
                                                                    <div>
                                                                        <div><?= $row['NamaSantri'] ?? '-' ?></div>
                                                                        <div><span class="badge <?= $groupBadgeClass ?>"><?= htmlspecialchars($groupPeserta) ?></span></div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="display: flex; flex-direction: column; align-items: center;">
                                                                    <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                                                    <?php
                                                                    // Ambil foto profil santri jika ada
                                                                    $photoUrl = null;
                                                                    $hasPhotoProfil = !empty($row['PhotoProfil']);
                                                                    if ($hasPhotoProfil) {
                                                                        $photoPath = FCPATH . 'uploads/santri/' . $row['PhotoProfil'];
                                                                        if (file_exists($photoPath)) {
                                                                            $photoUrl = base_url('uploads/santri/' . $row['PhotoProfil']);
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <?php if ($photoUrl): ?>
                                                                        <img src="<?= $photoUrl ?>" 
                                                                            alt="Foto Profil" 
                                                                            class="photo-profil-thumb"
                                                                            style="width: 45px; height: 60px; object-fit: cover; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6;"
                                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                        <div class="photo-profil-placeholder" style="width: 45px; height: 60px; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6; background-color: #e9ecef; display: none; align-items: center; justify-content: center; font-size: 9px; color: #6c757d; text-align: center; flex-shrink: 0; line-height: 1.2;">
                                                                            no profil
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div style="width: 45px; height: 60px; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #6c757d; text-align: center; flex-shrink: 0; line-height: 1.2;">
                                                                            no profil
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="card card-outline card-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="d-flex align-items-center mb-1 mb-md-0">
                                            <h3 class="card-title mb-0 mr-2">Info Grafis</h3>
                                            <span class="badge badge-primary"><?= esc($selectedGroupName) ?></span>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap" id="iklanControls">
                                            <!-- Toggle Format A/B -->
                                            <div class="btn-group btn-group-sm mr-2 mb-1" role="group" title="Format Header Strip">
                                                <button type="button" id="btnFormatA" class="btn btn-outline-secondary active" onclick="setHeaderFormat('A')">Format A</button>
                                                <button type="button" id="btnFormatB" class="btn btn-outline-secondary" onclick="setHeaderFormat('B')">Format B</button>
                                            </div>
                                            <!-- Toggle Iklan ON/OFF -->
                                            <div class="custom-control custom-switch mr-2 mb-1" title="Tampilkan / Sembunyikan Iklan">
                                                <input type="checkbox" class="custom-control-input" id="toggleIklan">
                                                <label class="custom-control-label" for="toggleIklan">Iklan</label>
                                            </div>
                                            <!-- Play Settings (hanya saat iklan ON) -->
                                            <div id="iklanPlaySettings" style="display:none;" class="d-flex align-items-center mr-2 mb-1">
                                                <select id="iklanInterval" class="form-control form-control-sm mr-1" style="width: auto; min-width: 80px;" title="Durasi per slide">
                                                    <option value="5">5 dtk</option>
                                                    <option value="10" selected>10 dtk</option>
                                                    <option value="15">15 dtk</option>
                                                    <option value="30">30 dtk</option>
                                                    <option value="60">60 dtk</option>
                                                </select>
                                                <select id="iklanMode" class="form-control form-control-sm mr-1" style="width: auto; min-width: 85px;" title="Mode putar">
                                                    <option value="loop">Loop</option>
                                                    <option value="random">Acak</option>
                                                    <option value="single">Tetap</option>
                                                </select>
                                                <select id="iklanFit" class="form-control form-control-sm mr-1" style="width: auto; min-width: 95px;" title="Penyesuaian Rasio Gambar">
                                                    <option value="contain_blur">Fit + Blur</option>
                                                    <option value="fill">Paksa Isi</option>
                                                    <option value="cover">Crop Penuh</option>
                                                    <option value="contain">Letterbox</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Hidden element untuk kompatibilitas script existing -->
                                        <div id="roomsContainer" style="display:none;"></div>
                                        <span id="barCompleted" style="display:none;"></span>
                                        <span id="barQueueing" style="display:none;"></span>
                                        <span id="descCompleted" style="display:none;"></span>
                                        <span id="descQueueing" style="display:none;"></span>

                                        <?php
                                        $total = $statistics['total'];
                                        $completed = $statistics['completed'];
                                        $queueing = $statistics['queueing'];
                                        $inProgress = $statistics['in_progress'] ?? 0;
                                        $progress = $statistics['progress'];

                                        $pctCompleted = $total > 0 ? round(($completed / max($total, 1)) * 100) : 0;
                                        $pctQueueing = $total > 0 ? round(($queueing / max($total, 1)) * 100) : 0;
                                        $pctInProgress = $total > 0 ? round(($inProgress / max($total, 1)) * 100) : 0;
                                        ?>

                                        <!-- COMPACT SUMMARY STRIP: FORMAT A (Dual Column: Progress Kiri, Ruangan Kanan) -->
                                        <div id="summaryStripA" class="mb-3">
                                            <div class="row no-gutters">
                                                <div class="col-md-6 pr-md-1 mb-2 mb-md-0">
                                                    <div class="p-2 rounded border bg-light h-100">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <small class="font-weight-bold text-muted"><i class="fas fa-chart-line mr-1"></i>Progres Ujian</small>
                                                            <small class="text-muted"><span id="statProgress"><?= $progress ?></span>% selesai</small>
                                                        </div>
                                                        <div class="progress mb-2" style="height: 10px;">
                                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="barProgress" style="width: <?= $progress ?>%" role="progressbar"></div>
                                                        </div>
                                                        <div class="d-flex justify-content-around text-center">
                                                            <div class="px-1">
                                                                <div class="text-muted" style="font-size: 0.7rem;">Total</div>
                                                                <strong id="statTotal" class="text-dark" style="font-size: 1.1rem;"><?= $total ?></strong>
                                                            </div>
                                                            <div class="px-1 border-left">
                                                                <div class="text-muted" style="font-size: 0.7rem;">Diuji</div>
                                                                <strong id="statCompleted" class="text-success" style="font-size: 1.1rem;"><?= $completed ?></strong>
                                                            </div>
                                                            <div class="px-1 border-left">
                                                                <div class="text-muted" style="font-size: 0.7rem;">Tunggu</div>
                                                                <strong id="statQueueing" class="text-warning" style="font-size: 1.1rem;"><?= $queueing ?></strong>
                                                            </div>
                                                            <div class="px-1 border-left">
                                                                <div class="text-muted" style="font-size: 0.7rem;">Ujian</div>
                                                                <strong id="statInProgress" class="text-danger" style="font-size: 1.1rem;"><?= $inProgress ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 pl-md-1">
                                                    <div class="p-2 rounded border bg-light h-100">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <small class="font-weight-bold text-muted"><i class="fas fa-door-open mr-1"></i>Status Ruangan</small>
                                                            <small class="text-muted"><?= count($rooms) ?> Ruangan</small>
                                                        </div>
                                                        <div id="roomsPillsContainer" class="d-flex flex-wrap align-items-center" style="gap: 4px; min-height: 48px;">
                                                            <?php if (!empty($rooms)): ?>
                                                                <?php foreach ($rooms as $room): ?>
                                                                    <?php
                                                                    $isFull = $room['is_full'] ?? false;
                                                                    $isOccupied = $room['occupied'] ?? false;
                                                                    $pillClass = $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success');
                                                                    $icon = $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open');
                                                                    $participants = $room['participants'] ?? [];
                                                                    $participantStr = '';
                                                                    if ($isOccupied && !empty($participants)) {
                                                                        $pNos = array_map(function($p) { return $p['NoPeserta']; }, $participants);
                                                                        $participantStr = ': ' . implode(',', $pNos);
                                                                    }
                                                                    ?>
                                                                    <span class="badge <?= $pillClass ?> p-1" style="font-size: 0.85rem;" title="Ruangan <?= $room['RoomId'] ?><?= $participantStr ?>">
                                                                        <i class="fas fa-<?= $icon ?>"></i> R<?= $room['RoomId'] ?><?= $participantStr ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <small class="text-muted">Belum ada ruangan terdaftar</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- COMPACT SUMMARY STRIP: FORMAT B (Stacked Single-Line Ribbon) -->
                                        <div id="summaryStripB" class="mb-3" style="display: none;">
                                            <div class="p-2 rounded border bg-light mb-1">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                    <div class="d-flex align-items-center flex-wrap mr-2">
                                                        <span class="badge badge-secondary mr-1 mb-1 p-1">Total: <strong id="statTotalB"><?= $total ?></strong></span>
                                                        <span class="badge badge-success mr-1 mb-1 p-1">Diuji: <strong id="statCompletedB"><?= $completed ?></strong></span>
                                                        <span class="badge badge-warning mr-1 mb-1 p-1">Tunggu: <strong id="statQueueingB"><?= $queueing ?></strong></span>
                                                        <span class="badge badge-danger mr-1 mb-1 p-1">Ujian: <strong id="statInProgressB"><?= $inProgress ?></strong></span>
                                                    </div>
                                                    <div class="flex-grow-1" style="min-width: 150px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                                            <span>Progress</span>
                                                            <span id="statProgressB"><?= $progress ?>%</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="barProgressB" style="width: <?= $progress ?>%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-2 rounded border bg-light">
                                                <div class="small font-weight-bold text-muted mb-1"><i class="fas fa-door-open mr-1"></i>Ruangan:</div>
                                                <div id="roomsRibbonContainer" class="d-flex flex-wrap align-items-center" style="gap: 4px;">
                                                    <?php if (!empty($rooms)): ?>
                                                        <?php foreach ($rooms as $room): ?>
                                                            <?php
                                                            $isFull = $room['is_full'] ?? false;
                                                            $isOccupied = $room['occupied'] ?? false;
                                                            $pillClass = $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success');
                                                            $icon = $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open');
                                                            $participants = $room['participants'] ?? [];
                                                            $participantStr = '';
                                                            if ($isOccupied && !empty($participants)) {
                                                                $pNos = array_map(function($p) { return $p['NoPeserta']; }, $participants);
                                                                $participantStr = ': ' . implode(',', $pNos);
                                                            }
                                                            ?>
                                                            <span class="badge <?= $pillClass ?> p-1" style="font-size: 0.85rem;" title="Ruangan <?= $room['RoomId'] ?><?= $participantStr ?>">
                                                                <i class="fas fa-<?= $icon ?>"></i> R<?= $room['RoomId'] ?><?= $participantStr ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <small class="text-muted">Belum ada ruangan terdaftar</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media Player Iklan (Ditukar dengan Monitoring Antrian Grup) -->
                                        <div id="iklanPlayerContainer" style="display: none;" class="mb-3">
                                            <div class="iklan-player-wrapper position-relative bg-dark rounded overflow-hidden shadow-sm" style="aspect-ratio: 16/9; width: 100%; min-height: 280px;">
                                                <div id="iklanSlideContainer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                                    <div id="iklanEmptyState" class="text-white text-center d-flex flex-column align-items-center justify-content-center" style="height: 100%;">
                                                        <i class="fas fa-images fa-3x mb-2 text-muted"></i>
                                                        <div class="text-muted">Belum ada media iklan.<br>Silakan upload media pada menu <strong>Munaqosah &gt; Kelola Iklan</strong> di sidebar.</div>
                                                    </div>
                                                </div>
                                                <div id="iklanSlideInfo" class="position-absolute" style="bottom: 10px; left: 14px; right: 14px; display: none; z-index: 10;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div id="iklanDots" class="d-flex align-items-center" style="gap: 6px;"></div>
                                                        <small class="text-white bg-dark px-2 py-1 rounded shadow" id="iklanTimerLabel" style="opacity: 0.85;">10 dtk</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Monitoring Antrian per Grup Materi -->
                                        <div class="mb-4 mt-4" id="sectionAntrian">
                                            <h5 class="mt-4 mb-3">Monitoring Antrian per Grup Materi</h5>
                                            <div id="antrianContainer">
                                                <?php if (!empty($antrianData ?? [])): ?>
                                                    <div class="row">
                                                        <?php foreach ($antrianData as $index => $antrian): ?>
                                                            <?php
                                                            $inputAntrianUrl = 'backend/munaqosah/input-registrasi-antrian';
                                                            $inputAntrianParams = [];
                                                            $inputAntrianParams[] = 'tahun=' . urlencode($current_tahun);
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
                                                                            <div class="col-3 stat-item" title="Total Peserta">
                                                                                <div class="stat-icon-wrapper">
                                                                                    <i class="fas fa-users stat-icon text-info"></i>
                                                                                    <span class="stat-label-text"><small class="text-muted">Total</small></span>
                                                                                </div>
                                                                                <strong><?= $antrian['statistics']['total'] ?></strong>
                                                                            </div>
                                                                            <div class="col-3 stat-item" title="Selesai">
                                                                                <div class="stat-icon-wrapper">
                                                                                    <i class="fas fa-check-circle stat-icon text-success"></i>
                                                                                    <span class="stat-label-text"><small class="text-muted">Selesai</small></span>
                                                                                </div>
                                                                                <strong class="text-success"><?= $antrian['statistics']['completed'] ?></strong>
                                                                            </div>
                                                                            <div class="col-3 stat-item" title="Tunggu">
                                                                                <div class="stat-icon-wrapper">
                                                                                    <i class="fas fa-clock stat-icon text-warning"></i>
                                                                                    <span class="stat-label-text"><small class="text-muted">Tunggu</small></span>
                                                                                </div>
                                                                                <strong class="text-warning"><?= $antrian['statistics']['waiting'] ?></strong>
                                                                            </div>
                                                                            <div class="col-3 stat-item" title="Sedang Ujian">
                                                                                <div class="stat-icon-wrapper">
                                                                                    <i class="fas fa-clipboard-check stat-icon text-danger"></i>
                                                                                    <span class="stat-label-text"><small class="text-muted">Ujian</small></span>
                                                                                </div>
                                                                                <strong class="text-danger"><?= $antrian['statistics']['in_progress'] ?></strong>
                                                                            </div>
                                                                        </div>
                                                                        <?php if (!empty($antrian['rooms'])): ?>
                                                                            <?php
                                                                            $occupiedCount = 0;
                                                                            $fullCount = 0;
                                                                            $totalParticipants = 0;
                                                                            $hasMultiCapacity = false;
                                                                            foreach ($antrian['rooms'] as $room) {
                                                                                if ($room['occupied'] ?? false) $occupiedCount++;
                                                                                if ($room['is_full'] ?? false) $fullCount++;
                                                                                $totalParticipants += ($room['participant_count'] ?? 0);
                                                                                if (($room['max_capacity'] ?? 1) > 1) {
                                                                                    $hasMultiCapacity = true;
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <div class="mt-2 pt-2 border-top">
                                                                                <small class="text-muted d-block mb-1">Status Ruangan:</small>
                                                                                <div class="room-badges-container">
                                                                                    <?php foreach ($antrian['rooms'] as $room): ?>
                                                                                        <?php
                                                                                        $isFull = $room['is_full'] ?? false;
                                                                                        $isOccupied = $room['occupied'] ?? false;
                                                                                        $participantCount = $room['participant_count'] ?? 0;
                                                                                        $maxCapacity = $room['max_capacity'] ?? 1;
                                                                                        ?>
                                                                                        <span class="badge room-badge <?= $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success') ?>" title="Kapasitas: <?= $participantCount ?>/<?= $maxCapacity ?>">
                                                                                            <i class="fas fa-<?= $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open') ?>"></i>
                                                                                            <?= $room['RoomId'] ?><?= $maxCapacity > 1 ? ' (' . $participantCount . '/' . $maxCapacity . ')' : '' ?>
                                                                                        </span>
                                                                                    <?php endforeach; ?>
                                                                                </div>
                                                                                <small class="text-muted">
                                                                                    <?= $totalParticipants ?> peserta di <?= $occupiedCount ?> ruangan<?= $hasMultiCapacity ? ' (' . $fullCount . ' penuh)' : '' ?>
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
                            </div>
                        </div>
                        <!-- Filter (Read Only untuk display) -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Kelola Iklan & Pengaturan Display -->
    <div class="modal fade" id="modalKelolaIklan" tabindex="-1" role="dialog" aria-labelledby="modalKelolaIklanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalKelolaIklanLabel"><i class="fas fa-images mr-1"></i> Kelola Iklan & Pengaturan Display</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="iklanModalTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tabMedia-tab" data-toggle="tab" href="#tabMedia" role="tab"><i class="fas fa-photo-video mr-1"></i>Daftar & Upload Media</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tabSetting-tab" data-toggle="tab" href="#tabSetting" role="tab"><i class="fas fa-sliders-h mr-1"></i>Pengaturan Display</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="iklanTabContent">
                        <!-- TAB 1: MEDIA -->
                        <div class="tab-pane fade show active" id="tabMedia" role="tabpanel">
                            <form id="formUploadIklan" enctype="multipart/form-data" class="p-3 bg-light rounded border mb-3">
                                <h6 class="font-weight-bold mb-2"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload Gambar Landscape / Video Iklan</h6>
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Pilih File Media (Rasio disarankan 16:9 Landscape):</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="iklanFileInput" name="iklan_file" accept=".jpg,.jpeg,.png,.webp,.mp4" required>
                                        <label class="custom-file-label" for="iklanFileInput">Pilih file gambar (landscape)...</label>
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, PNG, WebP (maks. 5MB). Format MP4 (maks. 25MB) aktif jika opsi video diaktifkan.</small>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1">Judul / Keterangan Media:</label>
                                    <input type="text" class="form-control form-control-sm" name="judul" id="iklanJudul" placeholder="Contoh: Penerimaan Santri Baru TA 2026/2027" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitUpload">
                                    <i class="fas fa-upload mr-1"></i> Unggah Media
                                </button>
                            </form>

                            <h6 class="font-weight-bold mb-2"><i class="fas fa-list mr-1"></i>Daftar Media Aktif</h6>
                            <div class="table-responsive">
                                <div id="iklanMediaList">
                                    <p class="text-muted text-center py-3"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar media...</p>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PENGATURAN -->
                        <div class="tab-pane fade" id="tabSetting" role="tabpanel">
                            <form id="formIklanSettings" class="p-3 bg-light rounded border">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Durasi Tampil per Slide</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="settingInterval" name="iklan_interval">
                                            <option value="5">5 detik</option>
                                            <option value="10" selected>10 detik (Standar)</option>
                                            <option value="15">15 detik</option>
                                            <option value="30">30 detik</option>
                                            <option value="60">1 menit</option>
                                        </select>
                                        <small class="text-muted">Berapa detik gambar ditampilkan sebelum berganti.</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Mode Putar (Playback)</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="settingMode" name="iklan_mode">
                                            <option value="loop">Loop Berurutan (Slide 1, 2, 3...)</option>
                                            <option value="random">Acak / Shuffle</option>
                                            <option value="single">Salah Satu Gambar Tetap (Static)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="settingSingleRow" style="display: none;">
                                    <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Pilih Gambar Tetap</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="settingSingleId" name="iklan_single_id">
                                            <option value="0">-- Pilih salah satu gambar --</option>
                                        </select>
                                        <small class="text-muted">Gambar yang dipilih akan terus tampil tanpa berganti.</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Dukungan Video MP4</label>
                                    <div class="col-sm-8">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="settingVideoEnabled" name="iklan_video_enabled">
                                            <label class="custom-control-label" for="settingVideoEnabled">Izinkan unggah file video (.mp4)</label>
                                        </div>
                                        <small class="text-muted">Default: Nonaktif. Aktifkan jika ingin memutar video profil lembaga.</small>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-success btn-sm" id="btnSaveSettings">
                                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan Display
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Full width untuk content saat tanpa sidebar dan navbar */
    .content-wrapper,
    .content-wrapper::before {
        margin-left: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* Full height untuk body */
    body {
        overflow-x: hidden;
    }

    .room-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }

    /* Animasi berkedip untuk badge urutan teratas */
    @keyframes badgeBlink {

        0%,
        100% {
            background-color: #28a745;
            border: 3px solid #28a745;
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }

        50% {
            background-color: #5cb85c;
            border: 3px solid #fff;
            box-shadow: 0 0 10px 3px rgba(40, 167, 69, 0.9);
        }
    }

    .badge-blink {
        animation: badgeBlink 2s ease-in-out infinite;
        border: 3px solid #28a745;
    }

    @media (max-width: 576px) {
        .room-card {
            margin-bottom: 0.75rem;
        }

        .room-card h5 {
            font-size: 1rem;
        }
    }

    /* Style untuk container badge status ruangan */
    .room-badges-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        overflow: hidden;
    }

    .room-badge {
        white-space: nowrap;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Pastikan badge tidak overflow pada container kecil */
    @media (max-width: 768px) {
        .room-badges-container {
            gap: 0.4rem;
        }

        .room-badge {
            font-size: 0.85em;
        }
    }

    /* Style untuk statistik dengan icon - selalu vertikal (icon di atas, label di bawah) */
    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.25rem;
        min-height: 60px;
    }

    .stat-icon-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.2rem;
        margin-bottom: 0.25rem;
        width: 100%;
    }

    .stat-icon {
        font-size: 1.1em;
        line-height: 1;
    }

    .stat-label-text {
        display: block;
        line-height: 1.2;
        width: 100%;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .stat-label-text small {
        display: block;
        font-size: 0.7em;
        line-height: 1.2;
    }

    .stat-item strong {
        display: block;
        margin-top: 0.15rem;
        line-height: 1.2;
    }

    /* Desktop - icon di atas, label di bawah dengan spacing yang cukup */
    @media (min-width: 992px) {
        .stat-icon-wrapper {
            gap: 0.25rem;
        }

        .stat-icon {
            font-size: 1.1em;
        }

        .stat-label-text small {
            font-size: 0.75em;
        }

        .stat-item {
            min-height: 65px;
        }
    }

    /* Tablet - icon di atas, label di bawah */
    @media (min-width: 769px) and (max-width: 991px) {
        .stat-icon-wrapper {
            gap: 0.2rem;
        }

        .stat-icon {
            font-size: 1em;
        }

        .stat-label-text small {
            font-size: 0.65em;
        }

        .stat-item {
            padding: 0.2rem;
            min-height: 55px;
        }

        .stat-item strong {
            font-size: 0.95em;
        }
    }

    /* Responsive untuk statistik card - sembunyikan teks pada layar kecil */
    @media (max-width: 768px) {
        .stat-label-text {
            display: none !important;
        }
        
        .stat-icon {
            font-size: 1.3em;
            margin-bottom: 0;
        }

        .stat-icon-wrapper {
            margin-bottom: 0.15rem;
        }

        .stat-item {
            padding: 0.15rem;
            min-height: 50px;
        }

        .stat-item strong {
            font-size: 0.9em;
        }
    }

    /* Pada layar sangat kecil, perbesar icon */
    @media (max-width: 576px) {
        .stat-icon {
            font-size: 1.5em;
        }

        .stat-item {
            min-height: 45px;
        }

        .stat-item strong {
            font-size: 0.85em;
        }
    }

    /* Untuk card yang lebih kecil, pastikan tidak tumpang tindih */
    @media (min-width: 992px) and (max-width: 1200px) {
        .stat-item {
            padding: 0.2rem;
        }

        .stat-icon-wrapper {
            gap: 0.2rem;
        }

        .stat-label-text small {
            font-size: 0.7em;
        }
    }

    /* Pastikan tidak ada overflow pada card */
    .stat-item {
        overflow: visible;
    }

    .stat-icon-wrapper {
        flex-shrink: 0;
    }

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

    /* Iklan Player & Slides */
    .iklan-player-wrapper {
        aspect-ratio: 16/9;
        background-color: #0b0b0b;
        position: relative;
    }
    .iklan-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #000;
    }
    .iklan-slide.active {
        opacity: 1;
        z-index: 2;
    }
    /* Latar Belakang Blur Artistik untuk menyesuaikan rasio gambar non-16:9 */
    .iklan-bg-blur {
        position: absolute;
        top: -20px;
        left: -20px;
        right: -20px;
        bottom: -20px;
        background-size: cover;
        background-position: center;
        filter: blur(28px) brightness(0.45);
        transform: scale(1.15);
        z-index: 1;
        opacity: 0.9;
        pointer-events: none;
        display: none;
    }
    .fit-contain_blur .iklan-bg-blur {
        display: block;
    }
    .iklan-media-item {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        transition: all 0.3s ease;
    }
    /* Mode: Proporsional + Blur & Letterbox */
    .fit-contain_blur .iklan-media-item,
    .fit-contain .iklan-media-item {
        object-fit: contain !important;
        max-width: 100%;
        max-height: 100%;
    }
    /* Mode: Paksa Regangkan (Stretch ke Bingkai 16:9 Penuh) */
    .fit-fill .iklan-media-item {
        object-fit: fill !important;
        width: 100% !important;
        height: 100% !important;
    }
    /* Mode: Isi Penuh (Crop / Cover) */
    .fit-cover .iklan-media-item {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
    }
    .iklan-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .iklan-dot:hover,
    .iklan-dot.active {
        background: #28a745;
        transform: scale(1.2);
    }
    .iklan-thumb {
        width: 75px;
        height: 45px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
</style>
<script>
    $(function() {
        // Format waktu update
        function updateLastUpdateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#lastUpdate').text(timeStr);
        }

        // Update waktu saat ini
        updateLastUpdateTime();

        // Auto Refresh Functionality (seragam dengan listAntrian.php)
        let autoRefreshInterval = null;
        let autoRefreshCountdownInterval = null;
        // Default true untuk monitoring (jika belum ada setting, aktifkan auto refresh)
        const storedRefreshEnabled = localStorage.getItem('monitoringAutoRefreshEnabled');
        let autoRefreshEnabled = storedRefreshEnabled === null ? true : storedRefreshEnabled === 'true';

        // Gunakan parameter interval dari URL jika ada, jika tidak gunakan dari localStorage, jika tidak gunakan default
        const urlParams = new URLSearchParams(window.location.search);
        const urlInterval = urlParams.get('interval');
        let autoRefreshSeconds = urlInterval ? parseInt(urlInterval) : (parseInt(localStorage.getItem('monitoringAutoRefreshSeconds')) || <?= $refresh_interval ?? 30 ?>);
        let countdownSeconds = autoRefreshSeconds;
        let pausedCountdown = null; // Untuk menyimpan countdown yang di-pause

        // Set interval dropdown ke nilai yang tersimpan
        $('#autoRefreshInterval').val(autoRefreshSeconds);

        // Simpan interval ke localStorage jika dari URL
        if (urlInterval) {
            localStorage.setItem('monitoringAutoRefreshSeconds', autoRefreshSeconds);
        }

        // Fungsi untuk format countdown
        function formatCountdown(seconds) {
            if (seconds >= 60) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                if (secs > 0) {
                    return `${minutes}m ${secs}s`;
                }
                return `${minutes}m`;
            }
            return `${seconds}s`;
        }

        // Fungsi untuk update UI auto refresh
        function updateAutoRefreshUI() {
            const $btn = $('#btnToggleAutoRefresh');
            const $status = $('#autoRefreshStatus');
            const $text = $('#autoRefreshText');
            const $icon = $btn.find('i');

            // Update countdown display
            $('#autoRefreshCountdown').text(formatCountdown(autoRefreshSeconds));

            if (autoRefreshEnabled) {
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
                $text.text('Nonaktifkan Auto Refresh');
                $icon.removeClass('fa-sync-alt').addClass('fa-pause');
                $status.show();
                startAutoRefresh();
            } else {
                $btn.removeClass('btn-primary').addClass('btn-outline-primary');
                $text.text('Aktifkan Auto Refresh');
                $icon.removeClass('fa-pause').addClass('fa-sync-alt');
                $status.hide();
                stopAutoRefresh();
            }
        }

        // Fungsi untuk update data via AJAX tanpa reload
        function refreshDataAjax() {
            // Cek apakah ada modal atau popup yang terbuka
            const hasOpenModal = $('.modal.show').length > 0;
            const hasSwalOpen = $('.swal2-container').length > 0;

            // Jangan refresh jika ada modal/popup terbuka
            if (hasOpenModal || hasSwalOpen) {
                return;
            }

            // Ambil parameter dari URL
            const params = new URLSearchParams(window.location.search);
            const ajaxUrl = '<?= base_url('backend/munaqosah/get-monitoring-status-antrian-ajax') ?>?' + params.toString();

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    // Tampilkan indikator loading (opsional)
                    $('#autoRefreshStatus').find('i').addClass('fa-spin');
                },
                success: function(response) {
                    if (response.success) {
                        // Update waktu terakhir update
                        updateLastUpdateTime();

                        // Update statistics
                        $('#statTotal').text(response.statistics.total);
                        $('#statCompleted').text(response.statistics.completed);
                        $('#statQueueing').text(response.statistics.waiting);
                        $('#statInProgress').text(response.statistics.in_progress || 0);
                        $('#statProgress').text(response.statistics.progress);

                        // Sync Format B
                        $('#statTotalB').text(response.statistics.total);
                        $('#statCompletedB').text(response.statistics.completed);
                        $('#statQueueingB').text(response.statistics.waiting);
                        $('#statInProgressB').text(response.statistics.in_progress || 0);
                        $('#statProgressB').text(response.statistics.progress + '%');
                        $('#barProgressB').css('width', response.statistics.progress + '%');

                        // Update progress bars
                        const pctCompleted = response.statistics.total > 0 
                            ? Math.round((response.statistics.completed / response.statistics.total) * 100) 
                            : 0;
                        const pctQueueing = response.statistics.total > 0 
                            ? Math.round((response.statistics.waiting / response.statistics.total) * 100) 
                            : 0;

                        $('#barCompleted').css('width', pctCompleted + '%');
                        $('#barQueueing').css('width', pctQueueing + '%');
                        $('#barProgress').css('width', response.statistics.progress + '%');
                        $('#descCompleted').text(pctCompleted + '%');
                        $('#descQueueing').text(pctQueueing + '%');

                        // Update table antrian
                        updateTableAntrian(response.queue);

                        // Update rooms
                        updateRooms(response.rooms);

                        // Update antrian per grup
                        updateAntrianPerGrup(response.antrianData);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error refreshing data:', error);
                },
                complete: function() {
                    // Hapus indikator loading
                    $('#autoRefreshStatus').find('i').removeClass('fa-spin');
                }
            });
        }

        // Fungsi untuk update table antrian
        function updateTableAntrian(queue) {
            const tbody = $('#tableAntrianBody');
            tbody.empty();

            const totalRooms = $('.room-card').length;
            let currentWaitingOrder = 0;

            queue.forEach(function(row) {
                const statusLabel = row.statusLabel;
                const badgeClass = row.badgeClass;
                const groupBadgeClass = row.groupBadgeClass;
                const noPesertaBadgeClass = row.noPesertaBadgeClass;
                const isTopQueue = row.isTopQueue;

                if (row.Status === 0) {
                    currentWaitingOrder++;
                }

                const badgeBlinkClass = isTopQueue ? 'badge-blink' : '';
                
                // Buat HTML untuk foto profil atau placeholder
                let photoHtml = '';
                if (row.PhotoProfil) {
                    // Cek apakah file ada dengan AJAX atau langsung coba load
                    // Jika gagal load, onerror akan menampilkan placeholder
                    const photoUrl = '<?= base_url('uploads/santri/') ?>' + row.PhotoProfil;
                    photoHtml = '<img src="' + photoUrl + '" ' +
                        'alt="Foto Profil" ' +
                        'class="photo-profil-thumb" ' +
                        'style="width: 45px; height: 60px; object-fit: cover; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6;" ' +
                        'onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">' +
                        '<div class="photo-profil-placeholder" style="width: 45px; height: 60px; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6; background-color: #e9ecef; display: none; align-items: center; justify-content: center; font-size: 9px; color: #6c757d; text-align: center; flex-shrink: 0; line-height: 1.2;">no profil</div>';
                } else {
                    photoHtml = '<div style="width: 45px; height: 60px; border-radius: 4px; margin-top: 8px; border: 2px solid #dee2e6; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #6c757d; text-align: center; flex-shrink: 0; line-height: 1.2;">no profil</div>';
                }
                
                const tr = $('<tr>');
                tr.append(
                    $('<td>').html(
                        '<div style="display: flex; align-items: flex-start;">' +
                        '<span class="badge ' + noPesertaBadgeClass + ' ' + badgeBlinkClass + '" style="font-size: 2em; margin-right: 8px;">' + row.NoPeserta + '</span>' +
                        '<div>' +
                        '<div>' + (row.NamaSantri || '-') + '</div>' +
                        '<div><span class="badge ' + groupBadgeClass + '">' + $('<div>').text(row.GroupPeserta).html() + '</span></div>' +
                        '</div>' +
                        '</div>'
                    )
                );
                tr.append(
                    $('<td>').html(
                        '<div style="display: flex; flex-direction: column; align-items: center;">' +
                        '<span class="badge ' + badgeClass + '">' + statusLabel + '</span>' +
                        photoHtml +
                        '</div>'
                    )
                );
                tbody.append(tr);
            });
        }

        // Fungsi untuk render compact room pills
        function renderRoomPills(rooms) {
            if (!rooms || rooms.length === 0) {
                const emptyHtml = '<small class="text-muted">Belum ada ruangan terdaftar</small>';
                $('#roomsPillsContainer').html(emptyHtml);
                $('#roomsRibbonContainer').html(emptyHtml);
                return;
            }

            let pillsHtml = '';
            rooms.forEach(function(room) {
                const isFull = room.is_full || false;
                const isOccupied = room.occupied || false;
                const pillClass = isFull ? 'badge-danger' : (isOccupied ? 'badge-warning' : 'badge-success');
                const icon = isFull ? 'users' : (isOccupied ? 'user' : 'door-open');
                const participants = room.participants || [];
                let participantStr = '';
                if (isOccupied && participants.length > 0) {
                    participantStr = ': ' + participants.map(function(p) { return p.NoPeserta; }).join(',');
                }
                pillsHtml += '<span class="badge ' + pillClass + ' p-1 mr-1 mb-1" style="font-size: 0.85rem;" title="Ruangan ' + room.RoomId + participantStr + '">';
                pillsHtml += '<i class="fas fa-' + icon + '"></i> R' + room.RoomId + participantStr;
                pillsHtml += '</span>';
            });

            $('#roomsPillsContainer').html(pillsHtml);
            $('#roomsRibbonContainer').html(pillsHtml);
        }

        // Fungsi untuk update rooms
        function updateRooms(rooms) {
            renderRoomPills(rooms);
            const container = $('#roomsContainer');
            container.empty();

            if (rooms.length === 0) {
                container.append(
                    '<div class="col-12">' +
                    '<div class="alert alert-info">Belum ada ruangan terdaftar untuk grup materi dan tipe ujian ini.</div>' +
                    '</div>'
                );
                return;
            }

            const totalRooms = rooms.length;
            const colClass = totalRooms === 1 ? 'col-12' : 'col-12 col-sm-6 col-lg-4';

            rooms.forEach(function(room) {
                const isOccupied = room.occupied || false;
                const participantCount = room.participant_count || 0;
                const maxCapacity = room.max_capacity || 1;
                const isFull = room.is_full || false;
                const participants = room.participants || [];

                let roomClass = 'bg-success text-white';
                let badgeHtml = '<span class="badge badge-light badge-pill"><i class="fas fa-door-open"></i> Kosong</span>';
                if (isFull) {
                    roomClass = 'bg-danger text-white';
                    badgeHtml = '<span class="badge badge-light badge-pill"><i class="fas fa-users"></i> Penuh</span>';
                } else if (isOccupied) {
                    roomClass = 'bg-warning text-dark';
                    badgeHtml = '<span class="badge badge-light badge-pill"><i class="fas fa-user"></i> Digunakan</span>';
                }

                let participantsHtml = '';
                if (isOccupied && participants.length > 0) {
                    participantsHtml = '<div class="room-participant mb-2">';
                    participants.forEach(function(participant) {
                        participantsHtml += '<div class="mb-1"><small>' + participant.NoPeserta + ' - ' + (participant.NamaSantri || '-') + '</small></div>';
                    });
                    participantsHtml += '</div>';
                } else {
                    participantsHtml = '<p class="mb-0"><i class="fas fa-door-open mr-1"></i>Ruangan tersedia</p>';
                }

                let capacityHtml = '';
                if (maxCapacity > 1) {
                    capacityHtml = '<div class="mb-2"><small><strong>Kapasitas:</strong> ' + participantCount + ' / ' + maxCapacity + '</small></div>';
                }

                const roomCard = $('<div>').addClass(colClass + ' mb-3').html(
                    '<div class="p-3 rounded shadow-sm room-card ' + roomClass + '">' +
                    '<div class="d-flex justify-content-between align-items-start mb-2">' +
                    '<h5 class="mb-0">Ruangan ' + room.RoomId + '</h5>' +
                    badgeHtml +
                    '</div>' +
                    capacityHtml +
                    participantsHtml +
                    '</div>'
                );

                container.append(roomCard);
            });
        }

        // Fungsi untuk update antrian per grup
        function updateAntrianPerGrup(antrianData) {
            const container = $('#antrianContainer');
            container.empty();

            if (!antrianData || antrianData.length === 0) {
                container.append(
                    '<div class="alert alert-info">' +
                    '<i class="fas fa-info-circle"></i> Belum ada data antrian untuk grup materi aktif.' +
                    '</div>'
                );
                return;
            }

            const row = $('<div>').addClass('row');

            antrianData.forEach(function(antrian, index) {
                const grup = antrian.grup;
                const stats = antrian.statistics;
                const rooms = antrian.rooms || [];

                const inputAntrianUrl = 'backend/munaqosah/input-registrasi-antrian';
                const inputAntrianParams = [];
                const urlParams = new URLSearchParams(window.location.search);
                inputAntrianParams.push('tahun=' + encodeURIComponent(urlParams.get('tahun') || ''));
                if (urlParams.get('type')) {
                    inputAntrianParams.push('type=' + encodeURIComponent(urlParams.get('type')));
                }
                inputAntrianParams.push('group=' + encodeURIComponent(grup.IdGrupMateriUjian));
                if (urlParams.get('tpq')) {
                    inputAntrianParams.push('tpq=' + encodeURIComponent(urlParams.get('tpq')));
                }
                const fullInputUrl = inputAntrianUrl + '?' + inputAntrianParams.join('&');

                let detailAntrianUrl = 'backend/munaqosah/monitoring-status-antrian?group=' + encodeURIComponent(grup.IdGrupMateriUjian);
                if (urlParams.get('type')) {
                    detailAntrianUrl += '&type=' + encodeURIComponent(urlParams.get('type'));
                }
                if (urlParams.get('tpq')) {
                    detailAntrianUrl += '&tpq=' + encodeURIComponent(urlParams.get('tpq'));
                }

                const progressColor = stats.progress >= 80 ? 'bg-success' : (stats.progress >= 50 ? 'bg-warning' : 'bg-danger');
                const bgClass = 'card-group-bg-' + ((index % 8) + 1);

                let roomsHtml = '';
                if (rooms.length > 0) {
                    let occupiedCount = 0;
                    let fullCount = 0;
                    let totalParticipants = 0;
                    let hasMultiCapacity = false;

                    rooms.forEach(function(room) {
                        if (room.occupied) occupiedCount++;
                        if (room.is_full) fullCount++;
                        totalParticipants += (room.participant_count || 0);
                        if ((room.max_capacity || 1) > 1) {
                            hasMultiCapacity = true;
                        }
                    });

                    roomsHtml = '<div class="mt-2 pt-2 border-top">' +
                        '<small class="text-muted d-block mb-1">Status Ruangan:</small>' +
                        '<div class="room-badges-container">';

                    rooms.forEach(function(room) {
                        const isFull = room.is_full || false;
                        const isOccupied = room.occupied || false;
                        const participantCount = room.participant_count || 0;
                        const maxCapacity = room.max_capacity || 1;

                        const badgeClass = isFull ? 'badge-danger' : (isOccupied ? 'badge-warning' : 'badge-success');
                        const icon = isFull ? 'users' : (isOccupied ? 'user' : 'door-open');

                        const capacityText = maxCapacity > 1 ? ' (' + participantCount + '/' + maxCapacity + ')' : '';
                        roomsHtml += '<span class="badge room-badge ' + badgeClass + '" title="Kapasitas: ' + participantCount + '/' + maxCapacity + '">' +
                            '<i class="fas fa-' + icon + '"></i> ' + room.RoomId + capacityText +
                            '</span>';
                    });

                    const fullText = hasMultiCapacity ? ' (' + fullCount + ' penuh)' : '';
                    roomsHtml += '</div>' +
                        '<small class="text-muted">' + totalParticipants + ' peserta di ' + occupiedCount + ' ruangan' + fullText + '</small>' +
                        '</div>';
                }

                const card = $('<div>').addClass('col-md-6 col-lg-4 mb-3').html(
                    '<div class="card ' + bgClass + '">' +
                    '<div class="card-body">' +
                    '<div class="d-flex justify-content-between align-items-start mb-2">' +
                    '<h6 class="card-title mb-0">' +
                    '<i class="fas fa-layer-group"></i> ' + $('<div>').text(grup.NamaMateriGrup).html() + ' ' +
                    '<small class="text-muted d-block"><i class="fas fa-tag"></i> ' + grup.IdGrupMateriUjian + '</small>' +
                    '</h6>' +
                    '<div class="btn-group btn-group-sm">' +
                    '<a href="<?= base_url() ?>' + fullInputUrl + '" class="btn btn-success" title="Input Antrian">' +
                    '<i class="fas fa-plus"></i>' +
                    '</a>' +
                    '<a href="<?= base_url() ?>' + detailAntrianUrl + '" class="btn btn-warning" title="Lihat Detail Antrian">' +
                    '<i class="fas fa-eye"></i>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mb-2">' +
                    '<div class="d-flex justify-content-between mb-1">' +
                    '<span class="small">Progress Antrian</span>' +
                    '<span class="small font-weight-bold">' + stats.progress + '%</span>' +
                    '</div>' +
                    '<div class="progress" style="height: 20px;">' +
                    '<div class="progress-bar ' + progressColor + '" role="progressbar" style="width: ' + stats.progress + '%">' + stats.progress + '%</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row text-center mt-2 mb-2">' +
                    '<div class="col-3 stat-item" title="Total Peserta">' +
                    '<div class="stat-icon-wrapper">' +
                    '<i class="fas fa-users stat-icon text-info"></i>' +
                    '<span class="stat-label-text"><small class="text-muted">Total</small></span>' +
                    '</div>' +
                    '<strong>' + stats.total + '</strong>' +
                    '</div>' +
                    '<div class="col-3 stat-item" title="Selesai">' +
                    '<div class="stat-icon-wrapper">' +
                    '<i class="fas fa-check-circle stat-icon text-success"></i>' +
                    '<span class="stat-label-text"><small class="text-muted">Selesai</small></span>' +
                    '</div>' +
                    '<strong class="text-success">' + stats.completed + '</strong>' +
                    '</div>' +
                    '<div class="col-3 stat-item" title="Tunggu">' +
                    '<div class="stat-icon-wrapper">' +
                    '<i class="fas fa-clock stat-icon text-warning"></i>' +
                    '<span class="stat-label-text"><small class="text-muted">Tunggu</small></span>' +
                    '</div>' +
                    '<strong class="text-warning">' + stats.waiting + '</strong>' +
                    '</div>' +
                    '<div class="col-3 stat-item" title="Sedang Ujian">' +
                    '<div class="stat-icon-wrapper">' +
                    '<i class="fas fa-clipboard-check stat-icon text-danger"></i>' +
                    '<span class="stat-label-text"><small class="text-muted">Ujian</small></span>' +
                    '</div>' +
                    '<strong class="text-danger">' + stats.in_progress + '</strong>' +
                    '</div>' +
                    '</div>' +
                    roomsHtml +
                    '</div>' +
                    '</div>'
                );

                row.append(card);
            });

            container.append(row);
        }

        // Fungsi untuk memulai auto refresh
        function startAutoRefresh() {
            stopAutoRefresh(); // Hentikan yang lama jika ada

            // Reset countdown atau gunakan paused countdown jika ada
            if (pausedCountdown !== null) {
                countdownSeconds = pausedCountdown;
                pausedCountdown = null;
            } else {
                countdownSeconds = autoRefreshSeconds;
            }

            // Update countdown display
            $('#autoRefreshCountdown').text(formatCountdown(countdownSeconds));

            // Mulai countdown
            autoRefreshCountdownInterval = setInterval(function() {
                countdownSeconds--;
                $('#autoRefreshCountdown').text(formatCountdown(countdownSeconds));

                if (countdownSeconds <= 0) {
                    // Refresh data via AJAX
                    refreshDataAjax();
                    // Reset countdown
                    countdownSeconds = autoRefreshSeconds;
                    $('#autoRefreshCountdown').text(formatCountdown(countdownSeconds));
                }
            }, 1000);

            // Backup refresh interval (jika countdown terlewat)
            autoRefreshInterval = setInterval(function() {
                refreshDataAjax();
            }, autoRefreshSeconds * 1000);
        }

        // Fungsi untuk menghentikan auto refresh
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
            if (autoRefreshCountdownInterval) {
                clearInterval(autoRefreshCountdownInterval);
                autoRefreshCountdownInterval = null;
            }
        }

        // Toggle auto refresh
        $('#btnToggleAutoRefresh').on('click', function() {
            autoRefreshEnabled = !autoRefreshEnabled;
            localStorage.setItem('monitoringAutoRefreshEnabled', autoRefreshEnabled);
            updateAutoRefreshUI();
        });

        // Change interval auto refresh
        $('#autoRefreshInterval').on('change', function() {
            const newInterval = parseInt($(this).val());
            autoRefreshSeconds = newInterval;
            localStorage.setItem('monitoringAutoRefreshSeconds', newInterval);

            // Jika auto refresh sedang aktif, restart dengan interval baru
            if (autoRefreshEnabled) {
                startAutoRefresh();
            } else {
                // Update countdown display meskipun tidak aktif
                countdownSeconds = newInterval;
                $('#autoRefreshCountdown').text(formatCountdown(newInterval));
            }
        });

        // Inisialisasi auto refresh UI
        updateAutoRefreshUI();

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            stopAutoRefresh();
            stopIklanTimer();
        });

        // -------------------------------------------------------------
        // FORMAT HEADER STRIP A / B (KOMBINASI STATUS & RUANGAN)
        // -------------------------------------------------------------
        let currentHeaderFormat = localStorage.getItem('monitoringHeaderFormat') || 'A';

        window.setHeaderFormat = function(fmt) {
            currentHeaderFormat = fmt;
            localStorage.setItem('monitoringHeaderFormat', fmt);
            applyHeaderFormat(fmt);
        };

        function applyHeaderFormat(fmt) {
            if (fmt === 'A') {
                $('#summaryStripA').show();
                $('#summaryStripB').hide();
                $('#btnFormatA').addClass('active btn-primary').removeClass('btn-outline-secondary');
                $('#btnFormatB').removeClass('active btn-primary').addClass('btn-outline-secondary');
            } else {
                $('#summaryStripA').hide();
                $('#summaryStripB').show();
                $('#btnFormatB').addClass('active btn-primary').removeClass('btn-outline-secondary');
                $('#btnFormatA').removeClass('active btn-primary').addClass('btn-outline-secondary');
            }
        }

        applyHeaderFormat(currentHeaderFormat);

        // -------------------------------------------------------------
        // IKLAN & SLIDESHOW ENGINE (BERBASIS SESSIONSTORAGE CACHE)
        // -------------------------------------------------------------
        const iklanListUrl = '<?= base_url('backend/munaqosah/get-iklan-list-ajax') ?>';
        const iklanVersionUrl = '<?= base_url('backend/munaqosah/get-iklan-version-ajax') ?>';
        const iklanUploadUrl = '<?= base_url('backend/munaqosah/upload-iklan-ajax') ?>';
        const iklanDeleteUrl = '<?= base_url('backend/munaqosah/delete-iklan-ajax') ?>';
        const iklanSettingsUrl = '<?= base_url('backend/munaqosah/update-iklan-settings-ajax') ?>';

        const pageType = '<?= esc($selected_type) ?>';
        const pageTpq = '<?= esc($selected_type === 'munaqosah' ? '0' : ($selected_tpq ?: '0')) ?>';
        const cacheKey = 'iklanMediaCache_' + pageType + '_' + pageTpq;
        const cacheVersionKey = 'iklanMediaVersion_' + pageType + '_' + pageTpq;

        let iklanMediaList = [];
        let iklanCurrentIndex = 0;
        let iklanTimer = null;
        let iklanCacheVersion = 0;

        let iklanEnabled = localStorage.getItem('monitoringIklanEnabled') !== null 
            ? localStorage.getItem('monitoringIklanEnabled') === 'true' 
            : <?= !empty($iklan_settings['iklan_enabled']) ? 'true' : 'false' ?>;
        let iklanInterval = parseInt(localStorage.getItem('monitoringIklanInterval') || '<?= esc($iklan_settings['iklan_interval'] ?? 10) ?>');
        let iklanMode = localStorage.getItem('monitoringIklanMode') || '<?= esc($iklan_settings['iklan_mode'] ?? 'loop') ?>';
        let iklanSingleId = parseInt(localStorage.getItem('monitoringIklanSingleId') || '<?= esc($iklan_settings['iklan_single_id'] ?? 0) ?>');
        let iklanFit = localStorage.getItem('monitoringIklanFit') || '<?= esc($iklan_settings['iklan_fit'] ?? 'contain_blur') ?>';

        function applyIklanFitClass(fit) {
            $('#iklanPlayerContainer')
                .removeClass('fit-contain_blur fit-fill fit-cover fit-contain')
                .addClass('fit-' + (fit || 'contain_blur'));
        }

        function initIklanUI() {
            $('#toggleIklan').prop('checked', iklanEnabled);
            $('#iklanInterval').val(iklanInterval);
            $('#iklanMode').val(iklanMode);
            $('#iklanFit').val(iklanFit);
            applyIklanFitClass(iklanFit);

            if (iklanEnabled) {
                $('#iklanPlaySettings').show();
                $('#iklanPlayerContainer').show();
                $('#sectionAntrian').hide();
                loadIklanData();
            } else {
                $('#iklanPlaySettings').hide();
                $('#iklanPlayerContainer').hide();
                $('#sectionAntrian').show();
                stopIklanTimer();
            }
        }

        // Toggle Iklan ON / OFF (Menukar antara Media Player vs Monitoring Antrian Grup)
        $('#toggleIklan').on('change', function() {
            iklanEnabled = $(this).is(':checked');
            localStorage.setItem('monitoringIklanEnabled', iklanEnabled);

            if (iklanEnabled) {
                $('#iklanPlaySettings').show();
                $('#iklanPlayerContainer').fadeIn(300);
                $('#sectionAntrian').hide();
                loadIklanData();
            } else {
                $('#iklanPlaySettings').hide();
                $('#iklanPlayerContainer').hide();
                $('#sectionAntrian').fadeIn(300);
                stopIklanTimer();
            }
        });

        // Event ubah interval & mode dari header
        $('#iklanInterval').on('change', function() {
            iklanInterval = parseInt($(this).val());
            localStorage.setItem('monitoringIklanInterval', iklanInterval);
            if (iklanEnabled && iklanMode !== 'single') {
                startIklanTimer();
            }
        });

        $('#iklanMode').on('change', function() {
            iklanMode = $(this).val();
            localStorage.setItem('monitoringIklanMode', iklanMode);
            if (iklanEnabled) {
                stopIklanTimer();
                if (iklanMode !== 'single') {
                    startIklanTimer();
                } else {
                    renderIklanPlayer();
                }
            }
        });

        $('#iklanFit').on('change', function() {
            iklanFit = $(this).val();
            localStorage.setItem('monitoringIklanFit', iklanFit);
            applyIklanFitClass(iklanFit);
        });

        // Load media iklan (menggunakan sessionStorage cache terisolasi per type & tpq)
        function loadIklanData() {
            const cachedData = sessionStorage.getItem(cacheKey);
            const cachedVersion = parseInt(sessionStorage.getItem(cacheVersionKey) || '0');

            if (cachedData && cachedVersion > 0) {
                try {
                    iklanMediaList = JSON.parse(cachedData);
                    iklanCacheVersion = cachedVersion;
                    renderIklanPlayer();
                    checkIklanVersion(); // Cek versi di background (hanya query 1 integer)
                    return;
                } catch (e) {
                    sessionStorage.removeItem(cacheKey);
                }
            }

            fetchIklanFromServer();
        }

        function checkIklanVersion() {
            const query = '?tahun=<?= esc($selected_tahun) ?>&type=' + encodeURIComponent(pageType) + '&tpq=' + encodeURIComponent(pageTpq);
            $.ajax({
                url: iklanVersionUrl + query,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.version > 0 && res.version !== iklanCacheVersion) {
                        fetchIklanFromServer();
                    }
                }
            });
        }

        function fetchIklanFromServer(callback) {
            const query = '?tahun=<?= esc($selected_tahun) ?>&type=' + encodeURIComponent(pageType) + '&tpq=' + encodeURIComponent(pageTpq);
            $.ajax({
                url: iklanListUrl + query,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        iklanMediaList = res.data || [];
                        iklanCacheVersion = res.version || Date.now();
                        sessionStorage.setItem(cacheKey, JSON.stringify(iklanMediaList));
                        sessionStorage.setItem(cacheVersionKey, iklanCacheVersion);
                        if (iklanEnabled) {
                            renderIklanPlayer();
                        }
                        if (typeof callback === 'function') {
                            callback(iklanMediaList);
                        }
                    }
                }
            });
        }

        // Render Slideshow Player
        function renderIklanPlayer() {
            const container = $('#iklanSlideContainer');
            container.find('.iklan-slide').remove();
            $('#iklanDots').empty();
            applyIklanFitClass(iklanFit);

            if (!iklanMediaList || iklanMediaList.length === 0) {
                $('#iklanEmptyState').show();
                $('#iklanSlideInfo').hide();
                stopIklanTimer();
                return;
            }

            $('#iklanEmptyState').hide();
            $('#iklanSlideInfo').show();

            iklanMediaList.forEach(function(media, idx) {
                let mediaTag = '';
                if (media.MediaType === 'video') {
                    mediaTag = '<video src="' + media.url + '" class="iklan-media-item" muted playsinline preload="auto"></video>';
                } else {
                    mediaTag = '<div class="iklan-bg-blur" style="background-image: url(\'' + media.url + '\');"></div>' +
                               '<img src="' + media.url + '" class="iklan-media-item" alt="' + $('<div>').text(media.Judul).html() + '" loading="lazy">';
                }

                const slide = $('<div>')
                    .addClass('iklan-slide')
                    .attr('data-index', idx)
                    .attr('data-id', media.id)
                    .attr('data-type', media.MediaType)
                    .html(mediaTag);

                container.append(slide);

                const dot = $('<div>')
                    .addClass('iklan-dot')
                    .attr('data-index', idx)
                    .attr('title', media.Judul)
                    .on('click', function() {
                        goToSlide(idx);
                        if (iklanMode !== 'single') {
                            startIklanTimer();
                        }
                    });

                $('#iklanDots').append(dot);
            });

            // Tentukan slide awal
            if (iklanMode === 'single' && iklanSingleId > 0) {
                const targetIdx = iklanMediaList.findIndex(function(m) { return parseInt(m.id) === iklanSingleId; });
                iklanCurrentIndex = targetIdx >= 0 ? targetIdx : 0;
                goToSlide(iklanCurrentIndex);
                stopIklanTimer();
            } else {
                iklanCurrentIndex = 0;
                goToSlide(0);
                startIklanTimer();
            }
        }

        function goToSlide(idx) {
            const slides = $('#iklanSlideContainer .iklan-slide');
            const dots = $('#iklanDots .iklan-dot');
            if (slides.length === 0) return;

            idx = ((idx % slides.length) + slides.length) % slides.length;
            iklanCurrentIndex = idx;

            slides.removeClass('active');
            dots.removeClass('active');

            const currentSlide = $(slides[idx]);
            currentSlide.addClass('active');
            $(dots[idx]).addClass('active');

            // Handle video playback jika slide adalah video
            slides.each(function() {
                const vid = $(this).find('video')[0];
                if (vid) {
                    vid.pause();
                    vid.currentTime = 0;
                }
            });

            const activeVideo = currentSlide.find('video')[0];
            if (activeVideo) {
                stopIklanTimer();
                activeVideo.play().catch(function() {});
                $(activeVideo).off('ended').on('ended', function() {
                    nextSlide();
                });
            }

            updateTimerLabel();
        }

        function nextSlide() {
            if (!iklanMediaList || iklanMediaList.length === 0) return;

            let nextIdx;
            if (iklanMode === 'random') {
                const total = iklanMediaList.length;
                if (total <= 1) {
                    nextIdx = 0;
                } else {
                    do {
                        nextIdx = Math.floor(Math.random() * total);
                    } while (nextIdx === iklanCurrentIndex);
                }
            } else {
                nextIdx = (iklanCurrentIndex + 1) % iklanMediaList.length;
            }

            goToSlide(nextIdx);
        }

        function startIklanTimer() {
            stopIklanTimer();
            if (iklanMode === 'single') return;

            iklanTimer = setInterval(function() {
                nextSlide();
            }, iklanInterval * 1000);

            updateTimerLabel();
        }

        function stopIklanTimer() {
            if (iklanTimer) {
                clearInterval(iklanTimer);
                iklanTimer = null;
            }
        }

        function updateTimerLabel() {
            const total = iklanMediaList ? iklanMediaList.length : 0;
            const current = iklanCurrentIndex + 1;
            let modeText = 'Loop';
            if (iklanMode === 'random') modeText = 'Acak';
            if (iklanMode === 'single') modeText = 'Tetap';

            $('#iklanTimerLabel').text(current + '/' + total + ' · ' + (iklanMode !== 'single' ? iklanInterval + 's (' + modeText + ')' : modeText));
        }

        // -------------------------------------------------------------
        // MODAL KELOLA IKLAN & PENGATURAN DISPLAY
        // -------------------------------------------------------------
        $('#btnKelolaIklan').on('click', function() {
            $('#modalKelolaIklan').modal('show');
            loadModalMediaList();
            initModalSettings();
        });

        $('#iklanFileInput').on('change', function() {
            const fileName = $(this).val().split('\\').pop() || $(this).val().split('/').pop();
            $(this).next('.custom-file-label').text(fileName || 'Pilih file...');
        });

        $('#settingMode').on('change', function() {
            if ($(this).val() === 'single') {
                $('#settingSingleRow').slideDown(200);
            } else {
                $('#settingSingleRow').slideUp(200);
            }
        });

        function initModalSettings() {
            $('#settingInterval').val(iklanInterval);
            $('#settingMode').val(iklanMode);
            if (iklanMode === 'single') {
                $('#settingSingleRow').show();
            } else {
                $('#settingSingleRow').hide();
            }
        }

        function loadModalMediaList() {
            $('#iklanMediaList').html('<p class="text-muted text-center py-3"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar media...</p>');
            fetchIklanFromServer(function(list) {
                renderModalMediaTable(list);
                // Populate dropdown pilihan single image
                const $sel = $('#settingSingleId');
                $sel.find('option:not(:first)').remove();
                list.forEach(function(m) {
                    const opt = $('<option>').val(m.id).text(m.Judul + ' (' + m.MediaType + ')');
                    if (parseInt(m.id) === iklanSingleId) {
                        opt.prop('selected', true);
                    }
                    $sel.append(opt);
                });
            });
        }

        function renderModalMediaTable(list) {
            if (!list || list.length === 0) {
                $('#iklanMediaList').html('<div class="alert alert-light text-center border">Belum ada media iklan. Silakan upload gambar di atas.</div>');
                return;
            }

            let html = '<table class="table table-sm table-hover table-bordered align-middle">';
            html += '<thead class="thead-light"><tr><th style="width:40px;">No</th><th style="width:90px;">Preview</th><th>Judul / Keterangan</th><th style="width:80px;">Tipe</th><th style="width:70px;" class="text-center">Aksi</th></tr></thead><tbody>';

            list.forEach(function(item, i) {
                let preview = '';
                if (item.MediaType === 'video') {
                    preview = '<span class="badge badge-secondary p-2"><i class="fas fa-film mr-1"></i>Video</span>';
                } else {
                    preview = '<img src="' + item.url + '" class="iklan-thumb">';
                }

                html += '<tr>';
                html += '<td class="text-center">' + (i + 1) + '</td>';
                html += '<td class="text-center">' + preview + '</td>';
                html += '<td><strong>' + $('<div>').text(item.Judul).html() + '</strong></td>';
                html += '<td><span class="badge ' + (item.MediaType === 'video' ? 'badge-primary' : 'badge-info') + '">' + item.MediaType + '</span></td>';
                html += '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-delete-iklan" data-id="' + item.id + '" title="Hapus"><i class="fas fa-trash"></i></button></td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $('#iklanMediaList').html(html);

            // Handler tombol hapus
            $('.btn-delete-iklan').on('click', function() {
                const id = $(this).data('id');
                if (confirm('Yakin ingin menghapus media ini?')) {
                    deleteIklanItem(id);
                }
            });
        }

        // Upload media via AJAX
        $('#formUploadIklan').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(window.location.search);
            formData.append('tahun', params.get('tahun') || '');
            formData.append('tpq', params.get('tpq') || '');

            const $btn = $('#btnSubmitUpload');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunggah...');

            $.ajax({
                url: iklanUploadUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Unggah Media');
                    if (res.success) {
                        sessionStorage.removeItem('iklanMediaCache');
                        sessionStorage.removeItem('iklanMediaVersion');
                        $('#formUploadIklan')[0].reset();
                        $('.custom-file-label').text('Pilih file gambar (landscape)...');
                        loadModalMediaList();
                    } else {
                        alert('Gagal upload: ' + (res.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Unggah Media');
                    alert('Terjadi kesalahan saat upload media.');
                }
            });
        });

        // Hapus media via AJAX
        function deleteIklanItem(id) {
            $.ajax({
                url: iklanDeleteUrl,
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        sessionStorage.removeItem('iklanMediaCache');
                        sessionStorage.removeItem('iklanMediaVersion');
                        loadModalMediaList();
                    } else {
                        alert('Gagal hapus: ' + (res.message || 'Terjadi kesalahan'));
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus media.');
                }
            });
        }

        // Simpan pengaturan display via AJAX
        $('#formIklanSettings').on('submit', function(e) {
            e.preventDefault();
            const params = new URLSearchParams(window.location.search);
            const data = {
                iklan_interval: $('#settingInterval').val(),
                iklan_mode: $('#settingMode').val(),
                iklan_single_id: $('#settingSingleId').val(),
                iklan_video_enabled: $('#settingVideoEnabled').is(':checked') ? 1 : 0,
                header_format: currentHeaderFormat,
                tpq: params.get('tpq') || ''
            };

            const $btn = $('#btnSaveSettings');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            $.ajax({
                url: iklanSettingsUrl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Pengaturan Display');
                    if (res.success) {
                        iklanInterval = parseInt(data.iklan_interval);
                        iklanMode = data.iklan_mode;
                        iklanSingleId = parseInt(data.iklan_single_id);

                        localStorage.setItem('monitoringIklanInterval', iklanInterval);
                        localStorage.setItem('monitoringIklanMode', iklanMode);
                        localStorage.setItem('monitoringIklanSingleId', iklanSingleId);

                        $('#iklanInterval').val(iklanInterval);
                        $('#iklanMode').val(iklanMode);

                        if (iklanEnabled) {
                            renderIklanPlayer();
                        }
                        alert('Pengaturan display berhasil disimpan.');
                        $('#modalKelolaIklan').modal('hide');
                    } else {
                        alert('Gagal simpan: ' + (res.message || 'Terjadi kesalahan'));
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Pengaturan Display');
                    alert('Terjadi kesalahan saat menyimpan pengaturan.');
                }
            });
        });

        // Inisialisasi awal UI Iklan
        initIklanUI();
    });
</script>
<?= $this->endSection() ?>