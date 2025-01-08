<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kesimpulan Data Nilai</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tblNilaiSummary" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Nama Santri</th>
                        <th>Kelas</th>
                        <th>Total Nilai</th>
                        <th>Rata-Rata</th>
                        <th>Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $MainDataNilai = $nilai->getResult();
                    foreach ($MainDataNilai as $DataNilai) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo base_url('/backend/nilai/showDetail/' . $DataNilai->IdSantri . '/' . $DataNilai->Semester) ?>" class="btn btn-warning btn-sm"><i class="fas fa-list"></i></a>

                            </td>
                            <td><?php echo $DataNilai->NamaSantri; ?></td>
                            <td><?php echo $DataNilai->NamaKelas; ?></td>
                            <td><?php echo $DataNilai->TotalNilai; ?></td>
                            <td><?php echo $DataNilai->NilaiRataRata; ?></td>
                            <td><?php echo $DataNilai->IdTahunAjaran; ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Aksi</th>
                        <th>Nama Santri</th>
                        <th>Semester</th>
                        <th>Total Nilai</th>
                        <th>Rata-Rata</th>
                        <th>Tahun Ajaran</th>
                    </tr>
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
    initializeDataTableUmum("#tblNilaiSummary", true, true);
</script>
<?= $this->endSection(); ?>