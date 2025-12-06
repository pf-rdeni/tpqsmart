<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Profil Pengguna
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Informasi Akun -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Akun Login</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Foto Profil -->
                                <div class="col-md-3 text-center mb-3">
                                    <?php
                                    $photoUrl = !empty($user['user_image'])
                                        ? base_url('uploads/profil/user/' . $user['user_image'])
                                        : base_url('images/no-photo.jpg');
                                    ?>
                                    <label class="text-center w-100">Photo Profil</label>
                                    <div class="text-center">
                                        <img id="previewPhoto" src="<?= $photoUrl ?>" alt="Preview Photo"
                                            class="img-thumbnail mx-auto d-block" style="width: 100%; max-width: 215px; height: auto; min-height: 280px; object-fit: cover;">
                                        <div class="mt-2 d-flex justify-content-between" style="width: 215px; margin: 0 auto; flex-wrap: wrap; gap: 5px;">
                                            <button type="button" class="btn btn-sm btn-primary flex-grow-1" onclick="document.getElementById('photo_profil').click()" style="min-width: 70px;">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success flex-grow-1" onclick="openCamera()" style="min-width: 70px;">
                                                <i class="fas fa-camera"></i> Ambil
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning flex-grow-1" id="btnEditPhoto" onclick="editExistingPhoto()" style="min-width: 70px; display: none;">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-center d-block mb-2 text-primary">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Upload foto profil atau ambil foto dengan kamera. <span id="editPhotoHint" style="display: none;"><strong>Klik Edit untuk crop foto yang sudah ada</strong></span></small>
                                    <form id="formUploadPhoto" style="display: none;">
                                        <input type="file" id="photo_profil" name="photo_profil"
                                            accept=".jpg,.jpeg,.png,image/*;capture=camera"
                                            onchange="previewPhoto(this)" style="display: none;">
                                    </form>
                                    <span id="photo_profilError" class="text-danger" style="display:none;">Photo Profil diperlukan.</span>
                                </div>
                                <!-- Informasi Akun -->
                                <div class="col-md-9">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px;">Nama Lengkap</th>
                                            <td><?= esc($user['fullname'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td><?= esc($user['username'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?= esc($user['email'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td><?= esc($user['nik'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php if (isset($user['active']) && $user['active'] == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Tidak Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Grup/Hak Akses</th>
                                            <td>
                                                <?php if (!empty($groups)): ?>
                                                    <?php foreach ($groups as $group): ?>
                                                        <span class="badge badge-primary"><?= esc($group) ?></span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tidak ada grup</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Edit Profil -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Edit Profil</h3>
                        </div>
                        <div class="card-body">
                            <form id="formEditProfil">
                                <div class="form-group">
                                    <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        value="<?= esc($user['fullname'] ?? '') ?>" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?= esc($user['username'] ?? '') ?>" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= esc($user['email'] ?? '') ?>" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Simpan Perubahan Profil
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Form Reset Password -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-key"></i> Reset Password</h3>
                        </div>
                        <div class="card-body">
                            <form id="formResetPassword">
                                <div class="form-group">
                                    <label for="current_password">Password Saat Ini <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" required autocomplete="current-password">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password"
                                        name="new_password" required autocomplete="new-password"
                                        minlength="8">
                                    <small class="form-text text-muted">Minimal 8 karakter</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required autocomplete="new-password">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-key"></i> Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crop Image -->
<div class="modal fade" id="modalCropImage" tabindex="-1" role="dialog" aria-labelledby="modalCropImageLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCropImageLabel">
                    <i class="fas fa-crop"></i> Crop Foto Profil
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading mb-2" style="cursor: pointer;" data-toggle="collapse" data-target="#petunjukCropProfil" aria-expanded="false" aria-controls="petunjukCropProfil">
                        <i class="fas fa-info-circle"></i> Petunjuk Crop Foto Profil 
                        <i class="fas fa-chevron-down float-right" id="iconPetunjukCropProfil"></i>
                    </h5>
                    <div class="collapse" id="petunjukCropProfil">
                        <ul class="mb-0">
                            <li><strong>Geser dan sesuaikan posisi foto</strong> dengan mengklik dan menyeret area crop (kotak biru) atau gunakan tombol kontrol di bawah</li>
                            <li><strong>Zoom in/out</strong> dengan menggunakan scroll mouse, pinch gesture pada touchscreen, atau tombol zoom</li>
                            <li><strong>Rasio foto 3:4</strong> - Pastikan wajah berada di tengah dan terlihat jelas</li>
                            <li><strong>Direkomendasikan:</strong> Foto dengan latar belakang merah, wajah menghadap ke depan, dan pencahayaan yang cukup</li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="img-container-crop" style="min-height: 400px;">
                            <img id="imageToCrop" src="" alt="Foto untuk di-crop" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <div class="btn-group" role="group" aria-label="Kontrol Crop">
                            <button type="button" class="btn btn-outline-primary" id="btnZoomIn" title="Zoom In">
                                <i class="fas fa-search-plus"></i> Zoom In
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btnZoomOut" title="Zoom Out">
                                <i class="fas fa-search-minus"></i> Zoom Out
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btnMove" title="Geser Foto">
                                <i class="fas fa-arrows-alt"></i> Geser
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnReset" title="Reset">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnCropImage">
                    <i class="fas fa-check"></i> Simpan Foto
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Image Upload Helper -->
<script src="<?= base_url('helpers/js/image-upload-helper.js') ?>"></script>
<style>
    .img-container-crop {
        width: 100%;
        min-height: 400px;
        max-height: 500px;
        background-color: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-container-crop>img {
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

    /* Styling untuk tombol kontrol crop */
    .btn-group .btn {
        min-width: 100px;
    }

    .btn-group .btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    let cropper = null;
    let selectedFile = null;

    // Menggunakan helper dari image-upload-helper.js
    // Pastikan ImageUploadHelper sudah dimuat sebelum menggunakan fungsi-fungsi ini
    if (!window.ImageUploadHelper) {
        console.error('ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.');
    }

    // Fungsi untuk mengecek dan menampilkan/menyembunyikan button Edit
    function toggleEditButton() {
        const previewPhoto = document.getElementById('previewPhoto');
        const btnEditPhoto = document.getElementById('btnEditPhoto');
        const editPhotoHint = document.getElementById('editPhotoHint');
        const currentPhotoUrl = previewPhoto.src;

        // Cek apakah foto bukan default no-photo
        if (currentPhotoUrl && !currentPhotoUrl.includes('no-photo.jpg')) {
            // Tampilkan button Edit dan hint
            if (btnEditPhoto) {
                btnEditPhoto.style.display = 'block';
            }
            if (editPhotoHint) {
                editPhotoHint.style.display = 'inline';
            }
        } else {
            // Sembunyikan button Edit dan hint
            if (btnEditPhoto) {
                btnEditPhoto.style.display = 'none';
            }
            if (editPhotoHint) {
                editPhotoHint.style.display = 'none';
            }
        }
    }

    // Pastikan Cropper.js tersedia saat halaman dimuat
    $(document).ready(function() {
        // Cek dan tampilkan button Edit jika foto sudah ada
        toggleEditButton();

        // Cek apakah Cropper.js sudah dimuat
        setTimeout(function() {
            if (typeof Cropper === 'undefined') {
                console.warn('Cropper.js belum dimuat. Pastikan koneksi internet stabil.');
                // Optionally reload Cropper.js secara manual jika perlu
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js';
                script.crossOrigin = 'anonymous';
                script.referrerPolicy = 'no-referrer';
                script.onload = function() {
                    console.log('Cropper.js loaded successfully');
                };
                script.onerror = function() {
                    console.error('Failed to load Cropper.js from CDN');
                };
                document.head.appendChild(script);
            } else {
                console.log('Cropper.js sudah tersedia');
            }
        }, 1000);
    });

    // Fungsi untuk menampilkan preview foto profil
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            showCropModalProfil(file);
        }
    }

    // Fungsi untuk menampilkan modal crop
    function showCropModalProfil(file, imageUrl = null) {
        // Jika file tidak ada tapi ada imageUrl, gunakan imageUrl
        if (!file && !imageUrl) {
            return;
        }

        const errorDiv = document.getElementById('photo_profilError');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }

        // Validasi file jika ada
        if (file) {
            // Validasi tipe file
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                if (errorDiv) {
                    errorDiv.innerHTML = 'Format file tidak valid (gunakan JPG, JPEG, atau PNG)';
                    errorDiv.style.display = 'block';
                }
                return;
            }

            // Validasi ukuran maksimal (diperbesar menjadi 50MB untuk memungkinkan proses resize)
            const maxFileSize = 50 * 1024 * 1024; // 50MB
            if (file.size > maxFileSize) {
                if (errorDiv) {
                    errorDiv.innerHTML = 'Ukuran file ' + file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB) terlalu besar (maksimal 50MB). Silakan kompres gambar terlebih dahulu.';
                    errorDiv.style.display = 'block';
                }
                return;
            }

            // Tampilkan loading indicator jika file besar
            const isLargeFile = file.size > 2 * 1024 * 1024; // > 2MB
            if (isLargeFile) {
                Swal.fire({
                    title: 'Memproses gambar...',
                    text: 'Sedang mengoptimalkan ukuran gambar, harap tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Resize gambar jika terlalu besar sebelum ditampilkan di cropper
            // Max dimensi 2000x2000 untuk performa cropper yang lebih baik
            const maxDimension = 2000;
            const resizeQuality = 0.85; // Quality untuk resize awal

            // Gunakan helper untuk resize sebelum crop
            if (!window.ImageUploadHelper || !window.ImageUploadHelper.resizeImageBeforeCrop) {
                if (isLargeFile) {
                    Swal.close();
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
                });
                return;
            }

            window.ImageUploadHelper.resizeImageBeforeCrop(file, maxDimension, maxDimension, resizeQuality, function(processedFile) {
                selectedFile = processedFile;

                // Tutup loading jika ada
                if (isLargeFile) {
                    Swal.close();
                }

                // Gunakan helper untuk memastikan Cropper.js sudah dimuat
                if (!window.ImageUploadHelper || !window.ImageUploadHelper.ensureCropperLoaded) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
                    });
                    return;
                }

                window.ImageUploadHelper.ensureCropperLoaded(function() {
                    const imageElement = document.getElementById('imageToCrop');

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    // Baca file yang sudah di-resize sebagai data URL
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const finalImageUrl = e.target.result;
                        initializeCropper(finalImageUrl);
                    };
                    reader.readAsDataURL(processedFile);
                });
            });
        } else if (imageUrl) {
            // Jika menggunakan URL yang sudah ada, langsung gunakan
            // Gunakan helper untuk memastikan Cropper.js sudah dimuat
            if (!window.ImageUploadHelper || !window.ImageUploadHelper.ensureCropperLoaded) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
                });
                return;
            }

            window.ImageUploadHelper.ensureCropperLoaded(function() {
                initializeCropper(imageUrl);
            });
        }
    }

    // Fungsi untuk inisialisasi cropper
    function initializeCropper(imageUrl) {
        const imageElement = document.getElementById('imageToCrop');

        imageElement.src = imageUrl;

        $('#modalCropImage').off('shown.bs.modal');
        $('#modalCropImage').modal('show');

        $('#modalCropImage').on('shown.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
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

                    if (!imageElement.src || imageElement.offsetWidth === 0) {
                        return;
                    }

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    try {
                        cropper = new Cropper(imageElement, {
                            aspectRatio: 3 / 4, // 3:4 untuk foto profil
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
                            minCropBoxWidth: 150,
                            minCropBoxHeight: 200,
                            ready: function() {
                                console.log('Cropper initialized successfully');
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

    // Fungsi untuk edit foto yang sudah ada
    function editExistingPhoto() {
        const previewPhoto = document.getElementById('previewPhoto');
        const currentPhotoUrl = previewPhoto.src;

        // Cek apakah foto bukan default no-photo
        if (currentPhotoUrl && !currentPhotoUrl.includes('no-photo.jpg')) {
            // Load foto yang sudah ada ke modal crop
            showCropModalProfil(null, currentPhotoUrl);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Tidak ada foto',
                text: 'Silakan upload foto terlebih dahulu'
            });
        }
    }

    // Function untuk crop dan upload image
    function uploadPhoto() {
        if (!cropper) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cropper belum diinisialisasi'
            });
            return;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Memproses foto...',
            text: 'Sedang memotong dan mengoptimalkan foto profil...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Dimensi target untuk foto profil (300x400 dengan rasio 3:4)
        const targetWidth = 300;
        const targetHeight = 400;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: targetWidth,
            height: targetHeight,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal membuat canvas'
            });
            return;
        }

        // Cek apakah helper tersedia
        if (!window.ImageUploadHelper || !window.ImageUploadHelper.resizeImageFile) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
            });
            return;
        }

        // Convert canvas ke blob dulu, lalu ke File untuk digunakan dengan helper
        canvas.toBlob(function(initialBlob) {
            if (!initialBlob) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengkonversi canvas ke blob'
                });
                return;
            }

            // Convert blob ke File untuk digunakan dengan helper
            const fileName = selectedFile ? selectedFile.name.replace(/\.[^/.]+$/, '') : 'photo';
            const initialFile = new File([initialBlob], fileName + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            // Gunakan helper untuk resize dan optimize dengan adaptive quality
            // Target: maksimal 500KB dengan dimensi sudah sesuai (300x400)
            window.ImageUploadHelper.resizeImageFile(initialFile, {
                maxWidth: targetWidth,
                maxHeight: targetHeight,
                quality: 0.85,
                maxFileSize: 500 * 1024 // 500KB
            }).then(function(optimizedFile) {
                // Baca file yang sudah dioptimalkan untuk preview dan upload
                const reader = new FileReader();
                reader.onload = function(e) {
                    const base64Image = e.target.result;
                    const preview = document.getElementById('previewPhoto');
                    const photoInput = document.getElementById('photo_profil');
                    const errorDiv = document.getElementById('photo_profilError');

                    // Update preview
                    preview.src = base64Image;
                    preview.style.border = '2px solid #28a745';
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }

                    // Upload dengan AJAX
                    const formData = new FormData();
                    formData.append('photo_profil_cropped', base64Image);

                    $.ajax({
                        url: '<?= base_url('backend/pages/uploadPhotoProfil') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Foto berhasil dioptimalkan!',
                                    text: response.message + ' Ukuran: ' + (optimizedFile.size / (1024 * 1024)).toFixed(2) + ' MB',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                // Update preview dengan URL baru
                                if (response.photo_url) {
                                    document.getElementById('previewPhoto').src = response.photo_url;
                                }
                                // Tampilkan button Edit setelah foto berhasil diupload
                                toggleEditButton();
                                // Close modal dan reset
                                $('#modalCropImage').modal('hide');
                                document.getElementById('photo_profil').value = '';
                                if (cropper) {
                                    cropper.destroy();
                                    cropper = null;
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupload foto profil'
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
                reader.onerror = function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal membaca file yang sudah dioptimalkan'
                    });
                };
                reader.readAsDataURL(optimizedFile);
            }).catch(function(error) {
                Swal.close();
                console.error('Error optimizing image:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengoptimalkan gambar: ' + (error.message || 'Unknown error')
                });
            });
        }, 'image/jpeg', 0.95); // Quality awal tinggi untuk canvas to blob
    }

    // Fungsi untuk membuka kamera
    function openCamera() {
        // Cek apakah browser mendukung getUserMedia
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Buat elemen video untuk preview kamera
            const videoPreview = document.createElement('video');
            videoPreview.autoplay = true;
            videoPreview.playsInline = true;
            videoPreview.style.cssText = 'max-width: 100%; max-height: 70vh; border-radius: 8px;';

            // Buat modal untuk menampilkan preview kamera
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:20px;';

            // Container untuk video dan controls
            const videoContainer = document.createElement('div');
            videoContainer.style.cssText = 'position:relative;width:100%;max-width:500px;display:flex;flex-direction:column;align-items:center;';
            videoContainer.appendChild(videoPreview);

            // Button container
            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:10px;margin-top:20px;width:100%;max-width:500px;';

            // Switch camera button
            const switchCameraBtn = document.createElement('button');
            switchCameraBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Ganti Kamera';
            switchCameraBtn.className = 'btn btn-info';
            switchCameraBtn.style.cssText = 'width:100%;max-width:300px;';

            // Tambahkan tombol ambil foto
            const captureBtn = document.createElement('button');
            captureBtn.innerHTML = '<i class="fas fa-camera"></i> Ambil Foto';
            captureBtn.className = 'btn btn-primary';
            captureBtn.style.cssText = 'width:100%;max-width:300px;';

            // Tambahkan tombol tutup
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '<i class="fas fa-times"></i> Tutup';
            closeBtn.className = 'btn btn-secondary';
            closeBtn.style.cssText = 'width:100%;max-width:300px;';

            buttonContainer.appendChild(switchCameraBtn);
            buttonContainer.appendChild(captureBtn);
            buttonContainer.appendChild(closeBtn);

            modal.appendChild(videoContainer);
            modal.appendChild(buttonContainer);

            // Tambahkan modal ke body
            document.body.appendChild(modal);

            let currentStream = null;
            let currentFacingMode = 'environment'; // Default: kamera belakang

            // Fungsi untuk menghentikan stream
            function stopStream() {
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
            }

            // Fungsi untuk memulai kamera
            function startCamera(facingMode) {
                stopStream();

                const constraints = {
                    video: {
                        facingMode: facingMode
                    }
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(stream => {
                        currentStream = stream;
                        currentFacingMode = facingMode;
                        videoPreview.srcObject = stream;
                    })
                    .catch(error => {
                        console.error('Error accessing camera:', error);
                        // Jika kamera belakang gagal, coba kamera depan
                        if (facingMode === 'environment') {
                            startCamera('user');
                        } else {
                            alert('Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                            document.body.removeChild(modal);
                        }
                    });
            }

            // Mulai dengan kamera belakang (default)
            startCamera('environment');

            // Switch camera button
            switchCameraBtn.onclick = () => {
                const newFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
                startCamera(newFacingMode);
            };

            // Handler untuk tombol ambil foto
            captureBtn.onclick = () => {
                // Buat canvas untuk mengambil foto
                const canvas = document.createElement('canvas');
                canvas.width = videoPreview.videoWidth;
                canvas.height = videoPreview.videoHeight;
                canvas.getContext('2d').drawImage(videoPreview, 0, 0);

                // Konversi ke blob
                canvas.toBlob(blob => {
                    // Buat file dari blob
                    const file = new File([blob], "camera-photo.jpg", {
                        type: "image/jpeg"
                    });

                    // Hentikan stream kamera dan tutup modal kamera
                    stopStream();
                    document.body.removeChild(modal);

                    // Tampilkan modal crop
                    showCropModalProfil(file);
                }, 'image/jpeg');
            };

            // Handler untuk tombol tutup
            closeBtn.onclick = () => {
                stopStream();
                document.body.removeChild(modal);
            };
        } else {
            alert('Browser Anda tidak mendukung akses kamera');
        }
    }

    // Handle button crop
    $(document).ready(function() {
        $('#btnCropImage').on('click', function() {
            uploadPhoto();
        });

        // Event listener untuk tombol Zoom In
        const btnZoomIn = document.getElementById('btnZoomIn');
        if (btnZoomIn) {
            btnZoomIn.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(0.1);
                }
            });
        }

        // Event listener untuk tombol Zoom Out
        const btnZoomOut = document.getElementById('btnZoomOut');
        if (btnZoomOut) {
            btnZoomOut.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(-0.1);
                }
            });
        }

        // Event listener untuk tombol Move/Geser
        const btnMove = document.getElementById('btnMove');
        if (btnMove) {
            btnMove.addEventListener('click', function() {
                if (cropper) {
                    const currentDragMode = cropper.options.dragMode;
                    if (currentDragMode === 'move') {
                        cropper.setDragMode('none');
                        btnMove.classList.remove('active');
                    } else {
                        cropper.setDragMode('move');
                        btnMove.classList.add('active');
                    }
                }
            });
        }

        // Event listener untuk tombol Reset
        const btnReset = document.getElementById('btnReset');
        if (btnReset) {
            btnReset.addEventListener('click', function() {
                if (cropper) {
                    cropper.reset();
                    if (btnMove) {
                        btnMove.classList.remove('active');
                    }
                }
            });
        }

        // Cleanup cropper saat modal ditutup
        $('#modalCropImage').on('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('photo_profil').value = '';
            selectedFile = null;
            // Reset tombol move
            if (btnMove) {
                btnMove.classList.remove('active');
            }
        });

        // Handle form edit profil
        $('#formEditProfil').on('submit', function(e) {
            e.preventDefault();

            // Reset previous validation states
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').empty();

            $.ajax({
                url: '<?= base_url('backend/pages/updateProfil') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formEditProfil button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                const input = $('#formEditProfil [name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(message);
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat memperbarui profil'
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
                },
                complete: function() {
                    $('#formEditProfil button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan Profil');
                }
            });
        });

        // Handle form reset password
        $('#formResetPassword').on('submit', function(e) {
            e.preventDefault();

            // Reset previous validation states
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').empty();

            // Client-side validation for password match
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();

            if (newPassword !== confirmPassword) {
                $('#confirm_password').addClass('is-invalid');
                $('#confirm_password').siblings('.invalid-feedback').text('Konfirmasi password tidak cocok');
                return;
            }

            $.ajax({
                url: '<?= base_url('backend/pages/resetPassword') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formResetPassword button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengubah...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Reset form
                            $('#formResetPassword')[0].reset();
                        });
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                const input = $('#formResetPassword [name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(message);
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat mengubah password'
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
                },
                complete: function() {
                    $('#formResetPassword button[type="submit"]').prop('disabled', false).html('<i class="fas fa-key"></i> Ubah Password');
                }
            });
        });
    });

    // Toggle icon chevron untuk petunjuk crop
    $('#petunjukCropProfil').on('show.bs.collapse', function () {
        $('#iconPetunjukCropProfil').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#petunjukCropProfil').on('hide.bs.collapse', function () {
        $('#iconPetunjukCropProfil').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
</script>
<?= $this->endSection(); ?>