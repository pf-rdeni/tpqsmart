<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-warning">
            <h3 class="card-title">
                <i class="fas fa-edit"></i> Ubah Absensi Santri
            </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
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
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Status Absensi Per Kelas
                        </h3>
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
                    displayStatusKelas(response.data, tanggal);
                } else {
                    $('#statusKelasList').html('<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada kelas yang ditemukan</div></div>');
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
    function displayStatusKelas(data, tanggal) {
        const container = $('#statusKelasList');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Tidak ada kelas yang ditemukan</div></div>');
            return;
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
                            <h6 class="card-title mb-2">
                                <i class="fas fa-chalkboard-teacher"></i> ${kelas.NamaKelas}
                            </h6>
                            <div class="mb-2">
                                <span class="status-badge ${statusClass}">
                                    <i class="${statusIcon}"></i> ${statusText}
                                </span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> ${kelas.totalAbsen} / ${kelas.totalSantri} santri
                                </small>
                            </div>
                            ${kategoriBadges}
                            <div class="progress mb-2" style="height: 8px;">
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

