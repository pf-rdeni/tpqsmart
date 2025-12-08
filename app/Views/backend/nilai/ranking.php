<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<style>
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

    /* Styling untuk badge ranking */
    .badge-ranking {
        font-size: 1.1em;
        padding: 8px 12px;
        font-weight: bold;
    }

    .badge-ranking-1 {
        background-color: #FFD700;
        color: #000;
    }

    .badge-ranking-2 {
        background-color: #C0C0C0;
        color: #000;
    }

    .badge-ranking-3 {
        background-color: #CD7F32;
        color: #fff;
    }

    .badge-ranking-other {
        background-color: #6c757d;
        color: #fff;
    }
</style>
<div class="col-12">
    <!-- Card Informasi -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Halaman Rangking
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
                    <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Informasi:</h5>
                    <ol class="mb-4">
                        <li class="mb-2">
                            <strong>Pilih Kelas:</strong> Gunakan tab di atas untuk memilih kelas yang ingin dilihat rangkingnya.
                            Setiap tab menampilkan rangking santri untuk kelas yang dipilih.
                        </li>
                        <li class="mb-2">
                            <strong>Rangking:</strong> Rangking dihitung berdasarkan nilai rata-rata dari semua materi pelajaran.
                            Santri dengan nilai rata-rata tertinggi mendapat rangking 1.
                        </li>
                        <li class="mb-2">
                            <strong>Badge Rangking:</strong>
                            <ul class="mt-2">
                                <li><span class="badge badge-ranking badge-ranking-1">1</span> - Rangking 1 (Emas)</li>
                                <li><span class="badge badge-ranking badge-ranking-2">2</span> - Rangking 2 (Perak)</li>
                                <li><span class="badge badge-ranking badge-ranking-3">3</span> - Rangking 3 (Perunggu)</li>
                                <li><span class="badge badge-ranking badge-ranking-other">4+</span> - Rangking lainnya</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Export Data:</strong> Gunakan tombol export di DataTable (Copy, Excel, dll) untuk menyalin atau mengunduh data rangking ke file Excel.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Tab aktif akan <strong>tersimpan otomatis</strong> di browser Anda, sehingga saat kembali ke halaman ini, tab terakhir yang dibuka akan otomatis aktif.</li>
                            <li>Data dapat diurutkan dan difilter menggunakan fitur DataTable (search box, sorting, dll).</li>
                            <li>Rangking dihitung per kelas, sehingga setiap kelas memiliki rangking 1, 2, 3, dst.</li>
                            <li>Hanya kelas yang memiliki santri aktif yang ditampilkan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rangking Santri Per Kelas - Semester <?= $semester ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/nilai/showRanking/Ganjil') ?>" class="btn btn-warning btn-sm <?= $semester == 'Ganjil' ? 'active' : '' ?>">
                    Semester Ganjil
                </a>
                <a href="<?= base_url('backend/nilai/showRanking/Genap') ?>" class="btn btn-info btn-sm <?= $semester == 'Genap' ? 'active' : '' ?>">
                    Semester Genap
                </a>
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
                                <table id="TableRanking-<?= $kelasId ?>" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rangking</th>
                                            <th>Nama Santri</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Kelas</th>
                                            <th>Tahun Ajaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $filteredData = [];
                                        
                                        // Pastikan $rankingData adalah array
                                        if (!is_array($rankingData)) {
                                            $rankingData = [];
                                        }
                                        
                                        // Filter berdasarkan IdKelas
                                        if (!empty($rankingData)) {
                                            $filteredData = array_filter($rankingData, function($item) use ($kelasId) {
                                                if (is_null($item)) {
                                                    return false;
                                                }
                                                // Handle both object and array
                                                $idKelas = is_object($item) ? ($item->IdKelas ?? null) : (isset($item['IdKelas']) ? $item['IdKelas'] : null);
                                                return $idKelas == $kelasId;
                                            });
                                            // Re-index array setelah filter
                                            $filteredData = array_values($filteredData);
                                        }
                                        
                                        // Sort berdasarkan ranking (ascending: 1, 2, 3, ...)
                                        if (!empty($filteredData)) {
                                            usort($filteredData, function($a, $b) {
                                                $rankingA = is_object($a) ? (int)($a->Rangking ?? 0) : (int)(isset($a['Rangking']) ? $a['Rangking'] : 0);
                                                $rankingB = is_object($b) ? (int)($b->Rangking ?? 0) : (int)(isset($b['Rangking']) ? $b['Rangking'] : 0);
                                                return $rankingA <=> $rankingB;
                                            });
                                        }
                                        
                                        if (empty($filteredData)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data rangking untuk kelas ini</td>
                                            </tr>
                                        <?php else:
                                            foreach ($filteredData as $data) : 
                                                // Handle both object and array
                                                $ranking = is_object($data) ? $data->Rangking : (isset($data['Rangking']) ? $data['Rangking'] : 0);
                                                $namaSantri = is_object($data) ? $data->NamaSantri : (isset($data['NamaSantri']) ? $data['NamaSantri'] : '-');
                                                $idSantri = is_object($data) ? ($data->IdSantri ?? '-') : (isset($data['IdSantri']) ? $data['IdSantri'] : '-');
                                                $jenisKelamin = is_object($data) ? ($data->JenisKelamin ?? '-') : (isset($data['JenisKelamin']) ? $data['JenisKelamin'] : '-');
                                                $namaKelas = is_object($data) ? ($data->NamaKelas ?? '-') : (isset($data['NamaKelas']) ? $data['NamaKelas'] : '-');
                                                $tahunAjaran = is_object($data) ? ($data->IdTahunAjaran ?? '-') : (isset($data['IdTahunAjaran']) ? $data['IdTahunAjaran'] : '-');
                                                $totalNilai = is_object($data) ? $data->TotalNilai : (isset($data['TotalNilai']) ? $data['TotalNilai'] : 0);
                                                $nilaiRataRata = is_object($data) ? $data->NilaiRataRata : (isset($data['NilaiRataRata']) ? $data['NilaiRataRata'] : 0);
                                                
                                                // Format tahun ajaran (contoh: 20242025 menjadi 2024/2025)
                                                $tahunAjaranFormatted = $tahunAjaran;
                                                if ($tahunAjaran != '-' && strlen($tahunAjaran) == 8) {
                                                    $tahunAjaranFormatted = substr($tahunAjaran, 0, 4) . '/' . substr($tahunAjaran, 4, 4);
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                        $badgeClass = 'badge-ranking-other';
                                                        if ($ranking == 1) {
                                                            $badgeClass = 'badge-ranking-1';
                                                        } elseif ($ranking == 2) {
                                                            $badgeClass = 'badge-ranking-2';
                                                        } elseif ($ranking == 3) {
                                                            $badgeClass = 'badge-ranking-3';
                                                        }
                                                        ?>
                                                        <div>
                                                            <span class="badge badge-ranking <?= $badgeClass ?>"><?= $ranking ?></span>
                                                        </div>
                                                        <div class="mt-1">
                                                            <small class="text-muted d-block">Total: <?= number_format($totalNilai, 0, ',', '.') ?></small>
                                                            <small class="text-muted d-block">Rata-rata: <strong><?= number_format(round($nilaiRataRata, 1), 1, ',', '.') ?></strong></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div><?= $namaSantri ?></div>
                                                        <small class="text-muted">Id Santri: <?= $idSantri ?></small>
                                                    </td>
                                                    <td><?= $jenisKelamin ?></td>
                                                    <td><?= $namaKelas ?></td>
                                                    <td><?= $tahunAjaranFormatted ?></td>
                                                </tr>
                                            <?php endforeach;
                                        endif; ?>
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
        // Key untuk localStorage
        const storageKey = 'ranking_activeTab';

        // Fungsi untuk menyimpan tab aktif ke localStorage
        function saveActiveTab(tabId) {
            localStorage.setItem(storageKey, tabId);
        }

        // Fungsi untuk memuat tab aktif dari localStorage
        function loadActiveTab() {
            const savedTabId = localStorage.getItem(storageKey);
            if (savedTabId) {
                // Hapus class active dari semua tab dan tab pane
                $('.nav-link').removeClass('active');
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
            const firstTab = $('.nav-link').first();
            const firstTabId = firstTab.attr('id');
            if (firstTabId) {
                const tabId = firstTabId.replace('tab-', '');
                saveActiveTab(tabId);
                firstTab.addClass('active').attr('aria-selected', 'true');
                $('#kelas-' + tabId).addClass('show active');
            }
        }

        // Event listener untuk menyimpan tab saat tab diubah dan recalculate DataTable
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            const tabId = $(e.target).attr('id').replace('tab-', '');
            saveActiveTab(tabId);
            
            const tableSelector = "#TableRanking-" + tabId;
            
            // Inisialisasi DataTable jika belum diinisialisasi
            if (!$.fn.DataTable.isDataTable(tableSelector)) {
                initRankingDataTable(tableSelector);
            }

            // Recalculate DataTable columns untuk tab yang baru ditampilkan
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                const table = $(tableSelector).DataTable();
                // Delay untuk memastikan tab pane sudah fully visible
                setTimeout(function() {
                    try {
                        // Recalculate columns dan scrollX
                        table.columns.adjust();
                        // Trigger resize untuk memastikan scrollX di-recalculate
                        $(window).trigger('resize');
                    } catch (e) {
                        console.warn('Error recalculating DataTable on tab switch:', e);
                    }
                }, 100);
            }
        });

        // Memuat tab yang tersimpan saat halaman dimuat
        loadActiveTab();

        // Fungsi untuk inisialisasi DataTable dengan pengecekan
        function initRankingDataTable(tableSelector) {
            const $table = $(tableSelector);
            
            // Pastikan tabel ada dan memiliki tbody
            if (!$table.length) {
                console.warn('Table not found:', tableSelector);
                return;
            }
            
            const tbody = $table.find('tbody');
            if (!tbody.length || tbody.find('tr').length === 0) {
                console.warn('Table has no tbody or rows:', tableSelector);
                return;
            }
            
            // Cek apakah DataTable sudah diinisialisasi
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                return;
            }
            
            try {
                initializeDataTableScrollX(tableSelector, [], {
                    "order": [[0, "asc"]], // Sort by ranking column (index 0) ascending
                    "columnDefs": [
                        {
                            "type": "num", // Set ranking column as numeric type
                            "targets": 0   // Target first column (ranking)
                        }
                    ]
                });
            } catch (e) {
                console.error('Error initializing DataTable for', tableSelector, ':', e);
            }
        }

        // Initial DataTable hanya untuk tab aktif terlebih dahulu
        setTimeout(function() {
            const activeTab = $('.nav-link.active');
            if (activeTab.length) {
                const activeTabId = activeTab.attr('id').replace('tab-', '');
                const activeTableSelector = "#TableRanking-" + activeTabId;
                initRankingDataTable(activeTableSelector);
            }
        }, 100);

    });
</script>
<?= $this->endSection(); ?>

