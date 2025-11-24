<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard Munaqosah - Operator</h3>
                    </div>
                    <div class="card-body">
                        <!-- Info TPQ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-building"></i> Informasi TPQ</h5>
                                    <p class="mb-1"><strong>Nama TPQ:</strong> <?= !empty($data_tpq) && isset($data_tpq['NamaTpq']) ? esc($data_tpq['NamaTpq']) : '-' ?></p>
                                    <p class="mb-1"><strong>ID TPQ:</strong> <?= esc($id_tpq ?? '-') ?></p>
                                    <p class="mb-0"><strong>Tahun Ajaran:</strong> <?= esc(convertTahunAjaran($current_tahun_ajaran)) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle AktiveTombolKelulusan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-toggle-on"></i> Pengaturan Tombol Kelulusan</h3>
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
                                                    <i class="fas fa-info-circle"></i> Toggle ini mengaktifkan/menonaktifkan tombol "Lihat Kelulusan" di halaman konfirmasi data santri.
                                                </small>
                                            <?php endif; ?>
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
                                        <p>Juri Aktif</p>
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

                        <!-- Statistik Type Ujian -->
                        <div class="row">
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?= number_format($statistik_munaqosah) ?></h3>
                                        <p>Munaqosah</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= number_format($statistik_pra_munaqosah) ?></h3>
                                        <p>Pra-Munaqosah</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
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

                        <!-- Menu Quick Access -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Access Menu</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['dashboard_monitoring'] ?>" class="btn btn-primary btn-block btn-lg">
                                                    <i class="fas fa-tachometer-alt"></i><br>Dashboard Monitoring
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['monitoring'] ?>" class="btn btn-info btn-block btn-lg">
                                                    <i class="fas fa-eye"></i><br>Monitoring Munaqosah
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['kelulusan'] ?>" class="btn btn-success btn-block btn-lg">
                                                    <i class="fas fa-certificate"></i><br>Kelulusan Ujian
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['konfigurasi'] ?>" class="btn btn-warning btn-block btn-lg">
                                                    <i class="fas fa-sliders-h"></i><br>Konfigurasi
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['data_juri'] ?>" class="btn btn-secondary btn-block btn-lg">
                                                    <i class="fas fa-user-tie"></i><br>Data Juri
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['daftar_peserta'] ?>" class="btn btn-dark btn-block btn-lg">
                                                    <i class="fas fa-users"></i><br>Daftar Peserta
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['registrasi_peserta'] ?>" class="btn btn-success btn-block btn-lg">
                                                    <i class="fas fa-user-plus"></i><br>Registrasi Peserta
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['antrian'] ?>" class="btn btn-info btn-block btn-lg">
                                                    <i class="fas fa-list"></i><br>Antrian Ujian
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <a href="<?= $menu_items['jadwal_peserta_ujian'] ?>" class="btn btn-danger btn-block btn-lg">
                                                    <i class="fas fa-calendar-alt"></i><br>Jadwal Peserta Ujian
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
    });
</script>
<?= $this->endSection(); ?>