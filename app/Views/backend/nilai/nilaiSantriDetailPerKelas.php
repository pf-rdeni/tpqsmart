<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<style>
    .vertical-header {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        text-align: left;
        white-space: nowrap;
        padding: 5px !important;
        height: 150px;
    }

    .table thead th {
        vertical-align: top;
    }
</style>
<?php
function capitalizeWords($str)
{
    return ucwords(strtolower($str));
}
?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List nilai santri</h3>
            <div class="card-tools">
                <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Ganjil') ?> class="btn btn-warning btn-sm">Semester Ganjil</a>
                <a href=<?php echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="btn btn-info btn-sm">Semester Genap</a>
            </div>
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
                                                <th>Aksi</th>
                                                <?php foreach (array_keys($dataNilai[0]) as $field): ?>
                                                    <?php if ($field !== 'IdKelas'): ?>
                                                        <?php if (in_array($field, ['IdSantri', 'Nama Santri', 'Nama Kelas', 'Tahun Ajaran', 'Semester'])): ?>
                                                            <th><?= htmlspecialchars(capitalizeWords($field)) ?></th>
                                                        <?php else: ?>
                                                            <th class="vertical-header"><?= htmlspecialchars(capitalizeWords($field)) ?></th>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <th class="vertical-header">Total Nilai</th>
                                                <th class="vertical-header">Nilai Rata-Rata</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $columnTotals = []; // Array untuk menyimpan total setiap kolom
                                        $rowCount = 0; // Counter jumlah baris (untuk rata-rata)
                                        ?>
                                        <?php foreach ($dataNilai as $santri) : ?>
                                            <?php if ($santri['Nama Kelas'] == $kelas || $kelas == "SEMUA"): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?= base_url('backend/nilai/showDetail/' . $santri['IdSantri'] . '/' . $santri['Semester']) ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                    </td>
                                                    <?php
                                                    $totalNilai = 0; // Variabel untuk menghitung total nilai per baris
                                                    $jumlahKolomNilai = 0; // Variabel untuk menghitung jumlah kolom nilai
                                                    $rowCount++; // Tambahkan counter jumlah baris
                                                    ?>
                                                    <?php foreach ($santri as $field => $value): ?>
                                                        <?php if ($field !== 'IdKelas'): ?>
                                                            <td><?= htmlspecialchars($value) ?></td>
                                                            <?php
                                                            // Hitung total nilai setiap kolom
                                                            if (!in_array($field, ['IdSantri', 'Nama Santri', 'Nama Kelas', 'Tahun Ajaran', 'Semester'])) {
                                                                $columnTotals[$field] = ($columnTotals[$field] ?? 0) + (int)$value;
                                                                $totalNilai += (int)$value;
                                                                $jumlahKolomNilai++;
                                                            }
                                                            ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <td><?= $totalNilai ?></td>
                                                    <td>
                                                        <?= $jumlahKolomNilai > 0 ? round($totalNilai / $jumlahKolomNilai, 2) : 0 ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Rata-Rata</th>
                                            <!--th colspan="6">Rata-rata Nilai Materi Pelajaran</th-->
                                            <?php
                                            $grandTotal = 0;
                                            $nilaiKolomCount = 0;
                                            ?>
                                            <?php foreach (array_keys($dataNilai[0]) as $field): ?>
                                                <?php if (!in_array($field, ['IdKelas', 'IdSantri', 'Nama Santri', 'Nama Kelas', 'Tahun Ajaran', 'Semester'])): ?>
                                                    <th>
                                                        <?= $rowCount > 0 ? round($columnTotals[$field] / $rowCount, 2) : 0 ?>
                                                    </th>
                                                    <?php
                                                    $grandTotal += $columnTotals[$field] ?? 0;
                                                    $nilaiKolomCount++;
                                                    ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <th><?= $rowCount > 0 ? round($grandTotal / $rowCount, 2) : 0 ?></th>
                                            <th>
                                                <?= $nilaiKolomCount > 0 ? round(($grandTotal / $nilaiKolomCount) / $rowCount, 2) : 0 ?>
                                            </th>
                                        </tr>
                                    </tbody>
                                    <tfoot>

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

<!-- buat modal untuk mengambil detail per satu row yang bisa di klik -->


<?= $this->endSection(); ?>
//script section
<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Initial DataTabel per kelas
        // Tambahkan Button Export ke dalam DataTable
        let buttons = [
            'copy', 'excel', 'colvis'
        ];

        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
            initializeDataTableUmum("#TableNilaiSemester-<?= $kelasId ?>", true, true, buttons);
        <?php endforeach; ?>
    });
</script>
<?= $this->endSection(); ?>