<?php
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
$peserta = $peserta ?? [];
$aktiveTombolKelulusan = $aktiveTombolKelulusan ?? true;
$aktiveTombolKelulusanPerType = $aktiveTombolKelulusanPerType ?? [];
$availableTypeUjian = $availableTypeUjian ?? ['munaqosah'];
$hasMultipleTypeUjian = count($availableTypeUjian) > 1;
$statusVerifikasi = $statusVerifikasi ?? null;
$isVerified = ($statusVerifikasi === 'valid' || $statusVerifikasi === 'dikonfirmasi');
$isPerluPerbaikan = ($statusVerifikasi === 'perlu_perbaikan');

// Tentukan default type ujian untuk inisialisasi
$defaultTypeUjian = !empty($availableTypeUjian) ? $availableTypeUjian[0] : 'munaqosah';
// Tentukan status tombol kelulusan untuk default type ujian
$aktiveTombolKelulusanDefault = $aktiveTombolKelulusanPerType[$defaultTypeUjian] ?? false;
?>

<?= $this->extend($templatePath); ?>
<?= $this->section('content'); ?>

<style>
    body {
        background-color: #f5f5f5;
    }

    .confirmation-card {
        max-width: 700px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .title-container {
        position: relative;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 3px solid #4caf50;
    }

    .title-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .green-line-vertical {
        width: 5px;
        height: 40px;
        background: linear-gradient(180deg, #4caf50 0%, #45a049 100%);
        border-radius: 3px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .card-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.3;
        text-transform: none;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 24px;
        }

        .green-line-vertical {
            height: 35px;
            width: 4px;
        }
    }

    .data-section {
        margin-bottom: 25px;
        padding: 15px 15px 15px 20px;
        background-color: #f9f9f9;
        border-radius: 6px;
        border-left: 5px solid #2196F3;
        position: relative;
    }

    .data-section::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15px;
        width: 5px;
        height: 20px;
        background: linear-gradient(180deg, #2196F3 0%, #1976D2 100%);
        border-radius: 0 3px 3px 0;
    }

    .data-label {
        font-weight: 600;
        color: #555;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .data-value {
        font-size: 16px;
        color: #000;
        font-weight: 500;
    }

    .info-box {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin: 25px 0;
        border-radius: 4px;
    }

    .info-box p {
        margin: 0;
        color: #856404;
        font-size: 14px;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 25px 0;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 6px;
    }

    .checkbox-wrapper input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-wrapper input[type="checkbox"]:disabled {
        cursor: not-allowed;
        opacity: 0.6;
        background-color: #e9ecef;
    }

    .checkbox-wrapper label {
        cursor: pointer;
        font-size: 14px;
        color: #333;
        line-height: 1.5;
    }

    .checkbox-wrapper label.disabled-label {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn-action {
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-status {
        background-color: #2196F3;
        color: white;
    }

    .btn-status:hover {
        background-color: #1976D2;
    }

    .btn-kelulusan {
        background-color: #4caf50;
        color: white;
    }

    .btn-kelulusan:hover {
        background-color: #45a049;
    }

    .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-action.locked {
        opacity: 0.8;
        cursor: not-allowed;
        position: relative;
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .btn-action.locked:hover {
        background-color: #c82333 !important;
        border-color: #bd2130 !important;
    }

    .btn-action.locked::after {
        content: '\f023';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .info-disabled {
        margin-top: 15px;
        padding: 12px;
        background-color: #e3f2fd;
        border-left: 4px solid #2196F3;
        border-radius: 4px;
        font-size: 14px;
        color: #1565C0;
        text-align: center;
    }

    .info-disabled i {
        font-size: 16px;
        margin-right: 5px;
    }

    .status-box {
        margin: 20px 0;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid;
    }

    .status-box.warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }

    .status-box.info {
        background-color: #d1ecf1;
        border-left-color: #17a2b8;
        color: #0c5460;
    }

    .status-box.success {
        background-color: #d4edda;
        border-left-color: #28a745;
        color: #155724;
    }

    .status-box strong {
        display: block;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .status-box p {
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
    }
</style>

<div class="confirmation-card">
    <div class="title-container">
        <div class="title-wrapper">
            <div class="green-line-vertical"></div>
            <h2 class="card-title">Konfirmasi Data Santri</h2>
        </div>
    </div>

    <div class="data-section">
        <div class="data-label">Nama Santri:</div>
        <div class="data-value"><?= esc($peserta['NamaSantri'] ?? '-') ?></div>
    </div>

    <div class="data-section">
        <div class="data-label">Jenis Kelamin:</div>
        <div class="data-value"><?= esc($peserta['JenisKelamin'] ?? '-') ?></div>
    </div>

    <div class="data-section">
        <div class="data-label">Tempat, Tanggal Lahir:</div>
        <div class="data-value">
            <?= esc($peserta['TempatLahirSantri'] ?? '-') ?>,
            <?= !empty($peserta['TanggalLahirSantri']) ? formatTanggalIndonesia($peserta['TanggalLahirSantri'], 'd F Y') : '-' ?>
        </div>
    </div>

    <div class="data-section">
        <div class="data-label">Nama Ayah:</div>
        <div class="data-value"><?= esc($peserta['NamaAyah'] ?? '-') ?></div>
    </div>

    <div class="data-section">
        <div class="data-label">Nama TPQ:</div>
        <div class="data-value"><?= esc($peserta['NamaTpq'] ?? '-') ?></div>
    </div>

    <?php if ($isPerluPerbaikan): ?>
        <div class="status-box warning">
            <strong><i class="fas fa-exclamation-triangle"></i> Status Verifikasi: Perlu Perbaikan</strong>
            <p>
                Data Ananda sedang dalam proses verifikasi perbaikan. Admin/Operator sedang meninjau permintaan perbaikan data yang telah Ananda ajukan.
                Tombol "Lihat Kelulusan" akan aktif setelah data selesai diverifikasi dan dikonfirmasi oleh admin.
            </p>
        </div>
    <?php elseif ($isVerified): ?>
        <div class="status-box success">
            <strong><i class="fas fa-check-circle"></i> Status Verifikasi: Data Valid</strong>
            <p>
                Data Ananda telah diverifikasi dan dikonfirmasi. Ananda dapat melihat status munaqosah dan hasil kelulusan ujian.
            </p>
        </div>
    <?php else: ?>
        <div class="info-box">
            <p>
                <strong>Informasi Penting:</strong><br>
                Pastikan data di atas sudah benar. Data ini akan digunakan untuk menampilkan status munaqosah dan hasil kelulusan ujian Ananda.
                Jika data di atas tidak benar, silakan centang checkbox di bawah dan pilih "Tidak, Perlu Perbaikan" untuk mengajukan permintaan perbaikan data.
            </p>
        </div>
    <?php endif; ?>

    <!-- Type Ujian akan dipilih melalui popup ketika tombol ditekan -->
    <!-- Hidden input untuk menyimpan default type ujian (jika hanya satu) -->
    <input type="hidden" name="typeUjian" value="<?= esc($availableTypeUjian[0] ?? 'munaqosah') ?>" id="typeUjianHidden">

    <form id="confirmationForm">
        <?php if (!$isVerified && !$isPerluPerbaikan): ?>
            <!-- Checkbox hanya ditampilkan jika belum ada status verifikasi (belum verified dan tidak perlu perbaikan) -->
            <div class="checkbox-wrapper">
                <input type="checkbox" id="confirmed" name="confirmed" required>
                <label for="confirmed">
                    Saya menyetujui bahwa data di atas sudah benar. Dengan menyetujui, saya akan dapat melihat status munaqosah dan hasil kelulusan ujian setelah data diverifikasi. Informasi tersebut hanya dapat dilihat oleh Ananda sendiri.
                </label>
            </div>
        <?php endif; ?>
        <!-- Jika status sudah ada (valid atau perlu_perbaikan), checkbox tidak ditampilkan karena status sudah ditampilkan di status-box di atas -->

        <div class="button-group">
            <!-- Tombol Status selalu aktif (tidak bergantung pada status verified) -->
            <button type="button" class="btn-action btn-status" onclick="processAction('status')" id="btnStatus">
                <i class="fas fa-tasks"></i> Lihat Status Munaqosah
            </button>
            <!-- Tombol Kelulusan bergantung pada status verified dan aktiveTombolKelulusan per type ujian -->
            <button
                type="button"
                class="btn-action btn-kelulusan"
                onclick="processAction('kelulusan')"
                id="btnKelulusan"
                data-aktive-per-type='<?= json_encode($aktiveTombolKelulusanPerType) ?>'>
                <i class="fas fa-graduation-cap"></i> Lihat Kelulusan
            </button>
        </div>

        <!-- Info untuk tombol kelulusan yang dinamis -->
        <div class="info-disabled" id="infoKelulusan" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <span>Tombol "Lihat Kelulusan" saat ini tidak aktif berdasarkan pengaturan sistem untuk type ujian yang dipilih. Silakan hubungi admin lembaga untuk informasi lebih lanjut.</span>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        const isVerified = <?= $isVerified ? 'true' : 'false' ?>;
        const isPerluPerbaikan = <?= $isPerluPerbaikan ? 'true' : 'false' ?>;
        const aktiveTombolKelulusanPerType = <?= json_encode($aktiveTombolKelulusanPerType) ?>;
        const defaultTypeUjian = '<?= esc($defaultTypeUjian, 'js') ?>';

        // Function untuk update status tombol kelulusan berdasarkan type ujian default
        // (Untuk multiple type ujian, status akan dicek saat popup muncul)
        function updateTombolKelulusan() {
            const availableTypeUjian = <?= json_encode($availableTypeUjian) ?>;

            // Ambil type ujian default dari hidden input
            const hiddenInput = $('#typeUjianHidden');
            const selectedTypeUjian = hiddenInput.length > 0 ? hiddenInput.val() : defaultTypeUjian;

            // Cek status aktiveTombolKelulusan untuk type ujian default
            const aktiveTombolKelulusan = aktiveTombolKelulusanPerType[selectedTypeUjian] ?? false;

            // Update tombol kelulusan
            const btnKelulusan = $('#btnKelulusan');
            const infoKelulusan = $('#infoKelulusan');

            // Jika ada multiple type ujian, tombol kelulusan akan dicek saat popup muncul
            // Tapi kita tetap perlu cek untuk type ujian default
            const hasMultiple = <?= $hasMultipleTypeUjian ? 'true' : 'false' ?>;

            if (hasMultiple) {
                // Jika ada multiple type ujian, cek apakah setidaknya ada satu type ujian yang aktif
                // Jika ada yang aktif dan status verified, enable tombol (nanti dicek lagi di popup)
                let hasActiveType = false;
                availableTypeUjian.forEach(function(type) {
                    if (aktiveTombolKelulusanPerType[type] === true) {
                        hasActiveType = true;
                    }
                });

                if (isVerified && hasActiveType) {
                    // Setidaknya ada satu type ujian yang aktif, enable tombol
                    btnKelulusan.prop('disabled', false);
                    btnKelulusan.removeClass('locked');
                    btnKelulusan.css('pointer-events', 'auto');
                    infoKelulusan.hide();
                } else if (isVerified && !hasActiveType) {
                    // Semua type ujian tidak aktif
                    btnKelulusan.prop('disabled', true);
                    btnKelulusan.addClass('locked');
                    btnKelulusan.css('pointer-events', 'none');
                    infoKelulusan.show();
                } else {
                    // Status belum verified
                    btnKelulusan.prop('disabled', true);
                    btnKelulusan.removeClass('locked');
                    btnKelulusan.css('pointer-events', 'auto');
                    infoKelulusan.hide();
                }
            } else {
                // Jika hanya satu type ujian, cek seperti biasa
                if (isVerified && aktiveTombolKelulusan) {
                    btnKelulusan.prop('disabled', false);
                    btnKelulusan.removeClass('locked');
                    btnKelulusan.css('pointer-events', 'auto');
                    infoKelulusan.hide();
                } else if (isVerified && !aktiveTombolKelulusan) {
                    btnKelulusan.prop('disabled', true);
                    btnKelulusan.addClass('locked');
                    btnKelulusan.css('pointer-events', 'none');
                    infoKelulusan.show();
                } else {
                    btnKelulusan.prop('disabled', true);
                    btnKelulusan.removeClass('locked');
                    btnKelulusan.css('pointer-events', 'auto');
                    infoKelulusan.hide();
                }
            }
        }

        // Inisialisasi tombol status (selalu aktif)
        $('#btnStatus').prop('disabled', false);

        // Inisialisasi tombol kelulusan
        updateTombolKelulusan();

        // Event handler hanya jika checkbox ada (belum verified dan tidak perlu perbaikan)
        // Jika status sudah valid, checkbox tidak ditampilkan, jadi tidak perlu event handler
        if (!isVerified && !isPerluPerbaikan) {
            // Pastikan checkbox ada sebelum menambahkan event handler
            if ($('#confirmed').length > 0) {
                $('#confirmed').on('change', function() {
                    const isChecked = $(this).is(':checked');

                    if (isChecked) {
                        // Tampilkan popup konfirmasi
                        showVerifikasiPopup();
                    } else {
                        // Jika unchecked, update tombol kelulusan (tombol status tetap aktif)
                        updateTombolKelulusan();
                    }
                });
            }
        }
    });

    function showVerifikasiPopup() {
        // Data santri untuk ditampilkan (format sama dengan yang ditampilkan di halaman)
        const dataSantri = {
            nama: '<?= esc($peserta['NamaSantri'] ?? '-', 'js') ?>',
            jenisKelamin: '<?= esc($peserta['JenisKelamin'] ?? '-', 'js') ?>',
            tempatLahir: '<?= esc($peserta['TempatLahirSantri'] ?? '-', 'js') ?>',
            tanggalLahir: '<?php
                            if (!empty($peserta['TanggalLahirSantri'])) {
                                if (function_exists('formatTanggalIndonesia')) {
                                    echo esc(formatTanggalIndonesia($peserta['TanggalLahirSantri'], 'd F Y'), 'js');
                                } else {
                                    echo esc(date('d F Y', strtotime($peserta['TanggalLahirSantri'])), 'js');
                                }
                            } else {
                                echo '-';
                            }
                            ?>',
            namaAyah: '<?= esc($peserta['NamaAyah'] ?? '-', 'js') ?>',
            namaTpq: '<?= esc($peserta['NamaTpq'] ?? '-', 'js') ?>'
        };

        Swal.fire({
            title: 'Verifikasi Data',
            html: `
                <div class="text-left">
                    <p class="mb-3"><strong>Apakah data santri berikut sudah benar?</strong></p>
                    
                    <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; margin-bottom: 15px;">
                        <table style="width: 100%; font-size: 14px;">
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; width: 35%; color: #495057;">Nama Santri:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.nama}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; color: #495057;">Jenis Kelamin:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.jenisKelamin}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; color: #495057;">Tempat Lahir:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.tempatLahir}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; color: #495057;">Tanggal Lahir:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.tanggalLahir}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; color: #495057;">Nama Ayah:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.namaAyah}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; font-weight: 600; color: #495057;">Nama TPQ:</td>
                                <td style="padding: 8px 0; color: #212529;">${dataSantri.namaTpq}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="alert alert-info" style="margin-bottom: 0;">
                        <i class="fas fa-info-circle"></i> Pastikan semua data sudah sesuai sebelum melanjutkan.
                    </div>
                </div>
            `,
            icon: 'question',
            width: '600px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Data Benar',
            cancelButtonText: '<i class="fas fa-times"></i> Tidak, Perlu Perbaikan',
            confirmButtonColor: '#4caf50',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Data benar - update status menjadi 'valid'
                verifikasiData('valid', null);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Data perlu perbaikan - tampilkan form
                showFormPerbaikan();
            } else {
                // User menutup popup, uncheck checkbox
                $('#confirmed').prop('checked', false);
            }
        });
    }

    function showFormPerbaikan() {
        // Data santri saat ini
        const dataSaatIni = {
            nama: '<?= esc($peserta['NamaSantri'] ?? '', 'js') ?>',
            jenisKelamin: '<?= esc($peserta['JenisKelamin'] ?? '', 'js') ?>',
            tempatLahir: '<?= esc($peserta['TempatLahirSantri'] ?? '', 'js') ?>',
            tanggalLahir: '<?= !empty($peserta['TanggalLahirSantri']) ? date('Y-m-d', strtotime($peserta['TanggalLahirSantri'])) : '' ?>',
            tanggalLahirDisplay: '<?= !empty($peserta['TanggalLahirSantri']) ? date('d-m-Y', strtotime($peserta['TanggalLahirSantri'])) : '' ?>',
            namaAyah: '<?= esc($peserta['NamaAyah'] ?? '', 'js') ?>'
        };

        Swal.fire({
            title: 'Form Perbaikan Data',
            html: `
                <div class="text-left" style="max-width: 100%;">
                    <p class="mb-3">Silakan isi data yang perlu diperbaiki pada kolom "Perbaikan Data":</p>
                    <style>
                        .table-perbaikan {
                            width: 100%;
                            margin-bottom: 0;
                            font-size: 14px;
                            border-collapse: collapse;
                        }
                        .table-perbaikan thead th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            text-align: left;
                            padding: 10px;
                            border: 1px solid #dee2e6;
                        }
                        .table-perbaikan tbody td {
                            padding: 10px;
                            border: 1px solid #dee2e6;
                            vertical-align: middle;
                        }
                        .table-perbaikan tbody td:first-child {
                            font-weight: 600;
                            background-color: #f8f9fa;
                            width: 25%;
                        }
                        .table-perbaikan tbody td:nth-child(2) {
                            background-color: #fff3cd;
                            width: 35%;
                            color: #856404;
                        }
                        .table-perbaikan tbody td:nth-child(3) {
                            background-color: #ffffff;
                            width: 40%;
                        }
                        .table-perbaikan input[type="text"],
                        .table-perbaikan input[type="date"],
                        .table-perbaikan select {
                            width: 100%;
                            padding: 6px 10px;
                            font-size: 14px;
                            border: 1px solid #ced4da;
                            border-radius: 4px;
                            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                        }
                        .table-perbaikan input[type="text"]:focus,
                        .table-perbaikan input[type="date"]:focus,
                        .table-perbaikan select:focus {
                            border-color: #80bdff;
                            outline: 0;
                            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                        }
                    </style>
                    <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                        <table class="table-perbaikan">
                            <thead>
                                <tr>
                                    <th>Data Santri</th>
                                    <th>Saat ini (Sebelum)</th>
                                    <th>Perbaikan Data (Sesudah)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td>${dataSaatIni.nama || '-'}</td>
                                    <td>
                                        <input type="text" id="perbaikanNama" 
                                            placeholder="Masukkan nama yang benar (kosongkan jika tidak diubah)">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>${dataSaatIni.jenisKelamin || '-'}</td>
                                    <td>
                                        <select id="perbaikanJenisKelamin" style="width: 100%; padding: 6px 10px;">
                                            <option value="">-- Pilih Jenis Kelamin (kosongkan jika tidak diubah) --</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tempat Lahir</td>
                                    <td>${dataSaatIni.tempatLahir || '-'}</td>
                                    <td>
                                        <input type="text" id="perbaikanTempatLahir" 
                                            placeholder="Masukkan tempat lahir yang benar (kosongkan jika tidak diubah)">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td>${dataSaatIni.tanggalLahirDisplay || '-'}</td>
                                    <td>
                                        <input type="date" id="perbaikanTanggalLahir" 
                                            placeholder="Pilih tanggal lahir yang benar">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nama Ayah</td>
                                    <td>${dataSaatIni.namaAyah || '-'}</td>
                                    <td>
                                        <input type="text" id="perbaikanNamaAyah" 
                                            placeholder="Masukkan nama ayah yang benar (kosongkan jika tidak diubah)">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-3" style="margin-bottom: 0;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Catatan:</strong> Hanya isi field yang perlu diperbaiki. Field yang kosong akan diabaikan.
                    </div>
                </div>
            `,
            icon: 'warning',
            width: '800px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Kirim Permintaan',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                // Focus pada input pertama yang kosong
                setTimeout(() => {
                    $('#perbaikanNama').focus();
                }, 300);

                // Fungsi untuk mengubah text menjadi title case (huruf pertama setiap kata besar)
                const toTitleCase = (str) => {
                    if (!str) return '';
                    // Ubah ke lowercase dulu, lalu capitalize setiap kata
                    return str.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
                };

                // Event listener untuk auto title case pada input text
                // Menggunakan event 'input' untuk real-time conversion
                $('#perbaikanNama, #perbaikanTempatLahir, #perbaikanNamaAyah').on('input', function() {
                    const input = this;
                    const cursorPosition = input.selectionStart;
                    const originalValue = input.value;

                    // Hanya proses jika ada nilai
                    if (originalValue && originalValue.length > 0) {
                        // Konversi ke title case
                        const titleCaseValue = toTitleCase(originalValue);

                        // Update value jika berbeda
                        if (originalValue !== titleCaseValue) {
                            // Simpan panjang sebelum cursor
                            const beforeCursor = originalValue.substring(0, cursorPosition);
                            const beforeCursorTitleCase = toTitleCase(beforeCursor);

                            // Update value
                            input.value = titleCaseValue;

                            // Kembalikan posisi cursor berdasarkan panjang string sebelum cursor
                            const newCursorPosition = beforeCursorTitleCase.length;

                            // Set cursor position dengan setTimeout untuk memastikan DOM sudah update
                            setTimeout(() => {
                                if (newCursorPosition <= titleCaseValue.length) {
                                    input.setSelectionRange(newCursorPosition, newCursorPosition);
                                } else {
                                    input.setSelectionRange(titleCaseValue.length, titleCaseValue.length);
                                }
                            }, 0);
                        }
                    }
                });
            },
            preConfirm: () => {
                try {
                    // Ambil nilai dari semua input menggunakan document.getElementById untuk memastikan elemen ditemukan
                    const perbaikanData = {
                        nama: (document.getElementById('perbaikanNama')?.value || '').trim(),
                        jenisKelamin: (document.getElementById('perbaikanJenisKelamin')?.value || '').trim(),
                        tempatLahir: (document.getElementById('perbaikanTempatLahir')?.value || '').trim(),
                        tanggalLahir: (document.getElementById('perbaikanTanggalLahir')?.value || '').trim(),
                        namaAyah: (document.getElementById('perbaikanNamaAyah')?.value || '').trim()
                    };

                    console.log('Data yang diambil dari form:', perbaikanData);

                    // Format data perbaikan untuk dikirim
                    const keteranganParts = [];
                    const perbaikanDataFiltered = {};

                    // Helper function untuk normalize string
                    const normalize = (str) => {
                        if (!str) return '';
                        return str.toString().trim();
                    };

                    // Cek dan filter hanya data yang berbeda (hanya yang diisi dan berbeda)
                    if (perbaikanData.nama && perbaikanData.nama.trim()) {
                        const namaBaru = normalize(perbaikanData.nama);
                        const namaLama = normalize(dataSaatIni.nama);
                        if (namaBaru && namaBaru !== namaLama) {
                            keteranganParts.push(`Nama: "${namaLama || '-'}" → "${namaBaru}"`);
                            perbaikanDataFiltered.nama = namaBaru;
                        }
                    }

                    if (perbaikanData.jenisKelamin && perbaikanData.jenisKelamin.trim()) {
                        const jenisKelaminBaru = normalize(perbaikanData.jenisKelamin);
                        const jenisKelaminLama = normalize(dataSaatIni.jenisKelamin);
                        if (jenisKelaminBaru && jenisKelaminBaru !== jenisKelaminLama) {
                            keteranganParts.push(`Jenis Kelamin: "${jenisKelaminLama || '-'}" → "${jenisKelaminBaru}"`);
                            perbaikanDataFiltered.jenisKelamin = jenisKelaminBaru;
                        }
                    }

                    if (perbaikanData.tempatLahir && perbaikanData.tempatLahir.trim()) {
                        const tempatLahirBaru = normalize(perbaikanData.tempatLahir);
                        const tempatLahirLama = normalize(dataSaatIni.tempatLahir);
                        if (tempatLahirBaru && tempatLahirBaru !== tempatLahirLama) {
                            keteranganParts.push(`Tempat Lahir: "${tempatLahirLama || '-'}" → "${tempatLahirBaru}"`);
                            perbaikanDataFiltered.tempatLahir = tempatLahirBaru;
                        }
                    }

                    if (perbaikanData.tanggalLahir && perbaikanData.tanggalLahir.trim()) {
                        // Bandingkan dengan format Y-m-d
                        const tanggalLahirBaru = perbaikanData.tanggalLahir.trim();
                        const tanggalLahirLama = normalize(dataSaatIni.tanggalLahir);
                        if (tanggalLahirBaru && tanggalLahirBaru !== tanggalLahirLama) {
                            // Format tanggal untuk display
                            try {
                                const dateObj = new Date(tanggalLahirBaru + 'T00:00:00');
                                if (!isNaN(dateObj.getTime())) {
                                    const day = String(dateObj.getDate()).padStart(2, '0');
                                    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                                    const year = dateObj.getFullYear();
                                    const formattedTanggal = `${day}-${month}-${year}`;

                                    keteranganParts.push(`Tanggal Lahir: "${dataSaatIni.tanggalLahirDisplay || '-'}" → "${formattedTanggal}"`);
                                    perbaikanDataFiltered.tanggalLahir = tanggalLahirBaru;
                                }
                            } catch (e) {
                                // Skip jika format tanggal tidak valid
                            }
                        }
                    }

                    if (perbaikanData.namaAyah && perbaikanData.namaAyah.trim()) {
                        const namaAyahBaru = normalize(perbaikanData.namaAyah);
                        const namaAyahLama = normalize(dataSaatIni.namaAyah);
                        if (namaAyahBaru && namaAyahBaru !== namaAyahLama) {
                            keteranganParts.push(`Nama Ayah: "${namaAyahLama || '-'}" → "${namaAyahBaru}"`);
                            perbaikanDataFiltered.namaAyah = namaAyahBaru;
                        }
                    }

                    if (keteranganParts.length === 0) {
                        Swal.showValidationMessage('Tidak ada perubahan data. Silakan isi data yang berbeda dari data saat ini.');
                        return false;
                    }

                    // Return data yang sudah difilter
                    return {
                        keterangan: keteranganParts.join('; '),
                        perbaikanData: perbaikanDataFiltered,
                        dataSaatIni: dataSaatIni
                    };
                } catch (error) {
                    console.error('Error di preConfirm:', error);
                    Swal.showValidationMessage('Terjadi kesalahan saat memvalidasi data. Silakan coba lagi.');
                    return false;
                }
            }
        }).then((result) => {
            console.log('Form perbaikan result:', result);

            if (result && result.isConfirmed && result.value) {
                try {
                    // Pastikan popup form sudah ditutup sebelum memanggil verifikasiData
                    Swal.close();

                    // Tunggu sebentar agar popup benar-benar tertutup
                    setTimeout(() => {
                        // Kirim permintaan perbaikan dengan data terstruktur
                        verifikasiData('perlu_perbaikan', result.value.keterangan, result.value.perbaikanData);
                    }, 100);
                } catch (error) {
                    console.error('Error saat memproses verifikasi data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memproses data. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#confirmed').prop('checked', false);
                    });
                }
            } else if (result && result.dismiss) {
                // User membatalkan atau menutup popup
                console.log('User membatalkan atau menutup popup');
                $('#confirmed').prop('checked', false);
            } else {
                // Fallback: uncheck checkbox
                $('#confirmed').prop('checked', false);
            }
        }).catch((error) => {
            console.error('Error di form perbaikan:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memproses form. Silakan coba lagi.',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#confirmed').prop('checked', false);
            });
        });
    }

    function verifikasiData(statusVerifikasi, keterangan, perbaikanData = null) {
        console.log('verifikasiData called:', {
            statusVerifikasi: statusVerifikasi,
            keterangan: keterangan,
            perbaikanData: perbaikanData
        });

        try {
            // Siapkan data untuk dikirim
            const dataToSend = {
                status_verifikasi: statusVerifikasi,
                keterangan: keterangan || ''
            };

            // Jika ada data perbaikan, tambahkan ke data yang dikirim
            if (perbaikanData && Object.keys(perbaikanData).length > 0) {
                try {
                    dataToSend.perbaikan_data = JSON.stringify(perbaikanData);
                } catch (e) {
                    console.error('Error stringifying perbaikanData:', e);
                }
            }

            console.log('Mengirim data verifikasi:', {
                statusVerifikasi: statusVerifikasi,
                keterangan: keterangan,
                perbaikanData: perbaikanData,
                dataToSend: dataToSend
            });

            // Tampilkan loading popup
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyimpan verifikasi data',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    // Pastikan loading ditutup
                }
            });

            // Jalankan AJAX request
            $.ajax({
                url: '<?= base_url('munaqosah/verifikasi-data') ?>',
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                timeout: 30000, // 30 detik timeout
                beforeSend: function(xhr) {
                    console.log('AJAX request dimulai...');
                },
                success: function(response) {
                    console.log('AJAX response received:', response);

                    // Tutup loading popup terlebih dahulu
                    Swal.close();

                    if (response && response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil disimpan',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload halaman untuk menampilkan status terbaru
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: (response && response.message) ? response.message : 'Terjadi kesalahan saat memproses verifikasi',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Uncheck checkbox jika gagal
                            $('#confirmed').prop('checked', false);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status,
                        readyState: xhr.readyState
                    });

                    // Tutup loading popup
                    Swal.close();

                    let errorMessage = 'Terjadi kesalahan saat memproses verifikasi. Silakan coba lagi.';

                    // Coba parse error response jika ada
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            // Coba parse response text sebagai JSON
                            const responseText = xhr.responseText;
                            if (responseText.startsWith('{') || responseText.startsWith('[')) {
                                const parsedResponse = JSON.parse(responseText);
                                if (parsedResponse.message) {
                                    errorMessage = parsedResponse.message;
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }

                    // Handle berbagai jenis error
                    if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server. Pastikan koneksi internet Anda stabil.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan di server. Silakan hubungi administrator.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'URL tidak ditemukan. Silakan refresh halaman dan coba lagi.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Akses ditolak. Silakan refresh halaman dan coba lagi.';
                    } else if (status === 'timeout') {
                        errorMessage = 'Request timeout. Silakan coba lagi.';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Uncheck checkbox jika error
                        $('#confirmed').prop('checked', false);
                    });
                },
                complete: function() {
                    console.log('AJAX request selesai');
                }
            });
        } catch (error) {
            console.error('Error di fungsi verifikasiData:', error);
            // Tutup loading jika masih terbuka
            if (Swal.isVisible()) {
                Swal.close();
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memproses verifikasi. Silakan coba lagi.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Uncheck checkbox jika error
                $('#confirmed').prop('checked', false);
            });
        }
    }

    // Function untuk menampilkan popup pilihan type ujian
    function showTypeUjianSelectionPopup(action) {
        const availableTypeUjian = <?= json_encode($availableTypeUjian) ?>;
        const aktiveTombolKelulusanPerType = <?= json_encode($aktiveTombolKelulusanPerType) ?>;
        const isVerified = <?= $isVerified ? 'true' : 'false' ?>;

        // Jika hanya satu type ujian, langsung proses tanpa popup
        if (availableTypeUjian.length === 1) {
            processActionWithTypeUjian(action, availableTypeUjian[0]);
            return;
        }

        // Buat HTML untuk pilihan type ujian
        let htmlContent = '<div class="text-left" style="padding: 10px 0;">';
        htmlContent += '<p class="mb-3" style="font-weight: 600; color: #333;">Silakan pilih type ujian:</p>';
        htmlContent += '<div style="display: flex; flex-direction: column; gap: 12px;">';

        availableTypeUjian.forEach(function(type) {
            const typeLabel = type === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';
            const typeIcon = type === 'pra-munaqosah' ? 'fa-book-reader' : 'fa-graduation-cap';
            const isActive = aktiveTombolKelulusanPerType[type] ?? false;
            const isDisabled = (action === 'kelulusan' && (!isVerified || !isActive));

            htmlContent += '<label style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s; ' +
                (isDisabled ? 'opacity: 0.6; background-color: #f5f5f5;' : 'background-color: #fff;') +
                '" class="type-ujian-option" data-type="' + type + '">';
            htmlContent += '<input type="radio" name="popupTypeUjian" value="' + type + '" ' +
                (isDisabled ? 'disabled' : '') + ' style="width: 18px; height: 18px; cursor: pointer;">';
            htmlContent += '<i class="fas ' + typeIcon + '" style="font-size: 20px; color: #4caf50;"></i>';
            htmlContent += '<div style="flex: 1;">';
            htmlContent += '<div style="font-weight: 600; color: #333; font-size: 16px;">' + typeLabel + '</div>';
            if (action === 'kelulusan' && !isVerified) {
                htmlContent += '<div style="font-size: 12px; color: #dc3545; margin-top: 4px;">Data belum diverifikasi</div>';
            } else if (action === 'kelulusan' && !isActive) {
                htmlContent += '<div style="font-size: 12px; color: #dc3545; margin-top: 4px;">Tidak aktif untuk type ujian ini</div>';
            }
            htmlContent += '</div>';
            htmlContent += '</label>';
        });

        htmlContent += '</div>';
        htmlContent += '</div>';

        // Tampilkan popup
        Swal.fire({
            title: action === 'status' ? 'Pilih Type Ujian untuk Status' : 'Pilih Type Ujian untuk Kelulusan',
            html: htmlContent,
            icon: 'question',
            width: '500px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Pilih',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#4caf50',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: true,
            allowEscapeKey: true,
            didOpen: () => {
                // Event listener untuk radio button
                $('input[name="popupTypeUjian"]').on('change', function() {
                    const selectedType = $(this).val();
                    const optionLabel = $(this).closest('.type-ujian-option');

                    // Update visual selection
                    $('.type-ujian-option').css({
                        'border-color': '#ddd',
                        'background-color': '#fff'
                    });
                    optionLabel.css({
                        'border-color': '#4caf50',
                        'background-color': '#e8f5e9'
                    });
                });

                // Event listener untuk klik pada label
                $('.type-ujian-option').on('click', function() {
                    const radio = $(this).find('input[type="radio"]');
                    if (!radio.prop('disabled')) {
                        radio.prop('checked', true).trigger('change');
                    }
                });

                // Set default selection (pilih yang pertama yang tidak disabled)
                const firstEnabled = $('input[name="popupTypeUjian"]:not(:disabled)').first();
                if (firstEnabled.length > 0) {
                    firstEnabled.prop('checked', true).trigger('change');
                }
            },
            preConfirm: () => {
                const selectedType = $('input[name="popupTypeUjian"]:checked').val();
                if (!selectedType) {
                    Swal.showValidationMessage('Silakan pilih type ujian terlebih dahulu');
                    return false;
                }
                return selectedType;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                processActionWithTypeUjian(action, result.value);
            }
        });
    }

    // Function untuk memproses action dengan type ujian yang dipilih
    function processActionWithTypeUjian(action, typeUjian) {
        const isVerified = <?= $isVerified ? 'true' : 'false' ?>;
        const aktiveTombolKelulusanPerType = <?= json_encode($aktiveTombolKelulusanPerType) ?>;

        // Untuk tombol kelulusan, cek status verified dan aktiveTombolKelulusan
        if (action === 'kelulusan') {
            if (!isVerified) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Data Ananda belum diverifikasi. Tombol kelulusan akan aktif setelah data selesai diverifikasi dan dikonfirmasi oleh admin.'
                });
                return;
            }

            // Cek aktiveTombolKelulusan untuk type ujian yang dipilih
            const aktiveTombolKelulusan = aktiveTombolKelulusanPerType[typeUjian] ?? false;
            if (!aktiveTombolKelulusan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tombol "Lihat Kelulusan" saat ini tidak aktif untuk type ujian yang dipilih berdasarkan pengaturan sistem. Silakan hubungi admin lembaga untuk informasi lebih lanjut.'
                });
                return;
            }
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim request ke backend
        $.ajax({
            url: '<?= base_url('munaqosah/process-konfirmasi') ?>',
            type: 'POST',
            data: {
                typeUjian: typeUjian,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat memproses data.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memproses data. Silakan coba lagi.'
                });
            }
        });
    }

    // Function utama untuk process action (akan menampilkan popup jika ada multiple type ujian)
    function processAction(action) {
        const hasMultipleTypeUjian = <?= $hasMultipleTypeUjian ? 'true' : 'false' ?>;
        const availableTypeUjian = <?= json_encode($availableTypeUjian) ?>;

        // Jika ada multiple type ujian, tampilkan popup
        // Jika hanya satu type ujian, langsung proses
        if (hasMultipleTypeUjian && availableTypeUjian.length > 1) {
            showTypeUjianSelectionPopup(action);
        } else {
            // Langsung proses dengan type ujian default
            const defaultTypeUjian = $('#typeUjianHidden').val() || availableTypeUjian[0] || 'munaqosah';
            processActionWithTypeUjian(action, defaultTypeUjian);
        }
    }
</script>
<?= $this->endSection(); ?>