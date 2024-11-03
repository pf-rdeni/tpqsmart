<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lembaga Pendidikan TPQ yang ada di Kecamatan Seri Kuala Lobam</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>TTL</th>
                        <th>Tingkat</th>
                        <th>Nama Ayah</th>
                        <th>Nama Ibu</th>
                        <th>No Hp Ayah/Ibu</th>
                        <th>Alamat</th>
                        <th>Rt/Rw</th>
                        <th>Kel/Desa</th>
                        <th>NIK</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= $santri['Nama']; ?></td>
                            <td><?= $santri['JenisKelamin']; ?></td>
                            <td><?= $santri['TempatLahir'] . ", " . $santri['TanggalLahir']; ?></td>
                            <?php foreach ($dataKelas as $kelas) : ?>
                                <?php if($kelas['IdKelas'] == $santri['IdKelas']) : ?>
                                    <td><?= $kelas['NamaKelas']; ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>                           
                            <td><?= $santri['NamaAyah']; ?></td>
                            <td><?= $santri['NamaIbu']; ?></td>
                            <td><?= $santri['NoHpAyah'] . " / " . $santri['NoHpIbu']; ?></td>
                            <td><?= $santri['Alamat']; ?></td>
                            <td><?= $santri['Rt'] . " / " . $santri['Rw']; ?></td>
                            <td><?= $santri['KelurahanDesa'] ?></td>
                            <td><?= $santri['Kk'] ?></td>
                            <td><?= $santri['Status']; ?></td>
                            <td>
                                <a href="" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>TTL</th>
                        <th>Tingkat</th>
                        <th>Nama Ayah</th>
                        <th>Nama Ibu</th>
                        <th>No Hp Ayah/Ibu</th>
                        <th>Alamat</th>
                        <th>Rt/Rw</th>
                        <th>Kel/Desa</th>
                        <th>NIK</th>
                        <th>Status</th>
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