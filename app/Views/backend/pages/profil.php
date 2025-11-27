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
                                        // Cek apakah ada foto profil yang valid
                                        $hasValidPhoto = false;
                                        $photoUrl = '';
                                        
                                        if (!empty($user['user_image']) && $user['user_image'] !== 'default.svg') {
                                            $imagePath = FCPATH . 'uploads/profil/user/' . $user['user_image'];
                                            if (file_exists($imagePath)) {
                                                $hasValidPhoto = true;
                                                $photoUrl = base_url('uploads/profil/user/' . $user['user_image']);
                                            }
                                        }
                                        
                                        // Jika tidak ada foto valid, gunakan icon Font Awesome
                                        if (!$hasValidPhoto) {
                                            $photoUrl = ''; // Kosongkan untuk trigger icon
                                        }
                                        ?>
                                        <label class="text-center w-100">Photo Profil</label>
                                        <div class="text-center">
                                            <?php if ($hasValidPhoto): ?>
                                                <img id="previewPhoto" src="<?= $photoUrl ?>" alt="Preview Photo"
                                                    class="img-thumbnail mx-auto d-block" style="width: 100%; max-width: 215px; height: auto; min-height: 280px; object-fit: cover;"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <i id="previewPhotoIcon" class="fas fa-user-circle mx-auto d-block" 
                                                   style="display: none; font-size: 215px; color: #6c757d; max-width: 215px; height: auto; min-height: 280px;"></i>
                                            <?php else: ?>
                                                <i id="previewPhotoIcon" class="fas fa-user-circle mx-auto d-block" 
                                                   style="font-size: 215px; color: #6c757d; max-width: 215px; height: auto; min-height: 280px;"></i>
                                                <img id="previewPhoto" src="" alt="Preview Photo"
                                                    class="img-thumbnail mx-auto d-block" style="display: none; width: 100%; max-width: 215px; height: auto; min-height: 280px; object-fit: cover;">
                                            <?php endif; ?>
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
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Petunjuk Crop Foto Profil</h5>
                    <ul class="mb-0">
                        <li><strong>Geser dan sesuaikan posisi foto</strong> dengan mengklik dan menyeret area crop (kotak biru) atau gunakan tombol kontrol di bawah</li>
                        <li><strong>Zoom in/out</strong> dengan menggunakan scroll mouse, pinch gesture pada touchscreen, atau tombol zoom</li>
                        <li><strong>Rasio foto 3:4</strong> - Pastikan wajah berada di tengah dan terlihat jelas</li>
                        <li><strong>Direkomendasikan:</strong> Foto dengan latar belakang merah, wajah menghadap ke depan, dan pencahayaan yang cukup</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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

