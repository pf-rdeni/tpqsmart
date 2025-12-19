<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Card Informasi Alur Proses -->
            <div class="col-12">
                <div class="card card-info collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Panduan Alur Proses Antrian Grup Materi Ujian
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                                <ol class="mb-4">
                                    <li class="mb-2">
                                        <strong>Filter Data Antrian:</strong>
                                        <ul class="mt-2">
                                            <li>Pilih <strong>TPQ</strong> (jika admin) atau otomatis terisi untuk TPQ/Panitia</li>
                                            <li>Pilih <strong>Grup Materi Ujian</strong> yang ingin dilihat antriannya</li>
                                            <li>Pilih <strong>Type Ujian</strong> (Munaqosah/Pra-Munaqosah) - otomatis terisi untuk Operator/Panitia</li>
                                            <li><strong>Tahun Ajaran</strong> otomatis terisi dan tidak dapat diubah</li>
                                            <li>Data antrian akan otomatis terfilter sesuai pilihan</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Registrasi Peserta ke Antrian:</strong>
                                        <ul class="mt-2">
                                            <li>Masukkan atau scan <strong>No Peserta</strong> di input field</li>
                                            <li>Gunakan tombol <span class="badge badge-warning"><i class="fas fa-qrcode"></i> Scan QR</span> untuk scan QR code dari kartu peserta</li>
                                            <li>Klik tombol <span class="badge badge-primary"><i class="fas fa-user-plus"></i> Registrasi</span> atau tekan <strong>Enter</strong></li>
                                            <li>Auto registrasi akan aktif setelah 3 digit dimasukkan</li>
                                            <li>Gunakan tombol <span class="badge badge-danger">Reset</span> untuk mengosongkan input</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Lihat Statistik Antrian:</strong>
                                        <ul class="mt-2">
                                            <li><strong>Total Peserta:</strong> Jumlah total peserta yang terdaftar di antrian</li>
                                            <li><strong>Sudah diuji:</strong> Jumlah peserta yang sudah selesai ujian (Status: Selesai)</li>
                                            <li><strong>Antrian ujian:</strong> Jumlah peserta yang menunggu untuk diuji (Status: Menunggu)</li>
                                            <li><strong>Progress:</strong> Persentase penyelesaian ujian (peserta selesai / total peserta)</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Monitor Status Ruangan:</strong>
                                        <ul class="mt-2">
                                            <li>Lihat daftar ruangan dan jumlah peserta per ruangan</li>
                                            <li>Status ruangan menampilkan:
                                                <ul>
                                                    <li>Peserta yang <strong>Sedang Ujian</strong> (Status: Proses) di ruangan tersebut</li>
                                                    <li>Peserta yang <strong>Menunggu</strong> (Status: Menunggu) untuk masuk ruangan</li>
                                                </ul>
                                            </li>
                                            <li>Gunakan tombol <span class="badge badge-success">Finish Room</span> untuk menyelesaikan semua peserta di ruangan</li>
                                            <li>Gunakan tombol <span class="badge badge-warning">Exit Room</span> untuk mengeluarkan peserta dari ruangan</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Kelola Antrian di Tabel:</strong>
                                        <ul class="mt-2">
                                            <li>Tabel menampilkan semua peserta dalam antrian dengan informasi:
                                                <ul>
                                                    <li><strong>Group Peserta:</strong> Grup pengelompokan peserta (dengan badge warna berbeda)</li>
                                                    <li><strong>No Peserta:</strong> Nomor peserta ujian</li>
                                                    <li><strong>Nama Peserta:</strong> Nama lengkap peserta</li>
                                                    <li><strong>Room:</strong> Ruangan tempat peserta ujian</li>
                                                    <li><strong>Status:</strong> Status peserta (Menunggu, Sedang Ujian, Selesai)</li>
                                                    <li><strong>Type Ujian:</strong> Jenis ujian (Munaqosah/Pra-Munaqosah)</li>
                                                    <li><strong>Tanggal Dibuat:</strong> Waktu registrasi ke antrian</li>
                                                </ul>
                                            </li>
                                            <li>Gunakan kolom <strong>Aksi</strong> untuk mengelola status peserta</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Fitur Tambahan:</strong>
                                        <ul class="mt-2">
                                            <li><strong>Auto Refresh:</strong> Aktifkan untuk refresh otomatis data antrian (10 detik - 5 menit)</li>
                                            <li><strong>Monitoring:</strong> Buka halaman monitoring di tab baru untuk tampilan layar penuh</li>
                                            <li><strong>Input Registrasi:</strong> Buka halaman khusus untuk input registrasi peserta</li>
                                        </ul>
                                    </li>
                                </ol>

                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                                    <ul class="mb-0">
                                        <li><strong>Status Antrian:</strong>
                                            <ul>
                                                <li><span class="badge badge-warning">Menunggu</span> = Peserta sudah terdaftar, menunggu giliran ujian</li>
                                                <li><span class="badge badge-danger">Sedang Ujian</span> = Peserta sedang dalam proses ujian di ruangan</li>
                                                <li><span class="badge badge-success">Selesai</span> = Peserta sudah selesai ujian</li>
                                            </ul>
                                        </li>
                                        <li><strong>Group Peserta:</strong> Setiap grup memiliki badge warna berbeda untuk memudahkan identifikasi</li>
                                        <li><strong>Scan QR Code:</strong> Gunakan fitur scan QR untuk registrasi cepat tanpa mengetik manual</li>
                                        <li><strong>Auto Refresh:</strong> Aktifkan auto refresh dengan interval 30 detik untuk update real-time tanpa refresh manual</li>
                                        <li><strong>Filter Berdasarkan Role:</strong>
                                            <ul>
                                                <li><strong>Admin:</strong> Dapat memilih semua TPQ dan Type Ujian</li>
                                                <li><strong>Operator:</strong> Hanya melihat Pra-Munaqosah, TPQ otomatis terisi</li>
                                                <li><strong>Panitia:</strong> Type Ujian otomatis sesuai IdTpq (munaqosah/pra-munaqosah)</li>
                                            </ul>
                                        </li>
                                        <li><strong>Status Ruangan:</strong> Monitor jumlah peserta per ruangan untuk mengatur alur ujian</li>
                                        <li><strong>Progress Tracking:</strong> Gunakan statistik progress untuk memantau tingkat penyelesaian ujian</li>
                                        <li><strong>Monitoring Screen:</strong> Buka di tab terpisah untuk tampilan layar besar (display monitor)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Antrian Grup Materi Ujian</h3>
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
                                <?php
                                // Tentukan role user
                                $isOperator = in_groups('Operator');
                                $isPanitia = in_groups('Panitia');
                                $isAdmin = in_groups('Admin');

                                // Gunakan variabel dari controller jika ada, jika tidak tentukan sendiri
                                $isPanitiaUmum = $is_panitia_umum ?? false;

                                // Untuk Panitia, tentukan TypeUjian berdasarkan IdTpq dari username
                                $panitiaTypeUjian = 'munaqosah'; // Default untuk panitia umum
                                if ($isPanitia) {
                                    if ($isPanitiaUmum) {
                                        $panitiaTypeUjian = 'munaqosah';
                                    } else {
                                        $panitiaTypeUjian = 'pra-munaqosah';
                                    }
                                }

                                // Tentukan apakah TypeUjian harus disabled
                                $typeDisabled = ($isOperator || $isPanitia) && !$isAdmin;
                                $typeRequiredValue = $isOperator ? 'pra-munaqosah' : ($isPanitia ? $panitiaTypeUjian : null);
                                $typeDisabledStyle = $typeDisabled ? 'background-color: #e9ecef; cursor: not-allowed;' : '';
                                ?>

                                <?php if (empty($session_id_tpq) && empty($is_panitia_tpq ?? false) && !$isPanitia): ?>
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
                                    <div class="form-group col-md-3">
                                        <label for="tahun">Tahun Ajaran</label>
                                        <input type="text" id="tahun" name="tahun" class="form-control" value="<?= $selected_tahun ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                    </div>
                                <?php elseif ($isPanitia && $isPanitiaUmum): ?>
                                    <!-- Jika panitia umum, tampilkan TypeUjian (disabled, default munaqosah) dan Grup Materi -->
                                    <input type="hidden" name="tpq" value="0">
                                    <div class="form-group col-md-4">
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
                                    <div class="form-group col-md-4">
                                        <label for="type">Type Ujian</label>
                                        <input type="hidden" name="type" id="type" value="munaqosah">
                                        <select class="form-control" disabled style="<?= $typeDisabledStyle ?>">
                                            <?php foreach ($types as $typeValue => $typeLabel): ?>
                                                <?php if ($typeValue === 'munaqosah'): ?>
                                                    <option value="<?= $typeValue ?>" selected>
                                                        <?= $typeLabel ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="tahun">Tahun Ajaran</label>
                                        <input type="text" id="tahun" name="tahun" class="form-control" value="<?= $selected_tahun ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                    </div>
                                <?php elseif (!empty($is_panitia_tpq ?? false) || ($isPanitia && !$isPanitiaUmum)): ?>
                                    <!-- Jika panitia TPQ, tampilkan TPQ mereka (disabled), TypeUjian (disabled, default pra-munaqosah) dan Grup Materi -->
                                    <div class="form-group col-md-3">
                                        <label for="tpq">TPQ</label>
                                        <input type="hidden" name="tpq" value="<?= $selected_tpq ?>">
                                        <select id="tpq" class="form-control" disabled style="background-color: #e9ecef; cursor: not-allowed;">
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
                                    <div class="form-group col-md-3">
                                        <label for="type">Type Ujian</label>
                                        <input type="hidden" name="type" id="type" value="pra-munaqosah">
                                        <select class="form-control" disabled style="<?= $typeDisabledStyle ?>">
                                            <?php foreach ($types as $typeValue => $typeLabel): ?>
                                                <?php if ($typeValue === 'pra-munaqosah'): ?>
                                                    <option value="<?= $typeValue ?>" selected>
                                                        <?= $typeLabel ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tahun">Tahun Ajaran</label>
                                        <input type="text" id="tahun" name="tahun" class="form-control" value="<?= $selected_tahun ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                    </div>
                                <?php else: ?>
                                    <!-- Jika admin TPQ atau Operator, tampilkan Grup Materi dan TypeUjian (disabled untuk Operator) -->
                                    <input type="hidden" name="tpq" value="<?= $session_id_tpq ?>">
                                    <div class="form-group col-md-4">
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
                                    <div class="form-group col-md-4">
                                        <label for="type">Type Ujian</label>
                                        <?php if ($isOperator): ?>
                                            <input type="hidden" name="type" id="type" value="pra-munaqosah">
                                            <select class="form-control" disabled style="<?= $typeDisabledStyle ?>">
                                                <?php foreach ($types as $typeValue => $typeLabel): ?>
                                                    <?php if ($typeValue === 'pra-munaqosah'): ?>
                                                        <option value="<?= $typeValue ?>" selected>
                                                            <?= $typeLabel ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="hidden" id="type" name="type" value="pra-munaqosah">
                                            <select class="form-control" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                                <?php foreach ($types as $typeValue => $typeLabel): ?>
                                                    <?php if ($typeValue === 'pra-munaqosah'): ?>
                                                        <option value="<?= $typeValue ?>" selected>
                                                            <?= $typeLabel ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="tahun">Tahun Ajaran</label>
                                        <input type="text" id="tahun" name="tahun" class="form-control" value="<?= $selected_tahun ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                    </div>
                                <?php endif; ?>
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
                                                        <th style="width: 15%;">No Peserta</th>
                                                        <th style="width: 35%;">Nama Santri</th>
                                                        <th style="width: 40%;" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($participants as $participant): ?>
                                                        <tr>
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
                                                                        title="Batal">
                                                                        <i class="fas fa-sign-out-alt"></i> Batal
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
                                                                        class="btn btn-success btn-finish-room"
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
                                                                        <i class="fas fa-sign-out-alt"></i> Batal
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
                                        <th class="no-sort">Aksi</th>
                                        <th class="no-sort">Group Peserta</th>
                                        <th class="no-sort">No Peserta</th>
                                        <th class="no-sort">Nama Peserta</th>
                                        <th class="no-sort">Room</th>
                                        <th class="no-sort">Status</th>
                                        <th class="no-sort">Type Ujian</th>
                                        <th class="no-sort">Tanggal Dibuat</th>
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
                                                            <button type="submit" class="btn btn-sm btn-success">Selesai</button>
                                                        </form>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post" class="form-update-status" data-confirm="Kembalikan peserta ke antrian menunggu?">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-sm btn-warning">Batal</button>
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
                                            <td>
                                                <?php
                                                $groupPeserta = $row['GroupPeserta'] ?? 'Group 1';
                                                // Ambil warna dari mapping dinamis, fallback ke secondary
                                                $badgeColor = $groupColorMap[$groupPeserta] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badgeColor ?>"><?= $groupPeserta ?></span>
                                            </td>
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
                                            <td><?= !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
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
        // Auto Apply Filter dengan localStorage
        const filterStorageKey = 'listAntrian_filter';
        let isInitializing = true; // Flag untuk mencegah auto submit saat inisialisasi
        let isUserChange = false; // Flag untuk mengetahui apakah perubahan dari user

        // Fungsi untuk mendapatkan parameter URL
        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                tpq: params.get('tpq') || '',
                group: params.get('group') || '',
                type: params.get('type') || '',
                tahun: params.get('tahun') || ''
            };
        }

        // Fungsi untuk menyimpan filter ke localStorage
        function saveFilterToStorage() {
            const filterData = {
                tpq: $('#tpq').length ? ($('#tpq').val() || '') : '',
                group: $('#group').val() || '',
                type: $('#type').length ? ($('#type').val() || '') : '',
                tahun: $('#tahun').val() || ''
            };
            localStorage.setItem(filterStorageKey, JSON.stringify(filterData));
        }

        // Fungsi untuk mendapatkan nilai filter saat ini dari form
        function getCurrentFilterValues() {
            return {
                tpq: $('#tpq').length ? ($('#tpq').val() || '') : '',
                group: $('#group').val() || '',
                type: $('#type').length ? ($('#type').val() || '') : '',
                tahun: $('#tahun').val() || ''
            };
        }

        // Fungsi untuk memuat filter dari localStorage ke form
        function loadFilterFromStorage() {
            const savedFilterStr = localStorage.getItem(filterStorageKey);
            if (!savedFilterStr) {
                return null;
            }

            try {
                return JSON.parse(savedFilterStr);
            } catch (e) {
                console.error('Error parsing filter from localStorage:', e);
                return null;
            }
        }

        // Fungsi untuk menerapkan filter ke form
        function applyFilterToForm(filterData) {
            if (filterData.tpq && $('#tpq').length) {
                $('#tpq').val(filterData.tpq);
            }
            if (filterData.group && $('#group').length) {
                $('#group').val(filterData.group);
            }
            if (filterData.type && $('#type').length) {
                $('#type').val(filterData.type);
            }
            if (filterData.tahun && $('#tahun').length) {
                $('#tahun').val(filterData.tahun);
            }
        }

        // Fungsi untuk apply filter (submit form)
        function applyFilter() {
            if (isInitializing) {
                return; // Jangan submit saat masih inisialisasi
            }
            saveFilterToStorage();
            $('form[method="get"]').submit();
        }

        // Cek apakah ada parameter URL
        const urlParams = getUrlParams();
        const hasUrlParams = urlParams.tpq || urlParams.group || urlParams.type || urlParams.tahun;

        // Jika ada parameter URL, gunakan parameter URL (prioritas) dan simpan ke localStorage
        if (hasUrlParams) {
            // Simpan parameter URL ke localStorage
            localStorage.setItem(filterStorageKey, JSON.stringify(urlParams));
        } else {
            // Jika tidak ada parameter URL, coba muat dari localStorage
            const savedFilter = loadFilterFromStorage();
            if (savedFilter) {
                // Terapkan filter dari localStorage ke form
                applyFilterToForm(savedFilter);

                // Cek apakah nilai dari localStorage berbeda dengan nilai default saat ini
                const currentValues = getCurrentFilterValues();
                const hasChanges = (
                    (savedFilter.tpq && savedFilter.tpq !== currentValues.tpq) ||
                    (savedFilter.group && savedFilter.group !== currentValues.group) ||
                    (savedFilter.type && savedFilter.type !== currentValues.type) ||
                    (savedFilter.tahun && savedFilter.tahun !== currentValues.tahun)
                );

                if (hasChanges) {
                    // Build URL dengan parameter filter dari localStorage
                    const baseUrl = '<?= base_url("backend/munaqosah/antrian") ?>';
                    const params = [];
                    if (savedFilter.tpq) params.push('tpq=' + encodeURIComponent(savedFilter.tpq));
                    if (savedFilter.group) params.push('group=' + encodeURIComponent(savedFilter.group));
                    if (savedFilter.type) params.push('type=' + encodeURIComponent(savedFilter.type));
                    if (savedFilter.tahun) params.push('tahun=' + encodeURIComponent(savedFilter.tahun));

                    if (params.length > 0) {
                        // Redirect ke URL dengan parameter filter
                        window.location.href = baseUrl + '?' + params.join('&');
                        return; // Keluar dari fungsi untuk mencegah eksekusi lebih lanjut
                    }
                } else {
                    // Jika tidak ada perubahan, tetap simpan nilai saat ini
                    saveFilterToStorage();
                }
            } else {
                // Jika tidak ada filter yang disimpan, simpan nilai saat ini
                saveFilterToStorage();
            }
        }

        // Set flag inisialisasi selesai setelah delay
        setTimeout(function() {
            isInitializing = false;
        }, 500);

        // Event handler untuk perubahan filter - Auto Apply dan Simpan ke localStorage
        $('#tpq, #group, #type').on('change', function() {
            if (!isInitializing) {
                isUserChange = true;
                // Simpan ke localStorage setiap kali ada perubahan
                saveFilterToStorage();
                applyFilter();
            }
        });

        const table = $('#tableAntrian').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            ordering: false,
            columnDefs: [{
                    orderable: false,
                    targets: '_all'
                },
                {
                    orderable: false,
                    targets: '.no-sort'
                }
            ]
        });

        // Nonaktifkan sorting secara paksa setelah DataTables diinisialisasi
        table.settings()[0].aoColumns.forEach(function(column, index) {
            column.bSortable = false;
        });

        // Nonaktifkan sorting di header tabel dengan CSS
        $('#tableAntrian thead th').css({
            'cursor': 'default !important',
            'user-select': 'none'
        });

        // Tambahkan style untuk mencegah indikator sorting dan pointer events hanya di header
        $('<style>')
            .prop('type', 'text/css')
            .html('#tableAntrian thead th { pointer-events: none !important; } #tableAntrian thead th.sorting:before, #tableAntrian thead th.sorting:after, #tableAntrian thead th.sorting_asc:before, #tableAntrian thead th.sorting_asc:after, #tableAntrian thead th.sorting_desc:before, #tableAntrian thead th.sorting_desc:after { display: none !important; }')
            .appendTo('head');

        // Mencegah event klik di header tabel dengan event delegation
        $(document).off('click', '#tableAntrian thead th').on('click', '#tableAntrian thead th', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });

        // Mencegah sorting dengan event handler yang lebih kuat
        $('#tableAntrian').off('order.dt').on('order.dt', function(e, settings) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        // Hapus class sorting dari DataTables dan pastikan tidak ada indikator sorting
        $('#tableAntrian thead th').removeClass('sorting sorting_asc sorting_desc sorting_disabled').addClass('sorting_disabled');

        // Catatan: Sorting sudah dinonaktifkan melalui:
        // 1. ordering: false di konfigurasi DataTable
        // 2. columnDefs dengan orderable: false untuk semua kolom
        // Tidak perlu memanggil orderable() karena sudah diatur di atas

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

                            // Tampilkan popup informasi - format standar
                            Swal.fire({
                                icon: 'info',
                                title: 'Peserta Sudah di Antrian',
                                html: `
                                    <div class="text-left">
                                        <div class="alert alert-info mb-3">
                                            <strong><i class="fas fa-info-circle"></i> Alasan:</strong> Peserta sudah terdaftar di antrian untuk grup ini
                                        </div>
                                        <p class="mb-3"><strong>Detail:</strong></p>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>No Peserta:</strong> ${queueData.no_peserta}</li>
                                            <li><strong>Nama:</strong> ${queueData.nama_santri}</li>
                                            <li><strong>Grup Materi:</strong> ${queueData.grup_materi}</li>
                                            <li><strong>Status:</strong> ${statusBadge}</li>
                                        </ul>
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
                        }
                        // Cek apakah peserta sudah selesai di grup yang sama
                        else if (response.already_completed && response.queue_data) {
                            const queueData = response.queue_data;
                            const statusBadge = '<span class="badge badge-success">Selesai</span>';
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Registrasi',
                                html: `
                                    <div class="text-left">
                                        <div class="alert alert-warning mb-3">
                                            <strong><i class="fas fa-exclamation-triangle"></i> Alasan:</strong> Peserta sudah menyelesaikan ujian di grup ini
                                        </div>
                                        <p class="mb-3"><strong>Detail:</strong></p>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>No Peserta:</strong> ${queueData.no_peserta}</li>
                                            <li><strong>Nama:</strong> ${queueData.nama_santri}</li>
                                            <li><strong>Grup Materi:</strong> ${queueData.grup_materi}</li>
                                            <li><strong>Status:</strong> ${statusBadge}</li>
                                        </ul>
                                    </div>
                                `,
                                confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                                confirmButtonColor: '#ffc107'
                            });
                        }
                        // Cek apakah peserta masih antri di grup lain
                        else if (response.blocked_by_other_group && response.conflict_data) {
                            const conflictData = response.conflict_data;
                            const statusBadge = conflictData.status == 1 ?
                                '<span class="badge badge-danger">Sedang Ujian</span>' :
                                '<span class="badge badge-warning">Menunggu</span>';

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Registrasi',
                                html: `
                                    <div class="text-left">
                                        <div class="alert alert-danger mb-3">
                                            <strong><i class="fas fa-times-circle"></i> Alasan:</strong> Peserta masih berada di antrian grup lain
                                        </div>
                                        <p class="mb-3"><strong>Detail:</strong></p>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>No Peserta:</strong> ${conflictData.no_peserta}</li>
                                            <li><strong>Nama:</strong> ${conflictData.nama_santri}</li>
                                            <li><strong>Grup Materi:</strong> ${conflictData.grup_materi_lain}</li>
                                            <li><strong>Status:</strong> ${statusBadge}</li>
                                        </ul>
                                        <div class="alert alert-info mt-3 mb-0">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                Selesaikan antrian di grup tersebut terlebih dahulu
                                            </small>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                        // Error biasa - format simple
                        else {
                            const errorMessage = response.message || 'Gagal registrasi';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Registrasi',
                                text: errorMessage,
                                confirmButtonText: '<i class="fas fa-check"></i> Mengerti',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Gagal registrasi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan pada server';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Registrasi',
                        text: errorMessage,
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

        // Event handler untuk button Batal dari room card
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
                text: `Batalkan peserta ${noPeserta} - ${nama} dari ruangan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan',
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