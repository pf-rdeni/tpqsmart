<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Antrian Grup Materi Ujian</h3>
                        <div class="btn-group mb-2 mb-md-0">
                            <a href="<?= base_url('backend/munaqosah/monitoring-status-antrian') ?>?tahun=<?= $selected_tahun ?>&type=<?= $selected_type ?>&group=<?= $selected_group ?><?= !empty($selected_tpq) ? '&tpq=' . $selected_tpq : '' ?>"
                                class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-desktop"></i> Monitoring
                            </a>
                            <a href="<?= base_url('backend/munaqosah/input-registrasi-antrian') ?>?tahun=<?= $selected_tahun ?>&type=<?= $selected_type ?>&group=<?= $selected_group ?><?= !empty($selected_tpq) ? '&tpq=' . $selected_tpq : '' ?>"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus"></i> Input Registrasi
                            </a>
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

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form method="get" action="<?= base_url('backend/munaqosah/antrian') ?>" class="mb-4">
                            <div class="form-row align-items-end">
                                <?php if (empty($session_id_tpq)): ?>
                                    <!-- Jika admin super, tampilkan dropdown TPQ -->
                                    <div class="form-group col-md-3">
                                        <label for="tpq">TPQ</label>
                                        <select name="tpq" id="tpq" class="form-control">
                                            <option value="">Semua TPQ</option>
                                            <?php foreach ($tpq_list as $tpq): ?>
                                                <?php
                                                $tpqId = is_array($tpq) ? $tpq['IdTpq'] : $tpq->IdTpq;
                                                $tpqNama = is_array($tpq) ? ($tpq['NamaTpq'] ?? '') : ($tpq->NamaTpq ?? '');
                                                ?>
                                                <option value="<?= $tpqId ?>" <?= ($selected_tpq === $tpqId) ? 'selected' : '' ?>>
                                                    <?= $tpqId ?> - <?= $tpqNama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="group">Grup Materi Ujian</label>
                                        <select name="group" id="group" class="form-control">
                                            <?php foreach ($groups as $group): ?>
                                                <option value="<?= $group['IdGrupMateriUjian'] ?>"
                                                    <?= ($selected_group === $group['IdGrupMateriUjian']) ? 'selected' : '' ?>>
                                                    <?= $group['NamaMateriGrup'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <!-- Jika admin TPQ, sembunyikan dropdown TPQ dan TypeUjian -->
                                    <input type="hidden" name="tpq" value="<?= $session_id_tpq ?>">
                                    <input type="hidden" id="type" name="type" value="pra-munaqosah">
                                    <div class="form-group col-md-9">
                                        <label for="group">Grup Materi Ujian</label>
                                        <select name="group" id="group" class="form-control">
                                            <?php foreach ($groups as $group): ?>
                                                <option value="<?= $group['IdGrupMateriUjian'] ?>"
                                                    <?= ($selected_group === $group['IdGrupMateriUjian']) ? 'selected' : '' ?>>
                                                    <?= $group['NamaMateriGrup'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <?php if (empty($session_id_tpq)): ?>
                                    <!-- Hanya tampilkan TypeUjian jika admin super, TahunAjaran disembunyikan untuk semua -->
                                    <div class="form-group col-md-3">
                                        <label for="type">Type Ujian</label>
                                        <select name="type" id="type" class="form-control">
                                            <?php foreach ($types as $typeValue => $typeLabel): ?>
                                                <option value="<?= $typeValue ?>" <?= ($selected_type === $typeValue) ? 'selected' : '' ?>>
                                                    <?= $typeLabel ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <!-- TahunAjaran disembunyikan untuk semua kondisi login -->
                                <input type="hidden" id="tahun" name="tahun" value="<?= $selected_tahun ?>">
                                <div class="form-group col-md-3 text-md-right">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- Input Registrasi -->
                        <div class="input-group my-4">
                            <input type="text" id="queueSearch" class="form-control" placeholder="Ketik atau scan QR no peserta untuk registrasi">
                            <div class="input-group-append">
                                <button class="btn btn-warning" type="button" id="btnScanQR">
                                    <i class="fas fa-qrcode"></i> Scan QR
                                </button>
                                <button class="btn btn-primary" type="button" id="btnQueueRegister">
                                    <i class="fas fa-user-plus"></i> Registrasi
                                </button>
                                <button class="btn btn-danger" type="button" id="btnQueueReset">Reset</button>
                            </div>
                        </div>
                        <small class="form-text text-muted mb-3">
                            <span class="text-info"><i class="fas fa-info-circle"></i> Auto registrasi akan aktif setelah 3 digit, atau tekan Enter</span>
                        </small>
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
                                        <span class="info-box-number"><?= $total ?></span>
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
                                        <span class="info-box-number"><?= $completed ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $pctCompleted ?>%"></div>
                                        </div>
                                        <span class="progress-description"><?= $pctCompleted ?>% selesai</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Antrian ujian</span>
                                        <span class="info-box-number"><?= $queueing ?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $pctQueueing ?>%"></div>
                                        </div>
                                        <span class="progress-description"><?= $pctQueueing ?>% menunggu</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Progress</span>
                                        <span class="info-box-number"><?= $progress ?>%</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                                        </div>
                                        <span class="progress-description">Tingkat penyelesaian</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Status Ruangan -->
                        <h5 class="mt-4">Status Ruangan</h5>
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
                                : 'col-lg-4 col-md-6';
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
                                                                        class="btn btn-light btn-finish-room"
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
                                                    <p class="mb-0">Kosong</p>
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
                                        <th>Group Peserta</th>
                                        <th>Tanggal Dibuat</th>
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
                                            <td>
                                                <?php
                                                $groupPeserta = $row['GroupPeserta'] ?? 'Group 1';
                                                // Ambil warna dari mapping dinamis, fallback ke secondary
                                                $badgeColor = $groupColorMap[$groupPeserta] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badgeColor ?>"><?= $groupPeserta ?></span>
                                            </td>
                                            <td><?= !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($status === 0): ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-open-room"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            In
                                                        </button>
                                                    <?php elseif ($status === 1): ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post" class="mr-1 form-update-status" data-confirm="Tandai peserta selesai?">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="2">
                                                            <button type="submit" class="btn btn-sm btn-success">Keluar</button>
                                                        </form>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post" class="form-update-status" data-confirm="Kembalikan peserta ke antrian menunggu?">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-sm btn-warning">Tunggu</button>
                                                        </form>
                                                    <?php elseif ($status === 2): ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post" class="form-update-status" data-confirm="Kembalikan peserta ke antrian menunggu?">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-sm btn-secondary">Tunggu</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('backend/munaqosah/delete-antrian/' . $row['id']) ?>"
                                                        class="btn btn-sm btn-outline-danger btn-delete-antrian"
                                                        data-confirm="Apakah Anda yakin ingin menghapus data ini?">
                                                        Del
                                                    </a>
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

<div class="modal fade" id="modalPilihRoom" tabindex="-1" role="dialog" aria-labelledby="modalPilihRoomLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPilihRoom" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihRoomLabel">Pilih Ruangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" value="1">
                    <div class="form-group">
                        <label>No Peserta</label>
                        <input type="text" id="modalNoPeserta" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Peserta</label>
                        <input type="text" id="modalNamaPeserta" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modalRoomId">Pilih Room</label>
                        <select name="room_id" id="modalRoomId" class="form-control" required>
                        </select>
                        <small class="form-text text-muted">Hanya menampilkan ruangan yang kosong.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Masukkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    const roomStatuses = <?= json_encode($rooms ?? []) ?>;

    $(function() {
        const table = $('#tableAntrian').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [
                [0, 'asc']
            ]
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
            $('#queueSearch').attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');

            const noPeserta = $('#queueSearch').val().trim();
            const idGrupMateri = $('#group').val();
            const typeUjian = $('#type').val();
            const tahunAjaran = $('#tahun').val();

            // Validasi input
            if (!noPeserta) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Masukkan nomor peserta terlebih dahulu'
                });
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

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mendaftarkan peserta ke antrian',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request AJAX
            $.ajax({
                url: '<?= base_url('backend/munaqosah/register-antrian-ajax') ?>',
                type: 'POST',
                data: {
                    NoPeserta: noPeserta,
                    IdGrupMateriUjian: idGrupMateri,
                    TypeUjian: typeUjian,
                    IdTahunAjaran: tahunAjaran,
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat registrasi'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat registrasi';
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
            $('#queueSearch').attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
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
            // Handle scan failure silently (don't show error for every scan attempt)
            // console.log('QR Scan failed:', error);
        }

        // Auto search when typing 3+ digits
        $('#queueSearch').on('input', function() {
            const noPeserta = $(this).val().trim();

            // Clear any existing timeout and countdown
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }

            // Show auto search indicator
            if (noPeserta.length >= 3) {
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
                $(this).attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
            }
        });

        // Clear auto search timeout and indicators when user focuses input
        $('#queueSearch').on('focus', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
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
            $(this).attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
        });

        // Event handler untuk form update status dengan SweetAlert2
        $('.form-update-status').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const confirmMessage = form.data('confirm') || 'Apakah Anda yakin?';

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmMessage,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form jika user confirm
                    form.off('submit'); // Remove event handler to prevent loop
                    form[0].submit(); // Submit form
                }
            });
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
                    // Redirect ke URL delete jika user confirm
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
                            // Reload halaman untuk memperbarui data
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
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request AJAX
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
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request AJAX
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
            // Hapus flag
            sessionStorage.removeItem('autoFocusQueueSearch');
            // Focus ke input setelah halaman selesai load
            setTimeout(function() {
                $('#queueSearch').focus();
            }, 500);
        }
    });
</script>
<?= $this->endSection() ?>