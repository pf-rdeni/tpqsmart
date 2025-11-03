<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Input Nilai Munaqosah</h3>
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
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="noPeserta">No Peserta <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="noPeserta" name="noPeserta" placeholder="Ketikkan atau scan QR No Peserta" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" id="btnScanQR">
                                                                <i class="fas fa-qrcode"></i> Scan QR
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Ketikkan nomor peserta atau gunakan tombol scan untuk membaca QR code pada kartu peserta<br>
                                                        <span class="text-info"><i class="fas fa-info-circle"></i> Auto search akan aktif setelah 3 digit, atau tekan Enter</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
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
                                        <div id="infoPeserta" class="alert alert-warning" style="display: none;">
                                            <h5><i class="icon fas fa-exclamation-triangle"></i> Informasi Peserta Sudah Dinilai</h5>
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
                                                                        <span class="info-box-number"><?= $total_peserta_sudah_dinilai ?></span>
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
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover mb-0" id="tabelPesertaTerakhir">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th width="15%">No Peserta</th>
                                                                            <th width="20%">Tanggal</th>
                                                                            <th width="12%">Waktu</th>
                                                                            <th width="15%">Durasi</th>
                                                                            <th width="18%">Juri</th>
                                                                            <th width="20%">Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tbodyPesertaTerakhir">
                                                                        <?php foreach ($peserta_terakhir as $index => $peserta): ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <strong><?= $peserta['NoPeserta'] ?></strong>
                                                                                </td>
                                                                                <td>
                                                                                    <?= date('d/m/Y', strtotime($peserta['updated_at'])) ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?= date('H:i:s', strtotime($peserta['updated_at'])) ?>
                                                                                </td>
                                                                                <td class="duration-cell">
                                                                                    <span class="<?= $peserta['duration_class'] ?>"><?= $peserta['duration'] ?></span>
                                                                                </td>
                                                                                <td>
                                                                                    <?= $peserta['UsernameJuri'] ?>
                                                                                </td>
                                                                                <td>
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

<!-- Section untuk menampilkan Ayat -->
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
        text-align: center;
        vertical-align: middle;
    }

    /* Center header dan isi kolom durasi */
    #tabelPesertaTerakhir th:nth-child(4) {
        text-align: center;
    }

    #tabelPesertaTerakhir td:nth-child(4) {
        text-align: center;
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

    /* Styling untuk tabel peserta terakhir */
    #tabelPesertaTerakhir {
        font-size: 0.9rem;
    }

    #tabelPesertaTerakhir th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
    }

    /* Override untuk kolom No Peserta (left align) */
    #tabelPesertaTerakhir th:first-child {
        text-align: left;
    }

    #tabelPesertaTerakhir td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
        text-align: center;
    }

    /* Override untuk kolom No Peserta (left align) */
    #tabelPesertaTerakhir td:first-child {
        text-align: left;
    }

    #tabelPesertaTerakhir tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Styling untuk kolom spesifik */
    #tabelPesertaTerakhir td:first-child {
        font-weight: 600;
        color: #495057;
    }

    #tabelPesertaTerakhir td:nth-child(2),
    #tabelPesertaTerakhir td:nth-child(3) {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: #495057;
    }

    #tabelPesertaTerakhir td:nth-child(5) {
        font-size: 0.8rem;
        color: #6c757d;
        font-family: 'Courier New', monospace;
    }

    /* Styling untuk button aksi */
    #tabelPesertaTerakhir .btn {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #tabelPesertaTerakhir {
            font-size: 0.8rem;
        }

        #tabelPesertaTerakhir th,
        #tabelPesertaTerakhir td {
            padding: 8px 4px;
        }
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

                        // Show peserta info with room validation info
                        showPesertaInfo(response.data.peserta, response.data.roomValidation);

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

        // Show peserta info
        function showPesertaInfo(peserta, roomValidation = null) {
            let roomInfoHtml = '';

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

        // Proceed to step 2
        function proceedToStep2() {
            generateNilaiForm();
            stepper.next();
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
                    // Tambahkan info tambahan untuk materi Quran
                    let additionalInfo = '';
                    if (materi.WebLinkAyat) {
                        additionalInfo = `
                         <br>
                         <div class="mt-2">
                             <button type="button" class="btn btn-sm btn-outline-info" onclick="showAyatModal('${materi.WebLinkAyat}', '${materi.NamaMateri}')">
                                 <i class="fas fa-eye"></i> Lihat Ayat
                             </button>
                         </div>
                     `;
                    }

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
                                 ${additionalInfo}
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
            currentPesertaData = null;
            currentJuriData = null;
            currentMateriData = null;
            errorCategoriesByKategori = {};
            isEditMode = false;

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

        // Initialize
    });

    // Global variable untuk menyimpan URL ayat saat ini
    let currentAyatUrl = '';

    // Fungsi untuk menampilkan section ayat
    function showAyatModal(url, title) {
        currentAyatUrl = url;
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
</script>

<?= $this->endSection(); ?>