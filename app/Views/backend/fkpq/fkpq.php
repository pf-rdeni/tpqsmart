<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); 
    ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-2 col-6">
                    <a href="<?= base_url('/backend/fkpq/create/') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah FKPQ</a>
                </div>

                <div class="col-lg-6 col-6">
                    <h3 class="card-title">Data FKPQ (Federasi Kelompok Pengelola TPQ)</h3>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID FKPQ</th>
                        <th>Nama FKPQ</th>
                        <th>Alamat</th>
                        <th>Nama Kepala</th>
                        <th>Tempat Belajar</th>
                        <th>Tahun Berdiri</th>
                        <th>No Telpon/Hp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($fkpq)) {
                        foreach ($fkpq as $dataFkpq) : ?>
                            <tr>
                                <td><?= $dataFkpq['IdFkpq'] ?></td>
                                <td><?= $dataFkpq['NamaFkpq']  ?></td>
                                <td><?= $dataFkpq['Alamat']  ?></td>
                                <td><?= $dataFkpq['KepalaSekolah']  ?></td>
                                <td><?= $dataFkpq['TempatBelajar']  ?></td>
                                <td><?= $dataFkpq['TahunBerdiri']  ?></td>
                                <td><?= $dataFkpq['NoHp']  ?></td>
                                <td>
                                    <a href="<?= base_url('/backend/fkpq/profil-lembaga/' . $dataFkpq['IdFkpq']) ?>" class="btn btn-info btn-sm" title="Profil Lembaga"><i class="fas fa-info-circle"></i></a>
                                    <a href="<?= base_url('/backend/fkpq/edit/' . $dataFkpq['IdFkpq']) ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?= base_url('/backend/fkpq/delete/' . $dataFkpq['IdFkpq']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda Yakin Akan Hapus Data Ini')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data FKPQ</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID FKPQ</th>
                        <th>Nama FKPQ</th>
                        <th>Alamat</th>
                        <th>Nama Kepala</th>
                        <th>Tempat Belajar</th>
                        <th>Tahun Berdiri</th>
                        <th>No Telpon/Hp</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

