<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-danger">
                        <h3 class="card-title">
                            <i class="fas fa-crown"></i> Dashboard Utama - Admin - Tahun Ajaran <?= esc($TahunAjaran) ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <h5><i class="icon fas fa-crown"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...!
                                        Selamat datang di dashboard pengelolaan lembaga sebagai <strong>Administrator</strong> pada Tahun Ajaran <strong><?= esc($TahunAjaran) ?></strong>.
                                        Dashboard ini memberikan akses penuh untuk mengelola seluruh data akademik dan administrasi sistem.</p>
                                </div>
                            </div>
                        </div>

                        <?= prayer_schedule_widget() ?>

                        <!-- Statistik Overview -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= $TotalGuru ?? 0 ?></h3>
                                        <p>Total Guru</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <a href="<?= base_url('backend/guru/show') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= $TotalSantri ?? 0 ?></h3>
                                        <p>Total Santri</p>
                                        <small><?= $TotalKelas ?? 0 ?> Kelas</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <a href="<?= base_url('backend/santri/showAturSantriBaru') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <?php if (isset($IsAdmin) && $IsAdmin): ?>
                                            <h3><?= $TotalTpq ?? 0 ?></h3>
                                            <p>Jumlah TPQ</p>
                                        <?php else: ?>
                                            <h3><?= $TotalWaliKelas ?? 0 ?></h3>
                                            <p>Wali Kelas</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="icon">
                                        <?php if (isset($IsAdmin) && $IsAdmin): ?>
                                            <i class="fas fa-building"></i>
                                        <?php else: ?>
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= isset($IsAdmin) && $IsAdmin ? base_url('backend/tpq/show') : base_url('backend/guruKelas/show') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3><?= $TotalSantriBaru ?? 0 ?></h3>
                                        <p>Santri Baru</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <a href="<?= base_url('backend/kelas/showSantriKelasBaru') ?>" class="small-box-footer">
                                        Tambah Data <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-danger card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions - Admin</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-block btn-success btn-lg">
                                                    <i class="fas fa-user-plus"></i><br>Tambah Santri
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/guru/show') ?>" class="btn btn-block btn-primary btn-lg">
                                                    <i class="fas fa-user-tie"></i><br>Data Guru
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/guruKelas/show') ?>" class="btn btn-block btn-info btn-lg">
                                                    <i class="fas fa-users-cog"></i><br>Guru Kelas
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/kelas/showSantriKelasBaru') ?>" class="btn btn-block btn-warning btn-lg">
                                                    <i class="fas fa-user-check"></i><br>Registrasi
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/kelas/showListSantriPerKelas') ?>" class="btn btn-block btn-purple btn-lg">
                                                    <i class="fas fa-graduation-cap"></i><br>Naik Kelas
                                                </a>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <a href="<?= base_url('backend/user/index') ?>" class="btn btn-block btn-dark btn-lg">
                                                    <i class="fas fa-cog"></i><br>Akun
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== RINGKASAN STATISTIK GURU TPQ (INFOGRAFIS) ==================== -->
                        <style id="infografis-styles-tag">
                        /* ==================== INFOGRAFIS STATISTIK GURU ==================== */
                        .infografis-guru-wrapper {
                            background: #ffffff;
                            border-radius: 16px;
                            position: relative;
                            overflow: hidden;
                            color: #1f2937;
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        }
                        .infografis-watermark {
                            position: absolute;
                            top: 0;
                            right: 0;
                            width: 320px;
                            height: 320px;
                            opacity: 0.035;
                            pointer-events: none;
                            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpolygon points='50 0, 61 35, 98 35, 68 57, 79 91, 50 70, 21 91, 32 57, 2 35, 39 35' fill='%231e5138'/%3E%3Cpolygon points='50 10, 59 38, 88 38, 64 56, 73 83, 50 66, 27 83, 36 56, 12 38, 41 38' fill='%23c59b27'/%3E%3C/svg%3E");
                            background-repeat: repeat;
                            background-size: 70px 70px;
                        }
                        .infografis-header {
                            text-align: center;
                            padding: 20px 20px 15px 20px;
                            position: relative;
                        }
                        .infografis-accent-dots {
                            position: absolute;
                            top: 20px;
                            left: 20px;
                            display: grid;
                            grid-template-columns: repeat(2, 6px);
                            gap: 6px;
                        }
                        .infografis-accent-dots span {
                            width: 6px;
                            height: 6px;
                            border-radius: 50%;
                            background-color: #c59b27;
                        }
                        .infografis-accent-dots span:nth-child(2),
                        .infografis-accent-dots span:nth-child(3) {
                            background-color: #1e5138;
                        }
                        .infografis-lantern {
                            position: absolute;
                            top: 15px;
                            right: 25px;
                        }
                        .infografis-lantern-icon {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 44px;
                            height: 44px;
                            border-radius: 12px;
                            background: #f0fdf4;
                            border: 1.5px solid #1e5138;
                            color: #1e5138;
                            font-size: 1.25rem;
                            box-shadow: 0 4px 10px rgba(30, 81, 56, 0.12);
                        }
                        .infografis-eyebrow {
                            font-size: 1.45rem;
                            font-weight: 800;
                            color: #1e5138;
                            letter-spacing: 0.08em;
                            text-transform: uppercase;
                            margin-bottom: 2px;
                        }
                        .infografis-main-title {
                            font-size: 2.15rem;
                            font-weight: 900;
                            color: #14422b;
                            letter-spacing: 0.02em;
                            margin-bottom: 6px;
                        }
                        .infografis-total-pill {
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            font-size: 1.1rem;
                            font-weight: 700;
                            color: #374151;
                        }
                        .infografis-total-badge {
                            background: #1e5138;
                            color: #ffffff;
                            font-weight: 800;
                            padding: 3px 14px;
                            border-radius: 8px;
                            letter-spacing: 0.03em;
                            font-size: 1.05rem;
                        }

                        .infografis-section-head {
                            display: flex;
                            align-items: center;
                            margin: 24px 0 16px 0;
                            gap: 12px;
                        }
                        .infografis-section-badge {
                            width: 38px;
                            height: 38px;
                            border-radius: 10px;
                            background: #c59b27;
                            color: #ffffff;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.15rem;
                            flex-shrink: 0;
                        }
                        .infografis-section-title {
                            font-size: 1.25rem;
                            font-weight: 800;
                            letter-spacing: 0.06em;
                            color: #111827;
                            text-transform: uppercase;
                            white-space: nowrap;
                            margin: 0;
                        }
                        .infografis-section-divider {
                            flex-grow: 1;
                            display: flex;
                            align-items: center;
                            gap: 4px;
                        }
                        .infografis-section-divider .line {
                            flex-grow: 1;
                            height: 2px;
                            background: #d4af37;
                        }
                        .infografis-section-divider .dots {
                            display: flex;
                            gap: 4px;
                        }
                        .infografis-section-divider .dot {
                            width: 6px;
                            height: 6px;
                            border-radius: 50%;
                            background: #d4af37;
                        }

                        .infografis-stat-card {
                            background: #ffffff;
                            border: 1px solid #e5e7eb;
                            border-radius: 12px;
                            padding: 14px 18px;
                            margin-bottom: 12px;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
                            transition: transform 0.2s ease, box-shadow 0.2s ease;
                        }
                        .infografis-stat-card:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 6px 12px rgba(0,0,0,0.06);
                        }
                        .infografis-stat-label {
                            font-size: 0.95rem;
                            color: #4b5563;
                            font-weight: 500;
                            margin-bottom: 3px;
                        }
                        .infografis-stat-value {
                            font-size: 1.8rem;
                            font-weight: 900;
                            color: #111827;
                            letter-spacing: -0.02em;
                            line-height: 1.1;
                        }

                        /* Status Aktif */
                        .status-aktif-stage-wrapper {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            width: 100% !important;
                            padding-top: 10px !important;
                        }
                        .status-aktif-stage {
                            display: flex !important;
                            justify-content: center !important;
                            align-items: flex-end !important;
                            gap: 42px !important;
                            height: 230px !important;
                            width: 100% !important;
                            max-width: 320px !important;
                        }
                        .status-col-aktif {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            justify-content: flex-end !important;
                            width: 95px !important;
                        }
                        .status-top-pct {
                            font-size: 1.15rem !important;
                            font-weight: 800 !important;
                            color: #0f172a !important;
                            margin-bottom: 6px !important;
                            line-height: 1 !important;
                        }
                        .status-pillar-gold {
                            width: 95px !important;
                            height: 185px;
                            background-color: #c59b27 !important;
                            border-radius: 3px 3px 0 0 !important;
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            justify-content: space-between !important;
                            padding: 12px 0 10px 0 !important;
                            box-shadow: 0 4px 12px rgba(197, 155, 39, 0.25) !important;
                            transition: height 0.6s ease !important;
                        }
                        .status-silhouette-top {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }
                        .status-silhouette-bottom {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }
                        .status-col-nonaktif {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            justify-content: flex-end !important;
                            width: 85px !important;
                        }
                        .status-silhouette-grey {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            margin-bottom: 6px !important;
                        }
                        .status-nonaktif-pct {
                            font-size: 1.1rem !important;
                            font-weight: 800 !important;
                            color: #0f172a !important;
                            margin-bottom: 4px !important;
                            line-height: 1 !important;
                        }
                        .status-pedestal-grey {
                            width: 85px !important;
                            height: 12px !important;
                            background-color: #88929e !important;
                            border-radius: 2px 2px 0 0 !important;
                        }
                        .status-stage-baseline {
                            width: 100% !important;
                            max-width: 320px !important;
                            height: 2px !important;
                            background-color: #64748b !important;
                            margin-bottom: 10px !important;
                        }
                        .status-footer-row {
                            display: flex !important;
                            justify-content: center !important;
                            gap: 42px !important;
                            width: 100% !important;
                            max-width: 320px !important;
                            text-align: center !important;
                        }
                        .status-label-col {
                            width: 110px !important;
                        }
                        .status-label-sub {
                            font-size: 0.88rem !important;
                            font-weight: 700 !important;
                            color: #334155 !important;
                            line-height: 1.2 !important;
                        }
                        .status-label-count {
                            font-size: 1.25rem !important;
                            font-weight: 900 !important;
                            color: #0f172a !important;
                            line-height: 1.2 !important;
                            margin-top: 2px !important;
                        }

                        /* Sebaran Wilayah */
                        .wilayah-table-wrapper {
                            width: 100% !important;
                        }
                        .wilayah-head-row {
                            display: flex !important;
                            align-items: center !important;
                            font-size: 1.1rem !important;
                            font-weight: 800 !important;
                            color: #0f172a !important;
                            padding-bottom: 8px !important;
                            margin-bottom: 4px !important;
                            border-bottom: 2px solid #e2e8f0 !important;
                            width: 100% !important;
                        }
                        .wilayah-th-name {
                            width: 26% !important;
                        }
                        .wilayah-th-total {
                            width: 74% !important;
                            text-align: right !important;
                            padding-right: 15px !important;
                        }
                        .wilayah-item-row {
                            display: flex !important;
                            align-items: center !important;
                            padding: 10px 0 !important;
                            border-bottom: 1px solid #f1f5f9 !important;
                            width: 100% !important;
                        }
                        .wilayah-item-row:last-child {
                            border-bottom: none !important;
                        }
                        .wilayah-item-name {
                            width: 26% !important;
                            font-size: 1.05rem !important;
                            font-weight: 700 !important;
                            color: #1e293b !important;
                        }
                        .wilayah-item-count {
                            width: 20% !important;
                            font-size: 1.15rem !important;
                            font-weight: 800 !important;
                            color: #0f172a !important;
                            text-align: right !important;
                            padding-right: 25px !important;
                        }
                        .wilayah-item-graphic {
                            width: 54% !important;
                            display: flex !important;
                            align-items: center !important;
                            gap: 12px !important;
                        }
                        .wilayah-map-icon {
                            flex-shrink: 0 !important;
                            width: 24px !important;
                            height: 24px !important;
                        }
                        .wilayah-track {
                            flex-grow: 1 !important;
                            height: 24px !important;
                            background-color: #d8dee4 !important;
                            border-radius: 9999px !important;
                            overflow: hidden !important;
                            position: relative !important;
                            display: block !important;
                        }
                        .wilayah-bar {
                            height: 100% !important;
                            min-height: 24px !important;
                            background-color: #184e38 !important;
                            border-radius: 9999px !important;
                            display: block !important;
                            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }

                        .infografis-footer {
                            background: #14422b;
                            border-top: 3px solid #c59b27;
                            padding: 12px 20px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            color: #ffffff;
                            border-radius: 0 0 14px 14px;
                            margin-top: 22px;
                        }
                        .infografis-footer-logo {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            font-size: 1.15rem;
                            font-weight: 800;
                            letter-spacing: 0.05em;
                            color: #ffffff;
                        }
                        .infografis-footer-period {
                            font-size: 0.95rem;
                            font-weight: 600;
                            color: #e2e8f0;
                        }

                        /* ==================== INFOGRAFIS STATISTIK SANTRI ==================== */
                        .infografis-santri-wrapper {
                            background: #ffffff;
                            border-radius: 16px;
                            position: relative;
                            overflow: hidden;
                            color: #1f2937;
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        }
                        .infografis-lantern-icon-santri {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 44px;
                            height: 44px;
                            border-radius: 12px;
                            background: #f0fdfa;
                            border: 1.5px solid #0f766e;
                            color: #0f766e;
                            font-size: 1.25rem;
                            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.12);
                        }
                        .infografis-footer-santri {
                            background: #0f766e;
                            border-top: 3px solid #c59b27;
                            padding: 12px 20px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            color: #ffffff;
                            border-radius: 0 0 14px 14px;
                            margin-top: 22px;
                        }

                        /* Media Print Rules */
                        @media print {
                            @page {
                                size: A4 portrait;
                                margin: 8mm 10mm;
                            }
                            * {
                                -webkit-print-color-adjust: exact !important;
                                print-color-adjust: exact !important;
                                color-adjust: exact !important;
                            }
                            html, body {
                                background: #ffffff !important;
                                margin: 0 !important;
                                padding: 0 !important;
                            }
                            .infografis-guru-wrapper, .infografis-santri-wrapper {
                                padding: 10px 15px !important;
                                border: none !important;
                                box-shadow: none !important;
                            }
                            .infografis-section-head {
                                page-break-after: avoid !important;
                            }
                            #wrapperChartGuruPerTpq, #wrapperChartSantriPerTpq, #wrapperChartSantriPerKelas, #wrapperChartRasioGuruSantri {
                                page-break-inside: avoid !important;
                            }
                            .no-print, .card-header, .main-sidebar, .main-header, .main-footer {
                                display: none !important;
                            }
                        }
                        </style>

                        <?php
                        $RingkasanStatistikGuru = $RingkasanStatistikGuru ?? [];
                        $ListTpq = $ListTpq ?? [];
                        $listKelurahanGuru = $RingkasanStatistikGuru['list_kelurahan'] ?? [];
                        $totalValidGuru = $RingkasanStatistikGuru['total_valid'] ?? 0;
                        $demografiUsiaGuru = $RingkasanStatistikGuru['demografi_usia'] ?? [];
                        $avgAgeGuru = $demografiUsiaGuru['avg_age'] ?? 0;
                        $minAgeGuru = $demografiUsiaGuru['min_age'] ?? 0;
                        $maxAgeGuru = $demografiUsiaGuru['max_age'] ?? 0;
                        $jkGuru = $RingkasanStatistikGuru['jenis_kelamin'] ?? [];
                        $stGuru = $RingkasanStatistikGuru['status_aktif'] ?? [];
                        $wilayahGuru = $RingkasanStatistikGuru['sebaran_wilayah'] ?? [];
                        $guruPerTpq = $RingkasanStatistikGuru['guru_per_tpq'] ?? [];
                        $periodeTextGuru = $RingkasanStatistikGuru['periode_text'] ?? ('Data per ' . date('F Y'));
                        ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-success shadow-sm" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #1e5138;">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap py-2 px-3 no-print">
                                        <div class="d-flex align-items-center mb-2 mb-xl-0">
                                            <i class="fas fa-chart-line text-success fa-lg mr-2"></i>
                                            <h3 class="card-title font-weight-bold mb-0 text-dark" style="font-size: 1.15rem;">
                                                Ringkasan Statistik Guru TPQ
                                            </h3>
                                        </div>
                                        <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 10px;">
                                            <!-- Filter TPQ -->
                                            <div class="d-flex align-items-center">
                                                <label for="selectFilterTpqGuru" class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-building mr-1"></i>TPQ:</label>
                                                <select id="selectFilterTpqGuru" class="form-control form-control-sm" style="min-width: 170px; border-radius: 8px;">
                                                    <option value="0">Semua TPQ</option>
                                                    <?php if (!empty($ListTpq)): ?>
                                                        <?php foreach ($ListTpq as $tpq): ?>
                                                            <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <!-- Filter Kelurahan / Desa -->
                                            <div class="d-flex align-items-center">
                                                <label for="selectFilterKelurahanGuru" class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-map-marker-alt mr-1"></i>Wilayah:</label>
                                                <select id="selectFilterKelurahanGuru" class="form-control form-control-sm" style="min-width: 160px; border-radius: 8px;">
                                                    <option value="">Semua Wilayah</option>
                                                    <?php if (!empty($listKelurahanGuru)): ?>
                                                        <?php foreach ($listKelurahanGuru as $kel): ?>
                                                            <option value="<?= esc($kel) ?>"><?= esc($kel) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <!-- Toggle Status Aktif / Non-Aktif -->
                                            <div class="d-flex align-items-center">
                                                <label class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-toggle-on mr-1"></i>Status:</label>
                                                <div class="btn-group btn-group-sm guru-status-toggle" role="group">
                                                    <button type="button" class="btn btn-sm btn-success active btn-toggle-status" data-status="all" style="border-radius: 8px 0 0 8px; font-weight: 600;">Semua</button>
                                                    <button type="button" class="btn btn-sm btn-outline-success btn-toggle-status" data-status="1" style="font-weight: 600;">Aktif</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-status" data-status="0" style="border-radius: 0 8px 8px 0; font-weight: 600;">Non-Aktif</button>
                                                </div>
                                            </div>
                                            <!-- Action Buttons -->
                                            <button type="button" class="btn btn-sm btn-outline-info ml-1" id="btnCopyGuruInfographic" style="border-radius: 8px;" onclick="copyGuruInfographicPNG()" title="Capture & Salin PNG ke Clipboard">
                                                <i class="fas fa-copy mr-1"></i> Salin PNG
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="btnDownloadGuruInfographic" style="border-radius: 8px;" onclick="downloadGuruInfographicPNG()" title="Unduh File Gambar PNG">
                                                <i class="fas fa-download mr-1"></i> Unduh PNG
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success ml-1" style="border-radius: 8px;" onclick="printGuruInfographic()" title="Cetak / PDF Infografis">
                                                <i class="fas fa-print mr-1"></i> Cetak PDF
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize" title="Layar Penuh">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div id="infografis-card-print" class="infografis-guru-wrapper p-3 p-md-4">
                                            <!-- Watermark ornament -->
                                            <div class="infografis-watermark"></div>

                                            <!-- Header Infografis -->
                                            <div class="infografis-header">
                                                <div class="infografis-accent-dots d-none d-sm-grid">
                                                    <span></span><span></span><span></span><span></span>
                                                </div>
                                                <div class="infografis-lantern d-none d-sm-block">
                                                    <div class="infografis-lantern-icon">
                                                        <i class="fas fa-mosque"></i>
                                                    </div>
                                                </div>
                                                <div class="infografis-eyebrow">RINGKASAN STATISTIK</div>
                                                <div class="infografis-main-title">GURU TPQ</div>
                                                <div class="infografis-total-pill">
                                                    <span>Total Data Valid:</span>
                                                    <span id="txtTotalValidGuru" class="infografis-total-badge"><?= $totalValidGuru ?> GURU</span>
                                                </div>
                                                <div id="txtNamaTpqSubtitle" class="text-muted small mt-1 font-weight-bold" style="display: none;"></div>
                                            </div>

                                            <!-- 1. DEMOGRAFI USIA -->
                                            <div class="infografis-section-head">
                                                <div class="infografis-section-badge">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <h4 class="infografis-section-title">DEMOGRAFI USIA</h4>
                                                <div class="infografis-section-divider">
                                                    <div class="line"></div>
                                                    <div class="dots">
                                                        <span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center">
                                                <div class="col-lg-8 mb-3 mb-lg-0">
                                                    <div class="font-weight-bold mb-2 text-dark" style="font-size: 1.05rem;">Distribusi Usia Guru</div>
                                                    <div style="position: relative; height: 260px; width: 100%;">
                                                        <canvas id="chartUsiaGuru"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="infografis-stat-card">
                                                        <div class="infografis-stat-label">Rata-rata Usia:</div>
                                                        <div class="infografis-stat-value" id="valAvgAge"><?= $avgAgeGuru ?> TAHUN</div>
                                                    </div>
                                                    <div class="infografis-stat-card">
                                                        <div class="infografis-stat-label">Usia Minimum:</div>
                                                        <div class="infografis-stat-value" id="valMinAge"><?= $minAgeGuru ?> TAHUN</div>
                                                    </div>
                                                    <div class="infografis-stat-card">
                                                        <div class="infografis-stat-label">Usia Maksimum:</div>
                                                        <div class="infografis-stat-value" id="valMaxAge"><?= $maxAgeGuru ?> TAHUN</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. JENIS KELAMIN & STATUS AKTIF -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge">
                                                    <i class="fas fa-venus-mars"></i>
                                                </div>
                                                <h4 class="infografis-section-title">JENIS KELAMIN &amp; STATUS AKTIF</h4>
                                                <div class="infografis-section-divider">
                                                    <div class="line"></div>
                                                    <div class="dots">
                                                        <span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center">
                                                <!-- Distribusi Jenis Kelamin -->
                                                <div class="col-lg-6 mb-4 mb-lg-0" style="border-right: 1px dashed #e5e7eb;">
                                                    <div class="font-weight-bold mb-2 text-dark" style="font-size: 1.05rem;">Distribusi Jenis Kelamin</div>
                                                    <div class="row align-items-center">
                                                        <div class="col-sm-7">
                                                            <div style="position: relative; height: 220px;">
                                                                <canvas id="chartJenisKelaminGuru"></canvas>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5 mt-3 mt-sm-0">
                                                            <div class="infografis-gender-item mb-3">
                                                                <div class="d-flex align-items-center">
                                                                    <span style="width: 14px; height: 14px; border-radius: 4px; background: #164e3b; display: inline-block;" class="mr-2"></span>
                                                                    <i class="fas fa-female text-success mr-1"></i>
                                                                    <span class="font-weight-bold text-dark">Perempuan:</span>
                                                                </div>
                                                                <div class="font-weight-bolder text-dark pl-4" style="font-size: 1.15rem;" id="txtCountPerempuan">
                                                                    <?= $jkGuru['perempuan'] ?? 0 ?> GURU <small class="text-muted font-weight-normal">(<?= $jkGuru['pct_perempuan'] ?? 0 ?>%)</small>
                                                                </div>
                                                            </div>
                                                            <div class="infografis-gender-item">
                                                                <div class="d-flex align-items-center">
                                                                    <span style="width: 14px; height: 14px; border-radius: 4px; background: #4ade80; display: inline-block;" class="mr-2"></span>
                                                                    <i class="fas fa-male text-info mr-1"></i>
                                                                    <span class="font-weight-bold text-dark">Laki-Laki:</span>
                                                                </div>
                                                                <div class="font-weight-bolder text-dark pl-4" style="font-size: 1.15rem;" id="txtCountLaki">
                                                                    <?= $jkGuru['laki_laki'] ?? 0 ?> GURU <small class="text-muted font-weight-normal">(<?= $jkGuru['pct_laki'] ?? 0 ?>%)</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status Aktif -->
                                                <div class="col-lg-6">
                                                    <div class="font-weight-bold mb-3 text-dark text-center" style="font-size: 1.15rem; letter-spacing: 0.02em;">Status Aktif</div>
                                                    <div class="status-aktif-stage-wrapper">
                                                        <div class="status-aktif-stage">
                                                            <!-- Aktif (1) Column -->
                                                            <div class="status-col-aktif">
                                                                <div class="status-top-pct" id="txtPctAktif"><?= $stGuru['pct_aktif'] ?? 0 ?>%</div>
                                                                <div class="status-pillar-gold" id="pillarAktif">
                                                                    <!-- Standing female silhouette -->
                                                                    <div class="status-silhouette-top">
                                                                        <svg viewBox="0 0 24 48" width="28" height="42" fill="#ffffff">
                                                                            <circle cx="12" cy="6" r="4.5"/>
                                                                            <path d="M7 13 C7 11.5, 17 11.5, 17 13 L19 28 C19 29, 17 29, 16.5 28 L15 20 L15 42 C15 43.5, 13 43.5, 13 42 L13 30 L11 30 L11 42 C11 43.5, 9 43.5, 9 42 L9 20 L7.5 28 C7 29, 5 29, 5 28 Z"/>
                                                                        </svg>
                                                                    </div>
                                                                    <!-- Sitting reading student silhouette -->
                                                                    <div class="status-silhouette-bottom">
                                                                        <svg viewBox="0 0 32 32" width="34" height="34" fill="#ffffff">
                                                                            <circle cx="16" cy="7" r="4.5"/>
                                                                            <path d="M10 13 C10 12, 22 12, 22 13 L23 18 C23 20, 20 21, 16 21 C12 21, 9 20, 9 18 Z"/>
                                                                            <path d="M8 22 C8 20, 24 20, 24 22 L26 27 C26 29, 23 30, 16 30 C9 30, 6 29, 6 27 Z"/>
                                                                            <polygon points="12 26, 16 23, 20 26, 16 28" fill="#ffffff" stroke="#c59b27" stroke-width="1"/>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Non-Aktif (0) Column -->
                                                            <div class="status-col-nonaktif">
                                                                <!-- Standing grey person silhouette on top -->
                                                                <div class="status-silhouette-grey">
                                                                    <svg viewBox="0 0 24 48" width="28" height="42" fill="#88929e">
                                                                        <circle cx="12" cy="6" r="4.5"/>
                                                                        <path d="M7 13 C7 11.5, 17 11.5, 17 13 L18 26 C18 27.5, 16.5 27.5, 16 26 L15 18 L15 42 C15 43.5, 13 43.5, 13 42 L13 29 L11 29 L11 42 C11 43.5, 9 43.5, 9 42 L9 18 L8 26 C7.5 27.5, 6 27.5, 6 26 Z"/>
                                                                    </svg>
                                                                </div>
                                                                <!-- Percentage in middle -->
                                                                <div class="status-nonaktif-pct" id="txtPctNonAktif"><?= $stGuru['pct_non_aktif'] ?? 0 ?>%</div>
                                                                <!-- Pedestal on bottom resting on baseline -->
                                                                <div class="status-pedestal-grey"></div>
                                                            </div>
                                                        </div>

                                                        <!-- Baseline horizontal line -->
                                                        <div class="status-stage-baseline"></div>

                                                        <!-- Labels & counts below baseline -->
                                                        <div class="status-footer-row">
                                                            <div class="status-label-col">
                                                                <div class="status-label-sub">AKTIF (1):</div>
                                                                <div class="status-label-count" id="txtCountAktif"><?= $stGuru['aktif'] ?? 0 ?> GURU</div>
                                                            </div>
                                                            <div class="status-label-col">
                                                                <div class="status-label-sub">NON-AKTIF (0):</div>
                                                                <div class="status-label-count" id="txtCountNonAktif"><?= $stGuru['non_aktif'] ?? 0 ?> GURU</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3. SEBARAN WILAYAH -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <h4 class="infografis-section-title">SEBARAN WILAYAH</h4>
                                                <div class="infografis-section-divider">
                                                    <div class="line"></div>
                                                    <div class="dots">
                                                        <span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="wilayah-table-wrapper px-2">
                                                <div class="wilayah-head-row">
                                                    <div class="wilayah-th-name">Kelurahan/Desa</div>
                                                    <div class="wilayah-th-total">Total</div>
                                                </div>
                                                <div id="containerSebaranWilayah">
                                                    <?php
                                                    $maxWilayahCount = !empty($wilayahGuru) ? max($wilayahGuru) : 1;
                                                    $scaleMax = max(1, round($maxWilayahCount * 1.18));
                                                    foreach ($wilayahGuru as $namaKel => $jml):
                                                        $pctWidth = round(($jml / $scaleMax) * 100);
                                                    ?>
                                                        <div class="wilayah-item-row">
                                                            <div class="wilayah-item-name"><?= esc($namaKel) ?></div>
                                                            <div class="wilayah-item-count"><?= $jml ?> GURU</div>
                                                            <div class="wilayah-item-graphic">
                                                                <svg viewBox="0 0 24 24" width="24" height="24" class="wilayah-map-icon" fill="none">
                                                                    <path d="M3 6.5L8.5 4.5L15.5 7.5L21 5.5V18.5L15.5 20.5L8.5 17.5L3 19.5V6.5Z" fill="#1e5138" fill-opacity="0.2" stroke="#1e5138" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M8.5 4.5V17.5M15.5 7.5V20.5" stroke="#1e5138" stroke-width="1.6" stroke-linecap="round"/>
                                                                    <path d="M12 4C10.34 4 9 5.34 9 7C9 9.25 12 12.5 12 12.5C12 12.5 15 9.25 15 7C15 5.34 13.66 4 12 4Z" fill="#1e5138"/>
                                                                    <circle cx="12" cy="7" r="1.2" fill="#ffffff"/>
                                                                </svg>
                                                                <div class="wilayah-track">
                                                                    <div class="wilayah-bar" style="width: <?= $pctWidth ?>%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <!-- 4. STATISTIK GURU PER TPQ -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge">
                                                    <i class="fas fa-mosque"></i>
                                                </div>
                                                <h4 class="infografis-section-title">STATISTIK GURU PER TPQ</h4>
                                                <div class="infografis-section-divider">
                                                    <div class="line"></div>
                                                    <div class="dots">
                                                        <span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="px-2">
                                                <div class="row align-items-center mb-3">
                                                    <div class="col-md-7 d-flex align-items-center flex-wrap" style="gap: 10px;">
                                                        <span class="badge badge-light border text-dark px-3 py-2 shadow-xs" style="font-size: 0.95rem; border-radius: 8px;">
                                                            <i class="fas fa-mosque text-success mr-1"></i> <strong id="txtTpqCount"><?= $guruPerTpq['total_tpq'] ?? 0 ?></strong> Lembaga TPQ
                                                        </span>
                                                        <span class="badge badge-light border text-dark px-3 py-2 shadow-xs" style="font-size: 0.95rem; border-radius: 8px;">
                                                            <i class="fas fa-trophy text-warning mr-1"></i> Terbanyak: <strong id="txtTopTpqName"><?= !empty($guruPerTpq['labels'][0]) ? esc($guruPerTpq['labels'][0]) : '-' ?></strong> (<span id="txtTopTpqCount"><?= !empty($guruPerTpq['totals'][0]) ? $guruPerTpq['totals'][0] : 0 ?></span> Guru)
                                                        </span>
                                                    </div>
                                                    <div class="col-md-5 d-flex justify-content-md-end align-items-center mt-2 mt-md-0" style="gap: 16px;">
                                                        <div class="d-flex align-items-center font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                                                            <span style="display:inline-block; width: 14px; height: 14px; background-color: #1e5138; border-radius: 3px; margin-right: 6px;"></span>
                                                            Perempuan
                                                        </div>
                                                        <div class="d-flex align-items-center font-weight-bold" style="font-size: 0.9rem; color: #1e293b;">
                                                            <span style="display:inline-block; width: 14px; height: 14px; background-color: #c59b27; border-radius: 3px; margin-right: 6px;"></span>
                                                            Laki-laki
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="wrapperChartGuruPerTpq" style="position: relative; width: 100%; min-height: 280px; height: 580px;">
                                                    <canvas id="chartGuruPerTpq"></canvas>
                                                </div>
                                            </div>

                                            <!-- 5. FOOTER -->
                                            <div class="infografis-footer">
                                                <div class="infografis-footer-logo">
                                                    <i class="fas fa-award text-warning"></i> FKPQ &bull; TPQ SMART
                                                </div>
                                                <div class="infografis-footer-period" id="txtFooterPeriode">
                                                    <?= esc($periodeTextGuru) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- ==================== RINGKASAN STATISTIK SANTRI & ANALISIS RASIO (INFOGRAFIS) ==================== -->
                        <?php
                        $RingkasanStatistikSantri = $RingkasanStatistikSantri ?? [];
                        $ListTpq = $ListTpq ?? [];
                        $listKelurahanSantri = $RingkasanStatistikSantri['list_kelurahan'] ?? [];
                        $totalValidSantri = $RingkasanStatistikSantri['total_valid'] ?? ($StatistikSantri['total'] ?? 0);
                        $kpiSantri = $RingkasanStatistikSantri['kpi'] ?? [];
                        $genderSantri = $RingkasanStatistikSantri['komposisi_gender'] ?? [];
                        $santriPerKelas = $RingkasanStatistikSantri['santri_per_kelas'] ?? [];
                        $santriPerTpq = $RingkasanStatistikSantri['santri_per_tpq'] ?? [];
                        $rasioSantri = $RingkasanStatistikSantri['rasio_guru_santri'] ?? [];
                        $matriksSantri = $RingkasanStatistikSantri['matriks_detail'] ?? [];
                        $periodeTextSantri = $RingkasanStatistikSantri['periode_text'] ?? ('Data per ' . date('F Y'));
                        ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-info shadow-sm" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #0f766e;">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap py-2 px-3 no-print">
                                        <div class="d-flex align-items-center mb-2 mb-xl-0">
                                            <i class="fas fa-user-graduate text-info fa-lg mr-2"></i>
                                            <h3 class="card-title font-weight-bold mb-0 text-dark" style="font-size: 1.15rem;">
                                                Statistik Santri & Analisis Rasio Guru:Santri
                                            </h3>
                                        </div>
                                        <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 10px;">
                                            <!-- Filter TPQ -->
                                            <div class="d-flex align-items-center">
                                                <label for="selectFilterTpqSantri" class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-building mr-1"></i>TPQ:</label>
                                                <select id="selectFilterTpqSantri" class="form-control form-control-sm" style="min-width: 170px; border-radius: 8px;">
                                                    <option value="0">Semua TPQ</option>
                                                    <?php if (!empty($ListTpq)): ?>
                                                        <?php foreach ($ListTpq as $tpq): ?>
                                                            <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <!-- Filter Kelurahan / Desa -->
                                            <div class="d-flex align-items-center">
                                                <label for="selectFilterKelurahanSantri" class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-map-marker-alt mr-1"></i>Wilayah:</label>
                                                <select id="selectFilterKelurahanSantri" class="form-control form-control-sm" style="min-width: 160px; border-radius: 8px;">
                                                    <option value="">Semua Wilayah</option>
                                                    <?php if (!empty($listKelurahanSantri)): ?>
                                                        <?php foreach ($listKelurahanSantri as $kel): ?>
                                                            <option value="<?= esc($kel) ?>"><?= esc($kel) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <!-- Toggle Status Aktif / Non-Aktif -->
                                            <div class="d-flex align-items-center">
                                                <label class="mb-0 mr-1 font-weight-bold text-muted small"><i class="fas fa-toggle-on mr-1"></i>Status:</label>
                                                <div class="btn-group btn-group-sm santri-status-toggle" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info btn-toggle-santri-status" data-status="all" style="border-radius: 8px 0 0 8px; font-weight: 600;">Semua</button>
                                                    <button type="button" class="btn btn-sm btn-info active btn-toggle-santri-status" data-status="1" style="font-weight: 600;">Aktif</button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning btn-toggle-santri-status" data-status="0" style="font-weight: 600;">Baru</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-santri-status" data-status="2" style="border-radius: 0 8px 8px 0; font-weight: 600;">Non-Aktif</button>
                                                </div>
                                            </div>
                                            <!-- Action Buttons -->
                                            <button type="button" class="btn btn-sm btn-outline-info ml-1" id="btnCopySantriInfographic" style="border-radius: 8px;" onclick="copySantriInfographicPNG()" title="Capture & Salin PNG ke Clipboard">
                                                <i class="fas fa-copy mr-1"></i> Salin PNG
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="btnDownloadSantriInfographic" style="border-radius: 8px;" onclick="downloadSantriInfographicPNG()" title="Unduh File Gambar PNG">
                                                <i class="fas fa-download mr-1"></i> Unduh PNG
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success ml-1" id="btnPrintSantriInfographic" style="border-radius: 8px;" onclick="printSantriInfographic()" title="Cetak / PDF Infografis">
                                                <i class="fas fa-print mr-1"></i> Cetak PDF
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize" title="Layar Penuh">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div id="infografis-santri-card-print" class="infografis-santri-wrapper p-3 p-md-4">
                                            <!-- Watermark ornament -->
                                            <div class="infografis-watermark"></div>

                                            <!-- HEADER INFOGRAFIS -->
                                            <div class="infografis-header">
                                                <div class="infografis-accent-dots">
                                                    <span></span><span></span>
                                                    <span></span><span></span>
                                                </div>
                                                <div class="infografis-lantern">
                                                    <div class="infografis-lantern-icon-santri">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </div>
                                                </div>
                                                <div class="infografis-eyebrow" style="color: #0f766e;">DATA POKOK PENDIDIKAN TPQ</div>
                                                <h2 class="infografis-main-title" style="color: #0f172a;">STATISTIK SANTRI & RASIO GURU:SANTRI</h2>
                                                <div class="infografis-total-pill">
                                                    TOTAL SANTRI TERDATA: 
                                                    <span class="infografis-total-badge" id="txtTotalValidSantri" style="background: #0f766e;">
                                                        <?= number_format($totalValidSantri) ?>
                                                    </span> 
                                                    SANTRI
                                                    <span id="txtFilterBadgeSantri" class="badge badge-light border ml-2 text-dark font-weight-bold" style="font-size: 0.85rem; padding: 4px 10px; border-radius: 8px;">
                                                        <i class="fas fa-filter text-muted mr-1"></i>Semua Wilayah & TPQ
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- TOP KPI MINI CARDS -->
                                            <div class="row mt-3 mb-2">
                                                <div class="col-6 col-lg-3 mb-2">
                                                    <div class="infografis-stat-card border-top" style="border-top: 3px solid #0f766e !important;">
                                                        <div class="infografis-stat-label text-muted small font-weight-bold text-uppercase">
                                                            <i class="fas fa-users text-teal mr-1"></i> Total Santri
                                                        </div>
                                                        <div class="infografis-stat-value text-dark" id="kpiSantriTotalVal">
                                                            <?= number_format($totalValidSantri) ?>
                                                        </div>
                                                        <small class="text-muted font-weight-bold" id="kpiSantriTotalTpqVal">
                                                            Dari <?= count($santriPerTpq['labels'] ?? []) ?> TPQ Terdata
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-lg-3 mb-2">
                                                    <div class="infografis-stat-card border-top" style="border-top: 3px solid #0284c7 !important;">
                                                        <div class="infografis-stat-label text-muted small font-weight-bold text-uppercase">
                                                            <i class="fas fa-venus-mars text-primary mr-1"></i> Komposisi Gender
                                                        </div>
                                                        <div class="infografis-stat-value text-dark" style="font-size: 1.45rem;" id="kpiSantriGenderVal">
                                                            <span class="text-info" id="kpiSantriGenderLakiVal"><?= number_format($genderSantri['laki_laki'] ?? 0) ?> L</span> 
                                                            <span class="text-muted" style="font-size: 1.1rem;">&bull;</span> 
                                                            <span class="text-pink" style="color: #ec4899;" id="kpiSantriGenderPerempuanVal"><?= number_format($genderSantri['perempuan'] ?? 0) ?> P</span>
                                                        </div>
                                                        <small class="text-muted font-weight-bold" id="kpiSantriGenderPctVal">
                                                            <?= $genderSantri['pct_laki'] ?? 0 ?>% Laki &bull; <?= $genderSantri['pct_perempuan'] ?? 0 ?>% Perempuan
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-lg-3 mb-2">
                                                    <div class="infografis-stat-card border-top" style="border-top: 3px solid #10b981 !important;">
                                                        <div class="infografis-stat-label text-muted small font-weight-bold text-uppercase">
                                                            <i class="fas fa-balance-scale text-success mr-1"></i> Rata-Rata Rasio
                                                        </div>
                                                        <div class="infografis-stat-value text-success" id="kpiSantriAvgRasioVal">
                                                            1 : <?= $kpiSantri['avg_rasio_ceil'] ?? 0 ?>
                                                        </div>
                                                        <small class="text-muted font-weight-bold" id="kpiSantriAvgRasioSub">
                                                            1 Guru : <?= $kpiSantri['avg_rasio'] ?? 0 ?> Santri
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-lg-3 mb-2">
                                                    <div class="infografis-stat-card border-top" style="border-top: 3px solid #c59b27 !important;">
                                                        <div class="infografis-stat-label text-muted small font-weight-bold text-uppercase">
                                                            <i class="fas fa-check-circle text-warning mr-1"></i> Status Rasio Lembaga
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap pt-1" style="gap: 5px;" id="kpiSantriStatusBadges">
                                                            <span class="badge badge-success px-2 py-1 font-weight-bold" id="kpiSantriCountIdealVal" title="Ideal (1:9 - 1:11)"><?= $kpiSantri['count_ideal'] ?? 0 ?> Ideal</span>
                                                            <span class="badge badge-info px-2 py-1 font-weight-bold" id="kpiSantriCountPadatVal" title="Padat / Butuh Tambahan Guru (> 1:11)"><?= $kpiSantri['count_padat'] ?? 0 ?> Padat</span>
                                                            <span class="badge badge-danger px-2 py-1 font-weight-bold" id="kpiSantriCountRendahVal" title="Rasio Rendah (< 1:9)"><?= $kpiSantri['count_rendah'] ?? 0 ?> Rendah</span>
                                                        </div>
                                                        <small class="text-muted font-weight-bold d-block mt-1">
                                                            Standar FKPQ (1:9 - 1:11)
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 1. DISTRIBUSI SANTRI PER TINGKATAN KELAS -->
                                            <div class="infografis-section-head">
                                                <div class="infografis-section-badge" style="background: #0f766e;">1</div>
                                                <h3 class="infografis-section-title">Distribusi Santri Per Tingkatan Kelas</h3>
                                                <div class="infografis-section-divider">
                                                    <div class="line" style="background: #0f766e;"></div>
                                                    <div class="dots"><div class="dot" style="background: #0f766e;"></div><div class="dot" style="background: #c59b27;"></div></div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-3">
                                                <div class="col-md-7">
                                                    <span class="badge badge-light border text-dark px-3 py-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 8px;">
                                                        <i class="fas fa-layer-group text-info mr-1"></i> <strong id="txtKelasCountSantri"><?= count($santriPerKelas['labels'] ?? []) ?></strong> Tingkatan Kelas Aktif
                                                    </span>
                                                </div>
                                                <div class="col-md-5 d-flex justify-content-md-end align-items-center font-weight-bold mt-2 mt-md-0" style="gap: 16px; font-size: 0.9rem;">
                                                    <div class="d-flex align-items-center">
                                                        <span style="display:inline-block; width: 14px; height: 14px; background-color: #0284c7; border-radius: 3px; margin-right: 6px;"></span>
                                                        Laki-laki
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span style="display:inline-block; width: 14px; height: 14px; background-color: #ec4899; border-radius: 3px; margin-right: 6px;"></span>
                                                        Perempuan
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-8 mb-4 mb-lg-0">
                                                    <div id="wrapperChartSantriPerKelas" style="position: relative; width: 100%; min-height: 280px; height: 320px;">
                                                        <canvas id="chartSantriPerKelas"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="table-responsive border" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
                                                        <table class="table table-sm table-striped table-hover mb-0" id="tableRingkasSantriKelas">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="padding-left: 12px;">Tingkat Kelas</th>
                                                                    <th class="text-center text-info">L</th>
                                                                    <th class="text-center" style="color: #ec4899;">P</th>
                                                                    <th class="text-center font-weight-bold">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyRingkasSantriKelas">
                                                                <?php if (!empty($santriPerKelas['items'])): ?>
                                                                    <?php foreach ($santriPerKelas['items'] as $k): ?>
                                                                        <tr>
                                                                            <td class="font-weight-bold text-dark" style="padding-left: 12px;"><?= esc($k['NamaKelas']) ?></td>
                                                                            <td class="text-center text-info font-weight-bold"><?= number_format($k['LakiLaki']) ?></td>
                                                                            <td class="text-center font-weight-bold" style="color: #ec4899;"><?= number_format($k['Perempuan']) ?></td>
                                                                            <td class="text-center font-weight-bold text-dark"><?= number_format($k['Total']) ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <tr><td colspan="4" class="text-center py-2 text-muted">Tidak ada data kelas</td></tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. SEBARAN SANTRI PER TPQ -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge" style="background: #0284c7;">2</div>
                                                <h3 class="infografis-section-title">Data Sebaran Santri Per TPQ</h3>
                                                <div class="infografis-section-divider">
                                                    <div class="line" style="background: #0284c7;"></div>
                                                    <div class="dots"><div class="dot" style="background: #0284c7;"></div><div class="dot" style="background: #c59b27;"></div></div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                                    <span class="badge badge-light border text-dark px-3 py-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 8px;">
                                                        <i class="fas fa-mosque text-primary mr-1"></i> <strong id="txtTpqCountSantri"><?= count($santriPerTpq['labels'] ?? []) ?></strong> TPQ Terdata
                                                    </span>
                                                    <span class="badge badge-light border text-dark px-3 py-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 8px;">
                                                        <i class="fas fa-trophy text-warning mr-1"></i> Santri Terbanyak: <strong id="txtTopTpqNameSantri"><?= esc($santriPerTpq['top_tpq_nama'] ?? '-') ?></strong> (<span id="txtTopTpqCountSantri"><?= number_format($santriPerTpq['top_tpq_count'] ?? 0) ?></span> Santri)
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center font-weight-bold mt-2 mt-md-0" style="gap: 16px; font-size: 0.9rem;">
                                                    <div class="d-flex align-items-center">
                                                        <span style="display:inline-block; width: 14px; height: 14px; background-color: #0284c7; border-radius: 3px; margin-right: 6px;"></span>
                                                        Laki-laki
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span style="display:inline-block; width: 14px; height: 14px; background-color: #ec4899; border-radius: 3px; margin-right: 6px;"></span>
                                                        Perempuan
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="wrapperChartSantriPerTpq" style="position: relative; width: 100%; min-height: 280px; height: <?= max(300, count($santriPerTpq['labels'] ?? []) * 28 + 60) ?>px;">
                                                <canvas id="chartSantriPerTpq"></canvas>
                                            </div>

                                            <!-- 3. ANALISIS RASIO GURU : SANTRI PER TPQ -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge" style="background: #10b981;">3</div>
                                                <h3 class="infografis-section-title">Analisis Rasio Guru : Santri Per TPQ</h3>
                                                <div class="infografis-section-divider">
                                                    <div class="line" style="background: #10b981;"></div>
                                                    <div class="dots"><div class="dot" style="background: #10b981;"></div><div class="dot" style="background: #c59b27;"></div></div>
                                                </div>
                                            </div>

                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-8">
                                                    <div class="p-2 px-3 bg-light border" style="border-radius: 10px; font-size: 0.88rem;">
                                                        <i class="fas fa-info-circle text-info mr-1"></i>
                                                        Standar proporsional FKPQ adalah <strong>1 Guru : 9 s/d 11 Santri</strong>. 
                                                        <span class="badge badge-success px-2 py-1 mx-1 font-weight-bold">Hijau (1:9 - 1:11) Ideal</span>
                                                        <span class="badge badge-info px-2 py-1 mx-1 font-weight-bold">Biru (> 1:11) Padat / Butuh Guru</span>
                                                        <span class="badge badge-danger px-2 py-1 mx-1 font-weight-bold">Merah (< 1:9) Rendah</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                                        </div>
                                                        <input type="text" id="inputSearchTpqRasio" class="form-control form-control-sm" placeholder="Cari TPQ / Wilayah..." style="border-radius: 0 8px 8px 0;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-stretch">
                                                <div class="col-lg-7 mb-4 mb-lg-0 d-flex flex-column">
                                                    <div id="wrapperChartRasioGuruSantri" class="flex-grow-1" style="position: relative; width: 100%; min-height: 320px; height: <?= max(320, count($rasioSantri['items'] ?? []) * 39 + 42) ?>px;">
                                                        <canvas id="chartRasioGuruSantri"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 d-flex flex-column">
                                                    <div class="table-responsive border flex-grow-1" style="border-radius: 12px; background: #ffffff;">
                                                        <table class="table table-sm table-hover mb-0" id="tableListRasioTpq">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="padding-left: 12px;">Lembaga TPQ</th>
                                                                    <th class="text-center">Guru</th>
                                                                    <th class="text-center">Santri</th>
                                                                    <th class="text-center">Rasio</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyListRasioTpq">
                                                                <?php if (!empty($rasioSantri['items'])): ?>
                                                                    <?php foreach ($rasioSantri['items'] as $r): ?>
                                                                        <tr class="row-rasio-tpq">
                                                                            <td style="padding-left: 12px;">
                                                                                <div class="font-weight-bold text-dark"><?= esc($r['NamaTpq']) ?></div>
                                                                            </td>
                                                                            <td class="text-center font-weight-bold text-success align-middle">
                                                                                <?= number_format($r['TotalGuru']) ?>
                                                                            </td>
                                                                            <td class="text-center font-weight-bold text-primary align-middle">
                                                                                <?= number_format($r['TotalSantri']) ?>
                                                                            </td>
                                                                            <td class="text-center align-middle">
                                                                                <span class="badge badge-<?= esc($r['BadgeColor']) ?> px-2 py-1 font-weight-bold">
                                                                                    <?= esc($r['Rasio']) ?>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <tr><td colspan="4" class="text-center py-3 text-muted">Data rasio tidak ditemukan</td></tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 4. MATRIKS DETAIL SEBARAN SANTRI PER KELAS -->
                                            <div class="infografis-section-head mt-4">
                                                <div class="infografis-section-badge" style="background: #6366f1;">4</div>
                                                <h3 class="infografis-section-title">Matriks Detail Sebaran Santri Per TPQ & Per Kelas</h3>
                                                <div class="infografis-section-divider">
                                                    <div class="line" style="background: #6366f1;"></div>
                                                    <div class="dots"><div class="dot" style="background: #6366f1;"></div><div class="dot" style="background: #c59b27;"></div></div>
                                                </div>
                                            </div>

                                            <div class="table-responsive border" style="border-radius: 12px; background: #ffffff; overflow-x: auto;">
                                                <table id="tblStatistikSantriPerTpqPerKelas" class="table table-bordered table-striped table-hover table-sm mb-0 text-nowrap" style="width: 100%; font-size: 0.9rem;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th class="align-middle text-left" style="min-width: 190px; padding-left: 12px;">Nama TPQ</th>
                                                            <th class="align-middle text-left" style="min-width: 140px;">Kelurahan/Desa</th>
                                                            <?php if (!empty($matriksSantri['kelasList'])): ?>
                                                                <?php foreach ($matriksSantri['kelasList'] as $kelas): ?>
                                                                    <th class="text-center align-middle" style="min-width: 75px;"><?= esc($kelas['NamaKelas']) ?></th>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                            <th class="align-middle text-center bg-info text-white font-weight-bold" style="min-width: 85px;">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodyMatriksSantri">
                                                        <?php if (!empty($matriksSantri['data'])): ?>
                                                            <?php foreach ($matriksSantri['data'] as $tpq): ?>
                                                                <tr>
                                                                    <td class="text-left font-weight-bold align-middle" style="padding-left: 12px;"><?= esc($tpq['NamaTpq']) ?></td>
                                                                    <td class="text-left align-middle"><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                    <?php if (!empty($matriksSantri['kelasList'])): ?>
                                                                        <?php foreach ($matriksSantri['kelasList'] as $kelas): ?>
                                                                            <?php $jumlah = $tpq['Kelas'][$kelas['IdKelas']]['Jumlah'] ?? 0; ?>
                                                                            <td class="text-center align-middle"><?= $jumlah > 0 ? number_format($jumlah) : '-' ?></td>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                    <td class="text-center bg-light font-weight-bold align-middle"><?= number_format($tpq['Total']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="<?= 3 + count($matriksSantri['kelasList'] ?? []) ?>" class="text-center py-3 text-muted">Data matriks santri per kelas belum tersedia</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot id="tfootMatriksSantri" class="thead-light">
                                                        <tr>
                                                            <th class="bg-primary text-white text-left font-weight-bold align-middle" style="padding-left: 12px;">Total</th>
                                                            <th class="bg-primary text-white align-middle"></th>
                                                            <?php if (!empty($matriksSantri['kelasList'])): ?>
                                                                <?php foreach ($matriksSantri['kelasList'] as $kelas): ?>
                                                                    <th class="text-center bg-primary text-white font-weight-bold align-middle"><?= number_format($matriksSantri['totalPerKelas'][$kelas['IdKelas']] ?? 0) ?></th>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                            <th class="text-center bg-primary text-white font-weight-bold align-middle"><?= number_format($matriksSantri['grandTotal'] ?? 0) ?></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <!-- 5. FOOTER INFOGRAFIS -->
                                            <div class="infografis-footer">
                                                <div class="infografis-footer-logo">
                                                    <i class="fas fa-award text-warning"></i> FKPQ &bull; TPQ SMART
                                                </div>
                                                <div class="infografis-footer-period" id="txtFooterPeriodeSantri">
                                                    <?= esc($periodeTextSantri) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Progress Penilaian per TPQ -->
                        <?php if (!empty($StatistikProgressNilaiPerTpq) && ($IsAdmin ?? false)): ?>
                            <?php
                            // Tentukan semester saat ini
                            $currentMonth = date('n');
                            $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card card-warning card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-chart-line"></i> Statistik Progress Penilaian per TPQ
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-warning btn-sm" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-3">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Informasi:</strong> Tabel ini menampilkan progress pengisian nilai per TPQ dan per kelas. Klik pada baris TPQ untuk melihat detail per kelas.
                                            </p>

                                            <?php
                                            // Tentukan semester saat ini menggunakan helper function
                                            $isSemesterGanjil = isSemesterGanjil();
                                            $isSemesterGenap = isSemesterGenap();
                                            ?>

                                            <?php
                                            // Loop untuk kedua semester (Ganjil dan Genap)
                                            $semesterList = [
                                                'Ganjil' => [
                                                    'data' => $StatistikProgressNilaiPerTpq['Ganjil'] ?? [],
                                                    'isActive' => $isSemesterGanjil,
                                                    'tableId' => 'tabelProgressNilaiGanjil'
                                                ],
                                                'Genap' => [
                                                    'data' => $StatistikProgressNilaiPerTpq['Genap'] ?? [],
                                                    'isActive' => $isSemesterGenap,
                                                    'tableId' => 'tabelProgressNilaiGenap'
                                                ]
                                            ];
                                            ?>
                                            <?php foreach ($semesterList as $semesterName => $semesterData): ?>
                                                <!-- Semester <?= $semesterName ?> -->
                                                <div class="mb-4">
                                                    <div class="card card-outline card-secondary <?= !$semesterData['isActive'] ? 'collapsed-card' : '' ?>">
                                                        <div class="card-header">
                                                            <h5 class="mb-0">
                                                                <i class="fas fa-book-reader"></i> Semester <?= $semesterName ?> TA <?= esc($TahunAjaran ?? '') ?>
                                                            </h5>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                                    <i class="fas <?= !$semesterData['isActive'] ? 'fa-plus' : 'fa-minus' ?>"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table id="<?= $semesterData['tableId'] ?>" class="table table-bordered table-striped table-hover">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width: 50px;"></th>
                                                                            <th>Nama TPQ</th>
                                                                            <th>Kelurahan/Desa</th>
                                                                            <th class="text-center">Total Santri</th>
                                                                            <th class="text-center">Sudah Dinilai</th>
                                                                            <th class="text-center">Belum Dinilai</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (!empty($semesterData['data'])): ?>
                                                                            <?php foreach ($semesterData['data'] as $tpq): ?>
                                                                                <?php
                                                                                $tpqKey = md5($tpq['IdTpq']);
                                                                                $hasDetail = !empty($tpq['Kelas']) && count($tpq['Kelas']) > 0;
                                                                                ?>
                                                                                <!-- Row TPQ (Parent) -->
                                                                                <tr class="tpq-row" data-tpq-key="<?= $tpqKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                                    <td class="text-center">
                                                                                        <?php if ($hasDetail): ?>
                                                                                            <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                                                                                        <?php else: ?>
                                                                                            <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div>
                                                                                            <strong><?= esc($tpq['NamaTpq']) ?></strong>
                                                                                        </div>
                                                                                        <?php if (empty($tpq['StatusTpq'])): ?>
                                                                                            <div class="mt-2">
                                                                                                <div class="progress" style="height: 25px; position: relative;">
                                                                                                    <div class="progress-bar <?= $tpq['PersentaseSudah'] < 50 ? 'bg-danger' : ($tpq['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                        style="width: <?= $tpq['PersentaseSudah'] ?>%;">
                                                                                                    </div>
                                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 15px; font-weight: 500; line-height: 25px; pointer-events: none;">
                                                                                                        <?= $tpq['PersentaseSudah'] ?>%
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        <?php else: ?>
                                                                                            <div class="mt-1">
                                                                                                <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                            </div>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td><?= esc($tpq['KelurahanDesa']) ?></td>
                                                                                    <td class="text-center">
                                                                                        <span class="badge badge-info"><?= number_format($tpq['TotalSantri']) ?></span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                            <span class="badge badge-<?= esc($tpq['StatusTpqColor']) ?>"><?= esc($tpq['StatusTpq']) ?></span>
                                                                                        <?php elseif ($tpq['TotalSudahDinilai'] > 0): ?>
                                                                                            <span class="badge badge-success"><?= number_format($tpq['TotalSudahDinilai']) ?></span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge badge-secondary">-</span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php if (!empty($tpq['StatusTpq'])): ?>
                                                                                            <span class="badge badge-secondary">-</span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge badge-danger"><?= number_format($tpq['TotalBelumDinilai']) ?></span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <!-- Detail Kelas (Child rows - hidden by default) -->
                                                                                <?php if ($hasDetail): ?>
                                                                                    <?php foreach ($tpq['Kelas'] as $kelas): ?>
                                                                                        <?php
                                                                                        $kelasKey = md5($tpq['IdTpq'] . '_' . $kelas['IdKelas']);
                                                                                        $hasSantri = !empty($kelas['Santri']) && count($kelas['Santri']) > 0;

                                                                                        // Hitung progress materi per kelas
                                                                                        // Progress Materi = total materi terisi dari total materi keseluruhan (total santri × total materi per santri)
                                                                                        $persentaseMateriKelas = 0;
                                                                                        $totalMateriKelas = 0;
                                                                                        if ($hasSantri) {
                                                                                            $totalMateriTerisi = 0;
                                                                                            $totalMateriKeseluruhan = 0;
                                                                                            foreach ($kelas['Santri'] as $santri) {
                                                                                                $totalMateri = ($santri['TotalMateri'] ?? 0);
                                                                                                $materiTerisi = ($santri['MateriTerisi'] ?? 0);
                                                                                                $totalMateriKeseluruhan += $totalMateri;
                                                                                                $totalMateriTerisi += $materiTerisi;
                                                                                            }
                                                                                            $totalMateriKelas = $totalMateriKeseluruhan;
                                                                                            if ($totalMateriKeseluruhan > 0) {
                                                                                                $persentaseMateriKelas = round(($totalMateriTerisi / $totalMateriKeseluruhan) * 100, 1);
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                        <tr class="kelas-row detail-<?= $tpqKey ?>" data-kelas-key="<?= $kelasKey ?>" style="display: none; background-color: #ffffff; cursor: pointer;">
                                                                                            <td class="text-center">
                                                                                                <?php if ($hasSantri): ?>
                                                                                                    <i class="fas fa-chevron-right expand-icon-kelas" style="transition: transform 0.3s; color: #007bff;"></i>
                                                                                                <?php else: ?>
                                                                                                    <i class="fas fa-angle-right text-muted"></i>
                                                                                                <?php endif; ?>
                                                                                            </td>
                                                                                            <td colspan="2" style="padding-left: 40px;">
                                                                                                <div>
                                                                                                    <strong><?= esc($kelas['NamaKelas']) ?></strong>
                                                                                                </div>
                                                                                                <div class="mt-2">
                                                                                                    <?php if (empty($kelas['StatusKelas'])): ?>
                                                                                                        <?php
                                                                                                        // Cek apakah progress Santri dan Materi sama 100% atau sama 0%
                                                                                                        $isBoth100 = ($kelas['PersentaseSudah'] == 100 && $persentaseMateriKelas == 100 && $hasSantri && $totalMateriKelas > 0);
                                                                                                        $isBoth0 = ($kelas['PersentaseSudah'] == 0 && $persentaseMateriKelas == 0 && $hasSantri && $totalMateriKelas > 0);
                                                                                                        ?>
                                                                                                        <?php if ($isBoth100): ?>
                                                                                                            <!-- Tampilkan satu progress bar jika keduanya 100% -->
                                                                                                            <div class="progress" style="height: 20px; position: relative;">
                                                                                                                <div class="progress-bar bg-success"
                                                                                                                    style="width: 100%;">
                                                                                                                </div>
                                                                                                                <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                                                    100%
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        <?php elseif ($isBoth0): ?>
                                                                                                            <!-- Tampilkan satu progress bar jika keduanya 0% -->
                                                                                                            <div class="progress" style="height: 20px; position: relative;">
                                                                                                                <div class="progress-bar bg-danger"
                                                                                                                    style="width: 0%;">
                                                                                                                </div>
                                                                                                                <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                                                    0%
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        <?php else: ?>
                                                                                                            <!-- Tampilkan progress bar Santri -->
                                                                                                            <div class="progress" style="height: 20px; margin-bottom: 5px; position: relative;">
                                                                                                                <div class="progress-bar <?= $kelas['PersentaseSudah'] < 50 ? 'bg-danger' : ($kelas['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                                    style="width: <?= $kelas['PersentaseSudah'] ?>%;">
                                                                                                                </div>
                                                                                                                <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                                                    <?= $kelas['PersentaseSudah'] ?>% (Santri)
                                                                                                                </span>
                                                                                                            </div>
                                                                                                            <!-- Tampilkan progress bar Materi jika ada -->
                                                                                                            <?php if ($hasSantri && $totalMateriKelas > 0): ?>
                                                                                                                <div class="progress" style="height: 20px; position: relative;">
                                                                                                                    <div class="progress-bar <?= $persentaseMateriKelas < 50 ? 'bg-danger' : ($persentaseMateriKelas < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                                        style="width: <?= $persentaseMateriKelas ?>%;">
                                                                                                                    </div>
                                                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 12px; font-weight: 500; line-height: 20px; pointer-events: none;">
                                                                                                                        <?= $persentaseMateriKelas ?>% (Materi)
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                            <?php endif; ?>
                                                                                                        <?php endif; ?>
                                                                                                    <?php else: ?>
                                                                                                        <div style="margin-bottom: 5px;">
                                                                                                            <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                                        </div>
                                                                                                    <?php endif; ?>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <span class="badge badge-info"><?= number_format($kelas['TotalSantri']) ?></span>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                    <span class="badge badge-<?= esc($kelas['StatusKelasColor']) ?>"><?= esc($kelas['StatusKelas']) ?></span>
                                                                                                <?php elseif ($kelas['SudahDinilai'] > 0): ?>
                                                                                                    <span class="badge badge-success"><?= number_format($kelas['SudahDinilai']) ?></span>
                                                                                                <?php else: ?>
                                                                                                    <span class="badge badge-secondary">-</span>
                                                                                                <?php endif; ?>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <?php if (!empty($kelas['StatusKelas'])): ?>
                                                                                                    <span class="badge badge-secondary">-</span>
                                                                                                <?php else: ?>
                                                                                                    <span class="badge badge-danger"><?= number_format($kelas['BelumDinilai']) ?></span>
                                                                                                <?php endif; ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <!-- Detail Santri (Grandchild rows - hidden by default) -->
                                                                                        <?php if ($hasSantri): ?>
                                                                                            <?php foreach ($kelas['Santri'] as $santri): ?>
                                                                                                <?php
                                                                                                $santriKey = md5($santri['IdSantri'] . '_' . $kelasKey);
                                                                                                $hasMateri = ($santri['TotalMateri'] ?? 0) > 0;
                                                                                                ?>
                                                                                                <tr class="santri-row detail-<?= $kelasKey ?>" style="display: none; background-color: #f8f9fa;" data-santri-key="<?= $santriKey ?>" data-santri-id="<?= $santri['IdSantri'] ?>" data-kelas-id="<?= $kelas['IdKelas'] ?>" data-semester="<?= $semesterName ?>">
                                                                                                    <td class="text-center">
                                                                                                        <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/' . $semesterName) ?>" style="text-decoration: none; cursor: pointer;" title="Input Nilai">
                                                                                                            <?php
                                                                                                            // Ambil foto profil santri
                                                                                                            $thumbnailPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/');
                                                                                                            $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                                                                                            $photoProfil = $santri['PhotoProfil'] ?? null;
                                                                                                            if (!empty($photoProfil)) {
                                                                                                                $thumbnailFile = 'thumb_' . $photoProfil;
                                                                                                                $thumbnailFullPath = FCPATH . 'uploads/santri/thumbnails/' . $thumbnailFile;
                                                                                                                if (file_exists($thumbnailFullPath)) {
                                                                                                                    $photoUrl = $thumbnailPath . $thumbnailFile;
                                                                                                                } else {
                                                                                                                    // Fallback ke foto asli jika thumbnail tidak ada
                                                                                                                    $photoFullPath = FCPATH . 'uploads/santri/' . $photoProfil;
                                                                                                                    if (file_exists($photoFullPath)) {
                                                                                                                        $photoUrl = $uploadPath . $photoProfil;
                                                                                                                    } else {
                                                                                                                        $photoUrl = base_url('images/no-photo.jpg');
                                                                                                                    }
                                                                                                                }
                                                                                                            } else {
                                                                                                                $photoUrl = base_url('images/no-photo.jpg');
                                                                                                            }
                                                                                                            ?>
                                                                                                            <img src="<?= $photoUrl ?>"
                                                                                                                alt="Foto <?= esc($santri['NamaSantri']) ?>"
                                                                                                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6; cursor: pointer;"
                                                                                                                onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td colspan="2" style="padding-left: 80px; cursor: pointer;" class="santri-expand-cell">
                                                                                                        <?php if ($hasMateri): ?>
                                                                                                            <i class="fas fa-chevron-right expand-icon-santri" style="transition: transform 0.3s; color: #007bff; margin-right: 8px;"></i>
                                                                                                        <?php else: ?>
                                                                                                            <i class="fas fa-minus" style="color: #ccc; margin-right: 8px;"></i>
                                                                                                        <?php endif; ?>
                                                                                                        <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/' . $semesterName) ?>" style="color: inherit; text-decoration: none; cursor: pointer;" title="Input Nilai" onclick="event.stopPropagation();">
                                                                                                            <div>
                                                                                                                <strong><?= esc($santri['NamaSantri']) ?></strong>
                                                                                                            </div>
                                                                                                            <div>
                                                                                                                <span class="text-muted small">ID: <?= esc($santri['IdSantri']) ?></span>
                                                                                                            </div>
                                                                                                            <div class="mt-2">
                                                                                                                <div class="progress" style="height: 18px; position: relative;">
                                                                                                                    <div class="progress-bar <?= $santri['PersentaseSudah'] < 50 ? 'bg-danger' : ($santri['PersentaseSudah'] < 90 ? 'bg-warning' : 'bg-success') ?>"
                                                                                                                        style="width: <?= min(100, $santri['PersentaseSudah']) ?>%;">
                                                                                                                    </div>
                                                                                                                    <span style="position: absolute; left: 50%; transform: translateX(-50%); color: #000; font-size: 11px; font-weight: 500; line-height: 18px; pointer-events: none;">
                                                                                                                        <?= $santri['PersentaseSudah'] ?>%
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td class="text-center">
                                                                                                        <span class="badge badge-<?= esc($santri['StatusColor'] ?? 'secondary') ?>">
                                                                                                            <?= esc($santri['StatusSantri'] ?? 'Belum Dinilai') ?>
                                                                                                        </span>
                                                                                                    </td>
                                                                                                    <td colspan="2" class="text-center">
                                                                                                        <span class="badge badge-info">
                                                                                                            <?= number_format($santri['MateriTerisi']) ?>/<?= number_format($santri['TotalMateri']) ?>
                                                                                                            <?php if ($santri['MateriBelum'] > 0): ?>
                                                                                                                <span class="ml-1"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                                            <?php endif; ?>
                                                                                                        </span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <!-- Group Sudah Dinilai dan Belum Dinilai (Tertutup secara default) -->
                                                                                                <?php if ($hasMateri): ?>
                                                                                                    <!-- Group Sudah Dinilai -->
                                                                                                    <?php if (($santri['MateriTerisi'] ?? 0) > 0): ?>
                                                                                                        <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #e8f5e9;" data-group-type="sudah" data-santri-key="<?= $santriKey ?>">
                                                                                                            <td></td>
                                                                                                            <td style="padding-left: 100px; cursor: pointer;" class="group-expand-cell">
                                                                                                                <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #28a745; margin-right: 8px;"></i>
                                                                                                                <span class="badge badge-success">Sudah Dinilai</span>
                                                                                                                <span class="text-muted small ml-2"><?= number_format($santri['MateriTerisi']) ?> Materi</span>
                                                                                                            </td>
                                                                                                            <td colspan="4"></td>
                                                                                                        </tr>
                                                                                                        <!-- Individual Materi Sudah Dinilai (akan di-load via AJAX) -->
                                                                                                        <tr class="materi-container detail-<?= $santriKey ?>-sudah" style="display: none;">
                                                                                                            <td></td>
                                                                                                            <td colspan="5" style="padding: 0;">
                                                                                                                <div class="materi-loading-<?= $santriKey ?>-sudah" style="display: block; text-align: center; padding: 20px;">
                                                                                                                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                                                </div>
                                                                                                                <div class="materi-content-<?= $santriKey ?>-sudah" style="display: none;"></div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    <?php endif; ?>
                                                                                                    <!-- Group Belum Dinilai -->
                                                                                                    <?php if (($santri['MateriBelum'] ?? 0) > 0): ?>
                                                                                                        <tr class="group-nilai-row detail-<?= $santriKey ?>" style="display: none; background-color: #ffebee;" data-group-type="belum" data-santri-key="<?= $santriKey ?>">
                                                                                                            <td></td>
                                                                                                            <td style="padding-left: 100px; cursor: pointer;" class="group-expand-cell">
                                                                                                                <i class="fas fa-chevron-right expand-icon-group" style="transition: transform 0.3s; color: #dc3545; margin-right: 8px;"></i>
                                                                                                                <span class="badge badge-danger">Belum Dinilai</span>
                                                                                                                <span class="text-muted small ml-2"><?= number_format($santri['MateriBelum']) ?> Materi</span>
                                                                                                            </td>
                                                                                                            <td colspan="4"></td>
                                                                                                        </tr>
                                                                                                        <!-- Individual Materi Belum Dinilai (akan di-load via AJAX) -->
                                                                                                        <tr class="materi-container detail-<?= $santriKey ?>-belum" style="display: none;">
                                                                                                            <td></td>
                                                                                                            <td colspan="5" style="padding: 0;">
                                                                                                                <div class="materi-loading-<?= $santriKey ?>-belum" style="display: block; text-align: center; padding: 20px;">
                                                                                                                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                                                                </div>
                                                                                                                <div class="materi-content-<?= $santriKey ?>-belum" style="display: none;"></div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    <?php endif; ?>
                                                                                                <?php endif; ?>
                                                                                            <?php endforeach; ?>
                                                                                        <?php endif; ?>
                                                                                    <?php endforeach; ?>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php else: ?>
                                                                            <tr>
                                                                                <td colspan="6" class="text-center">Tidak ada data untuk semester <?= $semesterName ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
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

<?= $this->section('styles'); ?>
<style>
    /* Style untuk header kolom kelas vertikal */
    #tblStatistikSantriPerTpqPerKelas th.vertical-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        white-space: nowrap;
        height: 120px;
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        vertical-align: middle;
        text-align: center;
    }

    /* Pastikan sel data juga memiliki lebar yang sesuai dengan header */
    #tblStatistikSantriPerTpqPerKelas tbody td {
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        text-align: center;
    }

    /* Style untuk footer kolom kelas */
    #tblStatistikSantriPerTpqPerKelas tfoot th {
        width: 35px;
        min-width: 35px;
        max-width: 35px;
        padding: 5px 2px;
        text-align: center;
    }

    /* Style untuk kolom Nama TPQ dan Alamat tetap normal */
    #tblStatistikSantriPerTpqPerKelas th:first-child,
    #tblStatistikSantriPerTpqPerKelas th:nth-child(2),
    #tblStatistikSantriPerTpqPerKelas td:first-child,
    #tblStatistikSantriPerTpqPerKelas td:nth-child(2),
    #tblStatistikSantriPerTpqPerKelas tfoot th:first-child,
    #tblStatistikSantriPerTpqPerKelas tfoot th:nth-child(2) {
        width: auto;
        min-width: auto;
        max-width: none;
        writing-mode: horizontal-tb;
        padding: 8px;
    }

    /* Style untuk kolom Total */
    #tblStatistikSantriPerTpqPerKelas th:last-child,
    #tblStatistikSantriPerTpqPerKelas td:last-child,
    #tblStatistikSantriPerTpqPerKelas tfoot th:last-child {
        width: 60px;
        min-width: 60px;
        max-width: 60px;
        writing-mode: horizontal-tb;
        padding: 8px 5px;
    }

    /* Pastikan semua kolom memiliki alignment yang sama */
    #tblStatistikSantriPerTpqPerKelas th,
    #tblStatistikSantriPerTpqPerKelas td,
    #tblStatistikSantriPerTpqPerKelas tfoot th {
        vertical-align: middle;
    }

    /* Pastikan tfoot memiliki width yang sama dengan body */
    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFoot {
        width: 100% !important;
    }

    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFootInner {
        width: 100% !important;
    }

    #tblStatistikSantriPerTpqPerKelas_wrapper .dataTables_scrollFootInner table {
        width: 100% !important;
        margin: 0 !important;
    }

    /* Style untuk tabel progress penilaian */
    .tpq-row {
        transition: background-color 0.2s ease;
        user-select: none;
    }

    .tpq-row:hover {
        background-color: #e9ecef !important;
    }

    .tpq-row.expanded {
        background-color: #e9ecef;
    }

    .detail-row {
        transition: all 0.3s ease;
    }

    .detail-row td {
        border-top: 1px solid #dee2e6;
    }

    .expand-icon {
        color: #007bff;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .tpq-row:hover .expand-icon {
        color: #0056b3;
    }

    /* Styling untuk badge di detail row */
    .detail-row .badge {
        font-weight: 500;
    }

    /* Style untuk kelas-row */
    .kelas-row {
        transition: background-color 0.2s ease;
        user-select: none;
    }

    .kelas-row:hover {
        background-color: #f0f0f0 !important;
    }

    .kelas-row.expanded {
        background-color: #f0f0f0;
    }

    .expand-icon-kelas {
        color: #007bff;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .kelas-row:hover .expand-icon-kelas {
        color: #0056b3;
    }

    /* Style untuk santri-row */
    .santri-row {
        transition: all 0.3s ease;
    }

    .santri-row td {
        border-top: 1px solid #dee2e6;
    }

    /* ==================== INFOGRAFIS STATISTIK GURU ==================== */
    .infografis-guru-wrapper {
        background: #ffffff;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        color: #1f2937;
    }

    .infografis-watermark {
        position: absolute;
        top: 0;
        right: 0;
        width: 320px;
        height: 320px;
        opacity: 0.035;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpolygon points='50 0, 61 35, 98 35, 68 57, 79 91, 50 70, 21 91, 32 57, 2 35, 39 35' fill='%231e5138'/%3E%3Cpolygon points='50 10, 59 38, 88 38, 64 56, 73 83, 50 66, 27 83, 36 56, 12 38, 41 38' fill='%23c59b27'/%3E%3C/svg%3E");
        background-repeat: repeat;
        background-size: 70px 70px;
    }

    .infografis-header {
        text-align: center;
        padding: 20px 20px 15px 20px;
        position: relative;
    }
    .infografis-accent-dots {
        position: absolute;
        top: 20px;
        left: 20px;
        display: grid;
        grid-template-columns: repeat(2, 6px);
        gap: 6px;
    }
    .infografis-accent-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #c59b27;
    }
    .infografis-accent-dots span:nth-child(2),
    .infografis-accent-dots span:nth-child(3) {
        background-color: #1e5138;
    }

    .infografis-lantern {
        position: absolute;
        top: 15px;
        right: 25px;
    }
    .infografis-lantern-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f0fdf4;
        border: 1.5px solid #1e5138;
        color: #1e5138;
        font-size: 1.25rem;
        box-shadow: 0 4px 10px rgba(30, 81, 56, 0.12);
    }

    .infografis-eyebrow {
        font-size: 1.45rem;
        font-weight: 800;
        color: #1e5138;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .infografis-main-title {
        font-size: 2.15rem;
        font-weight: 900;
        color: #14422b;
        letter-spacing: 0.02em;
        margin-bottom: 6px;
    }
    .infografis-total-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #374151;
    }
    .infografis-total-badge {
        background: #1e5138;
        color: #ffffff;
        font-weight: 800;
        padding: 3px 14px;
        border-radius: 8px;
        letter-spacing: 0.03em;
        font-size: 1.05rem;
    }

    .infografis-section-head {
        display: flex;
        align-items: center;
        margin: 22px 0 14px 0;
        gap: 12px;
    }
    .infografis-section-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #c59b27;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .infografis-section-title {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #111827;
        text-transform: uppercase;
        white-space: nowrap;
        margin: 0;
    }
    .infografis-section-divider {
        flex-grow: 1;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .infografis-section-divider .line {
        flex-grow: 1;
        height: 2px;
        background: #d4af37;
    }
    .infografis-section-divider .dots {
        display: flex;
        gap: 4px;
    }
    .infografis-section-divider .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #d4af37;
    }

    .infografis-stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .infografis-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.06);
    }
    .infografis-stat-label {
        font-size: 0.95rem;
        color: #4b5563;
        font-weight: 500;
        margin-bottom: 3px;
    }
    .infografis-stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: #111827;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }

    /* Status Aktif */
    .status-aktif-stage-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        padding-top: 10px;
    }
    .status-aktif-stage {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        gap: 36px;
        height: 230px;
        width: 100%;
        max-width: 320px;
    }
    .status-col-aktif {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        width: 95px;
    }
    .status-top-pct {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .status-pillar-gold {
        width: 95px;
        height: 185px;
        background: #c59b27;
        border-radius: 2px 2px 0 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0 10px 0;
        box-shadow: 0 4px 12px rgba(197, 155, 39, 0.22);
        transition: height 0.6s ease;
    }
    .status-silhouette-top {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .status-silhouette-bottom {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .status-col-nonaktif {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        width: 85px;
    }
    .status-silhouette-grey {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }
    .status-pedestal-grey {
        width: 85px;
        height: 12px;
        background: #88929e;
        border-radius: 2px 2px 0 0;
    }
    .status-stage-baseline {
        width: 100%;
        max-width: 320px;
        height: 2px;
        background: #94a3b8;
        margin-bottom: 10px;
    }
    .status-footer-row {
        display: flex;
        justify-content: center;
        gap: 36px;
        width: 100%;
        max-width: 320px;
        text-align: center;
    }
    .status-label-col {
        width: 110px;
    }
    .status-label-sub {
        font-size: 0.88rem;
        font-weight: 700;
        color: #334155;
        line-height: 1.2;
    }
    .status-label-count {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.2;
        margin-top: 2px;
    }

    /* Sebaran Wilayah */
    .wilayah-table-wrapper {
        width: 100%;
    }
    .wilayah-head-row {
        display: flex;
        align-items: center;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        padding-bottom: 8px;
        margin-bottom: 6px;
        border-bottom: 2px solid #e2e8f0;
    }
    .wilayah-th-name {
        width: 28%;
    }
    .wilayah-th-total {
        width: 72%;
        text-align: right;
        padding-right: 15px;
    }
    .wilayah-item-row {
        display: flex;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .wilayah-item-row:last-child {
        border-bottom: none;
    }
    .wilayah-item-name {
        width: 28%;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }
    .wilayah-item-count {
        width: 22%;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        text-align: right;
        padding-right: 20px;
    }
    .wilayah-item-graphic {
        width: 50%;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .wilayah-map-icon {
        flex-shrink: 0;
    }
    .wilayah-track {
        flex-grow: 1;
        height: 26px;
        background: #d8dee4;
        border-radius: 9999px;
        overflow: hidden;
        position: relative;
    }
    .wilayah-bar {
        height: 100%;
        background: #184e38;
        border-radius: 9999px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .infografis-footer {
        background: #14422b;
        border-top: 3px solid #c59b27;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #ffffff;
        border-radius: 0 0 14px 14px;
        margin-top: 22px;
    }
    .infografis-footer-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #ffffff;
    }
    .infografis-footer-period {
        font-size: 0.95rem;
        font-weight: 500;
        color: #d1fae5;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #infografis-card-print, #infografis-card-print * {
            visibility: visible;
        }
        #infografis-card-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
        .no-print {
            display: none !important;
        }
    }

    /* Styling Tabel Matriks Detail */
    #tblStatistikSantriPerTpqPerKelas th,
    #tblStatistikSantriPerTpqPerKelas td {
        vertical-align: middle !important;
        white-space: nowrap !important;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable for List TPQ dengan Rasio
        if ($('#tblTpqDenganRasio').length > 0) {
            initializeDataTableUmum('#tblTpqDenganRasio', true, true, [], {
                "pageLength": 25,
                "order": [
                    [3, "asc"]
                ], // Sort by rasio
                "columnDefs": [{
                    "targets": [2, 3, 4], // Jumlah Guru, Jumlah Santri, Rasio
                    "className": "text-center"
                }]
            });
        }

        // Initialize DataTable for Statistik Santri Per TPQ Per Kelas
        if ($('#tblStatistikSantriPerTpqPerKelas').length > 0) {
            if ($.fn.DataTable.isDataTable('#tblStatistikSantriPerTpqPerKelas')) {
                $('#tblStatistikSantriPerTpqPerKelas').DataTable().destroy();
            }

            var tableSantriPerKelas = $('#tblStatistikSantriPerTpqPerKelas').DataTable({
                "responsive": false,
                "scrollX": false, // Nonaktifkan scrollX DataTables agar thead, tbody, tfoot tetap SATU elemen tabel utuh & tidak lari-lari
                "autoWidth": false,
                "paging": true,
                "pageLength": 25,
                "lengthChange": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                "searching": true,
                "ordering": true,
                "info": true,
                "order": [],
                "language": {
                    "search": "Pencarian:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "paginate": {
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "columnDefs": [
                    {
                        "targets": "_all",
                        "className": "text-center align-middle"
                    },
                    {
                        "targets": [0, 1], // Nama TPQ dan Kelurahan/Desa
                        "orderable": true,
                        "className": "text-left align-middle"
                    }
                ]
            });
        }


        // Key untuk localStorage
        const storageKeyCard = 'dashboardAdmin_cardExpand';
        const storageKeyTpq = 'dashboardAdmin_tpqExpand';
        const storageKeyKelas = 'dashboardAdmin_kelasExpand';
        const storageKeySantri = 'dashboardAdmin_santriExpand';
        const storageKeyGroup = 'dashboardAdmin_groupExpand';

        // Fungsi untuk menyimpan status expand card ke localStorage
        function saveCardExpandState(cardId, isExpanded) {
            var states = JSON.parse(localStorage.getItem(storageKeyCard) || '{}');
            states[cardId] = isExpanded;
            localStorage.setItem(storageKeyCard, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand card dari localStorage
        function loadCardExpandState(cardId) {
            var states = JSON.parse(localStorage.getItem(storageKeyCard) || '{}');
            return states[cardId] !== undefined ? states[cardId] : null;
        }

        // Fungsi untuk menyimpan status expand TPQ ke localStorage
        function saveTpqExpandState(tpqKey, semester, isExpanded) {
            var key = semester + '_' + tpqKey;
            var states = JSON.parse(localStorage.getItem(storageKeyTpq) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeyTpq, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand TPQ dari localStorage
        function loadTpqExpandState(tpqKey, semester) {
            var key = semester + '_' + tpqKey;
            var states = JSON.parse(localStorage.getItem(storageKeyTpq) || '{}');
            return states[key] === true;
        }

        // Fungsi untuk menyimpan status expand kelas ke localStorage
        function saveKelasExpandState(kelasKey, semester, isExpanded) {
            var key = semester + '_' + kelasKey;
            var states = JSON.parse(localStorage.getItem(storageKeyKelas) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeyKelas, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand kelas dari localStorage
        function loadKelasExpandState(kelasKey, semester) {
            var key = semester + '_' + kelasKey;
            var states = JSON.parse(localStorage.getItem(storageKeyKelas) || '{}');
            return states[key] === true;
        }

        // Fungsi untuk menyimpan status expand santri ke localStorage
        function saveSantriExpandState(santriKey, semester, isExpanded) {
            var key = semester + '_' + santriKey;
            var states = JSON.parse(localStorage.getItem(storageKeySantri) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeySantri, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand santri dari localStorage
        function loadSantriExpandState(santriKey, semester) {
            var key = semester + '_' + santriKey;
            var states = JSON.parse(localStorage.getItem(storageKeySantri) || '{}');
            return states[key] === true;
        }

        // Fungsi untuk menyimpan status expand group ke localStorage
        function saveGroupExpandState(santriKey, groupType, semester, isExpanded) {
            var key = semester + '_' + santriKey + '_' + groupType;
            var states = JSON.parse(localStorage.getItem(storageKeyGroup) || '{}');
            states[key] = isExpanded;
            localStorage.setItem(storageKeyGroup, JSON.stringify(states));
        }

        // Fungsi untuk memuat status expand group dari localStorage
        function loadGroupExpandState(santriKey, groupType, semester) {
            var key = semester + '_' + santriKey + '_' + groupType;
            var states = JSON.parse(localStorage.getItem(storageKeyGroup) || '{}');
            return states[key] === true;
        }

        // Fungsi helper untuk mendapatkan card identifier
        function getCardIdentifier($card) {
            var cardId = $card.attr('id');
            if (cardId) {
                return cardId;
            }
            var headerText = $card.find('.card-header h3, .card-header h5').text().trim();
            if (headerText) {
                return headerText.replace(/[^a-zA-Z0-9]/g, '_');
            }
            return 'card_' + $card.index();
        }

        // Fungsi helper untuk menentukan semester berdasarkan tabel
        function getSemesterFromTable($table) {
            var tableId = $table.attr('id') || '';
            if (tableId.includes('Ganjil')) {
                return 'Ganjil';
            } else if (tableId.includes('Genap')) {
                return 'Genap';
            }
            var cardHeaderText = $table.closest('.card').find('.card-header').text();
            if (cardHeaderText.includes('Ganjil')) {
                return 'Ganjil';
            } else if (cardHeaderText.includes('Genap')) {
                return 'Genap';
            }
            return '';
        }

        // Handle expand/collapse untuk card semester (Ganjil/Genap)
        $(document).on('expanded.lte.cardwidget', function(event) {
            var $card = $(event.target).closest('.card');
            var cardId = getCardIdentifier($card);
            saveCardExpandState(cardId, true);
        });

        $(document).on('collapsed.lte.cardwidget', function(event) {
            var $card = $(event.target).closest('.card');
            var cardId = getCardIdentifier($card);
            saveCardExpandState(cardId, false);
        });

        // Load status expand card saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.card').each(function() {
                    var $card = $(this);
                    if ($card.find('[data-card-widget="collapse"]').length > 0) {
                        var cardId = getCardIdentifier($card);
                        var savedState = loadCardExpandState(cardId);
                        if (savedState !== null) {
                            if (savedState && $card.hasClass('collapsed-card')) {
                                $card.removeClass('collapsed-card');
                                $card.find('[data-card-widget="collapse"] i').removeClass('fa-plus').addClass('fa-minus');
                                $card.find('.card-body, .card-footer').slideDown();
                            } else if (!savedState && !$card.hasClass('collapsed-card')) {
                                $card.addClass('collapsed-card');
                                $card.find('[data-card-widget="collapse"] i').removeClass('fa-minus').addClass('fa-plus');
                                $card.find('.card-body, .card-footer').slideUp();
                            }
                        }
                    }
                });
            }, 100);
        });

        // Handle expand/collapse untuk tabel progress penilaian
        $(document).on('click', '.tpq-row', function() {
            var tpqKey = $(this).data('tpq-key');
            var $table = $(this).closest('table');
            var detailRows = $table.find('.detail-' + tpqKey);
            var expandIcon = $(this).find('.expand-icon');
            var semester = getSemesterFromTable($table);

            if (detailRows.length > 0) {
                if (detailRows.is(':visible')) {
                    // Collapse - tutup semua level kelas, santri, group nilai, dan materi
                    var $tpqRow = $(this);

                    detailRows.each(function() {
                        var $kelasRow = $(this);
                        var kelasKey = $kelasRow.data('kelas-key');

                        // Jika ini adalah kelas-row yang expanded, collapse juga semua child-nya
                        if ($kelasRow.hasClass('kelas-row') && kelasKey) {
                            var santriRows = $table.find('.detail-' + kelasKey);
                            if (santriRows.length > 0) {
                                santriRows.each(function() {
                                    var $santriRow = $(this);
                                    if ($santriRow.hasClass('santri-row')) {
                                        var santriKey = $santriRow.data('santri-key');
                                        if (santriKey) {
                                            // Tutup materi container
                                            var $materiContainers = $table.find('.materi-container.detail-' + santriKey + '-sudah, .materi-container.detail-' + santriKey + '-belum');
                                            $materiContainers.slideUp(200);

                                            // Tutup group nilai
                                            var $groupRows = $table.find('.group-nilai-row.detail-' + santriKey);
                                            $groupRows.slideUp(200);
                                            $groupRows.removeClass('expanded');
                                            $groupRows.find('.expand-icon-group').css('transform', 'rotate(0deg)');

                                            // Reset icon expand santri
                                            $santriRow.find('.expand-icon-santri').css('transform', 'rotate(0deg)');
                                            $santriRow.removeClass('expanded');

                                            // Update localStorage untuk santri dan group
                                            var santriSemester = $santriRow.data('semester') || semester;
                                            saveSantriExpandState(santriKey, santriSemester, false);
                                            saveGroupExpandState(santriKey, 'sudah', santriSemester, false);
                                            saveGroupExpandState(santriKey, 'belum', santriSemester, false);
                                        }
                                    }
                                });

                                // Tutup santri rows
                                santriRows.slideUp(200);
                                var kelasExpandIcon = $kelasRow.find('.expand-icon-kelas');
                                kelasExpandIcon.css('transform', 'rotate(0deg)');
                                $kelasRow.removeClass('expanded');
                                if (semester) {
                                    saveKelasExpandState(kelasKey, semester, false);
                                }
                            }
                        }
                    });

                    // Collapse semua detail rows (kelas) setelah semua child tertutup
                    setTimeout(function() {
                        detailRows.slideUp(300);
                        expandIcon.css('transform', 'rotate(0deg)');
                        $tpqRow.removeClass('expanded');
                        if (semester) {
                            saveTpqExpandState(tpqKey, semester, false);
                        }
                    }, 250);
                } else {
                    // Expand
                    detailRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                    if (semester) {
                        saveTpqExpandState(tpqKey, semester, true);
                    }
                }
            }
        });

        // Load status expand TPQ saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.tpq-row').each(function() {
                    var tpqKey = $(this).data('tpq-key');
                    var $table = $(this).closest('table');
                    var detailRows = $table.find('.detail-' + tpqKey);
                    var expandIcon = $(this).find('.expand-icon');
                    var semester = getSemesterFromTable($table);

                    if (semester && detailRows.length > 0) {
                        var isExpanded = loadTpqExpandState(tpqKey, semester);
                        if (isExpanded) {
                            detailRows.show();
                            expandIcon.css('transform', 'rotate(90deg)');
                            $(this).addClass('expanded');
                        }
                    }
                });
            }, 200);
        });

        // Hover effect untuk tpq-row
        $(document).on('mouseenter', '.tpq-row', function() {
            if ($('.detail-' + $(this).data('tpq-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f0f0f0');
            }
        }).on('mouseleave', '.tpq-row', function() {
            if ($('.detail-' + $(this).data('tpq-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f8f9fa');
            }
        });

        // Handle expand/collapse untuk kelas-row ke santri
        $(document).on('click', '.kelas-row', function(e) {
            e.stopPropagation(); // Prevent triggering tpq-row click
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table'); // Ambil tabel parent (Ganjil atau Genap)
            var santriRows = $table.find('.detail-' + kelasKey); // Cari hanya dalam tabel yang sama
            var expandIcon = $(this).find('.expand-icon-kelas');
            var semester = getSemesterFromTable($table);

            if (santriRows.length > 0) {
                if (santriRows.is(':visible')) {
                    // Collapse - tutup semua child rows (santri, group nilai, dan materi)
                    var $kelasRow = $(this);
                    santriRows.each(function() {
                        var $santriRow = $(this);
                        if ($santriRow.hasClass('santri-row')) {
                            var santriKey = $santriRow.data('santri-key');
                            if (santriKey) {
                                var $santriTable = $santriRow.closest('table');
                                var $materiContainers = $santriTable.find('.materi-container.detail-' + santriKey + '-sudah, .materi-container.detail-' + santriKey + '-belum');
                                $materiContainers.slideUp(200);
                                var $groupRows = $santriTable.find('.group-nilai-row.detail-' + santriKey);
                                $groupRows.slideUp(200);
                                $groupRows.removeClass('expanded');
                                $groupRows.find('.expand-icon-group').css('transform', 'rotate(0deg)');
                                $santriRow.find('.expand-icon-santri').css('transform', 'rotate(0deg)');
                                $santriRow.removeClass('expanded');
                                var santriSemester = $santriRow.data('semester') || semester;
                                saveSantriExpandState(santriKey, santriSemester, false);
                                saveGroupExpandState(santriKey, 'sudah', santriSemester, false);
                                saveGroupExpandState(santriKey, 'belum', santriSemester, false);
                            }
                        }
                    });
                    setTimeout(function() {
                        santriRows.slideUp(300);
                        expandIcon.css('transform', 'rotate(0deg)');
                        $kelasRow.removeClass('expanded');
                        if (semester) {
                            saveKelasExpandState(kelasKey, semester, false);
                        }
                    }, 250);
                } else {
                    // Expand
                    santriRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                    if (semester) {
                        saveKelasExpandState(kelasKey, semester, true);
                    }
                }
            }
        });

        // Load status expand kelas saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.kelas-row').each(function() {
                    var kelasKey = $(this).data('kelas-key');
                    var $table = $(this).closest('table');
                    var santriRows = $table.find('.detail-' + kelasKey);
                    var expandIcon = $(this).find('.expand-icon-kelas');
                    var semester = getSemesterFromTable($table);

                    if (semester && santriRows.length > 0) {
                        var isExpanded = loadKelasExpandState(kelasKey, semester);
                        if (isExpanded) {
                            santriRows.show();
                            expandIcon.css('transform', 'rotate(90deg)');
                            $(this).addClass('expanded');
                        }
                    }
                });
            }, 300);
        });

        // Hover effect untuk kelas-row
        $(document).on('mouseenter', '.kelas-row', function() {
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table');
            if ($table.find('.detail-' + kelasKey).is(':visible')) {
                $(this).css('background-color', '#f0f0f0');
            } else {
                $(this).css('background-color', '#f8f9fa');
            }
        }).on('mouseleave', '.kelas-row', function() {
            var kelasKey = $(this).data('kelas-key');
            var $table = $(this).closest('table');
            if ($table.find('.detail-' + kelasKey).is(':visible')) {
                $(this).css('background-color', '#f0f0f0');
            } else {
                $(this).css('background-color', '#ffffff');
            }
        });

        // Handle expand/collapse untuk santri
        $(document).on('click', '.santri-expand-cell', function(e) {
            e.stopPropagation();
            var $santriRow = $(this).closest('.santri-row');
            var santriKey = $santriRow.data('santri-key');
            var semester = $santriRow.data('semester');
            var $table = $santriRow.closest('table');
            var $groupRows = $table.find('.group-nilai-row.detail-' + santriKey);
            var expandIcon = $(this).find('.expand-icon-santri');

            if ($groupRows.length > 0) {
                if ($groupRows.is(':visible')) {
                    $groupRows.slideUp(300);
                    $table.find('.materi-container.detail-' + santriKey + '-sudah, .materi-container.detail-' + santriKey + '-belum').slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $santriRow.removeClass('expanded');
                    saveSantriExpandState(santriKey, semester, false);
                } else {
                    $groupRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $santriRow.addClass('expanded');
                    saveSantriExpandState(santriKey, semester, true);
                }
            }
        });

        // Handle expand/collapse untuk group nilai
        $(document).on('click', '.group-expand-cell', function(e) {
            e.stopPropagation();
            var $groupRow = $(this).closest('.group-nilai-row');
            var groupType = $groupRow.data('group-type');
            var santriKey = $groupRow.data('santri-key');
            var $table = $groupRow.closest('table');
            var $santriRow = $table.find('.santri-row[data-santri-key="' + santriKey + '"]');

            // Pastikan santri row ditemukan
            if ($santriRow.length === 0) {
                console.error('[DASHBOARD ADMIN] Santri row tidak ditemukan untuk santriKey:', santriKey);
                return;
            }

            var semester = $santriRow.data('semester');

            // Jika semester tidak ditemukan dari data attribute, coba dari tabel
            if (!semester) {
                semester = getSemesterFromTable($table);
            }

            var $materiContainer = $table.find('.materi-container.detail-' + santriKey + '-' + groupType);
            var expandIcon = $(this).find('.expand-icon-group');

            if ($materiContainer.length > 0) {
                if ($materiContainer.is(':visible')) {
                    $materiContainer.slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $groupRow.removeClass('expanded');
                    saveGroupExpandState(santriKey, groupType, semester, false);
                } else {
                    // Expand materi dan load data jika belum di-load
                    var $loadingDiv = $table.find('.materi-loading-' + santriKey + '-' + groupType);
                    var $contentDiv = $table.find('.materi-content-' + santriKey + '-' + groupType);

                    // Cek apakah data sudah di-load
                    var isDataLoaded = !$contentDiv.is(':empty') && $contentDiv.data('loaded') === true;

                    if (isDataLoaded) {
                        // Jika data sudah di-load, langsung tampilkan content dan hide loading
                        $loadingDiv.hide();
                        $contentDiv.show();
                    } else {
                        // Jika data belum di-load, tampilkan loading dan load data
                        $loadingDiv.css('display', 'block');
                        $contentDiv.hide();
                    }

                    expandIcon.css('transform', 'rotate(90deg)');
                    $groupRow.addClass('expanded');
                    saveGroupExpandState(santriKey, groupType, semester, true);

                    // Expand container
                    $materiContainer.slideDown(300);

                    if (!isDataLoaded) {
                        var idSantri = $santriRow.data('santri-id');
                        var idKelas = $santriRow.data('kelas-id');

                        // Debug logging
                        console.log('[DASHBOARD ADMIN] Loading materi:', {
                            idSantri: idSantri,
                            idKelas: idKelas,
                            semester: semester,
                            groupType: groupType,
                            santriKey: santriKey
                        });

                        if (!idSantri || !idKelas || !semester) {
                            $loadingDiv.hide();
                            $contentDiv.html('<div class="alert alert-danger" style="margin: 10px 80px;">Error: Parameter tidak lengkap (IdSantri: ' + idSantri + ', IdKelas: ' + idKelas + ', Semester: ' + semester + ')</div>').show();
                            return;
                        }

                        $.ajax({
                            url: '<?= base_url('backend/dashboard/getMateriPerSantri') ?>',
                            type: 'GET',
                            data: {
                                IdSantri: idSantri,
                                IdKelas: idKelas,
                                Semester: semester
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log('[DASHBOARD ADMIN] AJAX Success:', response);
                                $loadingDiv.hide();
                                if (response && response.success) {
                                    var materiList = groupType === 'sudah' ? response.data.sudahDinilai : response.data.belumDinilai;
                                    console.log('[DASHBOARD ADMIN] Materi List (' + groupType + '):', materiList);
                                    var htmlContent = '';

                                    if (materiList && materiList.length > 0) {
                                        materiList.forEach(function(materi) {
                                            var badgeClass = materi.Nilai > 0 ? 'badge-success' : 'badge-danger';
                                            var nilaiText = materi.Nilai > 0 ? materi.Nilai : '0';
                                            var badgeHtml = '<span class="badge ' + badgeClass + '">' + nilaiText + '</span>';
                                            htmlContent += '<div style="padding: 8px 0; padding-left: 41px; border-bottom: 1px solid #dee2e6;">Nilai ' + badgeHtml + ' - ' + materi.NamaMateri + '</div>';
                                        });
                                    } else {
                                        htmlContent = '<div style="padding: 8px 0; padding-left: 41px; text-align: center; color: #6c757d;">Tidak ada data</div>';
                                    }

                                    $contentDiv.html(htmlContent).show().data('loaded', true);
                                } else {
                                    var errorMsg = response && response.message ? response.message : 'Gagal memuat data materi';
                                    $contentDiv.html('<div class="alert alert-warning" style="margin: 10px 80px;">' + errorMsg + '</div>').show();
                                    console.error('[DASHBOARD ADMIN] Response tidak sukses:', response);
                                }
                            },
                            error: function(xhr, status, error) {
                                $loadingDiv.hide();
                                var errorMsg = 'Error: ' + error;
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    try {
                                        var errorResponse = JSON.parse(xhr.responseText);
                                        if (errorResponse.message) {
                                            errorMsg = errorResponse.message;
                                        }
                                    } catch (e) {
                                        errorMsg = 'Error: ' + xhr.status + ' ' + xhr.statusText;
                                    }
                                }
                                $contentDiv.html('<div class="alert alert-danger" style="margin: 10px 80px;">' + errorMsg + '</div>').show();
                                console.error('[DASHBOARD ADMIN] AJAX Error loading materi:', {
                                    error: error,
                                    status: status,
                                    xhr: xhr,
                                    responseText: xhr.responseText,
                                    statusCode: xhr.status
                                });
                            }
                        });
                    }
                }
            }
        });

        // Load status expand santri dan group saat page load
        $(document).ready(function() {
            setTimeout(function() {
                $('.santri-row').each(function() {
                    var $santriRow = $(this);
                    var santriKey = $santriRow.data('santri-key');
                    var semester = $santriRow.data('semester');
                    var $table = $santriRow.closest('table');

                    if (santriKey && semester) {
                        var isExpanded = loadSantriExpandState(santriKey, semester);
                        if (isExpanded) {
                            var $groupRows = $table.find('.group-nilai-row.detail-' + santriKey);
                            var expandIcon = $santriRow.find('.expand-icon-santri');

                            if ($groupRows.length > 0) {
                                $groupRows.show();
                                expandIcon.css('transform', 'rotate(90deg)');
                                $santriRow.addClass('expanded');

                                $groupRows.each(function() {
                                    var $groupRow = $(this);
                                    var groupType = $groupRow.data('group-type');
                                    var $materiContainer = $table.find('.materi-container.detail-' + santriKey + '-' + groupType);

                                    $materiContainer.hide();
                                    $groupRow.removeClass('expanded');
                                    $groupRow.find('.expand-icon-group').css('transform', 'rotate(0deg)');
                                    $table.find('.materi-content-' + santriKey + '-' + groupType).data('loaded', false);
                                });
                            }
                        }
                    }
                });
            }, 300);
        });

        // ==================== LOGIKA INFOGRAFIS STATISTIK GURU ====================
        var chartUsiaGuruInstance = null;
        var chartJenisKelaminInstance = null;
        var chartGuruPerTpqInstance = null;
        var initialGuruStatData = <?= json_encode($RingkasanStatistikGuru ?? []) ?>;

        function drawPillBadge(ctx, x, y, text) {
            if (typeof x !== 'number' || typeof y !== 'number' || isNaN(x) || isNaN(y)) return;
            ctx.save();
            ctx.font = 'bold 11px sans-serif';
            var textWidth = ctx.measureText(text).width;
            var pillW = Math.max(22, textWidth + 10);
            var pillH = 18;
            var pillX = x - pillW / 2;
            var pillY = y - pillH - 4;
            var r = 4;

            ctx.fillStyle = '#1e5138';
            ctx.beginPath();
            ctx.moveTo(pillX + r, pillY);
            ctx.lineTo(pillX + pillW - r, pillY);
            ctx.quadraticCurveTo(pillX + pillW, pillY, pillX + pillW, pillY + r);
            ctx.lineTo(pillX + pillW, pillY + pillH - r);
            ctx.quadraticCurveTo(pillX + pillW, pillY + pillH, pillX + pillW - r, pillY + pillH);
            ctx.lineTo(pillX + r, pillY + pillH);
            ctx.quadraticCurveTo(pillX, pillY + pillH, pillX, pillY + pillH - r);
            ctx.lineTo(pillX, pillY + r);
            ctx.quadraticCurveTo(pillX, pillY, pillX + r, pillY);
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(text, x, pillY + pillH / 2);
            ctx.restore();
        }

        function renderGuruCharts(data) {
            if (!data) return;
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js belum dimuat');
                return;
            }

            var isChartV3OrV4 = typeof Chart.version !== 'undefined' && parseInt(Chart.version) >= 3;

            // 1. Chart Demografi Usia Guru (Bar Chart)
            var ctxUsia = document.getElementById('chartUsiaGuru');
            if (ctxUsia) {
                if (chartUsiaGuruInstance) {
                    chartUsiaGuruInstance.destroy();
                    chartUsiaGuruInstance = null;
                }

                var demografi = data.demografi_usia || {};
                var labels = demografi.labels || [];
                var counts = demografi.counts || [];

                var pillBadgePlugin = {
                    id: 'pillBadgesGuru',
                    afterDatasetsDraw: function(chart) {
                        try {
                            var ctx = chart.ctx;
                            var datasets = chart.data && chart.data.datasets;
                            if (!datasets) return;
                            datasets.forEach(function(dataset, datasetIndex) {
                                var meta = chart.getDatasetMeta(datasetIndex);
                                if (meta && !meta.hidden && meta.data) {
                                    meta.data.forEach(function(bar, index) {
                                        var val = dataset.data[index];
                                        if (val > 0) {
                                            var x = (typeof bar.x !== 'undefined') ? bar.x : (bar._model ? bar._model.x : null);
                                            var y = (typeof bar.y !== 'undefined') ? bar.y : (bar._model ? bar._model.y : null);
                                            if (x !== null && y !== null && !isNaN(x) && !isNaN(y)) {
                                                drawPillBadge(ctx, x, y, String(val));
                                            }
                                        }
                                    });
                                }
                            });
                        } catch (e) {
                            console.error('Error drawing pill badges:', e);
                        }
                    }
                };

                var scalesConfig = isChartV3OrV4 ? {
                    x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#374151' } },
                    y: { display: false, beginAtZero: true, grace: '18%' }
                } : {
                    xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#374151', fontSize: 11, fontStyle: 'bold' } }],
                    yAxes: [{ display: false, ticks: { beginAtZero: true, suggestedMax: Math.max.apply(null, counts.concat([10])) * 1.25 } }]
                };

                chartUsiaGuruInstance = new Chart(ctxUsia, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Guru',
                            data: counts,
                            backgroundColor: '#2d7a53',
                            hoverBackgroundColor: '#1e5138',
                            borderRadius: 4,
                            barPercentage: 0.65,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 25, bottom: 5 }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var val = context.parsed ? (context.parsed.y || context.parsed) : (context.yLabel || 0);
                                        return val + ' Guru';
                                    }
                                }
                            }
                        },
                        legend: { display: false },
                        scales: scalesConfig
                    },
                    plugins: [pillBadgePlugin]
                });
            }

            // 2. Chart Distribusi Jenis Kelamin (Donut Chart)
            var ctxGender = document.getElementById('chartJenisKelaminGuru');
            if (ctxGender) {
                if (chartJenisKelaminInstance) {
                    chartJenisKelaminInstance.destroy();
                    chartJenisKelaminInstance = null;
                }

                var jk = data.jenis_kelamin || {};
                var countPerempuan = jk.perempuan || 0;
                var countLaki = jk.laki_laki || 0;

                chartJenisKelaminInstance = new Chart(ctxGender, {
                    type: 'doughnut',
                    data: {
                        labels: ['Perempuan', 'Laki-Laki'],
                        datasets: [{
                            data: [countPerempuan, countLaki],
                            backgroundColor: ['#164e3b', '#4ade80'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        cutoutPercentage: 62,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var val = context.parsed || (context.dataset ? context.dataset.data[context.dataIndex] : 0);
                                        var total = countPerempuan + countLaki;
                                        var pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                        var lbl = context.label || '';
                                        return lbl + ': ' + val + ' Guru (' + pct + '%)';
                                    }
                                }
                            }
                        },
                        legend: { display: false }
                    }
                });
            }

            // 3. Chart Guru Per TPQ (Horizontal Stacked Bar Chart)
            var ctxTpq = document.getElementById('chartGuruPerTpq');
            if (ctxTpq) {
                if (chartGuruPerTpqInstance) {
                    chartGuruPerTpqInstance.destroy();
                    chartGuruPerTpqInstance = null;
                }

                var gTpq = data.guru_per_tpq || {};
                var tpqLabels = gTpq.labels || [];
                var tpqTotals = gTpq.totals || [];
                var tpqLaki = gTpq.laki_laki || [];
                var tpqPerempuan = gTpq.perempuan || [];

                // Atur tinggi container secara dinamis agar bar tidak terhimpit
                var barHeight = Math.max(260, tpqLabels.length * 28 + 60);
                $('#wrapperChartGuruPerTpq').css('height', barHeight + 'px');

                var tpqTotalsPlugin = {
                    id: 'tpqTotalsPlugin',
                    afterDatasetsDraw: function(chart) {
                        try {
                            var ctx = chart.ctx;
                            var datasets = chart.data && chart.data.datasets;
                            if (!datasets || datasets.length === 0) return;

                            var metaLaki = chart.getDatasetMeta(1);
                            var metaP = chart.getDatasetMeta(0);

                            tpqLabels.forEach(function(lbl, index) {
                                var total = tpqTotals[index] || 0;
                                if (total > 0) {
                                    var lastBar = (metaLaki && metaLaki.data && metaLaki.data[index]) ? metaLaki.data[index] : (metaP && metaP.data ? metaP.data[index] : null);
                                    if (lastBar) {
                                        var x = (typeof lastBar.x !== 'undefined') ? lastBar.x : (lastBar._model ? lastBar._model.x : null);
                                        var y = (typeof lastBar.y !== 'undefined') ? lastBar.y : (lastBar._model ? lastBar._model.y : null);
                                        if (x !== null && y !== null && !isNaN(x) && !isNaN(y)) {
                                            ctx.save();
                                            ctx.fillStyle = '#0f172a';
                                            ctx.font = 'bold 11px sans-serif';
                                            ctx.textAlign = 'left';
                                            ctx.textBaseline = 'middle';
                                            ctx.fillText(total + ' Guru', x + 8, y);
                                            ctx.restore();
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error drawing TPQ totals:', err);
                        }
                    }
                };

                var maxVal = tpqTotals.length > 0 ? Math.max.apply(null, tpqTotals) : 10;

                var scalesTpqConfig = isChartV3OrV4 ? {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        max: Math.round(maxVal * 1.25) + 1,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11, weight: '600' }, color: '#64748b', stepSize: 2 }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '700' }, color: '#1e293b' }
                    }
                } : {
                    xAxes: [{
                        stacked: true,
                        ticks: { beginAtZero: true, suggestedMax: Math.round(maxVal * 1.25) + 1, fontColor: '#64748b', fontSize: 11 },
                        gridLines: { color: '#f1f5f9' }
                    }],
                    yAxes: [{
                        stacked: true,
                        gridLines: { display: false },
                        ticks: { fontColor: '#1e293b', fontSize: 11, fontStyle: 'bold' }
                    }]
                };

                var chartType = isChartV3OrV4 ? 'bar' : 'horizontalBar';
                var chartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    layout: {
                        padding: { right: 55, top: 10, bottom: 10 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var dsLabel = context.dataset ? (context.dataset.label || '') : '';
                                    var val = context.parsed ? (context.parsed.x || context.parsed) : (context.xLabel || 0);
                                    return dsLabel + ': ' + val + ' Guru';
                                },
                                afterBody: function(tooltipItems) {
                                    if (!tooltipItems || tooltipItems.length === 0) return '';
                                    var idx = tooltipItems[0].dataIndex;
                                    var tot = tpqTotals[idx] || 0;
                                    return 'Total: ' + tot + ' Guru';
                                }
                            }
                        }
                    },
                    legend: { display: false },
                    scales: scalesTpqConfig
                };

                chartGuruPerTpqInstance = new Chart(ctxTpq, {
                    type: chartType,
                    data: {
                        labels: tpqLabels,
                        datasets: [
                            {
                                label: 'Perempuan',
                                data: tpqPerempuan,
                                backgroundColor: '#1e5138',
                                hoverBackgroundColor: '#14422b',
                                borderRadius: 4,
                                barPercentage: 0.72,
                                categoryPercentage: 0.85
                            },
                            {
                                label: 'Laki-Laki',
                                data: tpqLaki,
                                backgroundColor: '#c59b27',
                                hoverBackgroundColor: '#a88219',
                                borderRadius: 4,
                                barPercentage: 0.72,
                                categoryPercentage: 0.85
                            }
                        ]
                    },
                    options: chartOptions,
                    plugins: [tpqTotalsPlugin]
                });
            }
        }

        function updateGuruInfografisDOM(data) {
            if (!data) return;

            // Total Valid
            $('#txtTotalValidGuru').text((data.total_valid || 0) + ' GURU');
            if (data.nama_tpq && data.id_tpq != 0) {
                $('#txtNamaTpqSubtitle').text('Lembaga: ' + data.nama_tpq).show();
            } else {
                $('#txtNamaTpqSubtitle').hide();
            }

            // Usia Cards
            var demografi = data.demografi_usia || {};
            $('#valAvgAge').text((demografi.avg_age || 0) + ' TAHUN');
            $('#valMinAge').text((demografi.min_age || 0) + ' TAHUN');
            $('#valMaxAge').text((demografi.max_age || 0) + ' TAHUN');

            // Jenis Kelamin
            var jk = data.jenis_kelamin || {};
            var jmlP = jk.perempuan || 0;
            var pctP = jk.pct_perempuan || 0;
            var jmlL = jk.laki_laki || 0;
            var pctL = jk.pct_laki || 0;
            $('#txtCountPerempuan').html(jmlP + ' GURU <small class="text-muted font-weight-normal">(' + pctP + '%)</small>');
            $('#txtCountLaki').html(jmlL + ' GURU <small class="text-muted font-weight-normal">(' + pctL + '%)</small>');

            // Status Aktif
            var st = data.status_aktif || {};
            var pctAktif = st.pct_aktif || 0;
            var countAktif = st.aktif || 0;
            var pctNonAktif = st.pct_non_aktif || 0;
            var countNonAktif = st.non_aktif || 0;

            $('#txtPctAktif').text(pctAktif + '%');
            $('#txtCountAktif').text(countAktif + ' GURU');
            var hAktif = Math.max(40, Math.min(185, Math.round(pctAktif * 1.85)));
            $('#pillarAktif').css('height', hAktif + 'px');

            $('#txtPctNonAktif').text(pctNonAktif + '%');
            $('#txtCountNonAktif').text(countNonAktif + ' GURU');

            // Sebaran Wilayah
            var wilayah = data.sebaran_wilayah || {};
            var wilayahHtml = '';
            var counts = Object.values(wilayah);
            var maxCount = counts.length > 0 ? Math.max.apply(null, counts) : 1;
            var scaleMax = Math.max(1, Math.round(maxCount * 1.18));

            for (var namaKel in wilayah) {
                if (wilayah.hasOwnProperty(namaKel)) {
                    var jml = wilayah[namaKel];
                    var pctWidth = Math.round((jml / scaleMax) * 100);
                    wilayahHtml += '<div class="wilayah-item-row">' +
                        '<div class="wilayah-item-name">' + namaKel + '</div>' +
                        '<div class="wilayah-item-count">' + jml + ' GURU</div>' +
                        '<div class="wilayah-item-graphic">' +
                            '<svg viewBox="0 0 24 24" width="24" height="24" class="wilayah-map-icon" fill="none">' +
                                '<path d="M3 6.5L8.5 4.5L15.5 7.5L21 5.5V18.5L15.5 20.5L8.5 17.5L3 19.5V6.5Z" fill="#1e5138" fill-opacity="0.2" stroke="#1e5138" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>' +
                                '<path d="M8.5 4.5V17.5M15.5 7.5V20.5" stroke="#1e5138" stroke-width="1.6" stroke-linecap="round"/>' +
                                '<path d="M12 4C10.34 4 9 5.34 9 7C9 9.25 12 12.5 12 12.5C12 12.5 15 9.25 15 7C15 5.34 13.66 4 12 4Z" fill="#1e5138"/>' +
                                '<circle cx="12" cy="7" r="1.2" fill="#ffffff"/>' +
                            '</svg>' +
                            '<div class="wilayah-track">' +
                                '<div class="wilayah-bar" style="width: ' + pctWidth + '%;"></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                }
            }
            if (!wilayahHtml) {
                wilayahHtml = '<div class="text-center text-muted py-3">Tidak ada data wilayah untuk filter ini</div>';
            }
            $('#containerSebaranWilayah').html(wilayahHtml);

            // Statistik Guru Per TPQ Summary Badges
            var gTpq = data.guru_per_tpq || {};
            var tpqLabels = gTpq.labels || [];
            var tpqTotals = gTpq.totals || [];
            $('#txtTpqCount').text(gTpq.total_tpq || tpqLabels.length || 0);
            if (tpqLabels.length > 0) {
                $('#txtTopTpqName').text(tpqLabels[0]);
                $('#txtTopTpqCount').text(tpqTotals[0] || 0);
            } else {
                $('#txtTopTpqName').text('-');
                $('#txtTopTpqCount').text('0');
            }

            // Periode
            if (data.periode_text) {
                $('#txtFooterPeriode').text(data.periode_text);
            }

            // Subtitle active filter indicator
            var subtitleParts = [];
            if (data.nama_tpq && data.id_tpq != 0) {
                subtitleParts.push('Lembaga: ' + data.nama_tpq);
            }
            if (data.filter_kelurahan) {
                subtitleParts.push('Wilayah: ' + data.filter_kelurahan);
            }
            if (data.filter_status && data.filter_status !== 'all') {
                subtitleParts.push('Status: ' + (data.filter_status === '1' ? 'Aktif' : 'Non-Aktif'));
            }
            if (subtitleParts.length > 0) {
                $('#txtNamaTpqSubtitle').html('<span class="badge badge-light border text-dark px-2 py-1"><i class="fas fa-filter text-success mr-1"></i>' + subtitleParts.join(' &bull; ') + '</span>').show();
            } else {
                $('#txtNamaTpqSubtitle').hide();
            }

            // Re-render charts with new data
            renderGuruCharts(data);
        }

        // Render initial charts
        setTimeout(function() {
            renderGuruCharts(initialGuruStatData);
        }, 200);

        // ==================== FILTER HANDLERS (TPQ, KELURAHAN, STATUS) ====================
        var currentGuruFilterStatus = 'all';

        function fetchFilteredGuruStats() {
            var idTpq = $('#selectFilterTpqGuru').val() || 0;
            var kelurahan = $('#selectFilterKelurahanGuru').val() || '';
            var status = currentGuruFilterStatus;

            $('#txtTotalValidGuru').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '<?= base_url('backend/dashboard/getStatistikGuruAjax') ?>',
                method: 'GET',
                data: {
                    id_tpq: idTpq,
                    kelurahan: kelurahan,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        updateGuruInfografisDOM(response.data);
                    }
                },
                error: function(xhr) {
                    console.error('Gagal mengambil statistik guru:', xhr);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Gagal memuat filter statistik guru');
                    }
                }
            });
        }

        // Filter TPQ change event
        $('#selectFilterTpqGuru').on('change', function() {
            fetchFilteredGuruStats();
        });

        // Filter Kelurahan/Desa change event
        $('#selectFilterKelurahanGuru').on('change', function() {
            fetchFilteredGuruStats();
        });

        // Toggle Status change event
        $('.btn-toggle-status').on('click', function(e) {
            e.preventDefault();
            var selectedStatus = $(this).data('status');
            currentGuruFilterStatus = String(selectedStatus);

            // Update UI buttons
            $('.btn-toggle-status').removeClass('active btn-success btn-secondary').addClass('btn-outline-success');
            $('.btn-toggle-status[data-status="0"]').removeClass('btn-outline-success').addClass('btn-outline-secondary');

            if (selectedStatus === 'all' || selectedStatus === '1') {
                $(this).removeClass('btn-outline-success').addClass('btn-success active');
            } else {
                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary active');
            }

            fetchFilteredGuruStats();
        });

        // Trigger chart resize if card is expanded or maximized
        $('#infografis-card-print').closest('.card').on('expanded.lte.cardwidget maximized.lte.cardwidget', function() {
            setTimeout(function() {
                if (chartUsiaGuruInstance) chartUsiaGuruInstance.resize();
                if (chartJenisKelaminInstance) chartJenisKelaminInstance.resize();
                if (chartGuruPerTpqInstance) chartGuruPerTpqInstance.resize();
            }, 200);
        });

        // ==================== INFOGRAFIS STATISTIK SANTRI & RASIO GURU:SANTRI ====================
        var chartSantriPerTpqInstance = null;
        var chartSantriPerKelasInstance = null;
        var chartRasioGuruSantriInstance = null;
        var currentSantriFilterStatus = '1';

        <?php
        $initSpk = $santriPerKelas ?? [];
        $initSpt = $santriPerTpq ?? [];
        $initRgs = $rasioSantri ?? [];
        ?>
        var dataSantriPerTpq = {
            labels: <?= json_encode($initSpt['labels'] ?? []) ?>,
            totals: <?= json_encode($initSpt['totals'] ?? []) ?>,
            laki: <?= json_encode($initSpt['laki'] ?? []) ?>,
            perempuan: <?= json_encode($initSpt['perempuan'] ?? []) ?>
        };

        var dataSantriPerKelas = {
            labels: <?= json_encode($initSpk['labels'] ?? []) ?>,
            totals: <?= json_encode($initSpk['totals'] ?? []) ?>,
            laki: <?= json_encode($initSpk['laki'] ?? []) ?>,
            perempuan: <?= json_encode($initSpk['perempuan'] ?? []) ?>
        };

        var dataRasioTpq = {
            labels: <?= json_encode($initRgs['labels'] ?? []) ?>,
            ratios: <?= json_encode($initRgs['ratios'] ?? []) ?>,
            ratioLabels: <?= json_encode($initRgs['ratio_labels'] ?? []) ?>,
            colors: <?= json_encode($initRgs['colors'] ?? []) ?>,
            santri: <?= json_encode($initRgs['santri'] ?? []) ?>,
            guru: <?= json_encode($initRgs['guru'] ?? []) ?>
        };

        function renderSantriAndRasioCharts() {
            if (typeof Chart === 'undefined') return;
            var isV3V4 = typeof Chart.version !== 'undefined' && parseInt(Chart.version) >= 3;

            // 1. Chart Santri Per Kelas (Grouped Bar Chart)
            var ctxSantriKelas = document.getElementById('chartSantriPerKelas');
            if (ctxSantriKelas) {
                if (chartSantriPerKelasInstance) {
                    chartSantriPerKelasInstance.destroy();
                    chartSantriPerKelasInstance = null;
                }

                chartSantriPerKelasInstance = new Chart(ctxSantriKelas, {
                    type: 'bar',
                    data: {
                        labels: dataSantriPerKelas.labels,
                        datasets: [
                            {
                                label: 'Laki-Laki',
                                data: dataSantriPerKelas.laki,
                                backgroundColor: '#0284c7',
                                hoverBackgroundColor: '#0369a1',
                                borderRadius: 4,
                                barPercentage: 0.65,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Perempuan',
                                data: dataSantriPerKelas.perempuan,
                                backgroundColor: '#ec4899',
                                hoverBackgroundColor: '#db2777',
                                borderRadius: 4,
                                barPercentage: 0.65,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 20, bottom: 5 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        var ds = ctx.dataset ? (ctx.dataset.label || '') : '';
                                        var v = ctx.parsed ? (ctx.parsed.y || ctx.parsed) : (ctx.yLabel || 0);
                                        return ds + ': ' + Number(v).toLocaleString() + ' Santri';
                                    }
                                }
                            }
                        },
                        scales: isV3V4 ? {
                            x: { grid: { display: false }, ticks: { font: { weight: '700', size: 11 }, color: '#1e293b' } },
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } }
                        } : {
                            xAxes: [{ gridLines: { display: false } }],
                            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                        }
                    }
                });
            }

            // 2. Chart Santri Per TPQ (Horizontal Stacked Bar)
            var ctxSantriTpq = document.getElementById('chartSantriPerTpq');
            if (ctxSantriTpq) {
                if (chartSantriPerTpqInstance) {
                    chartSantriPerTpqInstance.destroy();
                    chartSantriPerTpqInstance = null;
                }

                var maxValSantri = dataSantriPerTpq.totals.length > 0 ? Math.max.apply(null, dataSantriPerTpq.totals) : 10;
                var pluginTotalsSantri = {
                    id: 'santriTotalsPlugin',
                    afterDatasetsDraw: function(chart) {
                        try {
                            var ctx = chart.ctx;
                            var metaP = chart.getDatasetMeta(1);
                            var metaL = chart.getDatasetMeta(0);
                            dataSantriPerTpq.labels.forEach(function(lbl, index) {
                                var tot = dataSantriPerTpq.totals[index] || 0;
                                if (tot > 0) {
                                    var lastBar = (metaP && metaP.data && metaP.data[index]) ? metaP.data[index] : (metaL && metaL.data ? metaL.data[index] : null);
                                    if (lastBar) {
                                        var x = (typeof lastBar.x !== 'undefined') ? lastBar.x : (lastBar._model ? lastBar._model.x : null);
                                        var y = (typeof lastBar.y !== 'undefined') ? lastBar.y : (lastBar._model ? lastBar._model.y : null);
                                        if (x !== null && y !== null && !isNaN(x) && !isNaN(y)) {
                                            ctx.save();
                                            ctx.fillStyle = '#0f172a';
                                            ctx.font = 'bold 11px sans-serif';
                                            ctx.textAlign = 'left';
                                            ctx.textBaseline = 'middle';
                                            ctx.fillText(Number(tot).toLocaleString() + ' Santri', x + 8, y);
                                            ctx.restore();
                                        }
                                    }
                                }
                            });
                        } catch(e) {}
                    }
                };

                chartSantriPerTpqInstance = new Chart(ctxSantriTpq, {
                    type: isV3V4 ? 'bar' : 'horizontalBar',
                    data: {
                        labels: dataSantriPerTpq.labels,
                        datasets: [
                            {
                                label: 'Laki-Laki',
                                data: dataSantriPerTpq.laki,
                                backgroundColor: '#0284c7',
                                hoverBackgroundColor: '#0369a1',
                                borderRadius: 4,
                                barPercentage: 0.72,
                                categoryPercentage: 0.85
                            },
                            {
                                label: 'Perempuan',
                                data: dataSantriPerTpq.perempuan,
                                backgroundColor: '#ec4899',
                                hoverBackgroundColor: '#db2777',
                                borderRadius: 4,
                                barPercentage: 0.72,
                                categoryPercentage: 0.85
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        layout: { padding: { right: 75, top: 10, bottom: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        var ds = ctx.dataset ? (ctx.dataset.label || '') : '';
                                        var v = ctx.parsed ? (ctx.parsed.x || ctx.parsed) : (ctx.xLabel || 0);
                                        return ds + ': ' + Number(v).toLocaleString() + ' Santri';
                                    },
                                    afterBody: function(items) {
                                        if (!items || items.length === 0) return '';
                                        var idx = items[0].dataIndex;
                                        return 'Total: ' + Number(dataSantriPerTpq.totals[idx] || 0).toLocaleString() + ' Santri';
                                    }
                                }
                            }
                        },
                        scales: isV3V4 ? {
                            x: { stacked: true, beginAtZero: true, max: Math.round(maxValSantri * 1.25) + 2, grid: { color: '#f1f5f9' } },
                            y: { stacked: true, grid: { display: false }, ticks: { font: { weight: '700', size: 11 }, color: '#1e293b' } }
                        } : {
                            xAxes: [{ stacked: true, ticks: { beginAtZero: true, suggestedMax: Math.round(maxValSantri * 1.25) + 2 }, gridLines: { color: '#f1f5f9' } }],
                            yAxes: [{ stacked: true, gridLines: { display: false } }]
                        }
                    },
                    plugins: [pluginTotalsSantri]
                });
            }

            // 3. Chart Rasio Guru : Santri Per TPQ
            var ctxRasio = document.getElementById('chartRasioGuruSantri');
            if (ctxRasio) {
                if (chartRasioGuruSantriInstance) {
                    chartRasioGuruSantriInstance.destroy();
                    chartRasioGuruSantriInstance = null;
                }

                var pluginRasioLabels = {
                    id: 'rasioLabelsPlugin',
                    afterDatasetsDraw: function(chart) {
                        try {
                            var ctx = chart.ctx;
                            var meta = chart.getDatasetMeta(0);
                            dataRasioTpq.labels.forEach(function(lbl, index) {
                                var rStr = dataRasioTpq.ratioLabels[index] || '-';
                                var bar = meta && meta.data ? meta.data[index] : null;
                                if (bar && rStr !== '-') {
                                    var x = (typeof bar.x !== 'undefined') ? bar.x : (bar._model ? bar._model.x : null);
                                    var y = (typeof bar.y !== 'undefined') ? bar.y : (bar._model ? bar._model.y : null);
                                    if (x !== null && y !== null && !isNaN(x) && !isNaN(y)) {
                                        ctx.save();
                                        ctx.fillStyle = '#0f172a';
                                        ctx.font = 'bold 11px sans-serif';
                                        ctx.textAlign = 'left';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText(rStr, x + 8, y);
                                        ctx.restore();
                                    }
                                }
                            });
                        } catch(e) {}
                    }
                };

                var maxRasio = dataRasioTpq.ratios.length > 0 ? Math.max.apply(null, dataRasioTpq.ratios) : 15;

                chartRasioGuruSantriInstance = new Chart(ctxRasio, {
                    type: isV3V4 ? 'bar' : 'horizontalBar',
                    data: {
                        labels: dataRasioTpq.labels,
                        datasets: [{
                            label: 'Rasio Santri per 1 Guru',
                            data: dataRasioTpq.ratios,
                            backgroundColor: dataRasioTpq.colors,
                            borderRadius: 4,
                            barPercentage: 0.72,
                            categoryPercentage: 0.85
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        layout: { padding: { right: 55, top: 10, bottom: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        var idx = ctx.dataIndex;
                                        var rStr = dataRasioTpq.ratioLabels[idx] || '-';
                                        var s = dataRasioTpq.santri[idx] || 0;
                                        var g = dataRasioTpq.guru[idx] || 0;
                                        return 'Rasio ' + rStr + ' (' + g + ' Guru : ' + s + ' Santri)';
                                    }
                                }
                            }
                        },
                        scales: isV3V4 ? {
                            x: { beginAtZero: true, max: Math.max(16, Math.round(maxRasio * 1.25)), grid: { color: '#f1f5f9' }, title: { display: true, text: 'Santri per 1 Guru', font: { size: 10 } } },
                            y: { grid: { display: false }, ticks: { font: { weight: '700', size: 11 }, color: '#1e293b' } }
                        } : {
                            xAxes: [{ ticks: { beginAtZero: true, suggestedMax: Math.max(16, Math.round(maxRasio * 1.25)) } }],
                            yAxes: [{ gridLines: { display: false } }]
                        }
                    },
                    plugins: [pluginRasioLabels]
                });

                setTimeout(syncRasioChartHeight, 50);
            }
        }

        // Sinkronisasi tinggi grafik rasio dengan tinggi tabel rasio agar presisi dan tidak ada area kosong
        function syncRasioChartHeight() {
            var $tbl = $('#tableListRasioTpq');
            if ($tbl.length) {
                var tblHeight = $tbl.outerHeight();
                if (tblHeight && tblHeight > 250) {
                    $('#wrapperChartRasioGuruSantri').css('height', tblHeight + 'px');
                    if (chartRasioGuruSantriInstance) {
                        chartRasioGuruSantriInstance.resize();
                    }
                }
            }
        }

        // Live Search di Tabel Rasio
        $('#inputSearchTpqRasio').on('keyup', function() {
            var val = $(this).val().toLowerCase();
            $('#tableListRasioTpq tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
            });
            setTimeout(syncRasioChartHeight, 50);
        });

        // Trigger chart resize if Santri card is expanded or maximized
        $('#infografis-santri-card-print').closest('.card').on('expanded.lte.cardwidget maximized.lte.cardwidget', function() {
            setTimeout(function() {
                if (chartSantriPerKelasInstance) chartSantriPerKelasInstance.resize();
                if (chartSantriPerTpqInstance) chartSantriPerTpqInstance.resize();
                if (chartRasioGuruSantriInstance) chartRasioGuruSantriInstance.resize();
                syncRasioChartHeight();
            }, 200);
        });

        $(window).on('resize', function() {
            syncRasioChartHeight();
        });

        // Fungsi Update DOM Infografis Santri
        function updateSantriInfografisDOM(d) {
            if (!d) return;

            // Header total valid & badges
            $('#txtTotalValidSantri').text(Number(d.total_valid || 0).toLocaleString());
            var tpqText = (d.nama_tpq && d.nama_tpq !== 'SEMUA TPQ') ? d.nama_tpq : '';
            var kelText = d.filter_kelurahan ? ('Wilayah ' + d.filter_kelurahan) : '';
            var stText = (d.filter_status === '1') ? 'Aktif' : ((d.filter_status === '0') ? 'Santri Baru' : ((d.filter_status === '2') ? 'Non-Aktif' : ''));
            var badgeFilterParts = [];
            if (tpqText) badgeFilterParts.push(tpqText);
            if (kelText) badgeFilterParts.push(kelText);
            if (stText) badgeFilterParts.push('Status: ' + stText);
            $('#txtFilterBadgeSantri').html('<i class="fas fa-filter text-muted mr-1"></i>' + (badgeFilterParts.length > 0 ? badgeFilterParts.join(' &bull; ') : 'Semua Wilayah & TPQ'));

            // Top KPI Mini Cards
            var kpi = d.kpi || {};
            var gen = d.komposisi_gender || {};
            $('#kpiSantriTotalVal').text(Number(d.total_valid || 0).toLocaleString());
            var tpqCount = (d.santri_per_tpq && d.santri_per_tpq.labels) ? d.santri_per_tpq.labels.length : 0;
            $('#kpiSantriTotalTpqVal').text('Dari ' + tpqCount + ' TPQ Terdata');

            $('#kpiSantriGenderLakiVal').text(Number(gen.laki_laki || 0).toLocaleString() + ' L');
            $('#kpiSantriGenderPerempuanVal').text(Number(gen.perempuan || 0).toLocaleString() + ' P');
            $('#kpiSantriGenderPctVal').text((gen.pct_laki || 0) + '% Laki \u2022 ' + (gen.pct_perempuan || 0) + '% Perempuan');

            $('#kpiSantriAvgRasioVal').text('1 : ' + (kpi.avg_rasio_ceil || 0));
            $('#kpiSantriAvgRasioSub').text('1 Guru : ' + (kpi.avg_rasio || 0) + ' Santri');

            $('#kpiSantriCountIdealVal').text((kpi.count_ideal || 0) + ' Ideal');
            $('#kpiSantriCountPadatVal').text((kpi.count_padat || 0) + ' Padat');
            $('#kpiSantriCountRendahVal').text((kpi.count_rendah || 0) + ' Rendah');

            // Section 1: Santri Per Kelas
            var spk = d.santri_per_kelas || {};
            $('#txtKelasCountSantri').text((spk.labels || []).length);
            var tbodyKelasHtml = '';
            if (spk.items && spk.items.length > 0) {
                spk.items.forEach(function(item) {
                    tbodyKelasHtml += '<tr>' +
                        '<td class="font-weight-bold text-dark" style="padding-left: 12px;">' + (item.NamaKelas || '') + '</td>' +
                        '<td class="text-center text-info font-weight-bold">' + Number(item.LakiLaki || 0).toLocaleString() + '</td>' +
                        '<td class="text-center font-weight-bold" style="color: #ec4899;">' + Number(item.Perempuan || 0).toLocaleString() + '</td>' +
                        '<td class="text-center font-weight-bold text-dark">' + Number(item.Total || 0).toLocaleString() + '</td>' +
                        '</tr>';
                });
            } else {
                tbodyKelasHtml = '<tr><td colspan="4" class="text-center py-2 text-muted">Tidak ada data kelas</td></tr>';
            }
            $('#tbodyRingkasSantriKelas').html(tbodyKelasHtml);

            // Section 2: Santri Per TPQ info
            var spt = d.santri_per_tpq || {};
            $('#txtTpqCountSantri').text((spt.labels || []).length);
            $('#txtTopTpqNameSantri').text(spt.top_tpq_nama || '-');
            $('#txtTopTpqCountSantri').text(Number(spt.top_tpq_count || 0).toLocaleString());

            var tpqHeight = Math.max(300, (spt.labels || []).length * 28 + 60);
            $('#wrapperChartSantriPerTpq').css('height', tpqHeight + 'px');

            // Section 3: Rasio Guru : Santri Per TPQ
            var rgs = d.rasio_guru_santri || {};

            var tbodyRasioHtml = '';
            if (rgs.items && rgs.items.length > 0) {
                rgs.items.forEach(function(r) {
                    tbodyRasioHtml += '<tr class="row-rasio-tpq">' +
                        '<td style="padding-left: 12px;">' +
                            '<div class="font-weight-bold text-dark">' + (r.NamaTpq || '') + '</div>' +
                        '</td>' +
                        '<td class="text-center font-weight-bold text-success align-middle">' + Number(r.TotalGuru || 0).toLocaleString() + '</td>' +
                        '<td class="text-center font-weight-bold text-primary align-middle">' + Number(r.TotalSantri || 0).toLocaleString() + '</td>' +
                        '<td class="text-center align-middle">' +
                            '<span class="badge badge-' + (r.BadgeColor || 'secondary') + ' px-2 py-1 font-weight-bold">' + (r.Rasio || '-') + '</span>' +
                        '</td>' +
                        '</tr>';
                });
            } else {
                tbodyRasioHtml = '<tr><td colspan="4" class="text-center py-3 text-muted">Data rasio tidak ditemukan</td></tr>';
            }
            $('#tbodyListRasioTpq').html(tbodyRasioHtml);
            setTimeout(syncRasioChartHeight, 60);

            // Section 4: Matriks Detail
            var mtx = d.matriks_detail || {};
            var tbodyMatriksHtml = '';
            if (mtx.data && mtx.data.length > 0) {
                mtx.data.forEach(function(tpq) {
                    tbodyMatriksHtml += '<tr>' +
                        '<td class="text-left font-weight-bold align-middle" style="padding-left: 12px;">' + (tpq.NamaTpq || '') + '</td>' +
                        '<td class="text-left align-middle">' + (tpq.KelurahanDesa || '') + '</td>';
                    if (mtx.kelasList && mtx.kelasList.length > 0) {
                        mtx.kelasList.forEach(function(k) {
                            var j = (tpq.Kelas && tpq.Kelas[k.IdKelas]) ? (tpq.Kelas[k.IdKelas].Jumlah || 0) : 0;
                            tbodyMatriksHtml += '<td class="text-center align-middle">' + (j > 0 ? Number(j).toLocaleString() : '-') + '</td>';
                        });
                    }
                    tbodyMatriksHtml += '<td class="text-center bg-light font-weight-bold align-middle">' + Number(tpq.Total || 0).toLocaleString() + '</td>' +
                        '</tr>';
                });
            } else {
                var colSpan = 3 + ((mtx.kelasList && mtx.kelasList.length) || 0);
                tbodyMatriksHtml = '<tr><td colspan="' + colSpan + '" class="text-center py-3 text-muted">Data matriks santri per kelas belum tersedia</td></tr>';
            }
            $('#tbodyMatriksSantri').html(tbodyMatriksHtml);

            // Footer Matriks
            var tfootMatriksHtml = '<tr>' +
                '<th class="bg-primary text-white text-left font-weight-bold align-middle" style="padding-left: 12px;">Total</th>' +
                '<th class="bg-primary text-white align-middle"></th>';
            if (mtx.kelasList && mtx.kelasList.length > 0) {
                mtx.kelasList.forEach(function(k) {
                    var totK = (mtx.totalPerKelas && mtx.totalPerKelas[k.IdKelas]) ? mtx.totalPerKelas[k.IdKelas] : 0;
                    tfootMatriksHtml += '<th class="text-center bg-primary text-white font-weight-bold align-middle">' + Number(totK).toLocaleString() + '</th>';
                });
            }
            tfootMatriksHtml += '<th class="text-center bg-primary text-white font-weight-bold align-middle">' + Number(mtx.grandTotal || 0).toLocaleString() + '</th>' +
                '</tr>';
            $('#tfootMatriksSantri').html(tfootMatriksHtml);

            // Periode Footer
            if (d.periode_text) {
                $('#txtFooterPeriodeSantri').html(d.periode_text);
            }

            // Update chart data
            dataSantriPerTpq = {
                labels: spt.labels || [],
                totals: spt.totals || [],
                laki: spt.laki || [],
                perempuan: spt.perempuan || []
            };
            dataSantriPerKelas = {
                labels: spk.labels || [],
                totals: spk.totals || [],
                laki: spk.laki || [],
                perempuan: spk.perempuan || []
            };
            dataRasioTpq = {
                labels: rgs.labels || [],
                ratios: rgs.ratios || [],
                ratioLabels: rgs.ratio_labels || [],
                colors: rgs.colors || [],
                santri: rgs.santri || [],
                guru: rgs.guru || []
            };

            renderSantriAndRasioCharts();
        }

        // Fetch Filtered Santri Stats via AJAX
        function fetchFilteredSantriStats() {
            var idTpq = $('#selectFilterTpqSantri').val() || 0;
            var kelurahan = $('#selectFilterKelurahanSantri').val() || '';
            var status = currentSantriFilterStatus;

            $('#txtTotalValidSantri').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '<?= base_url('backend/dashboard/getStatistikSantriAjax') ?>',
                method: 'GET',
                data: {
                    id_tpq: idTpq,
                    kelurahan: kelurahan,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        updateSantriInfografisDOM(response.data);
                    }
                },
                error: function(xhr) {
                    console.error('Gagal mengambil statistik santri:', xhr);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Gagal memuat filter statistik santri');
                    }
                }
            });
        }

        // Filter events Santri
        $('#selectFilterTpqSantri').on('change', function() {
            fetchFilteredSantriStats();
        });

        $('#selectFilterKelurahanSantri').on('change', function() {
            fetchFilteredSantriStats();
        });

        $('.btn-toggle-santri-status').on('click', function(e) {
            e.preventDefault();
            var selectedStatus = $(this).data('status');
            currentSantriFilterStatus = String(selectedStatus);

            $('.btn-toggle-santri-status').removeClass('active btn-info btn-warning btn-secondary')
                .addClass('btn-outline-info');
            $('.btn-toggle-santri-status[data-status="0"]').removeClass('btn-outline-info').addClass('btn-outline-warning');
            $('.btn-toggle-santri-status[data-status="2"]').removeClass('btn-outline-info').addClass('btn-outline-secondary');

            if (selectedStatus === 'all' || selectedStatus === '1' || selectedStatus === 1) {
                $(this).removeClass('btn-outline-info').addClass('btn-info active');
            } else if (selectedStatus === '0' || selectedStatus === 0) {
                $(this).removeClass('btn-outline-warning').addClass('btn-warning active');
            } else if (selectedStatus === '2' || selectedStatus === 2) {
                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary active');
            }

            fetchFilteredSantriStats();
        });

        setTimeout(function() {
            renderSantriAndRasioCharts();
        }, 300);

        // Jadwal Sholat - moved to helper function
    });

    function printGuruInfographic() {
        // 1. Clone elemen infografis
        var $printContent = $('#infografis-card-print').clone();

        // 2. Konversi canvas Chart.js ke gambar PNG resolusi tinggi agar tidak hilang/blank saat print PDF
        var canvasUsia = document.getElementById('chartUsiaGuru');
        if (canvasUsia) {
            try {
                var imgUsia = document.createElement('img');
                imgUsia.src = canvasUsia.toDataURL('image/png', 1.0);
                imgUsia.style.width = '100%';
                imgUsia.style.height = 'auto';
                imgUsia.style.maxHeight = '260px';
                imgUsia.style.display = 'block';
                $printContent.find('#chartUsiaGuru').replaceWith(imgUsia);
            } catch (e) {
                console.error('Error converting usia canvas:', e);
            }
        }

        var canvasGender = document.getElementById('chartJenisKelaminGuru');
        if (canvasGender) {
            try {
                var imgGender = document.createElement('img');
                imgGender.src = canvasGender.toDataURL('image/png', 1.0);
                imgGender.style.width = '100%';
                imgGender.style.height = 'auto';
                imgGender.style.maxHeight = '220px';
                imgGender.style.display = 'block';
                $printContent.find('#chartJenisKelaminGuru').replaceWith(imgGender);
            } catch (e) {
                console.error('Error converting gender canvas:', e);
            }
        }

        var canvasTpq = document.getElementById('chartGuruPerTpq');
        if (canvasTpq) {
            try {
                var imgTpq = document.createElement('img');
                imgTpq.src = canvasTpq.toDataURL('image/png', 1.0);
                imgTpq.style.width = '100%';
                imgTpq.style.height = 'auto';
                imgTpq.style.display = 'block';
                $printContent.find('#chartGuruPerTpq').replaceWith(imgTpq);
            } catch (e) {
                console.error('Error converting tpq canvas:', e);
            }
        }

        // 3. Bersihkan iframe print lama jika ada
        $('#printGuruIframe').remove();

        // 4. Buat iframe tersembunyi
        var iframe = document.createElement('iframe');
        iframe.id = 'printGuruIframe';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        var iframeDoc = iframe.contentWindow || iframe.contentDocument;
        if (iframeDoc.document) iframeDoc = iframeDoc.document;

        var stylesCss = $('#infografis-styles-tag').text();

        var printHtml = '<!DOCTYPE html>' +
            '<html>' +
            '<head>' +
            '<title>Infografis Statistik Guru TPQ</title>' +
            '<meta charset="utf-8">' +
            '<meta name="viewport" content="width=device-width, initial-scale=1">' +
            '<link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">' +
            '<link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">' +
            '<style>' +
            '@page { size: A4 portrait; margin: 8mm 10mm; }' +
            '* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }' +
            'body { background: #ffffff !important; margin: 0; padding: 10px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }' +
            stylesCss +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="container-fluid p-0">' +
            $printContent.prop('outerHTML') +
            '</div>' +
            '</body>' +
            '</html>';

        iframeDoc.open();
        iframeDoc.write(printHtml);
        iframeDoc.close();

        // 5. Eksekusi print setelah styles & images siap
        setTimeout(function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 500);
    }

    // Helper untuk memuat html2canvas secara dinamis jika diperlukan
    function ensureHtml2Canvas(callback) {
        if (typeof html2canvas !== 'undefined') {
            callback();
        } else {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = callback;
            script.onerror = function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Gagal memuat library html2canvas.');
                }
            };
            document.head.appendChild(script);
        }
    }

    // Salin Gambar PNG ke Clipboard
    function copyGuruInfographicPNG() {
        var $btn = $('#btnCopyGuruInfographic');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyalin...');

        ensureHtml2Canvas(function() {
            var targetEl = document.getElementById('infografis-card-print');
            if (!targetEl) {
                $btn.prop('disabled', false).html(originalHtml);
                return;
            }

            var captureOptions = {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                scrollX: 0,
                scrollY: 0,
                onclone: function(clonedDoc) {
                    var origCard = document.getElementById('infografis-card-print');
                    var clonedCard = clonedDoc.getElementById('infografis-card-print');
                    if (origCard && clonedCard) {
                        var origCanvases = origCard.querySelectorAll('canvas');
                        var clonedCanvases = clonedCard.querySelectorAll('canvas');
                        for (var i = 0; i < origCanvases.length; i++) {
                            if (clonedCanvases[i]) {
                                var ctx = clonedCanvases[i].getContext('2d');
                                if (ctx) {
                                    ctx.drawImage(origCanvases[i], 0, 0);
                                }
                            }
                        }
                    }
                }
            };

            html2canvas(targetEl, captureOptions).then(function(canvas) {
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (typeof toastr !== 'undefined') toastr.error('Gagal membuat gambar PNG.');
                        return;
                    }

                    if (navigator.clipboard && window.ClipboardItem && navigator.clipboard.write) {
                        var item = new ClipboardItem({ 'image/png': blob });
                        navigator.clipboard.write([item]).then(function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Tersalin!');
                            setTimeout(function() { $btn.html(originalHtml); }, 2500);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Gambar Berhasil Disalin!',
                                    text: 'Infografis telah disalin ke clipboard sebagai PNG. Anda dapat langsung menempelkannya (Ctrl + V) ke WhatsApp, Word, dll.',
                                    timer: 3500,
                                    showConfirmButton: false
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success('Gambar PNG berhasil disalin ke clipboard! (Ctrl + V)');
                            }
                        }).catch(function(err) {
                            console.warn('Clipboard write gagal, fallback download:', err);
                            triggerDownloadCanvasPNG(canvas);
                            $btn.prop('disabled', false).html(originalHtml);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Clipboard Dibatasi Browser',
                                    text: 'Direct clipboard copy dibatasi oleh browser/koneksi non-HTTPS. File gambar PNG telah otomatis diunduh ke komputer Anda!',
                                    timer: 4500,
                                    showConfirmButton: true
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.info('Clipboard dibatasi browser. Gambar PNG otomatis diunduh.');
                            }
                        });
                    } else {
                        triggerDownloadCanvasPNG(canvas);
                        $btn.prop('disabled', false).html(originalHtml);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Gambar Telah Diunduh',
                                text: 'Browser belum mendukung direct clipboard copy. File gambar PNG telah otomatis diunduh ke komputer Anda!',
                                timer: 4500,
                                showConfirmButton: true
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.info('Direct clipboard tidak didukung. File PNG telah diunduh.');
                        }
                    }
                }, 'image/png');
            }).catch(function(err) {
                console.error('Error html2canvas capture:', err);
                $btn.prop('disabled', false).html(originalHtml);
                if (typeof toastr !== 'undefined') toastr.error('Terjadi kesalahan saat capture infografis.');
            });
        });
    }

    // Unduh File Gambar PNG
    function downloadGuruInfographicPNG() {
        var $btn = $('#btnDownloadGuruInfographic');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

        ensureHtml2Canvas(function() {
            var targetEl = document.getElementById('infografis-card-print');
            if (!targetEl) {
                $btn.prop('disabled', false).html(originalHtml);
                return;
            }

            var captureOptions = {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                scrollX: 0,
                scrollY: 0,
                onclone: function(clonedDoc) {
                    var origCard = document.getElementById('infografis-card-print');
                    var clonedCard = clonedDoc.getElementById('infografis-card-print');
                    if (origCard && clonedCard) {
                        var origCanvases = origCard.querySelectorAll('canvas');
                        var clonedCanvases = clonedCard.querySelectorAll('canvas');
                        for (var i = 0; i < origCanvases.length; i++) {
                            if (clonedCanvases[i]) {
                                var ctx = clonedCanvases[i].getContext('2d');
                                if (ctx) {
                                    ctx.drawImage(origCanvases[i], 0, 0);
                                }
                            }
                        }
                    }
                }
            };

            html2canvas(targetEl, captureOptions).then(function(canvas) {
                triggerDownloadCanvasPNG(canvas);
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Terunduh!');
                setTimeout(function() { $btn.html(originalHtml); }, 2500);
            }).catch(function(err) {
                console.error('Error html2canvas capture:', err);
                $btn.prop('disabled', false).html(originalHtml);
                if (typeof toastr !== 'undefined') toastr.error('Gagal mengunduh gambar infografis.');
            });
        });
    }

    // ==================== CETAK & EXPORT INFOGRAFIS SANTRI ====================
    function printSantriInfographic() {
        var $printContent = $('#infografis-santri-card-print').clone();

        // Konversi 3 canvas Chart.js ke gambar PNG high-res agar tidak hilang saat print PDF
        var canvasKelas = document.getElementById('chartSantriPerKelas');
        if (canvasKelas) {
            try {
                var imgKelas = document.createElement('img');
                imgKelas.src = canvasKelas.toDataURL('image/png', 1.0);
                imgKelas.style.width = '100%';
                imgKelas.style.height = 'auto';
                imgKelas.style.maxHeight = '320px';
                imgKelas.style.display = 'block';
                $printContent.find('#chartSantriPerKelas').replaceWith(imgKelas);
            } catch (e) {
                console.error('Error converting santri kelas canvas:', e);
            }
        }

        var canvasTpq = document.getElementById('chartSantriPerTpq');
        if (canvasTpq) {
            try {
                var imgTpq = document.createElement('img');
                imgTpq.src = canvasTpq.toDataURL('image/png', 1.0);
                imgTpq.style.width = '100%';
                imgTpq.style.height = 'auto';
                imgTpq.style.display = 'block';
                $printContent.find('#chartSantriPerTpq').replaceWith(imgTpq);
            } catch (e) {
                console.error('Error converting santri tpq canvas:', e);
            }
        }

        var canvasRasio = document.getElementById('chartRasioGuruSantri');
        if (canvasRasio) {
            try {
                var imgRasio = document.createElement('img');
                imgRasio.src = canvasRasio.toDataURL('image/png', 1.0);
                imgRasio.style.width = '100%';
                imgRasio.style.height = 'auto';
                imgRasio.style.display = 'block';
                $printContent.find('#chartRasioGuruSantri').replaceWith(imgRasio);
            } catch (e) {
                console.error('Error converting rasio canvas:', e);
            }
        }

        $('#printSantriIframe').remove();

        var iframe = document.createElement('iframe');
        iframe.id = 'printSantriIframe';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        var iframeDoc = iframe.contentWindow || iframe.contentDocument;
        if (iframeDoc.document) iframeDoc = iframeDoc.document;

        var stylesCss = $('#infografis-styles-tag').text();

        var printHtml = '<!DOCTYPE html>' +
            '<html>' +
            '<head>' +
            '<title>Infografis Statistik Santri TPQ & Rasio Guru</title>' +
            '<meta charset="utf-8">' +
            '<meta name="viewport" content="width=device-width, initial-scale=1">' +
            '<link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">' +
            '<link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">' +
            '<style>' +
            '@page { size: A4 portrait; margin: 8mm 10mm; }' +
            '* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }' +
            'body { background: #ffffff !important; margin: 0; padding: 10px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }' +
            stylesCss +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="container-fluid p-0">' +
            $printContent.prop('outerHTML') +
            '</div>' +
            '</body>' +
            '</html>';

        iframeDoc.open();
        iframeDoc.write(printHtml);
        iframeDoc.close();

        setTimeout(function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 500);
    }

    // Salin Gambar PNG Santri ke Clipboard
    function copySantriInfographicPNG() {
        var $btn = $('#btnCopySantriInfographic');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyalin...');

        ensureHtml2Canvas(function() {
            var targetEl = document.getElementById('infografis-santri-card-print');
            if (!targetEl) {
                $btn.prop('disabled', false).html(originalHtml);
                return;
            }

            var captureOptions = {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                scrollX: 0,
                scrollY: 0,
                onclone: function(clonedDoc) {
                    var origCard = document.getElementById('infografis-santri-card-print');
                    var clonedCard = clonedDoc.getElementById('infografis-santri-card-print');
                    if (origCard && clonedCard) {
                        var origCanvases = origCard.querySelectorAll('canvas');
                        var clonedCanvases = clonedCard.querySelectorAll('canvas');
                        for (var i = 0; i < origCanvases.length; i++) {
                            if (clonedCanvases[i]) {
                                var ctx = clonedCanvases[i].getContext('2d');
                                if (ctx) {
                                    ctx.drawImage(origCanvases[i], 0, 0);
                                }
                            }
                        }
                    }
                }
            };

            html2canvas(targetEl, captureOptions).then(function(canvas) {
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (typeof toastr !== 'undefined') toastr.error('Gagal membuat gambar PNG.');
                        return;
                    }

                    if (navigator.clipboard && window.ClipboardItem && navigator.clipboard.write) {
                        var item = new ClipboardItem({ 'image/png': blob });
                        navigator.clipboard.write([item]).then(function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Tersalin!');
                            setTimeout(function() { $btn.html(originalHtml); }, 2500);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Gambar Berhasil Disalin!',
                                    text: 'Infografis santri telah disalin ke clipboard sebagai PNG. Anda dapat langsung menempelkannya (Ctrl + V) ke WhatsApp, Word, dll.',
                                    timer: 3500,
                                    showConfirmButton: false
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success('Gambar PNG berhasil disalin ke clipboard! (Ctrl + V)');
                            }
                        }).catch(function(err) {
                            console.warn('Clipboard write gagal, fallback download:', err);
                            triggerDownloadCanvasPNG(canvas, 'Infografis-Statistik-Santri-TPQ-' + new Date().toISOString().slice(0, 10) + '.png');
                            $btn.prop('disabled', false).html(originalHtml);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Clipboard Dibatasi Browser',
                                    text: 'Direct clipboard copy dibatasi oleh browser/koneksi non-HTTPS. File gambar PNG telah otomatis diunduh ke komputer Anda!',
                                    timer: 4500,
                                    showConfirmButton: true
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.info('Clipboard dibatasi browser. Gambar PNG otomatis diunduh.');
                            }
                        });
                    } else {
                        triggerDownloadCanvasPNG(canvas, 'Infografis-Statistik-Santri-TPQ-' + new Date().toISOString().slice(0, 10) + '.png');
                        $btn.prop('disabled', false).html(originalHtml);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Gambar Telah Diunduh',
                                text: 'Browser belum mendukung direct clipboard copy. File gambar PNG telah otomatis diunduh ke komputer Anda!',
                                timer: 4500,
                                showConfirmButton: true
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.info('Direct clipboard tidak didukung. File PNG telah diunduh.');
                        }
                    }
                }, 'image/png');
            }).catch(function(err) {
                console.error('Error html2canvas capture:', err);
                $btn.prop('disabled', false).html(originalHtml);
                if (typeof toastr !== 'undefined') toastr.error('Terjadi kesalahan saat capture infografis santri.');
            });
        });
    }

    // Unduh File Gambar PNG Santri
    function downloadSantriInfographicPNG() {
        var $btn = $('#btnDownloadSantriInfographic');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

        ensureHtml2Canvas(function() {
            var targetEl = document.getElementById('infografis-santri-card-print');
            if (!targetEl) {
                $btn.prop('disabled', false).html(originalHtml);
                return;
            }

            var captureOptions = {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                scrollX: 0,
                scrollY: 0,
                onclone: function(clonedDoc) {
                    var origCard = document.getElementById('infografis-santri-card-print');
                    var clonedCard = clonedDoc.getElementById('infografis-santri-card-print');
                    if (origCard && clonedCard) {
                        var origCanvases = origCard.querySelectorAll('canvas');
                        var clonedCanvases = clonedCard.querySelectorAll('canvas');
                        for (var i = 0; i < origCanvases.length; i++) {
                            if (clonedCanvases[i]) {
                                var ctx = clonedCanvases[i].getContext('2d');
                                if (ctx) {
                                    ctx.drawImage(origCanvases[i], 0, 0);
                                }
                            }
                        }
                    }
                }
            };

            html2canvas(targetEl, captureOptions).then(function(canvas) {
                var fileName = 'Infografis-Statistik-Santri-TPQ-' + new Date().toISOString().slice(0, 10) + '.png';
                triggerDownloadCanvasPNG(canvas, fileName);
                $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Terunduh!');
                setTimeout(function() { $btn.html(originalHtml); }, 2500);
            }).catch(function(err) {
                console.error('Error html2canvas capture:', err);
                $btn.prop('disabled', false).html(originalHtml);
                if (typeof toastr !== 'undefined') toastr.error('Gagal mengunduh gambar infografis santri.');
            });
        });
    }

    function triggerDownloadCanvasPNG(canvas, customFilename) {
        var link = document.createElement('a');
        var dateStr = new Date().toISOString().slice(0, 10);
        link.download = customFilename || ('Infografis-Statistik-Guru-TPQ-' + dateStr + '.png');
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
        if (typeof toastr !== 'undefined') {
            toastr.info('File gambar PNG infografis berhasil diunduh.');
        }
    }
</script>
<?= prayer_schedule_js(base_url('backend/jadwal-sholat')) ?>
<?= prayer_schedule_settings_js(base_url('backend/jadwal-sholat')) ?>
<?= $this->endSection(); ?>