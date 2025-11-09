<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Input Registrasi Antrian</h3>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="d-flex align-items-center mr-2 mb-2 mb-md-0">
                                <select id="autoRefreshInterval" class="form-control form-control-sm mr-2" style="width: auto; min-width: 80px;" title="Pilih Interval Auto Refresh">
                                    <option value="10">10 detik</option>
                                    <option value="15">15 detik</option>
                                    <option value="30" selected>30 detik</option>
                                    <option value="60">1 menit</option>
                                    <option value="120">2 menit</option>
                                    <option value="300">5 menit</option>
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
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Hidden inputs untuk filter yang dibawa dari halaman sebelumnya -->
                        <?php if (empty($session_id_tpq)): ?>
                            <input type="hidden" id="tpq" value="<?= $selected_tpq ?>">
                            <input type="hidden" id="type" value="<?= $selected_type ?>">
                        <?php else: ?>
                            <input type="hidden" id="tpq" value="<?= $session_id_tpq ?>">
                            <input type="hidden" id="type" value="pra-munaqosah">
                        <?php endif; ?>
                        <input type="hidden" id="group" value="<?= $selected_group ?>">
                        <input type="hidden" id="tahun" value="<?= $selected_tahun ?>">

                        <!-- Info Filter Aktif (untuk konfirmasi) -->
                        <div class="alert alert-info mb-3 py-2">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Grup:</strong> <?php
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
                                    | <strong>Type:</strong> <?= $types[$selected_type] ?? $selected_type ?>
                                <?php endif; ?>
                            </small>
                        </div>

                        <!-- Input Registrasi -->
                        <div class="card card-warning mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-plus"></i> Registrasi Peserta</h3>
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="text" id="queueSearch" class="form-control form-control-lg" placeholder="Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)" inputmode="numeric" maxlength="3">
                                    <div class="input-group-append">
                                        <button class="btn btn-warning" type="button" id="btnScanQR">
                                            <i class="fas fa-qrcode"></i> Scan QR
                                        </button>
                                        <button class="btn btn-danger" type="button" id="btnQueueReset">Reset</button>
                                    </div>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <span class="text-info"><i class="fas fa-info-circle"></i> Auto registrasi akan aktif setelah 3 digit, atau tekan Enter</span>
                                </small>
                            </div>
                        </div>

                        <!-- Status Peserta (Compact untuk Mobile) -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 bg-light rounded">
                            <div class="d-flex align-items-center mr-2 mb-1 mb-sm-0">
                                <span class="badge badge-info badge-lg mr-1">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Total</small>
                                    <strong class="text-info"><?= $statistics['total'] ?></strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mr-2 mb-1 mb-sm-0">
                                <span class="badge badge-warning badge-lg mr-1">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Antrian</small>
                                    <strong class="text-warning"><?= $statistics['queueing'] ?></strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-1 mb-sm-0">
                                <span class="badge badge-success badge-lg mr-1">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Progress</small>
                                    <strong class="text-success"><?= $statistics['progress'] ?>%</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Status Ruangan -->
                        <h5 class="mt-3 mb-2">Status Ruangan</h5>
                        <?php if (!empty($rooms)): ?>
                            <?php
                            $totalRooms = count($rooms);
                            // Jika hanya 1 ruangan dengan banyak peserta, gunakan format tabel kompak
                            $singleRoom = $totalRooms === 1;
                            $firstRoom = $rooms[0] ?? null;
                            $singleRoomWithParticipants = $singleRoom &&
                                !empty($firstRoom['participants']) &&
                                count($firstRoom['participants']) > 1;

                            // Jika hanya 1 ruangan, gunakan full width, jika lebih gunakan grid responsif
                            $colClass = $totalRooms === 1
                                ? 'col-12'
                                : 'col-12 col-sm-6 col-lg-4';
                            ?>

                            <?php if ($singleRoomWithParticipants): ?>
                                <!-- Format Tabel Kompak untuk 1 Ruangan dengan Multiple Peserta -->
                                <?php
                                $room = $firstRoom;
                                $isOccupied = $room['occupied'] ?? false;
                                $participantCount = $room['participant_count'] ?? 0;
                                $maxCapacity = $room['max_capacity'] ?? 1;
                                $isFull = $room['is_full'] ?? false;
                                $participants = $room['participants'] ?? [];
                                ?>
                                <div class="card border-<?= $isFull ? 'danger' : ($isOccupied ? 'warning' : 'success') ?> mb-3">
                                    <div class="card-header bg-<?= $isFull ? 'danger' : ($isOccupied ? 'warning' : 'success') ?> text-<?= $isFull || !$isOccupied ? 'white' : 'dark' ?> d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"><i class="fas fa-door-open"></i> Ruangan <?= $room['RoomId'] ?></h5>
                                            <small>Kapasitas: <?= $participantCount ?> / <?= $maxCapacity ?></small>
                                        </div>
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
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 5%;">No</th>
                                                        <th style="width: 15%;">No Peserta</th>
                                                        <th style="width: 35%;">Nama Santri</th>
                                                        <th style="width: 40%;" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($participants as $participant): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><strong><?= $participant['NoPeserta'] ?></strong></td>
                                                            <td><?= $participant['NamaSantri'] ?? '-' ?></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <button type="button"
                                                                        class="btn btn-success btn-finish-room"
                                                                        data-id="<?= $participant['id'] ?? '' ?>"
                                                                        data-nopeserta="<?= $participant['NoPeserta'] ?? '' ?>"
                                                                        data-nama="<?= $participant['NamaSantri'] ?? '-' ?>"
                                                                        title="Selesai">
                                                                        <i class="fas fa-check"></i> Selesai
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-warning btn-exit-room"
                                                                        data-id="<?= $participant['id'] ?? '' ?>"
                                                                        data-nopeserta="<?= $participant['NoPeserta'] ?? '' ?>"
                                                                        data-nama="<?= $participant['NamaSantri'] ?? '-' ?>"
                                                                        title="Keluar">
                                                                        <i class="fas fa-sign-out-alt"></i> Keluar
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Format Card untuk Multiple Ruangan atau 1 Ruangan dengan 1 Peserta -->
                                <div class="row">
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
                                                    <div class="room-participant mb-3">
                                                        <?php foreach ($participants as $participant): ?>
                                                            <div class="mb-2 border-bottom pb-2">
                                                                <div class="mb-1">
                                                                    <strong>No Peserta:</strong> <?= $participant['NoPeserta'] ?>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <small><?= $participant['NamaSantri'] ?? '-' ?></small>
                                                                </div>
                                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                                    <button type="button"
                                                                        class="btn btn-light btn-finish-room text-dark"
                                                                        data-id="<?= $participant['id'] ?? '' ?>"
                                                                        data-nopeserta="<?= $participant['NoPeserta'] ?? '' ?>"
                                                                        data-nama="<?= $participant['NamaSantri'] ?? '-' ?>">
                                                                        <i class="fas fa-check"></i> Selesai
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-warning btn-exit-room"
                                                                        data-id="<?= $participant['id'] ?? '' ?>"
                                                                        data-nopeserta="<?= $participant['NoPeserta'] ?? '' ?>"
                                                                        data-nama="<?= $participant['NamaSantri'] ?? '-' ?>">
                                                                        <i class="fas fa-sign-out-alt"></i> Keluar
                                                                    </button>
                                                                </div>
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
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Belum ada ruangan terdaftar untuk grup materi dan tipe ujian ini. Tambahkan RoomId pada data juri.
                            </div>
                        <?php endif; ?>

                        <!-- Table Antrian -->
                        <h5 class="mt-3 mb-2">Daftar Antrian</h5>
                        <div class="table-responsive">
                            <table id="tableAntrian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Group Peserta</th>
                                        <th>No Peserta - Nama Santri</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Kumpulkan semua unique GroupPeserta dari data queue
                                    $uniqueGroups = [];
                                    foreach ($queue as $q) {
                                        $group = $q['GroupPeserta'] ?? 'Group 1';
                                        if (!in_array($group, $uniqueGroups)) {
                                            $uniqueGroups[] = $group;
                                        }
                                    }

                                    // Sort unique groups untuk konsistensi
                                    sort($uniqueGroups);

                                    // Array warna Bootstrap yang bisa diulang
                                    $baseColors = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger', 'badge-info', 'badge-dark', 'badge-secondary'];

                                    // Buat mapping warna dinamis untuk setiap group
                                    $groupColorMap = [];
                                    foreach ($uniqueGroups as $index => $group) {
                                        $colorIndex = $index % count($baseColors);
                                        $groupColorMap[$group] = $baseColors[$colorIndex];
                                    }

                                    $no = 1;
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
                                        $typeResolved = $row['TypeUjian'] ?? ($row['TypeUjianResolved'] ?? '-');
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <?php
                                                $groupPeserta = $row['GroupPeserta'] ?? 'Group 1';
                                                // Ambil warna dari mapping dinamis, fallback ke secondary
                                                $badgeColor = $groupColorMap[$groupPeserta] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badgeColor ?>"><?= $groupPeserta ?></span>
                                            </td>
                                            <td><?= $row['NoPeserta'] ?> - <?= $row['NamaSantri'] ?? '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($status === 0): ?>
                                                        <button type="button" class="btn btn-sm btn-warning btn-open-room"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Masuk
                                                        </button>
                                                    <?php elseif ($status === 1): ?>
                                                        <button type="button" class="btn btn-sm btn-success btn-finish-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Selesai
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-warning btn-exit-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Keluar
                                                        </button>
                                                    <?php elseif ($status === 2): ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-wait-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Selesai
                                                        </button>
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
        </div>
    </div>
