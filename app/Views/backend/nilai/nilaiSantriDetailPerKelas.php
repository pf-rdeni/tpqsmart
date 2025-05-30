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
                <!-- Tab Navigation Membuat Header Tab selection dari Nama Kelas-->
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
                <!-- Tab Content Megenerate Tabel-->
                <div class="card-body">
                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
                            <div class="tab-pane fade <?= $kelasId === array_key_first($dataKelas) ? 'show active' : '' ?>"
                                id="kelas-<?= $kelasId ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelasId ?>">
                                <table id="TableNilaiSemester-<?= $kelasId ?>" class="table table-bordered table-striped">
                                    <!-- Table Header Menampilkan Nama Kelas  TA Semester dan Nama Materi-->
                                    <thead>
                                        <tr>
                                            <?php if (!empty($dataNilai)): ?>
                                                <th>Aksi</th>
                                                <th>Id Santri</th>
                                                <th>Nama Santri</th>
                                                <th>Nama Kelas</th>
                                                <th>Tahun Ajaran</th>
                                                <th>Semester</th>
                                                <?php
                                                // Tampilkan header materi berdasarkan kelas yang dipilih
                                                if (isset($dataMateri[$kelasId])) {
                                                    foreach ($dataMateri[$kelasId] as $materi) {
                                                        echo '<th class="vertical-header">' . htmlspecialchars(capitalizeWords($materi->NamaMateri)) . '</th>';
                                                    }
                                                }
                                                ?>
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
                                                    <td><?= htmlspecialchars($santri['IdSantri']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Nama Santri']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Nama Kelas']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Tahun Ajaran']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Semester']) ?></td>
                                                    <?php
                                                    $totalNilai = 0;
                                                    $jumlahKolomNilai = 0;
                                                    $rowCount++;

                                                    // Tampilkan nilai materi berdasarkan kelas yang dipilih
                                                    if (isset($dataMateri[$kelasId])) {
                                                        foreach ($dataMateri[$kelasId] as $materi) {
                                                            $nilai = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : ' ';
                                                            echo '<td style="color:' . ($nilai === 0 ? 'red' : 'black') . ';">' . htmlspecialchars($nilai) . '</td>';

                                                            // Hitung total nilai
                                                            if ($nilai >= 0) {
                                                                $columnTotals[$materi->NamaMateri] = ($columnTotals[$materi->NamaMateri] ?? 0) + $nilai;
                                                                $totalNilai += $nilai;
                                                                $jumlahKolomNilai++;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $totalNilai >= 0 ? $totalNilai : ' ' ?></td>
                                                    <td><?= $jumlahKolomNilai > 0 ? round($totalNilai / $jumlahKolomNilai, 1) : ' ' ?></td>
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
                                            <?php
                                            $grandTotal = 0;
                                            $nilaiKolomCount = 0;

                                            // Tampilkan rata-rata nilai materi
                                            if (isset($dataMateri[$kelasId])) {
                                                foreach ($dataMateri[$kelasId] as $materi) {
                                                    $rataRata = $rowCount > 0 ? round($columnTotals[$materi->NamaMateri] / $rowCount, 1) : -1;
                                                    echo '<th>' . ($rataRata >= 0 ? $rataRata : ' ') . '</th>';

                                                    if ($rataRata >= 0) {
                                                        $grandTotal += $columnTotals[$materi->NamaMateri] ?? 0;
                                                        $nilaiKolomCount++;
                                                    }
                                                }
                                            }
                                            ?>
                                            <th><?= $rowCount > 0 ? round($grandTotal / $rowCount, 1) : ' ' ?></th>
                                            <th><?= $nilaiKolomCount > 0 ? round(($grandTotal / $nilaiKolomCount) / $rowCount, 1) : ' ' ?></th>
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