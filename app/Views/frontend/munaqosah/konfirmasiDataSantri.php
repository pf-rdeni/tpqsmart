<?php
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
$peserta = $peserta ?? [];
$aktiveTombolKelulusan = $aktiveTombolKelulusan ?? true;
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
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
    
    .checkbox-wrapper label {
        cursor: pointer;
        font-size: 14px;
        color: #333;
        line-height: 1.5;
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
    
    <div class="info-box">
        <p>
            <strong>Informasi Penting:</strong><br>
            Pastikan data di atas sudah benar. Data ini akan digunakan untuk menampilkan status munaqosah dan hasil kelulusan ujian Ananda. Jika data di atas tidak benar silahkan hubungi admin lembaga untuk melakukan perubahan data.
        </p>
    </div>
    
    <form id="confirmationForm">
        <div class="checkbox-wrapper">
            <input type="checkbox" id="confirmed" name="confirmed" required>
            <label for="confirmed">
                Saya menyetujui bahwa data di atas sudah benar dan saya berhak untuk melihat status munaqosah dan hasil kelulusan ujian, informasi tersebut hanya dapat dilihat oleh Ananda sendiri.
            </label>
        </div>
        
        <div class="button-group">
            <button type="button" class="btn-action btn-status" onclick="processAction('status')" disabled id="btnStatus">
                <i class="fas fa-tasks"></i> Lihat Status Munaqosah
            </button>
            <button 
                type="button" 
                class="btn-action btn-kelulusan <?= !$aktiveTombolKelulusan ? 'locked' : '' ?>" 
                onclick="<?= $aktiveTombolKelulusan ? "processAction('kelulusan')" : "return false;" ?>" 
                disabled 
                id="btnKelulusan"
                <?= !$aktiveTombolKelulusan ? 'style="pointer-events: none;"' : '' ?>
            >
                <i class="fas fa-graduation-cap"></i> Lihat Kelulusan
                <?php if (!$aktiveTombolKelulusan): ?>
                    <i class="fas fa-lock" style="margin-left: 8px;"></i>
                <?php endif; ?>
            </button>
        </div>
        
        <?php if (!$aktiveTombolKelulusan): ?>
            <div class="info-disabled">
                <i class="fas fa-info-circle"></i>
                <span>Tombol "Lihat Kelulusan" saat ini tidak aktif berdasarkan pengaturan sistem. Silakan hubungi admin lembaga untuk informasi lebih lanjut.</span>
            </div>
        <?php endif; ?>
    </form>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#confirmed').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('#btnStatus').prop('disabled', !isChecked);
            
            <?php if ($aktiveTombolKelulusan): ?>
                // Jika tombol kelulusan aktif, enable/disable berdasarkan checkbox
                $('#btnKelulusan').prop('disabled', !isChecked);
            <?php else: ?>
                // Jika tombol kelulusan tidak aktif, tetap disabled
                $('#btnKelulusan').prop('disabled', true);
            <?php endif; ?>
        });
        
        // Pastikan tombol kelulusan tetap disabled jika tidak aktif
        <?php if (!$aktiveTombolKelulusan): ?>
            $('#btnKelulusan').prop('disabled', true);
        <?php endif; ?>
    });
    
    function processAction(action) {
        const confirmed = $('#confirmed').is(':checked');
        
        if (!confirmed) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Ananda harus menyetujui informasi penting terlebih dahulu'
            });
            return;
        }
        
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '<?= base_url('munaqosah/process-konfirmasi') ?>',
            type: 'POST',
            data: {
                action: action,
                confirmed: confirmed ? '1' : '0'
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
</script>
<?= $this->endSection(); ?>

