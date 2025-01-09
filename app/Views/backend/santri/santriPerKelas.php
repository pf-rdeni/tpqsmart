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
                                    <?php if ($santri->StatusPenilaian == 0 && $santri->NamaJabatan == "GURU KELAS" || $santri->NamaJabatan == "WALI KELAS") : ?>
                                        <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 1 . '/' . $santri->IdJabatan) ?>" class="btn w-80 btn-warning me-2">
                                            <i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>&nbsp;Edit&nbsp;
                                        </a>
                                    <?php elseif ($santri->StatusPenilaian == 1 && $santri->NamaJabatan == "GURU KELAS"): ?>
                                        <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 0 . '/' . $santri->IdJabatan) ?>" class="btn w-70 btn-primary me-2">
                                            <i class="fas fa-eye"></i><span style="margin-left: 5px;"></span>View
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($santri->StatusPenilaian == 0) : ?>
                                        <i class="fas fa-exclamation-circle fa-lg" style="color:red" data-toggle="tooltip" data-placement="top" title="! Materi belum selesai dinilai"></i>
                                    <?php else : ?>
                                        <i class="fas fa-check-circle fa-lg" style="color:green" data-toggle="tooltip" data-placement="top" title=" ! Semua materi selesai dinilai"></i>
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
    $(document).ready(function() {
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();
        initializeDataTableUmum("#TableNilaiSemester", true, true);
    });
</script>
<?= $this->endSection(); ?>