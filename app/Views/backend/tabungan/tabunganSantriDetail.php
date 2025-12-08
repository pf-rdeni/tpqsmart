<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-piggy-bank"></i> Detail Tabungan - <?= esc($santri['NamaSantri'] ?? 'Santri') ?>
                    </h3>
                    <div class="card-tools">
                        <h3 class="card-title float-right bg-primary text-white p-2 rounded mr-2">
                            Saldo: Rp. <?= number_format($saldo ?? 0, 0, ',', '.'); ?>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($dataTabungan)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada transaksi tabungan.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Nominal</th>
                                        <th>Saldo</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($dataTabungan as $transaksi): ?>
                                        <?php 
                                        $trans = is_object($transaksi) ? $transaksi : (object)$transaksi;
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <?php
                                                // Prioritaskan UpdatedAt/updated_at, lalu CreatedAt/created_at, baru TanggalTransaksi
                                                $tanggal = null;
                                                
                                                // Cek UpdatedAt (camelCase) atau updated_at (underscore)
                                                $updatedAt = $trans->UpdatedAt ?? $trans->updated_at ?? null;
                                                if (!empty($updatedAt) && $updatedAt !== '0000-00-00 00:00:00' && $updatedAt !== '0000-00-00') {
                                                    $tanggal = $updatedAt;
                                                } else {
                                                    // Cek CreatedAt (camelCase) atau created_at (underscore)
                                                    $createdAt = $trans->CreatedAt ?? $trans->created_at ?? null;
                                                    if (!empty($createdAt) && $createdAt !== '0000-00-00 00:00:00' && $createdAt !== '0000-00-00') {
                                                        $tanggal = $createdAt;
                                                    } elseif (!empty($trans->TanggalTransaksi) && $trans->TanggalTransaksi !== '0000-00-00 00:00:00' && $trans->TanggalTransaksi !== '0000-00-00') {
                                                        $tanggal = $trans->TanggalTransaksi;
                                                    }
                                                }
                                                
                                                if ($tanggal) {
                                                    $timestamp = strtotime($tanggal);
                                                    if ($timestamp !== false && $timestamp > 0) {
                                                        echo date('d-m-Y | H:i:s', $timestamp);
                                                    } else {
                                                        echo '-';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (($trans->JenisTransaksi ?? '') === 'Setoran'): ?>
                                                    <span class="badge badge-success"><?= esc($trans->JenisTransaksi ?? '') ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><?= esc($trans->JenisTransaksi ?? '') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>Rp <?= number_format($trans->Nominal ?? 0, 0, ',', '.') ?></td>
                                            <td><strong>Rp <?= number_format($trans->Saldo ?? 0, 0, ',', '.') ?></strong></td>
                                            <td><?= esc($trans->Keterangan ?? '-') ?></td>
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

