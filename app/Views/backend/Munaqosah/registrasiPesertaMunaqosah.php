<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div>
    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Proses Peserta Munaqosah ke Tabel Nilai</h3>
                        </div>
                        <div class="card-body">
                            <form id="registrasiForm" method="POST" action="<?= base_url('backend/munaqosah/processRegistrasiPeserta') ?>">
                                <div class="row">
                                    <!-- Filter TPQ -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterTpq">Filter TPQ:</label>
                                            <?php
                                            $sessionIdTpq = session()->get('IdTpq');
                                            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;
                                            ?>
                                            <select class="form-control" id="filterTpq" name="filterTpq" <?= !$isAdmin ? 'disabled' : '' ?>>
                                                <?php if ($isAdmin): ?>
                                                    <option value="0">Semua TPQ</option>
                                                <?php endif; ?>
                                                <?php foreach ($tpq as $t): ?>
                                                    <option value="<?= $t['IdTpq'] ?>" <?= (session()->get('IdTpq') == $t['IdTpq']) ? 'selected' : '' ?>>
                                                        <?= $t['NamaTpq'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (!$isAdmin): ?>
                                                <small class="form-text text-muted">Hanya TPQ Anda yang tersedia</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Filter Kelas -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterKelas">Filter Kelas:</label>
                                            <select class="form-control" id="filterKelas" name="filterKelas">
                                                <option value="0">Semua Kelas</option>
                                                <?php foreach ($kelas as $k): ?>
                                                    <option value="<?= $k['IdKelas'] ?>"><?= $k['NamaKelas'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Type Ujian -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="typeUjian">Type Ujian:</label>
                                            <?php
                                            // Cek apakah user adalah Admin (IdTpq = 0 atau null berarti Admin)
                                            $sessionIdTpq = session()->get('IdTpq');
                                            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;
                                            ?>
                                            <select class="form-control" id="typeUjian" name="typeUjian" <?= $isAdmin ? '' : 'disabled' ?>>
                                                <option value="munaqosah" <?= $isAdmin ? 'selected' : '' ?>>Munaqosah</option>
                                                <option value="pra-munaqosah" <?= !$isAdmin ? 'selected' : '' ?>>Pra-Munaqosah</option>
                                            </select>
                                            <?php if (!$isAdmin): ?>
                                                <small class="form-text text-muted">Akses terbatas ke Pra-Munaqosah untuk user TPQ</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Tahun Ajaran -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tahunAjaran">Tahun Ajaran:</label>
                                            <input type="text" class="form-control" id="tahunAjaran" name="tahunAjaran"
                                                value="<?= $tahunAjaran ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Button Print Kartu Peserta Ujian -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-info btn-block" id="printKartuBtn" disabled>
                                                <i class="fas fa-print"></i> Print Kartu Ujian
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                <!-- Daftar Santri -->
                                <div class="row" id="daftarSantriContainer">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Daftar Peserta Munaqosah:</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="santriTable">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%" class="text-center">
                                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                                            </th>
                                                            <th width="8%">No Peserta</th>
                                                            <th width="8%">ID Santri</th>
                                                            <th width="22%">Nama Santri</th>
                                                            <th width="12%">Kelas</th>
                                                            <th width="18%">TPQ</th>
                                                            <th width="12%">Status Data Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="santriTableBody">
                                                        <!-- Data akan diisi via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success" id="processBtn" disabled>
                                            <i class="fas fa-save"></i> Proses ke Tabel Nilai
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Checkbox alignment fixes */
    #santriTable th:first-child,
    #santriTable td:first-child {
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }

    #santriTable th:first-child {
        background-color: transparent !important;
        color: inherit !important;
    }

    /* Style for clear preferences button */
    #clearPreferencesBtn {
        width: 100%;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }

    #selectAll {
        margin: 0;
        transform: scale(1.1);
    }

    .santri-checkbox {
        margin: 0;
        transform: scale(1.1);
    }

    /* Ensure consistent row height */
    #santriTable tbody tr {
        height: 40px;
    }

    #santriTable tbody td {
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }
</style>
<script>
    $(document).ready(function() {
        let selectedSantri = [];

        // Check if user is admin
        const isAdmin = <?= json_encode($isAdmin ?? true) ?>;
        console.log('User is admin:', isAdmin);

        // If not admin, force Pra-Munaqosah and prevent changes
        if (!isAdmin) {
            $('#typeUjian').val('pra-munaqosah').prop('disabled', true);
            $('#filterTpq').prop('disabled', true);
            console.log('User is not admin - Type Ujian set to Pra-Munaqosah and disabled');
            console.log('User is not admin - Filter TPQ disabled');
        }

        // Clean up invalid localStorage data first
        cleanupInvalidPreferences();

        // Load saved preferences with delay to ensure DOM is ready
        console.log('Initial load - loading Type Ujian preference...');
        if (isAdmin) {
            loadTypeUjianPreference();
        } else {
            // Force Pra-Munaqosah for non-admin
            console.log('Non-admin user - skipping Type Ujian preference load');
        }

        // Load filter preferences after a short delay to ensure dropdowns are rendered
        setTimeout(() => {
            console.log('=== LOADING FILTER PREFERENCES ===');
            console.log('Current TPQ value:', $('#filterTpq').val());
            console.log('Current Kelas value:', $('#filterKelas').val());
            console.log('Current Type Ujian value:', $('#typeUjian').val());

            // Only load filter preferences for admin users
            if (isAdmin) {
                loadFilterPreferences();
                showPreferencesRestoredNotification();
            } else {
                console.log('Non-admin user - skipping filter preferences load');
            }

            // Load santri data AFTER preferences are loaded
            console.log('=== LOADING SANTRI DATA ===');
            console.log('Final TPQ value before loadSantriData:', $('#filterTpq').val());
            console.log('Final Kelas value before loadSantriData:', $('#filterKelas').val());
            console.log('Final Type Ujian value before loadSantriData:', $('#typeUjian').val());
            loadSantriData();
        }, 100);

        // Add clear preferences button functionality (only for admin users)
        if (isAdmin) {
            addClearPreferencesButton();
        }

        // Filter change events
        $('#filterTpq, #filterKelas, #typeUjian').on('change', function() {
            // Show immediate feedback that filter is changing
            const filterName = $(this).attr('id');
            const filterValue = $(this).find('option:selected').text();

            // Save preferences based on filter type (only for admin users)
            console.log('Filter changed:', filterName, 'Value:', filterValue);

            if (isAdmin) {
                if (filterName === 'typeUjian') {
                    saveTypeUjianPreference(filterValue.trim());
                } else if (filterName === 'filterTpq') {
                    saveFilterPreference('munaqosah_filter_tpq', filterValue.trim());
                } else if (filterName === 'filterKelas') {
                    saveFilterPreference('munaqosah_filter_kelas', filterValue.trim());
                }
            } else {
                console.log('Non-admin user - skipping preference save');
            }

            // Show loading in table immediately
            $('#santriTableBody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data berdasarkan filter baru...</td></tr>');

            // Load data with loading indicator
            loadSantriData();
        });


        // Select all checkbox
        $('#selectAll').on('change', function() {
            if ($(this).is(':checked')) {
                selectAllSantri();
            } else {
                clearSelection();
            }
        });

        // Individual santri selection
        $(document).on('change', '.santri-checkbox', function() {
            const santriId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                if (!selectedSantri.includes(santriId)) {
                    selectedSantri.push(santriId);
                }
            } else {
                selectedSantri = selectedSantri.filter(id => id !== santriId);
                $('#selectAll').prop('checked', false);
            }

            updateProcessButton();
        });


        function loadSantriData() {
            const filterTpq = $('#filterTpq').val();
            const filterKelas = $('#filterKelas').val();
            const typeUjian = $('#typeUjian').val();

            // Get filter values for display
            const filterTpqText = $('#filterTpq option:selected').text();
            const filterKelasText = $('#filterKelas option:selected').text();
            const typeUjianText = $('#typeUjian option:selected').text();

            // Show SweetAlert2 loading with filter info
            Swal.fire({
                title: 'Memuat Data...',
                html: `
                <div class="text-left">
                    <p>Sedang mengambil data santri berdasarkan filter:</p>
                    <ul class="list-unstyled">
                        <li><strong>TPQ:</strong> ${filterTpqText}</li>
                        <li><strong>Kelas:</strong> ${filterKelasText}</li>
                        <li><strong>Type Ujian:</strong> ${typeUjianText}</li>
                    </ul>
                </div>
            `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Show table loading state
            $('#santriTableBody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

            $.ajax({
                url: '<?= base_url('backend/munaqosah/getSantriForRegistrasi') ?>',
                type: 'GET',
                data: {
                    filterTpq: filterTpq,
                    filterKelas: filterKelas,
                    typeUjian: typeUjian
                },
                timeout: 30000, // 30 detik timeout
                success: function(response) {
                    // Cek apakah response adalah error dari controller
                    if (response && response.success === false) {
                        // Close loading
                        Swal.close();

                        // Show error from controller
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: `
                                <div class="text-left">
                                    <p><strong>Pesan Error:</strong> ${response.user_message || response.message}</p>
                                    ${response.error_details ? `
                                        <details class="mt-3">
                                            <summary class="text-muted">Detail Teknis</summary>
                                            <small class="text-muted">
                                                <strong>Error:</strong> ${response.error_details.error_message}<br>
                                                <strong>Type:</strong> ${response.error_details.error_type}<br>
                                                <strong>File:</strong> ${response.error_details.file}<br>
                                                <strong>Line:</strong> ${response.error_details.line}
                                            </small>
                                        </details>
                                    ` : ''}
                                </div>
                            `,
                            confirmButtonText: 'Coba Lagi',
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Retry loading data
                                loadSantriData();
                            }
                        });
                        return;
                    }

                    // Close loading
                    Swal.close();

                    populateSantriTable(response);

                    // Show success toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Data berhasil dimuat!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading santri data:', error);

                    // Close loading
                    Swal.close();

                    // Determine error message based on status
                    var errorMessage = 'Tidak dapat memuat data santri. Silakan coba lagi.';
                    var errorTitle = 'Gagal Memuat Data!';

                    if (status === 'timeout') {
                        errorMessage = 'Koneksi timeout. Silakan coba lagi.';
                        errorTitle = 'Timeout!';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                        errorTitle = 'Not Found!';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                        errorTitle = 'Server Error!';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        errorTitle = 'Connection Error!';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        html: `
                            <div class="text-left">
                                <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                                <p><strong>Status:</strong> ${status}</p>
                                <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                                <details class="mt-3">
                                    <summary class="text-muted">Detail Teknis</summary>
                                    <small class="text-muted">${error}</small>
                                </details>
                            </div>
                        `,
                        confirmButtonText: 'Coba Lagi',
                        showCancelButton: true,
                        cancelButtonText: 'Tutup',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Retry loading data
                            loadSantriData();
                        }
                    });
                }
            });
        }

        // Function to clean up invalid localStorage preferences
        function cleanupInvalidPreferences() {
            try {
                console.log('Cleaning up invalid preferences...');

                // Check and clean TPQ preference
                const savedTpqPreference = localStorage.getItem('munaqosah_filter_tpq');
                if (savedTpqPreference && savedTpqPreference.trim() === '') {
                    localStorage.removeItem('munaqosah_filter_tpq');
                    console.log('Removed empty TPQ preference');
                }

                // Check and clean Kelas preference
                const savedKelasPreference = localStorage.getItem('munaqosah_filter_kelas');
                if (savedKelasPreference && savedKelasPreference.trim() === '') {
                    localStorage.removeItem('munaqosah_filter_kelas');
                    console.log('Removed empty Kelas preference');
                }

                // Check and clean Type Ujian preference
                const savedTypeUjianPreference = localStorage.getItem('munaqosah_type_ujian_preference');
                if (savedTypeUjianPreference && savedTypeUjianPreference.trim() === '') {
                    localStorage.removeItem('munaqosah_type_ujian_preference');
                    console.log('Removed empty Type Ujian preference');
                }

            } catch (error) {
                console.warn('Failed to cleanup invalid preferences:', error);
            }
        }

        // Function to save Type Ujian preference to localStorage
        function saveTypeUjianPreference(typeUjianValue) {
            try {
                localStorage.setItem('munaqosah_type_ujian_preference', typeUjianValue);
                console.log('Type Ujian preference saved:', typeUjianValue);
            } catch (error) {
                console.warn('Failed to save Type Ujian preference:', error);
            }
        }

        // Function to load Type Ujian preference from localStorage
        function loadTypeUjianPreference() {
            try {
                const savedPreference = localStorage.getItem('munaqosah_type_ujian_preference');
                console.log('Loading Type Ujian preference:', savedPreference);

                if (savedPreference) {
                    // Find the option with matching text
                    const $typeUjianSelect = $('#typeUjian');
                    const $matchingOption = $typeUjianSelect.find('option').filter(function() {
                        return $(this).text().trim() === savedPreference.trim();
                    });

                    if ($matchingOption.length > 0) {
                        $typeUjianSelect.val($matchingOption.val());
                        console.log('Type Ujian preference loaded:', savedPreference, 'with value:', $matchingOption.val());
                        console.log('Type Ujian value after setting:', $typeUjianSelect.val());
                    } else {
                        console.warn('Saved Type Ujian preference not found in options:', savedPreference);
                    }
                } else {
                    console.log('No Type Ujian preference found in localStorage');
                }
            } catch (error) {
                console.warn('Failed to load Type Ujian preference:', error);
            }
        }

        // Function to save filter preference to localStorage
        function saveFilterPreference(key, value) {
            try {
                localStorage.setItem(key, value);
                console.log('Filter preference saved:', key, value);

                // Verify the save was successful
                const savedValue = localStorage.getItem(key);
                console.log('Verification - saved value:', savedValue);
            } catch (error) {
                console.warn('Failed to save filter preference:', error);
            }
        }

        // Function to load all filter preferences from localStorage
        function loadFilterPreferences() {
            try {
                console.log('Loading filter preferences...');

                // Load TPQ filter preference
                const savedTpqPreference = localStorage.getItem('munaqosah_filter_tpq');
                console.log('Saved TPQ preference:', savedTpqPreference);

                if (savedTpqPreference) {
                    const $tpqSelect = $('#filterTpq');
                    console.log('TPQ select element found:', $tpqSelect.length);
                    console.log('TPQ options count:', $tpqSelect.find('option').length);

                    // Log all TPQ options for debugging
                    $tpqSelect.find('option').each(function() {
                        console.log('TPQ option:', $(this).val(), $(this).text().trim());
                    });

                    const $matchingTpqOption = $tpqSelect.find('option').filter(function() {
                        return $(this).text().trim() === savedTpqPreference.trim();
                    });

                    console.log('Matching TPQ option found:', $matchingTpqOption.length);
                    console.log('Saved TPQ text (trimmed):', savedTpqPreference.trim());
                    console.log('Looking for exact match with trimmed text');

                    if ($matchingTpqOption.length > 0) {
                        $tpqSelect.val($matchingTpqOption.val());
                        console.log('TPQ filter preference loaded:', savedTpqPreference.trim(), 'with value:', $matchingTpqOption.val());
                        console.log('TPQ value after setting:', $tpqSelect.val());
                    } else {
                        console.warn('No matching TPQ option found for:', savedTpqPreference.trim());
                        console.log('Available TPQ options:');
                        $tpqSelect.find('option').each(function() {
                            console.log('  -', $(this).text().trim());
                        });
                    }
                }

                // Load Kelas filter preference
                const savedKelasPreference = localStorage.getItem('munaqosah_filter_kelas');
                console.log('Saved Kelas preference:', savedKelasPreference);

                if (savedKelasPreference) {
                    const $kelasSelect = $('#filterKelas');
                    console.log('Kelas select element found:', $kelasSelect.length);
                    console.log('Kelas options count:', $kelasSelect.find('option').length);

                    // Log all Kelas options for debugging
                    $kelasSelect.find('option').each(function() {
                        console.log('Kelas option:', $(this).val(), $(this).text().trim());
                    });

                    const $matchingKelasOption = $kelasSelect.find('option').filter(function() {
                        return $(this).text().trim() === savedKelasPreference.trim();
                    });

                    console.log('Matching Kelas option found:', $matchingKelasOption.length);
                    console.log('Saved Kelas text (trimmed):', savedKelasPreference.trim());
                    console.log('Looking for exact match with trimmed text');

                    if ($matchingKelasOption.length > 0) {
                        $kelasSelect.val($matchingKelasOption.val());
                        console.log('Kelas filter preference loaded:', savedKelasPreference.trim(), 'with value:', $matchingKelasOption.val());
                        console.log('Kelas value after setting:', $kelasSelect.val());
                    } else {
                        console.warn('No matching Kelas option found for:', savedKelasPreference.trim());
                        console.log('Available Kelas options:');
                        $kelasSelect.find('option').each(function() {
                            console.log('  -', $(this).text().trim());
                        });
                    }
                }
            } catch (error) {
                console.warn('Failed to load filter preferences:', error);
            }
        }

        // Function to show notification if preferences were restored
        function showPreferencesRestoredNotification() {
            try {
                const hasTypeUjianPreference = localStorage.getItem('munaqosah_type_ujian_preference');
                const hasTpqPreference = localStorage.getItem('munaqosah_filter_tpq');
                const hasKelasPreference = localStorage.getItem('munaqosah_filter_kelas');

                if (hasTypeUjianPreference || hasTpqPreference || hasKelasPreference) {
                    let restoredItems = [];

                    if (hasTypeUjianPreference) {
                        restoredItems.push(`Type Ujian: ${hasTypeUjianPreference}`);
                    }
                    if (hasTpqPreference) {
                        restoredItems.push(`TPQ: ${hasTpqPreference}`);
                    }
                    if (hasKelasPreference) {
                        restoredItems.push(`Kelas: ${hasKelasPreference}`);
                    }

                    // Show notification after a short delay to avoid conflict with other notifications
                    setTimeout(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Preferensi Dipulihkan!',
                            text: restoredItems.join(', '),
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }, 500);
                }
            } catch (error) {
                console.warn('Failed to show preferences restored notification:', error);
            }
        }

        // Function to add clear preferences button functionality
        function addClearPreferencesButton() {
            // Add clear preferences button next to the TPQ filter
            const clearButton = `
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearPreferencesBtn" title="Hapus semua preferensi yang tersimpan">
                            <i class="fas fa-eraser"></i> Hapus Pilihan
                        </button>
                    </div>
                </div>
            `;

            // Add button after the Type Ujian filter div (last filter)
            $('#typeUjian').closest('.col-md-3').after(clearButton);

            // Adjust the row to ensure proper Bootstrap grid layout
            const filterRow = $('#filterTpq').closest('.row');
            if (filterRow.length > 0) {
                // Ensure the row has proper spacing
                filterRow.addClass('mb-3');
            }

            // Add click event handler
            $('#clearPreferencesBtn').on('click', function() {
                Swal.fire({
                    title: 'Hapus Pilihan?',
                    text: 'Apakah Anda yakin ingin menghapus semua preferensi yang tersimpan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        try {
                            // Clear all preferences
                            localStorage.removeItem('munaqosah_type_ujian_preference');
                            localStorage.removeItem('munaqosah_filter_tpq');
                            localStorage.removeItem('munaqosah_filter_kelas');

                            // Reset form to default values
                            $('#typeUjian').val('munaqosah');
                            $('#filterTpq').val('0');
                            $('#filterKelas').val('0');

                            // Show success message
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Pilihan Dihapus!',
                                text: 'Semua preferensi telah dihapus dan form direset ke default',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });

                            // Reload data with default values
                            loadSantriData();

                        } catch (error) {
                            console.warn('Failed to clear preferences:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal menghapus preferensi. Silakan coba lagi.',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        }

        function populateSantriTable(santriData) {
            const tbody = $('#santriTableBody');
            tbody.empty();

            // Cek apakah data adalah array
            if (!Array.isArray(santriData)) {
                console.error('Invalid data format:', santriData);
                tbody.html('<tr><td colspan="7" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Format data tidak valid</td></tr>');
                return;
            }

            const typeUjian = $('#typeUjian').val();
            const typeUjianText = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';

            if (santriData.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-info-circle"></i> Tidak ada data santri untuk filter yang dipilih</td></tr>');

                // Show info toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Tidak ada data ditemukan!',
                    text: 'Coba ubah filter atau pilihan yang lain',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            santriData.forEach(function(santri) {
                // Cek status berdasarkan type ujian yang dipilih
                let isPeserta, statusClass, disabledAttr;

                if (typeUjian === 'pra-munaqosah') {
                    isPeserta = santri.isPesertaPraMunaqosah ? `Sudah Ada Data ${typeUjianText}` : `Belum Ada Data ${typeUjianText}`;
                    statusClass = santri.isPesertaPraMunaqosah ? 'badge-danger' : 'badge-success';
                    disabledAttr = santri.isPesertaPraMunaqosah ? 'disabled' : '';
                } else {
                    isPeserta = santri.isPeserta ? `Sudah Ada Data ${typeUjianText}` : `Belum Ada Data ${typeUjianText}`;
                    statusClass = santri.isPeserta ? 'badge-danger' : 'badge-success';
                    disabledAttr = santri.isPeserta ? 'disabled' : '';
                }

                const row = `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input santri-checkbox" 
                               value="${santri.IdSantri}" ${disabledAttr}>
                    </td>
                    <td class="text-center">${santri.NoPesertaMunaqosah || '-'}</td>
                    <td>${santri.IdSantri}</td>
                    <td>${santri.NamaSantri}</td>
                    <td>${santri.NamaKelas}</td>
                    <td>${santri.NamaTpq}</td>
                    <td><span class="badge ${statusClass}">${isPeserta}</span></td>
                </tr>
            `;
                tbody.append(row);
            });

            // Update button status setelah data dimuat dengan delay kecil
            setTimeout(() => {
                updateProcessButton();
            }, 100);
        }

        function selectAllSantri() {
            $('.santri-checkbox:not(:disabled)').each(function() {
                if (!$(this).is(':checked')) {
                    $(this).prop('checked', true);
                    const santriId = $(this).val();
                    if (!selectedSantri.includes(santriId)) {
                        selectedSantri.push(santriId);
                    }
                }
            });
            updateProcessButton();
        }

        function clearSelection() {
            $('.santri-checkbox').prop('checked', false);
            selectedSantri = [];
            previewData = [];
            updateProcessButton();
        }

        function updateProcessButton() {
            const hasSelection = selectedSantri.length > 0;
            $('#processBtn').prop('disabled', !hasSelection);

            // Button Print Kartu Ujian aktif jika ada santri dengan status "Sudah Ada Data" (tidak perlu dipilih)
            const hasValidPrintData = checkValidPrintData();
            console.log('Updating print button. Has valid print data:', hasValidPrintData);
            $('#printKartuBtn').prop('disabled', !hasValidPrintData);
            console.log('Print button disabled:', $('#printKartuBtn').prop('disabled'));
        }

        function checkValidPrintData() {
            // Cek apakah ada santri dengan status "Sudah Ada Data" di tabel (tidak perlu dipilih)
            let hasValidData = false;
            let validCount = 0;

            console.log('Checking valid print data...');
            console.log('Total rows in table:', $('#santriTableBody tr').length);

            $('#santriTableBody tr').each(function() {
                // Cari badge di kolom terakhir
                const statusBadge = $(this).find('td:last-child .badge');
                const statusText = statusBadge.text().trim();

                console.log('Row status text:', statusText);
                console.log('Badge element:', statusBadge);
                console.log('Badge length:', statusBadge.length);

                // Cek apakah status menunjukkan "Sudah Ada Data" (untuk Munaqosah atau Pra-Munaqosah)
                if (statusText.includes('Sudah Ada Data')) {
                    hasValidData = true;
                    validCount++;
                    console.log('Found valid data row');
                }
            });

            console.log('Valid data count:', validCount);
            console.log('Has valid data:', hasValidData);

            return hasValidData;
        }

        // Process button click
        $('#processBtn').click(function() {
            if (selectedSantri.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu santri untuk diproses',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Konfirmasi type ujian yang akan diproses
            const typeUjian = $('#typeUjian').val();
            const typeUjianText = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';
            const selectedCount = selectedSantri.length;

            Swal.fire({
                title: 'Konfirmasi Proses',
                html: `
                    <div class="text-left">
                        <p><strong>Type Ujian:</strong> ${typeUjianText}</p>
                        <p><strong>Jumlah Santri:</strong> ${selectedCount} santri</p>
                        <p><strong>Keterangan:</strong> Data akan disimpan ke tabel nilai dengan type ujian <strong>${typeUjianText}</strong></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Proses Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    processRegistrasi();
                }
            });
        });

        // Print Kartu Ujian button click
        $('#printKartuBtn').click(function() {
            // Validasi bahwa ada santri dengan status "Sudah Ada Data"
            if (!checkValidPrintData()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Tidak ada santri dengan status "Sudah Ada Data" untuk dicetak kartu ujiannya',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Konfirmasi print kartu ujian
            const typeUjian = $('#typeUjian').val();
            const typeUjianText = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';

            // Hitung jumlah santri yang valid untuk print (semua santri dengan status "Sudah Ada Data")
            let validCount = 0;
            let validSantriNames = [];
            $('#santriTableBody tr').each(function() {
                const statusBadge = $(this).find('td:last-child .badge');
                const statusText = statusBadge.text().trim();
                const santriName = $(this).find('td:nth-child(4)').text().trim();

                if (statusText.includes('Sudah Ada Data')) {
                    validCount++;
                    validSantriNames.push(santriName);
                }
            });

            Swal.fire({
                title: 'Konfirmasi Print Kartu Ujian',
                html: `
                    <div class="text-left">
                        <p><strong>Type Ujian:</strong> ${typeUjianText}</p>
                        <p><strong>Jumlah Santri:</strong> ${validCount} santri dengan status "Sudah Ada Data"</p>
                        <p><strong>Keterangan:</strong> Semua santri dengan status "Sudah Ada Data" akan dicetak kartu ujiannya</p>
                        ${validCount <= 5 ? `<p><strong>Daftar Santri:</strong><br>${validSantriNames.join('<br>')}</p>` : ''}
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Print Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    printKartuUjian();
                }
            });
        });

        function printKartuUjian() {
            // Show loading
            Swal.fire({
                title: 'Menyiapkan Kartu Ujian...',
                text: 'Sedang memproses data untuk print kartu ujian',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Ambil semua santri yang memiliki status "Sudah Ada Data"
            const validSantriIds = [];
            $('#santriTableBody tr').each(function() {
                const statusBadge = $(this).find('td:last-child .badge');
                const statusText = statusBadge.text().trim();
                const santriId = $(this).find('td:nth-child(3)').text().trim();

                if (statusText.includes('Sudah Ada Data')) {
                    validSantriIds.push(santriId);
                }
            });

            // Prepare data for print
            const printData = {
                santri_ids: validSantriIds,
                typeUjian: $('#typeUjian').val(),
                tahunAjaran: $('#tahunAjaran').val(),
                filterTpq: $('#filterTpq').val(),
                filterKelas: $('#filterKelas').val()
            };

            // Create form for POST request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('backend/munaqosah/printKartuUjian') ?>';
            form.target = '_blank';

            // Add form fields
            Object.keys(printData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = typeof printData[key] === 'object' ? JSON.stringify(printData[key]) : printData[key];
                form.appendChild(input);
            });

            // Add CSRF token if available
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Submit form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Close loading
            Swal.close();

            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Kartu Ujian Dibuka!',
                text: 'Kartu ujian telah dibuka dalam tab baru untuk dicetak',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        function processRegistrasi() {
            // Show loading with SweetAlert2
            Swal.fire({
                title: 'Memproses Data...',
                text: 'Sedang menyimpan data ke tabel nilai munaqosah',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/processRegistrasiPeserta') ?>',
                type: 'POST',
                data: {
                    santri_ids: JSON.stringify(selectedSantri),
                    tahunAjaran: $('#tahunAjaran').val(),
                    typeUjian: $('#typeUjian').val()
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Gagal memproses data. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }




        function resetForm() {
            selectedSantri = [];
            previewData = [];
            $('#selectAll').prop('checked', false);
            $('.santri-checkbox').prop('checked', false);
            updateProcessButton();
        }
    });
</script>
<?= $this->endSection() ?>