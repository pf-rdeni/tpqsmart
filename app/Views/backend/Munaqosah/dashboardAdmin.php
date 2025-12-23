<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Admin</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Tahun Ajaran -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-calendar"></i> Tahun Ajaran: <?= esc(convertTahunAjaran($current_tahun_ajaran)) ?></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle AktiveTombolKelulusan -->
                        <?php if (in_groups('Admin')): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-toggle-on"></i> Pengaturan Tombol Kelulusan (Munaqosah)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="mb-3"><strong>Aktifkan Tombol Kelulusan</strong></label>
                                            <div class="d-flex align-items-center">
                                                <div class="toggle-switch-container position-relative">
                                                    <label class="toggle-switch">
                                                        <input type="checkbox"
                                                            class="toggle-switch-input"
                                                            id="toggleAktiveTombolKelulusan"
                                                            <?= $aktive_tombol_kelulusan_exists ? ($aktive_tombol_kelulusan_value ? 'checked' : '') : 'disabled' ?>
                                                            data-id-tpq="<?= esc($id_tpq_setting) ?>"
                                                            <?= !$aktive_tombol_kelulusan_exists ? 'title="Setting belum tersedia. Silakan buat setting terlebih dahulu di menu Konfigurasi Munaqosah."' : '' ?>>
                                                        <span class="toggle-switch-slider">
                                                            <span class="toggle-switch-label-on">AKTIF</span>
                                                            <span class="toggle-switch-label-off">TIDAK</span>
                                                        </span>
                                                    </label>
                                                    <div id="toggleProgressIndicator" class="toggle-progress-indicator" style="display: none;">
                                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!$aktive_tombol_kelulusan_exists): ?>
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> Setting belum tersedia. Silakan buat setting terlebih dahulu di menu <a href="<?= base_url('backend/munaqosah/list-konfigurasi-munaqosah') ?>">Konfigurasi Munaqosah</a>.
                                                </small>
                                            <?php else: ?>
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> Toggle ini mengaktifkan/menonaktifkan tombol "Lihat Kelulusan" di halaman konfirmasi data santri untuk typeUjian Munaqosah. <strong>Hanya Admin yang dapat mengubah pengaturan ini.</strong>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Statistik Card -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= number_format($total_peserta) ?></h3>
                                        <p>Total Peserta</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?= number_format($total_sudah_dinilai) ?></h3>
                                        <p>Sudah Dinilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="<?= $menu_items['monitoring'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= number_format($total_belum_dinilai) ?></h3>
                                        <p>Belum Dinilai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="small-box-footer">
                                        Registrasi Peserta <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?= number_format($total_juri) ?></h3>
                                        <p>Total Juri Aktif</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <a href="<?= $menu_items['data_juri'] ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Status Peserta -->
                        <div class="row mt-3">
                            <div class="col-12 mb-2">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-clipboard-check"></i> Statistik Status Verifikasi Data Peserta
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Menampilkan jumlah peserta berdasarkan status verifikasi data yang telah diverifikasi oleh orang tua santri">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Informasi:</strong> Statistik ini menampilkan jumlah peserta berdasarkan status verifikasi data mereka.
                                            Status verifikasi ditentukan setelah orang tua santri melakukan verifikasi data di halaman konfirmasi data santri.
                                        </p>
                                        <div class="row">
                                            <div class="col-lg-4 col-6">
                                                <div class="small-box bg-success">
                                                    <div class="inner">
                                                        <h3><?= number_format($total_status_valid) ?></h3>
                                                        <p>Status Valid</p>
                                                        <small style="font-size: 0.85em; opacity: 0.9;">Data telah diverifikasi dan dinyatakan benar</small>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-check-circle"></i>
                                                    </div>
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>?status=valid" class="small-box-footer">
                                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <div class="small-box bg-warning">
                                                    <div class="inner">
                                                        <h3><?= number_format($total_status_perbaikan) ?></h3>
                                                        <p>Perlu Perbaikan</p>
                                                        <small style="font-size: 0.85em; opacity: 0.9;">Data perlu diperbaiki oleh operator</small>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </div>
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>?status=perlu_perbaikan" class="small-box-footer">
                                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <div class="small-box bg-secondary">
                                                    <div class="inner">
                                                        <h3><?= number_format($total_status_belum_dikonfirmasi) ?></h3>
                                                        <p>Belum Dikonfirmasi</p>
                                                        <small style="font-size: 0.85em; opacity: 0.9;">Belum melakukan konfirmasi data</small>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="fas fa-clock"></i>
                                                    </div>
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>?status=belum_dikonfirmasi" class="small-box-footer">
                                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Penilaian -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Progress Penilaian</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $progressPercent = $total_peserta > 0
                                            ? round(($total_sudah_dinilai / $total_peserta) * 100)
                                            : 0;
                                        ?>
                                        <div class="progress mb-3" style="height: 30px;">
                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                style="width: <?= $progressPercent ?>%"
                                                aria-valuenow="<?= $progressPercent ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                <?= $progressPercent ?>%
                                            </div>
                                        </div>
                                        <p class="text-center">
                                            <strong><?= $total_sudah_dinilai ?></strong> dari <strong><?= $total_peserta ?></strong> peserta telah dinilai
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Jumlah Peserta per Tahun Ajaran dan TPQ -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-info collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-bar"></i> Statistik Jumlah Peserta Munaqosah per Tahun Ajaran
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Menampilkan jumlah peserta munaqosah per tahun ajaran dan per TPQ">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Informasi:</strong> Tabel ini menampilkan jumlah peserta munaqosah yang terdaftar per tahun ajaran. Klik pada baris tahun ajaran untuk melihat detail per TPQ.
                                        </p>
                                        <div class="table-responsive">
                                            <table id="tabelStatistikPesertaPerTahun" class="table table-bordered table-striped table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 50px;">No</th>
                                                        <th style="width: 50px;"></th>
                                                        <th>Tahun Ajaran</th>
                                                        <th>Nama TPQ</th>
                                                        <th>Kelurahan/Desa</th>
                                                        <th class="text-center">Jumlah Peserta</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($statistikGroupedByTahun) && count($statistikGroupedByTahun) > 0): ?>
                                                        <?php $no = 1; ?>
                                                        <?php foreach ($statistikGroupedByTahun as $tahunData): ?>
                                                            <?php
                                                            $tahunAjaranKey = md5($tahunData['IdTahunAjaran']); // Unique key untuk tahun ajaran
                                                            $hasDetail = !empty($tahunData['detail_tpq']) && count($tahunData['detail_tpq']) > 0;
                                                            ?>
                                                            <!-- Row Tahun Ajaran (Parent) -->
                                                            <tr class="tahun-row" data-tahun-key="<?= $tahunAjaranKey ?>" style="cursor: pointer; background-color: #f8f9fa;">
                                                                <td class="text-center"><?= $no++ ?></td>
                                                                <td class="text-center">
                                                                    <?php if ($hasDetail): ?>
                                                                        <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                                                                    <?php else: ?>
                                                                        <i class="fas fa-minus" style="color: #ccc;"></i>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <strong><?= convertTahunAjaran($tahunData['IdTahunAjaran']) ?></strong>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-info">Total Semua TPQ</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-secondary">-</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary badge-lg" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                                        <?= number_format($tahunData['total_peserta']) ?>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <!-- Detail TPQ (Child rows - hidden by default) -->
                                                            <?php if ($hasDetail): ?>
                                                                <?php foreach ($tahunData['detail_tpq'] as $detail): ?>
                                                                    <tr class="detail-row detail-<?= $tahunAjaranKey ?>" style="display: none; background-color: #ffffff;">
                                                                        <td></td>
                                                            <td class="text-center">
                                                                            <i class="fas fa-angle-right text-muted"></i>
                                                                        </td>
                                                                        <td style="padding-left: 40px;">
                                                                            <span class="text-muted small"><i class="fas fa-building text-info mr-1"></i>TPQ</span>
                                                                        </td>
                                                                        <td>
                                                                            <strong><?= esc($detail['NamaTpq'] ?? '-') ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <?= esc($detail['KelurahanDesa'] ?? '-') ?>
                                                            </td>
                                                            <td class="text-center">
                                                                            <span class="badge badge-secondary" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                                                                <?= number_format($detail['jumlah_peserta']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted py-4">
                                                                <i class="fas fa-info-circle"></i> Tidak ada data statistik
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                                <?php if (!empty($statistikGroupedByTahun) && count($statistikGroupedByTahun) > 0): ?>
                                                    <tfoot class="thead-light">
                                                        <tr>
                                                            <th colspan="5" class="text-right">Grand Total:</th>
                                                            <th class="text-center">
                                                                <span class="badge badge-success badge-lg" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                                    <?= number_format(array_sum(array_column($statistikGroupedByTahun, 'total_peserta'))) ?>
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                <?php endif; ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Konfigurasi & Setup -->
                                            <div class="col-md-6 mb-4">
                                                <h5><i class="fas fa-cog"></i> Konfigurasi & Setup</h5>
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['kategori_materi'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tags"></i> Kategori Materi
                                                    </a>
                                                    <a href="<?= $menu_items['kategori_kesalahan'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-exclamation-triangle"></i> Kategori Kesalahan
                                                    </a>
                                                    <a href="<?= $menu_items['grup_materi_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-layer-group"></i> Grup Materi Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['materi_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-book"></i> Materi Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['bobot_nilai'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-percentage"></i> Bobot Nilai
                                                    </a>
                                                    <a href="<?= $menu_items['konfigurasi'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-sliders-h"></i> Konfigurasi
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Data & Monitoring -->
                                            <div class="col-md-6 mb-4">
                                                <h5><i class="fas fa-chart-bar"></i> Data & Monitoring</h5>
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tachometer-alt"></i> Dashboard Monitoring
                                                    </a>
                                                    <a href="<?= $menu_items['monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-eye"></i> Monitoring Munaqosah
                                                    </a>
                                                    <a href="<?= $menu_items['cek_nilai_pasangan_juri'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-check"></i> Cek Nilai Pasangan Juri
                                                    </a>
                                                    <a href="<?= $menu_items['kelulusan'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-certificate"></i> Nilai Munaqosah
                                                    </a>
                                                    <a href="<?= $menu_items['kelulusan_simple'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-check-circle"></i> Kelulusan
                                                    </a>
                                                    <a href="<?= $menu_items['export_hasil_munaqosah'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-file-export"></i> Export Hasil Munaqosah
                                                    </a>
                                                    <a href="<?= $menu_items['data_juri'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-tie"></i> Data Juri
                                                    </a>
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-users"></i> Daftar Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-plus"></i> Registrasi Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['jadwal_peserta_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-calendar-alt"></i> Jadwal Peserta Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['antrian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-list"></i> Antrian Ujian
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .toggle-switch-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 100px;
        height: 40px;
        cursor: pointer;
    }

    .toggle-switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #dc3545;
        transition: 0.3s;
        border-radius: 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 10px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch-slider:before {
        position: absolute;
        content: "";
        height: 32px;
        width: 32px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .toggle-switch-input:checked+.toggle-switch-slider {
        background-color: #28a745;
    }

    .toggle-switch-input:checked+.toggle-switch-slider:before {
        transform: translateX(60px);
    }

    .toggle-switch-input:disabled+.toggle-switch-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .toggle-switch-label-on,
    .toggle-switch-label-off {
        font-size: 0.75rem;
        font-weight: bold;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        z-index: 1;
        transition: opacity 0.3s;
    }

    .toggle-switch-label-on {
        opacity: 0;
    }

    .toggle-switch-label-off {
        opacity: 1;
    }

    .toggle-switch-input:checked+.toggle-switch-slider .toggle-switch-label-on {
        opacity: 1;
    }

    .toggle-switch-input:checked+.toggle-switch-slider .toggle-switch-label-off {
        opacity: 0;
    }

    .toggle-progress-indicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        padding: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Styling untuk tabel statistik per tahun ajaran */
    .tahun-row {
        transition: background-color 0.2s ease;
        user-select: none;
    }

    .tahun-row:hover {
        background-color: #e9ecef !important;
    }

    .tahun-row.expanded {
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

    .tahun-row:hover .expand-icon {
        color: #0056b3;
    }

    /* Styling untuk badge di detail row */
    .detail-row .badge {
        font-weight: 500;
    }

    /* Border untuk memisahkan tahun ajaran */
    .tahun-row:not(:first-child) {
        border-top: 2px solid #dee2e6;
    }
</style>
<script>
    $(document).ready(function() {
        $('#toggleAktiveTombolKelulusan').on('change', function() {
            const isChecked = $(this).is(':checked');
            const idTpq = $(this).data('id-tpq');
            const toggle = $(this);
            const progressIndicator = $('#toggleProgressIndicator');

            // Disable toggle dan tampilkan progress indicator
            toggle.prop('disabled', true);
            progressIndicator.show();

            $.ajax({
                url: '<?= base_url('backend/munaqosah/toggle-aktive-tombol-kelulusan') ?>',
                type: 'POST',
                data: {
                    IdTpq: idTpq,
                    value: isChecked
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Setting berhasil diupdate',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Revert toggle state
                        toggle.prop('checked', !isChecked);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal mengupdate setting'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Revert toggle state
                    toggle.prop('checked', !isChecked);
                    let errorMessage = 'Terjadi kesalahan saat mengupdate setting';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Endpoint tidak ditemukan. Pastikan route sudah dikonfigurasi dengan benar.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function() {
                    // Re-enable toggle dan sembunyikan progress indicator
                    toggle.prop('disabled', false);
                    progressIndicator.hide();
                }
            });
        });

        // Script untuk expand/collapse rows statistik per tahun ajaran
        $(document).on('click', '.tahun-row', function() {
            var tahunKey = $(this).data('tahun-key');
            var detailRows = $('.detail-' + tahunKey);
            var expandIcon = $(this).find('.expand-icon');

            if (detailRows.length > 0) {
                if (detailRows.is(':visible')) {
                    // Collapse
                    detailRows.slideUp(300);
                    expandIcon.css('transform', 'rotate(0deg)');
                    $(this).removeClass('expanded');
                } else {
                    // Expand
                    detailRows.slideDown(300);
                    expandIcon.css('transform', 'rotate(90deg)');
                    $(this).addClass('expanded');
                }
            }
        });

        // Hover effect untuk tahun-row
        $(document).on('mouseenter', '.tahun-row', function() {
            if ($('.detail-' + $(this).data('tahun-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f0f0f0');
            }
        }).on('mouseleave', '.tahun-row', function() {
            if ($('.detail-' + $(this).data('tahun-key')).is(':visible')) {
                $(this).css('background-color', '#e9ecef');
            } else {
                $(this).css('background-color', '#f8f9fa');
            }
        });
    });
</script>
<?= $this->endSection(); ?>