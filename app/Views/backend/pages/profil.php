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
                                        <img id="previewPhoto" src="<?= $photoUrl ?>" alt="Foto Profil" 
                                            class="img-fluid img-thumbnail rounded-circle" 
                                            style="max-width: 200px; max-height: 200px; object-fit: cover; cursor: pointer;"
                                            onclick="document.getElementById('photo_profil').click();"
                                            title="Klik untuk mengubah foto">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="document.getElementById('photo_profil').click();">
                                                <i class="fas fa-camera"></i> Ganti Foto
                                            </button>
                                        </div>
                                        <form id="formUploadPhoto" style="display: none;">
                                            <input type="file" id="photo_profil" name="photo_profil" 
                                                accept="image/jpeg,image/jpg,image/png,image/gif" 
                                                onchange="showCropModal()">
                                        </form>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="img-container" style="min-height: 400px;">
                            <img id="imageToCrop" src="" alt="Foto untuk di-crop" style="max-width: 100%; display: block;">
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
.img-container {
    width: 100%;
    min-height: 400px;
    max-height: 500px;
    background-color: #f4f4f4;
    display: flex;
    align-items: center;
    justify-content: center;
}

.img-container > img {
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

// Pastikan Cropper.js tersedia saat halaman dimuat
$(document).ready(function() {
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

// Global function untuk menampilkan modal crop
function showCropModal() {
    const fileInput = document.getElementById('photo_profil');
    const file = fileInput.files[0];
    
    if (!file) {
        return;
    }

    // Validasi ukuran file (max 5MB untuk crop)
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
        selectedFile = file;

    // Pastikan Cropper.js sudah dimuat sebelum melanjutkan
    ensureCropperLoaded(function() {
        // Baca file sebagai URL
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageUrl = e.target.result;
            const imageElement = document.getElementById('imageToCrop');
            
            // Destroy cropper sebelumnya jika ada
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            
            // Set image source terlebih dahulu
            imageElement.src = imageUrl;
            
            // Destroy event handler sebelumnya jika ada
            $('#modalCropImage').off('shown.bs.modal');
            
            // Show modal
            $('#modalCropImage').modal('show');
            
            // Inisialisasi cropper setelah modal ditampilkan
            $('#modalCropImage').on('shown.bs.modal', function() {
                // Destroy cropper sebelumnya jika ada
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
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
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        
                        // Inisialisasi cropper
                        try {
                            cropper = new Cropper(imageElement, {
                                aspectRatio: 1, // 1:1 untuk foto profil (persegi)
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
                                minCropBoxWidth: 100,
                                minCropBoxHeight: 100,
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
                    }, 500); // Increase timeout untuk memastikan modal benar-benar rendered
                };
                
                // Trigger onload jika image sudah cached
                if (imageElement.complete) {
                    imageElement.onload();
                } else {
                    // Jika belum complete, tunggu event load
                    imageElement.addEventListener('load', imageElement.onload, { once: true });
                }
            });
        };
        reader.readAsDataURL(file);
    });
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
        width: 400,
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

// Handle button crop
$(document).ready(function() {
    $('#btnCropImage').on('click', function() {
        uploadPhoto();
    });

    // Cleanup cropper saat modal ditutup
    $('#modalCropImage').on('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('photo_profil').value = '';
        selectedFile = null;
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

