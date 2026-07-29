<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-file-invoice text-success me-2"></i> Laporan Hasil Ujian MDTA</h4>
                <small class="text-muted">Pilih jadwal ujian untuk melihat rekapitulasi nilai peserta</small>
            </div>
            <div>
                <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Jadwal
                </a>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 text-success fw-bold">
                <i class="fas fa-list me-2"></i>Daftar Ujian MDTA
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($jadwalList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum ada jadwal ujian MDTA yang dibuat</h5>
                    <a href="<?= base_url('backend/ujian-mdta/jadwal/tambah') ?>" class="btn btn-success btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i> Buat Jadwal Ujian Baru
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive p-3">
                    <table class="table table-hover table-striped align-middle mb-0" id="tabelIndexLaporan">
                        <thead class="table-success align-middle">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Ujian & Paket</th>
                                <th>Kelas</th>
                                <th>Nilai KKM</th>
                                <th>Waktu Pelaksanaan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="160">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalList as $idx => $j): ?>
                                <tr>
                                    <td class="text-center"><?= $idx + 1 ?></td>
                                    <td>
                                        <strong class="text-dark d-block"><?= esc($j['NamaUjian']) ?></strong>
                                        <small class="text-muted"><i class="fas fa-box-open me-1"></i><?= esc($j['NamaPaket'] ?? 'Paket Soal') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($j['NamaKelas'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark fw-bold"><?= number_format($j['NilaiMinimum'], 2) ?></span>
                                    </td>
                                    <td>
                                        <small class="d-block text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i><?= date('d/m/Y H:i', strtotime($j['TanggalMulai'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($j['Status'] === 'aktif'): ?>
                                            <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                                        <?php elseif ($j['Status'] === 'pause'): ?>
                                            <span class="badge bg-info text-white px-2 py-1"><i class="fas fa-pause me-1"></i>Pause</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-2 py-1"><i class="fas fa-file-alt me-1"></i>Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url("backend/ujian-mdta/laporan/{$j['id']}") ?>" class="btn btn-success btn-sm fw-semibold">
                                            <i class="fas fa-chart-line me-1"></i> Lihat Laporan
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    if ($('#tabelIndexLaporan').length) {
        $('#tabelIndexLaporan').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ jadwal",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
                "infoEmpty": "Menampilkan 0 jadwal",
                "infoFiltered": "(disaring dari _MAX_ total jadwal)",
                "zeroRecords": "Tidak ada data jadwal yang sesuai",
                "search": "Cari Jadwal:",
                "paginate": {
                    "first": "Pertama", "last": "Terakhir", "next": "Lanjut", "previous": "Sebelumnya"
                }
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>
