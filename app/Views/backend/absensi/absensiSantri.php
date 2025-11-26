<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check"></i> Absensi Santri
            </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
            // Kelompokkan santri berdasarkan kelas
            $santriByKelas = [];
            foreach ($santri as $row) {
                $santriByKelas[$row->NamaKelas][] = $row;
            }

            // Cek apakah ada data santri untuk hari ini
            if (empty($santriByKelas)) {
                echo "<div class='alert alert-info'><i class='fas fa-info-circle'></i> Semua santri sudah diabsen pada hari ini.</div>";
            } else {
                // Loop melalui setiap kelas dan buat form terpisah
                foreach ($santriByKelas as $kelas => $santriList): ?>
                    <div class="card card-outline card-primary mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-users"></i> Kelas: <?= esc($kelas) ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('/backend/absensi/simpanAbsensi') ?>" method="post" id="formAbsensi<?= $santriList[0]->IdKelas ?>">
                                <!-- Date Input -->
                                <div class="form-group mb-3">
                                    <label for="tanggal<?= $santriList[0]->IdKelas ?>">
                                        <i class="fas fa-calendar-alt"></i> Tanggal:
                                    </label>
                                    <?php
                                    // Ambil tanggal hari ini dalam format yang sesuai untuk input type="date" (YYYY-MM-DD)
                                    $tanggalHariIni = date('Y-m-d');
                                    ?>
                                    <input type="date"
                                        name="tanggal"
                                        id="tanggal<?= $santriList[0]->IdKelas ?>"
                                        value="<?= $tanggalHariIni; ?>"
                                        class="form-control form-control-lg"
                                        style="-webkit-appearance: none; -moz-appearance: textfield; appearance: none;"
                                        required>
                                </div>

                                <!-- Quick Action: Set All Hadir -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success btn-sm" onclick="setAllHadir(<?= $santriList[0]->IdKelas ?>)">
                                        <i class="fas fa-check-double"></i> Set Semua Hadir
                                    </button>
                                    <small class="text-muted ml-2">
                                        <i class="fas fa-info-circle"></i> Default: Semua santri di-set sebagai Hadir
                                    </small>
                                </div>

                                <!-- Tambahkan hidden input untuk menyimpan IdKelas, IdGuru, IdTahunAjaran -->
                                <input type="hidden" name="IdKelas" value="<?= $santriList[0]->IdKelas ?>">
                                <input type="hidden" name="IdGuru" value="<?= session()->get('IdGuru') ?>">
                                <input type="hidden" name="IdTahunAjaran" value="<?= $santriList[0]->IdTahunAjaran ?>">

                                <!-- Desktop View: Table -->
                                <div class="d-none d-md-block">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 30%;">Nama Santri</th>
                                                    <th style="width: 40%;">Kehadiran</th>
                                                    <th style="width: 30%;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($santriList as $row): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= esc($row->NamaSantri) ?></strong>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                                                <label class="btn btn-success absensi-btn active">
                                                                    <input type="radio"
                                                                        name="kehadiran[<?= $row->IdSantri ?>]"
                                                                        value="Hadir"
                                                                        autocomplete="off"
                                                                        checked
                                                                        onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Hadir')">
                                                                    <i class="fas fa-check-circle"></i> Hadir
                                                                </label>
                                                                <label class="btn btn-warning absensi-btn">
                                                                    <input type="radio"
                                                                        name="kehadiran[<?= $row->IdSantri ?>]"
                                                                        value="Izin"
                                                                        autocomplete="off"
                                                                        onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                                                    <i class="fas fa-info-circle"></i> Izin
                                                                </label>
                                                                <label class="btn btn-info absensi-btn">
                                                                    <input type="radio"
                                                                        name="kehadiran[<?= $row->IdSantri ?>]"
                                                                        value="Sakit"
                                                                        autocomplete="off"
                                                                        onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                                                    <i class="fas fa-thermometer-half"></i> Sakit
                                                                </label>
                                                                <label class="btn btn-danger absensi-btn">
                                                                    <input type="radio"
                                                                        name="kehadiran[<?= $row->IdSantri ?>]"
                                                                        value="Alfa"
                                                                        autocomplete="off"
                                                                        onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Alfa')">
                                                                    <i class="fas fa-times-circle"></i> Alfa
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="keterangan[<?= $row->IdSantri ?>]"
                                                                id="keterangan-<?= $row->IdSantri ?>"
                                                                class="form-control"
                                                                placeholder="Masukkan keterangan (jika Izin/Sakit)"
                                                                disabled>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Mobile View: Cards -->
                                <div class="d-md-none">
                                    <?php foreach ($santriList as $row): ?>
                                        <div class="card mb-3 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">
                                                    <i class="fas fa-user"></i> <?= esc($row->NamaSantri) ?>
                                                </h5>

                                                <!-- Kehadiran Options - Mobile Friendly -->
                                                <div class="mb-3">
                                                    <label class="d-block mb-2"><strong>Status Kehadiran:</strong></label>
                                                    <div class="btn-group-vertical w-100" role="group">
                                                        <label class="btn btn-success btn-lg absensi-btn-mobile mb-2 active">
                                                            <input type="radio"
                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                value="Hadir"
                                                                autocomplete="off"
                                                                checked
                                                                onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Hadir')">
                                                            <i class="fas fa-check-circle"></i> Hadir
                                                        </label>
                                                        <label class="btn btn-warning btn-lg absensi-btn-mobile mb-2">
                                                            <input type="radio"
                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                value="Izin"
                                                                autocomplete="off"
                                                                onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                                            <i class="fas fa-info-circle"></i> Izin
                                                        </label>
                                                        <label class="btn btn-info btn-lg absensi-btn-mobile mb-2">
                                                            <input type="radio"
                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                value="Sakit"
                                                                autocomplete="off"
                                                                onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                                            <i class="fas fa-thermometer-half"></i> Sakit
                                                        </label>
                                                        <label class="btn btn-danger btn-lg absensi-btn-mobile mb-2">
                                                            <input type="radio"
                                                                name="kehadiran[<?= $row->IdSantri ?>]"
                                                                value="Alfa"
                                                                autocomplete="off"
                                                                onchange="toggleKeterangan(<?= $row->IdSantri ?>, 'Alfa')">
                                                            <i class="fas fa-times-circle"></i> Alfa
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Keterangan Field -->
                                                <div class="form-group">
                                                    <label for="keterangan-<?= $row->IdSantri ?>">
                                                        <i class="fas fa-comment-alt"></i> Keterangan:
                                                    </label>
                                                    <input type="text"
                                                        name="keterangan[<?= $row->IdSantri ?>]"
                                                        id="keterangan-<?= $row->IdSantri ?>"
                                                        class="form-control form-control-lg"
                                                        placeholder="Masukkan keterangan (jika Izin/Sakit)"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-save"></i> Simpan Absensi Kelas <?= esc($kelas) ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
            <?php endforeach;
            }
            ?>
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
    }

    .absensi-btn-mobile {
        min-height: 56px;
        font-size: 1.1rem;
        padding: 1rem 1.25rem;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .absensi-btn-mobile i {
        margin-right: 12px;
        font-size: 1.3rem;
        width: 24px;
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
    }

    /* Form control styling */
    .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    /* Button group styling */
    .btn-group-vertical .btn {
        border-radius: 0.5rem !important;
        margin-bottom: 0.5rem;
    }

    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
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
            min-height: 56px;
            padding: 1.25rem 1.5rem;
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
            font-size: 1.1rem;
            margin-bottom: 1rem;
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
    function toggleKeterangan(id, status) {
        var keteranganField = document.getElementById('keterangan-' + id);

        // Enable field jika Izin atau Sakit
        if (status === 'Izin' || status === 'Sakit') {
            keteranganField.disabled = false;
            keteranganField.required = true;
            // Focus dengan delay untuk mobile
            setTimeout(function() {
                keteranganField.focus();
            }, 100);
        } else {
            // Disable dan clear field untuk status lain
            keteranganField.disabled = true;
            keteranganField.required = false;
            keteranganField.value = '';
        }

        // Update active state for labels
        var label = $('input[name="kehadiran[' + id + ']"]:checked').closest('label');
        $('input[name="kehadiran[' + id + ']"]').closest('label').removeClass('active');
        label.addClass('active');
    }

    // Function to set all students to "Hadir" in a specific class
    function setAllHadir(idKelas) {
        var form = $('#formAbsensi' + idKelas);

        // Set all radio buttons to "Hadir"
        form.find('input[type="radio"][value="Hadir"]').each(function() {
            $(this).prop('checked', true);
            var id = $(this).attr('name').match(/\[(\d+)\]/)[1];
            toggleKeterangan(id, 'Hadir');
        });

        // Show feedback - find button by class and onclick attribute
        var btn = $('button[onclick*="setAllHadir(' + idKelas + ')"]');
        var originalText = btn.html();
        btn.html('<i class="fas fa-check"></i> Semua Di-set Hadir');

        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    }

    // Initialize: Set all default to "Hadir" and disable keterangan fields
    $(document).ready(function() {
        // Ensure all "Hadir" radio buttons are checked by default
        $('input[type="radio"][value="Hadir"]').each(function() {
            var name = $(this).attr('name');
            // Only set if no radio in this group is checked
            if (!$('input[name="' + name + '"]:checked').length) {
                $(this).prop('checked', true);
                $(this).closest('label').addClass('active');
            }
        });

        // Initialize keterangan fields based on current selection
        $('input[id^="keterangan-"]').each(function() {
            var id = $(this).attr('id').replace('keterangan-', '');
            var checkedRadio = $('input[name="kehadiran[' + id + ']"]:checked');
            if (checkedRadio.length && (checkedRadio.val() === 'Izin' || checkedRadio.val() === 'Sakit')) {
                $(this).prop('disabled', false);
                $(this).prop('required', true);
            } else {
                $(this).prop('disabled', true);
                $(this).prop('required', false);
            }
        });

        // Update active states for all labels
        $('input[type="radio"]:checked').each(function() {
            $(this).closest('label').addClass('active');
        });

        // Handle form submission validation
        $('form[id^="formAbsensi"]').on('submit', function(e) {
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
                e.preventDefault();
                alert('Mohon lengkapi status kehadiran untuk semua santri!');
                return false;
            }

            // Validate keterangan for Izin/Sakit
            form.find('input[type="radio"][name^="kehadiran"]:checked').each(function() {
                var value = $(this).val();
                var name = $(this).attr('name');
                var id = name.match(/\[(\d+)\]/)[1];
                var keteranganField = $('#keterangan-' + id);

                if ((value === 'Izin' || value === 'Sakit') && !keteranganField.val().trim()) {
                    hasError = true;
                    keteranganField.addClass('is-invalid');
                    keteranganField.focus();
                } else {
                    keteranganField.removeClass('is-invalid');
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Mohon isi keterangan untuk santri yang Izin atau Sakit!');
                return false;
            }
        });
    });
</script>
<?= $this->endSection(); ?>