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
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title mb-0">Info Grafis</h3>
                                        <span class="badge badge-primary"><?= esc($selectedGroupName) ?></span>
                                    </div>
                                    <div class="card-body">
                                        <!-- Status Peserta -->
                                        <div class="row mb-3">
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
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Peserta</span>
                                                        <span class="info-box-number" id="statTotal"><?= $total ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">Teregister</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Sudah diuji</span>
                                                        <span class="info-box-number" id="statCompleted"><?= $completed ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" id="barCompleted" style="width: <?= $pctCompleted ?>%"></div>
                                                        </div>
                                                        <span class="progress-description"><span id="descCompleted"><?= $pctCompleted ?>%</span> selesai</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Antrian ujian</span>
                                                        <span class="info-box-number" id="statQueueing"><?= $queueing ?></span>
                                                        <div class="progress">
                                                            <div class="progress-bar" id="barQueueing" style="width: <?= $pctQueueing ?>%"></div>
                                                        </div>
                                                        <span class="progress-description"><span id="descQueueing"><?= $pctQueueing ?>%</span> menunggu</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="info-box bg-primary">
                                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Progress</span>
                                                        <span class="info-box-number" id="statProgress"><?= $progress ?>%</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" id="barProgress" style="width: <?= $progress ?>%"></div>
                                                        </div>
                                                        <span class="progress-description">Tingkat penyelesaian</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- Kosong untuk sekarang -->

                                        <!-- Status Ruangan -->
                                        <h5 class="mt-4 mb-3">Status Ruangan</h5>
                                        <div id="roomsContainer" class="row">
                                            <?php if (!empty($rooms)): ?>
                                                <?php
                                                $totalRooms = count($rooms);
                                                // Jika hanya 1 ruangan, gunakan full width, jika lebih gunakan grid responsif
                                                // Desktop: 1 ruangan = full, >1 = 3 kolom | Tablet: 2 kolom | Mobile: 1 kolom
                                                $colClass = $totalRooms === 1
                                                    ? 'col-12'
                                                    : 'col-12 col-sm-6 col-lg-4';
                                                ?>
                                                <?php foreach ($rooms as $room): ?>
                                                    <?php
                                                    $isOccupied = $room['occupied'] ?? false;
                                                    $participantCount = $room['participant_count'] ?? 0;
                                                    $maxCapacity = $room['max_capacity'] ?? 1;
                                                    $isFull = $room['is_full'] ?? false;
                                                    $participants = $room['participants'] ?? [];
                                                    ?>
                                                    <div class="<?= $colClass ?> mb-3">
                                                        <div class="p-3 rounded shadow-sm room-card <?= $isFull ? 'bg-danger text-white' : ($isOccupied ? 'bg-warning text-dark' : 'bg-success text-white') ?>">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h5 class="mb-0">Ruangan <?= $room['RoomId'] ?></h5>
                                                                <?php if ($isFull): ?>
                                                                    <span class="badge badge-light badge-pill">
                                                                        <i class="fas fa-users"></i> Penuh
                                                                    </span>
                                                                <?php elseif ($isOccupied): ?>
                                                                    <span class="badge badge-light badge-pill">
                                                                        <i class="fas fa-user"></i> Digunakan
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-light badge-pill">
                                                                        <i class="fas fa-door-open"></i> Kosong
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if ($maxCapacity > 1): ?>
                                                            <div class="mb-2">
                                                                <small>
                                                                    <strong>Kapasitas:</strong> <?= $participantCount ?> / <?= $maxCapacity ?>
                                                                </small>
                                                            </div>
                                                            <?php endif; ?>
                                                            <?php if ($isOccupied && !empty($participants)): ?>
                                                                <div class="room-participant mb-2">
                                                                    <?php foreach ($participants as $participant): ?>
                                                                        <div class="mb-1">
                                                                            <small>
                                                                                <?= $participant['NoPeserta'] ?> - <?= $participant['NamaSantri'] ?? '-' ?>
                                                                            </small>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <p class="mb-0">
                                                                    <i class="fas fa-door-open mr-1"></i>Ruangan tersedia
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        Belum ada ruangan terdaftar untuk grup materi dan tipe ujian ini.
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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
                                                                                <div class="d-flex flex-nowrap align-items-center" style="gap: 0.5rem;">
                                                                                    <?php foreach ($antrian['rooms'] as $room): ?>
                                                                                        <?php
                                                                                        $isFull = $room['is_full'] ?? false;
                                                                                        $isOccupied = $room['occupied'] ?? false;
                                                                                        $participantCount = $room['participant_count'] ?? 0;
                                                                                        $maxCapacity = $room['max_capacity'] ?? 1;
                                                                                        ?>
                                                                                        <span class="badge <?= $isFull ? 'badge-danger' : ($isOccupied ? 'badge-warning' : 'badge-success') ?>" style="white-space: nowrap; flex-shrink: 0;" title="Kapasitas: <?= $participantCount ?>/<?= $maxCapacity ?>">
                                                                                            <i class="fas fa-<?= $isFull ? 'users' : ($isOccupied ? 'user' : 'door-open') ?>"></i>
                                                                                            <?= $room['RoomId'] ?> (<?= $participantCount ?>/<?= $maxCapacity ?>)
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
                            </div>
                        </div>
                        <!-- Filter (Read Only untuk display) -->
                    </div>
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
                        $('#statProgress').text(response.statistics.progress + '%');

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

        // Fungsi untuk update rooms
        function updateRooms(rooms) {
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

                    rooms.forEach(function(room) {
                        if (room.occupied) occupiedCount++;
                        if (room.is_full) fullCount++;
                        totalParticipants += (room.participant_count || 0);
                    });

                    roomsHtml = '<div class="mt-2 pt-2 border-top">' +
                        '<small class="text-muted d-block mb-1">Status Ruangan:</small>' +
                        '<div class="d-flex flex-nowrap align-items-center" style="gap: 0.5rem;">';

                    rooms.forEach(function(room) {
                        const isFull = room.is_full || false;
                        const isOccupied = room.occupied || false;
                        const participantCount = room.participant_count || 0;
                        const maxCapacity = room.max_capacity || 1;

                        const badgeClass = isFull ? 'badge-danger' : (isOccupied ? 'badge-warning' : 'badge-success');
                        const icon = isFull ? 'users' : (isOccupied ? 'user' : 'door-open');

                        roomsHtml += '<span class="badge ' + badgeClass + '" style="white-space: nowrap; flex-shrink: 0;" title="Kapasitas: ' + participantCount + '/' + maxCapacity + '">' +
                            '<i class="fas fa-' + icon + '"></i> ' + room.RoomId + ' (' + participantCount + '/' + maxCapacity + ')' +
                            '</span>';
                    });

                    roomsHtml += '</div>' +
                        '<small class="text-muted">' + totalParticipants + ' peserta di ' + occupiedCount + ' ruangan (' + fullCount + ' penuh)</small>' +
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
                    '<div class="col-3"><small class="text-muted d-block">Total</small><strong>' + stats.total + '</strong></div>' +
                    '<div class="col-3"><small class="text-muted d-block">Selesai</small><strong class="text-success">' + stats.completed + '</strong></div>' +
                    '<div class="col-3"><small class="text-muted d-block">Menunggu</small><strong class="text-warning">' + stats.waiting + '</strong></div>' +
                    '<div class="col-3"><small class="text-muted d-block">Ujian</small><strong class="text-danger">' + stats.in_progress + '</strong></div>' +
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
        });
    });
</script>
<?= $this->endSection() ?>