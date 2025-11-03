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
            linear-gradient(to right, rgba(0,0,0,0.02) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0,0,0,0.02) 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    .status-card {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .form-label {
        font-size: 16px;
        font-weight: 500;
        color: #000;
        min-width: 80px;
    }
    
    .form-input {
        flex: 1;
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
    
    .btn-check:active {
        background-color: #3d8b40;
    }
</style>

<div class="status-card">
    <div class="logo-container">
        <img src="<?= base_url('public/images/no-photo.jpg') ?>" alt="Logo" onerror="this.style.display='none'">
    </div>
    
    <h1 class="title-main">Cek Status Munaqosah</h1>
    <h2 class="title-sub">TPQ Smart System</h2>
    <p class="title-year">Tahun Pelajaran <?= date('Y') . '/' . (date('Y') + 1) ?></p>
    
    <div class="instruction-box">
        <p class="instruction-text">
            <strong>Silakan masukkan HashKey Anda untuk melihat status munaqosah dan hasil kelulusan ujian.</strong>
        </p>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>
    
    <form id="hashKeyForm">
        <div class="form-group-custom">
            <label class="form-label" for="hasKey">HashKey:</label>
            <input 
                type="text" 
                id="hasKey" 
                name="hasKey" 
                class="form-input" 
                placeholder="Contoh: abc123xyz456" 
                required
                autocomplete="off"
            >
            <button type="submit" class="btn-check">Cek</button>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#hashKeyForm').on('submit', function(e) {
            e.preventDefault();
            
            const hasKey = $('#hasKey').val().trim();
            
            if (!hasKey) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'HashKey harus diisi'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '<?= base_url('munaqosah/verify-hashkey') ?>',
                type: 'POST',
                data: {
                    hasKey: hasKey
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'HashKey tidak valid'
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
        });
    });
</script>
<?= $this->endSection(); ?>

