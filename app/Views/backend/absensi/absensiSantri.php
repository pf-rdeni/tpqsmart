<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check"></i> Absensi Santri
            </h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/absensi/statistikKehadiran') ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-chart-bar"></i> <span class="d-none d-sm-inline">Detail Statistik</span>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
            // Tampilkan flash message jika ada
            if (session()->getFlashdata('success')) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<i class="fas fa-check-circle"></i> ' . session()->getFlashdata('success');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }
            if (session()->getFlashdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<i class="fas fa-exclamation-circle"></i> ' . session()->getFlashdata('error');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }

            // Ambil tanggal dari controller atau gunakan tanggal hari ini
            $tanggalHariIni = isset($tanggal_hari_ini) ? $tanggal_hari_ini : date('Y-m-d');
            $tanggalDipilih = isset($tanggal_dipilih) ? $tanggal_dipilih : $tanggalHariIni;

            // Gunakan kelas_list_all dari controller untuk menampilkan tab (semua kelas)
            // Jika tidak ada, buat dari santri yang ada
            $kelasList = isset($kelas_list_all) && !empty($kelas_list_all) ? $kelas_list_all : [];

            // Jika masih kosong, buat dari santri yang ada
            if (empty($kelasList)) {
                foreach ($santri as $row) {
                    if (!isset($kelasList[$row->IdKelas])) {
                        $kelasList[$row->IdKelas] = [
                            'IdKelas' => $row->IdKelas,
                            'NamaKelas' => $row->NamaKelas,
                            'IdTahunAjaran' => $row->IdTahunAjaran
                        ];
                    }
                }
            }

            // Kelompokkan santri berdasarkan kelas
            $santriByKelas = [];
            foreach ($santri as $row) {
                $santriByKelas[$row->NamaKelas][] = $row;
            }



            // Tampilkan tab kelas meskipun semua santri sudah diabsen
            if (!empty($kelasList)) {
            ?>
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="custom-tabs-one-tab" role="tablist">
                            <?php
                            $firstTab = true;
                            foreach ($kelasList as $index => $kelas): ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $firstTab ? 'active' : '' ?>"
                                        id="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>-tab"
                                        data-toggle="pill"
                                        href="#custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                        role="tab"
                                        aria-controls="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                        aria-selected="<?= $firstTab ? 'true' : 'false' ?>">
                                        <?= htmlspecialchars($kelas['NamaKelas']) ?>
                                    </a>
                                </li>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <?php
                            $firstTab = true;
                            foreach ($kelasList as $kelas):
                                // Ambil santri untuk kelas ini, jika tidak ada gunakan array kosong
                                $santriList = isset($santriByKelas[$kelas['NamaKelas']]) ? $santriByKelas[$kelas['NamaKelas']] : [];
                            ?>
                                <div class="tab-pane fade <?= $firstTab ? 'show active' : '' ?>"
                                    id="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>"
                                    role="tabpanel"
                                    aria-labelledby="custom-tabs-one-kelas-<?= $kelas['IdKelas'] ?>-tab">

                                    <form action="<?= base_url('/backend/absensi/simpanAbsensi') ?>" method="post" id="formAbsensi<?= $kelas['IdKelas'] ?>">
                                        <!-- Date Input -->
                                        <div class="form-group mb-3">
                                            <label for="tanggal<?= $kelas['IdKelas'] ?>">
                                                <i class="fas fa-calendar-alt"></i> Tanggal:
                                            </label>
                                            <input type="date"
                                                name="tanggal"
                                                id="tanggal<?= $kelas['IdKelas'] ?>"
                                                value="<?= $tanggalDipilih; ?>"
                                                class="form-control form-control-lg tanggal-input"
                                                data-kelas-id="<?= $kelas['IdKelas'] ?>"
                                                style="-webkit-appearance: none; -moz-appearance: textfield; appearance: none;"
                                                required>
                                        </div>

                                        <!-- Quick Action: Set All Hadir -->
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-success btn-sm" onclick="setAllHadir(<?= $kelas['IdKelas'] ?>)">
                                                <i class="fas fa-check-double"></i> Set Semua Hadir
                                            </button>
                                            <small class="text-muted ml-2">
                                                <i class="fas fa-info-circle"></i> Default: Semua santri di-set sebagai Hadir
                                            </small>
                                        </div>

                                        <!-- Tambahkan hidden input untuk menyimpan IdKelas, IdGuru, IdTahunAjaran -->
                                        <input type="hidden" name="IdKelas" value="<?= $kelas['IdKelas'] ?>">
                                        <input type="hidden" name="IdGuru" value="<?= session()->get('IdGuru') ?>">
                                        <input type="hidden" name="IdTahunAjaran" value="<?= $kelas['IdTahunAjaran'] ?>">

                                        <!-- Cards View untuk semua ukuran layar -->
                                        <div id="cards-container-<?= $kelas['IdKelas'] ?>" class="cards-container">
                                            <?php if (empty($santriList)): ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i> Semua santri di kelas <?= esc($kelas['NamaKelas']) ?> sudah diabsen pada tanggal <?= date('d-m-Y', strtotime($tanggalDipilih)) ?>.
                                                    <?php
                                                    // Tampilkan nama guru yang mengabsen jika ada
                                                    if (isset($guru_absensi[$kelas['IdKelas']])) {
                                                        echo ' <strong>Oleh: ' . esc($guru_absensi[$kelas['IdKelas']]) . '</strong>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($santriList as $row): ?>
                                                    <div class="card mb-1 shadow-sm">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-2 d-flex align-items-center">
                                                                <?php
                                                                $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                                                $thumbnailPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/');
                                                                $photoProfil = $row->PhotoProfil ?? '';
                                                                ?>
                                                                <?php if ($photoProfil): ?>
                                                                    <img src="<?= $thumbnailPath . 'thumb_' . $photoProfil; ?>"
                                                                        alt="PhotoProfil"
                                                                        class="photo-profil-thumbnail mr-2"
                                                                        loading="lazy"
                                                                        style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                                    <div class="image-popup" style="display: none; position: absolute; z-index: 1000;">
                                                                        <img src="<?= $uploadPath . $photoProfil; ?>"
                                                                            alt="PhotoProfil"
                                                                            width="200"
                                                                            height="250"
                                                                            loading="lazy"
                                                                            class="rounded shadow">
                                                                    </div>
                                                                <?php else: ?>
                                                                    <i class="fas fa-user-circle mr-2" style="font-size: 40px; color: #6c757d;"></i>
                                                                <?php endif; ?>
                                                                <?= esc($row->NamaSantri) ?>
                                                            </h5>

                                                            <!-- Kehadiran Options -->
                                                            <div class="mb-1">
                                                                <div class="btn-group w-100" role="group">
                                                                    <label class="btn btn-success absensi-btn-mobile active">
                                                                        <input type="radio"
                                                                            name="kehadiran[<?= $row->IdSantri ?>]"
                                                                            value="Hadir"
                                                                            autocomplete="off"
                                                                            checked>
                                                                        <i class="fas fa-check-circle"></i> Hadir
                                                                    </label>
                                                                    <label class="btn btn-warning absensi-btn-mobile">
                                                                        <input type="radio"
                                                                            name="kehadiran[<?= $row->IdSantri ?>]"
                                                                            value="Izin"
                                                                            autocomplete="off">
                                                                        <i class="fas fa-info-circle"></i> Izin
                                                                    </label>
                                                                    <label class="btn btn-info absensi-btn-mobile">
                                                                        <input type="radio"
                                                                            name="kehadiran[<?= $row->IdSantri ?>]"
                                                                            value="Sakit"
                                                                            autocomplete="off">
                                                                        <i class="fas fa-thermometer-half"></i> Sakit
                                                                    </label>
                                                                    <label class="btn btn-danger absensi-btn-mobile">
                                                                        <input type="radio"
                                                                            name="kehadiran[<?= $row->IdSantri ?>]"
                                                                            value="Alfa"
                                                                            autocomplete="off">
                                                                        <i class="fas fa-times-circle"></i> Alfa
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <!-- Keterangan Field -->
                                                            <div class="form-group keterangan-field" id="keterangan-field-<?= $row->IdSantri ?>" style="display: none;">
                                                                <label for="keterangan-<?= $row->IdSantri ?>">
                                                                    <i class="fas fa-comment-alt"></i> Keterangan:
                                                                </label>
                                                                <input type="text"
                                                                    name="keterangan[<?= $row->IdSantri ?>]"
                                                                    id="keterangan-<?= $row->IdSantri ?>"
                                                                    class="form-control form-control-lg"
                                                                    placeholder="Masukkan keterangan (opsional)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Submit Button - selalu ada di HTML, tampil/sembunyi via JavaScript -->
                                        <div class="mt-4" id="submit-button-container-<?= $kelas['IdKelas'] ?>" style="<?= empty($santriList) ? 'display: none;' : '' ?>">
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                                <i class="fas fa-save"></i> Simpan Absensi Kelas <?= esc($kelas['NamaKelas']) ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    /* Mobile-friendly styles */
    .absensi-btn {
        min-width: 80px;
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
        position: relative;
        overflow: visible;
    }

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
        width: auto;
        text-align: center;
    }

    .absensi-btn i {
        margin-right: 5px;
    }

    /* Active state styling */
    .absensi-btn.active,
    .absensi-btn-mobile.active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
        font-weight: 600;
        border-width: 2px;
    }


    /* Hover effect for better UX */
    .absensi-btn:hover:not(.active),
    .absensi-btn-mobile:hover:not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Card styling for mobile */
    .card.shadow-sm {
        border-left: 4px solid #007bff;
        margin-bottom: 0.5rem !important;
    }

    .card.shadow-sm .card-body {
        padding: 0.75rem 1rem !important;
    }

    .card.shadow-sm .card-title {
        margin-bottom: 0.75rem !important;
        font-size: 0.95rem;
    }

    .card.shadow-sm .btn-group {
        margin-bottom: 0.5rem !important;
    }

    /* Form control styling */
    .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    /* Keterangan field styling dengan transisi */
    .keterangan-field {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    /* Photo profil styling */
    .photo-profil-thumbnail {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 50% !important;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        object-fit: cover !important;
        object-position: center !important;
        border: 2px solid #dee2e6;
        transition: transform 0.2s ease, border-color 0.2s ease;
        display: block !important;
        flex-shrink: 0;
        padding: 0 !important;
        margin: 0 !important;
    }

    .photo-profil-thumbnail:hover {
        transform: scale(1.1);
        border-color: #007bff;
    }

    .image-popup {
        background: white;
        border: 2px solid #007bff;
        border-radius: 8px;
        padding: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        position: absolute;
        top: 50px;
        left: 0;
        z-index: 1000;
    }

    .image-popup img {
        display: block;
        max-width: 200px;
        max-height: 250px;
        object-fit: cover;
    }

    .card-title {
        position: relative;
    }

    /* Button group styling */
    .btn-group {
        overflow: visible;
    }

    .btn-group .btn {
        overflow: visible;
    }

    /* Table responsive improvements */
    @media (max-width: 768px) {
        .table-responsive {
            border: none;
        }
    }

    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        .absensi-btn {
            min-height: 48px;
            min-width: 90px;
            padding: 0.75rem 1rem;
        }

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

    /* Mobile specific improvements */
    @media (max-width: 768px) {
        .card.shadow-sm {
            margin-bottom: 1rem;
            border-radius: 0.75rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .form-control-lg {
            font-size: 16px;
            /* Prevents zoom on iOS */
            padding: 0.875rem 1rem;
        }

        /* Better spacing for mobile cards */
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem !important;
        }

        .card.shadow-sm {
            margin-bottom: 0.5rem !important;
        }

        .card.shadow-sm .card-body {
            padding: 0.75rem 1rem !important;
        }

        .card.shadow-sm .form-group {
            margin-bottom: 0.5rem !important;
        }

        /* Button horizontal untuk mobile */
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

    /* Fix date input for mobile */
    input[type="date"] {
        position: relative;
        z-index: 1;
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background: transparent;
        cursor: pointer;
        z-index: 2;
    }

    /* Ensure date input is clickable on mobile */
    @media (max-width: 768px) {
        input[type="date"] {
            min-height: 48px;
            font-size: 16px;
            /* Prevents zoom on iOS */
            padding-right: 40px;
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Function untuk menampilkan popup photo profil
    function showPopup(img) {
        const popup = img.nextElementSibling;
        if (popup && popup.classList.contains('image-popup')) {
            // Sembunyikan semua popup lainnya terlebih dahulu
            $('.image-popup').hide();
            // Tampilkan popup yang sesuai
            $(popup).fadeIn(200);
        }
    }

    // Function untuk menyembunyikan popup photo profil
    function hidePopup(img) {
        // Delay sedikit untuk memungkinkan mouse move ke popup
        setTimeout(function() {
            const popup = img.nextElementSibling;
            if (popup && popup.classList.contains('image-popup')) {
                // Cek apakah mouse masih di atas popup atau image
                if (!$(popup).is(':hover') && !$(img).is(':hover')) {
                    $(popup).fadeOut(200);
                }
            }
        }, 200);
    }

    // Function to update active state for radio button labels
    function updateActiveState() {
        // Hapus class active dari semua button terlebih dahulu
        $('.absensi-btn, .absensi-btn-mobile').removeClass('active');

        // Tambahkan class active hanya pada button yang checked
        $('input[type="radio"][name^="kehadiran"]:checked').each(function() {
            $(this).closest('label').addClass('active');
        });
    }

    // Function to toggle keterangan field based on kehadiran status
    function toggleKeteranganField() {
        $('input[type="radio"][name^="kehadiran"]:checked').each(function() {
            var name = $(this).attr('name');
            var id = name.match(/\[(\d+)\]/)[1];
            var status = $(this).val();
            var keteranganField = $('#keterangan-field-' + id);
            var keteranganInput = $('#keterangan-' + id);

            // Tampilkan field keterangan jika status bukan "Hadir"
            if (status !== 'Hadir') {
                keteranganField.slideDown(200);
                keteranganInput.focus();
            } else {
                keteranganField.slideUp(200);
                keteranganInput.val(''); // Clear value saat disembunyikan
            }
        });
    }

    // Function to set all students to "Hadir" in a specific class
    function setAllHadir(idKelas) {
        var form = $('#formAbsensi' + idKelas);

        // Langsung set semua radio button "Hadir" menjadi checked
        form.find('input[type="radio"][value="Hadir"]').each(function() {
            var name = $(this).attr('name');
            // Hapus checked dari semua radio dalam grup ini
            $('input[name="' + name + '"]').prop('checked', false);
            // Set checked pada radio "Hadir"
            $(this).prop('checked', true);
        });

        // Update active states
        updateActiveState();

        // Sembunyikan semua field keterangan karena semua di-set ke "Hadir"
        $('.keterangan-field').slideUp(200);
        $('input[id^="keterangan-"]').val('');

        // Show feedback
        var btn = $('button[onclick*="setAllHadir(' + idKelas + ')"]');
        var originalText = btn.html();
        btn.html('<i class="fas fa-check"></i> Semua Di-set Hadir');

        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    }

    // Initialize
    $(document).ready(function() {
        console.log('[DEBUG] ============================================');
        console.log('[DEBUG] Document ready - Initializing absensi page');
        console.log('[DEBUG] jQuery version:', $.fn.jquery);
        console.log('[DEBUG] ============================================');

        // Test apakah elemen tanggal input ada
        var tanggalInputs = $('.tanggal-input');
        console.log('[DEBUG] Tanggal input elements found:', tanggalInputs.length);

        if (tanggalInputs.length === 0) {
            console.error('[DEBUG] ERROR: No tanggal input elements found!');
            console.error('[DEBUG] This might be because elements are loaded dynamically');
        } else {
            // Test event listener
            tanggalInputs.each(function(index) {
                console.log('[DEBUG] Tanggal input #' + (index + 1) + ':', {
                    id: $(this).attr('id'),
                    kelasId: $(this).data('kelas-id'),
                    value: $(this).val(),
                    element: this
                });
            });

            // Test manual trigger untuk memastikan event listener bekerja
            console.log('[DEBUG] Testing manual change event...');
            tanggalInputs.first().on('change.test', function() {
                console.log('[DEBUG] Manual test event triggered!');
            });
        }

        console.log('[DEBUG] Event listener registered for .tanggal-input');
        console.log('[DEBUG] ============================================');
        // Fungsi untuk memastikan semua "Hadir" terpilih
        function ensureAllHadirSelected() {
            // Loop melalui semua grup radio button
            $('input[type="radio"][name^="kehadiran"]').each(function() {
                var name = $(this).attr('name');
                var checkedRadio = $('input[name="' + name + '"]:checked');

                // Jika tidak ada yang terpilih, pilih "Hadir"
                if (!checkedRadio.length) {
                    var hadirRadio = $('input[name="' + name + '"][value="Hadir"]');
                    if (hadirRadio.length) {
                        hadirRadio.prop('checked', true);
                    }
                }
            });
        }

        // Pastikan semua "Hadir" terpilih (untuk desktop dan mobile)
        ensureAllHadirSelected();

        // Force check semua "Hadir" yang sudah ada atribut checked di HTML
        $('input[type="radio"][value="Hadir"]').each(function() {
            var name = $(this).attr('name');
            // Pastikan hanya satu yang terpilih per grup
            if (!$('input[name="' + name + '"]:checked').length) {
                $(this).prop('checked', true);
            }
        });

        // Update active states untuk semua label (desktop dan mobile)
        updateActiveState();

        // Pastikan field keterangan tersembunyi secara default (karena default adalah "Hadir")
        toggleKeteranganField();

        // Jalankan lagi setelah sedikit delay untuk memastikan
        setTimeout(function() {
            ensureAllHadirSelected();
            updateActiveState();
            toggleKeteranganField();
        }, 100);

        // Handle radio button changes to update active states and toggle keterangan
        $(document).on('change', 'input[type="radio"][name^="kehadiran"]', function() {
            updateActiveState();
            toggleKeteranganField();
        });

        // Handle click event on label to ensure radio is checked
        $(document).on('click', '.absensi-btn, .absensi-btn-mobile', function(e) {
            // Jika yang diklik bukan input radio, trigger click pada input radio di dalam label
            if (!$(e.target).is('input[type="radio"]') && !$(e.target).closest('input[type="radio"]').length) {
                var radioInput = $(this).find('input[type="radio"]');
                if (radioInput.length) {
                    radioInput.prop('checked', true).trigger('change');
                }
            }
        });

        // Handle click event for radio button
        $(document).on('click', 'input[type="radio"][name^="kehadiran"]', function() {
            setTimeout(function() {
                updateActiveState();
                toggleKeteranganField();
            }, 50);
        });

        // Handle click di luar popup untuk menyembunyikan popup
        $(document).on('click touchstart', function(event) {
            // Cek apakah klik/tap di luar photo thumbnail dan popup
            if (!$(event.target).closest('.photo-profil-thumbnail, .image-popup').length) {
                $('.image-popup').fadeOut(200);
            }
        });

        // Handle hover pada popup agar tidak hilang saat hover di popup (desktop)
        $(document).on('mouseenter', '.image-popup', function() {
            $(this).stop(true, true).show();
        });

        $(document).on('mouseleave', '.image-popup', function() {
            $(this).fadeOut(200);
        });

        // Handle hover pada photo untuk menampilkan popup (desktop)
        $(document).on('mouseenter', '.photo-profil-thumbnail', function() {
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                $('.image-popup').not(popup).fadeOut(200);
                popup.fadeIn(200);
            }
        });

        $(document).on('mouseleave', '.photo-profil-thumbnail', function() {
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                // Delay untuk memungkinkan mouse move ke popup
                setTimeout(function() {
                    if (!popup.is(':hover')) {
                        popup.fadeOut(200);
                    }
                }, 200);
            }
        });

        // Handle tap/click pada photo untuk toggle popup (mobile & desktop)
        $(document).on('click', '.photo-profil-thumbnail', function(e) {
            e.stopPropagation();
            const popup = $(this).next('.image-popup');
            if (popup.length) {
                // Toggle popup: jika sudah tampil, sembunyikan; jika belum, tampilkan
                if (popup.is(':visible')) {
                    popup.fadeOut(200);
                } else {
                    $('.image-popup').not(popup).fadeOut(200);
                    popup.fadeIn(200);
                }
            }
        });

        // Attach photo popup handlers saat halaman pertama kali dimuat
        attachPhotoPopupHandlers();

        // Handle perubahan tanggal - load data via AJAX untuk kelas yang dipilih
        $(document).on('change', '.tanggal-input', function() {
            console.log('[DEBUG] Tanggal input changed');
            var tanggal = $(this).val();
            var idKelas = $(this).data('kelas-id');
            console.log('[DEBUG] Tanggal:', tanggal);
            console.log('[DEBUG] IdKelas:', idKelas);

            if (!tanggal) {
                console.error('[DEBUG] Tanggal kosong!');
                return;
            }

            if (!idKelas) {
                console.error('[DEBUG] IdKelas kosong!');
                return;
            }

            var form = $(this).closest('form');
            var tabPane = $(this).closest('.tab-pane');

            // Cari cards container dengan ID yang spesifik berdasarkan IdKelas
            var cardsContainer = form.find('#cards-container-' + idKelas);

            // Jika tidak ditemukan dengan ID, coba cari dengan class
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('.cards-container');
            }

            // Jika masih tidak ditemukan, cari div yang berisi cards - setelah hidden inputs
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('input[name="IdKelas"]').nextAll('div').first();
            }

            // Jika masih tidak ditemukan, cari div yang berisi card.shadow-sm
            if (cardsContainer.length === 0) {
                cardsContainer = form.find('div:has(.card.shadow-sm)').first();
            }

            // Cari tombol submit dengan ID yang spesifik atau dengan selector
            var submitButton = form.find('#submit-button-container-' + idKelas);

            // Jika tidak ditemukan dengan ID, coba cari dengan selector biasa
            if (submitButton.length === 0) {
                submitButton = form.find('button[type="submit"]').parent();
            }

            console.log('[DEBUG] Form found:', form.length > 0);
            console.log('[DEBUG] Tab pane found:', tabPane.length > 0);
            console.log('[DEBUG] Submit button found:', submitButton.length > 0);
            if (submitButton.length > 0) {
                console.log('[DEBUG] Submit button ID:', submitButton.attr('id'));
                console.log('[DEBUG] Submit button is visible:', submitButton.is(':visible'));
            } else {
                console.error('[DEBUG] Submit button NOT FOUND!');
            }
            console.log('[DEBUG] Cards container found:', cardsContainer.length > 0);
            if (cardsContainer.length > 0) {
                console.log('[DEBUG] Cards container ID:', cardsContainer.attr('id'));
                console.log('[DEBUG] Cards container classes:', cardsContainer.attr('class'));
                console.log('[DEBUG] Cards container current content length:', cardsContainer.html().length);
            } else {
                console.error('[DEBUG] Cards container NOT FOUND!');
                console.log('[DEBUG] Trying to find by form structure...');
                console.log('[DEBUG] Form children count:', form.children().length);
                form.children().each(function(index) {
                    console.log('[DEBUG]   Child ' + index + ':', this.tagName, this.className || '(no class)', $(this).attr('id') || '(no id)');
                });
            }

            // Sinkronkan semua input tanggal di semua tab
            $('.tanggal-input').val(tanggal);

            // Tampilkan loading indicator
            cardsContainer.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Memuat data...</p></div>');
            submitButton.hide();

            var ajaxUrl = '<?= base_url("backend/absensi/getSantriByKelasDanTanggal") ?>';
            console.log('[DEBUG] AJAX URL:', ajaxUrl);
            console.log('[DEBUG] AJAX Data:', {
                IdKelas: idKelas,
                tanggal: tanggal
            });

            // Panggil endpoint AJAX
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                data: {
                    IdKelas: idKelas,
                    tanggal: tanggal
                },
                dataType: 'json',
                beforeSend: function() {
                    console.log('[DEBUG] AJAX request started');
                },
                success: function(response) {
                    console.log('[DEBUG] AJAX success response:', response);
                    if (response.success) {
                        console.log('[DEBUG] Response success, updating list with', response.count, 'santri');
                        console.log('[DEBUG] Nama guru:', response.nama_guru || 'Tidak ada');
                        // Update tampilan dengan data baru (fungsi ini juga akan handle show/hide tombol submit)
                        updateSantriList(cardsContainer, response.santri, idKelas, tanggal, response.nama_guru);
                    } else {
                        console.error('[DEBUG] Response success but data failed:', response.message);
                        cardsContainer.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + (response.message || 'Gagal memuat data') + '</div>');
                        submitButton.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[DEBUG] AJAX Error Details:');
                    console.error('[DEBUG] Status:', status);
                    console.error('[DEBUG] Error:', error);
                    console.error('[DEBUG] XHR:', xhr);
                    console.error('[DEBUG] Response Text:', xhr.responseText);
                    console.error('[DEBUG] Status Code:', xhr.status);

                    var errorMessage = 'Terjadi kesalahan saat memuat data.';
                    if (xhr.status === 404) {
                        errorMessage += ' Endpoint tidak ditemukan.';
                    } else if (xhr.status === 500) {
                        errorMessage += ' Server error.';
                    } else if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            errorMessage += ' ' + (errorResponse.message || '');
                        } catch (e) {
                            errorMessage += ' ' + xhr.responseText.substring(0, 100);
                        }
                    }

                    cardsContainer.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + errorMessage + '</div>');
                    submitButton.hide();
                }
            });
        });

        // Fungsi untuk update tampilan list santri
        function updateSantriList(container, santriList, idKelas, tanggal, namaGuru) {
            console.log('[DEBUG] updateSantriList called');
            console.log('[DEBUG] Container:', container.length > 0 ? 'Found' : 'NOT FOUND');
            console.log('[DEBUG] Santri list count:', santriList.length);
            console.log('[DEBUG] IdKelas:', idKelas);
            console.log('[DEBUG] Tanggal:', tanggal);
            console.log('[DEBUG] Nama Guru:', namaGuru || 'Tidak ada');

            if (container.length === 0) {
                console.error('[DEBUG] Container is empty! Cannot update list.');
                return;
            }

            // Cari dan update tombol submit
            var form = container.closest('form');
            var submitButton = form.find('#submit-button-container-' + idKelas);
            if (submitButton.length === 0) {
                submitButton = form.find('button[type="submit"]').parent();
            }

            console.log('[DEBUG] Submit button in updateSantriList:', submitButton.length > 0 ? 'Found' : 'NOT FOUND');

            if (santriList.length === 0) {
                var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                console.log('[DEBUG] No santri, showing empty message');

                // Ambil nama guru dari parameter (jika ada)
                var namaGuruText = '';
                if (namaGuru && namaGuru !== null && namaGuru !== '') {
                    namaGuruText = ' <strong>Oleh: ' + namaGuru + '</strong>';
                }
                var message = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Semua santri sudah diabsen pada tanggal ' + tanggalFormatted + '.' + namaGuruText + '</div>';

                container.html(message);

                // Sembunyikan tombol submit jika tidak ada santri
                if (submitButton.length > 0) {
                    console.log('[DEBUG] Hiding submit button (no santri)');
                    submitButton.hide();
                }
                return;
            }

            // Tampilkan tombol submit jika ada santri
            if (submitButton.length > 0) {
                console.log('[DEBUG] Showing submit button (has santri)');
                submitButton.show();
            }

            console.log('[DEBUG] Building HTML for', santriList.length, 'santri');

            var html = '';
            var uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
            var thumbnailPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/thumbnails/' : base_url('uploads/santri/thumbnails/') ?>';

            santriList.forEach(function(santri) {
                var photoHtml = '';
                if (santri.PhotoProfil) {
                    photoHtml = '<img src="' + thumbnailPath + 'thumb_' + santri.PhotoProfil + '" ' +
                        'alt="PhotoProfil" ' +
                        'class="photo-profil-thumbnail mr-2" ' +
                        'loading="lazy" ' +
                        'style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">' +
                        '<div class="image-popup" style="display: none; position: absolute; z-index: 1000;">' +
                        '<img src="' + uploadPath + santri.PhotoProfil + '" ' +
                        'alt="PhotoProfil" width="200" height="250" loading="lazy" class="rounded shadow">' +
                        '</div>';
                } else {
                    photoHtml = '<i class="fas fa-user-circle mr-2" style="font-size: 40px; color: #6c757d;"></i>';
                }

                html += '<div class="card mb-1 shadow-sm">' +
                    '<div class="card-body">' +
                    '<h5 class="card-title mb-2 d-flex align-items-center">' +
                    photoHtml +
                    santri.NamaSantri +
                    '</h5>' +
                    '<div class="mb-1">' +
                    '<div class="btn-group w-100" role="group">' +
                    '<label class="btn btn-success absensi-btn-mobile active">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Hadir" autocomplete="off" checked>' +
                    '<i class="fas fa-check-circle"></i> Hadir' +
                    '</label>' +
                    '<label class="btn btn-warning absensi-btn-mobile">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Izin" autocomplete="off">' +
                    '<i class="fas fa-info-circle"></i> Izin' +
                    '</label>' +
                    '<label class="btn btn-info absensi-btn-mobile">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Sakit" autocomplete="off">' +
                    '<i class="fas fa-thermometer-half"></i> Sakit' +
                    '</label>' +
                    '<label class="btn btn-danger absensi-btn-mobile">' +
                    '<input type="radio" name="kehadiran[' + santri.IdSantri + ']" value="Alfa" autocomplete="off">' +
                    '<i class="fas fa-times-circle"></i> Alfa' +
                    '</label>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group keterangan-field" id="keterangan-field-' + santri.IdSantri + '" style="display: none;">' +
                    '<label for="keterangan-' + santri.IdSantri + '"><i class="fas fa-comment-alt"></i> Keterangan:</label>' +
                    '<input type="text" name="keterangan[' + santri.IdSantri + ']" id="keterangan-' + santri.IdSantri + '" ' +
                    'class="form-control form-control-lg" placeholder="Masukkan keterangan (opsional)">' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            });

            console.log('[DEBUG] HTML generated, length:', html.length);
            console.log('[DEBUG] Updating container HTML...');

            container.html(html);

            console.log('[DEBUG] Container updated. New content length:', container.html().length);
            console.log('[DEBUG] Container children count:', container.children().length);
            console.log('[DEBUG] Container is visible:', container.is(':visible'));

            // Re-initialize event handlers untuk radio buttons dan photo popup
            console.log('[DEBUG] Re-initializing event handlers...');
            ensureAllHadirSelected();
            updateActiveState();
            toggleKeteranganField();

            // Re-attach event handlers untuk photo popup
            attachPhotoPopupHandlers();

            console.log('[DEBUG] updateSantriList completed');
        }

        // Fungsi untuk attach event handlers untuk photo popup (hanya untuk elemen baru yang ditambahkan via AJAX)
        // Event handler utama sudah ada di atas menggunakan $(document).on() yang akan bekerja untuk elemen baru juga
        function attachPhotoPopupHandlers() {
            // Event handlers sudah terpasang dengan $(document).on() di atas
            // Fungsi ini hanya untuk memastikan event handlers terpasang saat elemen baru ditambahkan
            // Tidak perlu melakukan apa-apa karena $(document).on() sudah menangani elemen baru
        }

        // Handle form submission validation dengan popup konfirmasi
        $('form[id^="formAbsensi"]').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var hasError = false;

            // Check if all students have attendance status
            form.find('input[type="radio"][name^="kehadiran"]').each(function() {
                var name = $(this).attr('name');
                if (!$('input[name="' + name + '"]:checked').length) {
                    hasError = true;
                }
            });

            if (hasError) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Mohon lengkapi status kehadiran untuk semua santri!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Hitung statistik kehadiran
            var stats = {
                hadir: 0,
                izin: 0,
                sakit: 0,
                alfa: 0
            };

            form.find('input[type="radio"][name^="kehadiran"]:checked').each(function() {
                var status = $(this).val();
                if (stats.hasOwnProperty(status.toLowerCase())) {
                    stats[status.toLowerCase()]++;
                }
            });

            // Tampilkan popup konfirmasi
            var tanggal = form.find('input[name="tanggal"]').val();
            var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            var total = stats.hadir + stats.izin + stats.sakit + stats.alfa;

            // Buat HTML untuk tabel statistik
            var statsHtml = '<div class="text-left mt-3">' +
                '<table class="table table-bordered table-sm mb-0" style="font-size: 0.9rem;">' +
                '<thead class="thead-light">' +
                '<tr><th>Status</th><th class="text-center">Jumlah</th></tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr><td><i class="fas fa-check-circle text-success"></i> Hadir</td><td class="text-center"><strong>' + stats.hadir + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-info-circle text-info"></i> Izin</td><td class="text-center"><strong>' + stats.izin + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-heartbeat text-danger"></i> Sakit</td><td class="text-center"><strong>' + stats.sakit + '</strong></td></tr>' +
                '<tr><td><i class="fas fa-times-circle text-warning"></i> Alfa</td><td class="text-center"><strong>' + stats.alfa + '</strong></td></tr>' +
                '<tr class="table-primary"><td><strong>Total</strong></td><td class="text-center"><strong>' + total + '</strong></td></tr>' +
                '</tbody>' +
                '</table>' +
                '</div>';

            // Tampilkan SweetAlert2 konfirmasi
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Simpan Absensi',
                html: '<p class="mb-2"><strong>Tanggal:</strong> ' + tanggalFormatted + '</p>' + statsHtml + '<p class="text-muted mt-3 mb-0">Apakah Anda yakin ingin menyimpan data absensi ini?</p>',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> OK, Simpan',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Submit form secara asinkron
                    var formData = form.serialize();
                    var formAction = form.attr('action');

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu, data sedang disimpan',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: formAction,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Data absensi berhasil disimpan!',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload halaman dengan parameter tanggal untuk tetap menampilkan tab
                                    var tanggal = form.find('input[name="tanggal"]').val();
                                    window.location.href = '<?= base_url("backend/absensi") ?>?tanggal=' + tanggal;
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
                            console.error('Error:', error);
                            var errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                            if (xhr.responseText) {
                                try {
                                    var errorResponse = JSON.parse(xhr.responseText);
                                    errorMessage = errorResponse.message || errorMessage;
                                } catch (e) {
                                    errorMessage += ' ' + xhr.responseText.substring(0, 100);
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>