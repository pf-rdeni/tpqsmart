<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Nilai Santri Summary</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id Santri</th>
                        <th>Nama Santri</th>
                        <th>Tahun Ajaran</th>
                        <th>Kelas</th>
                        <th>Total Nilai</th>
                        <th>Rata-Rata Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $MainDataNilai = $nilai->getResult();
                    foreach ($MainDataNilai as $DataNilai) : ?>
                        <tr>
                            <td><?php echo $DataNilai->IdSantri; ?></td>
                            <td><?php echo $DataNilai->NamaSantri; ?></td>
                            <td><?php echo $DataNilai->IdTahunAjaran; ?></td>
                            <td><?php echo $DataNilai->NamaKelas; ?></td>
                            <td><?php echo $DataNilai->TotalNilai; ?></td>
                            <td><?php echo $DataNilai->NilaiRataRata; ?></td>
                            <td>
                                <a href="<?php echo base_url('/backend/nilai/showDetail/' . $DataNilai->IdSantri . '/' . $DataNilai->Semester) ?>" class="btn btn-warning btn-sm"><i class="fas fa-list"></i></a>

                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Id Santri</th>
                        <th>Nama Santri</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Total Nilai</th>
                        <th>Rata-Rata Nilai</th>
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