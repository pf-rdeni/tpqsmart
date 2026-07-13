<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Form Upload -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload"></i> Upload Foto Kegiatan</h3>
                    </div>
                    <form id="formUploadGaleri" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Judul Kegiatan</label>
                                <input type="text" class="form-control" name="Judul" required placeholder="Contoh: Khotmil Qur'an 2026">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Kegiatan</label>
                                <input type="date" class="form-control" name="TanggalKegiatan" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label>Keterangan / Deskripsi</label>
                                <textarea class="form-control" name="Keterangan" rows="3" placeholder="Deskripsi singkat kegiatan..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Pilih Gambar</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="foto" id="inputFoto" accept="image/*" required>
                                    <label class="custom-file-label" for="inputFoto">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Maksimal file 5MB. Format: JPG, PNG, WebP.</small>
                            </div>
                            <div class="form-group d-none" id="previewContainer">
                                <label>Pratinjau Gambar:</label>
                                <div class="border rounded p-1 text-center bg-light">
                                    <img id="imagePreview" src="#" alt="Pratinjau" style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary" id="btnSubmitUpload">
                                <i class="fas fa-save"></i> Upload & Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Foto -->
            <div class="col-md-8">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-images"></i> Foto Dokumentasi Kegiatan</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($galeri)): ?>
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-image"></i> Belum ada foto kegiatan. Silakan upload foto menggunakan form di samping.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($galeri as $item): ?>
                                    <div class="col-sm-6 col-md-4 mb-4" id="galeri-card-<?= $item['Id'] ?>">
                                        <div class="card h-100 border shadow-sm">
                                            <div class="position-relative">
                                                <img src="<?= base_url('uploads/galeri/' . $item['NamaFile']) ?>" 
                                                     class="card-img-top" 
                                                     alt="<?= esc($item['Judul']) ?>" 
                                                     style="height: 160px; object-fit: cover;">
                                                <div class="position-absolute" style="top: 8px; right: 8px;">
                                                    <span class="badge badge-<?= $item['IsActive'] ? 'success' : 'secondary' ?>">
                                                        <?= $item['IsActive'] ? 'Aktif' : 'Nonaktif' ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body p-2 d-flex flex-column">
                                                <h6 class="font-weight-bold mb-1 text-truncate" title="<?= esc($item['Judul']) ?>">
                                                    <?= esc($item['Judul']) ?>
                                                </h6>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($item['TanggalKegiatan'])) ?>
                                                </small>
                                                <p class="card-text text-sm text-muted mb-2 text-truncate-2" style="height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                    <?= esc($item['Keterangan']) ?: '-' ?>
                                                </p>
                                                
                                                <div class="mt-auto d-flex justify-content-between pt-2 border-top">
                                                    <!-- Switch status -->
                                                    <div class="custom-control custom-switch align-self-center">
                                                        <input type="checkbox" class="custom-control-input switch-status-galeri" 
                                                               id="switch-galeri-<?= $item['Id'] ?>" 
                                                               data-id="<?= $item['Id'] ?>"
                                                               <?= $item['IsActive'] ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="switch-galeri-<?= $item['Id'] ?>"></label>
                                                    </div>
                                                    
                                                    <button class="btn btn-danger btn-xs btn-delete-galeri" data-id="<?= $item['Id'] ?>" title="Hapus">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- bs-custom-file-input -->
<script src="<?= base_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script>
$(document).ready(function() {
    bsCustomFileInput.init();

    // Image preview handler
    $("#inputFoto").change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#previewContainer').removeClass('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            $('#previewContainer').addClass('d-none');
        }
    });

    // Upload Handler
    $('#formUploadGaleri').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $('#btnSubmitUpload').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: '<?= base_url('backend/tv-digital/upload-galeri') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#btnSubmitUpload').prop('disabled', false).html('<i class="fas fa-save"></i> Upload & Simpan');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                $('#btnSubmitUpload').prop('disabled', false).html('<i class="fas fa-save"></i> Upload & Simpan');
            }
        });
    });

    // Switch status handler
    $('.switch-status-galeri').change(function() {
        var id = $(this).data('id');
        var isActive = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '<?= base_url('backend/tv-digital/TvDigital/updateGaleri') ?>/' + id, // Gunakan dynamic route/method default fallback controller
            type: 'POST',
            data: { IsActive: isActive },
            success: function(response) {
                if (response.status !== 'success') {
                    Swal.fire('Error', 'Gagal update status.', 'error');
                }
            }
        });
    });

    // Hapus Galeri
    $('.btn-delete-galeri').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Foto Kegiatan?',
            text: "Foto akan dihapus secara permanen dari server!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/tv-digital/delete-galeri') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                $('#galeri-card-' + id).fadeOut(500, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
