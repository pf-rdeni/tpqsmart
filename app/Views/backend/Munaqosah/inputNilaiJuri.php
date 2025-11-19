<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Form Input Nilai Munaqosah
                            <span id="noPesertaHeader" class="ml-2 badge badge-info" style="display: none;"></span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" id="btnKembaliStep1" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Kembali ke Step 1
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Stepper -->
                        <div class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <div class="step" data-target="#step1">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step1" id="stepper1-trigger" aria-selected="true">
                                        <span class="bs-stepper-circle">1</span>
                                        <span class="bs-stepper-label">Input No Peserta</span>
                                    </button>
                                </div>
                                <div class="bs-stepper-line"></div>
                                <div class="step" data-target="#step2">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step2" id="stepper2-trigger" aria-selected="false" disabled="disabled">
                                        <span class="bs-stepper-circle">2</span>
                                        <span class="bs-stepper-label">Input Nilai</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <form id="formInputNilai">
                                    <!-- Step 1: Input No Peserta -->
                                    <div id="step1" class="content" role="tabpanel" aria-labelledby="stepper1-trigger">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="noPeserta">No Peserta <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="noPeserta" name="noPeserta" placeholder="Ketikkan atau scan QR No Peserta" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" id="btnScanQR">
                                                                <i class="fas fa-qrcode"></i> Scan QR
                                                            </button>
                                                            <button class="btn btn-outline-success" type="button" id="btnPesertaAntrian" style="display: none;">
                                                                <i class="fas fa-user-check"></i> <span id="btnPesertaAntrianText">Peserta Antrian</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Ketikkan nomor peserta atau gunakan tombol scan untuk membaca QR code pada kartu peserta<br>
                                                        <span class="text-info"><i class="fas fa-info-circle"></i> Auto search akan aktif setelah 3 digit, atau tekan Enter</span><br>
                                                        <span class="text-success"><i class="fas fa-bell"></i> Sistem akan mengecek antrian setiap 15 detik dan menampilkan rekomendasi No Peserta jika tersedia</span><br>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div>
                                                        <button type="button" class="btn btn-primary" id="btnCekPeserta">
                                                            <i class="fas fa-search"></i> Cek Peserta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Info Peserta -->
                                        <div id="infoPeserta" class="alert" style="display: none;">
                                            <h5 id="infoPesertaTitle"><i class="icon fas fa-info-circle"></i> Informasi Peserta</h5>
                                            <div id="pesertaInfo"></div>
                                        </div>
                                        <!-- Statistik Peserta -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header bg-gradient-info">
                                                        <h5 class="card-title mb-0 text-white">
                                                            <i class="fas fa-chart-pie"></i> Statistik Penilaian Munaqosah
                                                        </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php
                                                        $total = $total_peserta_sudah_dinilai + $total_peserta_belum_dinilai;
                                                        $pctSelesai = $total > 0 ? round(($total_peserta_sudah_dinilai / $total) * 100) : 0;
                                                        $pctBelum = $total > 0 ? round(($total_peserta_belum_dinilai / $total) * 100) : 0;
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-md-3 col-sm-6 mb-3">
                                                                <div class="info-box bg-info">
                                                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Total Peserta</span>
                                                                        <span class="info-box-number"><?= $total_peserta_terdaftar ?></span>
                                                                        <div class="progress">
                                                                            <div class="progress-bar" style="width:100%"></div>
                                                                        </div>
                                                                        <span class="progress-description">Terregistrasi</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 col-sm-6 mb-3">
                                                                <div class="info-box bg-success">
                                                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Sudah Dinilai</span>
                                                                        <span class="info-box-number"><?= $total_peserta_sudah_dinilai_juri_ini ?> / <?= $total_peserta_sudah_dinilai ?></span>
                                                                        <div class="progress">
                                                                            <div class="progress-bar" style="width: <?= $pctSelesai ?>%"></div>
                                                                        </div>
                                                                        <span class="progress-description"><?= $pctSelesai ?>% selesai</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 col-sm-6 mb-3">
                                                                <div class="info-box bg-warning">
                                                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Belum Dinilai</span>
                                                                        <span class="info-box-number"><?= $total_peserta_belum_dinilai ?></span>
                                                                        <div class="progress">
                                                                            <div class="progress-bar" style="width: <?= $pctBelum ?>%"></div>
                                                                        </div>
                                                                        <span class="progress-description"><?= $pctBelum ?>% pending</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 col-sm-6 mb-3">
                                                                <div class="info-box bg-primary">
                                                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Progress</span>
                                                                        <span class="info-box-number"><?= $pctSelesai ?>%</span>
                                                                        <div class="progress">
                                                                            <div class="progress-bar" style="width: <?= $pctSelesai ?>%"></div>
                                                                        </div>
                                                                        <span class="progress-description">Tingkat penyelesaian</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 3 Peserta Terakhir -->
                                        <?php if (!empty($peserta_terakhir)): ?>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header bg-gradient-secondary">
                                                            <h5 class="card-title mb-0 text-white">
                                                                <i class="fas fa-history"></i> 5 Peserta Terakhir yang Sudah Dinilai
                                                            </h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-bordered table-hover" id="tabelPesertaTerakhir">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No Peserta</th>
                                                                            <th>Tanggal</th>
                                                                            <th>Waktu</th>
                                                                            <th>Durasi</th>
                                                                            <th>Juri</th>
                                                                            <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($peserta_terakhir as $index => $peserta): ?>
                                                                            <tr>
                                                                                <td><strong><?= $peserta['NoPeserta'] ?></strong></td>
                                                                                <td><?= date('d/m/Y', strtotime($peserta['updated_at'])) ?></td>
                                                                                <td><?= date('H:i:s', strtotime($peserta['updated_at'])) ?></td>
                                                                                <td class="duration-cell text-center">
                                                                                    <span class="<?= $peserta['duration_class'] ?>"><?= $peserta['duration'] ?></span>
                                                                                </td>
                                                                                <td><?= $peserta['UsernameJuri'] ?></td>
                                                                                <td class="text-center">
                                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNoPeserta('<?= $peserta['NoPeserta'] ?>')">
                                                                                        <i class="fas fa-edit"></i> Ubah Nilai
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Dialog Edit Nilai -->
                                        <div class="modal fade" id="modalEditNilai" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Nilai Sudah Ada</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Nilai untuk peserta ini sudah pernah diinput. Apakah Anda ingin mengedit nilai yang sudah ada?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="button" class="btn btn-warning" id="btnEditNilai">Edit Nilai</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dialog Approval Admin -->
                                        <div class="modal fade" id="modalApprovalAdmin" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Persetujuan Admin</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="adminUsername">Username Admin</label>
                                                            <input type="text" class="form-control" id="adminUsername" name="adminUsername" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="adminPassword">Password Admin</label>
                                                            <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="button" class="btn btn-primary" id="btnConfirmEdit">Konfirmasi Edit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Input Nilai -->
                                    <div id="step2" class="content" role="tabpanel" aria-labelledby="stepper2-trigger">
                                        <div id="formNilaiContainer">
                                            <!-- Form nilai akan di-generate secara dinamis -->
                                        </div>

                                        <!-- Section untuk menampilkan Ayat (di bawah form) -->
                                        <div id="ayatSection" class="mt-4" style="display: none;">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <h5 class="card-title mb-0" id="ayatTitle">Lihat Ayat</h5>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openInNewTab()">
                                                                <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideAyatSection()">
                                                                <i class="fas fa-times"></i> Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div id="iframeContainer">
                                                        <iframe id="iframeAyat" src="" style="width: 100%; height: 500px; border: none;"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section untuk menampilkan Ayat dari API (di bawah form) -->
                                        <div id="ayatApiSection" class="mt-4" style="display: none;">
                                            <div class="card">
                                                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <h5 class="card-title mb-0 text-white" id="ayatApiTitle">Lihat Ayat Al-Qur'an</h5>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-light" id="btnZoomOutApi" title="Zoom Out">
                                                                <i class="fas fa-search-minus"></i> Zoom Out
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-light" id="btnZoomInApi" title="Zoom In">
                                                                <i class="fas fa-search-plus"></i> Zoom In
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-light" id="btnResetZoomApi" title="Reset Zoom">
                                                                <i class="fas fa-undo"></i> Reset
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-light" onclick="hideAyatApiSection()">
                                                                <i class="fas fa-times"></i> Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div id="ayatApiContent">
                                                        <!-- Content akan diisi oleh JavaScript -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Button untuk mengirim nilai -->
                                        <div id="btnKirimNilaiContainer" class="text-center mt-4" style="display: none;">
                                            <button type="button" class="btn btn-success btn-lg" id="btnKirimNilai">
                                                <i class="fas fa-paper-plane"></i> Kirim Nilai
                                            </button>
                                            <p class="text-muted mt-2">
                                                <i class="fas fa-info-circle"></i> Pastikan semua nilai sudah diisi dengan benar sebelum mengirim
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- QR Scanner Modal -->
<div class="modal fade" id="modalQRScanner" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 100%"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>


