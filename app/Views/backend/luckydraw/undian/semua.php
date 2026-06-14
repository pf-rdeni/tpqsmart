<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$totalPemenang = count($pemenang);
$totalSudah = count(array_filter((array) $pemenang, fn($p) => $p->status_diambil == 1));
$totalBelum = $totalPemenang - $totalSudah;
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-lg bg-info text-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase font-weight-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Total Pemenang</h6>
                    <h2 class="font-weight-bold mb-0"><?= $totalPemenang ?></h2>
                </div>
                <div class="bg-white-10 p-3 rounded-circle">
                    <i class="fas fa-trophy fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-lg bg-success text-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 text-uppercase font-weight-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Sudah Diambil</h6>
                    <h2 class="font-weight-bold mb-0"><?= $totalSudah ?></h2>
                </div>
                <div class="bg-white-10 p-3 rounded-circle">
                    <i class="fas fa-check-double fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-lg bg-warning text-dark">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-dark-50 text-uppercase font-weight-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Belum Diambil</h6>
                    <h2 class="font-weight-bold mb-0"><?= $totalBelum ?></h2>
                </div>
                <div class="bg-dark-10 p-3 rounded-circle">
                    <i class="fas fa-clock fa-2x opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-light py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h4 class="card-title font-weight-bold text-dark m-0 mb-2 mb-md-0">
                    <i class="fas fa-history text-primary mr-2"></i>Riwayat Semua Pemenang Undian
                </h4>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="filter-status" class="form-control form-control-sm rounded-pill px-3 mr-2" style="width: 170px;">
                        <option value="">Semua Status</option>
                        <option value="belum">Belum Diambil</option>
                        <option value="sudah">Sudah Diambil</option>
                    </select>
                    <input type="text" id="search-pemenang" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari nomor / barang / grup..." style="width: 220px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0 text-nowrap align-middle" id="table-pemenang">
                        <thead class="bg-primary text-white sticky-top">
                            <tr>
                                <th class="py-3 px-4 text-center border-0" width="10%">No. Undian</th>
                                <th class="py-3 px-4 border-0" width="45%">Hadiah & Kategori</th>
                                <th class="py-3 px-4 text-center border-0" width="20%">Status</th>
                                <th class="py-3 px-4 text-center border-0" width="25%">Waktu Diambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pemenang)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 d-block text-light"></i>
                                        Belum ada riwayat pemenang undian.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pemenang as $p) : ?>
                                    <tr data-status="<?= $p->status_diambil == 1 ? 'sudah' : 'belum' ?>">
                                        <td class="text-center py-3 px-4 align-middle">
                                            <span class="badge badge-light border border-secondary shadow-sm" style="font-size: 1.1rem; letter-spacing: 1px;">
                                                <?= esc($p->no_undian) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 align-middle">
                                            <div class="font-weight-bold text-dark"><?= esc($p->nama_barang) ?></div>
                                            <small class="text-muted">Kategori / Grup: <strong><?= esc($p->kategori) ?></strong></small>
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
</div>

<style>
.bg-white-10 {
    background-color: rgba(255, 255, 255, 0.15);
}
.bg-dark-10 {
    background-color: rgba(0, 0, 0, 0.08);
}
.opacity-5 {
    opacity: 0.55;
}
.gap-2 {
    gap: 0.5rem;
}
.gap-3 {
    gap: 1rem;
}
/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-pemenang');
    const filterStatus = document.getElementById('filter-status');
    const tableRows = document.querySelectorAll('#table-pemenang tbody tr');

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = filterStatus.value;

        tableRows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan > 1) return; // Skip empty row

            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status');

            const matchesQuery = text.includes(query);
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filterTable);
    }
    if (filterStatus) {
        filterStatus.addEventListener('change', filterTable);
    }
});
</script>

<?= $this->endSection(); ?>
