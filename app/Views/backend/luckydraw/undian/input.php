<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <!-- Left Column: Form and Winner List -->
    <div class="col-lg-8 col-md-12">
        <?php echo session()->getFlashdata('pesan'); ?>
        
        <!-- Input Form Card -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h4 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-gift text-primary mr-2"></i>Input Pemenang Lucky Draw
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('/backend/luckydraw/undian/store') ?>" method="POST" autocomplete="off" id="form-pemenang">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="form-group mb-3 mb-md-0">
                                <label class="font-weight-bold text-secondary">Pilih Barang Hadiah</label>
                                <select name="id_barang" id="id_barang" class="form-control form-control-lg custom-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php foreach($barang as $b): ?>
                                        <?php 
                                        $selected = ($b->id == $last_selected_id_barang && $b->sisa > 0) ? 'selected' : '';
                                        $disabled = ($b->sisa <= 0) ? 'disabled' : '';
                                        ?>
                                        <option value="<?= $b->id ?>" <?= $selected ?> <?= $disabled ?>>
                                            <?= esc($b->no_barang) ?> - <?= esc($b->nama_barang) ?> 
                                            (Sisa: <?= $b->sisa ?>/<?= $b->jumlah ?>) 
                                            <?= ($b->sisa <= 0) ? ' - [HABIS]' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-3 mb-md-0">
                                <label class="font-weight-bold text-secondary">Nomor Undian (4 Digit: 1000 - 9999)</label>
                                <input type="number" name="no_undian" class="form-control form-control-lg" min="1000" max="9999" required placeholder="Contoh: 1024" style="font-size: 1.15rem; font-weight: bold; letter-spacing: 1px;">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold shadow-sm" style="height: calc(1.5em + 1rem + 2px);">
                                <i class="fas fa-check-circle mr-1"></i>Tetapkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Winner List Card -->
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-list-ul text-success mr-2"></i>Daftar Pemenang Undian
                </h4>
                <div class="card-tools">
                    <input type="text" id="search-pemenang" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari nomor / barang..." style="width: 200px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0 text-nowrap align-middle" id="table-pemenang">
                        <thead class="bg-primary text-white sticky-top">
                            <tr>
                                <th class="py-3 px-4 text-center border-0" width="15%">No. Undian</th>
                                <th class="py-3 px-4 border-0">Barang Didapat</th>
                                <th class="py-3 px-4 text-center border-0" width="20%">Status</th>
                                <th class="py-3 px-4 text-center border-0" width="25%">Waktu Diambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pemenang)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 d-block text-light"></i>
                                        Belum ada pemenang yang diinput.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pemenang as $p) : ?>
                                    <tr>
                                        <td class="text-center py-3 px-4 align-middle">
                                            <span class="badge badge-light border border-secondary shadow-sm" style="font-size: 1.1rem; letter-spacing: 1px;">
                                                <?= esc($p->no_undian) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 align-middle">
                                            <div class="font-weight-bold text-dark"><?= esc($p->nama_barang) ?></div>
                                            <small class="text-muted">Kategori: <?= esc($p->kategori) ?></small>
                                        </td>
                                        <td class="text-center py-3 px-4 align-middle">
                                            <?php if($p->status_diambil == 1): ?>
                                                <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fas fa-check mr-1"></i>Sudah Diambil</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-clock mr-1"></i>Belum Diambil</span>
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

    <!-- Right Column: Prize Inventory Status (Counter Balance) -->
    <div class="col-lg-4 col-md-12">
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="card-title font-weight-bold m-0">
                    <i class="fas fa-chart-pie mr-2 text-warning"></i>Ketersediaan Hadiah
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <h6 class="font-weight-bold text-uppercase text-secondary mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Ringkasan Total</h6>
                    <?php 
                    $totalBarang = 0;
                    $totalSisa = 0;
                    foreach ($barang as $b) {
                        $totalBarang += $b->jumlah;
                        $totalSisa += $b->sisa;
                    }
                    $totalTerisi = $totalBarang - $totalSisa;
                    $totalPersen = ($totalBarang > 0) ? round(($totalTerisi / $totalBarang) * 100) : 0;
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Total Pemenang Terisi</span>
                        <span class="font-weight-bold text-primary"><?= $totalTerisi ?> / <?= $totalBarang ?> Hadiah</span>
                    </div>
                    <div class="progress progress-sm" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $totalPersen ?>%" aria-valuenow="<?= $totalPersen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-right mt-1">
                        <small class="text-muted font-italic"><?= $totalSisa ?> Hadiah belum terundi</small>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="font-weight-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Detail Sisa Stok & Counter Balance</h6>
                
                <div class="prize-list-scrollable" style="max-height: 450px; overflow-y: auto; padding-right: 5px;">
                    <?php foreach($barang as $b): ?>
                        <?php 
                        $terisi = $b->jumlah - $b->sisa;
                        $persen = ($b->jumlah > 0) ? round(($terisi / $b->jumlah) * 100) : 0;
                        
                        // Menentukan badge ketersediaan
                        if ($b->sisa <= 0) {
                            $badgeClass = 'badge-secondary';
                            $badgeLabel = 'Habis';
                            $progressBarClass = 'bg-secondary';
                            $textClass = 'text-muted';
                        } else if ($b->sisa <= 2) {
                            $badgeClass = 'badge-danger';
                            $badgeLabel = 'Sisa ' . $b->sisa;
                            $progressBarClass = 'bg-danger';
                            $textClass = 'text-dark font-weight-semibold';
                        } else {
                            $badgeClass = 'badge-success';
                            $badgeLabel = 'Sisa ' . $b->sisa;
                            $progressBarClass = 'bg-success';
                            $textClass = 'text-dark font-weight-semibold';
                        }
                        ?>
                        <div class="mb-3 p-3 rounded border bg-light d-flex flex-column shadow-xs transition-hover" style="border-left: 4px solid <?= ($b->sisa <= 0) ? '#6c757d' : (($b->sisa <= 2) ? '#dc3545' : '#28a745') ?> !important;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="font-weight-bold <?= $textClass ?>" style="font-size: 1rem;"><?= esc($b->nama_barang) ?></div>
                                    <small class="text-muted">Kode: <strong><?= esc($b->no_barang) ?></strong></small>
                                </div>
                                <span class="badge <?= $badgeClass ?> px-2.5 py-1.5 rounded text-uppercase font-weight-bold shadow-xs" style="font-size: 0.75rem;">
                                    <?= $badgeLabel ?>
                                </span>
                            </div>
                            
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Progres Terundi</small>
                                    <small class="font-weight-bold text-dark"><?= $terisi ?> / <?= $b->jumlah ?></small>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 3px;">
                                    <div class="progress-bar <?= $progressBarClass ?>" role="progressbar" style="width: <?= $persen ?>%" aria-valuenow="<?= $persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.transition-hover {
    transition: all 0.2s ease-in-out;
}
.transition-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}
/* Custom scrollbar for better visual alignment */
.prize-list-scrollable::-webkit-scrollbar,
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.prize-list-scrollable::-webkit-scrollbar-track,
.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.prize-list-scrollable::-webkit-scrollbar-thumb,
.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
.prize-list-scrollable::-webkit-scrollbar-thumb:hover,
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form AJAX Submit with SweetAlert
    const formPemenang = document.getElementById('form-pemenang');
    if (formPemenang) {
        formPemenang.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = formPemenang.querySelector('button[type="submit"]');
            const originalBtnHtml = btnSubmit.innerHTML;
            
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>Menyimpan...';
            btnSubmit.disabled = true;

            const formData = new FormData(formPemenang);

            fetch(formPemenang.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const noUndian = formData.get('no_undian');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Nomor undian ${noUndian} berhasil didaftarkan sebagai pemenang.`,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    btnSubmit.innerHTML = originalBtnHtml;
                    btnSubmit.disabled = false;
                    const noUndian = formData.get('no_undian');
                    Swal.fire({
                        title: 'Gagal Menyimpan!',
                        text: `Nomor undian ${noUndian} gagal disimpan karena: ${data.message}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnSubmit.innerHTML = originalBtnHtml;
                btnSubmit.disabled = false;
                const noUndian = formData.get('no_undian');
                Swal.fire({
                    title: 'Error Sistem!',
                    text: `Terjadi kesalahan saat memproses nomor undian ${noUndian}.`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Local live filter for winner list
    const searchInput = document.getElementById('search-pemenang');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#table-pemenang tbody tr');
            
            rows.forEach(row => {
                // Skip the empty state row
                if (row.cells.length === 1 && row.cells[0].colSpan === 4) return;
                
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?= $this->endSection(); ?>