.img-container-crop > img {
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

// Pastikan Cropper.js sudah dimuat
function ensureCropperLoaded(callback) {
    // Cek langsung apakah sudah tersedia
    if (typeof Cropper !== 'undefined' && typeof Cropper === 'function') {
        callback();
        return;
    }
    
    // Jika belum, tunggu dengan polling
    let attempts = 0;
    const maxAttempts = 50; // 50 x 100ms = 5 detik
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

// Fungsi untuk mengecek dan menampilkan/menyembunyikan button Edit
function toggleEditButton() {
    const previewPhoto = document.getElementById('previewPhoto');
    const previewPhotoIcon = document.getElementById('previewPhotoIcon');
    const btnEditPhoto = document.getElementById('btnEditPhoto');
    const editPhotoHint = document.getElementById('editPhotoHint');
    
    // Cek apakah ada foto yang valid (bukan icon)
    const hasValidPhoto = previewPhoto && previewPhoto.src && 
                         previewPhoto.style.display !== 'none' && 
                         !previewPhoto.src.includes('no-photo.jpg') &&
                         previewPhoto.src.trim() !== '';
    
    if (hasValidPhoto) {
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
        // Validasi ukuran (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            if (errorDiv) {
                errorDiv.innerHTML = 'Ukuran file ' + file.name + ' (' + (file.size / (1024 * 1024)).toFixed(5) + ' MB) terlalu besar (maksimal 5MB)';
                errorDiv.style.display = 'block';
            }
            return;
        }

        // Validasi tipe file
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            if (errorDiv) {
                errorDiv.innerHTML = 'Format file tidak valid (gunakan JPG, JPEG, atau PNG)';
                errorDiv.style.display = 'block';
            }
            return;
        }

        selectedFile = file;
    } else {
        selectedFile = null;
    }

    ensureCropperLoaded(function() {
        const imageElement = document.getElementById('imageToCrop');

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        // Jika ada file, baca sebagai data URL
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const finalImageUrl = e.target.result;
                initializeCropper(finalImageUrl);
            };
            reader.readAsDataURL(file);
        } else if (imageUrl) {
            // Jika menggunakan URL yang sudah ada, langsung gunakan
            initializeCropper(imageUrl);
        }
    });
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
    const currentPhotoUrl = previewPhoto ? previewPhoto.src : '';
    
    // Cek apakah foto valid dan bukan icon
    const hasValidPhoto = currentPhotoUrl && 
                         currentPhotoUrl.trim() !== '' &&
                         !currentPhotoUrl.includes('no-photo.jpg') &&
                         previewPhoto.style.display !== 'none';
    
    if (hasValidPhoto) {
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

    // Get cropped canvas
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 400,
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
            const preview = document.getElementById('previewPhoto');
            const photoInput = document.getElementById('photo_profil');
            const errorDiv = document.getElementById('photo_profilError');

            // Update preview - sembunyikan icon dan tampilkan gambar
            const previewIcon = document.getElementById('previewPhotoIcon');
            if (previewIcon) {
                previewIcon.style.display = 'none';
            }
            preview.src = base64Image;
            preview.style.display = 'block';
            preview.style.border = '2px solid #28a745';
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }

            // Upload dengan AJAX
            const formData = new FormData();
            formData.append('photo_profil_cropped', base64Image);

            // Show loading
            Swal.fire({
                title: 'Mengupload...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                            title: 'Berhasil',
                            text: response.message,
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
        reader.readAsDataURL(blob);
    }, 'image/jpeg', 0.9); // JPEG dengan quality 90%
}

// Fungsi untuk membuka kamera
function openCamera() {
    // Cek apakah browser mendukung getUserMedia
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Buat elemen video untuk preview kamera
        const videoPreview = document.createElement('video');
        videoPreview.autoplay = true;

        // Buat modal untuk menampilkan preview kamera
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;';

        // Tambahkan video ke modal
        modal.appendChild(videoPreview);

        // Tambahkan tombol ambil foto
        const captureBtn = document.createElement('button');
        captureBtn.textContent = 'Ambil Foto';
        captureBtn.className = 'btn btn-primary mt-3';
        modal.appendChild(captureBtn);

        // Tambahkan tombol tutup
        const closeBtn = document.createElement('button');
        closeBtn.textContent = 'Tutup';
        closeBtn.className = 'btn btn-secondary mt-2';
        modal.appendChild(closeBtn);

        // Tambahkan modal ke body
        document.body.appendChild(modal);

        // Minta akses kamera
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                videoPreview.srcObject = stream;

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
                        stream.getTracks().forEach(track => track.stop());
                        document.body.removeChild(modal);

                        // Tampilkan modal crop
                        showCropModalProfil(file);
                    }, 'image/jpeg');
                };

                // Handler untuk tombol tutup
                closeBtn.onclick = () => {
                    stream.getTracks().forEach(track => track.stop());
                    document.body.removeChild(modal);
                };
            })
            .catch(error => {
                console.error('Error accessing camera:', error);
                alert('Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                document.body.removeChild(modal);
            });
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
</script>
<?= $this->endSection(); ?>

