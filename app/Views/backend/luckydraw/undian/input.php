<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Input Pemenang Lucky Draw</h3>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/backend/luckydraw/undian/store') ?>" method="POST">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Pilih Barang Hadiah</label>
                            <select name="id_barang" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach($barang as $b): ?>
                                    <option value="<?= $b->id ?>"><?= $b->no_barang ?> - <?= $b->nama_barang ?> (Sisa/Jumlah: <?= $b->jumlah ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Nomor Undian (4 Digit: 1000 - 9999)</label>
                            <input type="number" name="no_undian" class="form-control" min="1000" max="9999" required placeholder="Contoh: 1024">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">Tetapkan</button>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
            <h4>Daftar Pemenang</h4>
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nomor Undian</th>
                        <th>Barang Didapat</th>
                        <th>Status</th>
                        <th>Waktu Diambil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pemenang as $p) : ?>
                        <tr>
                            <td><strong><?= $p->no_undian ?></strong></td>
                            <td><?= $p->no_barang ?> - <?= $p->nama_barang ?></td>
                            <td>
                                <?php if($p->status_diambil == 1): ?>
                                    <span class="badge badge-success">Sudah Diambil</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum Diambil</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $p->waktu_diambil ?: '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