<style>
    /* Styling untuk button kirim nilai */
    #btnKirimNilai {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    #btnKirimNilai:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* Styling untuk input yang invalid */
    .nilai-input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Animasi untuk button container */
    #btnKirimNilaiContainer {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Styling untuk info text */
    #btnKirimNilaiContainer p {
        font-size: 0.9em;
        margin-top: 8px;
    }

    /* Styling untuk section ayat */
    #ayatSection {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    #ayatSection .card {
        border: none;
        box-shadow: none;
    }

    #ayatSection .card-header {
        background-color: #e9ecef;
        border-bottom: 1px solid #dee2e6;
    }

    #iframeAyat {
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.3s ease;
        transform-origin: top left;
        overflow: hidden;
    }

    /* Container untuk iframe dengan pan capability */
    #iframeContainer {
        overflow: hidden;
        position: relative;
        width: 100%;
        height: 500px;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Zoom levels */
    /* Styling untuk button lihat ayat */
    .btn-outline-info {
        border-color: #17a2b8;
        color: #17a2b8;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Styling untuk button peserta antrian */
    #btnPesertaAntrian {
        border-color: #28a745;
        color: white;
        background-color: #90EE90;
        min-width: 180px;
        padding: 8px 20px;
        font-weight: bold;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        animation: blinkingBackground 1.5s infinite;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    #btnPesertaAntrian:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
        animation: none;
    }

    @keyframes blinkingBackground {
        0% {
            background-color: #90EE90;
            color: #155724;
        }

        50% {
            background-color: #ffffff;
            color: #28a745;
        }

        100% {
            background-color: #90EE90;
            color: #155724;
        }
    }

    /* Styling untuk kategori kesalahan */
    .error-categories-container {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    .error-categories-container .form-check {
        margin-bottom: 0.5rem;
    }

    .error-categories-container .form-check:last-child {
        margin-bottom: 0;
    }

    .error-categories-container .form-check-input {
        margin-top: 0.25rem;
    }

    .error-categories-container .form-check-label {
        font-size: 0.9rem;
        color: #495057;
        cursor: pointer;
    }

    .error-categories-container .form-check-input:checked+.form-check-label {
        color: #dc3545;
        font-weight: 500;
    }

    /* Styling untuk layout 2 kolom */
    .row.mb-4 {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem !important;
    }

    .row.mb-4:last-child {
        border-bottom: none;
        margin-bottom: 0 !important;
    }

    /* Simplify: use AdminLTE default info-box styles (remove custom gradients/overrides) */

    /* Gradient headers */
    .bg-gradient-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    /* Styling untuk kolom durasi */
    .duration-cell {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }

    .duration-fast {
        color: #28a745;
        background-color: rgba(40, 167, 69, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .duration-medium {
        color: #ffc107;
        background-color: rgba(255, 193, 7, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .duration-slow {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .duration-none {
        color: #6c757d;
        font-style: italic;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize stepper
        var stepper = new Stepper(document.querySelector('.bs-stepper'));

        // Global variables
        let currentPesertaData = null;
        let currentJuriData = null;
        let currentMateriData = null;
        let errorCategoriesByKategori = {};
        let isEditMode = false;

        const ERROR_AUTO_SHOW_THRESHOLD = 67;

        // Get current juri data from controller
        function getCurrentJuriData() {
            return {
                IdJuri: '<?= $juri_data->IdJuri ?? "" ?>',
                UsernameJuri: '<?= $juri_data->UsernameJuri ?? "" ?>',
                IdGrupMateriUjian: '<?= $juri_data->IdGrupMateriUjian ?? "" ?>',
                RoomId: '<?= $juri_data->RoomId ?? "" ?>'
            };
        }

        // Get current tahun ajaran
        function getCurrentTahunAjaran() {
            const tahunAjaran = '<?= $current_tahun_ajaran ?? "" ?>';
            if (tahunAjaran) {
                return tahunAjaran;
            }

            // Fallback: ambil dari server jika tidak tersedia
            console.warn('IdTahunAjaran tidak tersedia di view, akan mengambil dari server...');
            return null; // Akan dihandle oleh validasi
        }

        // Function to fetch tahun ajaran from server
        function fetchTahunAjaranFromServer() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= base_url("backend/munaqosah/get-current-tahun-ajaran") ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            resolve(response.data.IdTahunAjaran);
                        } else {
                            reject(new Error(response.message || 'Gagal mengambil tahun ajaran'));
                        }
                    },
                    error: function(xhr, status, error) {
                        reject(new Error('Error koneksi: ' + error));
                    }
                });
            });
        }

        // Get type ujian based on username
        function getTypeUjian() {
            return '<?= $juri_data->TypeUjian ?? "" ?>';
        }

        // Step 1: Cek Peserta
        $('#btnCekPeserta').click(async function() {
            // Clear any auto search timeouts when manually clicking
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $('#noPeserta').removeClass('border-info');
            $('#noPeserta').attr('placeholder', 'Masukkan atau scan QR No Peserta');

            const noPeserta = $('#noPeserta').val().trim();
            let tahunAjaran = getCurrentTahunAjaran();

            if (!noPeserta) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan masukkan nomor peserta terlebih dahulu'
                });
                return;
            }

            // Jika tahun ajaran tidak tersedia, coba ambil dari server
            if (!tahunAjaran) {
                try {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mengambil tahun ajaran dari server',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    tahunAjaran = await fetchTahunAjaranFromServer();
                    Swal.close();
                } catch (error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil tahun ajaran: ' + error.message
                    });
                    return;
                }
            }

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mengecek data peserta',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX call to check peserta
            $.ajax({
                url: '<?= base_url("backend/munaqosah/cekPeserta") ?>',
                type: 'POST',
                data: {
                    noPeserta: noPeserta,
                    IdJuri: getCurrentJuriData().IdJuri || 'J001', // Default fallback
                    IdTahunAjaran: tahunAjaran,
                    TypeUjian: getTypeUjian()
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        currentPesertaData = response.data.peserta;
                        currentJuriData = response.data.juri;
                        currentMateriData = response.data.materi;
                        errorCategoriesByKategori = (response.data.error_categories && typeof response.data.error_categories === 'object') ? response.data.error_categories : {};

                        // Show peserta info with room validation info dan status nilai
                        showPesertaInfo(response.data.peserta, response.data.roomValidation, response.data.nilaiExists || false);

                        // Check if nilai already exists
                        if (response.data.nilaiExists) {
                            $('#modalEditNilai').modal('show');
                        } else {
                            // Proceed to step 2
                            proceedToStep2();
                        }
                    } else {
                        // Handle different error types with detailed messages
                        let errorTitle = 'Error';
                        let errorMessage = response.message || 'Terjadi kesalahan saat mengecek peserta';
                        let errorDetails = response.details || '';

                        // Customize error message based on status
                        switch (response.status) {
                            case 'VALIDATION_ERROR':
                                errorTitle = 'Validasi Error';
                                break;
                            case 'DATA_NOT_FOUND':
                                errorTitle = 'Data Tidak Ditemukan';
                                // Tampilkan info peserta dengan background putih jika peserta tidak ditemukan
                                if (response.code === 'PESERTA_NOT_FOUND' || response.code === 'REGISTRASI_NOT_FOUND') {
                                    showPesertaNotFoundInfo(noPeserta, errorMessage);
                                }
                                break;
                            case 'AUTHENTICATION_ERROR':
                                errorTitle = 'Error Autentikasi';
                                break;
                            case 'AUTHORIZATION_ERROR':
                                errorTitle = 'Error Otorisasi';
                                break;
                            case 'SYSTEM_ERROR':
                                errorTitle = 'Error Sistem';
                                break;
                            default:
                                errorTitle = 'Error';
                        }

                        // Show detailed error message (kecuali untuk peserta tidak ditemukan yang sudah ditampilkan di info)
                        if (!(response.status === 'DATA_NOT_FOUND' && (response.code === 'PESERTA_NOT_FOUND' || response.code === 'REGISTRASI_NOT_FOUND'))) {
                            Swal.fire({
                                icon: 'error',
                                title: errorTitle,
                                html: `
                                <div style="text-align: left;">
                                    <p><strong>${errorMessage}</strong></p>
                                    ${errorDetails ? `<p><small>Detail: ${errorDetails}</small></p>` : ''}
                                    ${response.code ? `<p><small>Kode Error: ${response.code}</small></p>` : ''}
                                </div>
                            `
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan koneksi';
                    let errorResponse = null;

                    // Try to parse error response
                    try {
                        errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }

                    // Jika error karena peserta tidak ditemukan, tampilkan di info peserta
                    if (errorResponse && errorResponse.status === 'DATA_NOT_FOUND' &&
                        (errorResponse.code === 'PESERTA_NOT_FOUND' || errorResponse.code === 'REGISTRASI_NOT_FOUND')) {
                        const noPeserta = $('#noPeserta').val().trim();
                        if (noPeserta) {
                            showPesertaNotFoundInfo(noPeserta, errorMessage);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Koneksi',
                            text: errorMessage + ' (' + error + ')'
                        });
                    }
                }
            });
        });

        // Show peserta info
        function showPesertaInfo(peserta, roomValidation = null, nilaiExists = false) {
            let roomInfoHtml = '';

            // Set background color berdasarkan status nilai
            if (nilaiExists) {
                // Sudah dinilai - background merah
                $('#infoPeserta').removeClass('alert-success alert-warning alert-secondary').addClass('alert-danger');
                $('#infoPesertaTitle').html('<i class="icon fas fa-check-circle"></i> Informasi Peserta - <strong>Sudah Dinilai</strong>');
            } else {
                // Belum dinilai - background hijau
                $('#infoPeserta').removeClass('alert-danger alert-warning alert-secondary').addClass('alert-success');
                $('#infoPesertaTitle').html('<i class="icon fas fa-info-circle"></i> Informasi Peserta - <strong>Belum Dinilai</strong>');
            }

            // Tampilkan info room jika ada
            if (currentJuriData.RoomId) {
                roomInfoHtml = `<strong>Room:</strong> <span class="badge badge-info">${currentJuriData.RoomId}</span><br>`;
            }

            // Tampilkan info room validation jika ada
            let roomValidationHtml = '';
            if (roomValidation && roomValidation.enabled) {
                const juriCount = roomValidation.juriCount || 0;
                const maxJuri = roomValidation.maxJuri || 2;
                const remaining = maxJuri - juriCount;

                let statusBadge = '';
                if (juriCount === 0) {
                    statusBadge = '<span class="badge badge-success">Belum Ada Juri</span>';
                } else if (juriCount < maxJuri) {
                    statusBadge = `<span class="badge badge-warning">Sudah ${juriCount} dari ${maxJuri} Juri</span>`;
                } else {
                    statusBadge = `<span class="badge badge-danger">Penuh (${maxJuri} Juri)</span>`;
                }

                roomValidationHtml = `
                    <div class="col-md-12 mt-2">
                        <div class="alert alert-info mb-0">
                            <h6><i class="fas fa-info-circle"></i> Informasi Room Validation:</h6>
                            <small>
                                Status: ${statusBadge}<br>
                                ${roomValidation.message || ''}
                                ${juriCount > 0 ? `<br><strong>Juri yang sudah menilai:</strong>` : ''}
                            </small>
                            ${juriCount > 0 && roomValidation.juriList ? 
                                '<ul class="mb-0 mt-1">' + 
                                roomValidation.juriList.map(j => 
                                    `<li><small>${j.UsernameJuri} (Nilai: ${j.Nilai}) - ${new Date(j.updated_at).toLocaleString('id-ID')}</small></li>`
                                ).join('') + 
                                '</ul>' 
                            : ''}
                        </div>
                    </div>
                `;
            }

            const infoHtml = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Nama Santri:</strong> ${peserta.NamaSantri}<br>
                    <strong>No Peserta:</strong> ${peserta.NoPeserta}
                </div>
                <div class="col-md-6">
                    <strong>Grup Materi:</strong> ${currentJuriData.NamaMateriGrup}<br>
                    ${roomInfoHtml}
                    <strong>Juri:</strong> ${currentJuriData.UsernameJuri}
                </div>
                ${roomValidationHtml}
            </div>
        `;

            $('#pesertaInfo').html(infoHtml);
            $('#infoPeserta').show();
        }

        // Show peserta not found info dengan background putih
        function showPesertaNotFoundInfo(noPeserta, errorMessage) {
            // Set background putih (alert-light untuk background putih bersih)
            $('#infoPeserta').removeClass('alert-success alert-danger alert-warning alert-secondary').addClass('alert-light');
            $('#infoPesertaTitle').html('<i class="icon fas fa-exclamation-triangle"></i> Informasi Peserta - <strong>Tidak Ditemukan</strong>');

            const infoHtml = `
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-0">
                        <strong>No Peserta:</strong> <span class="badge badge-secondary">${noPeserta}</span><br>
                        <strong>Status:</strong> <span class="badge badge-danger">Tidak Ditemukan</span><br>
                        <strong>Pesan:</strong> ${errorMessage || 'Data peserta tidak ditemukan di database'}
                    </div>
                </div>
            </div>
        `;

            $('#pesertaInfo').html(infoHtml);
            $('#infoPeserta').show();
        }

        // Proceed to step 2
        function proceedToStep2() {
            // Update header dengan No Peserta
            if (currentPesertaData && currentPesertaData.NoPeserta) {
                $('#noPesertaHeader').text('No Peserta: ' + currentPesertaData.NoPeserta).show();
            }

            // Tampilkan tombol kembali ke step 1
            $('#btnKembaliStep1').show();

            // Stop check antrian saat pindah ke step 2
            stopCheckAntrian();

            generateNilaiForm();
            stepper.next();
        }

        // Fungsi untuk kembali ke step 1
        function kembaliKeStep1() {
            // Konfirmasi sebelum kembali
            Swal.fire({
                title: 'Kembali ke Step 1?',
                text: 'Data nilai yang sudah diinput akan hilang. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kembali',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Sembunyikan tombol kembali
                    $('#btnKembaliStep1').hide();
                    // Sembunyikan No Peserta di header
                    $('#noPesertaHeader').hide();
                    // Sembunyikan container form nilai dan tombol kirim
                    $('#formNilaiContainer').html('');
                    $('#btnKirimNilaiContainer').hide();
                    // Sembunyikan section ayat jika terbuka
                    $('#ayatSection').hide();

                    // Reset tampilan ayat API
                    resetAyatApiView();

                    // Kembali ke step 1
                    stepper.to(1);

                    // Mulai check antrian lagi saat kembali ke step 1
                    startCheckAntrian();

                    // Cek ulang nilai existing untuk update status background
                    if (currentPesertaData && currentPesertaData.NoPeserta) {
                        cekNilaiExisting(currentPesertaData.NoPeserta);
                    }
                }
            });
        }

        // Fungsi untuk cek nilai existing setelah kembali ke step 1
        function cekNilaiExisting(noPeserta) {
            let tahunAjaran = getCurrentTahunAjaran();

            // Jika tahun ajaran tidak tersedia, coba ambil dari server
            if (!tahunAjaran) {
                fetchTahunAjaranFromServer().then(function(tahun) {
                    tahunAjaran = tahun;
                    performCekNilai(noPeserta, tahunAjaran);
                }).catch(function(error) {
                    console.error('Error fetching tahun ajaran:', error);
                });
            } else {
                performCekNilai(noPeserta, tahunAjaran);
            }
        }

        // Fungsi untuk melakukan cek nilai
        function performCekNilai(noPeserta, tahunAjaran) {
            $.ajax({
                url: '<?= base_url("backend/munaqosah/cekPeserta") ?>',
                type: 'POST',
                data: {
                    noPeserta: noPeserta,
                    IdJuri: getCurrentJuriData().IdJuri || 'J001',
                    IdTahunAjaran: tahunAjaran,
                    TypeUjian: getTypeUjian()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update info peserta dengan status nilai yang baru
                        showPesertaInfo(response.data.peserta, response.data.roomValidation, response.data.nilaiExists || false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking nilai existing:', error);
                }
            });
        }

        // Generate dynamic form for input nilai
        function generateNilaiForm() {
            if (!currentMateriData || currentMateriData.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak ada materi yang tersedia untuk grup ini'
                });
                return;
            }

            // Show loading while fetching error categories
            Swal.fire({
                title: 'Memproses...',
                text: 'Menyiapkan form input nilai',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formHtml = '<div class="row">';

            // Group materi by kategori
            const groupedMateri = {};
            currentMateriData.forEach(materi => {
                if (!groupedMateri[materi.KategoriMateriUjian]) {
                    groupedMateri[materi.KategoriMateriUjian] = [];
                }
                groupedMateri[materi.KategoriMateriUjian].push(materi);
            });

            // Generate form for each kategori
            for (const kategori of Object.keys(groupedMateri)) {
                // Ambil kategori kesalahan dari data yang sudah disiapkan controller
                const errorCategories = getErrorCategoriesForKategori(kategori);

                formHtml += `
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">${kategori}</h5>
                        </div>
                        <div class="card-body">
            `;

                groupedMateri[kategori].forEach(materi => {
                    // Cek apakah materi memiliki WebLinkAyat atau IdSurah/IdAyat
                    const hasWebLink = materi.WebLinkAyat && String(materi.WebLinkAyat).trim() !== '';
                    const idSurah = materi.IdSurah ? parseInt(materi.IdSurah) : null;
                    const idAyat = materi.IdAyat ? parseInt(materi.IdAyat) : null;
                    const hasApiData = idSurah && idAyat && idSurah > 0 && idAyat > 0;
                    const showWebButton = hasWebLink && materi.WebLinkAyat;
                    const showApiButton = hasApiData && idSurah && idAyat;

                    // Escape untuk keamanan
                    const escapedUrl = materi.WebLinkAyat ? $('<div>').text(materi.WebLinkAyat).html() : '';
                    const escapedTitle = $('<div>').text(materi.NamaMateri || 'Lihat Ayat').html();
                    const escapedMateriId = $('<div>').text(materi.IdMateri || '').html();
                    const escapedIdSurah = idSurah ? $('<div>').text(idSurah).html() : '';
                    const escapedIdAyat = idAyat ? $('<div>').text(idAyat).html() : '';

                    formHtml += `
                     <div class="row mb-4">
                         <div class="col-md-6">
                             <div class="form-group">
                                 <label for="nilai_${materi.IdMateri}">${materi.NamaMateri}</label>
                                <input type="number" 
                                       class="form-control nilai-input" 
                                       id="nilai_${materi.IdMateri}" 
                                       name="nilai[${materi.IdMateri}]"
                                       data-materi-id="${materi.IdMateri}"
                                        min="<?= $nilai_minimal ?>" 
                                        max="<?= $nilai_maximal ?>" 
                                        step="1"
                                        oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);"
                                        required>
                                 <small class="form-text text-muted">Range nilai: <?= $nilai_minimal ?> - <?= $nilai_maximal ?></small>
                                 ${(showWebButton || showApiButton) ? `
                                 <div class="mt-2">
                                     <div class="btn-group btn-group-sm" role="group">
                                         ${showWebButton ? `
                                         <button type="button" class="btn btn-outline-info btn-lihat-ayat-card" 
                                                 data-url="${escapedUrl}" 
                                                 data-title="${escapedTitle}"
                                                 data-materi-id="${escapedMateriId}">
                                             <i class="fas fa-external-link-alt"></i> Lihat Ayat (Web)
                                         </button>
                                         ` : ''}
                                         ${showApiButton ? `
                                         <button type="button" class="btn btn-outline-primary btn-lihat-ayat-api" 
                                                 data-materi-id="${escapedMateriId}"
                                                 data-id-surah="${escapedIdSurah}"
                                                 data-id-ayat="${escapedIdAyat}"
                                                 data-title="${escapedTitle}">
                                             <i class="fas fa-book-quran"></i> Lihat Ayat (API)
                                         </button>
                                         ` : ''}
                                     </div>
                                 </div>
                                 ` : ''}
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="mb-0">Kategori Kesalahan (Opsional)</label>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary toggle-error-categories" 
                                            data-materi-id="${materi.IdMateri}"
                                            aria-label="Tampilkan atau sembunyikan kategori kesalahan">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="error-categories-container d-none" data-materi-id="${materi.IdMateri}" data-visible="false" data-manual-hidden="false">
                                     ${errorCategories.length > 0 ? errorCategories.map((category, index) => `
                                         <div class="form-check">
                                             <input class="form-check-input error-checkbox" 
                                                    type="checkbox" 
                                                    value="${category}" 
                                                    id="error_${materi.IdMateri}_${index}"
                                                    name="error[${materi.IdMateri}][]">
                                             <label class="form-check-label" for="error_${materi.IdMateri}_${index}">
                                                 ${category}
                                             </label>
                                         </div>
                                     `).join('') : '<p class="text-muted">Tidak ada kategori kesalahan tersedia</p>'}
                                 </div>
                                <small class="form-text text-muted">Tampilkan kategori kesalahan (klik ikon mata) atau otomatis jika nilai < 67.</small>
                             </div>
                         </div>
                     </div>
                 `;
                });

                formHtml += `
                        </div>
                    </div>
                </div>
            `;
            }

            formHtml += '</div>';

            // Close loading
            Swal.close();

            $('#formNilaiContainer').html(formHtml);

            // Setup event listeners for nilai inputs
            setupNilaiInputListeners();
            setupErrorCategoryToggles();
            $('.nilai-input').each(function() {
                handleAutoShowErrorCategories($(this));
            });

            // Setup event listeners for "Lihat Ayat" buttons dari card
            $(document).off('click', '.btn-lihat-ayat-card').on('click', '.btn-lihat-ayat-card', function() {
                const url = $(this).data('url');
                const title = $(this).data('title');
                const materiId = $(this).data('materi-id');
                if (url) {
                    showAyatModal(url, title || 'Lihat Ayat', materiId);
                }
            });

            // Setup event listeners for "Lihat Ayat (API)" buttons dari card
            $(document).off('click', '.btn-lihat-ayat-api').on('click', '.btn-lihat-ayat-api', function() {
                const materiId = $(this).data('materi-id');
                const idSurah = $(this).data('id-surah');
                const idAyat = $(this).data('id-ayat');
                const title = $(this).data('title');
                if (materiId) {
                    showAyatApiModal(materiId, idSurah, idAyat, title || 'Lihat Ayat');
                }
            });
        }

        // Function to get error categories for a kategori from preloaded data
        function getErrorCategoriesForKategori(kategori) {
            if (!kategori) {
                return [];
            }

            const categories = errorCategoriesByKategori[kategori];
            return Array.isArray(categories) ? categories : [];
        }

        // Setup event listeners for nilai inputs
        function setupNilaiInputListeners() {
            $('.nilai-input').on('input', function() {
                validateAndShowKirimButton();
                handleAutoShowErrorCategories($(this));
            });

            $('.nilai-input').on('blur', function() {
                validateAndShowKirimButton();
                handleAutoShowErrorCategories($(this));
            });
        }

        function setupErrorCategoryToggles() {
            $('.toggle-error-categories').off('click').on('click', function() {
                const materiId = $(this).data('materi-id');
                toggleErrorCategories(materiId, true);
            });
        }

        function handleAutoShowErrorCategories($input) {
            const materiId = $input.data('materi-id');
            if (!materiId) {
                return;
            }

            const container = getErrorCategoryContainer(materiId);
            if (!container.length) {
                return;
            }

            const rawValue = ($input.val() || '').toString().trim();
            const numericValue = parseFloat(rawValue);
            const hasTwoDigits = rawValue.length >= 2;
            const isValidNumber = !Number.isNaN(numericValue);
            const isLowScore = hasTwoDigits && isValidNumber && numericValue < ERROR_AUTO_SHOW_THRESHOLD;
            const shouldHide = hasTwoDigits && isValidNumber && numericValue >= ERROR_AUTO_SHOW_THRESHOLD;

            if (isLowScore) {
                showErrorCategories(materiId, false);
                container.attr('data-manual-hidden', 'false');
            } else if (shouldHide || rawValue === '') {
                hideErrorCategories(materiId, false);
                container.attr('data-manual-hidden', 'false');
            }
        }

        function toggleErrorCategories(materiId, triggeredByManual = false) {
            const container = getErrorCategoryContainer(materiId);
            if (!container.length) {
                return;
            }

            const isVisible = container.attr('data-visible') === 'true';
            if (isVisible) {
                hideErrorCategories(materiId, triggeredByManual);
            } else {
                showErrorCategories(materiId, triggeredByManual);
            }
        }

        function showErrorCategories(materiId, triggeredByManual = false) {
            const container = getErrorCategoryContainer(materiId);
            if (!container.length) {
                return;
            }

            container.removeClass('d-none').attr('data-visible', 'true');
            container.attr('data-manual-hidden', 'false');
            updateErrorToggleState(materiId, true);
        }

        function hideErrorCategories(materiId, triggeredByManual = false) {
            const container = getErrorCategoryContainer(materiId);
            if (!container.length) {
                return;
            }

            container.addClass('d-none').attr('data-visible', 'false');
            container.attr('data-manual-hidden', triggeredByManual ? 'true' : 'false');
            updateErrorToggleState(materiId, false);
        }

        function updateErrorToggleState(materiId, isVisible) {
            const toggleBtn = $(`.toggle-error-categories[data-materi-id="${materiId}"]`);
            const icon = toggleBtn.find('i');

            if (isVisible) {
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
                toggleBtn.attr('aria-pressed', 'true');
            } else {
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
                toggleBtn.attr('aria-pressed', 'false');
            }
        }

        function getErrorCategoryContainer(materiId) {
            return $(`.error-categories-container[data-materi-id="${materiId}"]`);
        }

        // Get nilai range from PHP
        const nilaiMinimal = <?= $nilai_minimal ?>;
        const nilaiMaximal = <?= $nilai_maximal ?>;

        // Validate all inputs and show/hide kirim button
        function validateAndShowKirimButton() {
            const nilaiInputs = $('.nilai-input');
            let allValid = true;
            let hasValue = false;

            nilaiInputs.each(function() {
                const value = parseFloat($(this).val());
                if ($(this).val().trim() !== '') {
                    hasValue = true;
                    if (isNaN(value) || value < nilaiMinimal || value > nilaiMaximal) {
                        allValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Show kirim button if all inputs are valid and at least one has value
            if (allValid && hasValue) {
                $('#btnKirimNilaiContainer').show();
            } else {
                $('#btnKirimNilaiContainer').hide();
            }
        }



        // Kirim nilai (button baru di step 2)
        $('#btnKirimNilai').click(async function() {
            // Validasi semua input terlebih dahulu
            const nilaiInputs = $('.nilai-input');
            let isValid = true;
            let errorMessage = '';

            nilaiInputs.each(function() {
                const value = parseFloat($(this).val());
                if ($(this).val().trim() !== '') {
                    if (isNaN(value) || value < nilaiMinimal || value > nilaiMaximal) {
                        isValid = false;
                        errorMessage = `Semua nilai harus dalam range ${nilaiMinimal}-${nilaiMaximal}`;
                        return false;
                    }
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: errorMessage
                });
                return;
            }

            // Panggil fungsi simpan nilai
            await simpanNilai();
        });


        // Fungsi simpan nilai yang bisa dipanggil dari button manapun
        async function simpanNilai() {
            let tahunAjaran = getCurrentTahunAjaran();

            // Jika tahun ajaran tidak tersedia, coba ambil dari server
            if (!tahunAjaran) {
                try {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mengambil tahun ajaran dari server',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    tahunAjaran = await fetchTahunAjaranFromServer();
                    Swal.close();
                } catch (error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil tahun ajaran: ' + error.message
                    });
                    return;
                }
            }

            // Validate all inputs
            const nilaiInputs = $('.nilai-input');
            let isValid = true;
            let errorMessage = '';

            nilaiInputs.each(function() {
                const value = parseFloat($(this).val());
                if ($(this).val().trim() !== '') {
                    if (isNaN(value) || value < nilaiMinimal || value > nilaiMaximal) {
                        isValid = false;
                        errorMessage = `Semua nilai harus dalam range ${nilaiMinimal}-${nilaiMaximal}`;
                        return false;
                    }
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: errorMessage
                });
                return;
            }

            // Collect form data
            const formData = {
                NoPeserta: currentPesertaData.NoPeserta,
                IdSantri: currentPesertaData.IdSantri,
                IdTpq: currentPesertaData.IdTpq,
                IdTahunAjaran: tahunAjaran,
                IdJuri: getCurrentJuriData().IdJuri || 'J001',
                TypeUjian: getTypeUjian(),
                isEditMode: isEditMode,
                nilai: {},
                catatan: {}
            };

            nilaiInputs.each(function() {
                const materiId = $(this).attr('name').replace('nilai[', '').replace(']', '');
                formData.nilai[materiId] = parseFloat($(this).val());
            });

            // Collect error categories data
            $('.error-categories-container').each(function() {
                const materiId = $(this).data('materi-id');
                const selectedErrors = [];

                $(this).find('.error-checkbox:checked').each(function() {
                    selectedErrors.push($(this).val());
                });

                // Format data sebagai string dengan format 1-3-4-8-10
                if (selectedErrors.length > 0) {
                    formData.catatan[materiId] = selectedErrors.join('-');
                }
            });

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyimpan data nilai',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX call to save nilai
            $.ajax({
                url: '<?= base_url("backend/munaqosah/simpanNilaiJuri") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Nilai berhasil disimpan',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect ke halaman input-nilai-juri untuk refresh data
                            window.location.href = '<?= base_url("backend/munaqosah/input-nilai-juri") ?>';
                        });
                    } else {
                        // Handle different error types with detailed messages
                        let errorTitle = 'Error';
                        let errorMessage = response.message || 'Terjadi kesalahan saat menyimpan nilai';
                        let errorDetails = response.details || '';

                        // Customize error message based on status
                        switch (response.status) {
                            case 'VALIDATION_ERROR':
                                errorTitle = 'Validasi Error';
                                break;
                            case 'DATA_NOT_FOUND':
                                errorTitle = 'Data Tidak Ditemukan';
                                break;
                            case 'DATABASE_ERROR':
                                errorTitle = 'Error Database';
                                break;
                            case 'AUTHENTICATION_ERROR':
                                errorTitle = 'Error Autentikasi';
                                break;
                            case 'AUTHORIZATION_ERROR':
                                errorTitle = 'Error Otorisasi';
                                break;
                            case 'SYSTEM_ERROR':
                                errorTitle = 'Error Sistem';
                                break;
                            default:
                                errorTitle = 'Error';
                        }

                        // Show detailed error message
                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: `
                             <div style="text-align: left;">
                                 <p><strong>${errorMessage}</strong></p>
                                 ${errorDetails ? `<p><small>Detail: ${errorDetails}</small></p>` : ''}
                                 ${response.code ? `<p><small>Kode Error: ${response.code}</small></p>` : ''}
                             </div>
                         `
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan koneksi';

                    // Try to parse error response
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Koneksi',
                        text: errorMessage + ' (' + error + ')'
                    });
                }
            });
        }

        // Edit nilai handlers
        $('#btnEditNilai').click(function() {
            $('#modalEditNilai').modal('hide');
            $('#modalApprovalAdmin').modal('show');
        });

        $('#btnConfirmEdit').click(function() {
            const adminUsername = $('#adminUsername').val();
            const adminPassword = $('#adminPassword').val();

            if (!adminUsername || !adminPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Username dan password admin harus diisi'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memverifikasi...',
                text: 'Memverifikasi kredensial admin',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX call to verify admin credentials
            $.ajax({
                url: '<?= base_url("backend/munaqosah/verifyAdminCredentials") ?>',
                type: 'POST',
                data: {
                    username: adminUsername,
                    password: adminPassword
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        isEditMode = true;
                        $('#modalApprovalAdmin').modal('hide');
                        proceedToStep2();
                    } else {
                        // Handle different error types with detailed messages
                        let errorTitle = 'Error';
                        let errorMessage = response.message || 'Kredensial admin tidak valid';
                        let errorDetails = response.details || '';

                        // Customize error message based on status
                        switch (response.status) {
                            case 'VALIDATION_ERROR':
                                errorTitle = 'Validasi Error';
                                break;
                            case 'AUTHENTICATION_ERROR':
                                errorTitle = 'Error Autentikasi';
                                break;
                            case 'AUTHORIZATION_ERROR':
                                errorTitle = 'Error Otorisasi';
                                break;
                            case 'SYSTEM_ERROR':
                                errorTitle = 'Error Sistem';
                                break;
                            default:
                                errorTitle = 'Error';
                        }

                        // Show detailed error message
                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: `
                            <div style="text-align: left;">
                                <p><strong>${errorMessage}</strong></p>
                                ${errorDetails ? `<p><small>Detail: ${errorDetails}</small></p>` : ''}
                                ${response.code ? `<p><small>Kode Error: ${response.code}</small></p>` : ''}
                            </div>
                        `
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan koneksi';

                    // Try to parse error response
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Koneksi',
                        text: errorMessage + ' (' + error + ')'
                    });
                }
            });
        });

        // Reset form
        function resetForm() {
            $('#noPeserta').val('');
            $('#infoPeserta').hide();
            $('#btnKirimNilaiContainer').hide();
            $('#ayatSection').hide();
            $('#noPesertaHeader').hide(); // Sembunyikan No Peserta di header
            $('#btnKembaliStep1').hide(); // Sembunyikan tombol kembali
            currentPesertaData = null;
            currentJuriData = null;
            currentMateriData = null;
            errorCategoriesByKategori = {};
            isEditMode = false;

            // Mulai check antrian lagi saat reset form
            startCheckAntrian();

            stepper.to(1);
        }

        // QR Scanner
        $('#btnScanQR').click(function() {
            $('#modalQRScanner').modal('show');

            // Initialize QR scanner
            if (typeof Html5QrcodeScanner !== 'undefined') {
                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    }
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            $('#noPeserta').val(decodedText);
            $('#modalQRScanner').modal('hide');

            // Auto check peserta after QR scan
            $('#btnCekPeserta').click();
        }

        function onScanFailure(error) {
            // Handle scan failure
            console.log('QR Scan failed:', error);
        }

        // Auto search when typing 3+ digits
        $('#noPeserta').on('input', function() {
            const noPeserta = $(this).val().trim();

            // Clear any existing timeout and countdown
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }

            // Show auto search indicator
            if (noPeserta.length >= 3) {
                // Add visual indicator
                $(this).addClass('border-info');

                // Show countdown indicator in placeholder
                const originalPlaceholder = $(this).attr('placeholder');
                let countdown = 1;

                const updatePlaceholder = () => {
                    $(this).attr('placeholder', `Auto search dalam ${countdown} detik...`);
                };

                updatePlaceholder();

                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        $(this).attr('placeholder', originalPlaceholder);
                        $(this).removeClass('border-info');
                        $('#btnCekPeserta').click();
                    } else {
                        updatePlaceholder();
                    }
                }, 1000);

                // Store interval ID for cleanup
                window.autoSearchCountdown = countdownInterval;

                window.autoSearchTimeout = setTimeout(function() {
                    clearInterval(window.autoSearchCountdown);
                    $(this).attr('placeholder', originalPlaceholder);
                    $(this).removeClass('border-info');
                    $('#btnCekPeserta').click();
                }, 1000); // 1 second delay after user stops typing
            } else {
                // Remove visual indicator if less than 3 digits
                $(this).removeClass('border-info');
                $(this).attr('placeholder', 'Masukkan atau scan QR No Peserta');
            }
        });

        // Enter key handler
        $('#noPeserta').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#btnCekPeserta').click();
            }
        });

        // Clear auto search timeout and indicators when user focuses input
        $('#noPeserta').on('focus', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Masukkan atau scan QR No Peserta');
        });

        // Clear indicators when user clicks away
        $('#noPeserta').on('blur', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Masukkan atau scan QR No Peserta');
        });

        // Event listener untuk tombol kembali ke step 1
        $('#btnKembaliStep1').on('click', function() {
            kembaliKeStep1();
        });

        // ==================== CHECK ANTRIAN PESERTA ====================
        let checkAntrianTimer = null;
        let currentRecommendedNoPeserta = null;
        let isCheckingAntrian = false;

        // Fungsi untuk check antrian peserta
        function checkAntrianPeserta() {
            // Hanya check jika di step 1 dan tidak sedang checking
            if (isCheckingAntrian) {
                return;
            }

            // Cek apakah masih di step 1
            const step1Active = $('#step1').hasClass('active') || stepper._currentIndex === 0;
            if (!step1Active) {
                return;
            }

            isCheckingAntrian = true;

            $.ajax({
                url: '<?= base_url('backend/munaqosah/get-next-peserta-from-antrian') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    isCheckingAntrian = false;

                    if (response.success && response.hasPeserta && response.NoPeserta) {
                        // Ada peserta baru di antrian
                        if (currentRecommendedNoPeserta !== response.NoPeserta) {
                            currentRecommendedNoPeserta = response.NoPeserta;
                            showPesertaAntrianButton(response.NoPeserta);
                        }
                    } else {
                        // Tidak ada peserta di antrian
                        if (currentRecommendedNoPeserta !== null) {
                            currentRecommendedNoPeserta = null;
                            hidePesertaAntrianButton();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    isCheckingAntrian = false;
                    console.error('Error checking antrian:', error);
                    // Tidak tampilkan error ke user, hanya log
                }
            });
        }

        // Fungsi untuk menampilkan button peserta antrian
        function showPesertaAntrianButton(noPeserta) {
            $('#btnPesertaAntrianText').text(noPeserta);
            $('#btnPesertaAntrian').fadeIn(300);
        }

        // Fungsi untuk menyembunyikan button peserta antrian
        function hidePesertaAntrianButton() {
            $('#btnPesertaAntrian').fadeOut(300);
            $('#btnPesertaAntrianText').text('Peserta Antrian');
        }

        // Event listener untuk button peserta antrian
        $('#btnPesertaAntrian').on('click', function() {
            if (currentRecommendedNoPeserta) {
                // Set No Peserta dan trigger check peserta
                $('#noPeserta').val(currentRecommendedNoPeserta);
                // Trigger cek peserta otomatis
                $('#btnCekPeserta').click();
            }
        });

        // Start checking antrian setiap 15 detik (hanya jika di step 1)
        function startCheckAntrian() {
            if (checkAntrianTimer) {
                clearInterval(checkAntrianTimer);
            }

            // Check pertama kali setelah 2 detik
            setTimeout(function() {
                checkAntrianPeserta();
            }, 2000);

            // Check setiap 15 detik
            checkAntrianTimer = setInterval(function() {
                // Hanya check jika masih di step 1
                const step1Active = $('#step1').hasClass('active') || stepper._currentIndex === 0;
                if (step1Active) {
                    checkAntrianPeserta();
                }
            }, 15000); // 15 detik
        }

        // Stop checking antrian
        function stopCheckAntrian() {
            if (checkAntrianTimer) {
                clearInterval(checkAntrianTimer);
                checkAntrianTimer = null;
            }
            hidePesertaAntrianButton();
            currentRecommendedNoPeserta = null;
        }

        // Mulai check antrian saat halaman dimuat (jika di step 1)
        startCheckAntrian();

        // Stop check antrian saat pindah ke step 2
        // (akan dihandle oleh fungsi proceedToStep2)

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            if (checkAntrianTimer) {
                clearInterval(checkAntrianTimer);
            }
        });

        // ==================== END CHECK ANTRIAN PESERTA ====================

        // Initialize DataTables untuk tabel peserta terakhir
        if ($.fn.DataTable && $('#tabelPesertaTerakhir').length) {
            $('#tabelPesertaTerakhir').DataTable({
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                order: [
                    [1, "desc"],
                    [2, "desc"]
                ],
                responsive: true,
                columnDefs: [{
                        targets: [3, 5], // Durasi, Aksi
                        orderable: false
                    },
                    {
                        targets: [5], // Aksi
                        searchable: false
                    }
                ],
                language: {
                    decimal: ",",
                    emptyTable: "Tidak ada data yang tersedia",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    infoPostFix: "",
                    thousands: ".",
                    lengthMenu: "Tampilkan _MENU_ data",
                    loadingRecords: "Memuat...",
                    processing: "Memproses...",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }

        // Initialize
    });

    // Global variable untuk menyimpan URL ayat saat ini dan IdMateri yang sedang dilihat
    let currentAyatUrl = '';
    let currentMateriIdForAyat = null;

    // Fungsi untuk menampilkan section ayat
    function showAyatModal(url, title, materiId = null) {
        currentAyatUrl = url;
        currentMateriIdForAyat = materiId; // Simpan IdMateri yang sedang dilihat
        $('#ayatTitle').text(title);

        // Reset iframe terlebih dahulu
        $('#iframeAyat').attr('src', '');

        // Tampilkan section
        $('#ayatSection').slideDown(300);

        // Set iframe source setelah section ditampilkan
        setTimeout(function() {
            $('#iframeAyat').attr('src', url);
        }, 350);

        // Scroll ke section ayat
        $('html, body').animate({
            scrollTop: $('#ayatSection').offset().top - 100
        }, 500);
    }

    // Fungsi untuk menyembunyikan section ayat
    function hideAyatSection() {
        $('#ayatSection').slideUp(300, function() {
            $('#iframeAyat').attr('src', '');

            // Fokuskan ke input field yang sesuai dengan IdMateri yang sedang dilihat
            if (currentMateriIdForAyat) {
                const inputField = $('#nilai_' + currentMateriIdForAyat);
                if (inputField.length > 0) {
                    // Scroll ke input field
                    $('html, body').animate({
                        scrollTop: inputField.offset().top - 100
                    }, 300);

                    // Fokuskan kursor ke input field setelah animasi selesai
                    setTimeout(function() {
                        inputField.focus();
                    }, 350);
                }
            }

            // Reset variabel
            currentMateriIdForAyat = null;
        });
    }

    // Fungsi untuk membuka ayat di tab baru
    function openInNewTab() {
        if (currentAyatUrl) {
            window.open(currentAyatUrl, '_blank');
        }
    }

    // Fungsi untuk set No Peserta dari tabel peserta terakhir
    function setNoPeserta(noPeserta) {
        $('#noPeserta').val(noPeserta);
        // Trigger cek peserta otomatis
        $('#btnCekPeserta').click();
    }

    // ==================== AYAT API FUNCTIONS ====================
    // Global variable untuk menyimpan IdMateri yang sedang dilihat
    let currentMateriIdForAyatApi = null;
    // Global variable untuk menyimpan semua data ayat dan pagination
    let allAyahsData = [];
    let currentPage = 1;
    const ayahsPerPage = 5;

    // Fungsi untuk menampilkan ayat dari API
    function showAyatApiModal(materiId, idSurah, idAyat, title) {
        currentMateriIdForAyatApi = materiId;
        $('#ayatApiTitle').text(title || 'Lihat Ayat Al-Qur\'an');

        // Show loading
        Swal.fire({
            title: 'Memuat...',
            text: 'Sedang mengambil ayat dari API',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX call to get ayat from API
        $.ajax({
            url: '<?= base_url("backend/munaqosah/getAyahByMateri") ?>',
            type: 'POST',
            data: {
                IdMateri: materiId
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (response.success && response.data) {
                    const data = response.data;
                    const materiInfo = data.materi_info || {};

                    // Simpan semua data ayat untuk pagination
                    allAyahsData = data.ayahs || [];

                    // Hitung halaman yang berisi IdAyat untuk kategori non-Baca Quran
                    if (!materiInfo.IsBacaQuran && idAyat) {
                        // Cari index ayat yang sesuai dengan IdAyat
                        const targetAyahIndex = allAyahsData.findIndex(function(ayah) {
                            return (ayah.ayah_number == idAyat) || (ayah.number_in_surah == idAyat);
                        });

                        if (targetAyahIndex >= 0) {
                            // Hitung halaman yang berisi ayat tersebut
                            currentPage = Math.floor(targetAyahIndex / ayahsPerPage) + 1;
                        } else {
                            currentPage = 1;
                        }
                    } else {
                        // Untuk Baca Quran, mulai dari halaman pertama
                        currentPage = 1;
                    }

                    // Render content dengan pagination
                    renderAyahsWithPagination(data, materiInfo, idAyat);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal mengambil ayat dari API'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                let errorMessage = 'Terjadi kesalahan koneksi';

                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default error message
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error Koneksi',
                    text: errorMessage + ' (' + error + ')'
                });
            }
        });
    }

    // Fungsi untuk render ayat dengan pagination
    function renderAyahsWithPagination(data, materiInfo, targetAyahNumber = null) {
        const totalAyahs = allAyahsData.length;
        const totalPages = Math.ceil(totalAyahs / ayahsPerPage);
        const startIndex = (currentPage - 1) * ayahsPerPage;
        const endIndex = Math.min(startIndex + ayahsPerPage, totalAyahs);
        const currentAyahs = allAyahsData.slice(startIndex, endIndex);

        let contentHtml = '';

        // Table untuk menampilkan ayat
        if (currentAyahs.length > 0) {
            contentHtml += `
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
            `;

            currentAyahs.forEach(function(ayah, index) {
                const ayahNumber = ayah.ayah_number || ayah.number || '';
                const ayahText = ayah.text || ''; // Text sudah dibersihkan dari bismillah di service level

                // Cek apakah ini ayat target yang harus di-highlight
                const isTargetAyah = targetAyahNumber && (
                    ayahNumber == targetAyahNumber ||
                    ayah.ayah_number == targetAyahNumber ||
                    ayah.number_in_surah == targetAyahNumber
                );
                const highlightClass = isTargetAyah ? 'table-warning' : '';
                const highlightStyle = isTargetAyah ? 'background-color: #fff3cd; font-weight: bold;' : '';

                // Tampilkan ayat
                contentHtml += `
                    <tr class="${highlightClass}" data-ayah-number="${ayahNumber}" ${isTargetAyah ? 'id="target-ayah-row"' : ''}>
                        <td class="text-center align-middle" style="vertical-align: middle; ${highlightStyle}">
                            <strong style="font-size: 18px; color: ${isTargetAyah ? '#856404' : '#007bff'};">
                                ${isTargetAyah ? '<i class="fas fa-bookmark"></i> ' : ''}${ayahNumber}
                            </strong>
                        </td>
                        <td class="ayah-text-api" style="text-align: right; direction: rtl; font-family: 'Amiri', 'Traditional Arabic', 'Arial', serif; padding: 10px 15px; ${highlightStyle}">
                            ${ayahText}
                        </td>
                    </tr>
                `;
            });

            contentHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            // Pagination controls
            if (totalPages > 1) {
                contentHtml += `
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                        <div>
                            <span class="text-muted">
                                Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalAyahs} ayat
                            </span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnPrevPage" ${currentPage === 1 ? 'disabled' : ''}>
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                Halaman ${currentPage} dari ${totalPages}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnNextPage" ${currentPage === totalPages ? 'disabled' : ''}>
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        } else {
            contentHtml += '<p class="text-muted">Tidak ada ayat yang ditemukan.</p>';
        }

        $('#ayatApiContent').html(contentHtml);
        $('#ayatApiSection').slideDown(300);

        // Apply saved zoom level
        applySavedZoomLevel();

        // Setup pagination button handlers
        setupPaginationHandlers(data, materiInfo, targetAyahNumber);

        // Scroll ke section ayat
        $('html, body').animate({
            scrollTop: $('#ayatApiSection').offset().top - 100
        }, 500);

        // Jika ada target ayat, scroll ke ayat tersebut setelah render
        if (targetAyahNumber) {
            setTimeout(function() {
                const targetRow = $('#target-ayah-row');
                if (targetRow.length > 0) {
                    $('html, body').animate({
                        scrollTop: targetRow.offset().top - 150
                    }, 500);

                    // Highlight dengan animasi
                    targetRow.css('transition', 'background-color 0.3s ease');
                    setTimeout(function() {
                        targetRow.css('background-color', '#fff3cd');
                        setTimeout(function() {
                            targetRow.css('background-color', '');
                        }, 2000);
                    }, 100);
                }
            }, 600);
        }
    }

    // Fungsi untuk setup pagination handlers
    function setupPaginationHandlers(data, materiInfo, targetAyahNumber = null) {
        // Remove existing handlers
        $('#btnPrevPage, #btnNextPage').off('click');

        // Prev page handler
        $('#btnPrevPage').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderAyahsWithPagination(data, materiInfo, targetAyahNumber);
                // Scroll ke atas tabel
                $('html, body').animate({
                    scrollTop: $('#ayatApiContent').offset().top - 100
                }, 300);
            }
        });

        // Next page handler
        $('#btnNextPage').on('click', function() {
            const totalPages = Math.ceil(allAyahsData.length / ayahsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderAyahsWithPagination(data, materiInfo, targetAyahNumber);
                // Scroll ke atas tabel
                $('html, body').animate({
                    scrollTop: $('#ayatApiContent').offset().top - 100
                }, 300);
            }
        });
    }

    // Fungsi untuk menyembunyikan section ayat API
    function hideAyatApiSection() {
        $('#ayatApiSection').slideUp(300, function() {
            $('#ayatApiContent').html('');

            // Fokuskan ke input field yang sesuai dengan IdMateri yang sedang dilihat
            if (currentMateriIdForAyatApi) {
                const inputField = $('#nilai_' + currentMateriIdForAyatApi);
                if (inputField.length > 0) {
                    // Scroll ke input field
                    $('html, body').animate({
                        scrollTop: inputField.offset().top - 100
                    }, 300);

                    // Fokuskan kursor ke input field setelah animasi selesai
                    setTimeout(function() {
                        inputField.focus();
                    }, 350);
                }
            }

            // Reset variabel
            resetAyatApiVariables();
        });
    }

    // Fungsi untuk reset variabel ayat API
    function resetAyatApiVariables() {
        currentMateriIdForAyatApi = null;
        allAyahsData = [];
        currentPage = 1;
    }

    // Fungsi untuk reset tampilan ayat API (untuk digunakan saat kembali ke step 1)
    function resetAyatApiView() {
        // Sembunyikan section ayat API
        $('#ayatApiSection').hide();

        // Kosongkan content
        $('#ayatApiContent').html('');

        // Reset title
        $('#ayatApiTitle').text('Lihat Ayat Al-Qur\'an');

        // Reset variabel
        resetAyatApiVariables();

        // Remove event handlers untuk pagination
        $('#btnPrevPage, #btnNextPage').off('click');
    }

    // ==================== ZOOM FUNCTIONS ====================
    const STORAGE_KEY_ZOOM = 'munaqosah_ayat_api_zoom';
    const DEFAULT_FONT_SIZE = 36;
    const MIN_FONT_SIZE = 20;
    const MAX_FONT_SIZE = 64;
    const ZOOM_STEP = 6;

    let currentFontSize = DEFAULT_FONT_SIZE;

    // Load saved zoom level from localStorage
    function loadSavedZoomLevel() {
        const savedZoom = localStorage.getItem(STORAGE_KEY_ZOOM);
        if (savedZoom) {
            currentFontSize = parseInt(savedZoom);
            // Ensure it's within valid range
            if (currentFontSize < MIN_FONT_SIZE) {
                currentFontSize = MIN_FONT_SIZE;
            } else if (currentFontSize > MAX_FONT_SIZE) {
                currentFontSize = MAX_FONT_SIZE;
            }
        }
    }

    // Apply saved zoom level to all ayah texts
    function applySavedZoomLevel() {
        loadSavedZoomLevel();
        $('.ayah-text-api').each(function() {
            $(this).css({
                'font-size': currentFontSize + 'px',
                'line-height': '1.3',
                'transition': 'font-size 0.3s ease'
            });
        });
    }

    // Setup zoom buttons
    $(document).ready(function() {
        // Load saved zoom level on page load
        loadSavedZoomLevel();

        // Zoom In button
        $('#btnZoomInApi').on('click', function() {
            if (currentFontSize < MAX_FONT_SIZE) {
                currentFontSize = Math.min(currentFontSize + ZOOM_STEP, MAX_FONT_SIZE);
                $('.ayah-text-api').css('font-size', currentFontSize + 'px');
                localStorage.setItem(STORAGE_KEY_ZOOM, currentFontSize);
            }
        });

        // Zoom Out button
        $('#btnZoomOutApi').on('click', function() {
            if (currentFontSize > MIN_FONT_SIZE) {
                currentFontSize = Math.max(currentFontSize - ZOOM_STEP, MIN_FONT_SIZE);
                $('.ayah-text-api').css('font-size', currentFontSize + 'px');
                localStorage.setItem(STORAGE_KEY_ZOOM, currentFontSize);
            }
        });

        // Reset Zoom button
        $('#btnResetZoomApi').on('click', function() {
            currentFontSize = DEFAULT_FONT_SIZE;
            $('.ayah-text-api').css('font-size', currentFontSize + 'px');
            localStorage.setItem(STORAGE_KEY_ZOOM, currentFontSize);
        });
    });
</script>

<?= $this->endSection(); ?>