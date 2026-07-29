<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-chart-pie text-warning me-2"></i> Analisis Butir Soal</h4>
            <small class="text-muted"><?= esc($jadwal['NamaUjian']) ?> — <?= esc($jadwal['NamaKelas'] ?? 'Kelas') ?></small>
        </div>
        <div>
            <a href="<?= base_url('backend/ujian-mdta/laporan-hasil') ?>" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="window.print();" class="btn btn-primary btn-sm">
                <i class="fas fa-print me-1"></i> Cetak Halaman
            </button>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card card-outline card-warning shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="row text-sm">
                <div class="col-md-3"><strong>Nama Ujian:</strong> <?= esc($jadwal['NamaUjian']) ?></div>
                <div class="col-md-3"><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($jadwal['TanggalMulai'])) ?></div>
                <div class="col-md-3"><strong>Nilai Minimal:</strong> <span class="badge bg-warning text-dark"><?= esc($jadwal['NilaiMinimum']) ?></span></div>
                <div class="col-md-3"><strong>Jumlah Soal:</strong> <?= count($soalList) ?> Soal</div>
            </div>
        </div>
    </div>

    <!-- Table Analisis Soal -->
    <div class="card card-default shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="bg-light text-center text-sm">
                        <tr>
                            <th width="50">No.</th>
                            <th width="100">Jenis</th>
                            <th>Uraian / Ringkasan Soal</th>
                            <th width="110">Dijawab</th>
                            <th width="110">Jawaban Benar</th>
                            <th width="110">Persentase Benar</th>
                            <th width="120">Tingkat Kesulitan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($soalList)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data soal pada paket ujian ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($soalList as $s): ?>
                                <?php 
                                $totalDijawab = (int)($s['total_dijawab'] ?? 0);
                                $totalBenar = (int)($s['total_benar'] ?? 0);
                                $persenBenar = $totalDijawab > 0 ? round(($totalBenar / $totalDijawab) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $s['NomorSoal'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark"><?= strtoupper(esc($s['JenisSoal'] ?? 'PG')) ?></span>
                                    </td>
                                    <td><?= strip_tags($s['UraianSoal'] ?? '-') ?></td>
                                    <td class="text-center fw-semibold"><?= $totalDijawab ?> santri</td>
                                    <td class="text-center text-success fw-bold"><?= $totalBenar ?> santri</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="fw-bold"><?= $persenBenar ?>%</span>
                                            <div class="progress" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-success" style="width: <?= $persenBenar ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($persenBenar >= 80): ?>
                                            <span class="badge bg-success">Sangat Mudah</span>
                                        <?php elseif ($persenBenar >= 60): ?>
                                            <span class="badge bg-primary">Mudah</span>
                                        <?php elseif ($persenBenar >= 40): ?>
                                            <span class="badge bg-warning text-dark">Sedang</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Sukar</span>
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
</div>

<?= $this->endSection(); ?>
