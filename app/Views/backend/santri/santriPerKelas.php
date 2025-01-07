<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri TPQ Per Kelas</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="TableNilaiSemester" class="table table-bordered table-striped">
                <thead>
                    <?php $headerFooter = '
                    <tr>
                        <th>Nilai S.Ganjil</th>
                        <th>Nilai S.Genap</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Tahun Ajaran</th>
                        <th>Tingkat Kelas</th>
                    </tr>';
                    echo $headerFooter;
                    ?>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start">
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 'Ganjil' . '/' . true . '/' . $santri->IdJabatan) ?>" class="btn btn-warning btn-sm w-80 me-2">
                                        <i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>Edit
                                    </a>
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 'Ganjil') ?>" class="btn btn-success btn-sm w-80">
                                        <i class="fas fa-eye"></i><span style="margin-left: 5px;"></span>View
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-start">
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 'Genap' . '/' . true . '/' . $santri->IdJabatan) ?>" class="btn btn-warning btn-sm w-80 me-2">
                                        <i class="fas fa-edit"></i><span style="margin-left: 5px;">Edit
                                    </a>
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . 'Genap') ?>" class="btn btn-success btn-sm w-80">
                                        <i class="fas fa-eye"></i><span style="margin-left: 5px;">View
                                    </a>
                                </div>
                            </td>
                            <td><?php echo $santri->NamaSantri; ?></td>
                            <td><?php echo $santri->JenisKelamin; ?></td>
                            <td><?php echo $santri->IdTahunAjaran; ?></td>
                            <td><?php echo $santri->NamaKelas; ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <?php echo $headerFooter; ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>
//script section
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum("#TableNilaiSemester", true, true);
</script>
<?= $this->endSection(); ?>