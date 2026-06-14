<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-success text-white text-center py-4">
                <h3 class="mb-0 font-weight-bold"><i class="fas fa-check-circle mr-2"></i>Verifikasi Serah Terima</h3>
            </div>
            <div class="card-body p-4">
                <p class="text-center text-muted mb-4">Cek ketersediaan nomor undian sebelum melakukan proses serah terima.</p>
                
                <form id="form-check" method="POST">
                    <div class="form-group mb-4">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-success"></i></span>
                            </div>
                            <input type="number" id="no_undian" name="no_undian" class="form-control border-left-0 pl-0" placeholder="Masukkan 4 Digit No Undian" min="1000" max="9999" required style="font-size: 1.5rem; letter-spacing: 2px; font-weight: bold; text-align: center;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-success btn-block btn-lg font-weight-bold shadow-sm" id="btn-check">
                        <i class="fas fa-search mr-2"></i>Cek Nomor Undian
                    </button>
                </form>

                <div id="check-result-container" class="mt-4" style="display: none;">
                    <!-- Hasil pengecekan dimunculkan di sini -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-light">
                <h3 class="card-title font-weight-bold text-dark m-0"><i class="fas fa-list-ul mr-2 text-primary"></i>Daftar Pemenang</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="py-3 px-4 text-center border-0" width="15%">No. Undian</th>
                                <th class="py-3 px-4 border-0">Barang Didapat</th>
                                <th class="py-3 px-4 text-center border-0" width="20%">Status</th>
                                <th class="py-3 px-4 text-center border-0" width="20%">Waktu Diambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pemenang)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 d-block text-light"></i>
                                        Belum ada daftar pemenang.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pemenang as $p) : ?>
                                    <tr>
                                        <td class="text-center py-3 px-4 align-middle">
                                            <span class="badge badge-light border border-secondary shadow-sm" style="font-size: 1.1rem; letter-spacing: 1px;">
                                                <?= $p->no_undian ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 align-middle">
                                            <div class="font-weight-bold text-dark"><?= esc($p->nama_barang) ?></div>
                                            <small class="text-muted">Kategori: <?= esc($p->kategori) ?></small>
                                        </td>
                                        <td class="text-center py-3 px-4 align-middle">
                                            <?php if($p->status_diambil == 1): ?>
                                                <span class="badge badge-success px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-check mr-1"></i>Sudah Diambil</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-clock mr-1"></i>Belum Diambil</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center py-3 px-4 align-middle text-muted">
                                            <small><i class="far fa-calendar-alt mr-1"></i><?= $p->waktu_diambil ?: '-' ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formCheck = document.getElementById('form-check');
    const checkResultContainer = document.getElementById('check-result-container');
    const btnCheck = document.getElementById('btn-check');
    
    // Step 1: Check Nomor Undian
    formCheck.addEventListener('submit', function(e) {
        e.preventDefault();
        const no_undian = document.getElementById('no_undian').value;
        
        btnCheck.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengecek...';
        btnCheck.disabled = true;
        checkResultContainer.style.display = 'none';
        
        // Panggil endpoint public /luckydraw/search untuk mencari info pemenang
        fetch('<?= base_url('/luckydraw/search') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'no_undian=' + encodeURIComponent(no_undian)
        })
        .then(response => response.json())
        .then(data => {
            btnCheck.innerHTML = '<i class="fas fa-search mr-2"></i>Cek Nomor Undian';
            btnCheck.disabled = false;
            
            checkResultContainer.style.display = 'block';
            
            if(data.is_winner) {
                const p = data.data;
                const isTaken = p.status_diambil == 1;
                
                let html = `
                    <div class="alert alert-info border-info text-center shadow-sm">
                        <h4 class="alert-heading font-weight-bold mb-3"><i class="fas fa-info-circle mr-2"></i>Data Ditemukan</h4>
                        <p class="mb-1">Nomor undian <strong>${p.no_undian}</strong> mendapatkan hadiah:</p>
                        <h5 class="font-weight-bold text-dark my-3 p-3 bg-white rounded border">${p.nama_barang} <br><small class="text-muted">(Kategori: ${p.kategori})</small></h5>
                `;
                
                if(isTaken) {
                    html += `<div class="badge badge-success p-3 w-100 shadow-sm" style="font-size:1.1rem;"><i class="fas fa-check-circle mr-2"></i>Status: Hadiah Sudah Diambil</div>`;
                } else {
                    html += `
                        <div class="badge badge-warning p-3 w-100 shadow-sm text-dark mb-3" style="font-size:1.1rem;"><i class="fas fa-exclamation-circle mr-2"></i>Status: Hadiah Belum Diambil</div>
                        <button class="btn btn-success btn-lg btn-block font-weight-bold shadow" onclick="prosesSerahTerima('${p.no_undian}')" id="btn-konfirmasi">
                            <i class="fas fa-handshake mr-2"></i>Konfirmasi Serah Terima
                        </button>
                    `;
                }
                
                html += `</div>`;
                checkResultContainer.innerHTML = html;
            } else {
                checkResultContainer.innerHTML = `
                    <div class="alert alert-danger text-center shadow-sm">
                        <i class="fas fa-times-circle fa-2x mb-2 d-block"></i>
                        <h5 class="font-weight-bold">Tidak Tersedia</h5>
                        <p class="mb-0">Nomor undian <strong>${no_undian}</strong> tidak terdaftar sebagai pemenang.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnCheck.innerHTML = '<i class="fas fa-search mr-2"></i>Cek Nomor Undian';
            btnCheck.disabled = false;
            
            checkResultContainer.style.display = 'block';
            checkResultContainer.innerHTML = `
                <div class="alert alert-danger text-center shadow-sm">
                    <i class="fas fa-times-circle mr-2"></i>Terjadi kesalahan saat mengecek data.
                </div>
            `;
        });
    });
});

// Step 2: Proses Serah Terima (Global function)
function prosesSerahTerima(no_undian) {
    const btnKonfirmasi = document.getElementById('btn-konfirmasi');
    btnKonfirmasi.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Memproses...';
    btnKonfirmasi.disabled = true;

    fetch('<?= base_url('/backend/luckydraw/undian/serah-terima') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'no_undian=' + encodeURIComponent(no_undian)
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload(); // Reload untuk mengupdate tabel
            });
        } else {
            btnKonfirmasi.innerHTML = '<i class="fas fa-handshake mr-2"></i>Konfirmasi Serah Terima';
            btnKonfirmasi.disabled = false;
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnKonfirmasi.innerHTML = '<i class="fas fa-handshake mr-2"></i>Konfirmasi Serah Terima';
        btnKonfirmasi.disabled = false;
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan pada server.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}
</script>

<?= $this->endSection(); ?>
