<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('pesan')) : ?>
            <?= session()->getFlashdata('pesan') ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Profil Lembaga</h3>
                        <?php if (!empty($tpq)) : ?>
                            <div class="card-tools">
                                <a href="<?= base_url('backend/tpq/edit/' . $tpq[0]['IdTpq']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Profil
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($tpq)) : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID TPQ:</label>
                                        <p class="form-control-static"><?= $tpq[0]['IdTpq'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama TPQ:</label>
                                        <p class="form-control-static"><?= $tpq[0]['NamaTpq'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Alamat:</label>
                                        <p class="form-control-static"><?= $tpq[0]['Alamat'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tempat Belajar:</label>
                                        <p class="form-control-static"><?= $tpq[0]['TempatBelajar'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Berdiri:</label>
                                        <p class="form-control-static"><?= $tpq[0]['TahunBerdiri'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> Data Tidak Ditemukan!</h4>
                                Belum ada data TPQ yang tersimpan. Silakan tambahkan data TPQ terlebih dahulu.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Lembaga Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Logo Lembaga</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($tpq) && !empty($tpq[0]['LogoLembaga'])) : ?>
                                <img id="previewLogo" src="<?= base_url('uploads/logo/' . $tpq[0]['LogoLembaga']) ?>"
                                    alt="Logo Lembaga"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 200px;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-image fa-3x"></i>
                                    <p>Belum ada logo</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="formUploadLogo" class="mt-3">
                            <?php if (!empty($tpq)) : ?>
                                <input type="hidden" name="IdTpq" id="IdTpqLogo" value="<?= $tpq[0]['IdTpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="logo">Upload Logo Baru:</label>
                                <small class="form-text text-muted d-block mb-2">
                                    Format yang didukung: Image (JPG, PNG, GIF) - Format persegi
                                </small>
                                <input type="file"
                                    class="form-control-file d-none"
                                    id="logo"
                                    name="logo"
                                    accept="image/jpeg,image/jpg,image/png,image/gif"
                                    onchange="showCropModalLogo()">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('logo').click();">
                                        <i class="fas fa-upload"></i> Upload Logo
                                    </button>
                                    <?php if (!empty($tpq) && !empty($tpq[0]['LogoLembaga'])) : ?>
                                        <button type="button" class="btn btn-warning" onclick="editLogo()" title="Edit ukuran logo">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kop Lembaga Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kop Lembaga</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($tpq) && !empty($tpq[0]['KopLembaga'])) : ?>
                                <img id="previewKop" src="<?= base_url('uploads/kop/' . $tpq[0]['KopLembaga']) ?>"
                                    alt="Kop Lembaga"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 100%;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-file-image fa-3x"></i>
                                    <p>Belum ada kop lembaga</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="formUploadKop" class="mt-3">
                            <?php if (!empty($tpq)) : ?>
                                <input type="hidden" name="IdTpq" id="IdTpq" value="<?= $tpq[0]['IdTpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="kop_lembaga">Upload Kop Baru:</label>
                                <small class="form-text text-muted d-block mb-2">
                                    Format yang didukung: Image (JPG, PNG, GIF)
                                </small>
                                <input type="file"
                                    class="form-control-file d-none"
                                    id="kop_lembaga"
                                    name="kop_lembaga"
                                    accept="image/jpeg,image/jpg,image/png,image/gif"
                                    onchange="showCropModalKop()">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="document.getElementById('kop_lembaga').click();">
                                        <i class="fas fa-upload"></i> Upload Kop
                                    </button>
                                    <?php if (!empty($tpq) && !empty($tpq[0]['KopLembaga'])) : ?>
                                        <button type="button" class="btn btn-warning" onclick="editKop()" title="Edit ukuran kop">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Crop Logo Lembaga -->
<div class="modal fade" id="modalCropLogo" tabindex="-1" role="dialog" aria-labelledby="modalCropLogoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCropLogoLabel">
                    <i class="fas fa-crop"></i> Crop Logo Lembaga
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Kontrol Crop untuk Logo -->
                <div class="crop-controls mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Zoom Controls">
                                <button type="button" class="btn btn-outline-secondary" onclick="zoomLogo('in')" title="Zoom In">
                                    <i class="fas fa-search-plus"></i> Zoom In
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="zoomLogo('out')" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i> Zoom Out
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm ml-2" role="group" aria-label="Move Controls">
                                <button type="button" class="btn btn-outline-info" onclick="moveLogo('up')" title="Geser Atas">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveLogo('down')" title="Geser Bawah">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveLogo('left')" title="Geser Kiri">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveLogo('right')" title="Geser Kanan">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="img-container-logo" style="min-height: 400px;">
                            <img id="imageToCropLogo" src="" alt="Logo untuk di-crop" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnCropLogo">
                    <i class="fas fa-check"></i> Simpan Logo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crop Kop Lembaga -->
<div class="modal fade" id="modalCropKop" tabindex="-1" role="dialog" aria-labelledby="modalCropKopLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCropKopLabel">
                    <i class="fas fa-crop"></i> Crop Kop Lembaga
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Kontrol Crop untuk Kop -->
                <div class="crop-controls mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Zoom Controls">
                                <button type="button" class="btn btn-outline-secondary" onclick="zoomKop('in')" title="Zoom In">
                                    <i class="fas fa-search-plus"></i> Zoom In
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="zoomKop('out')" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i> Zoom Out
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm ml-2" role="group" aria-label="Move Controls">
                                <button type="button" class="btn btn-outline-info" onclick="moveKop('up')" title="Geser Atas">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveKop('down')" title="Geser Bawah">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveKop('left')" title="Geser Kiri">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="moveKop('right')" title="Geser Kanan">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="img-container-kop" style="min-height: 400px;">
                            <img id="imageToCropKop" src="" alt="Kop untuk di-crop" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="btnCropKop">
                    <i class="fas fa-check"></i> Simpan Kop
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<style>
    .img-container-logo,
    .img-container-kop {
        width: 100%;
        min-height: 400px;
        max-height: 500px;
        background-color: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-container-logo>img,
    .img-container-kop>img {
        max-width: 100%;
        max-height: 100%;
        display: block;
    }

    /* Cropper container styling */
    .cropper-container {
        direction: ltr !important;
    }

    .cropper-wrap-box,
    .cropper-canvas,
    .cropper-drag-box,
    .cropper-crop-box,
    .cropper-modal {
        direction: ltr !important;
    }

    /* Styling untuk kontrol crop */
    .crop-controls {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }

    .crop-controls .btn-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .crop-controls .btn {
        min-width: 45px;
    }

    .crop-controls .btn i {
        margin-right: 5px;
    }
</style>

<script>
    let cropperLogo = null;
    let selectedFileLogo = null;
    let cropperKop = null;
    let selectedFileKop = null;

    // Pastikan Cropper.js sudah dimuat
    function ensureCropperLoaded(callback) {
        if (typeof Cropper !== 'undefined' && typeof Cropper === 'function') {
            callback();
            return;
        }

        let attempts = 0;
        const maxAttempts = 50;
        const checkInterval = setInterval(function() {
            attempts++;
            if (typeof Cropper !== 'undefined' && typeof Cropper === 'function') {
                clearInterval(checkInterval);
                callback();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat library Cropper.js. Pastikan koneksi internet stabil atau refresh halaman.'
                });
            }
        }, 100);
    }

    // Function untuk edit logo yang sudah ada
    function editLogo() {
        const previewImg = document.getElementById('previewLogo');
        if (!previewImg || !previewImg.src) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Logo tidak ditemukan'
            });
            return;
        }

        // Pastikan Cropper.js sudah dimuat
        ensureCropperLoaded(function() {
            showCropModalLogoFromBase64(previewImg.src);
        });
    }

    // Function untuk menampilkan modal crop Logo Lembaga
    function showCropModalLogo() {
        const fileInput = document.getElementById('logo');
        const file = fileInput.files[0];

        if (!file) {
            return;
        }

        // Validasi ukuran file (max 5MB)
        if (file.size > 5242880) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 5MB'
            });
            fileInput.value = '';
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau GIF'
            });
            fileInput.value = '';
            return;
        }

        // Simpan file untuk digunakan nanti
        selectedFileLogo = file;

        // Pastikan Cropper.js sudah dimuat sebelum melanjutkan
        ensureCropperLoaded(function() {
            // Baca file sebagai URL
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageUrl = e.target.result;
                showCropModalLogoFromBase64(imageUrl);
            };
            reader.readAsDataURL(file);
        });
    }

    // Function untuk menampilkan modal crop Logo dari base64 image
    function showCropModalLogoFromBase64(imageUrl) {
        const imageElement = document.getElementById('imageToCropLogo');

        // Destroy cropper sebelumnya jika ada
        if (cropperLogo) {
            cropperLogo.destroy();
            cropperLogo = null;
        }

        // Set image source terlebih dahulu
        imageElement.src = imageUrl;

        // Destroy event handler sebelumnya jika ada
        $('#modalCropLogo').off('shown.bs.modal');

        // Show modal
        $('#modalCropLogo').modal('show');

        // Inisialisasi cropper setelah modal ditampilkan
        $('#modalCropLogo').on('shown.bs.modal', function() {
            // Destroy cropper sebelumnya jika ada
            if (cropperLogo) {
                cropperLogo.destroy();
                cropperLogo = null;
            }

            // Reset image src untuk memastikan event onload terpicu
            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            // Tunggu image element benar-benar loaded
            imageElement.onload = function() {
                // Tunggu sedikit agar modal selesai render sepenuhnya
                setTimeout(function() {
                    // Cek lagi apakah Cropper.js tersedia
                    if (typeof Cropper === 'undefined') {
                        console.error('Cropper library not available');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.'
                        });
                        return;
                    }

                    // Pastikan image element sudah ada di DOM dan punya src
                    if (!imageElement.src || imageElement.offsetWidth === 0) {
                        console.error('Image not ready for cropper');
                        return;
                    }

                    // Destroy cropper sebelumnya jika masih ada
                    if (cropperLogo) {
                        cropperLogo.destroy();
                        cropperLogo = null;
                    }

                    // Inisialisasi cropper dengan aspect ratio 1:1 untuk logo (persegi)
                    try {
                        cropperLogo = new Cropper(imageElement, {
                            aspectRatio: 1, // 1:1 untuk logo (persegi)
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            responsive: true,
                            minCropBoxWidth: 200,
                            minCropBoxHeight: 200,
                            ready: function() {
                                console.log('Cropper Logo initialized successfully');
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing cropper:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menginisialisasi cropper: ' + error.message
                        });
                    }
                }, 500);
            };

            // Trigger onload jika image sudah cached
            if (imageElement.complete) {
                imageElement.onload();
            } else {
                // Jika belum complete, tunggu event load
                imageElement.addEventListener('load', imageElement.onload, {
                    once: true
                });
            }
        });
    }

    // Function untuk crop dan upload Logo Lembaga
    function uploadLogo() {
        if (!cropperLogo) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cropper belum diinisialisasi'
            });
            return;
        }

        // Get cropped canvas dengan ukuran standar untuk logo (persegi)
        const canvas = cropperLogo.getCroppedCanvas({
            width: 500,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal membuat canvas'
            });
            return;
        }

        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengkonversi gambar'
                });
                return;
            }

            // Convert blob to base64
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Image = e.target.result;

                // Upload dengan AJAX
                const formData = new FormData();
                formData.append('logo_cropped', base64Image);
                const idTpq = document.getElementById('IdTpqLogo')?.value;
                if (idTpq) {
                    formData.append('IdTpq', idTpq);
                }

                // Show loading
                Swal.fire({
                    title: 'Mengupload...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/tpq/uploadLogo') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // Update preview dengan URL baru
                            if (response.logo_url) {
                                const previewImg = document.getElementById('previewLogo');
                                if (previewImg) {
                                    previewImg.src = response.logo_url;
                                } else {
                                    // Reload halaman jika preview tidak ada
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                            } else {
                                // Reload halaman untuk update preview
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
                            // Close modal dan reset
                            $('#modalCropLogo').modal('hide');
                            document.getElementById('logo').value = '';
                            if (cropperLogo) {
                                cropperLogo.destroy();
                                cropperLogo = null;
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengupload logo'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9); // JPEG dengan quality 90%
    }

    // Function untuk edit kop yang sudah ada
    function editKop() {
        const previewImg = document.getElementById('previewKop');
        if (!previewImg || !previewImg.src) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Kop lembaga tidak ditemukan'
            });
            return;
        }

        // Pastikan Cropper.js sudah dimuat
        ensureCropperLoaded(function() {
            showCropModalKopFromBase64(previewImg.src);
        });
    }

    // Function untuk zoom Logo
    function zoomLogo(direction) {
        if (!cropperLogo) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Cropper belum siap. Tunggu sebentar.'
            });
            return;
        }
        const ratio = direction === 'in' ? 0.1 : -0.1;
        cropperLogo.zoom(ratio);
    }

    // Function untuk move/geser Logo
    function moveLogo(direction) {
        if (!cropperLogo) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Cropper belum siap. Tunggu sebentar.'
            });
            return;
        }
        const moveAmount = 10; // pixel
        let offsetX = 0;
        let offsetY = 0;
        
        switch(direction) {
            case 'up':
                offsetY = -moveAmount;
                break;
            case 'down':
                offsetY = moveAmount;
                break;
            case 'left':
                offsetX = -moveAmount;
                break;
            case 'right':
                offsetX = moveAmount;
                break;
        }
        
        cropperLogo.move(offsetX, offsetY);
    }

    // Function untuk zoom Kop
    function zoomKop(direction) {
        if (!cropperKop) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Cropper belum siap. Tunggu sebentar.'
            });
            return;
        }
        const ratio = direction === 'in' ? 0.1 : -0.1;
        cropperKop.zoom(ratio);
    }

    // Function untuk move/geser Kop
    function moveKop(direction) {
        if (!cropperKop) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Cropper belum siap. Tunggu sebentar.'
            });
            return;
        }
        const moveAmount = 10; // pixel
        let offsetX = 0;
        let offsetY = 0;
        
        switch(direction) {
            case 'up':
                offsetY = -moveAmount;
                break;
            case 'down':
                offsetY = moveAmount;
                break;
            case 'left':
                offsetX = -moveAmount;
                break;
            case 'right':
                offsetX = moveAmount;
                break;
        }
        
        cropperKop.move(offsetX, offsetY);
    }

    // Function untuk menampilkan modal crop Kop Lembaga
    function showCropModalKop() {
        const fileInput = document.getElementById('kop_lembaga');
        const file = fileInput.files[0];

        if (!file) {
            return;
        }

        // Validasi ukuran file (max 5MB)
        if (file.size > 5242880) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 5MB'
            });
            fileInput.value = '';
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau GIF'
            });
            fileInput.value = '';
            return;
        }

        // Simpan file untuk digunakan nanti
        selectedFileKop = file;

        // Pastikan Cropper.js sudah dimuat sebelum melanjutkan
        ensureCropperLoaded(function() {
            // Baca file sebagai URL
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageUrl = e.target.result;
                showCropModalKopFromBase64(imageUrl);
            };
            reader.readAsDataURL(file);
        });
    }

    // Function untuk menampilkan modal crop dari base64 image
    function showCropModalKopFromBase64(imageUrl) {
        const imageElement = document.getElementById('imageToCropKop');

        // Destroy cropper sebelumnya jika ada
        if (cropperKop) {
            cropperKop.destroy();
            cropperKop = null;
        }

        // Set image source terlebih dahulu
        imageElement.src = imageUrl;

        // Destroy event handler sebelumnya jika ada
        $('#modalCropKop').off('shown.bs.modal');

        // Show modal
        $('#modalCropKop').modal('show');

        // Inisialisasi cropper setelah modal ditampilkan
        $('#modalCropKop').on('shown.bs.modal', function() {
            // Destroy cropper sebelumnya jika ada
            if (cropperKop) {
                cropperKop.destroy();
                cropperKop = null;
            }

            // Reset image src untuk memastikan event onload terpicu
            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            // Tunggu image element benar-benar loaded
            imageElement.onload = function() {
                // Tunggu sedikit agar modal selesai render sepenuhnya
                setTimeout(function() {
                    // Cek lagi apakah Cropper.js tersedia
                    if (typeof Cropper === 'undefined') {
                        console.error('Cropper library not available');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.'
                        });
                        return;
                    }

                    // Pastikan image element sudah ada di DOM dan punya src
                    if (!imageElement.src || imageElement.offsetWidth === 0) {
                        console.error('Image not ready for cropper');
                        return;
                    }

                    // Destroy cropper sebelumnya jika masih ada
                    if (cropperKop) {
                        cropperKop.destroy();
                        cropperKop = null;
                    }

                    // Inisialisasi cropper dengan aspect ratio 4:1 untuk kop lembaga (landscape format standar A4)
                    try {
                        cropperKop = new Cropper(imageElement, {
                            aspectRatio: 4 / 1, // 4:1 untuk kop lembaga (standar untuk print A4)
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            responsive: true,
                            minCropBoxWidth: 400,
                            minCropBoxHeight: 100,
                            ready: function() {
                                console.log('Cropper Kop initialized successfully');
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing cropper:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menginisialisasi cropper: ' + error.message
                        });
                    }
                }, 500);
            };

            // Trigger onload jika image sudah cached
            if (imageElement.complete) {
                imageElement.onload();
            } else {
                // Jika belum complete, tunggu event load
                imageElement.addEventListener('load', imageElement.onload, {
                    once: true
                });
            }
        });
    }

    // Function untuk crop dan upload Kop Lembaga
    function uploadKop() {
        if (!cropperKop) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cropper belum diinisialisasi'
            });
            return;
        }

        // Get cropped canvas dengan ukuran standar untuk kop lembaga
        // Format landscape 4:1 dengan lebar 2000px dan tinggi 500px (optimal untuk print A4)
        const canvas = cropperKop.getCroppedCanvas({
            width: 2000,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal membuat canvas'
            });
            return;
        }

        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengkonversi gambar'
                });
                return;
            }

            // Convert blob to base64
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Image = e.target.result;

                // Upload dengan AJAX
                const formData = new FormData();
                formData.append('kop_lembaga_cropped', base64Image);
                const idTpq = document.getElementById('IdTpq')?.value;
                if (idTpq) {
                    formData.append('IdTpq', idTpq);
                }

                // Show loading
                Swal.fire({
                    title: 'Mengupload...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/tpq/uploadKop') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // Update preview dengan URL baru
                            if (response.kop_url) {
                                const previewImg = document.getElementById('previewKop');
                                if (previewImg) {
                                    previewImg.src = response.kop_url;
                                } else {
                                    // Reload halaman jika preview tidak ada
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                            } else {
                                // Reload halaman untuk update preview
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
                            // Close modal dan reset
                            $('#modalCropKop').modal('hide');
                            document.getElementById('kop_lembaga').value = '';
                            if (cropperKop) {
                                cropperKop.destroy();
                                cropperKop = null;
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengupload kop lembaga'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9); // JPEG dengan quality 90%
    }

    // Handle button crop
    $(document).ready(function() {
        // Handle crop logo
        $('#btnCropLogo').on('click', function() {
            uploadLogo();
        });

        // Cleanup cropper logo saat modal ditutup
        $('#modalCropLogo').on('hidden.bs.modal', function() {
            if (cropperLogo) {
                cropperLogo.destroy();
                cropperLogo = null;
            }
            document.getElementById('logo').value = '';
            selectedFileLogo = null;
        });

        // Handle crop kop
        $('#btnCropKop').on('click', function() {
            uploadKop();
        });

        // Cleanup cropper kop saat modal ditutup
        $('#modalCropKop').on('hidden.bs.modal', function() {
            if (cropperKop) {
                cropperKop.destroy();
                cropperKop = null;
            }
            document.getElementById('kop_lembaga').value = '';
            selectedFileKop = null;
        });
    });
</script>
<?= $this->endSection() ?>