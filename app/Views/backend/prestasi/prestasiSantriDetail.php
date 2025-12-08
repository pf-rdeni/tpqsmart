<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy"></i> Prestasi - <?= esc($santri['NamaSantri'] ?? 'Santri') ?>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">Total: <?= $totalPrestasi ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistik Prestasi -->
                    <?php if (!empty($prestasiByJenis)): ?>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Statistik Prestasi</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($prestasiByJenis as $jenis => $jumlah): ?>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-warning"><i class="fas fa-trophy"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?= esc($jenis) ?></span>
                                                            <span class="info-box-number"><?= $jumlah ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Daftar Prestasi -->
                    <?php if (empty($prestasiList)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada data prestasi.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Prestasi</th>
                                        <th>Materi</th>
                                        <th>Kategori</th>
                                        <th>Tingkatan</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($prestasiList as $prest): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= !empty($prest['Tanggal']) ? date('d/m/Y', strtotime($prest['Tanggal'])) : '-' ?></td>
                                            <td><strong><?= esc($prest['JenisPrestasi'] ?? '-') ?></strong></td>
                                            <td><?= esc($prest['NamaMateri'] ?? '-') ?></td>
                                            <td><?= esc($prest['Kategori'] ?? '-') ?></td>
                                            <td><?= esc($prest['Tingkatan'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($prest['Status'])): ?>
                                                    <span class="badge badge-info"><?= esc($prest['Status']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($prest['Keterangan'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

