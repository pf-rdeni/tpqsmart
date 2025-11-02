<?= $this->extend('backend/template/templateNoSidebarAndNavbar') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Monitoring Status Antrian</h3>
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-group mb-0">
                                <label class="mb-0 small">Auto Refresh (detik)</label>
                                <select id="refreshInterval" class="form-control form-control-sm">
                                    <option value="3" <?= $refresh_interval == 3 ? 'selected' : '' ?>>3 detik</option>
                                    <option value="5" <?= $refresh_interval == 5 ? 'selected' : '' ?>>5 detik</option>
                                    <option value="10" <?= $refresh_interval == 10 ? 'selected' : '' ?>>10 detik</option>
                                    <option value="15" <?= $refresh_interval == 15 ? 'selected' : '' ?>>15 detik</option>
                                    <option value="30" <?= $refresh_interval == 30 ? 'selected' : '' ?>>30 detik</option>
                                </select>
                            </div>
                            <div class="mt-3">
                                <span id="refreshStatus" class="badge badge-info">
                                    <i class="fas fa-sync-alt fa-spin"></i> Auto Refresh Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter (Read Only untuk display) -->
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
                            <?php if (empty($session_id_tpq)): ?>
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
                            <?php endif; ?>
                            <?php if (empty($session_id_tpq)): ?>
                                | <strong>Type Ujian:</strong> <?= $types[$selected_type] ?? $selected_type ?>
                            <?php endif; ?>
                            | <strong>Waktu Update:</strong> <span id="lastUpdate">-</span>
                        </div>

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
                        </div>

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
                                    <?php $isOccupied = $room['occupied']; ?>
                                    <div class="<?= $colClass ?> mb-3">
                                        <div class="p-3 rounded shadow-sm room-card <?= $isOccupied ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="mb-0">Ruangan <?= $room['RoomId'] ?></h5>
                                                <?php if ($isOccupied): ?>
                                                    <span class="badge badge-light badge-pill">
                                                        <i class="fas fa-user"></i> Digunakan
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light badge-pill">
                                                        <i class="fas fa-door-open"></i> Kosong
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($isOccupied && $room['participant']): ?>
                                                <div class="room-participant mb-3">
                                                    <div class="mb-1">
                                                        <strong>No Peserta:</strong> <?= $room['participant']['NoPeserta'] ?>
                                                    </div>
                                                    <div>
                                                        <small><?= $room['participant']['NamaSantri'] ?? '-' ?></small>
                                                    </div>
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

                        <!-- Daftar Antrian -->
                        <h5 class="mt-4 mb-3">Daftar Antrian</h5>
                        <div class="table-responsive">
                            <table id="tableAntrian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta</th>
                                        <th>Nama Peserta</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Type Ujian</th>
                                    </tr>
                                </thead>
                                <tbody id="tableAntrianBody">
                                    <?php $no = 1; ?>
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
                                        $typeResolved = $row['TypeUjian'] ?? ($row['TypeUjianResolved'] ?? '-');
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['NoPeserta'] ?></td>
                                            <td><?= $row['NamaSantri'] ?? '-' ?></td>
                                            <td>
                                                <?php if (!empty($row['RoomId'])): ?>
                                                    <span class="badge badge-info"><?= $row['RoomId'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                            </td>
                                            <td><?= $typeResolved ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    @media (max-width: 576px) {
        .room-card {
            margin-bottom: 0.75rem;
        }
        .room-card h5 {
            font-size: 1rem;
        }
    }
</style>
<script>
    $(function() {

        let refreshInterval = <?= $refresh_interval ?>;
        let refreshTimer = null;

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

        // Fungsi untuk refresh data
        function refreshData() {
            const params = new URLSearchParams(window.location.search);
            params.set('interval', refreshInterval);
            
            // Reload halaman dengan parameter yang sama
            window.location.reload();
        }

        // Event handler untuk perubahan interval
        $('#refreshInterval').on('change', function() {
            refreshInterval = parseInt($(this).val());
            const params = new URLSearchParams(window.location.search);
            params.set('interval', refreshInterval);
            
            // Update URL dan reload
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        // Set auto refresh timer
        function startAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            refreshTimer = setInterval(function() {
                refreshData();
            }, refreshInterval * 1000);
        }

        // Mulai auto refresh
        startAutoRefresh();

        // Update status refresh setiap detik
        let countdown = refreshInterval;
        const countdownInterval = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                countdown = refreshInterval;
            }
            $('#refreshStatus').html(`<i class="fas fa-sync-alt fa-spin"></i> Refresh dalam ${countdown} detik`);
        }, 1000);

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        });
    });
</script>
<?= $this->endSection() ?>

