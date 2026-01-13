<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <!-- Left Column: Data Santri (Editable) -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user"></i> Data Santri</h3>
            </div>
            <div class="card-body">
                <form id="formDataSantri">
                    <input type="hidden" id="idSantri" value="<?= esc($santri['id']) ?>">
                    
                    <div class="form-group">
                        <label>No. KK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="IdKartuKeluarga" id="idKartuKeluarga" 
                            value="<?= esc($santri['IdKartuKeluarga'] ?? '') ?>" 
                            maxlength="16" pattern="[0-9]{16}" inputmode="numeric" required>
                        <small id="kkInfo" class="form-text"></small>
                    </div>
                    
                    <div class="form-group">
                        <label>NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="NikSantri" id="nikSantri" 
                            value="<?= esc($santri['NikSantri'] ?? '') ?>" 
                            maxlength="16" pattern="[0-9]{16}" inputmode="numeric" required>
                        <small id="nikInfo" class="form-text"></small>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="NamaSantri" value="<?= esc($santri['NamaSantri'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" name="JenisKelamin" required>
                            <option value="Laki-laki" <?= ($santri['JenisKelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($santri['JenisKelamin'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="TempatLahirSantri" value="<?= esc($santri['TempatLahirSantri'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="TanggalLahirSantri" value="<?= esc($santri['TanggalLahirSantri'] ?? '') ?>" required>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <label>Nama Ayah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="NamaAyah" value="<?= esc($santri['NamaAyah'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Ibu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="NamaIbu" value="<?= esc($santri['NamaIbu'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kelurahan/Desa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="KelurahanDesaSantri" value="<?= esc($santri['KelurahanDesaSantri'] ?? '') ?>" required>
                    </div>
                    
                    <button type="button" class="btn btn-info btn-sm btn-block" id="btnSimpanData">
                        <i class="fas fa-save"></i> Simpan Perubahan Data
                    </button>
                </form>
                
                <hr>
                
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-success" id="btnValid">
                        <i class="fas fa-check"></i> Data Valid
                    </button>
                    <button type="button" class="btn btn-warning" id="btnRevisi">
                        <i class="fas fa-undo"></i> Perlu Perbaikan
                    </button>
                </div>
                
                <a href="<?= base_url('backend/santri/verifikasiDataSantri') ?>" class="btn btn-secondary btn-sm btn-block mt-3">
                    <i class="fas fa-arrow-left"></i> Kembali ke List
                </a>
            </div>
        </div>
    </div>
    
    <!-- Right Column: KK Viewer -->
    <div class="col-md-8">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-image"></i> Kartu Keluarga (KK)</h3>
                <div class="card-tools">
                    <!-- Upload New KK Button -->
                    <button type="button" class="btn btn-info btn-sm" id="btnUploadKk" title="Upload KK Baru">
                        <i class="fas fa-upload"></i> Upload Baru
                    </button>
                    <?php if ($kkImageUrl): ?>
                    <button type="button" class="btn btn-warning btn-sm" id="btnEditKk" title="Edit KK">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnSaveKk" title="Simpan KK" style="display: none;">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnCancelEdit" title="Batal Edit" style="display: none;">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-2">
                <?php if ($kkError): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= esc($kkError) ?>
                    </div>
                    <!-- Show upload option when no file exists -->
                    <div class="text-center py-4">
                        <button type="button" class="btn btn-primary" id="btnUploadKkAlt">
                            <i class="fas fa-upload"></i> Upload File KK
                        </button>
                    </div>
                <?php elseif ($kkImageUrl): ?>
                    <div id="kkViewerContainer" style="position: relative; width: 100%; min-height: 400px; max-height: 70vh; background: #f4f4f4; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: grab;">
                        <img id="kkImage" src="<?= $kkImageUrl ?>" alt="Kartu Keluarga" style="max-width: 100%; max-height: 70vh; transform-origin: center center; transition: transform 0.1s ease;">
                    </div>
                    <!-- View Mode Controls (Always visible) -->
                    <div id="viewControls" class="mt-2 text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomIn" title="Zoom In">
                                <i class="fas fa-search-plus"></i> Zoom In
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomOut" title="Zoom Out">
                                <i class="fas fa-search-minus"></i> Zoom Out
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetView" title="Reset View">
                                <i class="fas fa-compress-arrows-alt"></i> Reset
                            </button>
                        </div>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Scroll untuk zoom, drag untuk geser
                        </small>
                    </div>
                    <!-- Edit Mode Controls (Hidden by default) -->
                    <div id="cropControls" class="mt-2 text-center" style="display: none;">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm" id="btnRotateLeft" title="Putar Kiri">
                                <i class="fas fa-undo"></i> Putar Kiri
                            </button>
                            <button type="button" class="btn btn-info btn-sm" id="btnRotateRight" title="Putar Kanan">
                                <i class="fas fa-redo"></i> Putar Kanan
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnReset" title="Reset">
                                <i class="fas fa-sync"></i> Reset
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> File KK tidak tersedia
                    </div>
                    <div class="text-center py-4">
                        <button type="button" class="btn btn-primary" id="btnUploadKkAlt">
                            <i class="fas fa-upload"></i> Upload File KK
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload KK -->
<div class="modal fade" id="modalUploadKk" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white"><i class="fas fa-upload"></i> Upload Kartu Keluarga</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUploadKk">
                    <div class="form-group">
                        <label>Pilih File KK</label>
                        <input type="file" class="form-control-file" id="fileKk" accept="image/jpeg,image/jpg,image/png,application/pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Maks 10MB</small>
                    </div>
                    <div id="previewUploadKk" class="text-center mt-3" style="display: none;">
                        <img id="imgPreviewKk" src="" alt="Preview" style="max-width: 100%; max-height: 300px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSubmitUploadKk">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
let cropper = null;
let isEditMode = false;
const idSantri = document.getElementById('idSantri').value;
let nikValid = true;

// NIK Validation - Auto check on input
const nikInput = document.getElementById('nikSantri');
const nikInfo = document.getElementById('nikInfo');
const kkInput = document.getElementById('idKartuKeluarga');
const kkInfo = document.getElementById('kkInfo');

// Only allow numeric input
nikInput?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
    validateNikFormat();
    if (this.value.length === 16) {
        checkNikUniqueness();
    } else {
        nikInfo.textContent = '';
        nikInfo.className = 'form-text';
    }
});

