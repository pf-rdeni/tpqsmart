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
                        <h3 class="card-title">Informasi Profil Lembaga FKPQ</h3>
                        <?php if (!empty($fkpq)) : ?>
                            <div class="card-tools">
                                <a href="<?= base_url('backend/fkpq/print-profil-lembaga/' . $fkpq[0]['IdFkpq']) ?>" class="btn btn-danger btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Print PDF
                                </a>
                                <a href="<?= base_url('backend/fkpq/edit/' . $fkpq[0]['IdFkpq']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Profil FKPQ
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($fkpq)) : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID FKPQ:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['IdFkpq'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama FKPQ:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['NamaFkpq'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Alamat:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['Alamat'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kecamatan:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['Kecamatan'] ?? '-' ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Berdiri:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['TahunBerdiri'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tempat Belajar:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['TempatBelajar'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kepala FKPQ:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['KetuaFkpq'] ?? '-' ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. HP:</label>
                                        <p class="form-control-static"><?= $fkpq[0]['NoHp'] ?? '-' ?></p>
                                    </div>
                                </div>
                            </div>


                            <?php if (!empty($fkpq[0]['Visi']) || !empty($fkpq[0]['Misi'])) : ?>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <?php if (!empty($fkpq[0]['Visi'])) : ?>
                                            <div class="mb-3">
                                                <label class="font-weight-bold">Visi Lembaga:</label>
                                                <div>
                                                    <?= formatVisi($fkpq[0]['Visi']) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($fkpq[0]['Misi'])) : ?>
                                            <div class="mb-3">
                                                <label class="font-weight-bold">Misi Lembaga:</label>
                                                <div>
                                                    <?= formatMisi($fkpq[0]['Misi']) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> Data Tidak Ditemukan!</h4>
                                Belum ada data FKPQ yang tersimpan. Silakan tambahkan data FKPQ terlebih dahulu.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo dan Kop Lembaga Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><i class="fas fa-image"></i> Logo Lembaga FKPQ</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($fkpq) && !empty($fkpq[0]['LogoLembaga'])) : ?>
                                <img id="previewLogo" src="<?= base_url('uploads/logo/' . $fkpq[0]['LogoLembaga']) ?>"
                                    alt="Logo Lembaga FKPQ"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 200px;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-image fa-3x"></i>
                                    <p>Belum ada logo FKPQ</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="formUploadLogo" class="mt-3">
                            <?php if (!empty($fkpq)) : ?>
                                <input type="hidden" name="IdFkpq" id="IdFkpqLogo" value="<?= $fkpq[0]['IdFkpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="logo">Upload Logo FKPQ Baru:</label>
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
                                    <?php if (!empty($fkpq) && !empty($fkpq[0]['LogoLembaga'])) : ?>
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

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title"><i class="fas fa-file-image"></i> Kop Lembaga FKPQ</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($fkpq) && !empty($fkpq[0]['KopLembaga'])) : ?>
                                <img id="previewKop" src="<?= base_url('uploads/kop/' . $fkpq[0]['KopLembaga']) ?>"
                                    alt="Kop Lembaga FKPQ"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 100%;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-file-image fa-3x"></i>
                                    <p>Belum ada kop lembaga FKPQ</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="formUploadKop" class="mt-3">
                            <?php if (!empty($fkpq)) : ?>
                                <input type="hidden" name="IdFkpq" id="IdFkpq" value="<?= $fkpq[0]['IdFkpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="kop_lembaga">Upload Kop FKPQ Baru:</label>
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
                                    <?php if (!empty($fkpq) && !empty($fkpq[0]['KopLembaga'])) : ?>
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        selectedFileLogo = file;

        ensureCropperLoaded(function() {
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

        if (cropperLogo) {
            cropperLogo.destroy();
            cropperLogo = null;
        }

        imageElement.src = imageUrl;
        $('#modalCropLogo').off('shown.bs.modal');
        $('#modalCropLogo').modal('show');
        $('#modalCropLogo').on('shown.bs.modal', function() {
            if (cropperLogo) {
                cropperLogo.destroy();
                cropperLogo = null;
            }

            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            imageElement.onload = function() {
                setTimeout(function() {
                    if (typeof Cropper === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.'
                        });
                        return;
                    }

                    if (!imageElement.src || imageElement.offsetWidth === 0) return;

                    if (cropperLogo) {
                        cropperLogo.destroy();
                        cropperLogo = null;
                    }

                    try {
                        cropperLogo = new Cropper(imageElement, {
                            aspectRatio: 1,
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

            if (imageElement.complete) {
                imageElement.onload();
            } else {
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

        canvas.toBlob(function(blob) {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengkonversi gambar'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Image = e.target.result;
                const formData = new FormData();
                formData.append('logo_cropped', base64Image);
                const idFkpq = document.getElementById('IdFkpqLogo')?.value;
                if (idFkpq) {
                    formData.append('IdFkpq', idFkpq);
                }

                Swal.fire({
                    title: 'Mengupload...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/fkpq/upload-logo') ?>',
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
                            if (response.logo_url) {
                                const previewImg = document.getElementById('previewLogo');
                                if (previewImg) {
                                    previewImg.src = response.logo_url;
                                } else {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                            } else {
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9);
    }

    // Function untuk edit kop yang sudah ada
    function editKop() {
        const previewImg = document.getElementById('previewKop');
        if (!previewImg || !previewImg.src) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Kop tidak ditemukan'
            });
            return;
        }

        ensureCropperLoaded(function() {
            showCropModalKopFromBase64(previewImg.src);
        });
    }

    // Function untuk menampilkan modal crop Kop Lembaga
    function showCropModalKop() {
        const fileInput = document.getElementById('kop_lembaga');
        const file = fileInput.files[0];

        if (!file) {
            return;
        }

        if (file.size > 5242880) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 5MB'
            });
            fileInput.value = '';
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan'
            });
            fileInput.value = '';
            return;
        }

        selectedFileKop = file;

        ensureCropperLoaded(function() {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageUrl = e.target.result;
                showCropModalKopFromBase64(imageUrl);
            };
            reader.readAsDataURL(file);
        });
    }

    // Function untuk menampilkan modal crop Kop dari base64 image
    function showCropModalKopFromBase64(imageUrl) {
        const imageElement = document.getElementById('imageToCropKop');

        if (cropperKop) {
            cropperKop.destroy();
            cropperKop = null;
        }

        imageElement.src = imageUrl;
        $('#modalCropKop').off('shown.bs.modal');
        $('#modalCropKop').modal('show');
        $('#modalCropKop').on('shown.bs.modal', function() {
            if (cropperKop) {
                cropperKop.destroy();
                cropperKop = null;
            }

            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            imageElement.onload = function() {
                setTimeout(function() {
                    if (typeof Cropper === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.'
                        });
                        return;
                    }

                    if (!imageElement.src || imageElement.offsetWidth === 0) return;

                    if (cropperKop) {
                        cropperKop.destroy();
                        cropperKop = null;
                    }

                    try {
                        cropperKop = new Cropper(imageElement, {
                            aspectRatio: 4 / 1,
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

            if (imageElement.complete) {
                imageElement.onload();
            } else {
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

        canvas.toBlob(function(blob) {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengkonversi gambar'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Image = e.target.result;
                const formData = new FormData();
                formData.append('kop_lembaga_cropped', base64Image);
                const idFkpq = document.getElementById('IdFkpq')?.value;
                if (idFkpq) {
                    formData.append('IdFkpq', idFkpq);
                }

                Swal.fire({
                    title: 'Mengupload...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/fkpq/upload-kop') ?>',
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
                            if (response.kop_url) {
                                const previewImg = document.getElementById('previewKop');
                                if (previewImg) {
                                    previewImg.src = response.kop_url;
                                } else {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                            } else {
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            };
            reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9);
    }

    // Zoom controls untuk Logo
    function zoomLogo(direction) {
        if (!cropperLogo) return;
        if (direction === 'in') {
            cropperLogo.zoom(0.1);
        } else if (direction === 'out') {
            cropperLogo.zoom(-0.1);
        }
    }

    // Move controls untuk Logo
    function moveLogo(direction) {
        if (!cropperLogo) return;
        const moveX = 10;
        const moveY = 10;
        if (direction === 'up') {
            cropperLogo.move(0, moveY);
        } else if (direction === 'down') {
            cropperLogo.move(0, -moveY);
        } else if (direction === 'left') {
            cropperLogo.move(moveX, 0);
        } else if (direction === 'right') {
            cropperLogo.move(-moveX, 0);
        }
    }

    // Zoom controls untuk Kop
    function zoomKop(direction) {
        if (!cropperKop) return;
        if (direction === 'in') {
            cropperKop.zoom(0.1);
        } else if (direction === 'out') {
            cropperKop.zoom(-0.1);
        }
    }

    // Move controls untuk Kop
    function moveKop(direction) {
        if (!cropperKop) return;
        const moveX = 10;
        const moveY = 10;
        if (direction === 'up') {
            cropperKop.move(0, moveY);
        } else if (direction === 'down') {
            cropperKop.move(0, -moveY);
        } else if (direction === 'left') {
            cropperKop.move(moveX, 0);
        } else if (direction === 'right') {
            cropperKop.move(-moveX, 0);
        }
    }

    // Handle button crop
    $(document).ready(function() {
        $('#btnCropLogo').on('click', function() {
            uploadLogo();
        });

        $('#modalCropLogo').on('hidden.bs.modal', function() {
            if (cropperLogo) {
                cropperLogo.destroy();
                cropperLogo = null;
            }
            document.getElementById('logo').value = '';
            selectedFileLogo = null;
        });

        $('#btnCropKop').on('click', function() {
            uploadKop();
        });

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

