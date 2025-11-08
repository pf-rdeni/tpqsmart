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
                                                            <th width="12%">Status Registrasi</th>
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
                                            <i class="fas fa-save"></i> Proses Registrasi Munaqosah
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

    /* Status icon styling */
    .badge i {
        margin-right: 5px;
    }

    .badge.badge-light.border-success {
        background-color: #d4edda !important;
        border-color: #c3e6cb !important;
        color: #155724 !important;
    }

    .badge.badge-light.border-danger {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
        color: #721c24 !important;
    }

    /* Print button styling */
    .print-single-btn {
        padding: 0.25rem 0.5rem;
        margin-left: 8px;
        border: none;
        background: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .print-single-btn:hover:not(:disabled) {
        transform: scale(1.1);
        text-decoration: none;
    }

    .print-single-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .print-single-btn.text-success:hover:not(:disabled) {
        color: #28a745 !important;
    }

    .print-single-btn.text-danger:hover:not(:disabled) {
        color: #dc3545 !important;
    }

    /* Copy button styling */
    .copy-link-btn {
        padding: 0.25rem 0.5rem;
        margin-left: 8px;
        border: none;
        background: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        color: #17a2b8;
    }

    .copy-link-btn:hover:not(:disabled) {
        transform: scale(1.1);
        text-decoration: none;
        color: #138496 !important;
    }

    .copy-link-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .copy-link-btn.text-info:hover:not(:disabled) {
        color: #138496 !important;
    }
</style>
<script>
    $(document).ready(function() {
        let selectedSantri = [];
        let santriDataTable = null; // Variable untuk menyimpan instance DataTable

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

            // Destroy DataTable sebelum mengosongkan tbody
            destroyDataTable();

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

        // Print single santri button click
        $(document).on('click', '.print-single-btn:not(:disabled)', function() {
            const santriId = $(this).data('santri-id');
            const santriName = $(this).data('santri-name');

            if (!santriId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'ID Santri tidak ditemukan',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Konfirmasi print kartu ujian untuk satu santri
            const typeUjian = $('#typeUjian').val();
            const typeUjianText = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';

            Swal.fire({
                title: 'Print Kartu Ujian',
                html: `
                    <div class="text-left">
                        <p><strong>Nama Santri:</strong> ${santriName}</p>
                        <p><strong>ID Santri:</strong> ${santriId}</p>
                        <p><strong>Type Ujian:</strong> ${typeUjianText}</p>
                        <p><strong>Keterangan:</strong> Kartu ujian akan dicetak untuk santri ini</p>
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
                    printSingleKartuUjian(santriId, santriName);
                }
            });
        });

        // Copy link button click
        $(document).on('click', '.copy-link-btn:not(:disabled)', function() {
            const santriId = $(this).data('santri-id');
            const santriName = $(this).data('santri-name');
            const noPeserta = $(this).data('no-peserta');
            const hasKey = $(this).data('haskey');

            if (!hasKey) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'HasKey tidak ditemukan untuk santri ini',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Format teks yang akan dicopy
            // URL menggunakan base_url untuk fleksibilitas, format: /munaqosah/cek-status/#haskey
            const baseUrl = '<?= base_url('munaqosah/cek-status/') ?>';
            const statusUrl = baseUrl + hasKey;
            const copyText = `${noPeserta}-${santriName}\nCheck Status:\n${statusUrl}`;

            // Tampilkan popup dengan informasi yang akan dicopy
            Swal.fire({
                title: 'Copy Link Status Ujian',
                html: `
                    <div class="text-left">
                        <p><strong>Nama Santri:</strong> ${santriName}</p>
                        <p><strong>No Peserta:</strong> ${noPeserta}</p>
                        <p><strong>Konten yang akan dicopy:</strong></p>
                        <div class="border p-3 bg-light rounded mt-2" style="font-family: monospace; font-size: 0.9rem; white-space: pre-wrap; word-break: break-all;">
${copyText}
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-copy"></i> Copy ke Clipboard',
                cancelButtonText: 'Batal',
                footer: 'Klik "Copy ke Clipboard" untuk menyalin konten'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Copy ke clipboard
                    copyToClipboard(copyText, santriName);
                }
            });
        });

        // Fungsi untuk copy ke clipboard
        function copyToClipboard(text, santriName) {
            // Coba menggunakan Clipboard API modern
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Link untuk ${santriName} telah disalin ke clipboard`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }).catch(function(err) {
                    console.error('Error copying to clipboard:', err);
                    // Fallback ke method lama
                    fallbackCopyToClipboard(text, santriName);
                });
            } else {
                // Fallback untuk browser yang tidak support Clipboard API
                fallbackCopyToClipboard(text, santriName);
            }
        }

        // Fallback method untuk copy ke clipboard
        function fallbackCopyToClipboard(text, santriName) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Link untuk ${santriName} telah disalin ke clipboard`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    throw new Error('Copy command failed');
                }
            } catch (err) {
                console.error('Error copying to clipboard:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: `
                        <div class="text-left">
                            <p>Gagal menyalin ke clipboard. Silakan copy manual:</p>
                            <div class="border p-2 bg-light rounded mt-2" style="font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-all;">
${text}
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            } finally {
                document.body.removeChild(textArea);
            }
        }


        function loadSantriData() {
            const filterTpq = $('#filterTpq').val();
            const filterKelas = $('#filterKelas').val();
            const typeUjian = $('#typeUjian').val();

            // Destroy DataTable sebelum memuat data baru
            destroyDataTable();

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

        // Fungsi untuk destroy DataTable jika sudah ada instance
        function destroyDataTable() {
            if (santriDataTable !== null) {
                try {
                    santriDataTable.destroy();
                    santriDataTable = null;
                    console.log('DataTable destroyed');
                } catch (error) {
                    console.warn('Error destroying DataTable:', error);
                    santriDataTable = null;
                }
            }
        }

        // Fungsi untuk inisialisasi DataTable
        function initializeSantriDataTable() {
            // Destroy DataTable lama jika ada
            destroyDataTable();

            // Inisialisasi DataTable baru
            try {
                santriDataTable = $('#santriTable').DataTable({
                    "lengthChange": true,
                    "responsive": true,
                    "autoWidth": false,
                    "paging": true,
                    "pageLength": 20,
                    "lengthMenu": [
                        [10, 20, 30, 50, 100, -1],
                        [10, 20, 30, 50, 100, "Semua"]
                    ],
                    "language": {
                        "search": "Pencarian:",
                        "paginate": {
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        },
                        "lengthMenu": "Tampilkan _MENU_ entri",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "infoFiltered": "(disaring dari _MAX_ total entri)",
                        "zeroRecords": "Tidak ada data yang ditemukan"
                    },
                    "order": [
                        [3, 'asc']
                    ], // Urutkan berdasarkan nama santri (kolom ke-4, index 3)
                    "columnDefs": [{
                            "targets": [0], // Kolom checkbox
                            "orderable": false,
                            "searchable": false
                        },
                        {
                            "targets": [6], // Kolom status
                            "orderable": false
                        }
                    ],
                    "drawCallback": function(settings) {
                        // Setelah DataTable di-redraw, pastikan checkbox yang sudah dipilih tetap ter-check
                        $('.santri-checkbox').each(function() {
                            const santriId = $(this).val().toString();
                            if (selectedSantri.includes(santriId)) {
                                $(this).prop('checked', true);
                            }
                        });

                        // Update checkbox select all sesuai dengan state
                        const visibleCheckboxes = $('.santri-checkbox:not(:disabled):visible');
                        const visibleCheckedCheckboxes = $('.santri-checkbox:checked:not(:disabled):visible');

                        if (visibleCheckboxes.length > 0 && visibleCheckedCheckboxes.length === visibleCheckboxes.length) {
                            $('#selectAll').prop('checked', true);
                        } else {
                            $('#selectAll').prop('checked', false);
                        }
                    }
                });
                console.log('DataTable initialized successfully');
            } catch (error) {
                console.error('Error initializing DataTable:', error);
            }
        }

        function populateSantriTable(santriData) {
            const tbody = $('#santriTableBody');

            // Destroy DataTable sebelum mengosongkan tbody
            destroyDataTable();

            tbody.empty();

            // Cek apakah data adalah array
            if (!Array.isArray(santriData)) {
                console.error('Invalid data format:', santriData);
                tbody.html('<tr><td colspan="7" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Format data tidak valid</td></tr>');
                // Inisialisasi DataTable meskipun ada error untuk tetap menampilkan pesan error dengan fitur DataTable
                setTimeout(() => {
                    initializeSantriDataTable();
                }, 100);
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

                // Inisialisasi DataTable meskipun tidak ada data
                setTimeout(() => {
                    initializeSantriDataTable();
                }, 100);
                return;
            }

            santriData.forEach(function(santri) {
                // Cek status berdasarkan type ujian yang dipilih
                let isPeserta, statusClass, disabledAttr, printIconColor, printDisabled;
                let hasKey = santri.HasKey || null;
                let noPeserta = santri.NoPesertaMunaqosah || '-';

                // Cek apakah santri ini sudah dipilih sebelumnya
                const isChecked = selectedSantri.includes(santri.IdSantri.toString());
                const checkedAttr = isChecked ? 'checked' : '';

                if (typeUjian === 'pra-munaqosah') {
                    isPeserta = santri.isPesertaPraMunaqosah ? `<i class="fas fa-check-circle text-success"></i> Sudah Register` : `<i class="fas fa-times-circle text-danger"></i> Belum Register`;
                    statusClass = santri.isPesertaPraMunaqosah ? 'badge-light border border-success' : 'badge-light border border-danger';
                    disabledAttr = santri.isPesertaPraMunaqosah ? 'disabled' : '';
                    printIconColor = santri.isPesertaPraMunaqosah ? 'text-success' : 'text-danger';
                    printDisabled = santri.isPesertaPraMunaqosah ? '' : 'disabled';
                } else {
                    isPeserta = santri.isPeserta ? `<i class="fas fa-check-circle text-success"></i> Sudah Register` : `<i class="fas fa-times-circle text-danger"></i> Belum Register`;
                    statusClass = santri.isPeserta ? 'badge-light border border-success' : 'badge-light border border-danger';
                    disabledAttr = santri.isPeserta ? 'disabled' : '';
                    printIconColor = santri.isPeserta ? 'text-success' : 'text-danger';
                    printDisabled = santri.isPeserta ? '' : 'disabled';
                }

                // Button copy hanya muncul jika sudah register dan memiliki HasKey
                let copyButton = '';
                if ((typeUjian === 'pra-munaqosah' && santri.isPesertaPraMunaqosah && hasKey) ||
                    (typeUjian === 'munaqosah' && santri.isPeserta && hasKey)) {
                    copyButton = `
                        <button type="button" class="btn btn-sm btn-link copy-link-btn text-info" 
                                data-santri-id="${santri.IdSantri}" 
                                data-santri-name="${santri.NamaSantri}"
                                data-no-peserta="${noPeserta}"
                                data-haskey="${hasKey}"
                                title="Copy link WhatsApp untuk ${santri.NamaSantri}">
                            <i class="fas fa-copy"></i>
                        </button>
                    `;
                }

                const row = `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input santri-checkbox" 
                               value="${santri.IdSantri}" ${disabledAttr} ${checkedAttr}>
                    </td>
                    <td class="text-center">${noPeserta}</td>
                    <td>${santri.IdSantri}</td>
                    <td>${santri.NamaSantri}</td>
                    <td>${santri.NamaKelas}</td>
                    <td>${santri.NamaTpq}</td>
                    <td class="text-center">
                        <span class="badge ${statusClass}">${isPeserta}</span>
                        <button type="button" class="btn btn-sm btn-link print-single-btn ${printIconColor}" 
                                data-santri-id="${santri.IdSantri}" 
                                data-santri-name="${santri.NamaSantri}"
                                ${printDisabled}
                                title="Print kartu ujian untuk ${santri.NamaSantri}">
                            <i class="fas fa-print"></i>
                        </button>
                        ${copyButton}
                    </td>
                </tr>
            `;
                tbody.append(row);
            });

            // Inisialisasi DataTable setelah data dimuat
            setTimeout(() => {
                initializeSantriDataTable();

                // Update button status setelah DataTable diinisialisasi
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

                // Cek apakah status menunjukkan "Sudah Register" (untuk Munaqosah atau Pra-Munaqosah)
                if (statusText.includes('Sudah Register')) {
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
                        <p><strong>Keterangan:</strong> Data akan disimpan ke tabel registrasi munaqosah dengan type ujian <strong>${typeUjianText}</strong></p>
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
            // Validasi bahwa ada santri dengan status "Sudah Register"
            if (!checkValidPrintData()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Tidak ada santri dengan status "Sudah Register" untuk dicetak kartu ujiannya',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Konfirmasi print kartu ujian
            const typeUjian = $('#typeUjian').val();
            const typeUjianText = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';

            // Hitung jumlah santri yang valid untuk print (semua santri dengan status "Sudah Register")
            let validCount = 0;
            let validSantriNames = [];
            $('#santriTableBody tr').each(function() {
                const statusBadge = $(this).find('td:last-child .badge');
                const statusText = statusBadge.text().trim();
                const santriName = $(this).find('td:nth-child(4)').text().trim();

                if (statusText.includes('Sudah Register')) {
                    validCount++;
                    validSantriNames.push(santriName);
                }
            });

            Swal.fire({
                title: 'Konfirmasi Print Kartu Ujian',
                html: `
                    <div class="text-left">
                        <p><strong>Type Ujian:</strong> ${typeUjianText}</p>
                        <p><strong>Jumlah Santri:</strong> ${validCount} santri dengan status "Sudah Register"</p>
                        <p><strong>Keterangan:</strong> Semua santri dengan status "Sudah Register" akan dicetak kartu ujiannya</p>
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

            // Ambil semua santri yang memiliki status "Sudah Register"
            const validSantriIds = [];
            $('#santriTableBody tr').each(function() {
                const statusBadge = $(this).find('td:last-child .badge');
                const statusText = statusBadge.text().trim();
                const santriId = $(this).find('td:nth-child(3)').text().trim();

                if (statusText.includes('Sudah Register')) {
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

        function printSingleKartuUjian(santriId, santriName) {
            // Show loading
            Swal.fire({
                title: 'Menyiapkan Kartu Ujian...',
                text: `Sedang memproses kartu ujian untuk ${santriName}`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare data for print single santri
            const printData = {
                santri_ids: [santriId], // Array dengan satu ID santri
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
                text: `Kartu ujian untuk ${santriName} telah dibuka dalam tab baru`,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        function processRegistrasi() {
            // Show loading with SweetAlert2
            Swal.fire({
                title: 'Memproses Data...',
                text: 'Sedang menyimpan data ke tabel registrasi munaqosah',
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

            // Reset filter ke default
            $('#filterTpq').val('0');
            $('#filterKelas').val('0');
            if (isAdmin) {
                $('#typeUjian').val('munaqosah');
            }

            // Destroy DataTable dan reload data
            destroyDataTable();
            $('#santriTableBody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
            loadSantriData();

            updateProcessButton();
        }
    });
</script>
<?= $this->endSection() ?>