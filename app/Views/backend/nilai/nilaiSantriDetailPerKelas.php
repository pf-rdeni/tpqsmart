<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List nilai santri persemester</h3>
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
                                            <?php if (!empty($dataNilai)): ?>
                                                <?php foreach (array_keys($dataNilai[0]) as $field): ?>
                                                    <?php if ($field !== 'IdKelas'): ?>
                                                        <th><?= htmlspecialchars($field) ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <th>TotalNilai</th> <!-- Tambahkan header untuk kolom TotalNilai -->
                                                <th>NilaiRataRata</th> <!-- Tambahkan header untuk kolom NilaiRataRata -->
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataNilai as $santri) : ?>
                                            <?php if ($santri['NamaKelas'] == $kelas || $kelas == "SEMUA"): ?>
                                                <tr>
                                                    <?php
                                                    $totalNilai = 0; // Variabel untuk menghitung total nilai
                                                    $jumlahKolomNilai = 0; // Variabel untuk menghitung jumlah kolom nilai
                                                    ?>
                                                    <?php foreach ($santri as $field => $value): ?>
                                                        <?php if ($field !== 'IdKelas'): ?>
                                                            <td><?= htmlspecialchars($value) ?></td>
                                                            <?php
                                                            // Jumlahkan nilai jika field adalah kolom nilai (bukan kolom Semester atau lainnya)
                                                            if (!in_array($field, ['IdSantri', 'NamaSantri', 'NamaKelas', 'IdTahunAjaran', 'Semester'])) {
                                                                $totalNilai += (int)$value;
                                                                $jumlahKolomNilai++;
                                                            }
                                                            ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <td><?= $totalNilai ?></td> <!-- Tampilkan total nilai -->
                                                    <td>
                                                        <?= $jumlahKolomNilai > 0 ? round($totalNilai / $jumlahKolomNilai, 2) : 0 ?>
                                                    </td> <!-- Tampilkan rata-rata nilai -->
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