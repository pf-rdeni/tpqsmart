<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri TPQ Per Kelas</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="card card-primary card-tabs">
                <!-- Tab Navigation -->
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
                                <table id="TableNilaiSemester-<?= $kelasId ?>" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>Nama Santri</th>
                                            <th>Tingkat Kelas</th>
                                            <th>Id Santri</th>
                                            <th>Tahun Ajaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataSantri as $santri) : ?>
                                            <?php if ($santri->NamaKelas == $kelas || $kelas == "SEMUA"): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex justify-content-start">
                                                            <?php if ($santri->StatusPenilaian == 0 && $santri->NamaJabatan == "Guru Kelas" || $santri->NamaJabatan == "Wali Kelas") : ?>
                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 1 . '/' . $santri->IdJabatan) ?>" class="btn w-80 btn-warning me-2">
                                                                    <i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>&nbsp;Edit&nbsp;
                                                                </a>
                                                            <?php elseif ($santri->StatusPenilaian == 1 && $santri->NamaJabatan == "Guru Kelas"): ?>
                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 0 . '/' . $santri->IdJabatan) ?>" class="btn w-70 btn-primary me-2">
                                                                    <i class="fas fa-eye"></i><span style="margin-left: 5px;"></span>View
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if ($santri->StatusPenilaian == 0) : ?>
                                                                <i class="fas fa-exclamation-circle fa-lg" style="color:red" data-toggle="tooltip" data-placement="top" title="! Belum selesai dinilai"></i>
                                                            <?php else : ?>
                                                                <i class="fas fa-check-circle fa-lg" style="color:green" data-toggle="tooltip" data-placement="top" title=" ! Sudah Selesai dinilai"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $santri->NamaSantri; ?></td>
                                                    <td><?php echo $santri->NamaKelas; ?></td>
                                                    <td><?php echo $santri->IdSantri; ?></td>
                                                    <td><?php echo $santri->IdTahunAjaran; ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
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
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Initial DataTabel per kelas
        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
            initializeDataTableUmum("#TableNilaiSemester-<?= $kelasId ?>", true, true);
        <?php endforeach; ?>
    });
</script>
<?= $this->endSection(); ?>