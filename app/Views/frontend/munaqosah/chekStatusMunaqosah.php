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

    #qr-reader {
        position: relative;
    }

    #qr-reader video {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .focus-indicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(33, 150, 243, 0.8);
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 1000;
    }

    .focus-indicator.show {
        opacity: 1;
    }

    .scan-hint {
        text-align: center;
        margin-top: 10px;
        font-size: 12px;
        color: #666;
        padding: 8px;
        background-color: #f0f0f0;
        border-radius: 4px;
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
        <div id="qr-reader" style="display: none;">
            <div class="focus-indicator" id="focusIndicator">
                <i class="fas fa-crosshairs"></i> Fokus...
            </div>
        </div>
        <div class="scan-hint" id="scanHint" style="display: none;">
            <i class="fas fa-info-circle"></i> Ketuk dua kali pada layar untuk fokus kamera
        </div>
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
    let isProcessing = false; // Flag untuk mencegah multiple processing

    $(document).ready(function() {
        // Fungsi untuk memproses hashKey (dari input manual atau QR scan)
        function processHashKey(inputValue, fromScan = false) {
            // Mencegah multiple processing
            if (isProcessing) {
                return;
            }

            let hasKey = inputValue.trim();

            // Jika input adalah URL lengkap, ekstrak hashKey-nya
            if (hasKey.includes('cek-status/')) {
                const urlParts = hasKey.split('cek-status/');
                if (urlParts.length > 1) {
                    hasKey = urlParts[1].split('?')[0].split('#')[0].trim();
                }
            }

            // Validasi: Jika hashKey terlalu pendek (3 digit atau kurang), kemungkinan itu QR untuk nomor
            if (hasKey.length <= 3) {
                isProcessing = false;
                Swal.fire({
                    icon: 'warning',
                    title: 'QR Code Salah',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Anda memindai QR Code yang salah!</strong></p>
                            <p style="margin-top: 15px;">QR Code yang Anda pindai adalah untuk <strong>Nomor Peserta</strong> (biasanya 3 digit atau kurang).</p>
                            <p style="margin-top: 15px;"><strong>Instruksi:</strong></p>
                            <ul style="margin-top: 10px; padding-left: 20px;">
                                <li>Jangan pindai QR Code yang berada di <strong>tengah kartu</strong> (untuk nomor)</li>
                                <li>Pindai QR Code yang berada di <strong>samping bawah kiri kartu</strong> (untuk verifikasi data)</li>
                            </ul>
                            <p style="margin-top: 15px; color: #dc3545;"><strong>Silakan scan ulang QR Code yang benar.</strong></p>
                        </div>
                    `,
                    confirmButtonText: 'OK, Scan Ulang',
                    confirmButtonColor: '#2196F3',
                    allowOutsideClick: false
                }).then(() => {
                    // Setelah OK, scan akan dilanjutkan otomatis (tidak perlu restart karena masih scanning)
                    isProcessing = false;
                });
                return;
            }

            if (!hasKey) {
                isProcessing = false;
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'HashKey tidak ditemukan. Pastikan QR code berisi URL yang valid.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    isProcessing = false;
                    if (fromScan) {
                        // Scan akan dilanjutkan otomatis
                    }
                });
                return;
            }

            isProcessing = true;

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
                        isProcessing = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'HashKey Tidak Valid',
                            html: `
                                <div style="text-align: left;">
                                    <p><strong>QR Code yang Anda pindai tidak valid!</strong></p>
                                    <p style="margin-top: 15px;">${response.message || 'HashKey tidak valid atau tidak ditemukan.'}</p>
                                    <p style="margin-top: 15px;"><strong>Pastikan Anda memindai QR Code yang benar:</strong></p>
                                    <ul style="margin-top: 10px; padding-left: 20px;">
                                        <li>QR Code berada di <strong>samping bawah kiri kartu</strong></li>
                                        <li>Bukan QR Code yang di tengah kartu (untuk nomor)</li>
                                    </ul>
                                    <p style="margin-top: 15px; color: #dc3545;"><strong>Silakan scan ulang QR Code yang benar.</strong></p>
                                </div>
                            `,
                            confirmButtonText: 'OK, Scan Ulang',
                            confirmButtonColor: '#2196F3',
                            allowOutsideClick: false
                        }).then(() => {
                            // Setelah OK, scan akan dilanjutkan otomatis
                            if (fromScan && isScanning) {
                                // Scan masih berjalan, tidak perlu restart
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    isProcessing = false;
                    let errorMessage = 'Terjadi kesalahan saat memproses data. Silakan coba lagi.';

                    // Coba ambil pesan error dari response
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    } catch (e) {
                        // Ignore
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `
                            <div style="text-align: left;">
                                <p><strong>Terjadi kesalahan!</strong></p>
                                <p style="margin-top: 15px;">${errorMessage}</p>
                                <p style="margin-top: 15px;"><strong>Silakan coba:</strong></p>
                                <ul style="margin-top: 10px; padding-left: 20px;">
                                    <li>Pastikan QR Code yang dipindai adalah QR Code yang benar (samping bawah kiri kartu)</li>
                                    <li>Scan ulang QR Code</li>
                                    <li>Atau masukkan HashKey secara manual</li>
                                </ul>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#2196F3',
                        allowOutsideClick: false
                    }).then(() => {
                        if (fromScan && isScanning) {
                            // Scan akan dilanjutkan otomatis
                        }
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

            processHashKey(hasKey, false); // false = dari input manual, bukan scan
        });

        // Tombol scan QR code
        $('#btnScanQR').on('click', function() {
            startScan();
        });

        // Tombol stop scan
        $('#btnStopScan').on('click', function() {
            stopScan();
        });

        // Fungsi untuk trigger fokus kamera
        function triggerCameraFocus() {
            if (!html5QrCode || !isScanning) {
                return;
            }

            try {
                // Coba akses video element untuk trigger focus
                const videoElement = document.querySelector('#qr-reader video');
                if (videoElement && videoElement.srcObject) {
                    const stream = videoElement.srcObject;
                    const videoTrack = stream.getVideoTracks()[0];

                    if (videoTrack && typeof videoTrack.getCapabilities === 'function') {
                        const capabilities = videoTrack.getCapabilities();

                        // Cek apakah kamera mendukung focus
                        if (capabilities.focusMode && capabilities.focusMode.includes('continuous')) {
                            // Set focus mode ke continuous untuk auto focus
                            videoTrack.applyConstraints({
                                advanced: [{
                                    focusMode: 'continuous'
                                }]
                            }).then(() => {
                                console.log('Auto focus enabled');
                            }).catch(err => {
                                console.log('Focus constraint not supported:', err);
                            });
                        } else if (capabilities.focusMode && capabilities.focusMode.includes('single-shot')) {
                            // Trigger single-shot focus
                            videoTrack.applyConstraints({
                                advanced: [{
                                    focusMode: 'single-shot'
                                }]
                            }).then(() => {
                                // Kembalikan ke continuous setelah focus
                                setTimeout(() => {
                                    if (capabilities.focusMode.includes('continuous')) {
                                        videoTrack.applyConstraints({
                                            advanced: [{
                                                focusMode: 'continuous'
                                            }]
                                        });
                                    }
                                }, 1000);
                            }).catch(err => {
                                console.log('Focus trigger failed:', err);
                            });
                        }
                    }
                }

                // Tampilkan indicator fokus
                const focusIndicator = $('#focusIndicator');
                focusIndicator.addClass('show');
                setTimeout(() => {
                    focusIndicator.removeClass('show');
                }, 1000);
            } catch (error) {
                console.log('Focus trigger error:', error);
            }
        }

        // Fungsi untuk scroll ke area scan
        function scrollToScanArea() {
            const qrReader = document.getElementById('qr-reader');
            if (qrReader) {
                // Tunggu sedikit agar element sudah ter-render
                setTimeout(() => {
                    // Gunakan scrollIntoView untuk kompatibilitas yang lebih baik, terutama mobile
                    qrReader.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center', // Center element di viewport
                        inline: 'nearest'
                    });

                    // Fallback untuk browser yang tidak support scrollIntoView dengan options
                    // Atau jika perlu fine-tuning posisi
                    setTimeout(() => {
                        const qrReaderRect = qrReader.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        const elementHeight = qrReaderRect.height;
                        const currentTop = qrReaderRect.top;
                        const targetTop = (viewportHeight / 2) - (elementHeight / 2);
                        const scrollOffset = currentTop - targetTop;

                        if (Math.abs(scrollOffset) > 10) { // Hanya scroll jika offset > 10px
                            window.scrollBy({
                                top: scrollOffset,
                                behavior: 'smooth'
                            });
                        }
                    }, 100);
                }, 300); // Delay untuk memastikan element sudah ter-render
            }
        }

        // Fungsi untuk memulai scan
        function startScan() {
            if (isScanning) {
                return;
            }

            isScanning = true;
            isProcessing = false; // Reset processing flag
            $('#btnScanQR').prop('disabled', true);
            $('#qr-reader').show();
            $('#scanHint').show();
            $('#scanActions').show();

            // Auto scroll ke area scan (terutama untuk mobile)
            scrollToScanArea();

            // Clear previous instance jika ada
            if (html5QrCode) {
                html5QrCode.clear();
            }

            html5QrCode = new Html5Qrcode("qr-reader");

            // Konfigurasi kamera dengan auto focus
            const cameraConfig = {
                facingMode: "environment" // Gunakan kamera belakang jika tersedia
            };

            // Video constraints untuk auto focus (jika didukung)
            let videoConstraints = {
                facingMode: "environment"
            };

            // Coba tambahkan focus mode jika didukung
            try {
                // Untuk perangkat yang mendukung, tambahkan focus mode
                if (navigator.mediaDevices && navigator.mediaDevices.getSupportedConstraints) {
                    const supportedConstraints = navigator.mediaDevices.getSupportedConstraints();
                    if (supportedConstraints.focusMode) {
                        videoConstraints.focusMode = "continuous";
                    }
                }
            } catch (e) {
                console.log('Focus mode constraint not available');
            }

            // Konfigurasi scan yang dioptimalkan
            const scanConfig = {
                fps: 15, // Meningkatkan FPS untuk capture lebih cepat
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    // QR box yang lebih besar untuk capture lebih mudah
                    let minEdgePercentage = 0.7; // 70% dari viewfinder
                    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                    return {
                        width: qrboxSize,
                        height: qrboxSize
                    };
                },
                aspectRatio: 1.0, // Square aspect ratio untuk QR code
                disableFlip: false, // Biarkan flip untuk QR code yang terbalik
                videoConstraints: videoConstraints
            };

            html5QrCode.start(
                cameraConfig,
                scanConfig,
                function(decodedText, decodedResult) {
                    // QR code berhasil di-scan
                    console.log("QR Code berhasil di-scan:", decodedText);

                    // Hanya proses jika tidak sedang memproses yang lain
                    if (!isProcessing) {
                        processHashKey(decodedText, true); // true = dari scan
                    }
                },
                function(errorMessage) {
                    // Error handling (biasanya karena belum ada QR code yang terdeteksi)
                    // Tidak perlu menampilkan error, biarkan terus scan
                    // Hanya log untuk debugging
                    if (errorMessage && !errorMessage.includes('No QR code found')) {
                        console.log("Scan error (non-critical):", errorMessage);
                    }
                }
            ).then(() => {
                // Setelah kamera berhasil dimulai, scroll lagi untuk memastikan posisi optimal
                setTimeout(() => {
                    scrollToScanArea();
                }, 800);

                // Setelah kamera berhasil dimulai, coba enable auto focus
                setTimeout(() => {
                    triggerCameraFocus();
                }, 500);

                // Tambahkan event listener untuk double tap pada qr-reader
                const qrReaderElement = document.getElementById('qr-reader');
                if (qrReaderElement) {
                    let lastTap = 0;
                    qrReaderElement.addEventListener('touchend', function(e) {
                        const currentTime = new Date().getTime();
                        const tapLength = currentTime - lastTap;
                        
                        if (tapLength < 300 && tapLength > 0) {
                            // Double tap detected
                            e.preventDefault();
                            triggerCameraFocus();
                        }
                        lastTap = currentTime;
                    });

                    // Juga support untuk mouse double click (untuk desktop testing)
                    qrReaderElement.addEventListener('dblclick', function(e) {
                        e.preventDefault();
                        triggerCameraFocus();
                    });
                }
            }).catch(function(err) {
                // Error saat memulai kamera
                isScanning = false;
                isProcessing = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanHint').hide();
                $('#scanActions').hide();

                let errorMessage = 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.';

                if (err && err.message) {
                    if (err.message.includes('Permission denied')) {
                        errorMessage = 'Akses kamera ditolak. Silakan berikan izin akses kamera di pengaturan browser.';
                    } else if (err.message.includes('NotFoundError')) {
                        errorMessage = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera yang aktif.';
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            });
        }

        // Fungsi untuk menghentikan scan
        function stopScan() {
            if (!isScanning || !html5QrCode) {
                return;
            }

            isProcessing = false; // Reset processing flag

            // Hapus event listeners
            const qrReaderElement = document.getElementById('qr-reader');
            if (qrReaderElement) {
                const newElement = qrReaderElement.cloneNode(true);
                qrReaderElement.parentNode.replaceChild(newElement, qrReaderElement);
            }

            html5QrCode.stop().then(function() {
                html5QrCode.clear();
                html5QrCode = null;
                isScanning = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanHint').hide();
                $('#scanActions').hide();
            }).catch(function(err) {
                console.error("Error stopping scan:", err);
                isScanning = false;
                isProcessing = false;
                $('#btnScanQR').prop('disabled', false);
                $('#qr-reader').hide();
                $('#scanHint').hide();
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