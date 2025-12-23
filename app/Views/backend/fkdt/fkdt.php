<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); 
    ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-2 col-6">
                    <a href="<?= base_url('/backend/fkdt/create/') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah FKDT</a>
                </div>

                <div class="col-lg-6 col-6">
                    <h3 class="card-title">Data FKDT (Federasi Kelompok Diniyah Takmiliyah)</h3>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID FKDT</th>
                        <th>Nama FKDT</th>
                        <th>Alamat</th>
                        <th>Kecamatan</th>
                        <th>Nama Kepala</th>
                        <th>Tempat Belajar</th>
                        <th>Tahun Berdiri</th>
                        <th>No Telpon/Hp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($fkdt)) {
                        foreach ($fkdt as $dataFkdt) : ?>
                            <tr>
                                <td><?= $dataFkdt['IdFkdt'] ?></td>
                                <td><?= $dataFkdt['NamaFkdt']  ?></td>
                                <td><?= $dataFkdt['Alamat']  ?></td>
                                <td><?= $dataFkdt['Kecamatan'] ?? '-'  ?></td>
                                <td><?= $dataFkdt['KepalaSekolah']  ?></td>
                                <td><?= $dataFkdt['TempatBelajar']  ?></td>
                                <td><?= $dataFkdt['TahunBerdiri']  ?></td>
                                <td><?= $dataFkdt['NoHp']  ?></td>
                                <td>
                                    <a href="<?= base_url('/backend/fkdt/profil-lembaga/' . $dataFkdt['IdFkdt']) ?>" class="btn btn-info btn-sm" title="Profil Lembaga"><i class="fas fa-info-circle"></i></a>
                                    <a href="<?= base_url('/backend/fkdt/edit/' . $dataFkdt['IdFkdt']) ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?= base_url('/backend/fkdt/delete/' . $dataFkdt['IdFkdt']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda Yakin Akan Hapus Data Ini')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data FKDT</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID FKDT</th>
                        <th>Nama FKDT</th>
                        <th>Alamat</th>
                        <th>Kecamatan</th>
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

