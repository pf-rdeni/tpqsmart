<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content') ?>
<div class="col-12">
    <!-- Informasi Proses Flow -->
    <div class="card card-info card-outline collapsed-card mb-3">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Informasi Proses Normalisasi Data
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <h5><i class="fas fa-list-ol"></i> Halaman ini memiliki 3 fungsi normalisasi data:</h5>

                    <div class="mb-4">
                        <h6 class="text-primary"><i class="fas fa-users"></i> 1. Normalisasi Duplikasi Kelas Santri</h6>
                        <div class="alert alert-light border">
                            <strong><i class="fas fa-info-circle"></i> Fungsi:</strong> Membersihkan data duplikat di tabel kelas santri
                            <br><strong><i class="fas fa-mouse-pointer"></i> Tombol:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>"Cek Duplikasi"</strong> - Menampilkan santri yang memiliki duplikasi (santri yang sama di kelas yang sama, tahun ajaran yang sama, TPQ yang sama)</li>
                                <li><strong>"Normalisasi Duplikasi"</strong> - Menghapus duplikasi, menyisakan 1 record tertua per kombinasi</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-warning"><i class="fas fa-check-double"></i> 2. Normalisasi Data Nilai</h6>
                        <div class="alert alert-light border">
                            <strong><i class="fas fa-info-circle"></i> Fungsi:</strong> Membersihkan data nilai yang tidak valid atau duplikat untuk santri yang TERDAFTAR di kelas
                            <br><strong><i class="fas fa-mouse-pointer"></i> Tombol:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>"Cek Normalisasi Nilai"</strong> - Menampilkan data nilai yang:
                                    <ul>
                                        <li><span class="badge badge-danger">Tidak Valid</span> - Materi tidak sesuai dengan kelas (IdMateri tidak ada di tbl_kelas_materi_pelajaran)</li>
                                        <li><span class="badge badge-success">Duplikat Aman</span> - Ada duplikat dengan nilai kosong (aman dihapus)</li>
                                        <li><span class="badge badge-warning">Duplikat Perhatian</span> - Ada duplikat dengan nilai (perlu review)</li>
                                    </ul>
                                </li>
                                <li><strong>"Normalisasi Nilai"</strong> - Menghapus data nilai yang dipilih (tidak valid atau duplikat)</li>
                            </ul>
                            <small class="text-muted"><i class="fas fa-lightbulb"></i> <strong>Catatan:</strong> Hanya mengecek data nilai untuk santri yang sudah terdaftar di tbl_kelas_santri</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-info"><i class="fas fa-exclamation-triangle"></i> 3. Normalisasi Nilai Tanpa Kelas Santri</h6>
                        <div class="alert alert-light border">
                            <strong><i class="fas fa-info-circle"></i> Fungsi:</strong> Membersihkan data nilai untuk santri yang TIDAK TERDAFTAR di kelas (tidak ada di tbl_kelas_santri)
                            <br><strong><i class="fas fa-mouse-pointer"></i> Tombol:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>"Cek Nilai Tanpa Kelas Santri"</strong> - Menampilkan data nilai yang tidak memiliki referensi di tbl_kelas_santri, dengan kategori:
                                    <ul>
                                        <li><span class="badge badge-warning">Pindah TPQ</span> - Santri sudah pindah ke TPQ lain</li>
                                        <li><span class="badge badge-secondary">Tidak Aktif</span> - Santri tidak aktif (Active = 0)</li>
                                        <li><span class="badge badge-info">Tidak Terdaftar</span> - Santri ada di TPQ tapi tidak terdaftar di kelas untuk tahun ajaran tersebut</li>
                                        <li><span class="badge badge-danger">Tidak Ditemukan</span> - Santri tidak ada di database (kemungkinan pindah luar daerah)</li>
                                    </ul>
                                </li>
                                <li><strong>"Normalisasi Nilai Tanpa Kelas Santri"</strong> - Menghapus data nilai yang dipilih (data tanpa referensi kelas santri)</li>
                            </ul>
                            <small class="text-muted"><i class="fas fa-lightbulb"></i> <strong>Catatan:</strong> Mengecek data nilai untuk santri yang TIDAK ada di tbl_kelas_santri. Gunakan ini untuk membersihkan data nilai santri yang sudah pindah TPQ atau tidak terdaftar.</small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-sync-alt"></i> Urutan Normalisasi yang Disarankan:</h6>
                        <ol class="mb-0">
                            <li><strong>Normalisasi Duplikasi Kelas Santri</strong> - Bersihkan duplikasi kelas santri terlebih dahulu</li>
                            <li><strong>Normalisasi Nilai Tanpa Kelas Santri</strong> - Hapus data nilai untuk santri yang tidak terdaftar di kelas</li>
                            <li><strong>Normalisasi Data Nilai</strong> - Bersihkan data nilai yang tidak valid atau duplikat untuk santri yang terdaftar</li>
                        </ol>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                        <ul class="mb-0">
                            <li>Proses normalisasi akan <strong>menghapus data</strong>, pastikan sudah melakukan backup database</li>
                            <li>Hanya data yang dipilih yang akan dihapus, data yang valid tidak akan terpengaruh</li>
                            <li>Untuk duplikat dengan kategori <strong>"Perhatian"</strong>, pastikan untuk review nilai sebelum menghapus</li>
                            <li>Setelah normalisasi, disarankan untuk menjalankan <strong>"Perbarui Materi"</strong> di halaman Kelas Materi Pelajaran</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-sync-alt"></i> Normalisasi Data
                </h3>
                <div class="card-tools">
                    <?php if (in_groups('Admin')): ?>
                        <button type="button" class="btn btn-warning btn-sm mr-2" id="btnCheckNormalisasiNilai" title="Cek data nilai yang tidak valid dan duplikat">
                            <i class="fas fa-search"></i> Cek Normalisasi Nilai
                        </button>
                        <button type="button" class="btn btn-danger btn-sm mr-2" id="btnNormalisasiNilai" style="display: none;" title="Hapus data nilai yang dipilih">
                            <i class="fas fa-sync"></i> Normalisasi Nilai
                        </button>
                        <button type="button" class="btn btn-info btn-sm mr-2" id="btnCheckNilaiTanpaKelasSantri" title="Cek data nilai yang tidak ada di tabel kelas santri">
                            <i class="fas fa-search"></i> Cek Nilai Tanpa Kelas Santri
                        </button>
                        <button type="button" class="btn btn-danger btn-sm mr-2" id="btnNormalisasiNilaiTanpaKelasSantri" style="display: none;" title="Hapus data nilai yang tidak ada di kelas santri">
                            <i class="fas fa-sync"></i> Normalisasi Nilai Tanpa Kelas Santri
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary btn-sm" id="btnCheckDuplikasi">
                        <i class="fas fa-search"></i> Cek Duplikasi
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnNormalisasi" style="display: none;">
                        <i class="fas fa-sync"></i> Normalisasi Duplikasi
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="loadingIndicator" style="display: none;" class="text-center mb-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Memproses data...</p>
            </div>

            <div id="resultContainer" style="display: none;">
                <div class="alert alert-info" id="summaryAlert">
                    <i class="fas fa-info-circle"></i> <span id="summaryText"></span>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-secondary" id="btnSelectAll">
                        <i class="fas fa-check-square"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnUnselectAll" style="display: none;">
                        <i class="fas fa-square"></i> Batal Pilih Semua
                    </button>
                    <span class="ml-3" id="selectedCount">0 item dipilih</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tblDuplikasi">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="checkAll" title="Pilih Semua">
                                </th>
                                <th>No</th>
                                <th>Nama Santri</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>TPQ</th>
                                <th>Jumlah Duplikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDuplikasi">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Modal untuk detail duplikasi -->
                <div class="modal fade" id="modalDetailDuplikasi" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">Detail Duplikasi</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="detailContent">
                                    <!-- Detail akan diisi melalui JavaScript -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="noDataContainer" style="display: none;">
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h4>Tidak Ada Duplikasi</h4>
                    <p>Data kelas santri sudah bersih, tidak ada duplikasi yang ditemukan.</p>
                </div>
            </div>

            <!-- Section untuk Normalisasi Nilai -->
            <div id="normalisasiNilaiContainer" style="display: none;">
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-check-double"></i> Normalisasi Data Nilai</h5>

                <div id="loadingNormalisasi" style="display: none;" class="text-center mb-3">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memproses data...</p>
                </div>

                <div id="resultNormalisasi" style="display: none;">
                    <div class="alert alert-warning" id="summaryNormalisasi">
                        <i class="fas fa-info-circle"></i> <span id="summaryNormalisasiText"></span>
                    </div>

                    <!-- Rangkuman per TPQ -->
                    <div class="card card-info mb-3" id="summaryTpqContainer">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie"></i> Rangkuman Ketidaksesuaian per TPQ
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="summaryTpqCards">
                                <!-- Rangkuman akan diisi melalui JavaScript -->
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-primary" id="btnFilterAllTpq">
                                    <i class="fas fa-filter"></i> Tampilkan Semua TPQ
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="btnClearFilterTpq" style="display: none;">
                                    <i class="fas fa-times"></i> Hapus Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-secondary" id="btnSelectAllNilai">
                            <i class="fas fa-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="btnUnselectAllNilai" style="display: none;">
                            <i class="fas fa-square"></i> Batal Pilih Semua
                        </button>
                        <span class="ml-3" id="selectedCountNilai">0 item dipilih</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tblNormalisasiNilai">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="checkAllNilai" title="Pilih Semua">
                                    </th>
                                    <th>No</th>
                                    <th>IdSantri</th>
                                    <th>Nama Santri</th>
                                    <th>Materi</th>
                                    <th>Kelas</th>
                                    <th>TPQ</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Nilai</th>
                                    <th>Jenis Masalah</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyNormalisasiNilai">
                                <!-- Data akan diisi melalui JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="noDataNormalisasi" style="display: none;">
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Data Sudah Normal</h4>
                        <p>Data nilai sudah bersih, tidak ada data yang tidak valid atau duplikat.</p>
                    </div>
                </div>
            </div>

            <!-- Section untuk Normalisasi Nilai Tanpa Kelas Santri -->
            <div id="normalisasiNilaiTanpaKelasSantriContainer" style="display: none;">
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Normalisasi Nilai Tanpa Kelas Santri</h5>

                <div id="loadingNilaiTanpaKelasSantri" style="display: none;" class="text-center mb-3">
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memproses data...</p>
                </div>

                <div id="resultNilaiTanpaKelasSantri" style="display: none;">
                    <div class="alert alert-info" id="summaryNilaiTanpaKelasSantri">
                        <i class="fas fa-info-circle"></i> <span id="summaryNilaiTanpaKelasSantriText"></span>
                    </div>

                    <!-- Rangkuman per TPQ -->
                    <div class="card card-info mb-3" id="summaryTpqNilaiTanpaKelasSantriContainer">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie"></i> Rangkuman per TPQ
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="summaryTpqNilaiTanpaKelasSantriCards">
                                <!-- Rangkuman akan diisi melalui JavaScript -->
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-primary" id="btnFilterAllTpqNilaiTanpaKelasSantri">
                                    <i class="fas fa-filter"></i> Tampilkan Semua TPQ
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="btnClearFilterTpqNilaiTanpaKelasSantri" style="display: none;">
                                    <i class="fas fa-times"></i> Hapus Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-secondary" id="btnSelectAllNilaiTanpaKelasSantri">
                            <i class="fas fa-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="btnUnselectAllNilaiTanpaKelasSantri" style="display: none;">
                            <i class="fas fa-square"></i> Batal Pilih Semua
                        </button>
                        <span class="ml-3" id="selectedCountNilaiTanpaKelasSantri">0 item dipilih</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tblNilaiTanpaKelasSantri">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="checkAllNilaiTanpaKelasSantri" title="Pilih Semua">
                                    </th>
                                    <th>No</th>
                                    <th>IdSantri</th>
                                    <th>Nama Santri</th>
                                    <th>Materi</th>
                                    <th>Kelas</th>
                                    <th>TPQ</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Semester</th>
                                    <th>Nilai</th>
                                    <th>Jenis Masalah</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyNilaiTanpaKelasSantri">
                                <!-- Data akan diisi melalui JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="noDataNilaiTanpaKelasSantri" style="display: none;">
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Data Sudah Normal</h4>
                        <p>Semua data nilai memiliki referensi di tabel kelas santri.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize DataTable
    let dataTableDuplikasi = null;

    // Button Check Duplikasi
    $('#btnCheckDuplikasi').on('click', function() {
        checkDuplikasi();
    });

    // Button Normalisasi
    $('#btnNormalisasi').on('click', function() {
        normalisasiDuplikasi();
    });

    // Button Select All
    $('#btnSelectAll').on('click', function() {
        $('input[type="checkbox"][name="duplikasiCheckbox"]').prop('checked', true);
        $('#checkAll').prop('checked', true);
        updateSelectedCount();
        $('#btnSelectAll').hide();
        $('#btnUnselectAll').show();
    });

    // Button Unselect All
    $('#btnUnselectAll').on('click', function() {
        $('input[type="checkbox"][name="duplikasiCheckbox"]').prop('checked', false);
        $('#checkAll').prop('checked', false);
        updateSelectedCount();
        $('#btnSelectAll').show();
        $('#btnUnselectAll').hide();
    });

    // Check All checkbox
    $('#checkAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('input[type="checkbox"][name="duplikasiCheckbox"]').prop('checked', isChecked);
        updateSelectedCount();
        if (isChecked) {
            $('#btnSelectAll').hide();
            $('#btnUnselectAll').show();
        } else {
            $('#btnSelectAll').show();
            $('#btnUnselectAll').hide();
        }
    });

    // Update selected count
    function updateSelectedCount() {
        const count = $('input[type="checkbox"][name="duplikasiCheckbox"]:checked').length;
        $('#selectedCount').text(count + ' item dipilih');

        // Update checkAll status
        const total = $('input[type="checkbox"][name="duplikasiCheckbox"]').length;
        $('#checkAll').prop('checked', count === total && total > 0);

        if (count > 0) {
            $('#btnSelectAll').hide();
            $('#btnUnselectAll').show();
        } else {
            $('#btnSelectAll').show();
            $('#btnUnselectAll').hide();
        }
    }

    function checkDuplikasi() {
        // Tampilkan loading
        $('#loadingIndicator').show();
        $('#resultContainer').hide();
        $('#noDataContainer').hide();
        $('#btnCheckDuplikasi').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('backend/kelas/checkDuplikasiKelasSantri') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loadingIndicator').hide();
                $('#btnCheckDuplikasi').prop('disabled', false);

                if (response.status === 'success') {
                    if (response.total_duplikasi > 0) {
                        displayDuplikasi(response.data);
                        $('#btnNormalisasi').show();
                    } else {
                        $('#noDataContainer').show();
                        $('#btnNormalisasi').hide();
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat mengecek duplikasi',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loadingIndicator').hide();
                $('#btnCheckDuplikasi').prop('disabled', false);

                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengecek duplikasi: ' + error,
                    icon: 'error'
                });
            }
        });
    }

    function displayDuplikasi(data) {
        // Update summary
        $('#summaryText').text(`Ditemukan ${data.length} grup duplikasi. Klik "Lihat Detail" untuk melihat semua record yang duplikasi.`);
        $('#summaryAlert').removeClass('alert-success alert-danger').addClass('alert-warning');

        // Destroy DataTable jika sudah ada
        if (dataTableDuplikasi) {
            dataTableDuplikasi.destroy();
        }

        // Clear tbody
        $('#tbodyDuplikasi').empty();

        // Populate table
        data.forEach(function(item, index) {
            // Buat key unik untuk setiap grup duplikasi
            const uniqueKey = `${item.IdSantri}_${item.IdKelas}_${item.IdTahunAjaran}_${item.IdTpq}`;
            const row = `
                <tr>
                    <td>
                        <input type="checkbox" 
                               name="duplikasiCheckbox" 
                               class="duplikasi-checkbox" 
                               value="${uniqueKey}"
                               data-index="${index}">
                    </td>
                    <td>${index + 1}</td>
                    <td>${item.NamaSantri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaKelas || 'Tidak ditemukan'}</td>
                    <td>${item.IdTahunAjaran}</td>
                    <td>${item.NamaTpq || 'Tidak ditemukan'}</td>
                    <td><span class="badge badge-danger">${item.jumlah_duplikasi} record</span></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="showDetailDuplikasi(${index})" data-index="${index}">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </button>
                    </td>
                </tr>
            `;
            $('#tbodyDuplikasi').append(row);
        });

        // Reset checkbox state
        updateSelectedCount();

        // Store data globally untuk detail modal
        window.duplikasiData = data;

        // Initialize DataTable
        dataTableDuplikasi = $('#tblDuplikasi').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "columnDefs": [{
                "orderable": false,
                "targets": 0 // Kolom checkbox tidak bisa di-sort
            }]
        });

        // Event handler untuk checkbox setelah DataTable diinisialisasi
        $(document).on('change', 'input[type="checkbox"][name="duplikasiCheckbox"]', function() {
            updateSelectedCount();
        });

        $('#resultContainer').show();
    }

    function showDetailDuplikasi(index) {
        const item = window.duplikasiData[index];
        let detailHtml = `
            <div class="alert alert-warning">
                <strong>Nama Santri:</strong> ${item.NamaSantri || 'Tidak ditemukan'}<br>
                <strong>Kelas:</strong> ${item.NamaKelas || 'Tidak ditemukan'}<br>
                <strong>Tahun Ajaran:</strong> ${item.IdTahunAjaran}<br>
                <strong>TPQ:</strong> ${item.NamaTpq || 'Tidak ditemukan'}<br>
                <strong>Jumlah Duplikasi:</strong> ${item.jumlah_duplikasi} record
            </div>
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
        `;

        item.detail.forEach(function(detail, idx) {
            const isTertua = idx === 0;
            detailHtml += `
                <tr class="${isTertua ? 'table-success' : 'table-danger'}">
                    <td>${detail.Id}</td>
                    <td>${detail.Status == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Tidak Aktif</span>'}</td>
                    <td>${detail.created_at || '-'}</td>
                    <td>${detail.updated_at || '-'}</td>
                    <td>
                        ${isTertua ? '<span class="badge badge-success">Record Tertua (Akan Disimpan)</span>' : '<span class="badge badge-danger">Akan Dihapus</span>'}
                    </td>
                </tr>
            `;
        });

        detailHtml += `
                </tbody>
            </table>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Catatan:</strong> Record dengan ID terkecil (tertua) akan disimpan, record lainnya akan dihapus saat normalisasi.
            </div>
        `;

        $('#detailContent').html(detailHtml);
        $('#modalDetailDuplikasi').modal('show');
    }

    function normalisasiDuplikasi() {
        // Ambil semua checkbox yang dicentang
        const selectedCheckboxes = $('input[type="checkbox"][name="duplikasiCheckbox"]:checked');

        if (selectedCheckboxes.length === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Silakan pilih minimal satu grup duplikasi yang akan dinormalisasi.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Kumpulkan data yang dipilih
        const selectedData = [];
        selectedCheckboxes.each(function() {
            const index = $(this).data('index');
            selectedData.push(window.duplikasiData[index]);
        });

        Swal.fire({
            title: 'Konfirmasi Normalisasi',
            html: `
                <p>Apakah Anda yakin ingin menormalisasi <strong>${selectedData.length}</strong> grup duplikasi yang dipilih?</p>
                <p class="text-danger"><strong>Peringatan:</strong> Proses ini akan menghapus record duplikasi. Pastikan sudah melakukan backup database.</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Normalisasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses Normalisasi',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim data yang dipilih
                $.ajax({
                    url: '<?= base_url('backend/kelas/normalisasiDuplikasiKelasSantri') ?>',
                    type: 'POST',
                    data: {
                        selectedData: selectedData
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `
                                    <p>${response.message}</p>
                                    <p><strong>Total Grup:</strong> ${response.total_groups}</p>
                                    <p><strong>Total Dihapus:</strong> ${response.total_deleted} record</p>
                                `,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh data
                                checkDuplikasi();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Terjadi kesalahan saat normalisasi',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat normalisasi: ' + error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Initialize DataTable untuk Normalisasi Nilai
    let dataTableNormalisasiNilai = null;

    // Handler untuk tombol Cek Normalisasi Nilai
    $('#btnCheckNormalisasiNilai').on('click', function() {
        checkNormalisasiNilai();
    });

    // Handler untuk tombol Normalisasi Nilai (setelah data ditampilkan)
    $('#btnNormalisasiNilai').on('click', function() {
        normalisasiNilaiSelected();
    });

    function checkNormalisasiNilai() {
        // Tampilkan loading
        $('#loadingNormalisasi').show();
        $('#resultNormalisasi').hide();
        $('#noDataNormalisasi').hide();
        $('#normalisasiNilaiContainer').show();
        $('#btnCheckNormalisasiNilai').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('backend/santri/checkNormalisasiNilai') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loadingNormalisasi').hide();
                $('#btnCheckNormalisasiNilai').prop('disabled', false);

                if (response.success) {
                    if (response.total_to_delete > 0) {
                        displayNormalisasiData(response);
                        $('#btnNormalisasiNilai').show();
                    } else {
                        $('#noDataNormalisasi').show();
                        $('#btnNormalisasiNilai').hide();
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat mengecek data',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loadingNormalisasi').hide();
                $('#btnCheckNormalisasiNilai').prop('disabled', false);

                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengecek data: ' + error,
                    icon: 'error'
                });
            }
        });
    }

    // Variable untuk menyimpan data dan filter
    let allNormalisasiData = [];
    let selectedTpqFilter = null;

    function displayNormalisasiData(data) {
        // Simpan data global
        allNormalisasiData = [];
        data.invalid_data.forEach(item => {
            allNormalisasiData.push(item);
        });
        data.duplicate_data.forEach(item => {
            allNormalisasiData.push(item);
        });

        // Update summary
        const summaryText = `Ditemukan ${data.total_invalid} data tidak valid dan ${data.total_duplicate} data duplikat (Total: ${data.total_to_delete} data yang perlu dihapus).`;
        $('#summaryNormalisasiText').text(summaryText);
        $('#summaryNormalisasi').removeClass('alert-success alert-danger').addClass('alert-warning');

        // Tampilkan rangkuman per TPQ
        displaySummaryByTpq(data.summary_by_tpq || []);

        // Render tabel
        renderNormalisasiTable();
    }

    function displaySummaryByTpq(summaryByTpq) {
        const container = $('#summaryTpqCards');
        container.empty();

        if (summaryByTpq.length === 0) {
            container.html('<div class="col-12"><p class="text-muted">Tidak ada data rangkuman</p></div>');
            return;
        }

        summaryByTpq.forEach(function(tpq) {
            const card = `
                <div class="col-md-4 mb-3">
                    <div class="card card-tpq-summary" data-tpq-id="${tpq.IdTpq}" style="cursor: pointer; border: 2px solid #dee2e6; transition: all 0.3s; height: 100%;" onclick="filterByTpq('${tpq.IdTpq}')">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2 font-weight-bold" style="font-size: 1rem; line-height: 1.4;" title="${tpq.NamaTpq}">${tpq.NamaTpq}</h6>
                            <div class="text-right mb-3">
                                <span class="text-danger font-weight-bold" style="font-size: 2rem; line-height: 1;">${tpq.total || 0}</span>
                            </div>
                            <div class="pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.875rem;">Tidak Valid:</span>
                                    <span class="badge badge-danger" style="font-size: 0.875rem; padding: 0.4em 0.7em; min-width: 45px;">${tpq.total_invalid || 0}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.875rem;">Duplikat:</span>
                                    <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.4em 0.7em; min-width: 45px; background-color: #ffc107; color: #212529;">${tpq.total_duplicate || 0}</span>
                                </div>
                                <div class="pt-2 border-top mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-check-circle text-success"></i> Aman:
                                        </span>
                                        <span class="badge badge-success" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.total_duplicate_aman || 0}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-exclamation-triangle text-warning"></i> Perhatian:
                                        </span>
                                        <span class="badge badge-warning" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px; background-color: #ffc107; color: #212529;">${tpq.total_duplicate_perhatian || 0}</span>
                                    </div>
                                </div>
                                <div class="pt-2 border-top mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-users text-info"></i> Santri Terkena:
                                        </span>
                                        <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.jumlah_santri_terkena || 0}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function filterByTpq(idTpq) {
        if (selectedTpqFilter === idTpq) {
            // Jika klik yang sama, hapus filter
            selectedTpqFilter = null;
            $('.card-tpq-summary').css({
                'border': '2px solid #dee2e6',
                'box-shadow': 'none',
                'transform': 'scale(1)'
            });
            $('#btnClearFilterTpq').hide();
        } else {
            // Set filter baru
            selectedTpqFilter = idTpq;
            $('.card-tpq-summary').css({
                'border': '2px solid #dee2e6',
                'box-shadow': 'none',
                'transform': 'scale(1)'
            });
            $(`.card-tpq-summary[data-tpq-id="${idTpq}"]`).css({
                'border': '2px solid #007bff',
                'box-shadow': '0 0 10px rgba(0, 123, 255, 0.3)',
                'transform': 'scale(1.02)'
            });
            $('#btnClearFilterTpq').show();
        }
        renderNormalisasiTable();
    }

    function renderNormalisasiTable() {
        // Destroy DataTable jika sudah ada
        if (dataTableNormalisasiNilai) {
            dataTableNormalisasiNilai.destroy();
        }

        // Clear tbody
        $('#tbodyNormalisasiNilai').empty();

        // Filter data berdasarkan TPQ jika ada filter
        let filteredData = allNormalisasiData;
        if (selectedTpqFilter) {
            filteredData = allNormalisasiData.filter(item => item.IdTpq == selectedTpqFilter);
        }

        // Populate table
        filteredData.forEach(function(item, index) {
            let jenisMasalah = '';
            let keterangan = '';

            if (item.type === 'invalid') {
                jenisMasalah = '<span class="badge badge-danger">Tidak Valid</span>';
                keterangan = item.reason || 'IdMateri tidak ada di tbl_kelas_materi_pelajaran untuk IdKelas dan IdTpq ini, atau tidak sesuai dengan semester';
            } else if (item.type === 'duplicate') {
                // Tentukan badge berdasarkan kategori
                if (item.kategori === 'aman') {
                    jenisMasalah = '<span class="badge badge-success">Aman</span>';
                    keterangan = item.reason + ' - Nilai kosong, aman untuk dihapus';
                } else if (item.kategori === 'perhatian') {
                    jenisMasalah = '<span class="badge badge-warning">Perhatian</span>';
                    keterangan = item.reason + ' - Memiliki nilai (' + (item.Nilai || 0) + '), perlu direview sebelum dihapus';
                } else {
                    // Fallback jika kategori tidak ada
                    jenisMasalah = '<span class="badge badge-warning">Duplikat</span>';
                    keterangan = item.reason || 'Duplikat: IdMateri yang sama untuk IdSantri, IdKelas, IdTpq, IdTahunAjaran, dan Semester yang sama';
                }
            }

            const row = `
                <tr>
                    <td>
                        <input type="checkbox" 
                               name="nilaiCheckbox" 
                               class="nilai-checkbox" 
                               value="${item.Id}"
                               data-type="${item.type}"
                               checked>
                    </td>
                    <td>${index + 1}</td>
                    <td>${item.IdSantri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaSantri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaMateri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaKelas || 'Tidak ditemukan'}</td>
                    <td>${item.NamaTpq || 'Tidak ditemukan'}</td>
                    <td>${item.IdTahunAjaran}</td>
                    <td>${item.Semester}</td>
                    <td>${item.Nilai || 0}</td>
                    <td>
                        ${jenisMasalah}
                        <br><small class="text-muted">${keterangan}</small>
                    </td>
                </tr>
            `;
            $('#tbodyNormalisasiNilai').append(row);
        });

        // Reset checkbox state
        updateSelectedCountNilai();

        // Initialize DataTable
        dataTableNormalisasiNilai = $('#tblNormalisasiNilai').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "columnDefs": [{
                "orderable": false,
                "targets": 0 // Kolom checkbox tidak bisa di-sort
            }]
        });

        // Event handler untuk checkbox setelah DataTable diinisialisasi
        $(document).on('change', 'input[type="checkbox"][name="nilaiCheckbox"]', function() {
            updateSelectedCountNilai();
        });

        // Update checkbox state saat DataTable di-redraw (misalnya saat paging, search, dll)
        dataTableNormalisasiNilai.on('draw', function() {
            // Sync checkbox "Pilih Semua" dengan state checkbox yang terfilter
            if (dataTableNormalisasiNilai) {
                const filteredRows = dataTableNormalisasiNilai.rows({
                    search: 'applied'
                }).nodes().to$();
                const filteredCheckboxes = filteredRows.find('input[type="checkbox"][name="nilaiCheckbox"]');
                const checkedCount = filteredCheckboxes.filter(':checked').length;
                const totalCount = filteredCheckboxes.length;

                // Update checkbox "Pilih Semua" jika semua terpilih atau tidak ada yang terpilih
                if (totalCount > 0) {
                    $('#checkAllNilai').prop('checked', checkedCount === totalCount);
                }
                updateSelectedCountNilai();
            }
        });

        $('#resultNormalisasi').show();
    }

    function updateSelectedCountNilai() {
        if (dataTableNormalisasiNilai) {
            // Hitung hanya checkbox yang terfilter di DataTable
            const filteredRows = dataTableNormalisasiNilai.rows({
                search: 'applied'
            }).nodes().to$();
            const filteredCheckboxes = filteredRows.find('input[type="checkbox"][name="nilaiCheckbox"]');
            const selected = filteredCheckboxes.filter(':checked').length;
            const total = filteredCheckboxes.length;
            $('#selectedCountNilai').text(`${selected} dari ${total} item dipilih`);
        } else {
            // Fallback jika DataTable belum diinisialisasi
            const selected = $('input[type="checkbox"][name="nilaiCheckbox"]:checked').length;
            const total = $('input[type="checkbox"][name="nilaiCheckbox"]').length;
            $('#selectedCountNilai').text(`${selected} dari ${total} item dipilih`);
        }
    }

    // Handler untuk select all checkbox nilai
    $('#checkAllNilai').on('change', function() {
        const isChecked = $(this).prop('checked');
        selectAllFilteredNilai(isChecked);
        updateSelectedCountNilai();
    });

    $('#btnSelectAllNilai').on('click', function() {
        selectAllFilteredNilai(true);
        $('#checkAllNilai').prop('checked', true);
        updateSelectedCountNilai();
        $('#btnSelectAllNilai').hide();
        $('#btnUnselectAllNilai').show();
    });

    $('#btnUnselectAllNilai').on('click', function() {
        selectAllFilteredNilai(false);
        $('#checkAllNilai').prop('checked', false);
        updateSelectedCountNilai();
        $('#btnSelectAllNilai').show();
        $('#btnUnselectAllNilai').hide();
    });

    // Fungsi untuk memilih semua data yang terfilter (bukan hanya yang terlihat)
    function selectAllFilteredNilai(isChecked) {
        if (dataTableNormalisasiNilai) {
            // Gunakan DataTable API untuk mendapatkan semua row yang terfilter
            // { search: 'applied' } berarti hanya row yang sesuai dengan filter/search DataTable
            dataTableNormalisasiNilai.rows({
                search: 'applied'
            }).nodes().to$().find('input[type="checkbox"][name="nilaiCheckbox"]').prop('checked', isChecked);
        } else {
            // Fallback jika DataTable belum diinisialisasi
            $('input[type="checkbox"][name="nilaiCheckbox"]').prop('checked', isChecked);
        }
    }

    // Handler untuk filter TPQ
    $('#btnFilterAllTpq').on('click', function() {
        selectedTpqFilter = null;
        $('.card-tpq-summary').css('border', '2px solid #dee2e6');
        $('#btnClearFilterTpq').hide();
        renderNormalisasiTable();
    });

    $('#btnClearFilterTpq').on('click', function() {
        selectedTpqFilter = null;
        $('.card-tpq-summary').css('border', '2px solid #dee2e6');
        $('#btnClearFilterTpq').hide();
        renderNormalisasiTable();
    });

    function normalisasiNilaiSelected() {
        const selectedIds = [];

        if (dataTableNormalisasiNilai) {
            // Gunakan DataTable API untuk mengambil semua checkbox yang terpilih dari semua halaman
            // { search: 'applied' } untuk hanya mengambil row yang terfilter
            dataTableNormalisasiNilai.rows({
                search: 'applied'
            }).every(function() {
                const row = this.node();
                const checkbox = $(row).find('input[type="checkbox"][name="nilaiCheckbox"]');

                if (checkbox.is(':checked')) {
                    selectedIds.push(checkbox.val());
                }
            });
        } else {
            // Fallback jika DataTable belum diinisialisasi
            const selectedCheckboxes = $('input[type="checkbox"][name="nilaiCheckbox"]:checked');
            selectedCheckboxes.each(function() {
                selectedIds.push($(this).val());
            });
        }

        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Silakan pilih minimal satu data yang akan dinormalisasi.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Normalisasi',
            html: `Anda akan menghapus <strong>${selectedIds.length}</strong> data nilai yang dipilih. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Normalisasi...',
                    html: 'Mohon tunggu, sedang menghapus data nilai...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/santri/normalisasiNilai') ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        ids: selectedIds
                    }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh data
                                checkNormalisasiNilai();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat melakukan normalisasi: ' + error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // Initialize DataTable untuk Normalisasi Nilai Tanpa Kelas Santri
    let dataTableNilaiTanpaKelasSantri = null;
    let allNilaiTanpaKelasSantriData = [];
    let selectedTpqFilterNilaiTanpaKelasSantri = null;

    // Handler untuk tombol Cek Nilai Tanpa Kelas Santri
    $('#btnCheckNilaiTanpaKelasSantri').on('click', function() {
        checkNilaiTanpaKelasSantri();
    });

    // Handler untuk tombol Normalisasi Nilai Tanpa Kelas Santri
    $('#btnNormalisasiNilaiTanpaKelasSantri').on('click', function() {
        normalisasiNilaiTanpaKelasSantriSelected();
    });

    function checkNilaiTanpaKelasSantri() {
        // Tampilkan loading
        $('#loadingNilaiTanpaKelasSantri').show();
        $('#resultNilaiTanpaKelasSantri').hide();
        $('#noDataNilaiTanpaKelasSantri').hide();
        $('#normalisasiNilaiTanpaKelasSantriContainer').show();
        $('#btnCheckNilaiTanpaKelasSantri').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('backend/kelas/checkNilaiTanpaKelasSantri') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loadingNilaiTanpaKelasSantri').hide();
                $('#btnCheckNilaiTanpaKelasSantri').prop('disabled', false);

                if (response.success) {
                    if (response.total_to_delete > 0) {
                        displayNilaiTanpaKelasSantriData(response);
                        $('#btnNormalisasiNilaiTanpaKelasSantri').show();
                    } else {
                        $('#noDataNilaiTanpaKelasSantri').show();
                        $('#btnNormalisasiNilaiTanpaKelasSantri').hide();
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat mengecek data',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loadingNilaiTanpaKelasSantri').hide();
                $('#btnCheckNilaiTanpaKelasSantri').prop('disabled', false);

                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengecek data: ' + error,
                    icon: 'error'
                });
            }
        });
    }

    function displayNilaiTanpaKelasSantriData(data) {
        // Simpan data global
        allNilaiTanpaKelasSantriData = data.data || [];

        // Update summary
        const summaryText = `Ditemukan ${data.total_to_delete} data nilai yang tidak memiliki referensi di tabel kelas santri.`;
        $('#summaryNilaiTanpaKelasSantriText').text(summaryText);
        $('#summaryNilaiTanpaKelasSantri').removeClass('alert-success alert-danger').addClass('alert-info');

        // Tampilkan rangkuman per TPQ
        displaySummaryByTpqNilaiTanpaKelasSantri(data.summary_by_tpq || []);

        // Render tabel
        renderNilaiTanpaKelasSantriTable();
    }

    function displaySummaryByTpqNilaiTanpaKelasSantri(summaryByTpq) {
        const container = $('#summaryTpqNilaiTanpaKelasSantriCards');
        container.empty();

        if (summaryByTpq.length === 0) {
            container.html('<div class="col-12"><p class="text-muted">Tidak ada data rangkuman</p></div>');
            return;
        }

        summaryByTpq.forEach(function(tpq) {
            const card = `
                <div class="col-md-4 mb-3">
                    <div class="card card-tpq-summary-nilai-tanpa-kelas" data-tpq-id="${tpq.IdTpq}" style="cursor: pointer; border: 2px solid #dee2e6; transition: all 0.3s; height: 100%;" onclick="filterByTpqNilaiTanpaKelasSantri('${tpq.IdTpq}')">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2 font-weight-bold" style="font-size: 1rem; line-height: 1.4;" title="${tpq.NamaTpq}">${tpq.NamaTpq}</h6>
                            <div class="text-right mb-3">
                                <span class="text-danger font-weight-bold" style="font-size: 2rem; line-height: 1;">${tpq.total || 0}</span>
                            </div>
                            <div class="pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.875rem;">Dengan Nilai:</span>
                                    <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.4em 0.7em; min-width: 45px; background-color: #ffc107; color: #212529;">${tpq.total_dengan_nilai || 0}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted" style="font-size: 0.875rem;">Tanpa Nilai:</span>
                                    <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.4em 0.7em; min-width: 45px;">${tpq.total_tanpa_nilai || 0}</span>
                                </div>
                                <div class="pt-2 border-top mt-2">
                                    ${(tpq.total_pindah_tpq || 0) > 0 ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-exchange-alt text-warning"></i> Pindah TPQ:
                                        </span>
                                        <span class="badge badge-warning" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px; background-color: #ffc107; color: #212529;">${tpq.total_pindah_tpq || 0}</span>
                                    </div>
                                    ` : ''}
                                    ${(tpq.total_tidak_aktif || 0) > 0 ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-user-slash text-secondary"></i> Tidak Aktif:
                                        </span>
                                        <span class="badge badge-secondary" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.total_tidak_aktif || 0}</span>
                                    </div>
                                    ` : ''}
                                    ${(tpq.total_tidak_terdaftar || 0) > 0 ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-user-times text-info"></i> Tidak Terdaftar:
                                        </span>
                                        <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.total_tidak_terdaftar || 0}</span>
                                    </div>
                                    ` : ''}
                                    ${(tpq.total_tidak_ditemukan || 0) > 0 ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-question-circle text-danger"></i> Tidak Ditemukan:
                                        </span>
                                        <span class="badge badge-danger" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.total_tidak_ditemukan || 0}</span>
                                    </div>
                                    ` : ''}
                                    ${(tpq.total_tidak_diketahui || 0) > 0 ? `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-exclamation-triangle text-danger"></i> Tidak Diketahui:
                                        </span>
                                        <span class="badge badge-danger" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.total_tidak_diketahui || 0}</span>
                                    </div>
                                    ` : ''}
                                </div>
                                <div class="pt-2 border-top mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-users text-info"></i> Santri Terkena:
                                        </span>
                                        <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.jumlah_santri_terkena || 0}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-calendar-alt text-secondary"></i> Tahun Ajaran:
                                        </span>
                                        <span class="badge badge-secondary" style="font-size: 0.8rem; padding: 0.35em 0.6em; min-width: 40px;">${tpq.jumlah_tahun_ajaran_terkena || 0}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function filterByTpqNilaiTanpaKelasSantri(idTpq) {
        if (selectedTpqFilterNilaiTanpaKelasSantri === idTpq) {
            // Jika klik yang sama, hapus filter
            selectedTpqFilterNilaiTanpaKelasSantri = null;
            $('.card-tpq-summary-nilai-tanpa-kelas').css({
                'border': '2px solid #dee2e6',
                'box-shadow': 'none',
                'transform': 'scale(1)'
            });
            $('#btnClearFilterTpqNilaiTanpaKelasSantri').hide();
        } else {
            // Set filter baru
            selectedTpqFilterNilaiTanpaKelasSantri = idTpq;
            $('.card-tpq-summary-nilai-tanpa-kelas').css({
                'border': '2px solid #dee2e6',
                'box-shadow': 'none',
                'transform': 'scale(1)'
            });
            $(`.card-tpq-summary-nilai-tanpa-kelas[data-tpq-id="${idTpq}"]`).css({
                'border': '2px solid #17a2b8',
                'box-shadow': '0 0 10px rgba(23, 162, 184, 0.3)',
                'transform': 'scale(1.02)'
            });
            $('#btnClearFilterTpqNilaiTanpaKelasSantri').show();
        }
        renderNilaiTanpaKelasSantriTable();
    }

    // Fungsi untuk mendapatkan badge berdasarkan jenis masalah
    function getJenisMasalahBadge(jenisMasalah, kategori) {
        let badgeClass = 'badge-danger'; // Default
        let icon = '<i class="fas fa-exclamation-triangle"></i>';

        switch (kategori) {
            case 'pindah_tpq':
                badgeClass = 'badge-warning';
                icon = '<i class="fas fa-exchange-alt"></i>';
                break;
            case 'tidak_aktif':
                badgeClass = 'badge-secondary';
                icon = '<i class="fas fa-user-slash"></i>';
                break;
            case 'tidak_terdaftar':
                badgeClass = 'badge-info';
                icon = '<i class="fas fa-user-times"></i>';
                break;
            case 'tidak_ditemukan':
                badgeClass = 'badge-danger';
                icon = '<i class="fas fa-question-circle"></i>';
                break;
            default:
                badgeClass = 'badge-danger';
                icon = '<i class="fas fa-exclamation-triangle"></i>';
        }

        return `<span class="badge ${badgeClass}">${icon} ${jenisMasalah}</span>`;
    }

    function renderNilaiTanpaKelasSantriTable() {
        // Destroy DataTable jika sudah ada
        if (dataTableNilaiTanpaKelasSantri) {
            dataTableNilaiTanpaKelasSantri.destroy();
        }

        // Clear tbody
        $('#tbodyNilaiTanpaKelasSantri').empty();

        // Filter data berdasarkan TPQ jika ada filter
        let filteredData = allNilaiTanpaKelasSantriData;
        if (selectedTpqFilterNilaiTanpaKelasSantri) {
            filteredData = allNilaiTanpaKelasSantriData.filter(item => item.IdTpq == selectedTpqFilterNilaiTanpaKelasSantri);
        }

        // Populate table
        filteredData.forEach(function(item, index) {
            const row = `
                <tr>
                    <td>
                        <input type="checkbox" 
                               name="nilaiTanpaKelasSantriCheckbox" 
                               class="nilai-tanpa-kelas-santri-checkbox" 
                               value="${item.Id}"
                               checked>
                    </td>
                    <td>${index + 1}</td>
                    <td>${item.IdSantri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaSantri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaMateri || 'Tidak ditemukan'}</td>
                    <td>${item.NamaKelas || 'Tidak ditemukan'}</td>
                    <td>${item.NamaTpq || 'Tidak ditemukan'}</td>
                    <td>${item.IdTahunAjaran}</td>
                    <td>${item.Semester}</td>
                    <td>${item.Nilai || 0}</td>
                    <td>
                        ${getJenisMasalahBadge(item.jenis_masalah || 'Tidak Ada di Kelas Santri', item.kategori || 'tidak_diketahui')}
                        <br><small class="text-muted">${item.reason || 'IdSantri tidak ada di tbl_kelas_santri untuk kombinasi IdTpq dan IdTahunAjaran ini'}</small>
                    </td>
                </tr>
            `;
            $('#tbodyNilaiTanpaKelasSantri').append(row);
        });

        // Reset checkbox state
        updateSelectedCountNilaiTanpaKelasSantri();

        // Initialize DataTable
        dataTableNilaiTanpaKelasSantri = $('#tblNilaiTanpaKelasSantri').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "columnDefs": [{
                "orderable": false,
                "targets": 0 // Kolom checkbox tidak bisa di-sort
            }]
        });

        // Event handler untuk checkbox setelah DataTable diinisialisasi
        $(document).on('change', 'input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]', function() {
            updateSelectedCountNilaiTanpaKelasSantri();
        });

        // Update checkbox state saat DataTable di-redraw
        dataTableNilaiTanpaKelasSantri.on('draw', function() {
            if (dataTableNilaiTanpaKelasSantri) {
                const filteredRows = dataTableNilaiTanpaKelasSantri.rows({
                    search: 'applied'
                }).nodes().to$();
                const filteredCheckboxes = filteredRows.find('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]');
                const checkedCount = filteredCheckboxes.filter(':checked').length;
                const totalCount = filteredCheckboxes.length;

                if (totalCount > 0) {
                    $('#checkAllNilaiTanpaKelasSantri').prop('checked', checkedCount === totalCount);
                }
                updateSelectedCountNilaiTanpaKelasSantri();
            }
        });

        $('#resultNilaiTanpaKelasSantri').show();
    }

    function updateSelectedCountNilaiTanpaKelasSantri() {
        if (dataTableNilaiTanpaKelasSantri) {
            const filteredRows = dataTableNilaiTanpaKelasSantri.rows({
                search: 'applied'
            }).nodes().to$();
            const filteredCheckboxes = filteredRows.find('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]');
            const selected = filteredCheckboxes.filter(':checked').length;
            const total = filteredCheckboxes.length;
            $('#selectedCountNilaiTanpaKelasSantri').text(`${selected} dari ${total} item dipilih`);
        } else {
            const selected = $('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]:checked').length;
            const total = $('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]').length;
            $('#selectedCountNilaiTanpaKelasSantri').text(`${selected} dari ${total} item dipilih`);
        }
    }

    // Handler untuk select all checkbox nilai tanpa kelas santri
    $('#checkAllNilaiTanpaKelasSantri').on('change', function() {
        const isChecked = $(this).prop('checked');
        selectAllFilteredNilaiTanpaKelasSantri(isChecked);
        updateSelectedCountNilaiTanpaKelasSantri();
    });

    $('#btnSelectAllNilaiTanpaKelasSantri').on('click', function() {
        selectAllFilteredNilaiTanpaKelasSantri(true);
        $('#checkAllNilaiTanpaKelasSantri').prop('checked', true);
        updateSelectedCountNilaiTanpaKelasSantri();
        $('#btnSelectAllNilaiTanpaKelasSantri').hide();
        $('#btnUnselectAllNilaiTanpaKelasSantri').show();
    });

    $('#btnUnselectAllNilaiTanpaKelasSantri').on('click', function() {
        selectAllFilteredNilaiTanpaKelasSantri(false);
        $('#checkAllNilaiTanpaKelasSantri').prop('checked', false);
        updateSelectedCountNilaiTanpaKelasSantri();
        $('#btnSelectAllNilaiTanpaKelasSantri').show();
        $('#btnUnselectAllNilaiTanpaKelasSantri').hide();
    });

    function selectAllFilteredNilaiTanpaKelasSantri(isChecked) {
        if (dataTableNilaiTanpaKelasSantri) {
            dataTableNilaiTanpaKelasSantri.rows({
                search: 'applied'
            }).nodes().to$().find('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]').prop('checked', isChecked);
        } else {
            $('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]').prop('checked', isChecked);
        }
    }

    // Handler untuk filter TPQ
    $('#btnFilterAllTpqNilaiTanpaKelasSantri').on('click', function() {
        selectedTpqFilterNilaiTanpaKelasSantri = null;
        $('.card-tpq-summary-nilai-tanpa-kelas').css('border', '2px solid #dee2e6');
        $('#btnClearFilterTpqNilaiTanpaKelasSantri').hide();
        renderNilaiTanpaKelasSantriTable();
    });

    $('#btnClearFilterTpqNilaiTanpaKelasSantri').on('click', function() {
        selectedTpqFilterNilaiTanpaKelasSantri = null;
        $('.card-tpq-summary-nilai-tanpa-kelas').css('border', '2px solid #dee2e6');
        $('#btnClearFilterTpqNilaiTanpaKelasSantri').hide();
        renderNilaiTanpaKelasSantriTable();
    });

    function normalisasiNilaiTanpaKelasSantriSelected() {
        const selectedIds = [];

        if (dataTableNilaiTanpaKelasSantri) {
            dataTableNilaiTanpaKelasSantri.rows({
                search: 'applied'
            }).every(function() {
                const row = this.node();
                const checkbox = $(row).find('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]');

                if (checkbox.is(':checked')) {
                    selectedIds.push(checkbox.val());
                }
            });
        } else {
            const selectedCheckboxes = $('input[type="checkbox"][name="nilaiTanpaKelasSantriCheckbox"]:checked');
            selectedCheckboxes.each(function() {
                selectedIds.push($(this).val());
            });
        }

        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Silakan pilih minimal satu data yang akan dinormalisasi.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Normalisasi',
            html: `Anda akan menghapus <strong>${selectedIds.length}</strong> data nilai yang tidak memiliki referensi di tabel kelas santri. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Normalisasi...',
                    html: 'Mohon tunggu, sedang menghapus data nilai...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/kelas/normalisasiNilaiTanpaKelasSantri') ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        ids: selectedIds
                    }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh data
                                checkNilaiTanpaKelasSantri();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat melakukan normalisasi: ' + error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>