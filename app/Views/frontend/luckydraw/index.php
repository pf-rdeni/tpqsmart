<?= $this->extend('frontend/template/publicTemplate'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0 font-weight-bold"><i class="fas fa-gift mr-2"></i>Cek Undian Lucky Draw</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h4 class="text-primary font-weight-bold mb-2"><?= esc($kegiatan->nama_kegiatan) ?></h4>
                        <div class="text-muted" style="font-size: 0.95rem;">
                            <span class="mr-3"><i class="fas fa-calendar-alt text-info mr-1"></i> <?= date('d F Y', strtotime($kegiatan->tanggal_kegiatan)) ?></span>
                            <span><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($kegiatan->tempat_pelaksanaan) ?></span>
                        </div>
                    </div>
                    <p class="text-center text-muted mb-4">Silakan masukkan nomor kupon undian Anda untuk mengetahui apakah Anda beruntung mendapatkan hadiah.</p>
                    
                    <form id="search-form">
                        <div class="form-group mb-4">
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-ticket-alt text-primary"></i></span>
                                </div>
                                <input type="number" id="no_undian" class="form-control border-left-0 pl-0" placeholder="Contoh: <?= esc($kegiatan->kupon_min) ?>" min="<?= esc($kegiatan->kupon_min) ?>" max="<?= esc($kegiatan->kupon_max) ?>" required style="font-size: 1.5rem; letter-spacing: 2px; font-weight: bold; text-align: center;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm" id="btn-search">
                            <i class="fas fa-search mr-2"></i>Cek Sekarang
                        </button>
                    </form>

                    <div id="result-container" class="mt-4" style="display: none;">
                        <!-- Hasil pencarian akan dimunculkan di sini via JS -->
                    </div>
                    
                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= base_url('luckydraw/list') ?>" class="btn btn-outline-info rounded-pill px-4">
                            <i class="fas fa-list-ul mr-2"></i>Lihat Daftar Pemenang
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('/') ?>" class="text-muted"><i class="fas fa-home mr-1"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('search-form');
    const resultContainer = document.getElementById('result-container');
    const btnSearch = document.getElementById('btn-search');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const no_undian = document.getElementById('no_undian').value;
        
        // Show loading state
        btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengecek...';
        btnSearch.disabled = true;
        resultContainer.style.display = 'none';
        
        // Fetch API request
        fetch('<?= base_url('luckydraw/search') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'no_undian=' + encodeURIComponent(no_undian)
        })
        .then(response => response.json())
        .then(data => {
            btnSearch.innerHTML = '<i class="fas fa-search mr-2"></i>Cek Sekarang';
            btnSearch.disabled = false;
            
            resultContainer.style.display = 'block';
            
            if(data.is_winner) {
                const p = data.data;
                const isTaken = p.status_diambil == 1;
                
                let html = `
                    <div class="alert alert-success border-success text-center shadow-sm">
                        <h4 class="alert-heading font-weight-bold mb-3"><i class="fas fa-trophy text-warning fa-2x mb-2 d-block"></i>Selamat!</h4>
                        <p class="mb-1">Nomor undian <strong>${p.no_undian}</strong> berhasil mendapatkan:</p>
                        <h5 class="font-weight-bold text-dark my-3 p-3 bg-white rounded border">${p.nama_barang}</h5>
                `;
                
                if(isTaken) {
                    html += `<div class="badge badge-success w-100 shadow-sm" style="font-size:1.4rem; padding: 12px;"><i class="fas fa-check-circle mr-2"></i>Sudah Diambil</div>`;
                } else {
                    html += `<div class="badge badge-warning text-dark w-100 shadow-sm" style="font-size:1.4rem; padding: 12px;"><i class="fas fa-exclamation-circle mr-2"></i>Belum Diambil</div>`;
                    html += `<div class="bg-secondary text-white rounded p-2 mt-3 shadow-sm font-weight-bold" style="font-size: 1.1rem;">Silakan hubungi panitia untuk proses serah terima.</div>`;
                }
                
                html += `</div>`;
                resultContainer.innerHTML = html;
                
                // Fire sweet alert for winners
                if(!isTaken) {
                    Swal.fire({
                        title: 'Selamat!',
                        text: 'Anda mendapatkan ' + p.nama_barang,
                        icon: 'success',
                        confirmButtonText: 'Luar Biasa'
                    });
                }
                
            } else {
                resultContainer.innerHTML = `
                    <div class="alert text-center shadow" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border: 2px dashed #ced4da; border-radius: 15px; padding: 30px 20px;">
                        <div class="mb-3">
                            <i class="fas fa-smile-beam fa-4x text-info" style="opacity: 0.9; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);"></i>
                        </div>
                        <h4 class="text-info font-weight-bold mb-3" style="letter-spacing: 1px;">Tetap Semangat!</h4>
                        <p class="mb-3 text-dark" style="font-size: 1.15rem;">Nomor undian <strong class="text-primary">${no_undian}</strong> belum mendapatkan hadiah kali ini.</p>
                        <hr style="border-color: #dee2e6; width: 60%; margin: 15px auto;">
                        <p class="mb-0 text-secondary" style="font-size: 1rem; font-style: italic;">"Keberuntungan sejati adalah memiliki hati yang selalu bersyukur. Jangan berkecil hati, insyaAllah rezeki yang lebih baik sudah menanti Anda!"</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSearch.innerHTML = '<i class="fas fa-search mr-2"></i>Cek Sekarang';
            btnSearch.disabled = false;
            
            resultContainer.style.display = 'block';
            resultContainer.innerHTML = '<div class="alert alert-danger text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan. Silakan coba lagi.</div>';
        });
    });
});
</script>

<?= $this->endSection(); ?>
