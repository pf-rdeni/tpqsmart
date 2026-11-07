<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check"></i> Absensi Santri
            </h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/absensi/ubahAbsensi') ?>" class="btn btn-warning btn-sm mr-1">
                    <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Ubah Absensi / Batal Libur</span>
                </a>
                <a href="<?= base_url('backend/santri/statistikAbsensi') ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-chart-bar"></i> <span class="d-none d-sm-inline">Detail Statistik</span>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
            // Tampilkan flash message jika ada
            if (session()->getFlashdata('success')) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<i class="fas fa-check-circle"></i> ' . session()->getFlashdata('success');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            if (session()->getFlashdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<i class="fas fa-exclamation-circle"></i> ' . session()->getFlashdata('error');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }

            // Ambil tanggal dari controller atau gunakan tanggal hari ini
            $tanggalHariIni = isset($tanggal_hari_ini) ? $tanggal_hari_ini : date('Y-m-d');
            $tanggalDipilih = isset($tanggal_dipilih) ? $tanggal_dipilih : $tanggalHariIni;

            // Gunakan kelas_list_all dari controller untuk menampilkan tab (semua kelas)
            // Jika tidak ada, buat dari santri yang ada
            $kelasList = isset($kelas_list_all) && !empty($kelas_list_all) ? $kelas_list_all : [];

            // Jika masih kosong, buat dari santri yang ada
            if (empty($kelasList)) {
                foreach ($santri as $row) {
                    if ((int)($row->IdKelas ?? 0) == 10 || stripos($row->NamaKelas ?? '', 'alumni') !== false) {
                        continue;
                    }
                    if (!isset($kelasList[$row->IdKelas])) {
                        $kelasList[$row->IdKelas] = [
                            'IdKelas' => $row->IdKelas,
                            'NamaKelas' => $row->NamaKelas,
                            'IdTahunAjaran' => $row->IdTahunAjaran,
                            'total_santri' => 0,
                            'total_laki' => 0,
                            'total_perempuan' => 0
                        ];
                    }
                    $kelasList[$row->IdKelas]['total_santri']++;
                    $jk = strtolower(trim($row->JenisKelamin ?? ''));
                    if ($jk === 'l' || stripos($jk, 'laki') !== false) {
                        $kelasList[$row->IdKelas]['total_laki']++;
                    } elseif ($jk === 'p' || stripos($jk, 'perempuan') !== false) {
                        $kelasList[$row->IdKelas]['total_perempuan']++;
                    }
                }
            }

            // Hitung total keseluruhan santri jika belum dihitung dari controller
            $totalSantriSemua = isset($total_santri_semua) ? $total_santri_semua : array_sum(array_column($kelasList, 'total_santri'));
            $totalLakiSemua = isset($total_laki_semua) ? $total_laki_semua : array_sum(array_column($kelasList, 'total_laki'));
            $totalPerempuanSemua = isset($total_perempuan_semua) ? $total_perempuan_semua : array_sum(array_column($kelasList, 'total_perempuan'));

            // Kelompokkan santri berdasarkan kelas
            $santriByKelas = [];
            foreach ($santri as $row) {
                $santriByKelas[$row->NamaKelas][] = $row;
            }

            // Tampilkan ringkasan total santri per kelas di bagian atas
            if (!empty($kelasList)) {
            ?>
                <!-- ========================================================= -->
                <!-- RINGKASAN TOTAL SANTRI PER KELAS (DITEMPATKAN DI ATAS)    -->
                <!-- ========================================================= -->
                <div class="card card-outline card-primary shadow-sm mb-3" id="ringkasan-total-santri">
                    <div class="card-header py-2 bg-light d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-primary mr-2" style="font-size: 0.95rem; padding: 6px 9px;">
                                <i class="fas fa-users"></i>
                            </span>
                            <div>
                                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1rem;">
                                    Total Santri per Kelas
                                </h3>
                                <small class="text-muted d-block d-sm-inline" style="font-size: 0.78rem;">
                                    Klik salah satu kotak kelas untuk langsung membuka daftar absensinya
                                </small>
                            </div>
                        </div>
                        <div class="card-tools d-flex flex-wrap align-items-center mt-2 mt-sm-0">
                            <span class="badge badge-primary px-2 py-1 mr-1" style="font-size: 0.85rem;" title="Total Semua Santri">
                                <i class="fas fa-user-graduate mr-1"></i> Total Semua: <strong><?= number_format($totalSantriSemua, 0, ',', '.') ?></strong> Santri
                            </span>
                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.8rem;" title="Komposisi Gender Keseluruhan">
                                <span class="text-white"><i class="fas fa-mars text-lightblue"></i> <?= $totalLakiSemua ?> L</span>
                                <span class="mx-1">·</span>
                                <span class="text-white"><i class="fas fa-venus text-pink"></i> <?= $totalPerempuanSemua ?> P</span>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-md-3 bg-light">
                        <div class="row">
                            <?php
                            $firstTab = true;
                            foreach ($kelasList as $kelas):
                                if ((int)($kelas['IdKelas'] ?? 0) == 10 || stripos($kelas['NamaKelas'] ?? '', 'alumni') !== false) continue;
                                $jml = (int)($kelas['total_santri'] ?? 0);
                                $jmlL = (int)($kelas['total_laki'] ?? 0);
                                $jmlP = (int)($kelas['total_perempuan'] ?? 0);
                                $idKelas = $kelas['IdKelas'];
                                $statAbsen = isset($status_absensi_kelas[$idKelas]) ? $status_absensi_kelas[$idKelas] : null;
                                $totAbsen = $statAbsen ? (int)$statAbsen['total_absen'] : 0;
                                $h = $statAbsen ? (int)$statAbsen['hadir'] : 0;
                                $s = $statAbsen ? (int)$statAbsen['sakit'] : 0;
                                $i = $statAbsen ? (int)$statAbsen['izin'] : 0;
                                $a = $statAbsen ? (int)$statAbsen['alfa'] : 0;
                            ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2">
                                    <div class="card class-summary-card h-100 mb-0 shadow-sm <?= $firstTab ? 'active-summary-card' : '' ?>"
                                         data-kelas-id="<?= $kelas['IdKelas'] ?>"
                                         role="button"
                                         tabindex="0"
                                         title="Klik untuk membuka absensi kelas <?= esc($kelas['NamaKelas']) ?>">
                                        <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                            <div class="class-summary-title font-weight-bold text-truncate mb-1" title="<?= esc($kelas['NamaKelas']) ?>">
                                                <?= esc($kelas['NamaKelas']) ?>
                                            </div>
                                            <div class="my-1">
                                                <div class="class-total-number <?= $jml > 0 ? 'text-primary' : 'text-muted' ?>">
                                                    <?= number_format($jml, 0, ',', '.') ?>
                                                </div>
                                                <span class="badge badge-pill <?= $jml > 0 ? 'badge-light text-muted border' : 'badge-danger' ?>" style="font-size: 0.72rem;">
                                                    <?= $jml > 0 ? 'Santri' : '0 Santri' ?>
                                                </span>
                                            </div>

                                            <!-- Status Presensi Sesuai Tanggal Dipilih -->
                                            <div class="class-absensi-info my-1 py-1 border-top" id="card-absensi-info-<?= $idKelas ?>">
                                                <?php if ($totAbsen == 0): ?>
                                                    <span class="badge py-1 px-2 mb-1" style="font-size: 0.72rem; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                                        <i class="fas fa-times-circle mr-1 text-danger"></i>Belum Diabsen
                                                    </span>
                                                    <div class="text-muted text-xs font-italic">0 catatan presensi</div>
                                                <?php else: ?>
                                                    <?php
                                                    $isSemua = ($jml > 0 && $totAbsen >= $jml);
                                                    $statusLabel = $isSemua ? 'Sudah Diabsen' : "Sebagian ({$totAbsen}/{$jml})";
                                                    $badgeClass = $isSemua ? 'badge-success' : 'badge-info';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> py-1 px-2 mb-1" style="font-size: 0.72rem;">
                                                        <i class="fas fa-check-circle mr-1"></i><?= $statusLabel ?>
                                                    </span>
                                                    <div class="d-flex justify-content-center align-items-center mt-1" style="gap: 3px; font-size: 0.72rem;">
                                                        <span class="badge badge-success px-1" title="Hadir: <?= $h ?>">H: <?= $h ?></span>
                                                        <span class="badge badge-warning px-1" title="Izin: <?= $i ?>">I: <?= $i ?></span>
                                                        <span class="badge badge-info px-1" title="Sakit: <?= $s ?>">S: <?= $s ?></span>
                                                        <span class="badge badge-danger px-1" title="Alfa: <?= $a ?>">A: <?= $a ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php
                                                $infoBelumKls = $tanggal_belum_absen_info['kelas_data'][$idKelas] ?? null;
                                                $jmlBelumTgl = $infoBelumKls ? (int)$infoBelumKls['total_belum_absen'] : 0;
                                                ?>
                                                <div class="mt-1" id="badge-tertinggal-<?= $idKelas ?>">
                                                    <?php if ($jmlBelumTgl > 0): ?>
                                                        <span class="badge badge-warning text-xs px-1 py-0" title="<?= $jmlBelumTgl ?> tanggal aktif belum diabsen bulan ini" style="font-size: 0.68rem;">
                                                            <i class="fas fa-exclamation-triangle"></i> <?= $jmlBelumTgl ?> tgl kosong
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="class-gender-info text-muted border-top pt-1 mt-1 d-flex justify-content-around">
                                                <span title="Laki-laki" class="text-info font-weight-500">
                                                    <i class="fas fa-mars"></i> <?= $jmlL ?>
                                                </span>
                                                <span class="text-muted">·</span>
                                                <span title="Perempuan" class="text-danger font-weight-500">
                                                    <i class="fas fa-venus"></i> <?= $jmlP ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $firstTab = false;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="custom-tabs-one-tab" role="tablist">
                            <?php
                            $firstTab = true;
                            foreach ($kelasList as $index => $kelas): 
                                if ((int)($kelas['IdKelas'] ?? 0) == 10 || stripos($kelas['NamaKelas'] ?? '', 'alumni') !== false) continue;
                            ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $firstTab ? 'active' : '' ?>"
                                        id="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>-tab"
                                        data-toggle="pill"
                                        href="#custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                        role="tab"
                                        aria-controls="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                        aria-selected="<?= $firstTab ? 'true' : 'false' ?>">
                                        <?= htmlspecialchars($kelas['NamaKelas']) ?>
                                        <span class="badge badge-pill <?= $firstTab ? 'badge-primary' : 'badge-light border' ?> ml-1 tab-santri-badge" id="tab-badge-<?= $kelas['IdKelas'] ?>" title="<?= (int)($kelas['total_santri'] ?? 0) ?> Santri">
                                            <?= (int)($kelas['total_santri'] ?? 0) ?>
                                        </span>
                                    </a>
                                </li>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <?php
                            $firstTab = true;
                            foreach ($kelasList as $kelas):
                                if ((int)($kelas['IdKelas'] ?? 0) == 10 || stripos($kelas['NamaKelas'] ?? '', 'alumni') !== false) continue;
                                // Ambil santri untuk kelas ini, jika tidak ada gunakan array kosong
                                $santriList = isset($santriByKelas[$kelas['NamaKelas']]) ? $santriByKelas[$kelas['NamaKelas']] : [];
                            ?>
                                <div class="tab-pane fade <?= $firstTab ? 'show active' : '' ?>"
                                    id="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                    role="tabpanel"
                                    aria-labelledby="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>-tab">

                                    <form action="<?= base_url('/backend/absensi/simpanAbsensi') ?>" method="post" id="formAbsensi<?= $kelas['IdKelas'] ?>">
                                        <!-- Date Input -->
                                        <div class="form-group mb-3">
                                            <label for="tanggal<?= $kelas['IdKelas'] ?>">
                                                <i class="fas fa-calendar-alt"></i> Tanggal:
                                            </label>
                                            <input type="date"
                                                name="tanggal"
                                                id="tanggal<?= $kelas['IdKelas'] ?>"
                                                value="<?= $tanggalDipilih; ?>"
                                                class="form-control form-control-lg tanggal-input"
                                                data-kelas-id="<?= $kelas['IdKelas'] ?>"
                                                style="-webkit-appearance: none; -moz-appearance: textfield; appearance: none;"
                                                required>
                                        </div>

                                        <!-- Card Analisis Tanggal Belum Diabsen (Baseline Opsi B) -->
                                        <?php
                                        $idKlsCur = (int)$kelas['IdKelas'];
                                        $infoKlsCur = $tanggal_belum_absen_info['kelas_data'][$idKlsCur] ?? [
                                            'total_hari_aktif_tpq' => 0,
                                            'total_sudah_absen' => 0,
                                            'total_belum_absen' => 0,
                                            'belum_absen_list' => [],
                                            'grouped_by_month' => []
                                        ];
                                        $periodeStr = $tanggal_belum_absen_info['periode'] ?? 'bulan';
                                        $periodeLabelStr = $tanggal_belum_absen_info['periode_label'] ?? ('Bulan ' . date('F Y', strtotime($tanggalDipilih)));
                                        $isSemesterView = ($periodeStr === 'semester');
                                        ?>
                                        <div class="card shadow-sm mb-3 card-tanggal-belum-absen" id="card-belum-absen-<?= $kelas['IdKelas'] ?>" style="border-left: 4px solid #ffc107; background: #fffdf5;">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                                    <div class="mb-2 mb-md-0">
                                                        <span class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                                            <i class="fas fa-calendar-check text-warning mr-1"></i>
                                                            Kelengkapan Absensi: <span class="badge-periode-label-<?= $kelas['IdKelas'] ?> text-primary font-weight-bold"><?= esc($periodeLabelStr) ?></span>
                                                        </span>
                                                        <span class="badge badge-light border text-muted ml-1" style="font-size: 0.78rem;">
                                                            Baseline Opsi B: <strong class="badge-sudah-<?= $kelas['IdKelas'] ?>"><?= $infoKlsCur['total_sudah_absen'] ?></strong> / <strong class="badge-aktif-<?= $kelas['IdKelas'] ?>"><?= $infoKlsCur['total_hari_aktif_tpq'] ?></strong> Hari Aktif TPQ
                                                        </span>
                                                    </div>

                                                    <!-- Filter Periode: Minggu Ini, Minggu Kemarin, Per Bulan, Semester -->
                                                    <div class="btn-group btn-group-sm periode-filter-group shadow-xs" role="group">
                                                        <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'minggu_ini') ? 'active font-weight-bold' : '' ?>" data-periode="minggu_ini" data-kelas-id="<?= $kelas['IdKelas'] ?>">Minggu Ini</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'minggu_kemarin') ? 'active font-weight-bold' : '' ?>" data-periode="minggu_kemarin" data-kelas-id="<?= $kelas['IdKelas'] ?>">Minggu Lalu</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'bulan') ? 'active font-weight-bold' : '' ?>" data-periode="bulan" data-kelas-id="<?= $kelas['IdKelas'] ?>">Per Bulan</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'semester') ? 'active font-weight-bold' : '' ?>" data-periode="semester" data-kelas-id="<?= $kelas['IdKelas'] ?>">Per Semester</button>
                                                    </div>
                                                </div>

                                                <div class="content-belum-absen-<?= $kelas['IdKelas'] ?>">
                                                    <?php if ($infoKlsCur['total_belum_absen'] > 0): ?>
                                                        <p class="text-muted mb-2" style="font-size: 0.82rem;">
                                                            <i class="fas fa-exclamation-circle text-danger mr-1"></i>
                                                            Ditemukan <strong class="text-danger"><?= $infoKlsCur['total_belum_absen'] ?> tanggal aktif TPQ</strong> yang belum tercatat absensinya pada kelas ini. 
                                                            <strong>Klik tanggal di bawah</strong> untuk langsung membuka dan menginput absensi:
                                                        </p>

                                                        <?php if ($isSemesterView && !empty($infoKlsCur['grouped_by_month'])): ?>
                                                            <!-- Grouping per Bulan untuk Overview Periode Panjang / Semester (Default Collapsed) -->
                                                            <div class="semester-month-groups">
                                                                <?php foreach ($infoKlsCur['grouped_by_month'] as $mGroup): 
                                                                    $hasPending = (!$mGroup['is_lengkap'] && !empty($mGroup['tanggal_list']));
                                                                ?>
                                                                    <div class="month-group-card mb-2 border rounded bg-white shadow-xs">
                                                                        <div class="month-header d-flex justify-content-between align-items-center p-2 <?= $hasPending ? 'cursor-pointer' : '' ?>">
                                                                            <div class="d-flex align-items-center">
                                                                                <?php if ($hasPending): ?>
                                                                                    <i class="fas fa-chevron-right text-muted mr-2 chevron-icon" style="font-size: 0.75rem;"></i>
                                                                                <?php else: ?>
                                                                                    <i class="fas fa-check text-success mr-2" style="font-size: 0.75rem;"></i>
                                                                                <?php endif; ?>
                                                                                <span class="font-weight-bold text-dark" style="font-size: 0.84rem;">
                                                                                    <i class="fas fa-calendar-alt text-secondary mr-1"></i> <?= esc($mGroup['bulan_label']) ?>
                                                                                </span>
                                                                            </div>
                                                                            <div>
                                                                                <span class="badge <?= $mGroup['is_lengkap'] ? 'badge-success' : 'badge-warning' ?>" style="font-size: 0.72rem;">
                                                                                    <?= $mGroup['is_lengkap'] ? 'Lengkap (' . $mGroup['total_aktif'] . '/' . $mGroup['total_aktif'] . ' hari)' : ($mGroup['total_belum'] . ' hari tertinggal') ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ($hasPending): ?>
                                                                            <div class="month-collapse-body p-2 border-top bg-light" style="display: none;">
                                                                                <small class="text-muted d-block mb-1 font-italic" style="font-size: 0.75rem;">Pilih tanggal untuk diisi presensinya:</small>
                                                                                <div class="d-flex flex-wrap" style="gap: 5px;">
                                                                                    <?php foreach ($mGroup['tanggal_list'] as $tglItem): ?>
                                                                                        <button type="button" 
                                                                                                class="btn btn-outline-danger btn-sm btn-pilih-tanggal-kosong px-2 py-1 shadow-xs" 
                                                                                                data-tanggal="<?= $tglItem['tanggal'] ?>" 
                                                                                                data-kelas-id="<?= $kelas['IdKelas'] ?>" 
                                                                                                title="Klik untuk langsung membuka absensi tanggal <?= $tglItem['label'] ?>">
                                                                                            <i class="fas fa-calendar-day text-danger mr-1"></i>
                                                                                            <strong><?= $tglItem['badge_label'] ?></strong>
                                                                                        </button>
                                                                                    <?php endforeach; ?>
                                                                                </div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <!-- Tampilan Flat untuk Periode Mingguan atau Bulanan -->
                                                            <div class="d-flex flex-wrap" style="gap: 6px;">
                                                                <?php foreach ($infoKlsCur['belum_absen_list'] as $tglItem): ?>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-danger btn-sm btn-pilih-tanggal-kosong px-2 py-1 shadow-xs" 
                                                                            data-tanggal="<?= $tglItem['tanggal'] ?>" 
                                                                            data-kelas-id="<?= $kelas['IdKelas'] ?>" 
                                                                            title="Klik untuk langsung membuka absensi tanggal <?= $tglItem['label'] ?>">
                                                                        <i class="fas fa-calendar-day text-danger mr-1"></i>
                                                                        <strong><?= $tglItem['badge_label'] ?></strong>
                                                                    </button>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    <?php elseif ($infoKlsCur['total_hari_aktif_tpq'] > 0): ?>
                                                        <div class="text-success font-weight-500 py-1" style="font-size: 0.85rem;">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Alhamdulillah! Seluruh tanggal aktif TPQ (<strong><?= $infoKlsCur['total_hari_aktif_tpq'] ?> hari</strong>) pada <?= esc($periodeLabelStr) ?> sudah lengkap diabsen untuk kelas ini.
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-muted font-italic py-1" style="font-size: 0.85rem;">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            Belum ada catatan aktivitas presensi TPQ pada <?= esc($periodeLabelStr) ?>.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Action: Set All Hadir & Info Santri Kelas -->
                                        <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <button type="button" class="btn btn-success btn-sm" onclick="setAllHadir(<?= $kelas['IdKelas'] ?>)">
                                                    <i class="fas fa-check-double"></i> Set Semua Hadir
                                                </button>
                                                <small class="text-muted ml-2">
                                                    <i class="fas fa-info-circle"></i> Default: Semua santri di-set sebagai Hadir
                                                </small>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <span class="badge badge-light border px-2 py-1 text-dark" style="font-size: 0.85rem;" id="badge-total-kelas-<?= $kelas['IdKelas'] ?>">
                                                    <i class="fas fa-users text-primary mr-1"></i> Total Santri: <strong><?= (int)($kelas['total_santri'] ?? 0) ?></strong>
                                                    <span class="text-muted ml-1">(<?= (int)($kelas['total_laki'] ?? 0) ?> L, <?= (int)($kelas['total_perempuan'] ?? 0) ?> P)</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Tambahkan hidden input untuk menyimpan IdKelas, IdGuru, IdTahunAjaran -->
                                        <input type="hidden" name="IdKelas" value="<?= $kelas['IdKelas'] ?>">
                                        <input type="hidden" name="IdGuru" value="<?= session()->get('IdGuru') ?>">
                                        <input type="hidden" name="IdTahunAjaran" value="<?= $kelas['IdTahunAjaran'] ?>">

                                        <!-- Cards View untuk semua ukuran layar -->
                                        <div id="cards-container-<?= $kelas['IdKelas'] ?>" class="cards-container">
                                            <?php if ((int)($kelas['total_santri'] ?? 0) === 0): ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Belum ada santri terdaftar di kelas <?= esc($kelas['NamaKelas']) ?> pada tahun ajaran ini.
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                // Cek apakah kelas ini sudah memiliki data absensi pada tanggal ini
                                                $statAbsen = isset($status_absensi_kelas[$kelas['IdKelas']]) ? $status_absensi_kelas[$kelas['IdKelas']] : null;
                                                $totAbsen = $statAbsen ? (int)$statAbsen['total_absen'] : 0;
                                                $jmlSantriKelas = (int)($kelas['total_santri'] ?? 0);
                                                ?>
                                                <?php if ($totAbsen > 0): ?>
                                                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                                            <strong>Kelas ini sudah diabsen</strong> pada tanggal <?= date('d-m-Y', strtotime($tanggalDipilih)) ?>
                                                            <?php
                                                            if (isset($guru_absensi[$kelas['IdKelas']])) {
                                                                echo ' (Oleh: <strong>' . esc($guru_absensi[$kelas['IdKelas']]) . '</strong>)';
                                                            }
                                                            ?>.
                                                            <span class="text-muted d-block d-sm-inline ml-sm-1">Anda dapat melihat atau memperbarui status kehadiran di bawah jika ada perubahan.</span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (empty($santriList)): ?>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-info-circle"></i> Tidak ada data santri yang dapat ditampilkan untuk kelas ini.
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($santriList as $row): 
                                                        $curKehadiran = $row->Kehadiran ?? 'Hadir';
                                                        if ($curKehadiran === 'Pra-Masuk') {
                                                            $curKehadiran = 'Hadir'; // Default UI jika dibuka untuk diedit guru
                                                        }
                                                        $curKeterangan = $row->Keterangan ?? '';
                                                        $isHadir = ($curKehadiran === 'Hadir');
                                                        $isIzin = ($curKehadiran === 'Izin');
                                                        $isSakit = ($curKehadiran === 'Sakit');
                                                        $isAlfa = in_array($curKehadiran, ['Alfa', 'Alpa']);
                                                    ?>
                                                        <div class="card mb-1 shadow-sm">
                                                            <div class="card-body">
                                                                <h5 class="card-title mb-2 d-flex align-items-center">
                                                                    <?php
                                                                    $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                                                    $thumbnailPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/');
                                                                    $photoProfil = $row->PhotoProfil ?? '';
                                                                    ?>
                                                                    <?php if ($photoProfil): ?>
                                                                        <img src="<?= $thumbnailPath . 'thumb_' . $photoProfil; ?>"
                                                                            alt="PhotoProfil"
                                                                            class="photo-profil-thumbnail mr-2"
                                                                            loading="lazy"
                                                                            style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                                        <div class="image-popup" style="display: none; position: absolute; z-index: 1000;">
                                                                            <img src="<?= $uploadPath . $photoProfil; ?>"
                                                                                alt="PhotoProfil"
                                                                                width="200"
                                                                                height="250"
                                                                                loading="lazy"
                                                                                class="rounded shadow">
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <i class="fas fa-user-circle mr-2" style="font-size: 40px; color: #6c757d;"></i>
                                                                    <?php endif; ?>
                                                                    <?= esc($row->NamaSantri) ?>
                                                                </h5>

                                                                <!-- Kehadiran Options -->
                                                                <div class="mb-1">
                                                                    <div class="btn-group w-100" role="group">
                                                                        <label class="btn btn-success absensi-btn-mobile <?= $isHadir ? 'active' : '' ?>">
                                                                            <input type="radio"
                                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                                value="Hadir"
                                                                                autocomplete="off"
                                                                                <?= $isHadir ? 'checked' : '' ?>>
                                                                            <i class="fas fa-check-circle"></i> Hadir
                                                                        </label>
                                                                        <label class="btn btn-warning absensi-btn-mobile <?= $isIzin ? 'active' : '' ?>">
                                                                            <input type="radio"
                                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                                value="Izin"
                                                                                autocomplete="off"
                                                                                <?= $isIzin ? 'checked' : '' ?>>
                                                                            <i class="fas fa-info-circle"></i> Izin
                                                                        </label>
                                                                        <label class="btn btn-info absensi-btn-mobile <?= $isSakit ? 'active' : '' ?>">
                                                                            <input type="radio"
                                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                                value="Sakit"
                                                                                autocomplete="off"
                                                                                <?= $isSakit ? 'checked' : '' ?>>
                                                                            <i class="fas fa-thermometer-half"></i> Sakit
                                                                        </label>
                                                                        <label class="btn btn-danger absensi-btn-mobile <?= $isAlfa ? 'active' : '' ?>">
                                                                            <input type="radio"
                                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                                value="Alfa"
                                                                                autocomplete="off"
                                                                                <?= $isAlfa ? 'checked' : '' ?>>
                                                                            <i class="fas fa-times-circle"></i> Alfa
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <!-- Keterangan Field -->
                                                                <div class="form-group keterangan-field" id="keterangan-field-<?= $row->IdSantri ?>" style="<?= (!$isHadir || !empty($curKeterangan)) ? '' : 'display: none;' ?>">
                                                                    <label for="keterangan-<?= $row->IdSantri ?>">
                                                                        <i class="fas fa-comment-alt"></i> Keterangan:
                                                                    </label>
                                                                    <input type="text"
                                                                        name="keterangan[<?= $row->IdSantri ?>]"
                                                                        id="keterangan-<?= $row->IdSantri ?>"
                                                                        value="<?= esc($curKeterangan) ?>"
                                                                        class="form-control form-control-lg"
                                                                        placeholder="Masukkan keterangan (opsional)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Submit Button - selalu tampil jika ada santri -->
                                        <div class="mt-4" id="submit-button-container-<?= $kelas['IdKelas'] ?>" style="<?= (empty($santriList) && (int)($kelas['total_santri'] ?? 0) === 0) ? 'display: none;' : '' ?>">
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                                <i class="fas fa-save mr-1"></i> Simpan / Perbarui Absensi Kelas <?= esc($kelas['NamaKelas']) ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    /* Ringkasan Total Santri per Kelas di bagian atas */
    .class-summary-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        border-radius: 8px !important;
        background-color: #ffffff;
        border: 2px solid #dee2e6 !important;
        border-left: 2px solid #dee2e6 !important;
        margin-bottom: 0 !important;
    }

    .class-summary-card .card-body {
        padding: 0.6rem 0.5rem !important;
    }

    .class-summary-card:hover {
        transform: translateY(-2px);
        border-color: #007bff !important;
        border-left-color: #007bff !important;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15) !important;
    }

    .class-summary-card.active-summary-card {
        border-color: #007bff !important;
        border-left-color: #007bff !important;
        background-color: #f4f8ff;
        box-shadow: 0 3px 8px rgba(0, 123, 255, 0.2) !important;
    }

    .class-summary-card.active-summary-card .class-summary-title {
        color: #007bff;
    }

    .class-summary-card .class-total-number {
        font-size: 1.55rem;
        line-height: 1.1;
        font-weight: 700;
    }

    .class-summary-card .class-summary-title {
        font-size: 0.84rem;
        color: #495057;
    }

    .class-summary-card .class-gender-info {
        font-size: 0.72rem;
    }

    .tab-santri-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.55em;
        vertical-align: middle;
    }

    /* Card Analisis Tanggal Belum Diabsen (Baseline Opsi B) */
    .card-tanggal-belum-absen {
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out;
    }
    .periode-filter-group .btn-filter-periode {
        font-size: 0.76rem !important;
        padding: 0.22rem 0.55rem !important;
        border-color: #ced4da !important;
        background-color: #ffffff;
        color: #495057;
    }
    .periode-filter-group .btn-filter-periode:hover {
        background-color: #f1f3f5;
        color: #007bff;
    }
    .periode-filter-group .btn-filter-periode.active {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #ffffff !important;
    }
    .month-group-card {
        border-left: 3px solid #6c757d !important;
        transition: all 0.15s ease-in-out;
    }
    .month-group-card:hover {
        border-left-color: #007bff !important;
    }
    .month-header.cursor-pointer {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease-in-out;
    }
    .month-header.cursor-pointer:hover {
        background-color: #f8f9fa;
    }
    .chevron-icon {
        transition: transform 0.2s ease-in-out;
    }
    .month-header.active-collapse .chevron-icon {
        transform: rotate(90deg);
    }
    .btn-pilih-tanggal-kosong {
        transition: all 0.15s ease-in-out;
        border-radius: 6px;
        font-weight: 500;
    }
    .btn-pilih-tanggal-kosong:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(220, 53, 69, 0.25);
    }

    /* Mobile-friendly styles */
    .absensi-btn {
        min-width: 80px;
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
        position: relative;
        overflow: visible;
    }

    .absensi-btn-mobile {
        min-height: 48px;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-weight: 500;
        position: relative;
        overflow: visible;
        flex: 1;
    }

    .absensi-btn-mobile i {
        margin-right: 6px;
        font-size: 1rem;
        width: auto;
        text-align: center;
    }

    .absensi-btn i {
        margin-right: 5px;
    }

    /* Active state styling */
    .absensi-btn.active,
    .absensi-btn-mobile.active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
        font-weight: 600;
        border-width: 2px;
    }


    /* Hover effect for better UX */
    .absensi-btn:hover:not(.active),
    .absensi-btn-mobile:hover:not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Card styling for mobile */
    .card.shadow-sm {
        border-left: 4px solid #007bff;
        margin-bottom: 0.5rem !important;
    }

    .card.shadow-sm .card-body {
        padding: 0.75rem 1rem !important;
    }

    .card.shadow-sm .card-title {
        margin-bottom: 0.75rem !important;
        font-size: 0.95rem;
    }

    .card.shadow-sm .btn-group {
        margin-bottom: 0.5rem !important;
    }

    /* Form control styling */
    .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    /* Keterangan field styling dengan transisi */
    .keterangan-field {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    /* Photo profil styling */
    .photo-profil-thumbnail {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 50% !important;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        object-fit: cover !important;
        object-position: center !important;
        border: 2px solid #dee2e6;
        transition: transform 0.2s ease, border-color 0.2s ease;
        display: block !important;
        flex-shrink: 0;
        padding: 0 !important;
        margin: 0 !important;
    }

    .photo-profil-thumbnail:hover {
        transform: scale(1.1);
        border-color: #007bff;
    }

    .image-popup {
        background: white;
        border: 2px solid #007bff;
        border-radius: 8px;
        padding: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        position: absolute;
        top: 50px;
        left: 0;
        z-index: 1000;
    }

    .image-popup img {
        display: block;
        max-width: 200px;
        max-height: 250px;
        object-fit: cover;
    }

    .card-title {
        position: relative;
    }

    /* Button group styling */
    .btn-group {
        overflow: visible;
    }

    .btn-group .btn {
        overflow: visible;
    }

    /* Table responsive improvements */
    @media (max-width: 768px) {
        .table-responsive {
            border: none;
        }
    }

    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        .absensi-btn {
            min-height: 48px;
            min-width: 90px;
            padding: 0.75rem 1rem;
        }

        .absensi-btn-mobile {
            min-height: 48px;
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .absensi-btn-mobile i {
            margin-right: 4px;
            font-size: 0.9rem;
        }
    }

    /* Mobile specific improvements */
    @media (max-width: 768px) {
        .card.shadow-sm {
            margin-bottom: 1rem;
            border-radius: 0.75rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .form-control-lg {
            font-size: 16px;
            /* Prevents zoom on iOS */
            padding: 0.875rem 1rem;
        }

        /* Better spacing for mobile cards */
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem !important;
        }

        .card.shadow-sm {
            margin-bottom: 0.5rem !important;
        }

        .card.shadow-sm .card-body {
            padding: 0.75rem 1rem !important;
        }

        .card.shadow-sm .form-group {
            margin-bottom: 0.5rem !important;
        }

        /* Button horizontal untuk mobile */
        .absensi-btn-mobile {
            min-height: 48px;
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .absensi-btn-mobile i {
            margin-right: 4px;
            font-size: 0.9rem;
        }
    }

    /* Fix date input for mobile */
    input[type="date"] {
        position: relative;
        z-index: 1;
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background: transparent;
        cursor: pointer;
        z-index: 2;
    }

    /* Ensure date input is clickable on mobile */
    @media (max-width: 768px) {
        input[type="date"] {
            min-height: 48px;
            font-size: 16px;
            /* Prevents zoom on iOS */
            padding-right: 40px;
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Function untuk menampilkan popup photo profil
    function showPopup(img) {
        const popup = img.nextElementSibling;
        if (popup && popup.classList.contains('image-popup')) {
            // Sembunyikan semua popup lainnya terlebih dahulu
            $('.image-popup').hide();
            // Tampilkan popup yang sesuai
            $(popup).fadeIn(200);
        }
    }

    // Function untuk menyembunyikan popup photo profil
    function hidePopup(img) {
        // Delay sedikit untuk memungkinkan mouse move ke popup
        setTimeout(function() {
            const popup = img.nextElementSibling;
            if (popup && popup.classList.contains('image-popup')) {
                // Cek apakah mouse masih di atas popup atau image
                if (!$(popup).is(':hover') && !$(img).is(':hover')) {
                    $(popup).fadeOut(200);
                }
            }
        }, 200);
    }

    // Function to update active state for radio button labels
    function updateActiveState() {
        // Hapus class active dari semua button terlebih dahulu
        $('.absensi-btn, .absensi-btn-mobile').removeClass('active');

        // Tambahkan class active hanya pada button yang checked
        $('input[type="radio"][name^="kehadiran"]:checked').each(function() {
            $(this).closest('label').addClass('active');
        });
    }

    // Function to toggle keterangan field based on kehadiran status
    function toggleKeteranganField() {
        $('input[type="radio"][name^="kehadiran"]:checked').each(function() {
            var name = $(this).attr('name');
            var match = name.match(/\[(\d+)\]/);
            if (!match) return;
            var id = match[1];
            var status = $(this).val();
            var keteranganField = $('#keterangan-field-' + id);
            var keteranganInput = $('#keterangan-' + id);

            // Tampilkan field keterangan jika status bukan "Hadir" atau jika keterangan ada isinya
            if (status !== 'Hadir' || (keteranganInput.val() && keteranganInput.val().trim() !== '')) {
                keteranganField.show();
            } else {
                keteranganField.hide();
            }
        });
    }

    // Function to set all students to "Hadir" in a specific class
    function setAllHadir(idKelas) {
        var form = $('#formAbsensi' + idKelas);

        // Langsung set semua radio button "Hadir" menjadi checked
        form.find('input[type="radio"][value="Hadir"]').each(function() {
            var name = $(this).attr('name');
            // Hapus checked dari semua radio dalam grup ini
            $('input[name="' + name + '"]').prop('checked', false);
            // Set checked pada radio "Hadir"
            $(this).prop('checked', true);
        });

        // Update active states
        updateActiveState();

        // Sembunyikan semua field keterangan karena semua di-set ke "Hadir"
        $('.keterangan-field').slideUp(200);
        $('input[id^="keterangan-"]').val('');

        // Show feedback
        var btn = $('button[onclick*="setAllHadir(' + idKelas + ')"]');
        var originalText = btn.html();
        btn.html('<i class="fas fa-check"></i> Semua Di-set Hadir');

        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    }

    // Initialize
    $(document).ready(function() {
        // Klik kartu ringkasan kelas di bagian atas untuk membuka tab kelas
        $(document).on('click', '.class-summary-card', function() {
            var idKelas = $(this).data('kelas-id');
            if (idKelas) {
                var targetTab = $('#custom-tabs-one-kelas-' + idKelas + '-tab');
                if (targetTab.length) {
                    targetTab.tab('show');
                }
            }
        });

        // Sinkronkan highlight kartu ringkasan dan badge tab saat tab berubah
        $(document).on('shown.bs.tab', '#custom-tabs-one-tab a[data-toggle="pill"]', function(e) {
            var targetId = $(e.target).attr('id') || '';
            var match = targetId.match(/custom-tabs-one-kelas-(\d+)-tab/);
            if (match) {
                var idKelas = match[1];
                $('.class-summary-card').removeClass('active-summary-card');
                $('.class-summary-card[data-kelas-id="' + idKelas + '"]').addClass('active-summary-card');

                // Update badge styling inside tabs
                $('.tab-santri-badge').removeClass('badge-primary').addClass('badge-light border');
                $('#tab-badge-' + idKelas).removeClass('badge-light border').addClass('badge-primary');
            }
        });

        // Buka tab kelas berdasarkan parameter IdKelas dari URL
        const urlParams = new URLSearchParams(window.location.search);
        const idKelasParam = urlParams.get('IdKelas');
        
        if (idKelasParam) {
            // Tunggu sedikit untuk memastikan semua elemen sudah dimuat
            setTimeout(function() {
                // Hapus class active dari semua tab dan tab pane
                $('#custom-tabs-one-tab .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('#custom-tabs-one-tabContent .tab-pane').removeClass('show active');
                
                // Aktifkan tab yang sesuai dengan IdKelas
                const tabLink = $('#custom-tabs-one-kelas-' + idKelasParam + '-tab');
                const tabPane = $('#custom-tabs-one-kelas-' + idKelasParam);
                
                if (tabLink.length && tabPane.length) {
                    // Aktifkan tab link
                    tabLink.addClass('active').attr('aria-selected', 'true');
                    
                    // Aktifkan tab pane
                    tabPane.addClass('show active');

                    // Highlight kartu ringkasan kelas
                    $('.class-summary-card').removeClass('active-summary-card');
                    $('.class-summary-card[data-kelas-id="' + idKelasParam + '"]').addClass('active-summary-card');
                    $('.tab-santri-badge').removeClass('badge-primary').addClass('badge-light border');
                    $('#tab-badge-' + idKelasParam).removeClass('badge-light border').addClass('badge-primary');
                    
                    // Scroll ke tab yang dipilih
                    setTimeout(function() {
                        const tabOffset = tabLink.offset();
                        if (tabOffset) {
                            $('html, body').animate({
                                scrollTop: tabOffset.top - 100
                            }, 500);
                        }
                    }, 200);
                }
            }, 200);
        }
        
        console.log('[DEBUG] ============================================');
        console.log('[DEBUG] Document ready - Initializing absensi page');
        console.log('[DEBUG] jQuery version:', $.fn.jquery);
        console.log('[DEBUG] ============================================');

        // Test apakah elemen tanggal input ada
        var tanggalInputs = $('.tanggal-input');
        console.log('[DEBUG] Tanggal input elements found:', tanggalInputs.length);

        if (tanggalInputs.length === 0) {
            console.error('[DEBUG] ERROR: No tanggal input elements found!');
            console.error('[DEBUG] This might be because elements are loaded dynamically');
        } else {
            // Test event listener
            tanggalInputs.each(function(index) {
                console.log('[DEBUG] Tanggal input #' + (index + 1) + ':', {
                    id: $(this).attr('id'),
                    kelasId: $(this).data('kelas-id'),
                    value: $(this).val(),
                    element: this
                });
            });

            // Test manual trigger untuk memastikan event listener bekerja
            console.log('[DEBUG] Testing manual change event...');
            tanggalInputs.first().on('change.test', function() {
                console.log('[DEBUG] Manual test event triggered!');
            });
        }

        console.log('[DEBUG] Event listener registered for .tanggal-input');
        console.log('[DEBUG] ============================================');
        // Fungsi untuk memastikan semua "Hadir" terpilih
        function ensureAllHadirSelected() {
            // Loop melalui semua grup radio button
            $('input[type="radio"][name^="kehadiran"]').each(function() {
                var name = $(this).attr('name');
                var checkedRadio = $('input[name="' + name + '"]:checked');

                // Jika tidak ada yang terpilih, pilih "Hadir"
                if (!checkedRadio.length) {
                    var hadirRadio = $('input[name="' + name + '"][value="Hadir"]');
                    if (hadirRadio.length) {
                        hadirRadio.prop('checked', true);
                    }
                }
            });
        }

        // Pastikan semua "Hadir" terpilih (untuk desktop dan mobile)
        ensureAllHadirSelected();

        // Force check semua "Hadir" yang sudah ada atribut checked di HTML
        $('input[type="radio"][value="Hadir"]').each(function() {
            var name = $(this).attr('name');
            // Pastikan hanya satu yang terpilih per grup
            if (!$('input[name="' + name + '"]:checked').length) {
                $(this).prop('checked', true);
            }
        });

        // Update active states untuk semua label (desktop dan mobile)
        updateActiveState();

        // Pastikan field keterangan tersembunyi secara default (karena default adalah "Hadir")
        toggleKeteranganField();

        // Jalankan lagi setelah sedikit delay untuk memastikan
        setTimeout(function() {
            ensureAllHadirSelected();
            updateActiveState();
            toggleKeteranganField();
        }, 100);

        // Handle radio button changes to update active states and toggle keterangan
        $(document).on('change', 'input[type="radio"][name^="kehadiran"]', function() {
            var name = $(this).attr('name');
            var match = name.match(/\[(\d+)\]/);
            if (match) {
                var id = match[1];
                var status = $(this).val();
                var keteranganField = $('#keterangan-field-' + id);
                var keteranganInput = $('#keterangan-' + id);
                if (status !== 'Hadir') {
                    keteranganField.slideDown(200);
                    keteranganInput.focus();
                } else {
                    keteranganField.slideUp(200);
                    keteranganInput.val('');
                }
            }
            updateActiveState();
        });

        // Handle click di luar popup untuk menyembunyikan popup
        $(document).on('click touchstart', function(event) {
            // Cek apakah klik/tap di luar photo thumbnail dan popup
            if (!$(event.target).closest('.photo-profil-thumbnail, .image-popup').length) {
                $('.image-popup').fadeOut(200);
            }
        });

        // Handle hover pada popup agar tidak hilang saat hover di popup (desktop)
        $(document).on('mouseenter', '.image-popup', function() {
            $(this).stop(true, true).show();
        });

        $(document).on('mouseleave', '.image-popup', function() {
            $(this).fadeOut(200);
        });

        // Handle hover pada photo untuk menampilkan popup (desktop)
        $(document).on('mouseenter', '.photo-profil-thumbnail', function() {
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                $('.image-popup').not(popup).fadeOut(200);
                popup.fadeIn(200);
            }
        });

        $(document).on('mouseleave', '.photo-profil-thumbnail', function() {
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                // Delay untuk memungkinkan mouse move ke popup
                setTimeout(function() {
                    if (!popup.is(':hover')) {
                        popup.fadeOut(200);
                    }
                }, 200);
            }
        });

        // Handle tap/click pada photo untuk toggle popup (mobile & desktop)
        $(document).on('click', '.photo-profil-thumbnail', function(e) {
            e.stopPropagation();
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                // Toggle popup: jika sudah tampil, sembunyikan; jika belum, tampilkan
                if (popup.is(':visible')) {
                    popup.fadeOut(200);
                } else {
                    $('.image-popup').not(popup).fadeOut(200);
                    popup.fadeIn(200);
                }
            }
        });

        // Attach photo popup handlers saat halaman pertama kali dimuat
        attachPhotoPopupHandlers();

        // Handle perubahan tanggal - load data via AJAX untuk kelas yang dipilih
        $(document).on('change', '.tanggal-input', function() {
            console.log('[DEBUG] Tanggal input changed');
            var tanggal = $(this).val();
            var idKelas = $(this).data('kelas-id');
            console.log('[DEBUG] Tanggal:', tanggal);
            console.log('[DEBUG] IdKelas:', idKelas);

            if (!tanggal) {
                console.error('[DEBUG] Tanggal kosong!');
                return;
            }

            if (!idKelas) {
                console.error('[DEBUG] IdKelas kosong!');
                return;
            }

            var form = $(this).closest('form');
            var tabPane = $(this).closest('.tab-pane');

            // Cari cards container dengan ID yang spesifik berdasarkan IdKelas
            var cardsContainer = form.find('#cards-container-' + idKelas);

            // Jika tidak ditemukan dengan ID, coba cari dengan class
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('.cards-container');
            }

            // Jika masih tidak ditemukan, cari div yang berisi cards - setelah hidden inputs
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('input[name="IdKelas"]').nextAll('div').first();
            }

            // Jika masih tidak ditemukan, cari div yang berisi card.shadow-sm
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('div:has(.card.shadow-sm)').first();
            }

            // Cari tombol submit dengan ID yang spesifik atau dengan selector
            var submitButton = form.find('#submit-button-container-' + idKelas);

            // Jika tidak ditemukan dengan ID, coba cari dengan selector biasa
            if (submitButton.length === 0) {
                submitButton = form.find('button[type="submit"]').parent();
            }

            console.log('[DEBUG] Form found:', form.length > 0);
            console.log('[DEBUG] Tab pane found:', tabPane.length > 0);
            console.log('[DEBUG] Submit button found:', submitButton.length > 0);
            if (submitButton.length > 0) {
                console.log('[DEBUG] Submit button ID:', submitButton.attr('id'));
                console.log('[DEBUG] Submit button is visible:', submitButton.is(':visible'));
            } else {
                console.error('[DEBUG] Submit button NOT FOUND!');
            }
            console.log('[DEBUG] Cards container found:', cardsContainer.length > 0);
            if (cardsContainer.length > 0) {
                console.log('[DEBUG] Cards container ID:', cardsContainer.attr('id'));
                console.log('[DEBUG] Cards container classes:', cardsContainer.attr('class'));
                console.log('[DEBUG] Cards container current content length:', cardsContainer.html().length);
            } else {
                console.error('[DEBUG] Cards container NOT FOUND!');
                console.log('[DEBUG] Trying to find by form structure...');
                console.log('[DEBUG] Form children count:', form.children().length);
                form.children().each(function(index) {
                    console.log('[DEBUG]   Child ' + index + ':', this.tagName, this.className || '(no class)', $(this).attr('id') || '(no id)');
                });
            }

            // Sinkronkan semua input tanggal di semua tab
            $('.tanggal-input').val(tanggal);

            // Perbarui status dan rekap H, I, S, A pada semua kartu ringkasan kelas
            updateSummaryCardsAttendance(tanggal);

            // Perbarui card tanggal belum absen untuk semua kelas (Baseline Opsi B)
            updateTanggalBelumAbsenCards(tanggal);

            // Tampilkan loading indicator
            cardsContainer.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Memuat data...</p></div>');
            submitButton.hide();

            var ajaxUrl = '<?= base_url("backend/absensi/getSantriByKelasDanTanggal") ?>';
            console.log('[DEBUG] AJAX URL:', ajaxUrl);
            console.log('[DEBUG] AJAX Data:', {
                IdKelas: idKelas,
                tanggal: tanggal
            });

            // Panggil endpoint AJAX
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                data: {
                    IdKelas: idKelas,
                    tanggal: tanggal
                },
                dataType: 'json',
                beforeSend: function() {
                    console.log('[DEBUG] AJAX request started');
                },
                success: function(response) {
                    console.log('[DEBUG] AJAX success response:', response);
                    if (response.success) {
                        console.log('[DEBUG] Response success, updating list with', response.count, 'santri');
                        console.log('[DEBUG] Nama guru:', response.nama_guru || 'Tidak ada');
                        // Update tampilan dengan data baru (fungsi ini juga akan handle show/hide tombol submit)
                        updateSantriList(cardsContainer, response.santri, idKelas, tanggal, response.nama_guru, response.total_santri, response.sudah_absen);
                    } else {
                        console.error('[DEBUG] Response success but data failed:', response.message);
                        cardsContainer.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + (response.message || 'Gagal memuat data') + '</div>');
                        submitButton.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[DEBUG] AJAX Error Details:');
                    console.error('[DEBUG] Status:', status);
                    console.error('[DEBUG] Error:', error);
                    console.error('[DEBUG] XHR:', xhr);
                    console.error('[DEBUG] Response Text:', xhr.responseText);
                    console.error('[DEBUG] Status Code:', xhr.status);

                    var errorMessage = 'Terjadi kesalahan saat memuat data.';
                    if (xhr.status === 404) {
                        errorMessage += ' Endpoint tidak ditemukan.';
                    } else if (xhr.status === 500) {
                        errorMessage += ' Server error.';
                    } else if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            errorMessage += ' ' + (errorResponse.message || '');
                        } catch (e) {
                            errorMessage += ' ' + xhr.responseText.substring(0, 100);
                        }
                    }

                    cardsContainer.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + errorMessage + '</div>');
                    submitButton.hide();
                }
            });
        });

        // Fungsi untuk memperbarui status dan rincian H, I, S, A pada semua kartu ringkasan kelas secara otomatis
        function updateSummaryCardsAttendance(tanggal) {
            if (!tanggal) return;

            // Tampilkan status loading ringkas pada semua kartu kelas
            $('.class-absensi-info').each(function() {
                $(this).html('<span class="text-muted text-xs"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat...</span>');
            });

            $.ajax({
                url: '<?= base_url("backend/absensi/getStatusAbsensiPerKelas") ?>',
                type: 'GET',
                data: { tanggal: tanggal },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        response.data.forEach(function(item) {
                            var container = $('#card-absensi-info-' + item.IdKelas);
                            if (container.length > 0) {
                                var html = '';
                                if (item.totalAbsen === 0) {
                                    html = `
                                        <span class="badge py-1 px-2 mb-1" style="font-size: 0.72rem; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                            <i class="fas fa-times-circle mr-1 text-danger"></i>Belum Diabsen
                                        </span>
                                        <div class="text-muted text-xs font-italic">0 catatan presensi</div>
                                    `;
                                } else {
                                    var isSemua = item.totalSantri > 0 && item.totalAbsen >= item.totalSantri;
                                    var labelStatus = isSemua ? 'Sudah Diabsen' : `Sebagian (${item.totalAbsen}/${item.totalSantri})`;
                                    var badgeCls = isSemua ? 'badge-success' : 'badge-info';
                                    html = `
                                        <span class="badge ${badgeCls} py-1 px-2 mb-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-check-circle mr-1"></i>${labelStatus}
                                        </span>
                                        <div class="d-flex justify-content-center align-items-center mt-1" style="gap: 3px; font-size: 0.72rem;">
                                            <span class="badge badge-success px-1" title="Hadir: ${item.jumlahHadir}">H: ${item.jumlahHadir}</span>
                                            <span class="badge badge-warning px-1" title="Izin: ${item.jumlahIzin}">I: ${item.jumlahIzin}</span>
                                            <span class="badge badge-info px-1" title="Sakit: ${item.jumlahSakit}">S: ${item.jumlahSakit}</span>
                                            <span class="badge badge-danger px-1" title="Alfa: ${item.jumlahAlfa}">A: ${item.jumlahAlfa}</span>
                                        </div>
                                    `;
                                }
                                container.html(html);
                            }
                        });
                    }
                },
                error: function(err) {
                    console.warn('[DEBUG] Gagal memperbarui status card absensi:', err);
                }
            });
        }

        // Variabel global untuk menyimpan filter periode kelengkapan yang aktif ('minggu_ini', 'minggu_kemarin', 'bulan', 'semester')
        var currentPeriodeFilter = '<?= $tanggal_belum_absen_info['periode'] ?? 'bulan' ?>';

        // Fungsi untuk memperbarui card kelengkapan tanggal belum diabsen (Baseline Opsi B)
        function updateTanggalBelumAbsenCards(tanggal, periode) {
            if (!tanggal) return;
            periode = periode || currentPeriodeFilter || 'bulan';

            $.ajax({
                url: '<?= base_url("backend/absensi/getTanggalBelumAbsenPerKelas") ?>',
                type: 'GET',
                data: { 
                    tanggal: tanggal,
                    periode: periode
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        var periodeLabel = response.data.periode_label || '';
                        var totalHariAktifTpq = response.data.total_hari_aktif_tpq || 0;
                        var kelasData = response.data.kelas_data || {};
                        var isSemester = (periode === 'semester');

                        // Update status tombol filter periode di semua tab
                        $('.btn-filter-periode').removeClass('active font-weight-bold');
                        $('.btn-filter-periode[data-periode="' + periode + '"]').addClass('active font-weight-bold');

                        Object.keys(kelasData).forEach(function(idK) {
                            var info = kelasData[idK];
                            $('.badge-periode-label-' + idK).text(periodeLabel);
                            $('.badge-sudah-' + idK).text(info.total_sudah_absen);
                            $('.badge-aktif-' + idK).text(totalHariAktifTpq);

                            // Update badge tertinggal di kartu summary kelas di bagian atas
                            var badgeTertinggal = $('#badge-tertinggal-' + idK);
                            if (badgeTertinggal.length) {
                                if (info.total_belum_absen > 0) {
                                    badgeTertinggal.html('<span class="badge badge-warning text-xs px-1 py-0" title="' + info.total_belum_absen + ' tanggal aktif belum diabsen (' + periodeLabel + ')" style="font-size: 0.68rem;"><i class="fas fa-exclamation-triangle"></i> ' + info.total_belum_absen + ' tgl kosong</span>');
                                } else {
                                    badgeTertinggal.empty();
                                }
                            }

                            // Update konten card tanggal belum absen di tab kelas
                            var container = $('.content-belum-absen-' + idK);
                            if (container.length) {
                                var html = '';
                                if (info.total_belum_absen > 0) {
                                    html += '<p class="text-muted mb-2" style="font-size: 0.82rem;">' +
                                        '<i class="fas fa-exclamation-circle text-danger mr-1"></i>' +
                                        'Ditemukan <strong class="text-danger">' + info.total_belum_absen + ' tanggal aktif TPQ</strong> yang belum tercatat absensinya pada kelas ini. ' +
                                        '<strong>Klik tanggal di bawah</strong> untuk langsung membuka dan menginput absensi:</p>';

                                    if (isSemester && info.grouped_by_month && info.grouped_by_month.length > 0) {
                                        // Grouping per Bulan untuk Overview Semester yang Rapi (Default Collapsed)
                                        html += '<div class="semester-month-groups">';
                                        info.grouped_by_month.forEach(function(mGroup) {
                                            var badgeClass = mGroup.is_lengkap ? 'badge-success' : 'badge-warning';
                                            var badgeText = mGroup.is_lengkap ? ('Lengkap (' + mGroup.total_aktif + '/' + mGroup.total_aktif + ' hari)') : (mGroup.total_belum + ' hari tertinggal');
                                            var hasPending = (!mGroup.is_lengkap && mGroup.tanggal_list && mGroup.tanggal_list.length > 0);

                                            html += '<div class="month-group-card mb-2 border rounded bg-white shadow-xs">' +
                                                '<div class="month-header d-flex justify-content-between align-items-center p-2 ' + (hasPending ? 'cursor-pointer' : '') + '">' +
                                                '<div class="d-flex align-items-center">' +
                                                (hasPending ? '<i class="fas fa-chevron-right text-muted mr-2 chevron-icon" style="font-size: 0.75rem;"></i>' : '<i class="fas fa-check text-success mr-2" style="font-size: 0.75rem;"></i>') +
                                                '<span class="font-weight-bold text-dark" style="font-size: 0.84rem;">' +
                                                '<i class="fas fa-calendar-alt text-secondary mr-1"></i> ' + $('<div>').text(mGroup.bulan_label).html() +
                                                '</span>' +
                                                '</div>' +
                                                '<div>' +
                                                '<span class="badge ' + badgeClass + '" style="font-size: 0.72rem;">' + badgeText + '</span>' +
                                                '</div>' +
                                                '</div>';

                                            if (hasPending) {
                                                html += '<div class="month-collapse-body p-2 border-top bg-light" style="display: none;">' +
                                                    '<small class="text-muted d-block mb-1 font-italic" style="font-size: 0.75rem;">Pilih tanggal untuk diisi presensinya:</small>' +
                                                    '<div class="d-flex flex-wrap" style="gap: 5px;">';
                                                mGroup.tanggal_list.forEach(function(t) {
                                                    html += '<button type="button" class="btn btn-outline-danger btn-sm btn-pilih-tanggal-kosong px-2 py-1 shadow-xs" ' +
                                                        'data-tanggal="' + t.tanggal + '" data-kelas-id="' + idK + '" ' +
                                                        'title="Klik untuk langsung membuka absensi tanggal ' + t.label + '">' +
                                                        '<i class="fas fa-calendar-day text-danger mr-1"></i>' +
                                                        '<strong>' + t.badge_label + '</strong>' +
                                                        '</button>';
                                                });
                                                html += '</div></div>';
                                            }
                                            html += '</div>';
                                        });
                                        html += '</div>';
                                    } else {
                                        // Tampilan Flat untuk Periode Mingguan atau Bulanan
                                        html += '<div class="d-flex flex-wrap" style="gap: 6px;">';
                                        info.belum_absen_list.forEach(function(t) {
                                            html += '<button type="button" class="btn btn-outline-danger btn-sm btn-pilih-tanggal-kosong px-2 py-1 shadow-xs" ' +
                                                'data-tanggal="' + t.tanggal + '" data-kelas-id="' + idK + '" ' +
                                                'title="Klik untuk langsung membuka absensi tanggal ' + t.label + '">' +
                                                '<i class="fas fa-calendar-day text-danger mr-1"></i>' +
                                                '<strong>' + t.badge_label + '</strong>' +
                                                '</button>';
                                        });
                                        html += '</div>';
                                    }
                                } else if (totalHariAktifTpq > 0) {
                                    html += '<div class="text-success font-weight-500 py-1" style="font-size: 0.85rem;">' +
                                        '<i class="fas fa-check-circle mr-1"></i>' +
                                        'Alhamdulillah! Seluruh tanggal aktif TPQ (<strong>' + totalHariAktifTpq + ' hari</strong>) pada ' + $('<div>').text(periodeLabel).html() + ' sudah lengkap diabsen untuk kelas ini.' +
                                        '</div>';
                                } else {
                                    html += '<div class="text-muted font-italic py-1" style="font-size: 0.85rem;">' +
                                        '<i class="fas fa-info-circle mr-1"></i>' +
                                        'Belum ada catatan aktivitas presensi TPQ pada ' + $('<div>').text(periodeLabel).html() + '.' +
                                        '</div>';
                                }
                                container.html(html);
                            }
                        });
                    }
                },
                error: function(err) {
                    console.warn('[DEBUG] Gagal memperbarui data tanggal belum absen:', err);
                }
            });
        }

        // Event handler klik filter periode kelengkapan (Minggu Ini, Minggu Lalu, Per Bulan, Semester)
        $(document).on('click', '.btn-filter-periode', function(e) {
            e.preventDefault();
            var periode = $(this).data('periode');
            if (!periode) return;

            currentPeriodeFilter = periode;

            // Beri visual loading ringkas pada semua content belum absen
            $('.content-belum-absen-' + $(this).data('kelas-id')).html('<div class="text-center py-2 text-muted text-xs"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat overview periode...</div>');

            var tanggal = $('.tanggal-input').first().val() || '<?= date("Y-m-d") ?>';
            updateTanggalBelumAbsenCards(tanggal, periode);
        });

        // Event handler klik tombol tanggal yang belum diabsen untuk langsung membuka tanggal tersebut
        $(document).on('click', '.btn-pilih-tanggal-kosong', function(e) {
            e.preventDefault();
            var tanggal = $(this).data('tanggal');
            var idKelas = $(this).data('kelas-id');

            if (!tanggal || !idKelas) return;

            var inputTgl = $('#tanggal' + idKelas);
            if (inputTgl.length) {
                // Beri efek tombol sedang ditekan
                $(this).addClass('active disabled');

                // Set nilai input dan picu event change
                inputTgl.val(tanggal).trigger('change');

                // Scroll halus menuju list absensi santri
                var containerCards = $('#cards-container-' + idKelas);
                if (containerCards.length) {
                    $('html, body').animate({
                        scrollTop: containerCards.offset().top - 120
                    }, 400);
                }
            }
        });

        // Event handler klik toggle buka/tutup (collapse) card per bulan pada tampilan semester
        $(document).on('click', '.month-header.cursor-pointer', function(e) {
            e.preventDefault();
            var card = $(this).closest('.month-group-card');
            var body = card.find('.month-collapse-body');
            var header = $(this);

            if (body.is(':visible')) {
                body.slideUp(180);
                header.removeClass('active-collapse');
            } else {
                body.slideDown(180);
                header.addClass('active-collapse');
            }
        });

        // Fungsi untuk update tampilan list santri
        function updateSantriList(container, santriList, idKelas, tanggal, namaGuru, totalSantri, sudahAbsen) {
            console.log('[DEBUG] updateSantriList called');
            console.log('[DEBUG] Container:', container.length > 0 ? 'Found' : 'NOT FOUND');
            console.log('[DEBUG] Santri list count:', santriList ? santriList.length : 0);
            console.log('[DEBUG] IdKelas:', idKelas);
            console.log('[DEBUG] Tanggal:', tanggal);
            console.log('[DEBUG] Nama Guru:', namaGuru || 'Tidak ada');
            console.log('[DEBUG] Total Santri:', totalSantri);
            console.log('[DEBUG] Sudah Absen:', sudahAbsen);

            if (container.length === 0) {
                console.error('[DEBUG] Container is empty! Cannot update list.');
                return;
            }

            // Cari dan update tombol submit
            var form = container.closest('form');
            var submitButton = form.find('#submit-button-container-' + idKelas);
            if (submitButton.length === 0) {
                submitButton = form.find('button[type="submit"]').parent();
            }

            console.log('[DEBUG] Submit button in updateSantriList:', submitButton.length > 0 ? 'Found' : 'NOT FOUND');

            if (!santriList || santriList.length === 0) {
                console.log('[DEBUG] No santri, showing empty message');
                var message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Belum ada santri terdaftar di kelas ini pada tahun ajaran ini.</div>';
                container.html(message);

                if (submitButton.length > 0) {
                    console.log('[DEBUG] Hiding submit button (no santri)');
                    submitButton.hide();
                }
                return;
            }

            // Tampilkan tombol submit jika ada santri
            if (submitButton.length > 0) {
                console.log('[DEBUG] Showing submit button (has santri)');
                submitButton.show();
            }

            console.log('[DEBUG] Building HTML for', santriList.length, 'santri');

            var html = '';
            var uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
            var thumbnailPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/') ?>';

            // Tampilkan banner info jika kelas sudah memiliki data absensi pada tanggal tersebut
            var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            var jmlSudah = (typeof sudahAbsen !== 'undefined' && sudahAbsen !== null) ? parseInt(sudahAbsen) : 0;
            if (jmlSudah > 0) {
                var namaGuruText = (namaGuru && namaGuru !== null && namaGuru !== '') ? ' (Oleh: <strong>' + $('<div>').text(namaGuru).html() + '</strong>)' : '';
                html += '<div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap">' +
                    '<div>' +
                    '<i class="fas fa-check-circle text-success mr-1"></i>' +
                    '<strong>Kelas ini sudah diabsen</strong> pada tanggal ' + tanggalFormatted + namaGuruText + '.' +
                    '<span class="text-muted d-block d-sm-inline ml-sm-1">Anda dapat melihat atau memperbarui status kehadiran di bawah jika ada perubahan.</span>' +
                    '</div>' +
                    '</div>';
            }

            santriList.forEach(function(santri) {
                var photoHtml = '';
                if (santri.PhotoProfil) {
                    photoHtml = '<img src="' + thumbnailPath + 'thumb_' + santri.PhotoProfil + '" ' +
                        'alt="PhotoProfil" ' +
                        'class="photo-profil-thumbnail mr-2" ' +
                        'loading="lazy" ' +
                        'style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">' +
                        '<div class="image-popup" style="display: none; position: absolute; z-index: 1000;">' +
                        '<img src="' + uploadPath + santri.PhotoProfil + '" ' +
                        'alt="PhotoProfil" width="200" height="250" loading="lazy" class="rounded shadow">' +
                        '</div>';
                } else {
                    photoHtml = '<i class="fas fa-user-circle mr-2" style="font-size: 40px; color: #6c757d;"></i>';
                }

                var curKehadiran = santri.Kehadiran || 'Hadir';
                var curKeterangan = santri.Keterangan || '';
                var isHadir = (curKehadiran === 'Hadir');
                var isIzin = (curKehadiran === 'Izin');
                var isSakit = (curKehadiran === 'Sakit');
                var isAlfa = (curKehadiran === 'Alfa' || curKehadiran === 'Alpa');
                var showKeterangan = (!isHadir || curKeterangan !== '');

                html += '<div class="card mb-1 shadow-sm">' +
                    '<div class="card-body">' +
                    '<h5 class="card-title mb-2 d-flex align-items-center">' +
                    photoHtml +
                    $('<div>').text(santri.NamaSantri).html() +
                    '</h5>' +
                    '<div class="mb-1">' +
                    '<div class="btn-group w-100" role="group">' +
                    '<label class="btn btn-success absensi-btn-mobile ' + (isHadir ? 'active' : '') + '">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Hadir" autocomplete="off" ' + (isHadir ? 'checked' : '') + '>' +
                    '<i class="fas fa-check-circle"></i> Hadir' +
                    '</label>' +
                    '<label class="btn btn-warning absensi-btn-mobile ' + (isIzin ? 'active' : '') + '">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Izin" autocomplete="off" ' + (isIzin ? 'checked' : '') + '>' +
                    '<i class="fas fa-info-circle"></i> Izin' +
                    '</label>' +
                    '<label class="btn btn-info absensi-btn-mobile ' + (isSakit ? 'active' : '') + '">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Sakit" autocomplete="off" ' + (isSakit ? 'checked' : '') + '>' +
                    '<i class="fas fa-thermometer-half"></i> Sakit' +
                    '</label>' +
                    '<label class="btn btn-danger absensi-btn-mobile ' + (isAlfa ? 'active' : '') + '">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Alfa" autocomplete="off" ' + (isAlfa ? 'checked' : '') + '>' +
                    '<i class="fas fa-times-circle"></i> Alfa' +
                    '</label>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group keterangan-field" id="keterangan-field-' + santri.IdSantri + '" style="' + (showKeterangan ? '' : 'display: none;') + '">' +
                    '<label for="keterangan-' + santri.IdSantri + '"><i class="fas fa-comment-alt"></i> Keterangan:</label>' +
                    '<input type="text" name="keterangan[' + santri.IdSantri + ']" id="keterangan-' + santri.IdSantri + '" ' +
                    'value="' + $('<div>').text(curKeterangan).html() + '" ' +
                    'class="form-control form-control-lg" placeholder="Masukkan keterangan (opsional)">' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            });

            console.log('[DEBUG] HTML generated, length:', html.length);
            console.log('[DEBUG] Updating container HTML...');

            container.html(html);

            console.log('[DEBUG] Container updated. New content length:', container.html().length);
            console.log('[DEBUG] Container children count:', container.children().length);
            console.log('[DEBUG] Container is visible:', container.is(':visible'));

            // Re-initialize event handlers untuk radio buttons dan photo popup
            console.log('[DEBUG] Re-initializing event handlers...');
            ensureAllHadirSelected();
            updateActiveState();
            toggleKeteranganField();

            // Re-attach event handlers untuk photo popup
            attachPhotoPopupHandlers();

            console.log('[DEBUG] updateSantriList completed');
        }

        // Fungsi untuk attach event handlers untuk photo popup (hanya untuk elemen baru yang ditambahkan via AJAX)
        // Event handler utama sudah ada di atas menggunakan $(document).on() yang akan bekerja untuk elemen baru juga
        function attachPhotoPopupHandlers() {
            // Event handlers sudah terpasang dengan $(document).on() di atas
            // Fungsi ini hanya untuk memastikan event handlers terpasang saat elemen baru ditambahkan
            // Tidak perlu melakukan apa-apa karena $(document).on() sudah menangani elemen baru
        }

        // Handle form submission validation dengan popup konfirmasi
        $('form[id^="formAbsensi"]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var hasError = false;

            // Check if all students have attendance status
            form.find('input[type="radio"][name^="kehadiran"]').each(function() {
                var name = $(this).attr('name');
                if (!$('input[name="' + name + '"]:checked').length) {
                    hasError = true;
                }
            });

            if (hasError) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Mohon lengkapi status kehadiran untuk semua santri!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Hitung statistik kehadiran
            var stats = {
                hadir: 0,
                izin: 0,
                sakit: 0,
                alfa: 0
            };

            form.find('input[type="radio"][name^="kehadiran"]:checked').each(function() {
                var status = $(this).val();
                if (stats.hasOwnProperty(status.toLowerCase())) {
                    stats[status.toLowerCase()]++;
                }
            });

            // Tampilkan popup konfirmasi
            var tanggal = form.find('input[name="tanggal"]').val();
            var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            var total = stats.hadir + stats.izin + stats.sakit + stats.alfa;

            // Buat HTML untuk tabel statistik
            var statsHtml = '<div class="text-left mt-3">' +
                '<table class="table table-bordered table-sm mb-0" style="font-size: 0.9rem;">' +
                '<thead class="thead-light">' +
                '<tr><th>Status</th><th class="text-center">Jumlah</th></tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr><td><i class="fas fa-check-circle text-success"></i> Hadir</td><td class="text-center"><strong>' + stats.hadir + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-info-circle text-info"></i> Izin</td><td class="text-center"><strong>' + stats.izin + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-heartbeat text-danger"></i> Sakit</td><td class="text-center"><strong>' + stats.sakit + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-times-circle text-warning"></i> Alfa</td><td class="text-center"><strong>' + stats.alfa + '</strong></td></tr>' +
                '<tr class="table-primary"><td><strong>Total</strong></td><td class="text-center"><strong>' + total + '</strong></td></tr>' +
                '</tbody>' +
                '</table>' +
                '</div>';

            // Tampilkan SweetAlert2 konfirmasi
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Simpan Absensi',
                html: '<p class="mb-2"><strong>Tanggal:</strong> ' + tanggalFormatted + '</p>' + statsHtml + '<p class="text-muted mt-3 mb-0">Apakah Anda yakin ingin menyimpan data absensi ini?</p>',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> OK, Simpan',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Submit form secara asinkron
                    var formData = form.serialize();
                    var formAction = form.attr('action');

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu, data sedang disimpan',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: formAction,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Data absensi berhasil disimpan!',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload halaman dengan parameter tanggal untuk tetap menampilkan tab
                                    var tanggal = form.find('input[name="tanggal"]').val();
                                    window.location.href = '<?= base_url("backend/absensi") ?>?tanggal=' + tanggal;
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Gagal menyimpan data absensi',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            var errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                            if (xhr.responseText) {
                                try {
                                    var errorResponse = JSON.parse(xhr.responseText);
                                    errorMessage = errorResponse.message || errorMessage;
                                } catch (e) {
                                    errorMessage += ' ' + xhr.responseText.substring(0, 100);
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>