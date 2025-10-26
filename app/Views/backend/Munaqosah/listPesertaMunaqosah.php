<?= $this->extend('backend/template/template'); ?>

<?php helper('nilai'); ?>

<?= $this->section('content') ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">

                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#modalAddPeserta"><i class="fas fa-edit"></i>Tambah Peserta Munaqosah</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tabelPesertaMunaqosah" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Santri</th>
                        <th>Nama Santri</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Ayah</th>
                        <th>TPQ</th>
                        <th>Alamat</th>
                        <th>Tahun Ajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($peserta) && count($peserta) > 0): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($peserta as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row->IdSantri ?? '-' ?></td>
                                <td><?= $row->NamaSantri ?? '-' ?></td>
                                <td><?= $row->TempatLahirSantri ?? '-' ?></td>
                                <td><?= $row->TanggalLahirSantri ? formatTanggalIndonesia($row->TanggalLahirSantri, 'd F Y') : '-' ?></td>
                                <td><?= $row->JenisKelamin ?? '-' ?></td>
                                <td><?= $row->NamaAyah ?? '-' ?></td>
                                <td><?= $row->NamaTpq ?? '-' ?></td>
                                <td><?= $row->KelurahanDesa ?? '-' ?></td>
                                <td><?= $row->IdTahunAjaran ?? '-' ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm mr-1"
                                        onclick="editPeserta(<?= $row->IdSantri ?>, '<?= $row->NamaSantri ?? 'Tidak Diketahui' ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deletePeserta(<?= $row->IdSantri ?>, '<?= $row->NamaSantri ?? 'Tidak Diketahui' ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data peserta</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Peserta -->
<div class="modal fade" id="modalAddPeserta" tabindex="-1" role="dialog" aria-labelledby="modalAddPesertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAddPesertaLabel">
                    <i class="fas fa-user-plus"></i> Tambah Peserta Munaqosah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddPeserta">
                <div class="modal-body">
                    <!-- Step 1: Filter -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-filter"></i> Langkah 1: Pilih Filter</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IdTpq">TPQ</label>
                                        <select class="form-control select2" id="IdTpq" name="IdTpq">
                                            <?php if (count($dataTpq) > 1): ?>
                                                <option value="">Semua TPQ</option>
                                                <?php foreach ($dataTpq as $tpq): ?>
                                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?></option>
                                                <?php endforeach; ?>
                                            <?php elseif (count($dataTpq) == 1): ?>
                                                <?php foreach ($dataTpq as $tpq): ?>
                                                    <option value="<?= $tpq['IdTpq'] ?>" selected><?= $tpq['NamaTpq'] ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">Tidak ada data TPQ</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IdKelas">Kelas</label>
                                        <select class="form-control select2" id="IdKelas" name="IdKelas">
                                            <option value="">Semua Kelas</option>
                                            <?php foreach ($dataKelas as $kelas): ?>
                                                <?php if ($kelas['NamaKelas'] !== 'ALUMNI' && $kelas['NamaKelas'] !== 'NA'): ?>
                                                    <option value="<?= $kelas['IdKelas'] ?>"><?= $kelas['NamaKelas'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-info" id="btnLoadSantri">
                                    <i class="fas fa-search"></i> Tampilkan Data Santri
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pilih Santri -->
                    <div class="card mb-3" id="cardPilihSantri" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Langkah 2: Pilih Santri</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tahun Ajaran</label>
                                <input type="text" class="form-control" id="IdTahunAjaran" name="IdTahunAjaran" value="<?= $tahunAjaran ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Pilih Santri <span class="text-danger">*</span></label>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" data-overlayscrollbars>
                                    <table class="table table-sm table-hover mb-0" id="tabelSantri">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </div>
                                                </th>
                                                <th>ID</th>
                                                <th>Nama Santri</th>
                                                <th>Kelas</th>
                                                <th>TPQ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodySantri">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle"></i> Klik "Tampilkan Data Santri" untuk memuat data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Konfirmasi -->
                    <div class="card" id="cardKonfirmasi" style="display: none;">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Langkah 3: Konfirmasi</h6>
                        </div>
                        <div class="card-body">
                            <div id="selectedSantriList">
                                <!-- Daftar santri yang dipilih akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="btnSimpan" disabled>
                        <i class="fas fa-save"></i> Simpan Peserta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit Peserta -->
<div class="modal fade" id="modalEditPeserta" tabindex="-1" role="dialog" aria-labelledby="modalEditPesertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPesertaLabel">Edit Data Peserta Munaqosah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPeserta">
                <div class="modal-body">
                    <input type="hidden" id="editIdSantri" name="IdSantri">

                    <!-- Data Santri Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-edit"></i> Data Santri
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editIdSantriDisplay">ID Santri</label>
                                        <input type="text" class="form-control" id="editIdSantriDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editNamaSantri">Nama Santri <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaSantri" name="NamaSantri" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editTempatLahirSantri">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editTempatLahirSantri" name="TempatLahirSantri" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editTanggalLahirSantri">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="editTanggalLahirSantri" name="TanggalLahirSantri" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editJenisKelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editJenisKelamin" name="JenisKelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editNamaAyah">Nama Ayah <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaAyah" name="NamaAyah" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <p class="text-muted"><i class="fas fa-info-circle"></i> Perubahan data ini akan disimpan di data utama santri.</p>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="editConfirmSave" required>
                                <label class="form-check-label" for="editConfirmSave">Saya mengerti dan menyetujui perubahan ini.</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Keluarga Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-id-card"></i> Informasi Kartu Keluarga
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Santri</label>
                                                <div id="kkSantriInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Ayah</label>
                                                <div id="kkAyahInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Ibu</label>
                                                <div id="kkIbuInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Wali</label>
                                                <div id="kkWaliInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary" disabled>
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Custom styling untuk Select2 agar konsisten dengan form-control */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-container--bootstrap4 .select2-selection--single:hover {
        border-color: #80bdff;
    }

    .select2-container--bootstrap4 .select2-selection--single:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #495057;
        padding: 0;
        line-height: 1.5;
        height: auto;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
        right: 0.75rem;
        top: 0;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        height: calc(2.25rem + 2px);
        right: 2rem;
        top: 0;
        width: 16px !important;
        height: 16px !important;
        line-height: 16px !important;
        text-align: center !important;
        background: #dc3545 !important;
        color: white !important;
        border-radius: 50% !important;
        font-size: 12px !important;
        font-weight: bold !important;
        z-index: 10 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
        background: #c82333 !important;
    }

    /* Pastikan clear button tidak tertutup scrollbar */
    .select2-container--bootstrap4 .select2-selection--single {
        position: relative !important;
        overflow: visible !important;
    }

    /* Perbaikan untuk dropdown yang memiliki clear button */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding-right: 50px !important;
    }

    /* Tambahan styling untuk memastikan clear button terlihat jelas */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear::before {
        content: "×" !important;
        font-size: 16px !important;
        font-weight: bold !important;
        line-height: 1 !important;
    }

    /* Pastikan clear button tidak tertutup elemen lain */
    .select2-container--bootstrap4 .select2-selection--single {
        z-index: 1 !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        z-index: 2 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    /* Hover effect untuk clear button */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
        transform: translateY(-50%) scale(1.1) !important;
        transition: all 0.2s ease !important;
    }

    /* Dropdown styling */
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .select2-container--bootstrap4 .select2-results__option {
        padding: 0.375rem 0.75rem;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: #fff;
    }

    /* Modal z-index fix */
    .select2-container {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }

    /* Konsistensi tinggi untuk semua form control */
    .form-control {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Spacing yang konsisten */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    /* Styling untuk checkbox di tabel */
    .table-responsive {
        position: relative;
    }

    .table-responsive .table {
        margin-bottom: 0;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 0.5rem 0.75rem;
    }

    /* Styling khusus untuk kolom checkbox */
    .table th:first-child,
    .table td:first-child {
        width: 40px;
        text-align: center;
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }

    /* Checkbox styling yang konsisten */
    .form-check-input {
        margin: 0;
        transform: scale(1.2);
        position: relative;
        top: 0;
        left: 0;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Pastikan checkbox di header dan body sejajar */
    .table thead th:first-child {
        text-align: center;
        vertical-align: middle;
        padding: 0.75rem 0.25rem;
    }

    .table tbody td:first-child {
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }

    /* Override untuk checkbox di header */
    .table thead th:first-child .form-check-input {
        margin: 0;
        display: inline-block;
    }

    /* Override untuk checkbox di body */
    .table tbody td:first-child .form-check-input {
        margin: 0;
        display: inline-block;
    }

    /* Flexbox container untuk alignment yang sempurna */
    .table th:first-child .d-flex,
    .table td:first-child .d-flex {
        height: 100%;
        min-height: 20px;
    }

    /* Pastikan checkbox terpusat sempurna */
    .table th:first-child,
    .table td:first-child {
        position: relative;
    }

    .table th:first-child .d-flex,
    .table td:first-child .d-flex {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
    }

    /* Sticky header untuk tabel */
    .table-responsive .thead-light th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Hover effect untuk row */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Custom scrollbar untuk overlayScrollbars */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* OverlayScrollbars compatibility */
    .os-scrollbar {
        z-index: 1;
    }

    .os-scrollbar-handle {
        background: #c1c1c1 !important;
        border-radius: 4px !important;
    }

    .os-scrollbar-handle:hover {
        background: #a8a8a8 !important;
    }
</style>
<script>
    $(document).ready(function() {
        console.log('Document ready - initializing...');

        // Inisialisasi DataTables dengan error handling
        if ($('#tabelPesertaMunaqosah').length > 0) {
            try {
                var table = $('#tabelPesertaMunaqosah').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "dom": 'Bfrtip',
                    "language": {
                        "emptyTable": "Tidak ada data peserta munaqosah",
                        "zeroRecords": "Tidak ada data yang cocok"
                    },
                    // munculkan menu untuk export data ke pdf,excel,print
                    "buttons": [{
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'btn btn-danger btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Exclude action column
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            className: 'btn btn-success btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Exclude action column
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            className: 'btn btn-info btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Exclude action column
                            }
                        }
                    ]
                });

                // Pastikan button container ditambahkan ke wrapper
                table.buttons().container().appendTo('#tabelPesertaMunaqosah_wrapper .col-md-6:eq(0)');
                console.log('DataTables initialized successfully');
            } catch (error) {
                console.error('DataTables initialization error:', error);
                // Fallback: hide table if DataTables fails
                $('#tabelPesertaMunaqosah').hide();
            }
        } else {
            console.log('Table element not found, skipping DataTables initialization');
        }

        // Inisialisasi Select2 untuk dropdown
        $('.select2').select2({
            placeholder: 'Pilih opsi...',
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4',
            language: 'id',
            dropdownParent: $('#modalAddPeserta')
        });
        console.log('Select2 initialized');

        // Event handler untuk tombol Load Santri
        $('#btnLoadSantri').on('click', function() {
            var idTpq = $('#IdTpq').val() || 0;
            var idKelas = $('#IdKelas').val() || 0;

            // Validasi minimal satu filter dipilih (bukan "semua")
            if (idTpq == 0 && idKelas == 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu filter (TPQ atau Kelas)',
                    icon: 'warning'
                });
                return;
            }

            loadSantriData(idKelas, idTpq);
        });

        // Event handler untuk Select All
        $('#selectAll').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.santri-checkbox').prop('checked', isChecked);
            updateSelectedSantri();
        });

        // Event handler untuk checkbox individual
        $(document).on('change', '.santri-checkbox', function() {
            updateSelectedSantri();
        });

        // Test apakah elemen ada
        console.log('IdKelas element exists:', $('#IdKelas').length > 0);
        console.log('IdTpq element exists:', $('#IdTpq').length > 0);

        // Re-initialize Select2 saat modal dibuka untuk styling yang konsisten
        $('#modalAddPeserta').on('shown.bs.modal', function() {
            $('.select2').select2('destroy');
            $('.select2').select2({
                placeholder: 'Pilih opsi...',
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                language: 'id',
                dropdownParent: $('#modalAddPeserta')
            });

            // Initialize overlayScrollbars untuk tabel
            if (typeof OverlayScrollbars !== 'undefined') {
                $('[data-overlayscrollbars]').overlayScrollbars({
                    scrollbars: {
                        autoHide: 'leave',
                        autoHideDelay: 200
                    },
                    overflow: {
                        x: 'hidden',
                        y: 'scroll'
                    }
                });
            }
        });

        // Destroy Select2 saat modal ditutup
        $('#modalAddPeserta').on('hidden.bs.modal', function() {
            $('.select2').select2('destroy');

            // Destroy overlayScrollbars
            if (typeof OverlayScrollbars !== 'undefined') {
                $('[data-overlayscrollbars]').overlayScrollbars().destroy();
            }
        });

        // Form Add Peserta
        $('#formAddPeserta').on('submit', function(e) {
            e.preventDefault();

            var selectedSantri = [];
            $('.santri-checkbox:checked').each(function() {
                selectedSantri.push($(this).val());
            });

            if (selectedSantri.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu santri',
                    icon: 'warning'
                });
                return;
            }

            var tahunAjaran = $('#IdTahunAjaran').val();

            // Kumpulkan IdTpq dari santri yang dipilih
            var selectedTpq = [];
            $('.santri-checkbox:checked').each(function() {
                var idTpq = $(this).data('idtpq');
                if (idTpq && selectedTpq.indexOf(idTpq) === -1) {
                    selectedTpq.push(idTpq);
                }
            });

            // Kirim data multiple
            var dataToSend = {
                santri_ids: selectedSantri,
                IdTahunAjaran: tahunAjaran,
                IdTpq: selectedTpq
            };

            // Show loading
            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Sedang menyimpan data peserta munaqosah, mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/save-peserta-multiple') ?>',
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                timeout: 60000, // 60 detik timeout untuk save operation
                success: function(response) {
                    // Close loading
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Tampilkan detail errors jika ada
                        var errorMessage = response.message;
                        var detailedErrors = '';

                        if (response.detailed_errors && response.detailed_errors.length > 0) {
                            detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                            response.detailed_errors.forEach(function(error, index) {
                                detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                            });
                            detailedErrors += '</div>';
                        }

                        if (response.error_count) {
                            detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                        }

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage + detailedErrors,
                            icon: 'error',
                            width: '600px'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Close loading
                    Swal.close();

                    var errorMessage = 'Terjadi kesalahan pada server';
                    var errorTitle = 'Error!';

                    // Determine error message based on status
                    if (status === 'timeout') {
                        errorMessage = 'Koneksi timeout. Proses penyimpanan membutuhkan waktu yang lebih lama.';
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

                    // Coba parse response error jika ada
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.detailed_errors && response.detailed_errors.length > 0) {
                            var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                            response.detailed_errors.forEach(function(error, index) {
                                detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                            });
                            detailedErrors += '</div>';
                            errorMessage += detailedErrors;
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Jika tidak bisa parse JSON, gunakan error default
                    }

                    Swal.fire({
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
                        icon: 'error',
                        width: '600px',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    });

    // Load data santri berdasarkan kelas yang dipilih dan TPQ yang dipilih
    function loadSantriData(idKelas, idTpq) {
        console.log('loadSantriData called with:', idKelas, idTpq);

        // Show SweetAlert2 loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang mengambil data santri, mohon tunggu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Show table loading state
        $('#tbodySantri').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        $('#cardPilihSantri').show();

        // Handle parameter 0 untuk "semua"
        var urlKelas = (idKelas == 0) ? 0 : idKelas;
        var urlTpq = (idTpq == 0) ? 0 : idTpq;

        $.ajax({
            url: '<?= base_url('backend/munaqosah/getSantriData/') ?>' + urlKelas + '/' + urlTpq,
            type: 'GET',
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(data) {
                console.log('Data received:', data);

                // Close loading
                Swal.close();

                // Cek apakah response adalah error dari controller
                if (data && data.success === false) {
                    // Show error from controller
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: `
                            <div class="text-left">
                                <p><strong>Pesan Error:</strong> ${data.user_message || data.message}</p>
                                ${data.error_details ? `
                                    <details class="mt-3">
                                        <summary class="text-muted">Detail Teknis</summary>
                                        <small class="text-muted">
                                            <strong>Error:</strong> ${data.error_details.error_message}<br>
                                            <strong>Type:</strong> ${data.error_details.error_type}<br>
                                            <strong>File:</strong> ${data.error_details.file}<br>
                                            <strong>Line:</strong> ${data.error_details.line}
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
                            loadSantriData(idKelas, idTpq);
                        }
                    });
                    return;
                }

                // Cek apakah data adalah array
                if (!Array.isArray(data)) {
                    console.error('Invalid data format:', data);
                    $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Format data tidak valid</td></tr>');
                    return;
                }

                if (data.length > 0) {
                    var html = '';
                    $.each(data, function(index, item) {
                        html += '<tr>';
                        html += '<td class="text-center">';
                        html += '<div class="d-flex justify-content-center align-items-center">';
                        html += '<input type="checkbox" class="form-check-input santri-checkbox" value="' + item.IdSantri + '" data-nama="' + item.NamaSantri + '" data-kelas="' + (item.NamaKelas || '-') + '" data-tpq="' + (item.NamaTpq || '-') + '" data-idtpq="' + (item.IdTpq || '') + '">';
                        html += '</div>';
                        html += '</td>';
                        html += '<td>' + item.IdSantri + '</td>';
                        html += '<td>' + item.NamaSantri + '</td>';
                        html += '<td>' + (item.NamaKelas || '-') + '</td>';
                        html += '<td>' + (item.NamaTpq || '-') + '</td>';
                        html += '</tr>';
                    });
                    $('#tbodySantri').html(html);

                    // Re-initialize overlayScrollbars setelah data dimuat
                    if (typeof OverlayScrollbars !== 'undefined') {
                        $('[data-overlayscrollbars]').overlayScrollbars().destroy();
                        $('[data-overlayscrollbars]').overlayScrollbars({
                            scrollbars: {
                                autoHide: 'leave',
                                autoHideDelay: 200
                            },
                            overflow: {
                                x: 'hidden',
                                y: 'scroll'
                            }
                        });
                    }

                    // Show success message if data loaded successfully
                    if (data.length > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Dimuat!',
                            text: 'Ditemukan ' + data.length + ' santri',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                } else {
                    $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle"></i> Tidak ada data santri</td></tr>');

                    // Show info message for no data
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Data',
                        text: 'Tidak ditemukan santri untuk filter yang dipilih',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);

                // Close loading
                Swal.close();

                // Show error in table
                $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error memuat data</td></tr>');

                // Determine error message based on status
                var errorMessage = 'Terjadi kesalahan saat memuat data';
                var errorTitle = 'Error!';

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

                // Show detailed error with SweetAlert2
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
                        loadSantriData(idKelas, idTpq);
                    }
                });
            }
        });
    }

    // Update daftar santri yang dipilih
    function updateSelectedSantri() {
        var selectedSantri = [];
        var selectedTpq = [];
        $('.santri-checkbox:checked').each(function() {
            var idTpq = $(this).data('idtpq');
            if (idTpq && selectedTpq.indexOf(idTpq) === -1) {
                selectedTpq.push(idTpq);
            }
            selectedSantri.push({
                id: $(this).val(),
                nama: $(this).data('nama'),
                kelas: $(this).data('kelas'),
                tpq: $(this).data('tpq'),
                idtpq: idTpq
            });
        });

        if (selectedSantri.length > 0) {
            $('#cardKonfirmasi').show();
            var html = '<div class="alert alert-info"><strong>Santri yang dipilih (' + selectedSantri.length + '):</strong></div>';
            html += '<div class="row">';

            $.each(selectedSantri, function(index, santri) {
                html += '<div class="col-md-6 mb-2">';
                html += '<div class="card card-body py-2">';
                html += '<div class="d-flex justify-content-between align-items-center">';
                html += '<div>';
                html += '<strong>' + santri.nama + '</strong><br>';
                html += '<small class="text-muted">ID: ' + santri.id + ' | Kelas: ' + santri.kelas + ' | TPQ: ' + santri.tpq + '</small>';
                html += '</div>';
                html += '<button type="button" class="btn btn-sm btn-outline-danger remove-santri" data-id="' + santri.id + '">';
                html += '<i class="fas fa-times"></i>';
                html += '</button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div>';
            $('#selectedSantriList').html(html);
            $('#btnSimpan').prop('disabled', false);
        } else {
            $('#cardKonfirmasi').hide();
            $('#btnSimpan').prop('disabled', true);
        }
    }

    // Event handler untuk remove santri
    $(document).on('click', '.remove-santri', function() {
        var id = $(this).data('id');
        $('.santri-checkbox[value="' + id + '"]').prop('checked', false);
        updateSelectedSantri();
    });

    function deletePeserta(idSantri, namaSantri) {
        // Tampilkan loading saat mengambil data terkait
        Swal.fire({
            title: 'Memeriksa Data...',
            text: 'Sedang memeriksa data terkait peserta',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Cek data terkait terlebih dahulu
        $.ajax({
            url: '<?= base_url('backend/munaqosah/check-data-terkait/') ?>' + idSantri,
            type: 'GET',
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                // Tutup loading
                Swal.close();
                if (response.success) {
                    var dataTerkait = response.data_terkait;
                    var totalTerkait = response.total_terkait;

                    if (totalTerkait > 0) {
                        // Ada data terkait, tampilkan konfirmasi detail
                        var detailMessage = 'Peserta <strong>' + namaSantri + '</strong> memiliki data terkait:<br><br>';

                        if (dataTerkait.nilai_munaqosah) {
                            detailMessage += '• <strong>' + dataTerkait.nilai_munaqosah.count + '</strong> data nilai munaqosah<br>';
                        }
                        if (dataTerkait.antrian_munaqosah) {
                            detailMessage += '• <strong>' + dataTerkait.antrian_munaqosah.count + '</strong> data antrian munaqosah<br>';
                        }

                        detailMessage += '<br><span class="text-danger"><strong>Semua data terkait akan dihapus juga!</strong></span>';
                        detailMessage += '<br><br><div class="alert alert-success"><i class="fas fa-info-circle"></i> <strong>Info:</strong> Data santri utama tidak akan dihapus, hanya dihapus dari daftar peserta munaqosah.</div>';

                        Swal.fire({
                            title: 'Konfirmasi Hapus Data Terkait',
                            html: detailMessage,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus Semua!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return performDelete(idSantri);
                            }
                        });
                    } else {
                        // Tidak ada data terkait, hapus langsung
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus peserta <strong>${namaSantri}</strong>?<br><br><span class="text-success">✓ Tidak ada data terkait yang akan terpengaruh.</span><br><br><div class="alert alert-success"><i class="fas fa-info-circle"></i> <strong>Info:</strong> Data santri utama tidak akan dihapus, hanya dihapus dari daftar peserta munaqosah.</div>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return performDelete(idSantri);
                            }
                        });
                    }
                } else {
                    // Tampilkan detail errors jika ada
                    var errorMessage = response.message;
                    var detailedErrors = '';

                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                    }

                    if (response.error_count) {
                        detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                    }

                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage + detailedErrors,
                        icon: 'error',
                        width: '600px'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Tutup loading jika ada error
                Swal.close();

                var errorMessage = 'Terjadi kesalahan saat mengecek data terkait';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat mengecek data terkait. Silakan coba lagi.';
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

                // Coba parse response error jika ada
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                        errorMessage += detailedErrors;
                    }
                } catch (e) {
                    // Jika tidak bisa parse JSON, gunakan error default
                }

                Swal.fire({
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
                    icon: 'error',
                    width: '600px',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function performDelete(idSantri) {
        return $.ajax({
            url: '<?= base_url('backend/munaqosah/delete-peserta-by-santri/') ?>' + idSantri,
            type: 'DELETE',
            dataType: 'json',
            timeout: 30000 // 30 detik timeout
        }).then(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                // Tampilkan detail errors jika ada
                var errorMessage = response.message;
                var detailedErrors = '';

                if (response.detailed_errors && response.detailed_errors.length > 0) {
                    detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                    response.detailed_errors.forEach(function(error, index) {
                        detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                    });
                    detailedErrors += '</div>';
                }

                if (response.error_count) {
                    detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                }

                Swal.fire({
                    title: 'Gagal!',
                    html: errorMessage + detailedErrors,
                    icon: 'error',
                    width: '600px'
                });
            }
        }).catch(function(xhr) {
            var errorMessage = 'Terjadi kesalahan saat menghapus data';
            var errorTitle = 'Error!';
            var status = xhr.statusText || 'Unknown';
            var httpCode = xhr.status || 0;

            // Determine error message based on status
            if (status === 'timeout') {
                errorMessage = 'Koneksi timeout saat menghapus data. Silakan coba lagi.';
                errorTitle = 'Timeout!';
            } else if (httpCode === 404) {
                errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                errorTitle = 'Not Found!';
            } else if (httpCode === 500) {
                errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                errorTitle = 'Server Error!';
            } else if (httpCode === 0) {
                errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                errorTitle = 'Connection Error!';
            }

            // Coba parse response error jika ada
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.detailed_errors && response.detailed_errors.length > 0) {
                    var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                    response.detailed_errors.forEach(function(error, index) {
                        detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                    });
                    detailedErrors += '</div>';
                    errorMessage += detailedErrors;
                }
            } catch (e) {
                // Jika tidak bisa parse JSON, gunakan error default
            }

            Swal.fire({
                title: errorTitle,
                html: `
                    <div class="text-left">
                        <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                        <p><strong>Status:</strong> ${status}</p>
                        <p><strong>HTTP Code:</strong> ${httpCode}</p>
                    </div>
                `,
                icon: 'error',
                width: '600px',
                confirmButtonText: 'OK'
            });
        });
    }

    // Fungsi untuk edit peserta
    function editPeserta(IdSantri, NamaSantri) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang mengambil data santri',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX untuk mengambil detail santri
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-detail-santri') ?>',
            type: 'POST',
            data: {
                IdSantri: IdSantri
            },
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                Swal.close();

                if (response.success) {
                    // Isi form dengan data yang diterima
                    $('#editIdSantri').val(response.data.IdSantri);
                    $('#editIdSantriDisplay').val(response.data.IdSantri);
                    $('#editNamaSantri').val(response.data.NamaSantri);
                    $('#editTempatLahirSantri').val(response.data.TempatLahirSantri);
                    $('#editTanggalLahirSantri').val(response.data.TanggalLahirSantri);
                    $('#editJenisKelamin').val(response.data.JenisKelamin);
                    $('#editNamaAyah').val(response.data.NamaAyah);

                    // Tampilkan informasi kartu keluarga
                    displayKartuKeluargaInfo(response.data);

                    // Reset checkbox dan button
                    $('#editConfirmSave').prop('checked', false);
                    $('#formEditPeserta button[type="submit"]').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');

                    // Tampilkan modal
                    $('#modalEditPeserta').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal mengambil data santri'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                var errorMessage = 'Terjadi kesalahan saat mengambil data santri';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat mengambil data santri. Silakan coba lagi.';
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
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Format tempat lahir saat mengetik
    $('#editTempatLahirSantri').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Format nama santri saat mengetik
    $('#editNamaSantri').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Format nama ayah saat mengetik
    $('#editNamaAyah').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Event handler untuk checkbox konfirmasi
    $('#editConfirmSave').on('change', function() {
        const submitButton = $('#formEditPeserta button[type="submit"]');
        if ($(this).is(':checked')) {
            submitButton.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            submitButton.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
    });

    // Fungsi untuk menampilkan informasi kartu keluarga
    function displayKartuKeluargaInfo(data) {
        // Kartu Keluarga Santri
        if (data.FileKkSantri && data.FileKkSantri.trim() !== '') {
            $('#kkSantriInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/') ?>${data.FileKkSantri}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkSantriInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Ayah
        if (data.FileKkAyah && data.FileKkAyah.trim() !== '') {
            $('#kkAyahInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/') ?>${data.FileKkAyah}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkAyahInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Ibu
        if (data.FileKkIbu && data.FileKkIbu.trim() !== '') {
            $('#kkIbuInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/') ?>${data.FileKkIbu}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkIbuInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Wali
        if (data.FileKkWali && data.FileKkWali.trim() !== '') {
            $('#kkWaliInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/') ?>${data.FileKkWali}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkWaliInfo').html('<span class="text-muted">Tidak ada file</span>');
        }
    }

    // Event handler untuk form edit
    $('#formEditPeserta').on('submit', function(e) {
        e.preventDefault();

        // Validasi checkbox konfirmasi
        if (!$('#editConfirmSave').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Diperlukan!',
                text: 'Silakan centang kotak konfirmasi untuk melanjutkan penyimpanan data.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan perubahan data',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX untuk update data
        $.ajax({
            url: '<?= base_url('backend/munaqosah/update-santri') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                Swal.close();

                if (response.success) {
                    // Cek apakah ada perubahan atau tidak
                    if (response.no_changes) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Perubahan',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Tutup modal
                            $('#modalEditPeserta').modal('hide');
                        });
                    } else {
                        // Ada perubahan, tampilkan detail perubahan dalam tabel
                        var changeMessage = response.message;
                        var changeTable = '';

                        if (response.changes) {
                            // Parse changes dari string ke array
                            var changes = response.changes.split('<br>');
                            changeTable = '<br><br><div class="table-responsive"><table class="table table-bordered table-sm">';
                            changeTable += '<thead class="thead-light"><tr><th style="width: 30%;">Field</th><th style="width: 35%;" class="text-danger">Before</th><th style="width: 35%;" class="text-success">After</th></tr></thead>';
                            changeTable += '<tbody>';

                            changes.forEach(function(change) {
                                if (change.trim()) {
                                    // Parse format: "Field: 'old' → 'new'"
                                    var match = change.match(/^(.+?):\s*'(.+?)'\s*→\s*'(.+?)'$/);
                                    if (match) {
                                        var field = match[1];
                                        var before = match[2];
                                        var after = match[3];

                                        changeTable += '<tr>';
                                        changeTable += '<td><strong>' + field + '</strong></td>';
                                        changeTable += '<td class="text-danger"><span class="badge badge-danger">' + before + '</span></td>';
                                        changeTable += '<td class="text-success"><span class="badge badge-success">' + after + '</span></td>';
                                        changeTable += '</tr>';
                                    }
                                }
                            });

                            changeTable += '</tbody></table></div>';
                        }

                        if (response.change_count) {
                            changeTable += '<div class="alert alert-info mt-2"><i class="fas fa-info-circle"></i> Total <strong>' + response.change_count + '</strong> field yang diperbarui</div>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: changeMessage + changeTable,
                            width: '700px',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Tutup modal
                            $('#modalEditPeserta').modal('hide');
                            // Reload halaman untuk update data
                            location.reload();
                        });
                    }
                } else {
                    var errorMessage = response.message || 'Gagal memperbarui data santri';
                    var detailedErrors = '';

                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage + detailedErrors,
                        width: '600px'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                var errorMessage = 'Terjadi kesalahan pada server';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat menyimpan data. Silakan coba lagi.';
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

                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                        errorMessage += detailedErrors;
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // Jika tidak bisa parse JSON, gunakan error default
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
                    width: '600px',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>



<?= $this->endSection() ?>