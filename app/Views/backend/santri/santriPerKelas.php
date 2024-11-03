<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri TPQ Per Kelas</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Wali Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Tingkat Kelas</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Nilai Semester Ganjil</th>
                        <th>Nilai Semester Genap</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td><?php echo $santri->GuruNama; ?></td>
                            <td><?php echo $santri->IdTahunAjaran; ?></td>
                            <td><?php echo $santri->NamaKelas; ?></td>
                            <td><?php echo $santri->SantriNama; ?></td>
                            <td><?php echo $santri->JenisKelamin; ?></td>
                            <td>
                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 1 .'/'.true .'/'. $santri->IdJabatan) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 1) ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                            </td>
                            <td>
                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 2 .'/'.true .'/'. $santri->IdJabatan) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 2) ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Wali Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Tingkat Kelas</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Nilai Semester Ganjil</th>
                        <th>Nilai Semester Genap</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>