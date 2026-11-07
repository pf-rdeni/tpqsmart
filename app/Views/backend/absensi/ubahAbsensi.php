<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-warning d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-edit"></i> Ubah Absensi Santri
            </h3>
            <?php if (!empty($isOperator)): ?>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-outline-dark btn-sm shadow-sm font-weight-bold" data-toggle="collapse" data-target="#panelDeteksiLibur">
                        <i class="fas fa-search-location mr-1 text-danger"></i> Deteksi & Cek Absensi Hari Libur
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php if (!empty($isOperator)): ?>
                <!-- Panel Khusus Deteksi Absensi Hari Libur (Bulan / Semester / Preferensi Hari) -->
                <div class="collapse mb-4" id="panelDeteksiLibur">
                    <div class="card card-outline card-danger shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title font-weight-bold text-danger mb-0">
                                <i class="fas fa-calendar-check mr-1"></i> Deteksi Salah Input Absensi pada Hari Libur Lembaga
                            </h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#panelDeteksiLibur"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="font-weight-600 text-sm"><i class="fas fa-filter mr-1"></i> Rentang Periode:</label>
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-secondary btn-sm active" id="lblPerBulan">
                                            <input type="radio" name="optPeriodeLibur" value="bulan" checked autocomplete="off"> Per Bulan
                                        </label>
                                        <label class="btn btn-outline-secondary btn-sm" id="lblPerSemester">
                                            <input type="radio" name="optPeriodeLibur" value="semester" autocomplete="off"> Per Semester
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="font-weight-600 text-sm"><i class="fas fa-calendar-day mr-1"></i> Preferensi Hari Libur Lembaga:</label>
                                    <div class="d-flex flex-wrap" id="checkboxHariLibur">
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur0" value="0" checked>
                                            <label for="chkLibur0" class="custom-control-label font-weight-normal text-danger">Minggu</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur6" value="6" checked>
                                            <label for="chkLibur6" class="custom-control-label font-weight-normal text-danger">Sabtu</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur5" value="5">
                                            <label for="chkLibur5" class="custom-control-label font-weight-normal">Jumat</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur1" value="1">
                                            <label for="chkLibur1" class="custom-control-label font-weight-normal">Senin</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur2" value="2">
                                            <label for="chkLibur2" class="custom-control-label font-weight-normal">Selasa</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur3" value="3">
                                            <label for="chkLibur3" class="custom-control-label font-weight-normal">Rabu</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mr-3 mb-1">
                                            <input class="custom-control-input" type="checkbox" id="chkLibur4" value="4">
                                            <label for="chkLibur4" class="custom-control-label font-weight-normal">Kamis</label>
                                        </div>
                                    </div>
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Centang hari yang ditetapkan sebagai hari libur di lembaga Anda.</small>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <button type="button" class="btn btn-danger btn-block font-weight-bold shadow-sm" id="btnJalankanDeteksi" onclick="jalankanDeteksiHariLibur()">
                                        <i class="fas fa-search mr-1"></i> Scan & Temukan
                                    </button>
                                </div>
                            </div>

                            <!-- Area Hasil Scan Hari Libur -->
                            <div id="hasilScanLibur" class="mt-3" style="display: none;">
                                <div class="alert alert-light border py-2 mb-2 d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-sm" id="scanLiburInfoTitle">
                                        <i class="fas fa-history mr-1"></i> Hasil Scan
                                    </span>
                                    <span class="badge badge-danger text-sm px-2 py-1" id="scanLiburBadgeCount">0 Tanggal Ditemukan</span>
                                </div>
                                <div id="scanLiburContainer">
                                    <!-- Render list tanggal libur via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Step 1: Pilih Tanggal -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">
                            <i class="fas fa-calendar-alt"></i> Pilih Tanggal:
                        </label>
                        <input type="date" 
                            id="tanggal" 
                            class="form-control form-control-lg" 
                            value="<?= date('Y-m-d') ?>"
                            required>
                    </div>
                </div>
            </div>

            <!-- Status Absensi Per Kelas -->
            <div id="statusAbsensiKelas" class="mb-4" style="display: none;">
                <div class="card card-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Status Absensi Per Kelas
                        </h3>
                        <div class="card-tools ml-auto" id="containerBatalSemua" style="display: none;">
                            <button type="button" class="btn btn-danger btn-sm shadow-sm font-weight-bold" id="btnBatalAbsensiSemua" onclick="konfirmasiBatalkanAbsensi('all')">
                                <i class="fas fa-calendar-times mr-1"></i> Batalkan Absensi Seluruh Kelas (Hari Libur)
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="statusKelasList" class="row">
                            <!-- Akan diisi via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Pencarian Santri -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="searchSantri">
                            <i class="fas fa-search"></i> Cari Santri:
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                id="searchSantri" 
                                class="form-control" 
                                placeholder="Ketik nama santri (minimal 2 karakter)..."
                                autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btnSearch">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Ketik nama santri untuk mencari
                        </small>
                    </div>
                </div>
            </div>

            <!-- Hasil Pencarian -->
            <div id="searchResults" class="mb-4" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Hasil Pencarian:</h5>
                    <div id="tanggalPencarian" class="text-muted">
                        <!-- Akan diisi via JavaScript -->
                    </div>
                </div>
                <div id="santriList" class="list-group"></div>
            </div>

            <!-- Step 3: Form Edit Absensi -->
            <div id="formEditAbsensi" style="display: none;">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i> Edit Absensi
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Info Santri -->
                        <div id="santriInfo" class="mb-4 p-3 bg-light rounded">
                            <!-- Akan diisi via JavaScript -->
                        </div>

                        <!-- Form Absensi -->
                        <form id="formAbsensi">
                            <input type="hidden" id="IdSantri" name="IdSantri">
                            <input type="hidden" id="tanggalAbsensi" name="tanggal">

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-clipboard-check"></i> Status Kehadiran:
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <label class="btn btn-success absensi-btn-mobile">
                                        <input type="radio" name="kehadiran" value="Hadir" autocomplete="off">
                                        <i class="fas fa-check-circle"></i> Hadir
                                    </label>
                                    <label class="btn btn-warning absensi-btn-mobile">
                                        <input type="radio" name="kehadiran" value="Izin" autocomplete="off">
                                        <i class="fas fa-info-circle"></i> Izin
                                    </label>
                                    <label class="btn btn-info absensi-btn-mobile">
                                        <input type="radio" name="kehadiran" value="Sakit" autocomplete="off">
                                        <i class="fas fa-thermometer-half"></i> Sakit
                                    </label>
                                    <label class="btn btn-danger absensi-btn-mobile">
                                        <input type="radio" name="kehadiran" value="Alfa" autocomplete="off">
                                        <i class="fas fa-times-circle"></i> Alfa
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="keteranganGroup" style="display: none;">
                                <label for="keterangan">
                                    <i class="fas fa-comment-alt"></i> Keterangan:
                                </label>
                                <input type="text" 
                                    id="keterangan" 
                                    name="keterangan" 
                                    class="form-control form-control-lg" 
                                    placeholder="Masukkan keterangan (opsional)">
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg btn-block mt-2" id="btnCancel">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    .absensi-btn-mobile {
        min-height: 48px;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-weight: 500;
        position: relative;
        overflow: visible;
        flex: 1;
    }

    .absensi-btn-mobile i {
        margin-right: 6px;
        font-size: 1rem;
    }

    .absensi-btn-mobile.active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
        font-weight: 600;
        border-width: 2px;
    }

    .absensi-btn-mobile:hover:not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .list-group-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .list-group-item .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    .list-group-item .badge i {
        margin-right: 3px;
    }

    #tanggalPencarian {
        font-size: 0.9rem;
    }

    #tanggalPencarian i {
        margin-right: 5px;
    }

    .santri-info-card {
        border-left: 4px solid #007bff;
    }

    .photo-profil-thumbnail {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #dee2e6;
    }

    .status-kelas-card {
        border-left: 4px solid #6c757d;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }

    .status-kelas-card.sudah-absen {
        border-left-color: #28a745;
        background-color: #f8fff9;
    }

    .status-kelas-card.sebagian-absen {
        border-left-color: #ffc107;
        background-color: #fffef8;
    }

    .status-kelas-card.belum-absen {
        border-left-color: #dc3545;
        background-color: #fff8f8;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge.sudah-absen {
        background-color: #28a745;
        color: white;
    }

    .status-badge.sebagian-absen {
        background-color: #ffc107;
        color: #212529;
    }

    .status-badge.belum-absen {
        background-color: #dc3545;
        color: white;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    .badge i {
        margin-right: 3px;
    }

    .gap-1 {
        gap: 0.25rem;
    }

    @media (max-width: 768px) {
        .absensi-btn-mobile {
            min-height: 48px;
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .absensi-btn-mobile i {
            margin-right: 4px;
            font-size: 0.9rem;
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    let searchTimeout;
    let selectedSantri = null;

    // Fungsi untuk load status absensi per kelas
    function loadStatusAbsensiKelas(tanggal) {
        if (!tanggal) {
            $('#statusAbsensiKelas').hide();
            return;
        }

        $.ajax({
            url: '<?= base_url("backend/absensi/getStatusAbsensiPerKelas") ?>',
            type: 'GET',
            data: {
                tanggal: tanggal
            },
            dataType: 'json',
            beforeSend: function() {
                $('#statusKelasList').html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Memuat status...</div>');
                $('#statusAbsensiKelas').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displayStatusKelas(response.data, tanggal, response.isOperator);
                } else {
                    $('#statusKelasList').html('<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada kelas yang ditemukan</div></div>');
                    $('#containerBatalSemua').hide();
                }
                $('#statusAbsensiKelas').show();
            },
            error: function(xhr, status, error) {
                $('#statusKelasList').html('<div class="col-12"><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal memuat status absensi</div></div>');
                $('#statusAbsensiKelas').show();
            }
        });
    }

    // Fungsi untuk display status kelas
    function displayStatusKelas(data, tanggal, isOperator) {
        const container = $('#statusKelasList');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada kelas yang ditemukan</div></div>');
            $('#containerBatalSemua').hide();
            return;
        }

        // Cek apakah ada kelas yang sudah tercatat absensinya pada tanggal ini
        let totalAbsenSemuaKelas = 0;
        data.forEach(function(k) {
            totalAbsenSemuaKelas += (parseInt(k.totalAbsen) || 0);
        });

        // Tombol Batal Seluruh Kelas HANYA untuk Admin / Operator Lembaga
        if (isOperator && totalAbsenSemuaKelas > 0) {
            $('#containerBatalSemua').show();
        } else {
            $('#containerBatalSemua').hide();
        }

        data.forEach(function(kelas) {
            let statusClass = 'belum-absen';
            let statusText = 'Belum Diabsen';
            let statusIcon = 'fas fa-times-circle';
            let statusColor = 'text-danger';

            if (kelas.status === 'sudah_absen_semua') {
                statusClass = 'sudah-absen';
                statusText = 'Sudah Diabsen Semua';
                statusIcon = 'fas fa-check-circle';
                statusColor = 'text-success';
            } else if (kelas.status === 'sebagian_absen') {
                statusClass = 'sebagian-absen';
                statusText = 'Sebagian Diabsen';
                statusIcon = 'fas fa-exclamation-circle';
                statusColor = 'text-warning';
            }

            const progressPercent = kelas.totalSantri > 0 
                ? Math.round((kelas.totalAbsen / kelas.totalSantri) * 100) 
                : 0;

            let guruInfo = '';
            if (kelas.namaGuru) {
                guruInfo = `<small class="text-muted d-block mt-1"><i class="fas fa-user"></i> Oleh: ${kelas.namaGuru}</small>`;
            }

            // Tombol Batalkan Absensi Per Kelas HANYA untuk Admin / Operator Lembaga
            let btnBatalPerKelas = '';
            if (isOperator && kelas.totalAbsen > 0) {
                btnBatalPerKelas = `
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-block" onclick="konfirmasiBatalkanAbsensi('${kelas.IdKelas}', '${kelas.NamaKelas}')">
                            <i class="fas fa-trash-alt mr-1"></i> Batalkan Absensi Kelas Ini
                        </button>
                    </div>
                `;
            }

            // Buat link absensi jika belum diabsen atau sebagian diabsen
            let linkAbsensi = '';
            if ((statusClass === 'belum-absen' || statusClass === 'sebagian-absen') && tanggal) {
                const absensiUrl = `<?= base_url('backend/absensi') ?>?tanggal=${tanggal}&IdKelas=${kelas.IdKelas}`;
                linkAbsensi = `
                    <div class="mt-2">
                        <a href="${absensiUrl}" class="btn btn-sm btn-primary btn-block">
                            <i class="fas fa-clipboard-check"></i> Absen Kelas Ini
                        </a>
                    </div>
                `;
            }

            // Buat badge kategori dengan jumlah
            let kategoriBadges = '';
            if (kelas.totalAbsen > 0) {
                const badges = [];
                if (kelas.jumlahHadir > 0) {
                    badges.push(`<span class="badge badge-success mr-1 mb-1"><i class="fas fa-check-circle"></i> Hadir: ${kelas.jumlahHadir}</span>`);
                }
                if (kelas.jumlahIzin > 0) {
                    badges.push(`<span class="badge badge-warning mr-1 mb-1"><i class="fas fa-info-circle"></i> Izin: ${kelas.jumlahIzin}</span>`);
                }
                if (kelas.jumlahSakit > 0) {
                    badges.push(`<span class="badge badge-info mr-1 mb-1"><i class="fas fa-thermometer-half"></i> Sakit: ${kelas.jumlahSakit}</span>`);
                }
                if (kelas.jumlahAlfa > 0) {
                    badges.push(`<span class="badge badge-danger mr-1 mb-1"><i class="fas fa-times-circle"></i> Alfa: ${kelas.jumlahAlfa}</span>`);
                }
                
                if (badges.length > 0) {
                    kategoriBadges = `<div class="mt-2">${badges.join('')}</div>`;
                }
            }

            const cardHtml = `
                <div class="col-md-6 col-lg-4">
                    <div class="card status-kelas-card ${statusClass}">
                        <div class="card-body">
                            <h6 class="card-title mb-2 font-weight-bold">
                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i> ${kelas.NamaKelas}
                            </h6>
                            <div class="mb-2">
                                <span class="status-badge ${statusClass}">
                                    <i class="${statusIcon}"></i> ${statusText}
                                </span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted font-weight-500">
                                    <i class="fas fa-users"></i> ${kelas.totalAbsen} / ${kelas.totalSantri} santri tercatat
                                </small>
                            </div>
                            ${kategoriBadges}
                            <div class="progress mb-2 mt-2" style="height: 8px;">
                                <div class="progress-bar ${statusClass === 'sudah-absen' ? 'bg-success' : statusClass === 'sebagian-absen' ? 'bg-warning' : 'bg-danger'}" 
                                     role="progressbar" 
                                     style="width: ${progressPercent}%" 
                                     aria-valuenow="${progressPercent}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            ${guruInfo}
                            ${linkAbsensi}
                            ${btnBatalPerKelas}
                        </div>
                    </div>
                </div>
            `;

            container.append(cardHtml);
        });
    }

    $(document).ready(function() {
        // Load status absensi saat halaman pertama kali dimuat
        const tanggal = $('#tanggal').val();
        if (tanggal) {
            loadStatusAbsensiKelas(tanggal);
        }

        // Auto search saat mengetik (debounce)
        $('#searchSantri').on('input', function() {
            const keyword = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (keyword.length >= 2) {
                searchTimeout = setTimeout(function() {
                    searchSantri(keyword);
                }, 500);
            } else {
                $('#searchResults').hide();
                $('#santriList').empty();
            }
        });

        // Manual search button
        $('#btnSearch').on('click', function() {
            const keyword = $('#searchSantri').val().trim();
            if (keyword.length >= 2) {
                searchSantri(keyword);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal ketik 2 karakter untuk mencari',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Handle radio button change
        $('input[name="kehadiran"]').on('change', function() {
            updateActiveState();
            toggleKeteranganField();
        });

        // Handle form submission
        $('#formAbsensi').on('submit', function(e) {
            e.preventDefault();
            saveAbsensi();
        });

        // Handle cancel button
        $('#btnCancel').on('click', function() {
            resetForm();
        });
    });

    function searchSantri(keyword) {
        const tanggal = $('#tanggal').val();
        $.ajax({
            url: '<?= base_url("backend/absensi/searchSantri") ?>',
            type: 'GET',
            data: {
                keyword: keyword,
                tanggal: tanggal || ''
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnSearch').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mencari...');
            },
            success: function(response) {
                $('#btnSearch').prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                
                if (response.success) {
                    displaySearchResults(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal mencari santri',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#btnSearch').prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mencari santri',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function displaySearchResults(data) {
        const listContainer = $('#santriList');
        listContainer.empty();

        // Tampilkan tanggal pencarian
        const tanggal = $('#tanggal').val();
        const tanggalPencarian = $('#tanggalPencarian');
        if (tanggal) {
            const dateObj = new Date(tanggal + 'T00:00:00');
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const tanggalFormatted = dateObj.toLocaleDateString('id-ID', options);
            tanggalPencarian.html(`
                <small>
                    <i class="fas fa-calendar-alt"></i> 
                    <strong>Tanggal:</strong> ${tanggalFormatted}
                </small>
            `);
        } else {
            tanggalPencarian.html('<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Pilih tanggal terlebih dahulu</small>');
        }

        if (data.length === 0) {
            listContainer.html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada santri yang ditemukan</div>');
            $('#searchResults').show();
            return;
        }

        data.forEach(function(santri) {
            const photoHtml = santri.PhotoProfil 
                ? `<img src="<?= base_url('uploads/santri/thumbnails/thumb_') ?>${santri.PhotoProfil}" class="photo-profil-thumbnail mr-2" alt="Photo">`
                : `<i class="fas fa-user-circle mr-2" style="font-size: 50px; color: #6c757d;"></i>`;

            // Buat badge status kehadiran
            let statusBadge = '';
            if (santri.Kehadiran) {
                let badgeClass = '';
                let badgeIcon = '';
                let badgeText = '';
                
                switch(santri.Kehadiran.toLowerCase()) {
                    case 'hadir':
                        badgeClass = 'badge-success';
                        badgeIcon = 'fas fa-check-circle';
                        badgeText = 'Hadir';
                        break;
                    case 'izin':
                        badgeClass = 'badge-warning';
                        badgeIcon = 'fas fa-info-circle';
                        badgeText = 'Izin';
                        break;
                    case 'sakit':
                        badgeClass = 'badge-info';
                        badgeIcon = 'fas fa-thermometer-half';
                        badgeText = 'Sakit';
                        break;
                    case 'alfa':
                        badgeClass = 'badge-danger';
                        badgeIcon = 'fas fa-times-circle';
                        badgeText = 'Alfa';
                        break;
                    default:
                        badgeClass = 'badge-secondary';
                        badgeIcon = 'fas fa-question-circle';
                        badgeText = santri.Kehadiran;
                }
                
                statusBadge = `<span class="badge ${badgeClass} ml-2"><i class="${badgeIcon}"></i> ${badgeText}</span>`;
            } else {
                statusBadge = `<span class="badge badge-secondary ml-2"><i class="fas fa-clock"></i> Belum Diabsen</span>`;
            }

            const item = $(`
                <div class="list-group-item list-group-item-action" data-id="${santri.IdSantri}">
                    <div class="d-flex align-items-center">
                        ${photoHtml}
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">${santri.NamaSantri}</h6>
                                ${statusBadge}
                            </div>
                            <small class="text-muted">${santri.NamaKelas || 'Tidak ada kelas'}</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ml-2"></i>
                    </div>
                </div>
            `);

            item.on('click', function() {
                selectSantri(santri);
            });

            listContainer.append(item);
        });

        $('#searchResults').show();
    }

    function selectSantri(santri) {
        selectedSantri = santri;
        const tanggal = $('#tanggal').val();

        if (!tanggal) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih tanggal terlebih dahulu',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Load absensi data
        loadAbsensiData(santri.IdSantri, tanggal);
    }

    function loadAbsensiData(IdSantri, tanggal) {
        $.ajax({
            url: '<?= base_url("backend/absensi/getAbsensiSantri") ?>',
            type: 'GET',
            data: {
                IdSantri: IdSantri,
                tanggal: tanggal
            },
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Memuat data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    displayAbsensiForm(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data absensi',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function displayAbsensiForm(data) {
        const santri = data.santri;
        const absensi = data.absensi;

        // Display santri info
        const photoHtml = santri.PhotoProfil 
            ? `<img src="<?= base_url('uploads/santri/thumbnails/thumb_') ?>${santri.PhotoProfil}" class="photo-profil-thumbnail mr-3" alt="Photo">`
            : `<i class="fas fa-user-circle mr-3" style="font-size: 50px; color: #6c757d;"></i>`;

        $('#santriInfo').html(`
            <div class="d-flex align-items-center">
                ${photoHtml}
                <div>
                    <h5 class="mb-0">${santri.NamaSantri}</h5>
                    <p class="mb-0 text-muted">${santri.NamaKelas || 'Tidak ada kelas'}</p>
                </div>
            </div>
        `);

        // Set form values
        $('#IdSantri').val(santri.IdSantri);
        $('#tanggalAbsensi').val($('#tanggal').val());

        // Set kehadiran
        if (absensi && absensi.Kehadiran) {
            $('input[name="kehadiran"][value="' + absensi.Kehadiran + '"]').prop('checked', true);
            $('#keterangan').val(absensi.Keterangan || '');
        } else {
            // Default ke Hadir jika belum ada absensi
            $('input[name="kehadiran"][value="Hadir"]').prop('checked', true);
            $('#keterangan').val('');
        }

        updateActiveState();
        toggleKeteranganField();

        // Show form
        $('#formEditAbsensi').slideDown();
        $('html, body').animate({
            scrollTop: $('#formEditAbsensi').offset().top - 100
        }, 500);
    }

    function updateActiveState() {
        $('.absensi-btn-mobile').removeClass('active');
        $('input[name="kehadiran"]:checked').closest('label').addClass('active');
    }

    function toggleKeteranganField() {
        const kehadiran = $('input[name="kehadiran"]:checked').val();
        if (kehadiran !== 'Hadir') {
            $('#keteranganGroup').slideDown();
        } else {
            $('#keteranganGroup').slideUp();
            $('#keterangan').val('');
        }
    }

    function saveAbsensi() {
        const formData = {
            IdSantri: $('#IdSantri').val(),
            tanggal: $('#tanggalAbsensi').val(),
            kehadiran: $('input[name="kehadiran"]:checked').val(),
            keterangan: $('#keterangan').val() || ''
        };

        if (!formData.kehadiran) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih status kehadiran terlebih dahulu',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan perubahan absensi ini?',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Simpan',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("backend/absensi/updateAbsensi") ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Data absensi berhasil disimpan',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                resetForm();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal menyimpan data absensi',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan data',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk membatalkan / menghapus absensi tanggal (Khusus Admin / Operator)
    function konfirmasiBatalkanAbsensi(idKelas, namaKelas = '') {
        const tanggal = $('#tanggal').val();
        if (!tanggal) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih tanggal terlebih dahulu.'
            });
            return;
        }

        const isSemua = (idKelas === 'all');
        const titleText = isSemua 
            ? 'Batalkan Absensi Seluruh Kelas?' 
            : `Batalkan Absensi Kelas ${namaKelas}?`;
        
        const htmlText = isSemua 
            ? `<div class="text-left text-sm">
                <p>Anda akan <strong>menghapus / membatalkan seluruh rekaman absensi</strong> pada tanggal <strong>${tanggal}</strong> untuk semua kelas.</p>
                <div class="alert alert-warning py-2 px-3 mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Gunakan opsi ini jika tanggal tersebut adalah <strong>HARI LIBUR</strong> atau ada kesalahan input absensi massal.
                </div>
                <p class="mb-0 text-muted">Setelah dibatalkan, tanggal ini tidak lagi dianggap sebagai hari aktif TPQ yang menuntut absensi kelas.</p>
               </div>`
            : `<div class="text-left text-sm">
                <p>Anda akan membatalkan rekaman absensi pada kelas <strong>${namaKelas}</strong> untuk tanggal <strong>${tanggal}</strong>.</p>
                <p class="mb-0 text-muted">Santri pada kelas ini tidak lagi tercatat hadir/absen pada tanggal tersebut.</p>
               </div>`;

        Swal.fire({
            title: titleText,
            html: htmlText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Batalkan Absensi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang membatalkan absensi tanggal...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url("backend/absensi/batalkanAbsensiTanggal") ?>',
                    type: 'POST',
                    data: {
                        tanggal: tanggal,
                        IdKelas: idKelas
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload status kelas & reset form
                                loadStatusAbsensiKelas(tanggal);
                                resetForm();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal membatalkan absensi.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem saat membatalkan absensi: ' + error,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk menjalankan deteksi tanggal libur yang terlanjur terabsen
    function jalankanDeteksiHariLibur() {
        const periode = $('input[name="optPeriodeLibur"]:checked').val() || 'bulan';
        const tanggal = $('#tanggal').val() || '<?= date('Y-m-d') ?>';

        // Kumpulkan hari libur yang dicentang
        const hariLiburDipilih = [];
        $('#checkboxHariLibur input[type="checkbox"]:checked').each(function() {
            hariLiburDipilih.push($(this).val());
        });

        if (hariLiburDipilih.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan centang minimal satu hari sebagai preferensi hari libur.'
            });
            return;
        }

        const btn = $('#btnJalankanDeteksi');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memindai...');

        $.ajax({
            url: '<?= base_url("backend/absensi/deteksiAbsensiHariLibur") ?>',
            type: 'GET',
            data: {
                periode: periode,
                tanggal: tanggal,
                hari_libur: hariLiburDipilih.join(',')
            },
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Scan & Temukan');
                if (response.success) {
                    renderHasilDeteksiLibur(response);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal mendeteksi absensi hari libur.'
                    });
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Scan & Temukan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem: ' + error
                });
            }
        });
    }

    // Render hasil scan absensi hari libur
    function renderHasilDeteksiLibur(res) {
        $('#hasilScanLibur').show();
        $('#scanLiburInfoTitle').html(`<i class="fas fa-calendar-alt text-primary mr-1"></i> Hasil Scan Periode: <strong>${res.periode_label}</strong>`);
        $('#scanLiburBadgeCount').text(`${res.total_tanggal_ditemukan} Tanggal Ditemukan`);

        const container = $('#scanLiburContainer');
        container.empty();

        if (res.total_tanggal_ditemukan === 0) {
            container.html(`
                <div class="alert alert-success mb-0 py-3 text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                    <strong>Bersih! Tidak Ditemukan Absensi di Hari Libur</strong>
                    <div class="text-xs text-muted mt-1">Seluruh absensi yang tercatat pada periode ini berada di luar hari libur yang dicentang.</div>
                </div>
            `);
            return;
        }

        let html = '<div class="list-group shadow-sm">';
        res.data.forEach(function(item) {
            let kelasBadgeList = '';
            item.kelas_terkena.forEach(function(k) {
                kelasBadgeList += `<span class="badge badge-light border mr-1 mb-1 font-weight-normal py-1 px-2">
                    <strong class="text-dark">${k.NamaKelas}</strong>: ${k.total_santri} santri 
                    <small class="text-muted">(H:${k.hadir}, I:${k.izin}, S:${k.sakit}, A:${k.alfa})</small>
                </span>`;
            });

            html += `
                <div class="list-group-item list-group-item-action p-3 mb-2 rounded border">
                    <div class="d-flex w-100 justify-content-between align-items-center flex-wrap mb-2">
                        <h6 class="mb-1 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i> ${item.hari}, ${item.tanggal_formatted}
                        </h6>
                        <div>
                            <span class="badge badge-danger px-2 py-1 mr-2">${item.total_rekaman} Total Rekaman</span>
                            <button type="button" class="btn btn-xs btn-outline-primary mr-1" onclick="pilihTanggalScan('${item.tanggal}')" title="Buka dan telusuri tanggal ini di form atas">
                                <i class="fas fa-eye mr-1"></i> Buka Tanggal
                            </button>
                            <button type="button" class="btn btn-xs btn-danger shadow-sm font-weight-bold" onclick="konfirmasiBatalkanAbsensiTanggalLangsung('${item.tanggal}', '${item.hari}, ${item.tanggal_formatted}')" title="Batalkan langsung rekaman absensi hari libur ini">
                                <i class="fas fa-trash-alt mr-1"></i> Batalkan Tanggal Ini
                            </button>
                        </div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Kelas yang terlanjur terabsen pada tanggal ini:</small>
                        <div class="d-flex flex-wrap">${kelasBadgeList}</div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        container.html(html);
    }

    // Klik tombol Buka Tanggal dari hasil scan
    function pilihTanggalScan(tanggal) {
        $('#tanggal').val(tanggal).trigger('change');
        $('html, body').animate({
            scrollTop: $("#statusAbsensiKelas").offset().top - 70
        }, 400);
    }

    // Batalkan langsung dari panel hasil scan hari libur
    function konfirmasiBatalkanAbsensiTanggalLangsung(tanggal, labelTanggal) {
        Swal.fire({
            title: 'Batalkan Absensi Hari Libur?',
            html: `<div class="text-left text-sm">
                <p>Anda akan membatalkan / menghapus <strong>seluruh rekaman absensi</strong> pada tanggal <strong>${labelTanggal}</strong>.</p>
                <div class="alert alert-warning py-2 px-3 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Tanggal ini akan dihapus dari riwayat absensi sehingga tidak lagi dianggap sebagai hari aktif TPQ yang mewajibkan kelas lain absen.
                </div>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Absensi Libur!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menghapus data absensi libur...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url("backend/absensi/batalkanAbsensiTanggal") ?>',
                    type: 'POST',
                    data: {
                        tanggal: tanggal,
                        IdKelas: 'all'
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh status kelas jika sedang di tanggal tersebut dan re-scan
                                if ($('#tanggal').val() === tanggal) {
                                    loadStatusAbsensiKelas(tanggal);
                                }
                                jalankanDeteksiHariLibur();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal membatalkan absensi.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem: ' + error,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    function resetForm() {
        selectedSantri = null;
        $('#searchSantri').val('');
        $('#searchResults').hide();
        $('#santriList').empty();
        $('#formEditAbsensi').hide();
        $('#santriInfo').empty();
        $('#formAbsensi')[0].reset();
        $('.absensi-btn-mobile').removeClass('active');
    }

    // Handle tanggal change
    $('#tanggal').on('change', function() {
        const tanggal = $(this).val();
        
        // Load status absensi per kelas
        loadStatusAbsensiKelas(tanggal);
        
        // Jika ada keyword di search box, trigger pencarian ulang
        const keyword = $('#searchSantri').val().trim();
        if (keyword.length >= 2) {
            searchSantri(keyword);
        }
        
        if (selectedSantri) {
            // Reload absensi data jika santri sudah dipilih
            loadAbsensiData(selectedSantri.IdSantri, tanggal);
        }
    });
</script>
<?= $this->endSection(); ?>

