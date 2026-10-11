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

    /* Fix untuk scrollX DataTable di mobile - memastikan konsistensi */
    .dataTables_scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dataTables_scrollBody {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    /* Memastikan tabel tidak shrink di mobile */
    .dataTables_wrapper .dataTables_scroll .dataTables_scrollBody table {
        min-width: 100%;
    }

    /* Fix untuk card body yang mengandung DataTable */
    .card-body .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }
</style>
<?php
function capitalizeWords($str)
{
    return ucwords(strtolower($str));
}

// Ambil setting alphabet untuk setiap kelas
$settingAlphabetActive = [];
foreach ($dataNilai as $santri) {
    $settingAlphabetActive[$santri['IdKelas']] = getAlphabetKelasSettings($settingNilai, $santri['IdKelas'])['isAlphabetKelas'];
}


?>
<div class="col-12">
    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Nilai Santri Per Kelas
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                    <ol class="mb-4">
                        <li class="mb-2">
                            <strong>Pilih Semester:</strong> Gunakan tombol <span class="badge badge-warning">Semester Ganjil</span> atau
                            <span class="badge badge-info">Semester Genap</span> di pojok kanan atas untuk melihat data nilai per semester.
                        </li>
                        <li class="mb-2">
                            <strong>Pilih Kelas:</strong> Gunakan tab di atas untuk memilih kelas yang ingin dilihat nilai santrinya.
                            Setiap tab mewakili satu kelas dengan daftar santri di dalamnya.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Nilai:</strong> Tabel menampilkan nilai santri untuk setiap materi pelajaran.
                            Nilai ditampilkan dalam format <strong>angka</strong> atau <strong>huruf</strong> (A, B, C, D) tergantung setting kelas.
                            <ul class="mt-2">
                                <li>Nilai <span style="color: red;"><strong>merah</strong></span> atau <span style="color: red;"><strong>0</strong></span> menandakan nilai belum diisi.</li>
                                <li>Baris terakhir menampilkan <strong>Rata-Rata Kelas</strong> untuk setiap materi.</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Detail Nilai:</strong> Klik tombol <span class="badge badge-primary"><i class="fas fa-eye"></i> Detail</span>
                            pada baris santri untuk melihat detail nilai per materi beserta perbandingan dengan rata-rata kelas dalam modal popup.
                        </li>
                        <li class="mb-2">
                            <strong>Export Data:</strong> Gunakan tombol export di DataTable (Copy, Excel, dll) untuk menyalin atau mengunduh data nilai ke file Excel.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Header kolom materi ditampilkan secara <strong>vertikal</strong> untuk menghemat ruang dan memudahkan pembacaan.</li>
                            <li>Kolom <strong>Total Nilai</strong> dan <strong>Nilai Rata-Rata</strong> dihitung otomatis dari semua nilai materi santri.</li>
                            <li>Jika kelas menggunakan sistem nilai <strong>huruf</strong>, kolom akan menampilkan "Rata-Rata Huruf" sebagai ganti "Total Nilai" dan "Nilai Rata-Rata".</li>
                            <li>Data dapat diurutkan dan difilter menggunakan fitur DataTable (search box, sorting, dll).</li>
                            <li>Modal detail nilai menampilkan perbandingan nilai santri dengan rata-rata kelas untuk setiap materi.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                                <th>Nama Santri</th>
                                                <th>Nama Kelas</th>
                                                <th>Id Santri</th>
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
                                                <?php if ($settingAlphabetActive[$kelasId] ?? false): ?>
                                                    <th class="vertical-header">Rata-Rata Huruf</th>
                                                <?php else: ?>
                                                    <th class="vertical-header">Total Nilai</th>
                                                    <th class="vertical-header">Nilai Rata-Rata</th>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $columnTotals = []; // Array untuk menyimpan total setiap kolom
                                        $rowCount = 0; // Counter jumlah baris (untuk rata-rata)

                                        // Siapkan array materi yang sudah diurutkan untuk digunakan di semua tempat
                                        $materiSorted = [];
                                        if (isset($dataMateri[$kelasId])) {
                                            // Urutkan materi berdasarkan UrutanMateri
                                            $materiSorted = $dataMateri[$kelasId];
                                            usort($materiSorted, function ($a, $b) {
                                                return $a->UrutanMateri - $b->UrutanMateri;
                                            });
                                        }
                                        ?>
                                        <?php foreach ($dataNilai as $santri) : ?>
                                            <?php if ($santri['Nama Kelas'] == $kelas): ?>
                                                <?php
                                                // Hitung progress pengisian nilai untuk santri ini
                                                $totalMateri = 0;
                                                $materiTerisi = 0;

                                                // Hitung progress dari data nilai menggunakan $materiSorted
                                                if (!empty($materiSorted)) {
                                                    foreach ($materiSorted as $materi) {
                                                        $totalMateri++;
                                                        $nilaiMateri = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : 0;
                                                        // Nilai dianggap terisi jika > 0 (bukan 0 atau kosong)
                                                        if ($nilaiMateri > 0) {
                                                            $materiTerisi++;
                                                        }
                                                    }
                                                }

                                                // Hitung persentase
                                                $persentase = $totalMateri > 0 ? round(($materiTerisi / $totalMateri) * 100, 1) : 0;

                                                // Tentukan warna badge berdasarkan persentase
                                                $badgeColor = 'secondary';
                                                if ($persentase >= 100) {
                                                    $badgeColor = 'success';
                                                } elseif ($persentase >= 75) {
                                                    $badgeColor = 'info';
                                                } elseif ($persentase >= 50) {
                                                    $badgeColor = 'warning';
                                                } elseif ($persentase > 0) {
                                                    $badgeColor = 'danger';
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalDetailNilai<?= $santri['IdSantri'] ?>">
                                                                <i class="fas fa-eye"></i> Detail
                                                            </button>
                                                            <button type="button" class="btn btn-<?= $badgeColor ?> btn-sm" data-toggle="tooltip" data-placement="top"
                                                                title="Progress: <?= $materiTerisi ?>/<?= $totalMateri ?> materi (<?= $persentase ?>%)">
                                                                <i class="fas fa-percentage"></i>
                                                                <span class="badge badge-light"><?= $persentase ?>%</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($santri['Nama Santri']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Nama Kelas']) ?></td>
                                                    <td><?= htmlspecialchars($santri['IdSantri']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Tahun Ajaran']) ?></td>
                                                    <td><?= htmlspecialchars($santri['Semester']) ?></td>
                                                    <?php
                                                    $totalNilai = 0;
                                                    $jumlahKolomNilai = 0;
                                                    $rowCount++;

                                                    // Tampilkan nilai materi berdasarkan kelas yang dipilih
                                                    // Gunakan $materiSorted yang sudah diurutkan di atas
                                                    if (!empty($materiSorted)) {
                                                        foreach ($materiSorted as $materi) {
                                                            $nilai = isset($santri[$materi->NamaMateri]) ? (int)$santri[$materi->NamaMateri] : ' ';
                                                            if ($settingAlphabetActive[$kelasId] ?? false) {
                                                                echo '<td style="color:' . ($nilai === 0 ? 'red' : 'black') . ';">' . htmlspecialchars(konversiNilaiHuruf($nilai, $settingNilai)) . '</td>';
                                                            } else {
                                                                echo '<td style="color:' . ($nilai === 0 ? 'red' : 'black') . ';">' . htmlspecialchars(konversiNilaiAngkaArabic($nilai)) . '</td>';
                                                            }

                                                            // Hitung total nilai
                                                            if ($nilai >= 0) {
                                                                $columnTotals[$materi->NamaMateri] = ($columnTotals[$materi->NamaMateri] ?? 0) + $nilai;
                                                                $totalNilai += $nilai;
                                                                $jumlahKolomNilai++;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($settingAlphabetActive[$kelasId] ?? false): ?>
                                                        <td><?= $jumlahKolomNilai > 0 ? konversiNilaiHuruf(round($totalNilai / $jumlahKolomNilai, 1), $settingNilai) : ' ' ?></td>
                                                    <?php else: ?>
                                                        <td><?= $totalNilai >= 0 ? konversiNilaiAngkaArabic($totalNilai) : ' ' ?></td>
                                                        <td><?= $jumlahKolomNilai > 0 ? konversiNilaiAngkaArabic(round($totalNilai / $jumlahKolomNilai, 1)) : ' ' ?></td>
                                                    <?php endif; ?>
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

                                            // Tampilkan rata-rata nilai materi menggunakan $materiSorted yang sudah diurutkan
                                            if (!empty($materiSorted)) {
                                                foreach ($materiSorted as $materi) {
                                                    $rataRata = $rowCount > 0 ? round($columnTotals[$materi->NamaMateri] / $rowCount, 1) : -1;
                                                    if ($settingAlphabetActive[$kelasId] ?? false) {
                                                        echo '<th>' . ($rataRata >= 0 ? konversiNilaiHuruf($rataRata, $settingNilai) : ' ') . '</th>';
                                                    } else {
                                                        echo '<th>' . ($rataRata >= 0 ? konversiNilaiAngkaArabic($rataRata) : ' ') . '</th>';
                                                    }

                                                    if ($rataRata >= 0) {
                                                        $grandTotal += $columnTotals[$materi->NamaMateri] ?? 0;
                                                        $nilaiKolomCount++;
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php if ($settingAlphabetActive[$kelasId] ?? false): ?>
                                                <th><?= $nilaiKolomCount > 0 ? konversiNilaiHuruf(round(($grandTotal / $nilaiKolomCount) / $rowCount, 1), $settingNilai) : ' ' ?></th>
                                            <?php else: ?>
                                                <th><?= $rowCount > 0 ? konversiNilaiAngkaArabic(round($grandTotal / $rowCount, 1)) : ' ' ?></th>
                                                <th><?= $nilaiKolomCount > 0 ? konversiNilaiAngkaArabic(round(($grandTotal / $nilaiKolomCount) / $rowCount, 1)) : ' ' ?></th>
                                            <?php endif; ?>
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
                                            <?php if ($settingAlphabetActive[$kelasId] ?? false): ?>
                                                <th>Huruf</th>
                                                <th>Rata-Rata Kelas</th>
                                            <?php else: ?>
                                                <th>Nilai</th>
                                                <th>Rata-Rata Kelas</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalNilaiSantri = 0;
                                        $jumlahMateri = 0;

                                        // Siapkan array materi yang sudah diurutkan untuk modal
                                        $materiSortedModal = [];
                                        if (isset($dataMateri[$kelasId])) {
                                            $materiSortedModal = $dataMateri[$kelasId];
                                            usort($materiSortedModal, function ($a, $b) {
                                                return $a->UrutanMateri - $b->UrutanMateri;
                                            });
                                        }

                                        if (!empty($materiSortedModal)) {
                                            foreach ($materiSortedModal as $materi) {
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
                                                    <?php if ($settingAlphabetActive[$kelasId] ?? false): ?>
                                                        <td style="color: <?= $nilai === 0 ? 'red' : 'black' ?>"><?= konversiNilaiHuruf($nilai, $settingNilai) ?></td>
                                                        <td><?= $rataRata >= 0 ? konversiNilaiHuruf($rataRata, $settingNilai) : ' ' ?></td>
                                                    <?php else: ?>
                                                        <td style="color: <?= $nilai === 0 ? 'red' : 'black' ?>"><?= konversiNilaiAngkaArabic($nilai) ?></td>
                                                        <td><?= $rataRata >= 0 ? konversiNilaiAngkaArabic($rataRata) : ' ' ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php if (!$settingAlphabetActive[$kelasId]): ?>
                                            <tr class="table-info">
                                                <td><strong>Total Nilai</strong></td>
                                                <td><strong><?= $totalNilaiSantri >= 0 ? konversiNilaiAngkaArabic($totalNilaiSantri) : ' ' ?></strong></td>
                                                <td></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="table-info">
                                            <?php if ($settingAlphabetActive[$kelasId]): ?>
                                                <td><strong>Rata-Rata</strong></td>
                                                <td><strong><?= $jumlahMateri > 0 ? konversiNilaiHuruf(round($totalNilaiSantri / $jumlahMateri, 1), $settingNilai) : ' ' ?></strong></td>
                                                <td></td>
                                            <?php else: ?>
                                                <td><strong>Rata-Rata</strong></td>
                                                <td><strong><?= $jumlahMateri > 0 ? konversiNilaiAngkaArabic(round($totalNilaiSantri / $jumlahMateri, 1)) : ' ' ?></strong></td>
                                                <td></td>
                                            <?php endif; ?>
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

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Key untuk localStorage
        const storageKey = 'nilaiSantriDetailPerKelas_activeTab';

        // Fungsi untuk menyimpan tab aktif ke localStorage
        function saveActiveTab(tabId) {
            localStorage.setItem(storageKey, tabId);
        }

        // Fungsi untuk memuat tab aktif dari localStorage
        function loadActiveTab() {
            const savedTabId = localStorage.getItem(storageKey);
            if (savedTabId) {
                // Hapus class active dari semua tab dan tab pane
                $('#kelasTab .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('.tab-pane').removeClass('show active');

                // Aktifkan tab yang tersimpan
                const tabLink = $('#tab-' + savedTabId);
                const tabPane = $('#kelas-' + savedTabId);

                if (tabLink.length && tabPane.length) {
                    tabLink.addClass('active').attr('aria-selected', 'true');
                    tabPane.addClass('show active');

                    // Trigger tab event untuk Bootstrap
                    tabLink.tab('show');
                } else {
                    // Jika tab yang tersimpan tidak ditemukan, set ke tab pertama
                    setFirstTab();
                }
            } else {
                // Jika belum ada yang tersimpan, set ke tab pertama
                setFirstTab();
            }
        }

        // Fungsi untuk set tab pertama sebagai aktif
        function setFirstTab() {
            // Gunakan selector spesifik untuk tab kelas agar tidak konflik dengan tab lain
            const firstTab = $('#kelasTab .nav-link').first();
            const firstTabId = firstTab.attr('id');

            if (firstTabId && firstTab.length) {
                const tabId = firstTabId.replace('tab-', '');

                // Hapus active dari semua tab dan tab pane terlebih dahulu
                $('#kelasTab .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('.tab-pane').removeClass('show active');

                // Aktifkan tab pertama
                firstTab.addClass('active').attr('aria-selected', 'true');
                const firstTabPane = $('#kelas-' + tabId);
                if (firstTabPane.length) {
                    firstTabPane.addClass('show active');
                }

                // Trigger Bootstrap tab untuk memastikan tab benar-benar aktif
                firstTab.tab('show');

                // Simpan ke localStorage
                saveActiveTab(tabId);
            }
        }

        // Fungsi untuk memastikan tab pertama tetap aktif
        function ensureFirstTabActive() {
            const activeTab = $('#kelasTab .nav-link.active');
            if (!activeTab.length) {
                // Jika tidak ada tab aktif, aktifkan tab pertama
                setFirstTab();
            } else {
                // Pastikan tab pane juga aktif dan visible
                const activeTabId = activeTab.attr('id').replace('tab-', '');
                const activeTabPane = $('#kelas-' + activeTabId);
                if (activeTabPane.length) {
                    // Pastikan class show active ada dan hapus fade jika perlu
                    activeTabPane.addClass('show active');
                    // Pastikan tab link juga aktif
                    activeTab.addClass('active').attr('aria-selected', 'true');
                }
            }
        }

        // Event listener untuk menyimpan tab saat tab diubah
        // Gunakan event delegation untuk memastikan event terikat dengan benar
        $(document).on('shown.bs.tab', '#kelasTab a[data-toggle="tab"]', function(e) {
            const tabId = $(e.target).attr('id').replace('tab-', '');
            if (tabId) {
                saveActiveTab(tabId);
            }
        });

        // Memuat tab yang tersimpan saat halaman dimuat
        // Pastikan tab aktif SEBELUM inisialisasi DataTable
        // Gunakan setTimeout untuk memastikan DOM sudah siap dan semua elemen ter-render

        // Langsung aktifkan tab pertama saat DOM ready (tanpa menunggu localStorage)
        // Ini memastikan tab selalu terlihat saat pertama kali load
        const firstTab = $('#kelasTab .nav-link').first();
        const firstTabId = firstTab.attr('id');
        let firstTabIdValue = null;
        if (firstTabId) {
            firstTabIdValue = firstTabId.replace('tab-', '');
            // Pastikan tab pertama aktif secara visual
            firstTab.addClass('active').attr('aria-selected', 'true');
            $('#kelas-' + firstTabIdValue).addClass('show active');
        }

        // Kemudian load dari localStorage jika ada
        setTimeout(function() {
            const savedTabId = localStorage.getItem(storageKey);
            if (savedTabId && savedTabId !== firstTabIdValue) {
                // Jika ada tab yang tersimpan dan berbeda dengan tab pertama, aktifkan
                loadActiveTab();
            } else {
                // Jika tidak ada atau sama dengan tab pertama, pastikan tab pertama aktif
                ensureFirstTabActive();
            }

            // Verifikasi lagi setelah loadActiveTab
            setTimeout(function() {
                ensureFirstTabActive();
            }, 50);
        }, 100);

        // Initial DataTable per kelas dengan scroll horizontal dan export buttons
        // Inisialisasi setelah tab sudah aktif
        setTimeout(function() {
            <?php foreach ($dataKelas as $kelasId => $kelas): ?>
                // Hanya inisialisasi DataTable jika tabel ada dan terlihat
                const table<?= $kelasId ?> = $("#TableNilaiSemester-<?= $kelasId ?>");
                if (table<?= $kelasId ?>.length && !$.fn.DataTable.isDataTable("#TableNilaiSemester-<?= $kelasId ?>")) {
                    initializeDataTableScrollX("#TableNilaiSemester-<?= $kelasId ?>", ['copy', 'excel', 'colvis']);

                    // Pastikan tab tetap aktif setelah DataTable draw
                    $("#TableNilaiSemester-<?= $kelasId ?>").on('draw.dt', function() {
                        ensureFirstTabActive();
                    });
                }
            <?php endforeach; ?>

            // Pastikan tab aktif setelah semua DataTable diinisialisasi
            ensureFirstTabActive();
        }, 200);

        // Monitor untuk memastikan tab tetap aktif (jika ada sesuatu yang menutupnya)
        // Monitor lebih lama dan lebih agresif
        let monitorCount = 0;
        const monitorInterval = setInterval(function() {
            monitorCount++;
            if (monitorCount > 20) { // Stop setelah 10 detik (20 x 500ms)
                clearInterval(monitorInterval);
                return;
            }

            // Cek apakah ada tab aktif
            const activeTab = $('#kelasTab .nav-link.active');
            const activeTabPane = $('.tab-pane.show.active');

            if (!activeTab.length || !activeTabPane.length) {
                // Jika tidak ada tab atau tab pane aktif, aktifkan tab pertama
                ensureFirstTabActive();
            } else {
                // Pastikan tab pane yang aktif sesuai dengan tab yang aktif
                const activeTabId = activeTab.attr('id').replace('tab-', '');
                const expectedTabPane = $('#kelas-' + activeTabId);
                if (!expectedTabPane.hasClass('show active')) {
                    expectedTabPane.addClass('show active');
                }
            }
        }, 500);

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