</section>

<!-- QR Scanner Modal -->
<div class="modal fade" id="modalQRScanner" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 100%"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }

    .room-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }

    /* Styling untuk tabel kompak Status Ruangan */
    .table-sm thead th {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.5rem;
        white-space: nowrap;
    }

    .table-sm tbody td {
        padding: 0.5rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-sm tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Kompak tombol aksi di tabel */
    .table-sm .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.3;
    }

    /* Responsif untuk mobile */
    @media (max-width: 576px) {
        .badge-lg {
            font-size: 0.875rem;
            padding: 0.4rem 0.6rem;
        }

        .bg-light {
            font-size: 0.875rem;
        }

        .room-card {
            margin-bottom: 0.75rem;
        }

        .room-card h5 {
            font-size: 1rem;
        }

        .btn-group-sm {
            font-size: 0.75rem;
        }

        /* Tabel kompak di mobile */
        .table-sm thead th {
            font-size: 0.75rem;
            padding: 0.4rem 0.25rem;
        }

        .table-sm tbody td {
            font-size: 0.75rem;
            padding: 0.4rem 0.25rem;
        }

        .table-sm .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .table-sm .btn-group-sm .btn i {
            font-size: 0.7rem;
        }
    }
</style>
<script>
    $(function() {
        const table = $('#tableAntrian').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [
                [0, 'asc']
            ]
        });

        // Validasi input hanya angka 3 digit
        let autoRegistrasiHandler = null;

        $('#queueSearch').on('input', function(e) {
            let value = $(this).val();
            // Hanya terima angka
            value = value.replace(/[^0-9]/g, '');
            // Batasi maksimal 3 digit
            if (value.length > 3) {
                value = value.substring(0, 3);
            }
            $(this).val(value);

            // Auto registrasi logic (mengganti handler lama)
            const noPeserta = value.trim();

            // Clear any existing timeout and countdown
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }

            // Show auto search indicator - hanya jika sudah 3 digit angka
            if (/^\d{3}$/.test(noPeserta)) {
                // Add visual indicator
                $(this).addClass('border-info');

                // Show countdown indicator in placeholder
                const originalPlaceholder = $(this).attr('placeholder');
                let countdown = 1;

                const updatePlaceholder = () => {
                    $(this).attr('placeholder', `Auto registrasi dalam ${countdown} detik...`);
                };

                updatePlaceholder();

                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        $(this).attr('placeholder', originalPlaceholder);
                        $(this).removeClass('border-info');
                        registerAntrian();
                    } else {
                        updatePlaceholder();
                    }
                }, 1000);

                // Store interval ID for cleanup
                window.autoSearchCountdown = countdownInterval;

                const $input = $(this);
                window.autoSearchTimeout = setTimeout(function() {
                    clearInterval(window.autoSearchCountdown);
                    $input.attr('placeholder', originalPlaceholder);
                    $input.removeClass('border-info');
                    registerAntrian();
                }, 1000); // 1 second delay after user stops typing
            } else {
                // Remove visual indicator if less than 3 digits
                $(this).removeClass('border-info');
                $(this).attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');
            }
        });

        // Prevent paste huruf
        $('#queueSearch').on('paste', function(e) {
            const paste = (e.originalEvent || e).clipboardData.getData('text');
            const numbersOnly = paste.replace(/[^0-9]/g, '').substring(0, 3);
            e.preventDefault();
            $(this).val(numbersOnly);
            // Trigger input event untuk auto registrasi
            $(this).trigger('input');
        });

        // Validasi keypress - hanya terima angka
        $('#queueSearch').on('keypress', function(e) {
            // Hanya terima angka (0-9), backspace (8), delete (46), arrow keys
            const charCode = e.which || e.keyCode;
            // Allow: backspace (8), delete (46), arrow keys (37-40), tab (9)
            if ([8, 9, 37, 38, 39, 40, 46].includes(charCode)) {
                return true;
            }
            // Hanya terima angka (48-57)
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
                return false;
            }
            // Batasi 3 digit (cek panjang saat ini)
            if ($(this).val().length >= 3) {
                e.preventDefault();
                return false;
            }
        });

        // Fungsi registrasi antrian
        function registerAntrian() {
            // Clear any auto search timeouts when manually clicking
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $('#queueSearch').removeClass('border-info');
            $('#queueSearch').attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');

            const noPeserta = $('#queueSearch').val().trim();
            const idGrupMateri = $('#group').val() || $('input#group').val();
            const typeUjian = $('#type').val() || $('input#type').val();
            const tahunAjaran = $('#tahun').val() || $('input#tahun').val();

            // Validasi input
            if (!noPeserta) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Masukkan nomor peserta terlebih dahulu'
                });
                return;
            }

            // Validasi harus 3 digit angka
            if (!/^\d{3}$/.test(noPeserta)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Nomor peserta harus 3 digit angka'
                });
                $('#queueSearch').focus();
                return;
            }

            if (!idGrupMateri) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih grup materi ujian terlebih dahulu'
                });
                return;
            }

            if (!typeUjian) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih type ujian terlebih dahulu'
                });
                return;
            }

            if (!tahunAjaran) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Tahun ajaran tidak boleh kosong'
                });
                return;
            }

            // Kirim request AJAX
            registerAntrianAjax(noPeserta, idGrupMateri, typeUjian, tahunAjaran);
        }

        // Fungsi untuk melakukan request AJAX registrasi
        function registerAntrianAjax(noPeserta, idGrupMateri, typeUjian, tahunAjaran, action = null, antrianGrupLainId = null) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mendaftarkan peserta ke antrian',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Siapkan data request
            const requestData = {
                NoPeserta: noPeserta,
                IdGrupMateriUjian: idGrupMateri,
                TypeUjian: typeUjian,
                IdTahunAjaran: tahunAjaran,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            };

            // Tambahkan action dan antrian_grup_lain_id jika ada
            if (action) {
                requestData.action = action;
            }
            if (antrianGrupLainId) {
                requestData.antrian_grup_lain_id = antrianGrupLainId;
            }

            // Kirim request AJAX
            $.ajax({
                url: '<?= base_url('backend/munaqosah/register-antrian-ajax') ?>',
                type: 'POST',
                data: requestData,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            didOpen: () => {
                                // Reset input
                                $('#queueSearch').val('');
                            }
                        }).then(() => {
                            // Set flag untuk auto focus setelah reload
                            sessionStorage.setItem('autoFocusQueueSearch', 'true');
                            // Reload halaman untuk memperbarui tabel setelah auto close
                            location.reload();
                        });
                    } else {
                        // Cek apakah peserta sudah ada di antrian aktif (sama grup dan type)
                        if (response.already_in_queue && response.queue_data) {
                            const queueData = response.queue_data;
                            const statusBadge = queueData.status == 1 ?
                                '<span class="badge badge-danger">Sedang Ujian</span>' :
                                '<span class="badge badge-warning">Menunggu</span>';

                            // Badge TypeUjian
                            const typeUjianLabel = queueData.type_ujian === 'pra-munaqosah' ?
                                'Pra-Munaqosah' :
                                queueData.type_ujian === 'munaqosah' ?
                                'Munaqosah' :
                                queueData.type_ujian || '-';
                            const typeUjianBadge = queueData.type_ujian === 'pra-munaqosah' ?
                                '<span class="badge badge-info">' + typeUjianLabel + '</span>' :
                                queueData.type_ujian === 'munaqosah' ?
                                '<span class="badge badge-primary">' + typeUjianLabel + '</span>' :
                                '<span class="badge badge-secondary">' + typeUjianLabel + '</span>';

                            // Badge Status Nilai
                            const nilaiBadge = queueData.has_nilai === true ?
                                '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Sudah Ada Nilai</span>' :
                                '<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> Belum Ada Nilai</span>';

                            // Badge Room ID
                            const roomBadge = queueData.room_id ?
                                '<span class="badge badge-info"><i class="fas fa-door-open"></i> ' + queueData.room_id + '</span>' :
                                '<span class="badge badge-secondary">Belum ada ruangan</span>';

                            // Format tanggal
                            let tanggalDibuat = '-';
                            if (queueData.created_at) {
                                const date = new Date(queueData.created_at);
                                tanggalDibuat = date.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }

                            // Tampilkan popup informasi
                            Swal.fire({
                                icon: 'info',
                                title: 'Peserta Sudah di Antrian',
                                html: `
                                    <div class="text-left">
                                        <p><strong>Peserta sudah terdaftar di antrian aktif untuk grup dan tipe ujian ini:</strong></p>
                                        <ul class="list-unstyled mt-3">
                                            <li><strong>No Peserta:</strong> ${queueData.no_peserta}</li>
                                            <li><strong>Nama:</strong> ${queueData.nama_santri}</li>
                                            <li><strong>Grup Materi:</strong> ${queueData.grup_materi}</li>
                                            <li><strong>Type Ujian:</strong> ${typeUjianBadge}</li>
                                            <li><strong>Status Antrian:</strong> ${statusBadge}</li>
                                            <li><strong>Ruangan:</strong> ${roomBadge}</li>
                                            <li><strong>Status Nilai:</strong> ${nilaiBadge}</li>
                                            <li><strong>Tanggal Registrasi:</strong> ${tanggalDibuat}</li>
                                        </ul>
                                        <div class="alert alert-info mt-3 mb-2">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Informasi:</strong> 
                                                Peserta sudah terdaftar di antrian untuk grup materi dan tipe ujian ini. 
                                                Tidak perlu melakukan registrasi ulang.
                                            </small>
                                        </div>
                                        ${queueData.is_tulis_al_quran === true && queueData.has_nilai === false ? 
                                            '<div class="alert alert-warning mt-2 mb-0">' +
                                                '<small>' +
                                                    '<i class="fas fa-exclamation-triangle"></i> ' +
                                                    '<strong>Catatan Khusus Tulis Al-Quran:</strong> ' +
                                                    'Untuk ujian Tulis Al-Quran, juri membutuhkan waktu yang cukup lama untuk memasukkan nilai. ' +
                                                    'Oleh karena itu, nilai mungkin belum ada meskipun peserta sudah menyelesaikan ujian tertulis.' +
                                                '</small>' +
                                            '</div>' : ''}
                                    </div>
                                `,
                                confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                        // Cek apakah perlu konfirmasi (antrian di grup lain)
                        else if (response.needs_confirmation && response.conflict_data) {
                            const conflictData = response.conflict_data;
                            const statusBadge = conflictData.status == 1 ?
                                '<span class="badge badge-danger">Sedang Ujian</span>' :
                                '<span class="badge badge-warning">Menunggu</span>';

                            // Badge TypeUjian
                            const typeUjianLabel = conflictData.type_ujian === 'pra-munaqosah' ?
                                'Pra-Munaqosah' :
                                conflictData.type_ujian === 'munaqosah' ?
                                'Munaqosah' :
                                conflictData.type_ujian || '-';
                            const typeUjianBadge = conflictData.type_ujian === 'pra-munaqosah' ?
                                '<span class="badge badge-info">' + typeUjianLabel + '</span>' :
                                conflictData.type_ujian === 'munaqosah' ?
                                '<span class="badge badge-primary">' + typeUjianLabel + '</span>' :
                                '<span class="badge badge-secondary">' + typeUjianLabel + '</span>';

                            // Badge Status Nilai
                            const nilaiBadge = conflictData.has_nilai === true ?
                                '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Sudah Ada Nilai</span>' :
                                '<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> Belum Ada Nilai</span>';

                            // Tampilkan popup konfirmasi
                            Swal.fire({
                                icon: 'warning',
                                title: 'Konflik Antrian Ditemukan',
                                html: `
                                    <div class="text-left">
                                        <p><strong>Peserta terdeteksi sedang antri di grup materi lain:</strong></p>
                                        <ul class="list-unstyled mt-3">
                                            <li><strong>No Peserta:</strong> ${conflictData.no_peserta}</li>
                                            <li><strong>Nama:</strong> ${conflictData.nama_santri}</li>
                                            <li><strong>Grup Materi:</strong> ${conflictData.grup_materi_lain}</li>
                                            <li><strong>Type Ujian:</strong> ${typeUjianBadge}</li>
                                            <li><strong>Status Antrian:</strong> ${statusBadge}</li>
                                            <li><strong>Status Nilai:</strong> ${nilaiBadge}</li>
                                        </ul>
                                        <div class="alert alert-info mt-3 mb-2">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Catatan:</strong> 
                                                ${conflictData.has_nilai === true 
                                                    ? 'Peserta sudah memiliki nilai di grup materi ini. Jika peserta sudah selesai, pilih "Ya, Sudah Selesai" untuk mengubah status antrian menjadi selesai.' 
                                                    : 'Peserta belum memiliki nilai di grup materi ini. Jika peserta belum selesai, pilih "Belum Selesai" untuk menghapus antrian.'}
                                            </small>
                                        </div>
                                        ${conflictData.is_tulis_al_quran === true && conflictData.has_nilai === false ? 
                                            '<div class="alert alert-warning mt-2 mb-0">' +
                                                '<small>' +
                                                    '<i class="fas fa-exclamation-triangle"></i> ' +
                                                    '<strong>Catatan Khusus Tulis Al-Quran:</strong> ' +
                                                    'Untuk ujian Tulis Al-Quran, juri membutuhkan waktu yang cukup lama untuk memasukkan nilai. ' +
                                                    'Oleh karena itu, nilai mungkin belum ada meskipun peserta sudah menyelesaikan ujian tertulis. ' +
                                                    'Silakan konfirmasi langsung ke peserta apakah sudah selesai mengerjakan ujian tertulis. ' +
                                                    'Jika peserta sudah selesai, pilih "Ya, Sudah Selesai" meskipun nilai belum ada.' +
                                                '</small>' +
                                            '</div>' : ''}
                                        <p class="mt-3"><strong>Silakan konfirmasi ke peserta:</strong></p>
                                        <p class="text-muted">Apakah peserta sudah selesai ujian di grup materi tersebut?</p>
                                    </div>
                                `,
                                showCancelButton: true,
                                showDenyButton: true,
                                confirmButtonText: '<i class="fas fa-check"></i> Ya, Sudah Selesai',
                                denyButtonText: '<i class="fas fa-times"></i> Belum Selesai',
                                cancelButtonText: '<i class="fas fa-arrow-left"></i> Batal',
                                confirmButtonColor: '#28a745',
                                denyButtonColor: '#dc3545',
                                cancelButtonColor: '#6c757d',
                                reverseButtons: true,
                                focusConfirm: false,
                                focusDeny: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Peserta sudah selesai, update status menjadi 2
                                    registerAntrianAjax(noPeserta, idGrupMateri, typeUjian, tahunAjaran, 'update', conflictData.antrian_id);
                                } else if (result.isDenied) {
                                    // Peserta belum selesai, hapus antrian di grup lain
                                    Swal.fire({
                                        title: 'Konfirmasi Hapus',
                                        text: 'Apakah Anda yakin ingin menghapus antrian di grup lain?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc3545',
                                        cancelButtonColor: '#6c757d',
                                        confirmButtonText: 'Ya, Hapus',
                                        cancelButtonText: 'Batal'
                                    }).then((deleteResult) => {
                                        if (deleteResult.isConfirmed) {
                                            registerAntrianAjax(noPeserta, idGrupMateri, typeUjian, tahunAjaran, 'delete', conflictData.antrian_id);
                                        }
                                    });
                                }
                                // Jika cancel, tidak ada yang dilakukan
                            });
                        } else {
                            // Error biasa - format standard
                            const errorMessage = response.message || 'Terjadi kesalahan saat registrasi';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Registrasi',
                                html: `
                                    <div class="text-left">
                                        <p>${errorMessage}</p>
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <small>
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                <strong>Tips:</strong> 
                                                Pastikan semua data sudah benar dan peserta belum terdaftar di antrian sebelumnya.
                                            </small>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat registrasi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server. Pastikan koneksi internet Anda stabil.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Registrasi',
                        html: `
                            <div class="text-left">
                                <p>${errorMessage}</p>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <small>
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Tips:</strong> 
                                        Pastikan semua data sudah benar dan koneksi internet stabil. 
                                        Jika masalah berlanjut, hubungi administrator sistem.
                                    </small>
                                </div>
                            </div>
                        `,
                        confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        // Event handler untuk tombol registrasi
        $('#btnQueueRegister').on('click', function() {
            registerAntrian();
        });

        // Event handler untuk Enter key
        $('#queueSearch').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                registerAntrian();
            }
        });

        // Event handler untuk reset
        $('#btnQueueReset').on('click', function() {
            // Clear any auto search timeouts
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $('#queueSearch').val('');
            $('#queueSearch').removeClass('border-info');
            $('#queueSearch').attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');
        });

        // QR Scanner
        let html5QrcodeScanner = null;

        $('#btnScanQR').click(function() {
            $('#modalQRScanner').modal('show');

            // Initialize QR scanner only once
            if (typeof Html5QrcodeScanner !== 'undefined') {
                // Clear existing scanner if any
                if (html5QrcodeScanner) {
                    try {
                        html5QrcodeScanner.clear();
                    } catch (e) {
                        console.log('Error clearing scanner:', e);
                    }
                }

                // Initialize new scanner
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    }
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'QR Scanner library tidak tersedia'
                });
            }
        });

        // Clear scanner when modal is closed
        $('#modalQRScanner').on('hidden.bs.modal', function() {
            if (html5QrcodeScanner) {
                try {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                } catch (e) {
                    console.log('Error clearing scanner:', e);
                }
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            $('#queueSearch').val(decodedText);
            $('#modalQRScanner').modal('hide');

            // Auto register antrian after QR scan
            registerAntrian();
        }

        function onScanFailure(error) {
            // Handle scan failure silently
        }


        // Clear auto search timeout and indicators when user focuses input
        $('#queueSearch').on('focus', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');
        });

        // Clear indicators when user clicks away
        $('#queueSearch').on('blur', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');
        });

        // Event handler untuk delete antrian dengan SweetAlert2
        $('.btn-delete-antrian').on('click', function(e) {
            e.preventDefault();
            const link = $(this);
            const url = link.attr('href');
            const confirmMessage = link.data('confirm') || 'Apakah Anda yakin ingin menghapus data ini?';

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Event handler untuk tombol In (auto assign room)
        $('.btn-open-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mencari ruangan kosong',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request AJAX untuk auto assign room
            $.ajax({
                url: `<?= base_url('backend/munaqosah/auto-assign-room-ajax') ?>/${id}`,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: response.message || 'Tidak ada ruangan kosong saat ini'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat mengassign ruangan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });

        // Event handler untuk button Selesai dari room card
        $('.btn-finish-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID antrian tidak ditemukan'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai peserta ${noPeserta} - ${nama} selesai ujian?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 2,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta ditandai selesai',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Event handler untuk button Keluar dari room card
        $('.btn-exit-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID antrian tidak ditemukan'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Keluarkan peserta ${noPeserta} - ${nama} dari ruangan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Event handler untuk button Selesai dari tabel
        $('.btn-finish-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai peserta ${noPeserta} - ${nama} selesai ujian?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 2,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta ditandai selesai',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Event handler untuk button Keluar dari tabel
        $('.btn-exit-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Keluarkan peserta ${noPeserta} - ${nama} dari ruangan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Event handler untuk button Tunggu dari tabel
        $('.btn-wait-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Kembalikan peserta ${noPeserta} - ${nama} ke antrian menunggu?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kembalikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Auto focus ke input registrasi setelah reload (jika ada flag)
        if (sessionStorage.getItem('autoFocusQueueSearch') === 'true') {
            sessionStorage.removeItem('autoFocusQueueSearch');
            setTimeout(function() {
                $('#queueSearch').focus();
            }, 500);
        }

        // Auto Refresh Functionality
        let autoRefreshInterval = null;
        let autoRefreshCountdownInterval = null;
        let autoRefreshEnabled = localStorage.getItem('autoRefreshEnabled') === 'true';
        let autoRefreshSeconds = parseInt(localStorage.getItem('autoRefreshSeconds')) || 30; // Default 30 detik
        let countdownSeconds = autoRefreshSeconds;
        let pausedCountdown = null; // Untuk menyimpan countdown yang di-pause

        // Set interval dropdown ke nilai yang tersimpan
        $('#autoRefreshInterval').val(autoRefreshSeconds);

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
                    const isInputFocused = $('#queueSearch').is(':focus');

                    // Jangan refresh jika ada modal/popup terbuka atau input sedang focused
                    if (!hasOpenModal && !hasSwalOpen && !isInputFocused) {
                        location.reload();
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
                const isInputFocused = $('#queueSearch').is(':focus');

                if (!hasOpenModal && !hasSwalOpen && !isInputFocused) {
                    location.reload();
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
            localStorage.setItem('autoRefreshEnabled', autoRefreshEnabled);
            updateAutoRefreshUI();
        });

        // Change interval auto refresh
        $('#autoRefreshInterval').on('change', function() {
            const newInterval = parseInt($(this).val());
            autoRefreshSeconds = newInterval;
            localStorage.setItem('autoRefreshSeconds', newInterval);

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

        // Pause auto refresh saat input focused
        $('#queueSearch').on('focus', function() {
            if (autoRefreshEnabled && autoRefreshCountdownInterval) {
                // Pause countdown saat input focused, simpan nilai countdown saat ini
                pausedCountdown = countdownSeconds;
                clearInterval(autoRefreshCountdownInterval);
                autoRefreshCountdownInterval = null;
                // Juga pause backup interval
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
            }
        });

        // Resume auto refresh saat input blur (jika auto refresh enabled)
        $('#queueSearch').on('blur', function() {
            if (autoRefreshEnabled) {
                // Resume countdown
                startAutoRefresh();
            }
        });
    });
</script>
<?= $this->endSection() ?>