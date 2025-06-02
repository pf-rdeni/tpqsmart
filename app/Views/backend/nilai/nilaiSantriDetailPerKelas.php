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
                                                    // Urutkan materi berdasarkan UrutanMateri
                                                    usort($dataMateri[$kelasId], function ($a, $b) {
                                                        return $a->UrutanMateri - $b->UrutanMateri;
                                                    });

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
                                            <?php if ($santri['Nama Kelas'] == $kelas): ?>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalDetailNilai<?= $santri['IdSantri'] ?>">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </button>
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
                                                        // Urutkan materi berdasarkan UrutanMateri
                                                        usort($dataMateri[$kelasId], function ($a, $b) {
                                                            return $a->UrutanMateri - $b->UrutanMateri;
                                                        });

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
                                            <th>Rata-Rata Kelas</th>
                                            <?php
                                            $grandTotal = 0;
                                            $nilaiKolomCount = 0;

                                            // Tampilkan rata-rata nilai materi
                                            if (isset($dataMateri[$kelasId])) {
                                                // Urutkan materi berdasarkan UrutanMateri
                                                usort($dataMateri[$kelasId], function ($a, $b) {
                                                    return $a->UrutanMateri - $b->UrutanMateri;
                                                });

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

<!-- Modal Detail Individual Nilai Santri dan Rata-Rata -->
<?php foreach ($dataNilai as $santri) : ?>
    <?php foreach ($dataKelas as $kelasId => $kelas): ?>
        <?php if ($santri['Nama Kelas'] == $kelas): ?>
            <div class="modal fade" id="modalDetailNilai<?= $santri['IdSantri'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetailNilaiLabel<?= $santri['IdSantri'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title" id="modalDetailNilaiLabel<?= $santri['IdSantri'] ?>">Detail Nilai : <strong><?= htmlspecialchars($santri['Nama Santri']) ?></strong> Kelas <?= htmlspecialchars($santri['Nama Kelas']) ?> </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="modalTabelDetailNilai-<?= $santri['IdSantri'] ?>">
                                    <thead>
                                        <tr>
                                            <th>Nama Materi</th>
                                            <th>Nilai</th>
                                            <th>Rata-Rata Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalNilaiSantri = 0;
                                        $jumlahMateri = 0;
                                        if (isset($dataMateri[$kelasId])) {
                                            // Urutkan materi berdasarkan UrutanMateri
                                            usort($dataMateri[$kelasId], function ($a, $b) {
                                                return $a->UrutanMateri - $b->UrutanMateri;
                                            });

                                            foreach ($dataMateri[$kelasId] as $materi) {
                                                $nilai = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : ' ';

                                                // Hitung rata-rata hanya untuk kelas yang sama
                                                $totalNilaiKelas = 0;
                                                $jumlahSantriKelas = 0;
                                                foreach ($dataNilai as $santriKelas) {
                                                    if ($santriKelas['Nama Kelas'] == $kelas) {
                                                        $nilaiSantriKelas = isset($santriKelas[$materi->NamaMateri]) ? (int)$santriKelas[$materi->NamaMateri] : 0;
                                                        if ($nilaiSantriKelas >= 0) {
                                                            $totalNilaiKelas += $nilaiSantriKelas;
                                                            $jumlahSantriKelas++;
                                                        }
                                                    }
                                                }
                                                $rataRata = $jumlahSantriKelas > 0 ? round($totalNilaiKelas / $jumlahSantriKelas, 1) : ' ';

                                                if ($nilai !== ' ') {
                                                    $totalNilaiSantri += $nilai;
                                                    $jumlahMateri++;
                                                }
                                        ?>
                                                <tr>
                                                    <td><?= htmlspecialchars(capitalizeWords($materi->NamaMateri)) ?></td>
                                                    <td style="color: <?= $nilai === 0 ? 'red' : 'black' ?>"><?= htmlspecialchars($nilai) ?></td>
                                                    <td><?= $rataRata >= 0 ? $rataRata : ' ' ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <tr class="table-info">
                                            <td><strong>Total Nilai</strong></td>
                                            <td><strong><?= $totalNilaiSantri >= 0 ? $totalNilaiSantri : ' ' ?></strong></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Rata-Rata</strong></td>
                                            <td><strong><?= $jumlahMateri > 0 ? round($totalNilaiSantri / $jumlahMateri, 1) : ' ' ?></strong></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

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

        // Inisialisasi DataTable untuk modal
        function initModalDataTable(santriId) {
            let tableId = '#modalTabelDetailNilai-' + santriId;
            let table = $(tableId);

            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            // Reinitialize DataTable
            table.DataTable({
                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "pageLength": 6,
                "searching": true,
                "ordering": false,
                "info": true,
                "scrollY": "400px",
                "scrollCollapse": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],

            });
        }

        // Event handler untuk modal
        <?php foreach ($dataNilai as $santri) : ?>
            let modal<?= $santri['IdSantri'] ?> = $('#modalDetailNilai<?= $santri['IdSantri'] ?>');

            modal<?= $santri['IdSantri'] ?>.on('shown.bs.modal', function() {
                initModalDataTable('<?= $santri['IdSantri'] ?>');
            });

            modal<?= $santri['IdSantri'] ?>.on('hidden.bs.modal', function() {
                let tableId = '#modalTabelDetailNilai-<?= $santri['IdSantri'] ?>';
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().destroy();
                }
            });
        <?php endforeach; ?>
    });
</script>
<?= $this->endSection(); ?>