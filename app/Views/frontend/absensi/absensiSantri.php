<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($page_title) ? $page_title : 'Absensi Santri' ?></title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    
    <style>
        .absensi-btn-mobile {
            min-height: 48px;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            margin-right: 5px;
            cursor: pointer;
        }
        .absensi-btn-mobile .icheck-primary {
             margin: 0;
        }
        
        /* Mimic backend card styles */
        .card-primary.card-outline-tabs > .card-header a.active {
            border-top: 3px solid #007bff;
        }
        
        .photo-profil-thumbnail {
            width: 33px;
            height: 35px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
            border: 2px solid #ddd;
        }
        .santri-name {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        body {
            background-color: #f4f6f9;
        }
        
        /* Mobile specific adjustments */
        @media(max-width: 576px) {
            .nav-tabs .nav-link {
                padding: 0.5rem 0.5rem;
                font-size: 0.9rem;
            }
            .btn-group-toggle label {
                padding: 5px;
                font-size: 12px;
                flex-direction: column;
            }
            .btn-group-toggle i {
                margin-bottom: 2px;
            }
        }

        /* Card Analisis Tanggal Belum Diabsen (Baseline Opsi B) */
        .card-tanggal-belum-absen {
            border-radius: 8px !important;
            transition: all 0.2s ease-in-out;
        }
        .periode-filter-group .btn-filter-periode {
            font-size: 0.78rem !important;
            padding: 0.25rem 0.6rem !important;
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

        /* Simple Statistik Card (Mobile First) */
        .stat-presensi-card {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
        }
        .stat-box-item {
            border-radius: 8px;
            padding: 8px 3px 6px 3px;
            border: 1px solid transparent;
            transition: all 0.15s ease-in-out;
        }
        .stat-box-hadir {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .stat-box-izin {
            background-color: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .stat-box-sakit {
            background-color: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }
        .stat-box-alfa {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .stat-box-number {
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.15;
            margin: 2px 0;
        }
        .stat-box-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            opacity: 0.9;
        }
        .stat-box-sub {
            font-size: 0.68rem;
            opacity: 0.75;
            font-weight: 500;
        }
        @media (max-width: 400px) {
            .stat-box-label {
                font-size: 0.65rem;
            }
            .stat-box-number {
                font-size: 1.2rem;
            }
            .stat-box-sub {
                font-size: 0.62rem;
            }
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="#" class="navbar-brand">
        <span class="brand-text font-weight-light">Absensi<b>Public</b></span>
      </a>
      
      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
         <ul class="navbar-nav ml-auto">
           <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Guru: <strong><?= isset($guru_nama) ? esc($guru_nama) : 'Guru' ?></strong></span>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('absensi/haskey/logout/' . $hash_key) ?>" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> Absensi Santri</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <!-- Card Tabs for Classes -->
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <?php foreach($kelas_list_all as $k): 
                        $idKTab = (int)$k['IdKelas'];
                        $infoKTab = $tanggal_belum_absen_info['kelas_data'][$idKTab] ?? null;
                        $jmlBlmTab = $infoKTab ? (int)$infoKTab['total_belum_absen'] : 0;
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($k['IdKelas'] == $selected_kelas) ? 'active' : '' ?>" 
                           href="?tanggal=<?= $tanggal_dipilih ?>&IdKelas=<?= $k['IdKelas'] ?>&periode=<?= esc($periode_dipilih) ?>" 
                           role="tab">
                           <?= esc($k['NamaKelas']) ?>
                           <?php if ($jmlBlmTab > 0): ?>
                               <span class="badge badge-warning ml-1" title="<?= $jmlBlmTab ?> tanggal aktif belum diabsen" style="font-size: 0.7rem;"><?= $jmlBlmTab ?></span>
                           <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="card-body">
                <!-- Date Picker & Actions -->
                 <div class="row mb-3">
                     <div class="col-md-6">
                         <div class="form-group mb-2 mb-md-0">
                             <label><i class="fas fa-calendar-alt"></i> Tanggal Absensi:</label>
                             <input type="date" class="form-control form-control-lg" value="<?= $tanggal_dipilih ?>" onchange="updateTanggal(this.value)">
                         </div>
                     </div>
                     <div class="col-md-6 d-flex align-items-end">
                         <div class="form-group w-100 mb-0">
                             <button type="button" class="btn btn-success btn-lg btn-block" onclick="setAllHadir()">
                                 <i class="fas fa-check-double"></i> Set Semua Hadir
                             </button>
                             <small class="text-muted"><i class="fas fa-info-circle"></i> Default: Semua santri di-set sebagai Hadir</small>
                         </div>
                     </div>
                 </div>

                <!-- Simple Statistik Presensi Santri (Mobile First) -->
                <?php
                $statKls = $stat_absensi_kelas ?? [
                    'total_santri' => 0,
                    'sudah_absen' => 0,
                    'belum_absen' => 0,
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alfa' => 0,
                    'persen_hadir' => 0,
                    'status' => 'belum'
                ];
                ?>
                <div class="card stat-presensi-card shadow-sm mb-3">
                    <div class="card-body p-2 p-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <div class="d-flex align-items-center mb-1 mb-sm-0">
                                <span class="badge badge-primary mr-2 px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-chart-pie mr-1"></i> Rekap Hari Ini
                                </span>
                                <span class="text-dark font-weight-bold" style="font-size: 0.85rem;">
                                    <?= date('d M Y', strtotime($tanggal_dipilih)) ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <span class="badge badge-light border text-muted" style="font-size: 0.72rem;">
                                    <i class="fas fa-users mr-1"></i>Total: <strong class="text-dark"><?= $statKls['total_santri'] ?></strong> Santri
                                </span>
                                <?php if ($statKls['status'] === 'lengkap'): ?>
                                    <span class="badge badge-success px-2 py-1" id="badge-status-presensi" style="font-size: 0.72rem;">
                                        <i class="fas fa-check-circle mr-1"></i>Lengkap
                                    </span>
                                <?php elseif ($statKls['status'] === 'sebagian'): ?>
                                    <span class="badge badge-info px-2 py-1" id="badge-status-presensi" style="font-size: 0.72rem;">
                                        <i class="fas fa-adjust mr-1"></i>Sebagian (<?= $statKls['sudah_absen'] ?>/<?= $statKls['total_santri'] ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge py-1 px-2" id="badge-status-presensi" style="font-size: 0.72rem; background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                                        <i class="fas fa-times-circle text-danger mr-1"></i>Belum Diabsen
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 4 Kotak Mini Responsif untuk Layar Handphone -->
                        <div class="row no-gutters mx-n1 text-center">
                            <div class="col-3 px-1">
                                <div class="stat-box-item stat-box-hadir">
                                    <div class="stat-box-label">
                                        <i class="fas fa-check-circle"></i> Hadir
                                    </div>
                                    <div class="stat-box-number" id="stat-count-hadir"><?= $statKls['hadir'] ?></div>
                                    <div class="stat-box-sub text-truncate">
                                        <?= $statKls['total_santri'] > 0 ? round(($statKls['hadir'] / $statKls['total_santri']) * 100) . '%' : '0%' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 px-1">
                                <div class="stat-box-item stat-box-izin">
                                    <div class="stat-box-label">
                                        <i class="fas fa-envelope"></i> Izin
                                    </div>
                                    <div class="stat-box-number" id="stat-count-izin"><?= $statKls['izin'] ?></div>
                                    <div class="stat-box-sub text-truncate">
                                        <?= $statKls['total_santri'] > 0 ? round(($statKls['izin'] / $statKls['total_santri']) * 100) . '%' : '0%' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 px-1">
                                <div class="stat-box-item stat-box-sakit">
                                    <div class="stat-box-label">
                                        <i class="fas fa-clinic-medical"></i> Sakit
                                    </div>
                                    <div class="stat-box-number" id="stat-count-sakit"><?= $statKls['sakit'] ?></div>
                                    <div class="stat-box-sub text-truncate">
                                        <?= $statKls['total_santri'] > 0 ? round(($statKls['sakit'] / $statKls['total_santri']) * 100) . '%' : '0%' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 px-1">
                                <div class="stat-box-item stat-box-alfa">
                                    <div class="stat-box-label">
                                        <i class="fas fa-times-circle"></i> Alfa
                                    </div>
                                    <div class="stat-box-number" id="stat-count-alfa"><?= $statKls['alfa'] ?></div>
                                    <div class="stat-box-sub text-truncate">
                                        <?= $statKls['total_santri'] > 0 ? round(($statKls['alfa'] / $statKls['total_santri']) * 100) . '%' : '0%' ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($statKls['status'] === 'sebagian'): ?>
                            <div class="mt-2 text-center text-xs text-muted font-italic" id="stat-subinfo">
                                <i class="fas fa-info-circle text-info mr-1"></i>
                                Masih ada <strong><?= $statKls['belum_absen'] ?> santri</strong> yang belum diinput pada form di bawah.
                            </div>
                        <?php elseif ($statKls['status'] === 'belum'): ?>
                            <div class="mt-2 text-center text-xs text-muted font-italic" id="stat-subinfo">
                                <i class="fas fa-edit text-secondary mr-1"></i>
                                Belum ada absensi tersimpan pada tanggal ini. Klik <strong>Set Semua Hadir</strong> atau pilih kehadiran santri di bawah.
                            </div>
                        <?php else: ?>
                            <div class="mt-2 text-center text-xs text-success font-weight-500" id="stat-subinfo">
                                <i class="fas fa-check-double mr-1"></i>
                                Seluruh <?= $statKls['total_santri'] ?> santri telah tercatat (Tingkat Kehadiran: <strong><?= $statKls['persen_hadir'] ?>%</strong>).
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Analisis Tanggal Belum Diabsen (Baseline Opsi B) -->
                <?php
                $idKlsCur = (int)$selected_kelas;
                $infoKlsCur = $tanggal_belum_absen_info['kelas_data'][$idKlsCur] ?? [
                    'total_hari_aktif_tpq' => 0,
                    'total_sudah_absen' => 0,
                    'total_belum_absen' => 0,
                    'belum_absen_list' => [],
                    'grouped_by_month' => []
                ];
                $periodeStr = $tanggal_belum_absen_info['periode'] ?? 'bulan';
                $periodeLabelStr = $tanggal_belum_absen_info['periode_label'] ?? ('Bulan ' . date('F Y', strtotime($tanggal_dipilih)));
                $isSemesterView = ($periodeStr === 'semester');
                ?>
                <div class="card shadow-sm mb-4 card-tanggal-belum-absen" id="card-belum-absen-<?= $selected_kelas ?>" style="border-left: 4px solid #ffc107; background: #fffdf5;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <div class="mb-2 mb-md-0">
                                <span class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                    <i class="fas fa-calendar-check text-warning mr-1"></i>
                                    Kelengkapan Absensi: <span class="badge-periode-label text-primary font-weight-bold"><?= esc($periodeLabelStr) ?></span>
                                </span>
                                <span class="badge badge-light border text-muted ml-1" style="font-size: 0.78rem;">
                                    Baseline: <strong class="badge-sudah"><?= $infoKlsCur['total_sudah_absen'] ?></strong> / <strong class="badge-aktif"><?= $infoKlsCur['total_hari_aktif_tpq'] ?></strong> Hari Aktif TPQ
                                </span>
                            </div>

                            <!-- Filter Periode: Minggu Ini, Minggu Kemarin, Per Bulan, Semester -->
                            <div class="btn-group btn-group-sm periode-filter-group shadow-xs" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'minggu_ini') ? 'active font-weight-bold' : '' ?>" data-periode="minggu_ini">Minggu Ini</button>
                                <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'minggu_kemarin') ? 'active font-weight-bold' : '' ?>" data-periode="minggu_kemarin">Minggu Lalu</button>
                                <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'bulan') ? 'active font-weight-bold' : '' ?>" data-periode="bulan">Per Bulan</button>
                                <button type="button" class="btn btn-outline-secondary btn-filter-periode <?= ($periodeStr === 'semester') ? 'active font-weight-bold' : '' ?>" data-periode="semester">Per Semester</button>
                            </div>
                        </div>

                        <div class="content-belum-absen">
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

                <!-- Absensi Form -->
                <form action="<?= base_url('absensi/haskey/simpan') ?>" method="post" id="formAbsensi">
                    <input type="hidden" name="IdTpq" value="<?= $IdTpq ?>">
                    <input type="hidden" name="IdTahunAjaran" value="<?= $IdTahunAjaran ?>">
                    <input type="hidden" name="IdKelas" value="<?= $selected_kelas ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal_dipilih ?>">

                    <?php if (empty($santri)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Semua santri di kelas ini sudah diabsen pada tanggal <?= date('d-m-Y', strtotime($tanggal_dipilih)) ?>.
                            <?php if (!empty($absensi_recorder)): ?>
                                <br><small><i class="fas fa-user-check text-success"></i> Diabsensi oleh: <strong><?= esc($absensi_recorder) ?></strong></small>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($santri_sudah_absen)): ?>
                        <button type="button" class="btn btn-warning btn-block mb-3" id="btnToggleEdit" onclick="toggleEditSection()">
                            <i class="fas fa-edit"></i> Ubah Absensi
                        </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div id="santri-list">
                            <?php foreach ($santri as $row): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <?php 
                                            $photoProfil = $row->PhotoProfil ?? '';
                                            $thumbnailPath = base_url('uploads/santri/thumbnails/thumb_' . $photoProfil);
                                            
                                            // Generate Initials (Acronym)
                                            $namaWords = explode(' ', trim($row->NamaSantri));
                                            $acronym = '';
                                            foreach ($namaWords as $w) {
                                                $acronym .= mb_substr($w, 0, 1);
                                            }
                                            $acronym = strtoupper(substr($acronym, 0, 2));

                                            // Deterministic Color based on Name
                                            $bgColors = ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'];
                                            $colorIndex = crc32($row->NamaSantri) % count($bgColors);
                                            $bgColor = $bgColors[abs($colorIndex)];
                                            ?>

                                            <div class="mr-3 position-relative">
                                                <?php if($photoProfil): ?>
                                                    <!-- Image with fallback handling -->
                                                    <img src="<?= $thumbnailPath ?>" 
                                                         class="photo-profil-thumbnail" 
                                                         onerror="this.style.display='none'; document.getElementById('avatar-fallback-<?= $row->IdSantri ?>').style.display='flex';">
                                                    
                                                    <!-- Hidden fallback for onerror key -->
                                                    <div id="avatar-fallback-<?= $row->IdSantri ?>"
                                                         class="photo-profil-thumbnail align-items-center justify-content-center text-white" 
                                                         style="display: none; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Default Avatar (Initials) -->
                                                    <div class="photo-profil-thumbnail d-flex align-items-center justify-content-center text-white" 
                                                         style="background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h5 class="m-0 santri-name text-dark"><?= esc($row->NamaSantri) ?></h5>
                                        </div>

                                        <!-- Option Buttons Styles like Backend -->
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                          <label class="btn btn-outline-success absensi-btn-mobile active" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Hadir')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Hadir" checked autocomplete="off"> 
                                            <i class="fas fa-check-circle"></i> Hadir
                                          </label>
                                          <label class="btn btn-outline-warning absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Izin" autocomplete="off"> 
                                            <i class="fas fa-envelope"></i> Izin
                                          </label>
                                          <label class="btn btn-outline-info absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Sakit" autocomplete="off"> 
                                            <i class="fas fa-clinic-medical"></i> Sakit
                                          </label>
                                          <label class="btn btn-outline-danger absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Alfa')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Alfa" autocomplete="off"> 
                                            <i class="fas fa-times-circle"></i> Alfa
                                          </label>
                                        </div>
                                        
                                        <div class="form-group mt-2" id="keterangan-box-<?= $row->IdSantri ?>" style="display: none;">
                                            <input type="text" name="keterangan[<?= $row->IdSantri ?>]" class="form-control form-control-sm" placeholder="Keterangan (Opsional)">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4 mb-4">
                             <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnSimpan">
                                 <i class="fas fa-save"></i> SIMPAN ABSENSI
                             </button>
                        </div>
                        
                        <?php if (!empty($santri_sudah_absen)): ?>
                        <hr>
                        <button type="button" class="btn btn-warning btn-block mb-3" id="btnToggleEdit" onclick="toggleEditSection()">
                            <i class="fas fa-edit"></i> Ubah Absensi yang Sudah Tercatat
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
                
                <!-- Section Edit Absensi -->
                <?php if (!empty($santri_sudah_absen)): ?>
                <div id="editAbsensiSection" style="display: none;">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i> Ubah Absensi
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="toggleEditSection()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning alert-sm mb-3">
                                <i class="fas fa-info-circle"></i> Klik pada status kehadiran untuk mengubah absensi santri.
                            </div>
                            
                            <?php foreach ($santri_sudah_absen as $row): 
                                if (($row->Kehadiran ?? '') === 'Pra-Masuk') {
                                    continue; // Sembunyikan santri pra-masuk dari list publik
                                }
                                $photoProfil = $row->PhotoProfil ?? '';
                                $thumbnailPath = base_url('uploads/santri/thumbnails/thumb_' . $photoProfil);
                                
                                // Generate Initials
                                $namaWords = explode(' ', trim($row->NamaSantri));
                                $acronym = '';
                                foreach ($namaWords as $w) {
                                    $acronym .= mb_substr($w, 0, 1);
                                }
                                $acronym = strtoupper(substr($acronym, 0, 2));

                                // Deterministic Color
                                $bgColors = ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'];
                                $colorIndex = crc32($row->NamaSantri) % count($bgColors);
                                $bgColor = $bgColors[abs($colorIndex)];
                                
                                // Status badge class
                                $statusClass = '';
                                $statusIcon = '';
                                switch(strtolower($row->Kehadiran ?? '')) {
                                    case 'hadir':
                                        $statusClass = 'badge-success';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 'izin':
                                        $statusClass = 'badge-warning';
                                        $statusIcon = 'fa-envelope';
                                        break;
                                    case 'sakit':
                                        $statusClass = 'badge-info';
                                        $statusIcon = 'fa-clinic-medical';
                                        break;
                                    case 'alfa':
                                        $statusClass = 'badge-danger';
                                        $statusIcon = 'fa-times-circle';
                                        break;
                                }
                            ?>
                            <div class="card mb-2 shadow-sm santri-edit-card" id="edit-card-<?= $row->IdSantri ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3 position-relative">
                                                <?php if($photoProfil): ?>
                                                    <img src="<?= $thumbnailPath ?>" 
                                                         class="photo-profil-thumbnail" 
                                                         onerror="this.style.display='none'; document.getElementById('edit-avatar-fallback-<?= $row->IdSantri ?>').style.display='flex';">
                                                    <div id="edit-avatar-fallback-<?= $row->IdSantri ?>"
                                                         class="photo-profil-thumbnail align-items-center justify-content-center text-white" 
                                                         style="display: none; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="photo-profil-thumbnail d-flex align-items-center justify-content-center text-white" 
                                                         style="background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="m-0 santri-name text-dark"><?= esc($row->NamaSantri) ?></h6>
                                                <small class="text-muted">
                                                    Status: <span class="badge <?= $statusClass ?>" id="status-badge-<?= $row->IdSantri ?>">
                                                        <i class="fas <?= $statusIcon ?>"></i> <?= ucfirst($row->Kehadiran ?? '-') ?>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Buttons -->
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons" id="edit-buttons-<?= $row->IdSantri ?>">
                                        <label class="btn btn-outline-success absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'hadir' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Hadir'); updateSingleAbsensi(<?= $row->IdSantri ?>, 'Hadir')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Hadir" <?= strtolower($row->Kehadiran) == 'hadir' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-check-circle"></i> Hadir
                                        </label>
                                        <label class="btn btn-outline-warning absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'izin' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Izin" <?= strtolower($row->Kehadiran) == 'izin' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-envelope"></i> Izin
                                        </label>
                                        <label class="btn btn-outline-info absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'sakit' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Sakit" <?= strtolower($row->Kehadiran) == 'sakit' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-clinic-medical"></i> Sakit
                                        </label>
                                        <label class="btn btn-outline-danger absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'alfa' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Alfa'); updateSingleAbsensi(<?= $row->IdSantri ?>, 'Alfa')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Alfa" <?= strtolower($row->Kehadiran) == 'alfa' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-times-circle"></i> Alfa
                                        </label>
                                    </div>
                                    
                                    <!-- Keterangan Input (Opsional) -->
                                    <?php 
                                    $showKeterangan = in_array(strtolower($row->Kehadiran ?? ''), ['izin', 'sakit']);
                                    ?>
                                    <div class="form-group mt-2" id="edit-keterangan-box-<?= $row->IdSantri ?>" style="display: <?= $showKeterangan ? 'block' : 'none' ?>;">
                                        <div class="input-group">
                                            <input type="text" id="edit-keterangan-<?= $row->IdSantri ?>" class="form-control form-control-sm" placeholder="Keterangan (Opsional)" value="<?= esc($row->Keterangan ?? '') ?>" onblur="submitEditKeterangan(<?= $row->IdSantri ?>)">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-sm btn-primary" onclick="submitEditKeterangan(<?= $row->IdSantri ?>)">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <!-- /.card -->
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="container">
        <div class="float-right d-none d-sm-inline">
          <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#">TPQ Smart</a>.</strong>
    </div>
  </footer>
</div>

<!-- jQuery -->
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('template/backend/dist/js/adminlte.min.js') ?>"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function updateTanggal(val) {
        var url = new URL(window.location.href);
        url.searchParams.set('tanggal', val);
        window.location.href = url.toString();
    }

    // Event handler klik tombol tanggal yang belum diabsen untuk langsung membuka tanggal tersebut
    $(document).on('click', '.btn-pilih-tanggal-kosong', function(e) {
        e.preventDefault();
        var tanggal = $(this).data('tanggal');
        if (tanggal) {
            updateTanggal(tanggal);
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

    // Variabel filter periode aktif saat ini
    var currentPeriodeFilter = '<?= $periodeStr ?>';

    // Event handler klik filter periode kelengkapan (Minggu Ini, Minggu Lalu, Per Bulan, Semester)
    $(document).on('click', '.btn-filter-periode', function(e) {
        e.preventDefault();
        var periode = $(this).data('periode');
        if (!periode) return;

        currentPeriodeFilter = periode;

        // Update URL tanpa reload agar state tersimpan
        var url = new URL(window.location.href);
        url.searchParams.set('periode', periode);
        window.history.replaceState({}, '', url.toString());

        // Visual loading
        $('.content-belum-absen').html('<div class="text-center py-2 text-muted text-xs"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat overview periode...</div>');

        $.ajax({
            url: '<?= base_url("absensi/haskey/getTanggalBelumAbsen") ?>',
            type: 'GET',
            data: {
                IdTpq: '<?= $IdTpq ?>',
                IdTahunAjaran: '<?= $IdTahunAjaran ?>',
                tanggal: '<?= $tanggal_dipilih ?>',
                periode: periode
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var periodeLabel = response.data.periode_label || '';
                    var totalHariAktifTpq = response.data.total_hari_aktif_tpq || 0;
                    var selectedKelasId = '<?= $selected_kelas ?>';
                    var info = (response.data.kelas_data && response.data.kelas_data[selectedKelasId]) ? response.data.kelas_data[selectedKelasId] : { total_sudah_absen: 0, total_belum_absen: 0, belum_absen_list: [], grouped_by_month: [] };
                    var isSemester = (periode === 'semester');

                    $('.btn-filter-periode').removeClass('active font-weight-bold');
                    $('.btn-filter-periode[data-periode="' + periode + '"]').addClass('active font-weight-bold');
                    $('.badge-periode-label').text(periodeLabel);
                    $('.badge-sudah').text(info.total_sudah_absen);
                    $('.badge-aktif').text(totalHariAktifTpq);

                    var html = '';
                    if (info.total_belum_absen > 0) {
                        html += '<p class="text-muted mb-2" style="font-size: 0.82rem;">' +
                            '<i class="fas fa-exclamation-circle text-danger mr-1"></i>' +
                            'Ditemukan <strong class="text-danger">' + info.total_belum_absen + ' tanggal aktif TPQ</strong> yang belum tercatat absensinya pada kelas ini. ' +
                            '<strong>Klik tanggal di bawah</strong> untuk langsung membuka dan menginput absensi:</p>';

                        if (isSemester && info.grouped_by_month && info.grouped_by_month.length > 0) {
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
                                            'data-tanggal="' + t.tanggal + '" ' +
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
                            html += '<div class="d-flex flex-wrap" style="gap: 6px;">';
                            info.belum_absen_list.forEach(function(t) {
                                html += '<button type="button" class="btn btn-outline-danger btn-sm btn-pilih-tanggal-kosong px-2 py-1 shadow-xs" ' +
                                    'data-tanggal="' + t.tanggal + '" ' +
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
                    $('.content-belum-absen').html(html);
                }
            }
        });
    });

    function setAllHadir() {
        $('input[value="Hadir"]').prop('checked', true).trigger('change');
        $('.btn-group-toggle label').removeClass('active');
        $('input[value="Hadir"]').parent('label').addClass('active');

        // Hide all keterangan boxes
        $('div[id^="keterangan-box-"]').hide();
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Semua santri di-set Hadir',
            timer: 1000,
            showConfirmButton: false
        });
    }

    function toggleKeterangan(idSantri, status) {
        var box = $('#keterangan-box-' + idSantri);
        if (status === 'Hadir') {
            box.slideUp();
        } else {
            box.slideDown();
        }
    }

    $(document).ready(function() {
        $('#formAbsensi').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = $('#btnSimpan');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> SIMPAN ABSENSI');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> SIMPAN ABSENSI');
                }
            });
        });
    });

    // Toggle edit absensi section
    function toggleEditSection() {
        var editSection = $('#editAbsensiSection');
        var toggleBtn = $('#btnToggleEdit');
        
        if (editSection.is(':visible')) {
            editSection.slideUp();
            toggleBtn.html('<i class="fas fa-edit"></i> Ubah Absensi');
        } else {
            editSection.slideDown();
            toggleBtn.html('<i class="fas fa-times"></i> Tutup');
            
            // Scroll to edit section
            $('html, body').animate({
                scrollTop: editSection.offset().top - 100
            }, 500);
        }
    }

    // Update live simple statistik di mobile saat guru mengubah status absensi
    function refreshStatistikMobile() {
        var hadir = 0, izin = 0, sakit = 0, alfa = 0;
        var total = <?= (int)($statKls['total_santri'] ?? 0) ?>;

        $('.santri-edit-card').each(function() {
            var radio = $(this).find('input[type="radio"]:checked');
            if (radio.length) {
                var v = (radio.val() || '').toLowerCase();
                if (v === 'hadir') hadir++;
                else if (v === 'izin') izin++;
                else if (v === 'sakit') sakit++;
                else if (v === 'alfa') alfa++;
            }
        });

        $('#stat-count-hadir').text(hadir);
        $('#stat-count-izin').text(izin);
        $('#stat-count-sakit').text(sakit);
        $('#stat-count-alfa').text(alfa);

        if (total > 0) {
            $('.stat-box-hadir .stat-box-sub').text(Math.round((hadir / total) * 100) + '%');
            $('.stat-box-izin .stat-box-sub').text(Math.round((izin / total) * 100) + '%');
            $('.stat-box-sakit .stat-box-sub').text(Math.round((sakit / total) * 100) + '%');
            $('.stat-box-alfa .stat-box-sub').text(Math.round((alfa / total) * 100) + '%');

            var totalAbsen = hadir + izin + sakit + alfa;
            if (totalAbsen >= total) {
                $('#badge-status-presensi').removeClass().addClass('badge badge-success px-2 py-1').html('<i class="fas fa-check-circle mr-1"></i>Lengkap');
                $('#stat-subinfo').removeClass().addClass('mt-2 text-center text-xs text-success font-weight-500').html('<i class="fas fa-check-double mr-1"></i>Seluruh ' + total + ' santri telah tercatat (Tingkat Kehadiran: <strong>' + Math.round((hadir / total) * 100) + '%</strong>).');
            } else if (totalAbsen > 0) {
                $('#badge-status-presensi').removeClass().addClass('badge badge-info px-2 py-1').html('<i class="fas fa-adjust mr-1"></i>Sebagian (' + totalAbsen + '/' + total + ')');
            }
        }
    }

    // Update single absensi via AJAX
    function updateSingleAbsensi(idSantri, kehadiran) {
        var formData = {
            IdSantri: idSantri,
            tanggal: '<?= $tanggal_dipilih ?>',
            kehadiran: kehadiran,
            IdKelas: '<?= $selected_kelas ?>',
            IdTahunAjaran: '<?= $IdTahunAjaran ?>',
            IdTpq: '<?= $IdTpq ?>'
        };

        // Update button states immediately
        var buttonGroup = $('#edit-buttons-' + idSantri);
        buttonGroup.find('label').removeClass('active');
        buttonGroup.find('input[value="' + kehadiran + '"]').prop('checked', true).closest('label').addClass('active');

        $.ajax({
            url: '<?= base_url("absensi/haskey/update") ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update status badge
                    var badgeClass = '';
                    var badgeIcon = '';
                    switch(kehadiran.toLowerCase()) {
                        case 'hadir':
                            badgeClass = 'badge-success';
                            badgeIcon = 'fa-check-circle';
                            break;
                        case 'izin':
                            badgeClass = 'badge-warning';
                            badgeIcon = 'fa-envelope';
                            break;
                        case 'sakit':
                            badgeClass = 'badge-info';
                            badgeIcon = 'fa-clinic-medical';
                            break;
                        case 'alfa':
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fa-times-circle';
                            break;
                    }
                    
                    var badge = $('#status-badge-' + idSantri);
                    badge.removeClass('badge-success badge-warning badge-info badge-danger');
                    badge.addClass(badgeClass);
                    badge.html('<i class="fas ' + badgeIcon + '"></i> ' + kehadiran);
                    
                    // Update live mini statistik di mobile
                    refreshStatistikMobile();

                    // Flash effect to indicate success
                    var card = $('#edit-card-' + idSantri);
                    card.css('background-color', '#d4edda');
                    setTimeout(function() {
                        card.css('background-color', '');
                    }, 500);
                    
                    // Toast notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Absensi berhasil diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    }

    // Toggle keterangan field visibility for edit mode
    function toggleEditKeterangan(idSantri, status) {
        var box = $('#edit-keterangan-box-' + idSantri);
        
        // Update button states immediately
        var buttonGroup = $('#edit-buttons-' + idSantri);
        buttonGroup.find('label').removeClass('active');
        buttonGroup.find('input[value="' + status + '"]').prop('checked', true).closest('label').addClass('active');
        
        if (status === 'Izin' || status === 'Sakit') {
            box.slideDown();
        } else {
            box.slideUp();
        }
    }

    // Submit keterangan with status update for Izin/Sakit
    function submitEditKeterangan(idSantri) {
        var kehadiran = $('input[name="edit_kehadiran_' + idSantri + '"]:checked').val();
        var keterangan = $('#edit-keterangan-' + idSantri).val();
        
        var formData = {
            IdSantri: idSantri,
            tanggal: '<?= $tanggal_dipilih ?>',
            kehadiran: kehadiran,
            keterangan: keterangan,
            IdKelas: '<?= $selected_kelas ?>',
            IdTahunAjaran: '<?= $IdTahunAjaran ?>',
            IdTpq: '<?= $IdTpq ?>'
        };

        $.ajax({
            url: '<?= base_url("absensi/haskey/update") ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update status badge
                    var badgeClass = '';
                    var badgeIcon = '';
                    switch(kehadiran.toLowerCase()) {
                        case 'hadir':
                            badgeClass = 'badge-success';
                            badgeIcon = 'fa-check-circle';
                            break;
                        case 'izin':
                            badgeClass = 'badge-warning';
                            badgeIcon = 'fa-envelope';
                            break;
                        case 'sakit':
                            badgeClass = 'badge-info';
                            badgeIcon = 'fa-clinic-medical';
                            break;
                        case 'alfa':
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fa-times-circle';
                            break;
                    }
                    
                    var badge = $('#status-badge-' + idSantri);
                    badge.removeClass('badge-success badge-warning badge-info badge-danger');
                    badge.addClass(badgeClass);
                    badge.html('<i class="fas ' + badgeIcon + '"></i> ' + kehadiran);
                    
                    // Flash effect
                    var card = $('#edit-card-' + idSantri);
                    card.css('background-color', '#d4edda');
                    setTimeout(function() {
                        card.css('background-color', '');
                    }, 500);
                    
                    // Toast notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Absensi berhasil diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    }
</script>
</body>
</html>
