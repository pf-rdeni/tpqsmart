<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Konfirmasi Hapus Santri</h3>
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
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Informasi Santri yang Akan Dihapus</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>ID Santri:</strong> <?= $dataSantri['IdSantri'] ?><br>
                                <strong>Nama:</strong> <?= $dataSantri['NamaSantri'] ?><br>
                                <strong>Kelas:</strong> <?= $dataSantri['NamaKelas'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>TPQ:</strong> <?= $dataSantri['NamaTpq'] ?><br>
                                <strong>Tahun Ajaran:</strong> <?= $currentTahunAjaran ?><br>
                                <strong>Status Active:</strong> 
                                <span class="badge badge-<?= $dataSantri['Active'] == 1 ? 'success' : ($dataSantri['Active'] == 2 ? 'secondary' : 'warning') ?>">
                                    <?= $dataSantri['Active'] == 1 ? 'Aktif' : ($dataSantri['Active'] == 2 ? 'Alumni' : 'Non-Aktif') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peringatan Kritikal -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if ($totalNilaiRecords > 0 || $existingKelasSantri > 0 || $existingAbsensi > 0): ?>
                        <?php if ($hasExistingNilai): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> PERINGATAN KRITIS - Data Akan Dihapus Permanen</h5>
                            <p><strong>Santri ini memiliki data yang akan dihapus permanen:</strong></p>
                            <ul class="mb-3">
                                <li><strong><?= $existingNilaiCount ?> data nilai yang sudah diisi</strong> (Nilai > 0)</li>
                                <li><strong><?= $totalNilaiRecords - $existingNilaiCount ?> data nilai kosong</strong> (Nilai = 0 atau NULL)</li>
                                <li><strong>Total: <?= $totalNilaiRecords ?> data nilai</strong></li>
                                <li><strong><?= $existingKelasSantri ?> data kelas santri</strong></li>
                                <li><strong><?= $existingAbsensi ?> data absensi</strong></li>
                            </ul>
                            <p><strong>PERINGATAN:</strong> Semua data di atas akan dihapus PERMANEN dan TIDAK DAPAT DIPULIHKAN!</p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge badge-danger">KRITIS</span> - Ada data penting yang akan hilang</p>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                                <label class="form-check-label" for="confirmDelete">
                                    <strong>Saya mengerti dan setuju untuk menghapus PERMANEN semua data santri ini</strong>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-info-circle"></i> Peringatan - Data Akan Dihapus Permanen</h5>
                            <p><strong>Santri ini memiliki data yang akan dihapus permanen:</strong></p>
                            <ul class="mb-3">
                                <li><strong>0 data nilai yang sudah diisi</strong> (Nilai > 0)</li>
                                <li><strong><?= $totalNilaiRecords ?> data nilai kosong</strong> (Nilai = 0 atau NULL)</li>
                                <li><strong>Total: <?= $totalNilaiRecords ?> data nilai</strong></li>
                                <li><strong><?= $existingKelasSantri ?> data kelas santri</strong></li>
                                <li><strong><?= $existingAbsensi ?> data absensi</strong></li>
                            </ul>
                            <p><strong>PERINGATAN:</strong> Semua data di atas akan dihapus PERMANEN dan TIDAK DAPAT DIPULIHKAN!</p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge badge-warning">PERINGATAN</span> - Data akan hilang permanen</p>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                                <label class="form-check-label" for="confirmDelete">
                                    <strong>Saya mengerti dan setuju untuk menghapus PERMANEN semua data santri ini</strong>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Informasi Penghapusan</h5>
                        <p>Santri ini tidak memiliki data nilai, kelas, atau absensi di tahun ajaran <?= $currentTahunAjaran ?>.</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge badge-info">AMAN</span> - Tidak ada data terkait yang akan hilang</p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                            <label class="form-check-label" for="confirmDelete">
                                <strong>Saya mengerti dan setuju untuk menghapus data santri ini</strong>
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Konfirmasi Delete -->
            <form id="formDeleteSantri">
                <input type="hidden" name="IdSantri" value="<?= $dataSantri['IdSantri'] ?>">
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmDeleteFinal" required>
                                <label class="form-check-label" for="confirmDeleteFinal">
                                    <strong>Saya telah membaca dan memahami semua konsekuensi penghapusan data santri ini</strong>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus Permanen
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
<?= $this->section('scripts'); ?>
<style>
/* Styling untuk checkbox konfirmasi */
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

.badge-info {
    background-color: #17a2b8 !important;
    color: white !important;
    font-weight: bold;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.badge-secondary {
    background-color: #6c757d !important;
    color: white !important;
    font-weight: bold;
    padding: 0.5em 0.75em;
    border-radius: 0.375rem;
}

.badge i {
    margin-right: 0.25em;
}
</style>
<script>
// Fungsi untuk menangani button Hapus Permanen
function handleHapusPermanen() {
    console.log('Button Hapus Permanen diklik!');
    
    // Ambil nilai dari form
    const IdSantri = '<?= $dataSantri['IdSantri'] ?>';
    const NamaSantri = '<?= $dataSantri['NamaSantri'] ?>';
    const NamaKelas = '<?= $dataSantri['NamaKelas'] ?>';
    const NamaTpq = '<?= $dataSantri['NamaTpq'] ?>';
    const confirmDelete = $('#confirmDelete').is(':checked');
    const confirmDeleteFinal = $('#confirmDeleteFinal').is(':checked');
    
    // Debug: Log nilai untuk troubleshooting
    console.log('Debug Validasi:');
    console.log('IdSantri:', IdSantri);
    console.log('NamaSantri:', NamaSantri);
    console.log('confirmDelete:', confirmDelete);
    console.log('confirmDeleteFinal:', confirmDeleteFinal);
    console.log('Element #confirmDelete found:', $('#confirmDelete').length > 0);
    console.log('Element #confirmDeleteFinal found:', $('#confirmDeleteFinal').length > 0);
        
    // Validasi dasar - pastikan form tidak kosong
    if (!confirmDelete || !confirmDeleteFinal) {
        // Jika keduanya kosong, tampilkan pesan umum
        if (!confirmDelete && !confirmDeleteFinal) {
            console.log('Validasi: Form belum lengkap (keduanya kosong)');
            Swal.fire({
                title: 'Form Belum Lengkap!',
                html: `<div class="text-left">
                        <p><strong>Silakan lengkapi konfirmasi terlebih dahulu:</strong></p>
                        <ul class="text-left">
                            <li>Centang checkbox konfirmasi pertama</li>
                            <li>Centang checkbox konfirmasi kedua</li>
                        </ul>
                       </div>`,
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        // Jika hanya salah satu yang kosong, lanjut ke validasi spesifik
    }
        
    // Validasi checkbox konfirmasi pertama
    if (!confirmDelete) {
        console.log('Validasi: Checkbox konfirmasi pertama belum dicentang');
        Swal.fire({
            title: 'Konfirmasi Diperlukan!',
            html: `<div class="text-left">
                    <p><strong>Anda harus menyetujui penghapusan data santri terlebih dahulu:</strong></p>
                    <ul class="text-left">
                        <li>Centang checkbox "Saya mengerti dan setuju untuk menghapus PERMANEN semua data santri ini"</li>
                        <li>Pastikan Anda memahami konsekuensi penghapusan data</li>
                    </ul>
                    <p class="mt-3 text-danger"><strong>Perhatian:</strong> Data akan dihapus PERMANEN dan tidak dapat dipulihkan.</p>
                   </div>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    // Validasi checkbox konfirmasi kedua
    if (!confirmDeleteFinal) {
        console.log('Validasi: Checkbox konfirmasi kedua belum dicentang');
        Swal.fire({
            title: 'Konfirmasi Diperlukan!',
            html: `<div class="text-left">
                    <p><strong>Anda harus menyetujui penghapusan data santri terlebih dahulu:</strong></p>
                    <ul class="text-left">
                        <li>Centang checkbox "Saya telah membaca dan memahami semua konsekuensi penghapusan data santri ini"</li>
                        <li>Pastikan Anda memahami konsekuensi penghapusan data</li>
                    </ul>
                    <p class="mt-3 text-danger"><strong>Perhatian:</strong> Data akan dihapus PERMANEN dan tidak dapat dipulihkan.</p>
                   </div>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
        
    // Validasi tambahan - pastikan data santri valid
    if (!IdSantri || !NamaSantri) {
        console.log('Validasi: Data santri tidak valid');
        Swal.fire({
            title: 'Data Santri Tidak Valid!',
            text: 'Data santri tidak ditemukan atau tidak valid',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
        
    // Konfirmasi sebelum submit
    console.log('Semua validasi berhasil, menampilkan konfirmasi');
    Swal.fire({
        title: 'Konfirmasi Penghapusan Permanen',
        html: `<div class="text-left">
                <p><strong>Detail Santri yang Akan Dihapus:</strong></p>
                <ul class="text-left">
                    <li><strong>ID Santri:</strong> ${IdSantri}</li>
                    <li><strong>Nama:</strong> ${NamaSantri}</li>
                    <li><strong>Kelas:</strong> ${NamaKelas}</li>
                    <li><strong>TPQ:</strong> ${NamaTpq}</li>
                </ul>
                <p class="mt-3 text-danger"><strong>PERINGATAN:</strong> Tindakan ini TIDAK DAPAT DIBATALKAN!</p>
                <p class="text-danger"><strong>Semua data santri akan dihapus PERMANEN dan tidak dapat dipulihkan.</strong></p>
               </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('User mengkonfirmasi, memproses data');
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menghapus Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Siapkan form data
            const formData = new FormData();
            formData.append('IdSantri', IdSantri);
            formData.append('confirmDelete', confirmDelete ? '1' : '0');
            
            // Kirim data
            fetch('<?= base_url('backend/santri/processDeleteSantri') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                // Cek apakah response valid
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Cek apakah data response valid
                if (typeof data !== 'object' || data === null) {
                    throw new Error('Response data tidak valid');
                }
                
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `<div class="text-left">
                                <p><strong>Penghapusan santri berhasil!</strong></p>
                                <ul class="text-left">
                                    <li><strong>Nama Santri:</strong> ${NamaSantri}</li>
                                    <li><strong>ID Santri:</strong> ${IdSantri}</li>
                                    <li><strong>Status:</strong> Data telah dihapus permanen</li>
                                </ul>
                               </div>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '<?= base_url('backend/santri/showAturSantriBaru') ?>';
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        html: `<div class="text-left">
                                <p><strong>Penghapusan santri gagal:</strong></p>
                                <p>${data.message || 'Terjadi kesalahan yang tidak diketahui'}</p>
                               </div>`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    html: `<div class="text-left">
                            <p><strong>Terjadi kesalahan saat memproses data:</strong></p>
                            <p>${error.message || 'Kesalahan tidak diketahui'}</p>
                            <p class="mt-3 text-muted">Silakan coba lagi atau hubungi administrator.</p>
                           </div>`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

$(document).ready(function() {
    // Validasi checkbox konfirmasi
    $('#confirmDelete, #confirmDeleteFinal').on('change', function() {
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

    // Event handler untuk form submit (fallback)
    $('#formDeleteSantri').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submit event triggered');
        handleHapusPermanen();
    });
    
    // Event handler langsung untuk button
    $('button[type="submit"]').on('click', function(e) {
        e.preventDefault();
        console.log('Button click event triggered');
        handleHapusPermanen();
    });
});
</script>
<?= $this->endSection(); ?>
