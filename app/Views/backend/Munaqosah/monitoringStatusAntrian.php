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
                            <div class="col-4">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Daftar Antrian</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Daftar Antrian -->
                                        <div class="table-responsive">
                                            <table id="tableAntrian" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Grup Peserta</th>
                                                        <th>Peserta</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableAntrianBody">
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
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="badge <?= $groupBadgeClass ?>"><?= htmlspecialchars($groupPeserta) ?></span>
                                                            </td>
                                                            <td><?= $row['NoPeserta'] ?>- <?= $row['NamaSantri'] ?? '-' ?></td>
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
                                    <div class="card-header">
                                        <h3 class="card-title">Info Grafis</h3>
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
                                                            <div class="mb-2">
                                                                <small>
                                                                    <strong>Kapasitas:</strong> <?= $participantCount ?> / <?= $maxCapacity ?>
                                                                </small>
                                                            </div>
                                                            <?php if ($isOccupied && !empty($participants)): ?>
                                                                <div class="room-participant mb-2">
                                                                    <?php foreach ($participants as $participant): ?>
                                                                        <div class="mb-1">
                                                                            <small>
                                                                                <strong>No Peserta:</strong> <?= $participant['NoPeserta'] ?> -
                                                                                <?= $participant['NamaSantri'] ?? '-' ?>
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
                    // Cek apakah ada modal atau popup yang terbuka
                    const hasOpenModal = $('.modal.show').length > 0;
                    const hasSwalOpen = $('.swal2-container').length > 0;

                    // Jangan refresh jika ada modal/popup terbuka
                    if (!hasOpenModal && !hasSwalOpen) {
                        // Reload halaman dengan parameter yang sama
                        const params = new URLSearchParams(window.location.search);
                        params.set('interval', autoRefreshSeconds);
                        window.location.href = window.location.pathname + '?' + params.toString();
                    } else {
                        // Reset countdown jika tidak bisa refresh
                        countdownSeconds = autoRefreshSeconds;
                        $('#autoRefreshCountdown').text(formatCountdown(countdownSeconds));
                    }
                }
            }, 1000);

            // Backup refresh interval (jika countdown terlewat)
            autoRefreshInterval = setInterval(function() {
                const hasOpenModal = $('.modal.show').length > 0;
                const hasSwalOpen = $('.swal2-container').length > 0;

                if (!hasOpenModal && !hasSwalOpen) {
                    // Reload halaman dengan parameter yang sama
                    const params = new URLSearchParams(window.location.search);
                    params.set('interval', autoRefreshSeconds);
                    window.location.href = window.location.pathname + '?' + params.toString();
                }
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