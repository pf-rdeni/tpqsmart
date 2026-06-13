<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-2 col-6">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahBarang">
                        Tambah Barang
                    </button>
                </div>
                <div class="col-lg-6 col-6">
                    <h3 class="card-title">Data Barang Lucky Draw</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No Barang</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $b) : ?>
                        <tr>
                            <td><?= $b->no_barang ?></td>
                            <td><?= $b->kategori ?></td>
                            <td><?= $b->nama_barang ?></td>
                            <td><?= $b->jumlah ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalEditBarang<?= $b->id ?>"><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('/backend/luckydraw/barang/delete/' . $b->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        
                        <!-- Modal Edit -->
                        <div class="modal fade" id="ModalEditBarang<?= $b->id ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Barang</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form action="<?= base_url('/backend/luckydraw/barang/update/' . $b->id) ?>" method="POST">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>No Barang</label>
                                                <input type="text" name="no_barang" class="form-control" value="<?= $b->no_barang ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Kategori</label>
                                                <input type="text" name="kategori" class="form-control" value="<?= $b->kategori ?>" required placeholder="Contoh: Elektronik, Hiburan, dll.">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control" value="<?= $b->nama_barang ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control" value="<?= $b->jumlah ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="ModalTambahBarang" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Barang Lucky Draw</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('/backend/luckydraw/barang/store') ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>No Barang</label>
                        <input type="text" name="no_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="kategori" class="form-control" required placeholder="Contoh: Elektronik, Hiburan, dll.">
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
