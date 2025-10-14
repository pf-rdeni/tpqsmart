<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Ubah TPQ Santri (Pindah Sekolah)</h3>
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
                                <strong>Kelas:</strong> <?= $dataSantri['NamaKelas'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>TPQ Saat Ini:</strong> <?= $dataSantri['NamaTpq'] ?><br>
                                <strong>Status Active:</strong> 
                                <span class="badge badge-<?= $dataSantri['Active'] == 1 ? 'success' : 'danger' ?>">
                                    <?= $dataSantri['Active'] == 1 ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peringatan -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Peringatan Penting</h5>
                        <p><strong>Perhatian:</strong> Aksi ini akan memindahkan santri dari TPQ saat ini ke TPQ yang dipilih.</p>
                        <ul>
                            <li>Santri akan dipindahkan dari TPQ saat ini ke TPQ yang dipilih</li>
                            <li>Santri akan menjadi santri baru di TPQ tujuan</li>
                            <li>Data santri akan tetap tersimpan di TPQ tujuan</li>
                            <li>Pastikan TPQ tujuan sudah siap menerima santri ini</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Ubah TPQ -->
            <form id="formUbahTpq">
                <input type="hidden" name="IdSantri" value="<?= $dataSantri['IdSantri'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="IdTpq">Pilih TPQ Tujuan <span class="text-danger">*</span></label>
                            <select class="form-control" name="IdTpq" id="IdTpq" required>
                                <option value="">-- Pilih TPQ Tujuan --</option>
                                <?php foreach ($dataTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" 
                                        <?= ($tpq['IdTpq'] == $dataSantri['IdTpq']) ? 'disabled' : '' ?>>
                                        <?= $tpq['NamaTpq'] ?>
                                        <?= ($tpq['IdTpq'] == $dataSantri['IdTpq']) ? '(TPQ Saat Ini)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>TPQ Saat Ini</label>
                            <input type="text" class="form-control" value="<?= $dataSantri['NamaTpq'] ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmPindahTpq" required>
                                <label class="form-check-label" for="confirmPindahTpq">
                                    Saya mengerti bahwa santri akan dipindahkan ke TPQ lain dan menjadi santri baru di TPQ tujuan
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Pindahkan Santri
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
/* Styling untuk SweetAlert popup */
.swal2-popup .swal2-html-container ul {
    text-align: left !important;
    margin-left: 20px;
}

.swal2-popup .swal2-html-container li {
    margin-bottom: 8px;
}

.swal2-popup .swal2-html-container .text-left {
    text-align: left !important;
}

.swal2-popup .swal2-html-container .text-warning {
    color: #856404 !important;
    font-weight: bold;
}

.swal2-popup .swal2-html-container strong {
    color: #495057;
}
</style>
<script>
// Fungsi untuk menangani button Pindahkan Santri
function handlePindahSantri() {
    console.log('Button Pindahkan Santri diklik!');
    
    // Ambil nilai dari form
    const IdTpqBaru = $('#IdTpq').val();
    const IdTpqLama = '<?= $dataSantri['IdTpq'] ?>';
    const confirmPindahTpq = $('#confirmPindahTpq').is(':checked');
    
    // Debug: Log nilai untuk troubleshooting
    console.log('Debug Validasi:');
    console.log('IdTpqBaru:', IdTpqBaru);
    console.log('IdTpqLama:', IdTpqLama);
    console.log('confirmPindahTpq:', confirmPindahTpq);
    console.log('Element #IdTpq found:', $('#IdTpq').length > 0);
    console.log('Element #confirmPindahTpq found:', $('#confirmPindahTpq').length > 0);
        
    // Validasi dasar - pastikan form tidak kosong
    if (!IdTpqBaru || !confirmPindahTpq) {
        // Jika keduanya kosong, tampilkan pesan umum
        if (!IdTpqBaru && !confirmPindahTpq) {
            console.log('Validasi: Form belum lengkap (keduanya kosong)');
            Swal.fire({
                title: 'Form Belum Lengkap!',
                html: `<div class="text-left">
                        <p><strong>Silakan lengkapi form terlebih dahulu:</strong></p>
                        <ul class="text-left">
                            <li>Pilih TPQ tujuan dari dropdown</li>
                            <li>Centang checkbox konfirmasi</li>
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
        
    // Validasi TPQ tujuan
    if (!IdTpqBaru) {
        console.log('Validasi: TPQ tujuan belum dipilih');
        Swal.fire({
            title: 'TPQ Tujuan Belum Dipilih!',
            html: `<div class="text-left">
                    <p><strong>Silakan pilih TPQ tujuan terlebih dahulu:</strong></p>
                    <ul class="text-left">
                        <li>Klik dropdown "Pilih TPQ Tujuan"</li>
                        <li>Pilih TPQ yang akan menjadi tujuan santri</li>
                        <li>Pastikan TPQ tujuan berbeda dengan TPQ saat ini</li>
                    </ul>
                   </div>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
        
    if (IdTpqBaru == IdTpqLama) {
        console.log('Validasi: TPQ tujuan sama dengan TPQ saat ini');
        Swal.fire({
            title: 'TPQ Tujuan Tidak Valid!',
            html: `<div class="text-left">
                    <p><strong>TPQ yang dipilih sama dengan TPQ saat ini:</strong></p>
                    <ul class="text-left">
                        <li>TPQ Saat Ini: <strong><?= $dataSantri['NamaTpq'] ?></strong></li>
                        <li>TPQ Tujuan: <strong><?= $dataSantri['NamaTpq'] ?></strong></li>
                    </ul>
                    <p class="mt-3">Silakan pilih TPQ yang berbeda sebagai tujuan.</p>
                   </div>`,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
        return;
    }
    
    if (!confirmPindahTpq) {
        console.log('Validasi: Checkbox konfirmasi belum dicentang');
        Swal.fire({
            title: 'Konfirmasi Diperlukan!',
            html: `<div class="text-left">
                    <p><strong>Anda harus menyetujui perpindahan santri terlebih dahulu:</strong></p>
                    <ul class="text-left">
                        <li>Centang checkbox "Saya mengerti bahwa santri akan dipindahkan ke TPQ lain dan menjadi santri baru di TPQ tujuan"</li>
                        <li>Pastikan Anda memahami konsekuensi perpindahan santri</li>
                    </ul>
                    <p class="mt-3 text-warning"><strong>Perhatian:</strong> Santri akan menjadi santri baru di TPQ tujuan.</p>
                   </div>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
        
    // Validasi tambahan - pastikan data santri valid
    const IdSantri = '<?= $dataSantri['IdSantri'] ?>';
    const NamaSantri = '<?= $dataSantri['NamaSantri'] ?>';
    const NamaTpqLama = '<?= $dataSantri['NamaTpq'] ?>';
    
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
        title: 'Konfirmasi Pindah TPQ',
        html: `<div class="text-left">
                <p><strong>Detail Perpindahan Santri:</strong></p>
                <ul class="text-left">
                    <li><strong>Nama Santri:</strong> ${NamaSantri}</li>
                    <li><strong>TPQ Saat Ini:</strong> ${NamaTpqLama}</li>
                    <li><strong>TPQ Tujuan:</strong> ${$('#IdTpq option:selected').text()}</li>
                </ul>
                <p class="mt-3"><strong>Perhatian:</strong> Santri akan menjadi santri baru di TPQ tujuan.</p>
               </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Pindahkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('User mengkonfirmasi, memproses data');
            
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Siapkan form data
            const formData = new FormData();
            formData.append('IdSantri', IdSantri);
            formData.append('IdTpq', IdTpqBaru);
            
            // Kirim data
            fetch('<?= base_url('backend/santri/processUbahTpq') ?>', {
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
                                <p><strong>Perpindahan santri berhasil!</strong></p>
                                <ul class="text-left">
                                    <li><strong>Nama Santri:</strong> ${NamaSantri}</li>
                                    <li><strong>TPQ Tujuan:</strong> ${$('#IdTpq option:selected').text()}</li>
                                    <li><strong>Status:</strong> Santri baru di TPQ tujuan</li>
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
                                <p><strong>Perpindahan santri gagal:</strong></p>
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

// Event handler untuk button Pindahkan Santri
$(document).ready(function() {
    // Event handler untuk form submit (fallback)
    $('#formUbahTpq').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submit event triggered');
        handlePindahSantri();
    });
    
    // Event handler langsung untuk button
    $('button[type="submit"]').on('click', function(e) {
        e.preventDefault();
        console.log('Button click event triggered');
        handlePindahSantri();
    });
});
</script>
<?= $this->endSection(); ?>
