<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Ubah Kelas Santri</h3>
                <a href="<?= base_url('backend/santri/showAturSantriBaru') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Informasi Santri -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-user"></i> Informasi Santri</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>ID Santri:</strong> <?= $dataSantri['IdSantri'] ?><br>
                                <strong>Nama:</strong> <?= $dataSantri['NamaSantri'] ?><br>
                                <strong>TPQ:</strong> <?= $dataSantri['NamaTpq'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Kelas Saat Ini:</strong> <?= $dataSantri['NamaKelas'] ?><br>
                                <strong>Tahun Ajaran:</strong> <?= $currentTahunAjaran ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi kondisi data nilai -->
            <?php if ($totalNilaiRecords > 0): ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if ($hasExistingNilai): ?>
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Peringatan Kritis</h5>
                        <p>Santri ini memiliki data nilai di tahun ajaran <?= $currentTahunAjaran ?>:</p>
                        <ul class="mb-3">
                            <li><strong><?= $existingNilaiCount ?> data nilai yang sudah diisi</strong> (Nilai > 0)</li>
                            <li><strong><?= $totalNilaiRecords - $existingNilaiCount ?> data nilai kosong</strong> (Nilai = 0 atau NULL)</li>
                            <li><strong>Total: <?= $totalNilaiRecords ?> data nilai</strong></li>
                        </ul>
                        <p><strong>Perhatian:</strong> Jika Anda mengubah kelas, <strong>SEMUA <?= $totalNilaiRecords ?> data nilai</strong> akan dihapus dan diganti dengan data nilai kosong untuk kelas yang baru.</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge badge-danger">KRITIS</span> - Ada nilai yang sudah diisi</p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="confirmDeleteNilai" required>
                            <label class="form-check-label" for="confirmDeleteNilai">
                                <strong>Saya mengerti dan setuju untuk menghapus semua <?= $totalNilaiRecords ?> data nilai</strong>
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle"></i> Informasi Data Nilai</h5>
                        <p>Santri ini memiliki data nilai di tahun ajaran <?= $currentTahunAjaran ?>:</p>
                        <ul class="mb-3">
                            <li><strong>0 data nilai yang sudah diisi</strong> (Nilai > 0)</li>
                            <li><strong><?= $totalNilaiRecords ?> data nilai kosong</strong> (Nilai = 0 atau NULL)</li>
                            <li><strong>Total: <?= $totalNilaiRecords ?> data nilai</strong></li>
                        </ul>
                        <p><strong>Perhatian:</strong> Jika Anda mengubah kelas, <strong>SEMUA <?= $totalNilaiRecords ?> data nilai</strong> akan dihapus dan diganti dengan data nilai kosong untuk kelas yang baru.</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge badge-success"><i class="fas fa-shield-alt"></i> AMAN</span> - Tidak ada nilai yang sudah diisi</p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="confirmDeleteNilai" required>
                            <label class="form-check-label" for="confirmDeleteNilai">
                                <strong>Saya mengerti dan setuju untuk menghapus semua <?= $totalNilaiRecords ?> data nilai</strong>
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Status Aman</h5>
                        <p>Santri ini tidak memiliki data nilai di tahun ajaran <?= $currentTahunAjaran ?>.</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge badge-success"><i class="fas fa-shield-alt"></i> AMAN</span> - Tidak ada data nilai</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Form Ubah Kelas -->
            <form id="formUbahKelas">
                <input type="hidden" name="IdSantri" value="<?= $dataSantri['IdSantri'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="IdKelas">Pilih Kelas Baru <span class="text-danger">*</span></label>
                            <select class="form-control" name="IdKelas" id="IdKelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($dataKelas as $kelas): ?>
                                    <option value="<?= $kelas['IdKelas'] ?>" 
                                        <?= ($kelas['IdKelas'] == $dataSantri['IdKelas']) ? 'disabled' : '' ?>>
                                        <?= $kelas['NamaKelas'] ?>
                                        <?= ($kelas['IdKelas'] == $dataSantri['IdKelas']) ? '(Kelas Saat Ini)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kelas Saat Ini</label>
                            <input type="text" class="form-control" value="<?= $dataSantri['NamaKelas'] ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('backend/santri/showAturSantriBaru') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<?= $this->endSection(); ?>
<?= $this->section('styles'); ?>
<style>
    .form-check-input:required:invalid {
        border-color: #dc3545;
    }
    
    .form-check-input:required:valid {
        border-color: #28a745;
    }
    
    .form-check-label .text-danger {
        font-size: 1.2em;
        font-weight: bold;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Styling untuk badge status */
    .badge-success {
        background-color: #28a745 !important;
        color: white !important;
        font-weight: bold;
        padding: 0.5em 0.75em;
        border-radius: 0.375rem;
    }
    
    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: bold;
        padding: 0.5em 0.75em;
        border-radius: 0.375rem;
    }
    
    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
        font-weight: bold;
        padding: 0.5em 0.75em;
        border-radius: 0.375rem;
    }
    
    .badge i {
        margin-right: 0.25em;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Event handler untuk checkbox konfirmasi
    $('#confirmDeleteNilai').on('change', function() {
        if (this.checked) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $(this).closest('.form-check').find('.invalid-feedback').remove();
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            if (!$(this).closest('.form-check').find('.invalid-feedback').length) {
                $(this).closest('.form-check').append('<div class="invalid-feedback">Checkbox ini wajib dicentang</div>');
            }
        }
    });

    $('#formUbahKelas').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const IdKelasBaru = $('#IdKelas').val();
        const IdKelasLama = '<?= $dataSantri['IdKelas'] ?>';
        const hasExistingNilai = <?= $hasExistingNilai ? 'true' : 'false' ?>;
        const confirmDeleteNilai = $('#confirmDeleteNilai').is(':checked');
        
        // Validasi
        if (!IdKelasBaru) {
            Swal.fire({
                title: 'Error!',
                text: 'Silakan pilih kelas baru',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (IdKelasBaru == IdKelasLama) {
            Swal.fire({
                title: 'Error!',
                text: 'Kelas yang dipilih sama dengan kelas saat ini',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Validasi checkbox konfirmasi jika ada data nilai
        if (<?= $totalNilaiRecords ?> > 0 && !confirmDeleteNilai) {
            let alertMessage = 'Anda harus mencentang checkbox "Saya mengerti dan setuju" terlebih dahulu.';
            if (hasExistingNilai) {
                alertMessage += `\n\n⚠️ PERINGATAN: Ada <?= $existingNilaiCount ?> data nilai yang sudah diisi yang akan dihapus.`;
            }
            alertMessage += `\n\nTotal <?= $totalNilaiRecords ?> data nilai akan dihapus.`;
            
            Swal.fire({
                title: 'Konfirmasi Diperlukan!',
                text: alertMessage,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Konfirmasi sebelum submit
        let confirmMessage = `Apakah Anda yakin ingin mengubah kelas santri menjadi kelas yang dipilih?`;
        if (hasExistingNilai) {
            confirmMessage += `\n\n⚠️ PERINGATAN KRITIS: SEMUA data nilai akan dihapus (termasuk yang sudah diisi dan yang kosong) dan diganti dengan data nilai kosong untuk kelas baru.`;
        } else {
            confirmMessage += `\n\nℹ️ INFORMASI: SEMUA data nilai akan dihapus dan diganti dengan data nilai kosong untuk kelas baru.`;
        }
        
        Swal.fire({
            title: 'Konfirmasi Perubahan Kelas',
            text: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah Kelas',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Kirim data
                fetch('<?= base_url('backend/santri/processUbahKelas') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '<?= base_url('backend/santri/showAturSantriBaru') ?>';
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memproses data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
