<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">

                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#modalAddMateri"><i class="fas fa-edit"></i>Tambah Materi Munaqosah</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tableMateri" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Materi</th>
                        <th>Nama Materi</th>
                        <th>Kategori Materi </th>
                        <th>Grup Materi Ujian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($materi as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row->IdMateri ?></td>
                            <td><?= esc($row->NamaMateri ?? '-') ?></td>
                            <td><?= esc($row->NamaKategoriMateri ?? ($row->IdKategoriMateri ?? '-')) ?></td>
                            <td>
                                <select class="form-control form-control-sm grup-materi-dropdown"
                                    data-id="<?= $row->id ?>"
                                    data-current-grup="<?= htmlspecialchars($row->IdGrupMateriUjian, ENT_QUOTES) ?>">
                                    <option value="">Pilih Grup</option>
                                    <?php foreach ($grupMateriAktif as $grup): ?>
                                        <option value="<?= $grup['IdGrupMateriUjian'] ?>"
                                            <?= ($grup['IdGrupMateriUjian'] == $row->IdGrupMateriUjian) ? 'selected' : '' ?>>
                                            <?= $grup['NamaMateriGrup'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <?php if ($row->Status == 'Aktif'): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> <?= $row->Status ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> <?= $row->Status ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteMateri(<?= $row->id ?>)">
                                    <i class="fas fa-trash"></i>
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
</div>
</section>
</div>

<!-- Modal Add Materi -->
<div class="modal fade" id="modalAddMateri" tabindex="-1" role="dialog" aria-labelledby="modalAddMateriLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddMateriLabel">Tambah Materi Munaqosah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableMateriPilihan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>ID Materi</th>
                                <th>Nama Materi</th>
                                <th>Kategori</th>
                                <th>Grup Materi Ujian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materiPelajaran as $mp): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input materi-checkbox"
                                            value="<?= esc($mp['IdMateri']) ?>"
                                            data-nama="<?= esc($mp['NamaMateri']) ?>"
                                            data-id-kategori="<?= esc($mp['IdKategoriMateri'] ?? '') ?>"
                                            data-kategori-nama="<?= esc($mp['NamaKategoriMateri'] ?? ($mp['Kategori'] ?? '-')) ?>">
                                    </td>
                                    <td><?= esc($mp['IdMateri']) ?></td>
                                    <td><?= esc($mp['NamaMateri']) ?></td>
                                    <td><?= esc($mp['NamaKategoriMateri'] ?? ($mp['Kategori'] ?? '-')) ?></td>
                                    <td>
                                        <select class="form-control form-control-sm grup-materi-select"
                                            data-materi-id="<?= $mp['IdMateri'] ?>">
                                            <option value="">Pilih Grup</option>
                                            <?php foreach ($grupMateriAktif as $grup): ?>
                                                <option value="<?= $grup['IdGrupMateriUjian'] ?>"><?= $grup['NamaMateriGrup'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="btnPreview" disabled>
                    <i class="fas fa-eye"></i> Preview
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewLabel">Preview Materi yang Dipilih</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Materi</th>
                                <th>Nama Materi</th>
                                <th>Kategori</th>
                                <th>Grup Materi Ujian</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveMateri">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Perbaikan alignment checkbox */
    .table th.text-center,
    .table td.text-center {
        text-align: center !important;
        vertical-align: middle !important;
        position: relative;
        overflow: hidden;
    }

    .table th.text-center input[type="checkbox"],
    .table td.text-center input[type="checkbox"] {
        margin: 0 auto;
        display: block;
        transform: scale(1.1);
        position: relative;
        z-index: 1;
    }

    /* Pastikan checkbox terpusat di sel dan tidak keluar */
    .table td.text-center {
        padding: 8px 4px;
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        position: relative;
        overflow: hidden;
    }

    .table th.text-center {
        padding: 8px 4px;
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        position: relative;
        overflow: hidden;
    }

    /* Pastikan checkbox tidak keluar dari container */
    #tableMateriPilihan_wrapper {
        overflow: hidden;
    }

    #tableMateriPilihan {
        table-layout: fixed;
        width: 100% !important;
    }

    #tableMateriPilihan th:first-child,
    #tableMateriPilihan td:first-child {
        width: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    /* Status badge interaktif */
    .badge {
        font-size: 0.75em;
        padding: 0.5em 0.75em;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
        border: 1px solid #1e7e34;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
        border: 1px solid #bd2130;
    }

    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .badge-success:hover {
        background-color: #218838;
        border-color: #1c7430;
    }

    .badge-danger:hover {
        background-color: #c82333;
        border-color: #a71e2a;
    }

    .badge i {
        font-size: 0.875em;
    }

    /* Animasi untuk status change */
    @keyframes statusChange {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .badge.status-changing {
        animation: statusChange 0.5s ease-in-out;
    }


    /* DataTable Length Menu */
    .dataTables_length {
        margin-bottom: 10px;
    }

    .dataTables_length select {
        margin: 0 5px;
        padding: 0.25rem 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    /* DataTable Search Box */
    .dataTables_filter {
        margin-bottom: 10px;
    }

    .dataTables_filter input {
        margin-left: 5px;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        width: 200px;
    }

    /* Modal DataTable Responsive */
    .modal-body .table-responsive {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Pastikan modal tidak overflow */
    .modal-xl {
        max-width: 95%;
    }

    .modal-xl .modal-body {
        padding: 15px;
    }

    /* Fix untuk DataTable wrapper */
    .dataTables_wrapper {
        overflow: hidden;
    }

    .dataTables_scroll {
        overflow: hidden;
    }

    /* Grup Materi Dropdown Interaktif */
    .grup-materi-dropdown {
        min-width: 150px;
        font-size: 0.875rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .grup-materi-dropdown:hover {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .grup-materi-dropdown:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .grup-materi-dropdown.updating {
        background-color: #f8f9fa;
        border-color: #ffc107;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }
</style>
<script>
    $(document).ready(function() {
        // DataTable untuk tabel utama
        $('#tableMateri').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "order": [
                [0, "asc"]
            ]
        });

        // Grup Materi Dropdown interaktif - change untuk update grup
        $(document).on('change', '.grup-materi-dropdown', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var dropdown = $(this);
            var id = dropdown.data('id');
            var currentGrup = dropdown.data('current-grup');
            var newGrup = dropdown.val();

            console.log('Grup dropdown changed - ID:', id, 'Current:', currentGrup, 'New:', newGrup);

            if (newGrup === currentGrup) {
                console.log('No change detected, skipping update');
                return;
            }

            if (!newGrup) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Grup Materi Ujian tidak boleh kosong',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false
                });
                dropdown.val(currentGrup);
                return;
            }

            // Tampilkan loading
            dropdown.addClass('updating');

            // Konfirmasi perubahan grup
            Swal.fire({
                title: 'Ubah Grup Materi?',
                text: `Apakah Anda yakin ingin mengubah grup materi ujian?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request untuk update grup
                    console.log('Sending AJAX request to:', '<?= base_url('backend/munaqosah/update-grup-materi') ?>/' + id);

                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/update-grup-materi') ?>/' + id,
                        type: 'POST',
                        data: {
                            IdGrupMateriUjian: newGrup,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            console.log('Response:', response);
                            if (response.success) {
                                // Update data attribute
                                dropdown.data('current-grup', newGrup);

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Grup materi ujian berhasil diubah',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                console.error('Error response:', response);
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Gagal mengubah grup materi ujian',
                                    icon: 'error'
                                });
                                // Revert dropdown value
                                dropdown.val(currentGrup);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan pada server: ' + error,
                                icon: 'error'
                            });
                            // Revert dropdown value
                            dropdown.val(currentGrup);
                        },
                        complete: function() {
                            dropdown.removeClass('updating');
                        }
                    });
                } else {
                    // Revert dropdown value
                    dropdown.val(currentGrup);
                    dropdown.removeClass('updating');
                }
            });
        });

        // Status badge interaktif - klik untuk toggle status
        $(document).on('click', '.badge', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var badge = $(this);
            var row = badge.closest('tr');
            var id = row.find('button[onclick*="deleteMateri"]').attr('onclick').match(/\d+/)[0];
            var currentStatus = badge.text().trim();
            var newStatus = currentStatus === 'Aktif' ? 'Tidak Aktif' : 'Aktif';

            console.log('Badge clicked - ID:', id, 'Current:', currentStatus, 'New:', newStatus); // Debug log

            // Tampilkan loading
            badge.addClass('status-changing');

            // Konfirmasi perubahan status
            Swal.fire({
                title: 'Ubah Status?',
                text: `Apakah Anda yakin ingin mengubah status menjadi "${newStatus}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request untuk update status
                    console.log('Sending AJAX request to:', '<?= base_url('backend/munaqosah/update-status-materi') ?>/' + id);
                    console.log('Data:', {
                        status: newStatus,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    });

                    // Test route first
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/test-update-status') ?>/' + id,
                        type: 'POST',
                        data: {
                            status: newStatus,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        success: function(testResponse) {
                            console.log('Test route response:', testResponse);

                            // If test works, proceed with actual update
                            $.ajax({
                                url: '<?= base_url('backend/munaqosah/update-status-materi') ?>/' + id,
                                type: 'POST',
                                data: {
                                    status: newStatus,
                                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                                },
                                success: function(response) {
                                    console.log('Response:', response); // Debug log
                                    if (response.success) {
                                        // Update badge
                                        updateStatusBadge(badge, newStatus);

                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Status berhasil diubah',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        console.error('Error response:', response); // Debug log
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message || 'Gagal mengubah status',
                                            icon: 'error'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX Error:', xhr.responseText); // Debug log
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan pada server: ' + error,
                                        icon: 'error'
                                    });
                                },
                                complete: function() {
                                    badge.removeClass('status-changing');
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Test route error:', xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Test route gagal: ' + error,
                                icon: 'error'
                            });
                            badge.removeClass('status-changing');
                        }
                    });
                } else {
                    badge.removeClass('status-changing');
                }
            });
        });

        // Function untuk update status badge
        function updateStatusBadge(badge, newStatus) {
            badge.removeClass('badge-success badge-danger');

            if (newStatus === 'Aktif') {
                badge.addClass('badge-success');
                badge.html('<i class="fas fa-check-circle"></i> ' + newStatus);
            } else {
                badge.addClass('badge-danger');
                badge.html('<i class="fas fa-times-circle"></i> ' + newStatus);
            }
        }

        // DataTable untuk tabel pilihan materi
        var tableMateriPilihan = $('#tableMateriPilihan').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 25, // Tingkatkan dari 10 ke 25 untuk menampilkan lebih banyak data
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ], // Tambahkan opsi "Semua"
            "searching": true, // Pastikan pencarian aktif
            "search": {
                "regex": false, // Nonaktifkan regex untuk pencarian yang lebih mudah
                "smart": true, // Aktifkan smart search
                "caseInsensitive": true // Pencarian tidak case sensitive
            },
            "paging": true, // Pastikan paging aktif
            "info": true, // Tampilkan info jumlah data
            "processing": false, // Client-side processing
            "deferRender": false, // Render semua data sekaligus untuk pencarian yang lebih baik
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "columnDefs": [{
                "targets": 0,
                "className": "text-center",
                "orderable": false
            }],
            "dom": 'lfrtip' // Hapus tombol export, hanya tampilkan length, filter, dan table
        });

        // Pastikan checkbox tetap terpusat setelah DataTable render
        tableMateriPilihan.on('draw', function() {
            // Fix checkbox positioning
            $('#tableMateriPilihan tbody tr').each(function() {
                var checkboxCell = $(this).find('td:first-child');
                var checkbox = checkboxCell.find('input[type="checkbox"]');

                // Pastikan checkbox dalam batas sel
                checkbox.css({
                    'margin': '0 auto',
                    'display': 'block',
                    'position': 'relative',
                    'z-index': '1',
                    'max-width': '20px',
                    'max-height': '20px'
                });

                // Pastikan sel checkbox memiliki ukuran tetap
                checkboxCell.css({
                    'width': '50px',
                    'min-width': '50px',
                    'max-width': '50px',
                    'overflow': 'hidden',
                    'position': 'relative'
                });
            });
        });

        // Event handler untuk pencarian - pastikan pencarian bekerja di semua halaman
        tableMateriPilihan.on('search.dt', function() {
            console.log('Search performed on tableMateriPilihan');
            var searchTerm = tableMateriPilihan.search();
            var filteredInfo = tableMateriPilihan.page.info();
            console.log('Search term:', searchTerm);
            console.log('Filtered info:', filteredInfo);
            // Update preview button setelah pencarian
            updatePreviewButton();
            updateSelectAll();
        });

        // Event handler untuk perubahan halaman
        tableMateriPilihan.on('page.dt', function() {
            console.log('Page changed on tableMateriPilihan');
            // Update preview button setelah perubahan halaman
            updatePreviewButton();
            updateSelectAll();
        });

        // Event handler untuk perubahan length (jumlah data per halaman)
        tableMateriPilihan.on('length.dt', function() {
            console.log('Length changed on tableMateriPilihan');
            // Update preview button setelah perubahan length
            updatePreviewButton();
            updateSelectAll();
        });

        // Fungsi untuk memastikan pencarian global bekerja
        function ensureGlobalSearch() {
            // Pastikan pencarian dilakukan di semua data, bukan hanya halaman aktif
            tableMateriPilihan.search(tableMateriPilihan.search(), false, false);
        }

        // Tambahkan event listener untuk input pencarian
        $(document).on('keyup', '.dataTables_filter input', function() {
            setTimeout(function() {
                ensureGlobalSearch();
            }, 100);
        });

        // Select All checkbox
        $('#selectAll').on('change', function() {
            var isChecked = $(this).is(':checked');

            // Update semua halaman DataTable
            tableMateriPilihan.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.materi-checkbox');
                checkbox.prop('checked', isChecked);
            });

            updatePreviewButton();
        });

        // Individual checkbox change
        $('.materi-checkbox').on('change', function() {
            updatePreviewButton();
            updateSelectAll();
        });

        // Update preview button state
        function updatePreviewButton() {
            var checkedCount = 0;
            var selectionsValid = true;
            var disableReason = '';

            // Cek semua halaman DataTable
            tableMateriPilihan.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.materi-checkbox');
                var grupSelect = $(row).find('.grup-materi-select');

                if (checkbox.is(':checked')) {
                    checkedCount++;

                    if (!grupSelect.val()) {
                        selectionsValid = false;
                        disableReason = 'Pilih grup untuk setiap materi yang dipilih.';
                    } else if (!checkbox.data('idKategori')) {
                        selectionsValid = false;
                        disableReason = 'Kategori materi belum dipetakan ke master.';
                    }
                }
            });

            var previewButton = $('#btnPreview');
            if (checkedCount > 0 && selectionsValid) {
                previewButton.prop('disabled', false).removeAttr('title');
            } else {
                previewButton.prop('disabled', true);
                if (checkedCount > 0 && disableReason) {
                    previewButton.attr('title', disableReason);
                } else {
                    previewButton.removeAttr('title');
                }
            }
        }

        // Update select all checkbox
        function updateSelectAll() {
            var totalCheckboxes = 0;
            var checkedCheckboxes = 0;

            // Hitung total dan checked dari semua halaman DataTable
            tableMateriPilihan.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.materi-checkbox');
                totalCheckboxes++;
                if (checkbox.is(':checked')) {
                    checkedCheckboxes++;
                }
            });

            if (checkedCheckboxes === 0) {
                $('#selectAll').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAll').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAll').prop('indeterminate', true);
            }
        }

        // Grup materi select change events
        $(document).on('change', '.grup-materi-select', function() {
            updatePreviewButton();
        });


        // Preview button click
        $('#btnPreview').on('click', function() {
            var selectedMateri = [];

            // Ambil semua data dari semua halaman DataTable
            tableMateriPilihan.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.materi-checkbox');
                var grupSelect = $(row).find('.grup-materi-select');

                if (checkbox.is(':checked')) {
                    selectedMateri.push({
                        id: checkbox.val(),
                        nama: checkbox.data('nama'),
                        idKategori: checkbox.data('idKategori') || '',
                        kategoriNama: checkbox.data('kategoriNama') || '-',
                        grup: grupSelect.val()
                    });
                }
            });

            // Update preview table
            var previewTableBody = $('#previewTableBody');
            previewTableBody.empty();

            selectedMateri.forEach(function(materi) {
                previewTableBody.append(
                    '<tr>' +
                    '<td>' + materi.id + '</td>' +
                    '<td>' + materi.nama + '</td>' +
                    '<td>' + materi.kategoriNama + '</td>' +
                    '<td>' + materi.grup + '</td>' +
                    '</tr>'
                );
            });

            $('#modalPreview').modal('show');
        });

        // Save materi button
        $('#btnSaveMateri').on('click', function() {
            var selectedMateri = [];
            var missingKategori = [];
            var missingGrup = [];

            // Ambil semua data dari semua halaman DataTable
            tableMateriPilihan.rows().every(function() {
                var row = this.node();
                var checkbox = $(row).find('.materi-checkbox');
                var grupSelect = $(row).find('.grup-materi-select');

                if (checkbox.is(':checked')) {
                    var idKategori = checkbox.data('idKategori');
                    var namaKategori = checkbox.data('kategoriNama');
                    var grupVal = grupSelect.val();

                    if (!idKategori) {
                        missingKategori.push(checkbox.val());
                        return;
                    }

                    if (!grupVal) {
                        missingGrup.push(checkbox.val());
                        return;
                    }

                    selectedMateri.push({
                        IdMateri: checkbox.val(),
                        IdKategoriMateri: idKategori,
                        NamaKategoriMateri: namaKategori,
                        IdGrupMateriUjian: grupVal
                    });
                }
            });

            if (selectedMateri.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Pilih minimal satu materi dan pastikan grup serta kategori valid.',
                    icon: 'warning'
                });
                return;
            }

            if (missingKategori.length > 0) {
                Swal.fire({
                    title: 'Kategori Tidak Ditemukan',
                    text: 'Beberapa materi belum memiliki mapping kategori yang valid. Periksa materi: ' + missingKategori.join(', '),
                    icon: 'warning'
                });
                return;
            }

            if (missingGrup.length > 0) {
                Swal.fire({
                    title: 'Grup Belum Dipilih',
                    text: 'Pilih grup materi untuk semua materi yang dipilih. Materi tanpa grup: ' + missingGrup.join(', '),
                    icon: 'warning'
                });
                return;
            }

            var data = {
                materi: selectedMateri
            };

            $.ajax({
                url: '<?= base_url('backend/munaqosah/save-materi-batch') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else if (response.duplicate_check) {
                        // Tampilkan konfirmasi untuk duplikasi
                        showDuplicateConfirmation(response, data);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        icon: 'error'
                    });
                }
            });
        });

        // Function untuk menampilkan konfirmasi duplikasi
        function showDuplicateConfirmation(response, originalData) {
            var duplicateList = '';
            if (response.materi_info) {
                response.materi_info.forEach(function(materi) {
                    var kategoriLabel = materi.NamaKategoriMateri || materi.KategoriAsli || materi.Kategori || '-';
                    duplicateList += '<div style="margin: 8px 0; padding: 8px; background-color: #f8f9fa; border-left: 3px solid #ffc107; border-radius: 4px;">' +
                        '<strong>' + materi.IdMateri + '</strong> - ' + materi.NamaMateri + ' (' + kategoriLabel + ')' +
                        '</div>';
                });
            }

            Swal.fire({
                title: 'Materi Sudah Ada!',
                html: '<p>Materi berikut sudah ada di sistem:</p>' +
                    '<div style="max-height: 200px; overflow-y: auto; margin: 15px 0;">' + duplicateList + '</div>' +
                    '<p>Apakah Anda ingin melanjutkan dan melewati materi yang sudah ada?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lewati dan Simpan',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim data dengan skip_duplicates = true
                    var confirmData = Object.assign({}, originalData);
                    confirmData.skip_duplicates = 'true';

                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/save-materi-batch-confirm') ?>',
                        type: 'POST',
                        data: confirmData,
                        dataType: 'json',
                        success: function(confirmResponse) {
                            if (confirmResponse.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: confirmResponse.message,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: confirmResponse.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan pada server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        // Form Edit Materi
        $('#formEditMateri').on('submit', function(e) {
            e.preventDefault();

            var id = $('#editId').val();

            $.ajax({
                url: '<?= base_url('backend/munaqosah/update-materi/') ?>' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        icon: 'error'
                    });
                }
            });
        });
    });


    function deleteMateri(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/delete-materi/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else if (response.blocked_delete) {
                            // Tampilkan informasi detail mengapa tidak bisa dihapus
                            var usageCount = response.usage_count || 0;
                            var materiInfo = response.materi_info || {};

                            Swal.fire({
                                title: 'Tidak Dapat Dihapus!',
                                html: '<div style="text-align: left;">' +
                                    '<p><strong>Materi tidak dapat dihapus karena sudah digunakan dalam sistem.</strong></p>' +
                                    '<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">' +
                                    '<p><strong>ID Materi:</strong> ' + materiInfo.IdMateri + '</p>' +
                                    '<p><strong>Grup Materi:</strong> ' + materiInfo.IdGrupMateriUjian + '</p>' +
                                    '<p><strong>Digunakan di:</strong> ' + usageCount + ' data nilai munaqosah</p>' +
                                    '</div>' +
                                    '<p style="color: #6c757d; font-size: 14px;">' +
                                    'Untuk menghapus materi ini, Anda harus menghapus terlebih dahulu semua data nilai yang menggunakan materi ini.' +
                                    '</p>' +
                                    '</div>',
                                icon: 'error',
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#6c757d'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>