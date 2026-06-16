<?= $this->extend('frontend/template/publicTemplate'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="font-weight-bold"><i class="fas fa-list-alt text-primary mr-2"></i>Daftar Pemenang Lucky Draw</h2>
            <h5 class="text-primary"><?= isset($kegiatan) ? esc($kegiatan->nama_kegiatan) : '' ?></h5>
            <p class="text-muted">Berikut adalah daftar nomor undian yang telah mendapatkan hadiah.</p>
        </div>
    </div>
    
    <?php
    $summary = [];
    foreach ($pemenang as $p) {
        $key = $p->kategori . ' - ' . $p->nama_barang;
        if (!isset($summary[$key])) {
            $summary[$key] = ['diambil' => 0, 'belum' => 0];
        }
        if ($p->status_diambil == 1) {
            $summary[$key]['diambil']++;
        } else {
            $summary[$key]['belum']++;
        }
    }
    ?>
    
    <?php if(!empty($summary)): ?>
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white font-weight-bold">
                    <i class="fas fa-chart-pie mr-2"></i>Ringkasan Pemenang per Kategori
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped mb-0 text-center">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="align-middle">Kategori - Nama Barang</th>
                                    <th class="align-middle" width="20%"><i class="fas fa-check-circle text-success mr-1"></i>Sudah Diambil</th>
                                    <th class="align-middle" width="20%"><i class="fas fa-clock text-warning mr-1"></i>Belum Diambil</th>
                                    <th class="align-middle" width="20%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalDiambil = 0;
                                $totalBelum = 0;
                                $totalSemua = 0;
                                foreach($summary as $kategori => $stat): 
                                    $totalDiambil += $stat['diambil'];
                                    $totalBelum += $stat['belum'];
                                    $totalRow = $stat['diambil'] + $stat['belum'];
                                    $totalSemua += $totalRow;
                                ?>
                                <tr>
                                    <td class="text-left font-weight-bold text-primary"><?= esc($kategori) ?></td>
                                    <td><span class="badge badge-success" style="font-size: 1rem;"><?= $stat['diambil'] ?></span></td>
                                    <td><span class="badge badge-warning text-dark" style="font-size: 1rem;"><?= $stat['belum'] ?></span></td>
                                    <td><span class="badge badge-secondary" style="font-size: 1rem;"><?= $totalRow ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td class="text-right">TOTAL</td>
                                    <td class="text-success"><?= $totalDiambil ?></td>
                                    <td class="text-warning text-dark"><?= $totalBelum ?></td>
                                    <td class="text-secondary"><?= $totalSemua ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="listPemenangTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="py-3 px-4 text-center border-0" width="20%">No. Undian</th>
                                    <th class="py-3 px-4 border-0">Hadiah Didapat</th>
                                    <th class="py-3 px-4 text-center border-0" width="25%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pemenang)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
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
                                                <div class="font-weight-bold text-dark"><?= $p->nama_barang ?></div>
                                                <small class="text-muted">Kategori: <?= $p->kategori ?></small>
                                            </td>
                                            <td class="text-center py-3 px-4 align-middle">
                                                <?php if($p->status_diambil == 1): ?>
                                                    <span class="badge badge-success px-3 py-2 rounded-pill"><i class="fas fa-check mr-1"></i>Sudah Diambil</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-clock mr-1"></i>Belum Diambil</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('luckydraw') ?>" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-search mr-2"></i>Cek Nomor Undian Saya
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#listPemenangTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 25,
        "ordering": false // nonaktifkan auto-sort agar urutan default dari query dipertahankan
    });
});
</script>
<?= $this->endSection(); ?>
