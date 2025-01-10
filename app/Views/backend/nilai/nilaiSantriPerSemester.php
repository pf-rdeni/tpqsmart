<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kesimpulan Data Nilai</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Tab Navigation -->
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
                            <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                <a class="nav-link border-white text-center <?= $kelasId === array_key_first($dataKelas) ? 'active' : '' ?>"
                                    id="tab-<?= $kelasId ?>"
                                    data-toggle="tab"
                                    href="#kelas-<?= $kelasId ?>"
                                    role="tab"
                                    aria-controls="kelas-<?= $kelasId ?>"
                                    aria-selected="<?= $kelasId === array_key_first($dataKelas) ? 'true' : 'false' ?>">
                                    <?= $kelas ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>
                <div class="card-body">
                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
                            <div class="tab-pane fade <?= $kelasId === array_key_first($dataKelas) ? 'show active' : '' ?>"
                                id="kelas-<?= $kelasId ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelasId ?>">
                                <table id="tblNilaiSummary-<?= $kelasId ?>" class="table table-bordered table-striped">
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
                                            <?php if ($DataNilai->IdKelas == $kelasId || $kelasId == 0) : ?>
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
                                            <?php endif; ?>
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
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
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
        // Inisialisasi DataTable untuk setiap kelas
        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
            initializeDataTableUmum("#tblNilaiSummary-<?= $kelasId ?>", true, true);
        <?php endforeach; ?>
    });
</script>
<?= $this->endSection(); ?>