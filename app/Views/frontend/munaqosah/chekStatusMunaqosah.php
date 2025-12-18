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
        max-width: 600px;
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

    .btn-check:active {
        background-color: #3d8b40;
    }

    .scan-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e0e0e0;
    }

    .scan-divider {
        text-align: center;
        margin: 20px 0;
        position: relative;
    }

    .scan-divider::before,
    .scan-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 45%;
        height: 1px;
        background-color: #ddd;
    }

    .scan-divider::before {
        left: 0;
    }

    .scan-divider::after {
        right: 0;
    }

    .scan-divider span {
        background-color: white;
        padding: 0 15px;
        color: #666;
        font-size: 14px;
    }

    .btn-scan {
        width: 100%;
        padding: 12px 30px;
        background-color: #2196F3;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-scan:hover {
        background-color: #1976D2;
    }

    .btn-scan:active {
        background-color: #1565C0;
    }

    .btn-scan:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    #qr-reader {
        width: 100%;
        margin: 20px 0;
        border-radius: 8px;
        overflow: hidden;
    }

    #qr-reader__dashboard {
        padding: 15px;
        background-color: #f5f5f5;
        border-radius: 8px;
    }

    .scan-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-stop-scan {
        width: 100%;
        padding: 12px 30px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-stop-scan:hover {
        background-color: #c82333;
    }
</style>

<div class="status-card">
    <div class="logo-container">
        <img src="<?= base_url('public/images/no-photo.jpg') ?>" alt="Logo" onerror="this.style.display='none'">
    </div>

    <h1 class="title-main">Cek Status Munaqosah</h1>
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
            <div class="row">
                <div class="col-md-9">
                    <input
                        type="text"
                        id="hasKey"
                        name="hasKey"
                        class="form-input"
                        placeholder="Ketikkan hashkey atau scan QR code"
                        required
                        autocomplete="off">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn-check" style="width: 100%;">Cek</button>
                </div>
            </div>
        </div>
    </form>

    <div class="scan-section">
        <div class="scan-divider">
            <span>ATAU</span>
        </div>
        <button type="button" class="btn-scan" id="btnScanQR">
            <i class="fas fa-qrcode"></i> Scan QR Code
        </button>
        <div id="qr-reader" style="display: none;"></div>
        <div class="scan-actions" id="scanActions" style="display: none;">
            <button type="button" class="btn-stop-scan" id="btnStopScan">
                <i class="fas fa-stop"></i> Stop Scan
            </button>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Library untuk scan QR Code -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let isScanning = false;

    $(document).ready(function() {
        // Fungsi untuk memproses hashKey (dari input manual atau QR scan)
        function processHashKey(inputValue) {
            let hasKey = inputValue.trim();

            // Jika input adalah URL lengkap, ekstrak hashKey-nya
            if (hasKey.includes('cek-status/')) {
                const urlParts = hasKey.split('cek-status/');
                if (urlParts.length > 1) {
                    hasKey = urlParts[1].split('?')[0].split('#')[0].trim();
                }
            }

            if (!hasKey) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'HashKey tidak ditemukan. Pastikan QR code berisi URL yang valid.'
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
                        // Stop scan jika sedang berjalan
                        if (isScanning) {
                            stopScan();
                        }
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
        }

        // Form submit untuk input manual
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

            processHashKey(hasKey);
        });

        // Tombol scan QR code
        $('#btnScanQR').on('click', function() {
            startScan();
        });

        // Tombol stop scan
        $('#btnStopScan').on('click', function() {
            stopScan();
        });

        // Fungsi untuk memulai scan
        function startScan() {
            if (isScanning) {
                return;
            }

            isScanning = true;
            $('#btnScanQR').prop('disabled', true);
            $('#qr-reader').show();
            $('#scanActions').show();

            html5QrCode = new Html5Qrcode("qr-reader");
            
            html5QrCode.start(
                { facingMode: "environment" }, // Gunakan kamera belakang jika tersedia
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                function(decodedText, decodedResult) {
                    // QR code berhasil di-scan
                    console.log("QR Code berhasil di-scan:", decodedText);
                    processHashKey(decodedText);
                },
                function(errorMessage) {
                    // Error handling (biasanya karena belum ada QR code yang terdeteksi)
                    // Tidak perlu menampilkan error, biarkan terus scan
                }
            ).catch(function(err) {
                // Error saat memulai kamera
                isScanning = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanActions').hide();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.'
                });
            });
        }

        // Fungsi untuk menghentikan scan
        function stopScan() {
            if (!isScanning || !html5QrCode) {
                return;
            }

            html5QrCode.stop().then(function() {
                html5QrCode.clear();
                html5QrCode = null;
                isScanning = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanActions').hide();
            }).catch(function(err) {
                console.error("Error stopping scan:", err);
                isScanning = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanActions').hide();
            });
        }

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            if (isScanning && html5QrCode) {
                stopScan();
            }
        });
    });
</script>
<?= $this->endSection(); ?>