kkInput?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
    validateKkFormat();
});

function validateNikFormat() {
    if (nikInput.value.length > 0 && nikInput.value.length < 16) {
        nikInfo.textContent = `NIK harus 16 digit (${nikInput.value.length}/16)`;
        nikInfo.className = 'form-text text-warning';
        nikInput.classList.remove('is-valid', 'is-invalid');
        nikInput.classList.add('is-invalid');
        nikValid = false;
    } else if (nikInput.value.length === 16) {
        nikInput.classList.remove('is-invalid');
    }
}

function validateKkFormat() {
    if (kkInput.value.length > 0 && kkInput.value.length < 16) {
        kkInfo.textContent = `No. KK harus 16 digit (${kkInput.value.length}/16)`;
        kkInfo.className = 'form-text text-warning';
        kkInput.classList.remove('is-valid', 'is-invalid');
        kkInput.classList.add('is-invalid');
    } else if (kkInput.value.length === 16) {
        kkInfo.textContent = '✓ Format No. KK valid';
        kkInfo.className = 'form-text text-success';
        kkInput.classList.remove('is-invalid');
        kkInput.classList.add('is-valid');
    } else {
        kkInfo.textContent = '';
        kkInput.classList.remove('is-valid', 'is-invalid');
    }
}

function checkNikUniqueness() {
    const nik = nikInput.value;
    if (nik.length !== 16) return;
    
    nikInfo.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memeriksa NIK...';
    nikInfo.className = 'form-text text-info';
    
    fetch('<?= base_url('backend/santri/checkNikUnique') ?>', {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `nik=${nik}&idSantri=${idSantri}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.isUnique) {
            nikInfo.innerHTML = '✓ NIK valid dan tersedia';
            nikInfo.className = 'form-text text-success';
            nikInput.classList.remove('is-invalid');
            nikInput.classList.add('is-valid');
            nikValid = true;
        } else {
            nikInfo.innerHTML = `<i class="fas fa-exclamation-triangle"></i> NIK sudah digunakan oleh: <strong>${data.usedBy || 'santri lain'}</strong>`;
            nikInfo.className = 'form-text text-danger';
            nikInput.classList.remove('is-valid');
            nikInput.classList.add('is-invalid');
            nikValid = false;
        }
    })
    .catch(err => {
        nikInfo.textContent = 'Gagal memeriksa NIK';
        nikInfo.className = 'form-text text-warning';
        nikValid = true; // Allow save on error
    });
}

// View mode zoom/pan variables
let currentZoom = 1;
let isDragging = false;
let startX, startY, translateX = 0, translateY = 0;
const kkImage = document.getElementById('kkImage');
const kkContainer = document.getElementById('kkViewerContainer');

<?php if ($kkImageUrl): ?>
// Zoom with scroll wheel
if (kkContainer) {
    kkContainer.addEventListener('wheel', function(e) {
        if (isEditMode) return; // Don't zoom in edit mode
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        currentZoom = Math.min(Math.max(0.5, currentZoom + delta), 5);
        updateImageTransform();
    });

    // Drag to pan
    kkContainer.addEventListener('mousedown', function(e) {
        if (isEditMode) return;
        isDragging = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        kkContainer.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging || isEditMode) return;
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        updateImageTransform();
    });

    document.addEventListener('mouseup', function() {
        isDragging = false;
        if (kkContainer) kkContainer.style.cursor = 'grab';
    });
}

function updateImageTransform() {
    if (kkImage) {
        kkImage.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
    }
}

// Zoom In Button
document.getElementById('btnZoomIn').addEventListener('click', function() {
    currentZoom = Math.min(currentZoom + 0.25, 5);
    updateImageTransform();
});

// Zoom Out Button
document.getElementById('btnZoomOut').addEventListener('click', function() {
    currentZoom = Math.max(currentZoom - 0.25, 0.5);
    updateImageTransform();
});

// Reset View Button
document.getElementById('btnResetView').addEventListener('click', function() {
    currentZoom = 1;
    translateX = 0;
    translateY = 0;
    updateImageTransform();
});
<?php endif; ?>

// Upload KK Modal
function openUploadModal() {
    document.getElementById('fileKk').value = '';
    document.getElementById('previewUploadKk').style.display = 'none';
    $('#modalUploadKk').modal('show');
}

document.getElementById('btnUploadKk')?.addEventListener('click', openUploadModal);
document.getElementById('btnUploadKkAlt')?.addEventListener('click', openUploadModal);

// Preview uploaded file
document.getElementById('fileKk')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('imgPreviewKk').src = ev.target.result;
                document.getElementById('previewUploadKk').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('previewUploadKk').style.display = 'none';
        }
    }
});

// Submit Upload KK
document.getElementById('btnSubmitUploadKk')?.addEventListener('click', function() {
    const fileInput = document.getElementById('fileKk');
    if (!fileInput.files[0]) {
        Swal.fire('Error', 'Pilih file terlebih dahulu', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('idSantri', idSantri);
    formData.append('fileKk', fileInput.files[0]);

    Swal.fire({
        title: 'Mengupload...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('<?= base_url('backend/santri/uploadNewKk') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.status === 'success') {
            Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
});

// Simpan Data Santri
document.getElementById('btnSimpanData').addEventListener('click', function() {
    // Validate required fields
    const form = document.getElementById('formDataSantri');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Check NIK format (16 digits)
    const nikValue = nikInput?.value || '';
    if (nikValue.length !== 16) {
        Swal.fire('Error', 'NIK harus 16 digit', 'error');
        nikInput?.focus();
        return;
    }
    
    // Check KK format (16 digits)
    const kkValue = kkInput?.value || '';
    if (kkValue.length !== 16) {
        Swal.fire('Error', 'No. KK harus 16 digit', 'error');
        kkInput?.focus();
        return;
    }
    
    // Check NIK validity (not duplicate)
    if (!nikValid) {
        Swal.fire('Error', 'NIK sudah digunakan oleh santri lain. Silakan gunakan NIK yang berbeda.', 'error');
        nikInput?.focus();
        return;
    }
    
    const formData = new FormData(form);
    formData.append('idSantri', idSantri);
    
    fetch('<?= base_url('backend/santri/updateDataSantri') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire('Berhasil', data.message, 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
});

// Update Status - Valid
document.getElementById('btnValid').addEventListener('click', function() {
    updateStatus('1');
});

// Update Status - Revisi
document.getElementById('btnRevisi').addEventListener('click', function() {
    updateStatus('2');
});

function updateStatus(status) {
    const statusLabel = status === '1' ? 'Valid' : 'Perlu Perbaikan';
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin mengubah status menjadi ${statusLabel}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('idSantri', idSantri);
            formData.append('status', status);
            
            fetch('<?= base_url('backend/santri/processVerifikasi') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Berhasil', data.message, 'success').then(() => {
                        window.location.href = '<?= base_url('backend/santri/verifikasiDataSantri') ?>';
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
        }
    });
}

<?php if ($kkImageUrl): ?>
// Edit KK - Initialize Cropper
document.getElementById('btnEditKk').addEventListener('click', function() {
    isEditMode = true;
    // Reset view transforms before entering edit mode
    currentZoom = 1;
    translateX = 0;
    translateY = 0;
    if (kkImage) kkImage.style.transform = '';
    
    this.style.display = 'none';
    document.getElementById('btnUploadKk').style.display = 'none';
    document.getElementById('btnSaveKk').style.display = 'inline-block';
    document.getElementById('btnCancelEdit').style.display = 'inline-block';
    document.getElementById('cropControls').style.display = 'block';
    document.getElementById('viewControls').style.display = 'none';
    
    cropper = new Cropper(kkImage, {
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
    });
});

// Cancel Edit
document.getElementById('btnCancelEdit').addEventListener('click', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    isEditMode = false;
    document.getElementById('btnEditKk').style.display = 'inline-block';
    document.getElementById('btnUploadKk').style.display = 'inline-block';
    document.getElementById('btnSaveKk').style.display = 'none';
    document.getElementById('btnCancelEdit').style.display = 'none';
    document.getElementById('cropControls').style.display = 'none';
    document.getElementById('viewControls').style.display = 'block';
});

// Save KK
document.getElementById('btnSaveKk').addEventListener('click', function() {
    if (!cropper) {
        Swal.fire('Error', 'Cropper tidak aktif', 'error');
        return;
    }
    
    const canvas = cropper.getCroppedCanvas({
        maxWidth: 2000,
        maxHeight: 2000,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });
    
    const croppedImageData = canvas.toDataURL('image/jpeg', 0.9);
    
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData();
    formData.append('idSantri', idSantri);
    formData.append('croppedImageData', croppedImageData);
    
    fetch('<?= base_url('backend/santri/updateFileKk') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.status === 'success') {
            Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
});

// Rotate Left
document.getElementById('btnRotateLeft').addEventListener('click', function() {
    if (cropper) cropper.rotate(-90);
});

// Rotate Right
document.getElementById('btnRotateRight').addEventListener('click', function() {
    if (cropper) cropper.rotate(90);
});

// Reset
document.getElementById('btnReset').addEventListener('click', function() {
    if (cropper) cropper.reset();
});
<?php endif; ?>
</script>
<?= $this->endSection(); ?>
