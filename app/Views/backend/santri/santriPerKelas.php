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
</style>
<div class="col-12">
    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Data Santri Per Kelas
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
                            <strong>Pilih Kelas:</strong> Gunakan tab di atas untuk memilih kelas yang ingin dikelola.
                            Tab <strong>"SEMUA"</strong> menampilkan semua santri dari semua kelas.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Progress Penilaian:</strong> Setiap baris santri menampilkan icon progress persentase yang menunjukkan seberapa banyak nilai yang sudah diisi:
                            <ul class="mt-2">
                                <li>Icon <i class="fas fa-hourglass-half"></i> dengan badge persentase menampilkan progress pengisian nilai</li>
                                <li>Warna badge berubah sesuai progress: <span class="badge badge-success">Hijau (100%)</span>, <span class="badge badge-info">Biru (75-99%)</span>, <span class="badge badge-warning">Kuning (50-74%)</span>, <span class="badge badge-danger">Merah (1-49%)</span>, <span class="badge badge-secondary">Abu-abu (0%)</span></li>
                                <li>Hover pada icon untuk melihat detail: "Progress: X/Y materi (Z%)"</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Edit Nilai:</strong> Klik tombol <span class="badge badge-warning"><i class="fas fa-edit"></i> Edit</span>
                            pada baris santri untuk mengisi atau mengubah nilai santri. Tombol ini hanya muncul untuk:
                            <ul class="mt-2">
                                <li>Guru Kelas atau Wali Kelas</li>
                                <li>Santri yang belum selesai dinilai (StatusPenilaian = 0)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>View Nilai:</strong> Klik tombol <span class="badge badge-primary"><i class="fas fa-eye"></i> View</span>
                            pada baris santri untuk melihat nilai yang sudah diisi. Tombol ini muncul untuk:
                            <ul class="mt-2">
                                <li>Guru Kelas atau Wali Kelas</li>
                                <li>Santri yang sudah selesai dinilai (StatusPenilaian = 1)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Export Data:</strong> Gunakan tombol export di DataTable (Copy, Excel, dll) untuk menyalin atau mengunduh data santri ke file Excel.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Tab aktif akan <strong>tersimpan otomatis</strong> di browser Anda, sehingga saat kembali ke halaman ini, tab terakhir yang dibuka akan otomatis aktif.</li>
                            <li>Data dapat diurutkan dan difilter menggunakan fitur DataTable (search box, sorting, dll).</li>
                            <li>Hanya <strong>Guru Kelas</strong> atau <strong>Wali Kelas</strong> yang dapat mengedit nilai santri di kelas mereka.</li>
                            <li>Progress persentase akan otomatis terupdate saat nilai diisi. Setelah semua nilai diisi (100%), tombol akan berubah menjadi <strong>View</strong>.</li>
                            <li>Gunakan tab <strong>"SEMUA"</strong> untuk melihat semua santri sekaligus tanpa filter kelas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                                <?php
                                                // OPTIMASI: Gunakan progress yang sudah dihitung di controller
                                                $progress = $progressData[$santri->IdSantri] ?? [
                                                    'totalMateri' => 0,
                                                    'materiTerisi' => 0,
                                                    'persentase' => 0,
                                                    'badgeColor' => 'secondary'
                                                ];
                                                $totalMateri = $progress['totalMateri'];
                                                $materiTerisi = $progress['materiTerisi'];
                                                $persentase = $progress['persentase'];
                                                $badgeColor = $progress['badgeColor'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex justify-content-start align-items-center">
                                                            <?php
                                                            // Logika tombol Edit/View:
                                                            // 1. Jika login sebagai Wali Kelas → selalu bisa Edit (tidak peduli status nilai)
                                                            // 2. Jika login sebagai Guru Pendamping/Guru Kelas → 
                                                            //    - Jika nilai sudah 100% → hanya View
                                                            //    - Jika nilai belum 100% → bisa Edit
                                                            $showEdit = false;
                                                            $showView = false;

                                                            // Cek apakah user login sebagai Wali Kelas
                                                            $isUserWaliKelas = isset($isWaliKelas) && $isWaliKelas;

                                                            // Cek apakah user memiliki jabatan yang berhak mengakses (Guru Kelas, Wali Kelas, atau Guru Pendamping)
                                                            $hasAccess = ($santri->NamaJabatan == "Guru Kelas" || $santri->NamaJabatan == "Wali Kelas" || $santri->NamaJabatan == "Guru Pendamping");

                                                            if ($hasAccess) {
                                                                if ($isUserWaliKelas) {
                                                                    // Wali Kelas: selalu bisa Edit (tidak peduli status nilai)
                                                                    $showEdit = true;
                                                                } else {
                                                                    // Guru Pendamping/Guru Kelas: tergantung status nilai
                                                                    if ($santri->StatusPenilaian == 0) {
                                                                        // Nilai belum 100% → bisa Edit
                                                                        $showEdit = true;
                                                                    } elseif ($santri->StatusPenilaian == 1) {
                                                                        // Nilai sudah 100% → hanya View
                                                                        $showView = true;
                                                                    }
                                                                }
                                                            }

                                                            if ($showEdit) : ?>
                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 1 . '/' . $santri->IdJabatan) ?>" class="btn btn-warning me-2">
                                                                    <i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>&nbsp;Edit&nbsp;
                                                                </a>
                                                            <?php elseif ($showView) : ?>
                                                                <a href="<?= base_url('backend/nilai/showDetail/' . $santri->IdSantri . '/' . $semester . '/' . 0 . '/' . $santri->IdJabatan) ?>" class="btn btn-primary me-2">
                                                                    <i class="fas fa-eye"></i><span style="margin-left: 5px;"></span>View
                                                                </a>
                                                            <?php endif; ?>

                                                            <!-- Icon Progress Persentase - Tampilkan jika ada data materi -->
                                                            <?php if ($totalMateri > 0): ?>
                                                                <button type="button" class="btn btn-<?= $badgeColor ?> me-2" data-toggle="tooltip" data-placement="top"
                                                                    title="Progress: <?= $materiTerisi ?>/<?= $totalMateri ?> materi (<?= $persentase ?>%)">
                                                                    <i class="fas fa-hourglass-half"></i>
                                                                    <span class="badge badge-light ml-1"><?= $persentase ?>%</span>
                                                                </button>
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

        // Key untuk localStorage
        const storageKey = 'santriPerKelas_activeTab';

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

            // Recalculate DataTable columns untuk tab yang baru ditampilkan
            const tableSelector = "#TableNilaiSemester-" + tabId;
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

        // Initial DataTable per kelas dengan scroll horizontal untuk mobile
        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
            initializeDataTableScrollX("#TableNilaiSemester-<?= $kelasId ?>");
        <?php endforeach; ?>

        // Recalculate DataTable untuk tab aktif setelah semua tabel diinisialisasi
        setTimeout(function() {
            const activeTab = $('.nav-link.active');
            if (activeTab.length) {
                const activeTabId = activeTab.attr('id').replace('tab-', '');
                const activeTableSelector = "#TableNilaiSemester-" + activeTabId;
                if ($.fn.DataTable.isDataTable(activeTableSelector)) {
                    const activeTable = $(activeTableSelector).DataTable();
                    activeTable.columns.adjust();
                }
            }
        }, 500);
    });
</script>
<?= $this->endSection(); ?>