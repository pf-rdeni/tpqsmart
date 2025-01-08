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
                        <th>Aksi</th>
                        <th>Nama Santri</th>
                        <th>Tingkat Kelas</th>
                        <th>Tahun Ajaran</th>
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
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . true . '/' . $santri->IdJabatan) ?>" class="btn btn-warning btn-lg me-2">
                                        <i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>Edit
                                    </a>
                                    <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester) ?>" class="btn btn-primary btn-lg me-2">
                                        <i class="fas fa-eye"></i><span style="margin-left: 5px;"></span>View
                                    </a>
                                    <?php if ($santri->StatusPenilaian == 0) : ?>
                                        <i class="fas fa-exclamation-triangle" style="color:red"></i>
                                    <?php else : ?>
                                        <i class="fas fa-check-circle" style="color:green"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo $santri->NamaSantri; ?></td>
                            <td><?php echo $santri->NamaKelas; ?></td>
                            <td><?php echo $santri->IdTahunAjaran; ?></td>
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