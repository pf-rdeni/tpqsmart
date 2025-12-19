<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Panitia</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Tahun Ajaran & Type Ujian -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0 py-2 d-flex align-items-center">
                                    <i class="fas fa-calendar mr-2" style="font-size: 1.1em;"></i>
                                    <strong>Tahun Ajaran:</strong> <span class="ml-1"><?= esc(convertTahunAjaran($current_tahun_ajaran)) ?></span>
                                    <span class="mx-3">|</span>
                                    <i class="fas fa-graduation-cap mr-2" style="font-size: 1.1em;"></i>
                                    <strong>Type Ujian:</strong> <span class="badge badge-primary ml-1"><?= esc($type_ujian) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle AktiveTombolKelulusan -->
                        <?php 
                        // Untuk typeUjian munaqosah (IdTpq = '0'), hanya Admin yang bisa melihat dan mengubah pengaturan
                        // Untuk pra-munaqosah (IdTpq != '0'), semua panitia bisa melihat dan mengubah
                        $showToggle = true;
                        if (isset($id_tpq_setting) && $id_tpq_setting === '0') {
                            // Untuk munaqosah, hanya Admin yang bisa melihat toggle
                            $showToggle = isset($is_admin) && $is_admin && in_groups('Admin');
                        }
                        ?>
                        <?php if ($showToggle): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-toggle-on"></i> Pengaturan Tombol Kelulusan<?= (isset($id_tpq_setting) && $id_tpq_setting === '0') ? ' (Munaqosah)' : '' ?></h3>
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
                                                               <?= (isset($can_edit_munaqosah_setting) && !$can_edit_munaqosah_setting) ? 'disabled' : '' ?>
                                                               data-id-tpq="<?= esc($id_tpq_setting) ?>"
                                                               <?= !$aktive_tombol_kelulusan_exists ? 'title="Setting belum tersedia. Konfigurasi perlu disetting di akun operator lembaga."' : '' ?>
                                                               <?= (isset($can_edit_munaqosah_setting) && !$can_edit_munaqosah_setting) ? 'title="Hanya Admin yang dapat mengubah pengaturan tombol kelulusan untuk typeUjian munaqosah."' : '' ?>>
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
                                                    <i class="fas fa-info-circle"></i> Setting belum tersedia. Konfigurasi perlu disetting di akun operator lembaga.
                                                </small>
                                            <?php elseif (isset($id_tpq_setting) && $id_tpq_setting === '0'): ?>
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> Toggle ini mengaktifkan/menonaktifkan tombol "Lihat Kelulusan" di halaman konfirmasi data santri untuk typeUjian Munaqosah. <strong>Hanya Admin yang dapat mengubah pengaturan ini.</strong>
                                                </small>
                                            <?php else: ?>
                                                <small class="form-text text-muted mt-2">
                                                    <i class="fas fa-info-circle"></i> Toggle ini mengaktifkan/menonaktifkan tombol "Lihat Kelulusan" di halaman konfirmasi data santri.
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['daftar_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-users text-primary"></i> Daftar Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['registrasi_peserta'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-user-plus text-success"></i> Registrasi Peserta
                                                    </a>
                                                    <a href="<?= $menu_items['jadwal_peserta_ujian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-calendar-alt text-info"></i> Jadwal Peserta Ujian
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="list-group">
                                                    <a href="<?= $menu_items['antrian'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-list text-warning"></i> Antrian Ujian
                                                    </a>
                                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-tachometer-alt text-secondary"></i> Dashboard Monitoring
                                                    </a>
                                                    <a href="<?= $menu_items['monitoring'] ?>" class="list-group-item list-group-item-action">
                                                        <i class="fas fa-eye text-dark"></i> Monitoring Munaqosah
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                    <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="small-box-footer">
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

                        <!-- Tabel Peserta Munaqosah Per TPQ (Hanya untuk Panitia Umum) -->
                        <?php if (isset($is_panitia_umum) && $is_panitia_umum): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card card-info collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-table"></i> Informasi Peserta Munaqosah Per TPQ
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="tabelPesertaPerTpq" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 5%;">No</th>
                                                            <th>Nama TPQ</th>
                                                            <th>Alamat</th>
                                                            <th class="text-center" style="width: 15%;">Laki-Laki</th>
                                                            <th class="text-center" style="width: 15%;">Perempuan</th>
                                                            <th class="text-center" style="width: 15%;">Total Peserta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($peserta_per_tpq)): ?>
                                                            <?php $no = 1; ?>
                                                            <?php foreach ($peserta_per_tpq as $row): ?>
                                                                <tr>
                                                                    <td><?= $no++ ?></td>
                                                                    <td><?= esc($row['NamaTpq']) ?></td>
                                                                    <td><?= esc($row['KelurahanDesa'] ?? '-') ?></td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-primary" style="font-size: 1em; padding: 5px 10px;">
                                                                            <?= number_format($row['jumlah_laki_laki']) ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge badge-pink" style="font-size: 1em; padding: 5px 10px; background-color: #e91e63; color: white;">
                                                                            <?= number_format($row['jumlah_perempuan']) ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong><?= number_format($row['total_peserta']) ?></strong>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            <tr class="bg-light font-weight-bold">
                                                                <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary" style="font-size: 1em; padding: 5px 10px;">
                                                                        <?= number_format(array_sum(array_column($peserta_per_tpq, 'jumlah_laki_laki'))) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-pink" style="font-size: 1em; padding: 5px 10px; background-color: #e91e63; color: white;">
                                                                        <?= number_format(array_sum(array_column($peserta_per_tpq, 'jumlah_perempuan'))) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <strong><?= number_format(array_sum(array_column($peserta_per_tpq, 'total_peserta'))) ?></strong>
                                                                </td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">
                                                                    <i class="fas fa-info-circle"></i> Belum ada data peserta Munaqosah
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Statistik Antrian -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Antrian Selesai</span>
                                        <span class="info-box-number"><?= number_format($antrian_selesai) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Antrian Menunggu</span>
                                        <span class="info-box-number"><?= number_format($antrian_menunggu) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-spinner"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sedang Ujian</span>
                                        <span class="info-box-number"><?= number_format($antrian_proses) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Antrian per Grup Materi -->
                        <?php if (!empty($antrian_data)): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Statistik Antrian per Grup Materi</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Grup Materi</th>
                                                            <th>Total</th>
                                                            <th>Menunggu</th>
                                                            <th>Sedang Ujian</th>
                                                            <th>Selesai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; ?>
                                                        <?php foreach ($antrian_data as $grupId => $data): ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= esc($data['nama']) ?></td>
                                                                <td><span class="badge badge-info"><?= number_format($data['total']) ?></span></td>
                                                                <td><span class="badge badge-warning"><?= number_format($data['menunggu']) ?></span></td>
                                                                <td><span class="badge badge-danger"><?= number_format($data['proses']) ?></span></td>
                                                                <td><span class="badge badge-success"><?= number_format($data['selesai']) ?></span></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
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

    .toggle-switch-input:checked + .toggle-switch-slider {
        background-color: #28a745;
    }

    .toggle-switch-input:checked + .toggle-switch-slider:before {
        transform: translateX(60px);
    }

    .toggle-switch-input:disabled + .toggle-switch-slider {
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

    .toggle-switch-input:checked + .toggle-switch-slider .toggle-switch-label-on {
        opacity: 1;
    }

    .toggle-switch-input:checked + .toggle-switch-slider .toggle-switch-label-off {
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

    // Inisialisasi DataTable untuk tabel peserta per TPQ (hanya untuk panitia umum)
    <?php if (isset($is_panitia_umum) && $is_panitia_umum && !empty($peserta_per_tpq)): ?>
        initializeDataTableUmum('#tabelPesertaPerTpq', true, true, ['copy', 'excel', 'pdf', 'print'], {
            "order": [[1, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": 0 } // Nonaktifkan sorting pada kolom No
            ]
        });
    <?php endif; ?>
});
</script>
<?= $this->endSection(); ?>