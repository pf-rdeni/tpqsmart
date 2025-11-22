<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
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
                            <strong>Lihat Status Penilaian:</strong> Setiap baris santri menampilkan indikator status penilaian:
                            <ul class="mt-2">
                                <li><i class="fas fa-exclamation-circle" style="color:red"></i> <span style="color:red;"><strong>Merah</strong></span> - Belum selesai dinilai</li>
                                <li><i class="fas fa-check-circle" style="color:green"></i> <span style="color:green;"><strong>Hijau</strong></span> - Sudah selesai dinilai</li>
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
                                <li>Guru Kelas</li>
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
                            <li>Setelah semua nilai diisi dan disimpan, status akan berubah menjadi <strong>Sudah Selesai Dinilai</strong> dan tombol akan berubah menjadi <strong>View</strong>.</li>
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
                }
            }
        }

        // Event listener untuk menyimpan tab saat tab diubah
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            const tabId = $(e.target).attr('id').replace('tab-', '');
            saveActiveTab(tabId);
        });

        // Memuat tab yang tersimpan saat halaman dimuat
        loadActiveTab();

        // Initial DataTabel per kelas
        <?php foreach ($dataKelas as $kelasId => $kelas): ?>
            initializeDataTableUmum("#TableNilaiSemester-<?= $kelasId ?>", true, true);
        <?php endforeach; ?>
    });
</script>
<?= $this->endSection(); ?>