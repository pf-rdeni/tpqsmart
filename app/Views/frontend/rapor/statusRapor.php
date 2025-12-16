<?php
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
?>

<?= $this->extend($templatePath); ?>
<?= $this->section('content'); ?>

<style>
    body {
        background-color: #f5f5f5;
        background-image:
            linear-gradient(to right, rgba(0, 0, 0, 0.02) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0, 0, 0, 0.02) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .status-card {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo-container img {
        max-width: 120px;
        height: auto;
    }

    .title-main {
        font-size: 28px;
        font-weight: bold;
        color: #000;
        text-align: center;
        margin-bottom: 10px;
    }

    .title-sub {
        font-size: 20px;
        font-weight: bold;
        color: #000;
        text-align: center;
        margin-bottom: 5px;
    }

    .title-year {
        font-size: 16px;
        color: #000;
        text-align: center;
        margin-bottom: 30px;
    }

    .instruction-box {
        background-color: #e8f5e9;
        border-left: 4px solid #4caf50;
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 4px;
    }

    .instruction-text {
        color: #2e7d32;
        font-size: 14px;
        margin: 0;
    }

    .form-group-custom {
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 16px;
        font-weight: 500;
        color: #000;
        min-width: 80px;
    }

    .form-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-input::placeholder {
        color: #999;
    }

    .btn-check {
        padding: 10px 30px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-check:hover {
        background-color: #45a049;
    }

    .status-info {
        margin-top: 30px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: bold;
        width: 200px;
        color: #495057;
    }

    .info-value {
        flex: 1;
        color: #212529;
    }

    .badge-status {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }

    .badge-belum {
        background-color: #ffc107;
        color: #000;
    }

    .badge-sudah {
        background-color: #17a2b8;
        color: #fff;
    }

    .badge-kembali {
        background-color: #28a745;
        color: #fff;
    }

    .badge-kembali:hover {
        background-color: #218838;
    }

    .section-header {
        font-size: 18px;
        font-weight: bold;
        color: #495057;
        margin: 0;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
    }

    .section-content {
        padding: 15px 20px 20px 20px;
        border-radius: 0 0 8px 8px;
        margin-bottom: 20px;
    }

    .section-santri {
        background-color: #e3f2fd;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .section-santri .section-header {
        background-color: #bbdefb;
        color: #1565c0;
    }

    .section-serah {
        background-color: #fff3e0;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .section-serah .section-header {
        background-color: #ffe0b2;
        color: #e65100;
    }

    .section-kembali {
        background-color: #f1f8e9;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .section-kembali .section-header {
        background-color: #dcedc8;
        color: #33691e;
    }
</style>

<div class="status-card">
    <div class="logo-container">
        <img src="<?= base_url('public/images/no-photo.jpg') ?>" alt="Logo" onerror="this.style.display='none'">
    </div>

    <h1 class="title-main">Cek Status Serah Terima Rapor</h1>
    <p class="title-year">Tahun Pelajaran <?= date('Y') . '/' . (date('Y') + 1) ?></p>

    <?php if (empty($statusData)): ?>
        <!-- Form Input HasKey -->
        <div class="instruction-box">
            <p class="instruction-text">
                <strong>Silakan masukkan HasKey Anda untuk melihat status serah terima rapor.</strong>
            </p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <form id="hasKeyForm">
            <div class="form-group-custom">
                <div class="row">
                    <div class="col-md-9">
                        <input
                            type="text"
                            id="hasKey"
                            name="hasKey"
                            class="form-input"
                            placeholder="Ketikkan HasKey"
                            required
                            autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-check" style="width: 100%;">Cek</button>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <!-- Tampilkan Status -->
        <div class="status-info">
            <h3 class="mb-4" style="text-align: center; color: #495057;">
                <i class="fas fa-info-circle"></i> Informasi Serah Terima Rapor
            </h3>

            <!-- Bagian Informasi Santri -->
            <div class="section-santri">
                <div class="section-header">
                    <i class="fas fa-user-graduate"></i> Informasi Santri
                </div>
                <div class="section-content">
                    <div class="info-row">
                        <div class="info-label">Nama Santri:</div>
                        <div class="info-value"><?= esc(isset($santri) && !empty($santri) ? $santri['NamaSantri'] : '-') ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Kelas:</div>
                        <div class="info-value"><?= esc($kelas['NamaKelas'] ?? '-') ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">TPQ:</div>
                        <div class="info-value"><?= esc($tpq['NamaTpq'] ?? '-') ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Tahun Ajaran:</div>
                        <div class="info-value"><?= esc(isset($statusData['idTahunAjaran']) && !empty($statusData['idTahunAjaran']) ? convertTahunAjaran($statusData['idTahunAjaran']) : '-') ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Semester:</div>
                        <div class="info-value"><?= esc($statusData['Semester'] ?? '-') ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <?php
                            $status = $statusData['Status'] ?? 'Belum Diserahkan';
                            $badgeClass = 'badge-belum';
                            if ($status === 'Sudah Diserahkan') {
                                $badgeClass = 'badge-sudah';
                            } elseif ($status === 'Sudah Dikembalikan') {
                                $badgeClass = 'badge-kembali';
                            }
                            ?>
                            <span class="badge-status <?= $badgeClass ?>"><?= esc($status) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Diserahkan -->
            <div class="section-serah">
                <div class="section-header">
                    <i class="fas fa-hand-holding"></i> Diserahkan
                </div>
                <div class="section-content">
                    <?php if (!empty($transaksiSerah)): ?>
                        <div class="info-row">
                            <div class="info-label">Tanggal Diserahkan:</div>
                            <div class="info-value">
                                <?= esc(formatTanggalIndonesia($transaksiSerah['TanggalTransaksi'], 'l, d F Y')) ?>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Diserahkan oleh:</div>
                            <div class="info-value"><?= esc(isset($guru) && !empty($guru) && isset($guru['Nama']) ? toTitleCase($guru['Nama']) : '-') ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Diterima oleh:</div>
                            <div class="info-value"><?= esc($transaksiSerah['NamaWaliSantri'] ?? '-') ?></div>
                        </div>

                        <?php if (!empty($transaksiSerah['FotoBukti'])): ?>
                            <div class="info-row">
                                <div class="info-label">Foto Bukti:</div>
                                <div class="info-value">
                                    <a href="<?= base_url('uploads/serah_terima_rapor/' . esc($transaksiSerah['FotoBukti'], 'attr')) ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-image"></i> Lihat Foto Bukti
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="info-row">
                            <div class="info-value" style="color: #6c757d; font-style: italic;">Belum ada data penyerahan</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bagian Dikembalikan -->
            <div class="section-kembali">
                <div class="section-header">
                    <i class="fas fa-undo"></i> Dikembalikan
                </div>
                <div class="section-content">
                    <?php if (!empty($transaksiTerima)): ?>
                        <div class="info-row">
                            <div class="info-label">Tanggal Dikembalikan:</div>
                            <div class="info-value">
                                <?= esc(formatTanggalIndonesia($transaksiTerima['TanggalTransaksi'], 'l, d F Y')) ?>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Dikembalikan oleh:</div>
                            <div class="info-value"><?= esc($transaksiTerima['NamaWaliSantri'] ?? '-') ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Diterima oleh:</div>
                            <div class="info-value"><?= esc(isset($guruTerima) && !empty($guruTerima) && isset($guruTerima['Nama']) ? toTitleCase($guruTerima['Nama']) : (isset($guru) && !empty($guru) && isset($guru['Nama']) ? toTitleCase($guru['Nama']) : '-')) ?></div>
                        </div>

                        <?php if (!empty($transaksiTerima['FotoBukti'])): ?>
                            <div class="info-row">
                                <div class="info-label">Foto Bukti:</div>
                                <div class="info-value">
                                    <a href="<?= base_url('uploads/serah_terima_rapor/' . esc($transaksiTerima['FotoBukti'], 'attr')) ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-image"></i> Lihat Foto Bukti
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="info-row">
                            <div class="info-value" style="color: #6c757d; font-style: italic;">Belum ada data pengembalian</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="<?= base_url('cek-status-rapor') ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Cek Status Lain
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    <?php if (empty($statusData)): ?>
    $('#hasKeyForm').on('submit', function(e) {
        e.preventDefault();

        const hasKey = $('#hasKey').val().trim();

        if (!hasKey) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'HasKey harus diisi'
            });
            return;
        }

        // Redirect ke URL dengan hasKey
        window.location.href = '<?= base_url('cek-status-rapor') ?>/' + hasKey;
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection(); ?>


