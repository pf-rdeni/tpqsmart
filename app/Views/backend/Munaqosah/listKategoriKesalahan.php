<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kategori Kesalahan Munaqosah</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddKesalahan">
                                <i class="fas fa-plus"></i> Tambah Kategori Kesalahan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tableKesalahan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Kategori Kesalahan</th>
                                        <th>Nama Kategori Kesalahan</th>
                                        <th>Kategori Materi</th>
                                        <th>Nilai Min</th>
                                        <th>Nilai Max</th>
                                        <th>Status</th>
                                        <th>Updated At</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan dimuat via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Add Kategori Kesalahan -->
<div class="modal fade" id="modalAddKesalahan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKesalahan">
                <div class="modal-body">
                    <!-- Group Info (Shared Fields) -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Informasi Group (Shared)</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="addIdKategoriMateri">Kategori Materi <span class="text-danger">*</span></label>
                                <select class="form-control" id="addIdKategoriMateri" name="IdKategoriMateri" required>
                                    <option value="">Pilih Kategori Materi</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="addNilaiMin">Nilai Minimum <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="addNilaiMin" name="NilaiMin" min="40" max="99" required>
                                        <small class="form-text text-muted">Nilai minimum harus antara 40-99</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="addNilaiMax">Nilai Maximum <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="addNilaiMax" name="NilaiMax" min="40" max="99" required>
                                        <small class="form-text text-muted">Nilai maximum harus antara 40-99</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Kategori Kesalahan -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Daftar Kategori Kesalahan</h6>
                            <button type="button" class="btn btn-sm btn-light" id="btnAddNewItemAdd">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="addItemsList">
                                <!-- Items akan dimuat via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori Kesalahan -->
<div class="modal fade" id="modalEditKesalahan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKesalahan">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editIdKategoriKesalahan">ID Kategori Kesalahan</label>
                        <input type="text" class="form-control" id="editIdKategoriKesalahan" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editIdKategoriMateri">Kategori Materi <span class="text-danger">*</span></label>
                        <select class="form-control" id="editIdKategoriMateri" name="IdKategoriMateri" required>
                            <option value="">Pilih Kategori Materi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editNamaKategoriKesalahan">Nama Kategori Kesalahan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNamaKategoriKesalahan" name="NamaKategoriKesalahan" required>
                    </div>
                    <div class="form-group">
                        <label for="editNilaiMin">Nilai Minimum <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editNilaiMin" name="NilaiMin" min="40" max="99" required>
                        <small class="form-text text-muted">Nilai minimum harus antara 40-99</small>
                    </div>
                    <div class="form-group">
                        <label for="editNilaiMax">Nilai Maximum <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editNilaiMax" name="NilaiMax" min="40" max="99" required>
                        <small class="form-text text-muted">Nilai maximum harus antara 40-99</small>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="editStatus" name="Status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Group Kategori Kesalahan -->
<div class="modal fade" id="modalEditGroup" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Group Kategori Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditGroup">
                <div class="modal-body">
                    <!-- Group Info (Shared Fields) -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Informasi Group (Shared)</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="editGroupIdKategoriMateri">Kategori Materi <span class="text-danger">*</span></label>
                                <select class="form-control" id="editGroupIdKategoriMateri" name="IdKategoriMateri" required>
                                    <option value="">Pilih Kategori Materi</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editGroupNilaiMin">Nilai Minimum <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="editGroupNilaiMin" name="NilaiMin" min="40" max="99" required>
                                        <small class="form-text text-muted">Nilai minimum harus antara 40-99</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editGroupNilaiMax">Nilai Maximum <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="editGroupNilaiMax" name="NilaiMax" min="40" max="99" required>
                                        <small class="form-text text-muted">Nilai maximum harus antara 40-99</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Kategori Kesalahan dalam Group -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Daftar Kategori Kesalahan</h6>
                            <button type="button" class="btn btn-sm btn-light" id="btnAddNewItem">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="groupItemsList">
                                <!-- Items akan dimuat via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Variabel global untuk menyimpan grouped data
    var globalGroupedData = {};

    $(document).ready(function() {
        // Load kategori materi for dropdown
        loadKategoriMateri();

        // Load data on page load
        loadData();

        // Reset form saat modal add ditutup
        $('#modalAddKesalahan').on('hidden.bs.modal', function() {
            $('#formAddKesalahan')[0].reset();
            $('#addItemsList').html('');
        });

        // Auto-add item pertama saat modal add dibuka
        $('#modalAddKesalahan').on('show.bs.modal', function() {
            // Tambah item pertama otomatis jika belum ada
            if ($('#addItemsList .item-row').length === 0) {
                addNewItemToAddModal();
            }
        });

        // Add New Item Button (Modal Add)
        $('#btnAddNewItemAdd').on('click', function() {
            addNewItemToAddModal();
        });

        // Function untuk menambah item baru di modal add
        function addNewItemToAddModal() {
            // Get next ID dari server
            $.ajax({
                url: '<?= base_url('backend/munaqosah/getNextIdKategoriKesalahan') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var nextId = response.nextId;
                        var itemCount = $('#addItemsList .item-row').length + 1;
                        var newItemHtml = '<div class="item-row mb-3 p-3 border rounded border-success" data-item-id="new">';
                        newItemHtml += '<div class="d-flex justify-content-between align-items-start mb-2">';
                        newItemHtml += '<h6 class="mb-0 text-success">Item #' + itemCount + '</h6>';
                        newItemHtml += '<button type="button" class="btn btn-sm btn-danger remove-item-add-btn" data-item-id="new">';
                        newItemHtml += '<i class="fas fa-trash"></i> Hapus';
                        newItemHtml += '</button>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="row">';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>ID Kategori Kesalahan</label>';
                        newItemHtml += '<input type="text" class="form-control item-id-kesalahan" value="' + nextId + '" readonly style="background-color: #e9ecef;">';
                        newItemHtml += '<small class="form-text text-muted">ID otomatis di-generate</small>';
                        newItemHtml += '<input type="hidden" class="item-id" value="new">';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>Nama Kategori Kesalahan <span class="text-danger">*</span></label>';
                        newItemHtml += '<input type="text" class="form-control item-nama" required>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="row">';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>Status <span class="text-danger">*</span></label>';
                        newItemHtml += '<select class="form-control item-status">';
                        newItemHtml += '<option value="Aktif" selected>Aktif</option>';
                        newItemHtml += '<option value="Tidak Aktif">Tidak Aktif</option>';
                        newItemHtml += '</select>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';

                        $('#addItemsList').append(newItemHtml);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mendapatkan ID berikutnya'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mendapatkan ID: ' + error
                    });
                }
            });
        }

        // Remove Item Button (Modal Add)
        $(document).on('click', '.remove-item-add-btn', function() {
            var itemRow = $(this).closest('.item-row');
            var itemCount = $('#addItemsList .item-row').length;

            if (itemCount <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal harus ada satu item'
                });
                return;
            }

            itemRow.remove();

            // Update nomor item
            $('#addItemsList .item-row').each(function(index) {
                $(this).find('h6').text('Item #' + (index + 1));
            });
        });

        // Add Form Submit
        $('#formAddKesalahan').on('submit', function(e) {
            e.preventDefault();

            // Validasi shared fields
            var validationResult = validateNilaiForm('#formAddKesalahan');
            if (!validationResult.valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: validationResult.message
                });
                return;
            }

            // Kumpulkan data items
            var items = [];
            var hasError = false;

            $('#addItemsList .item-row').each(function() {
                var idKesalahan = $(this).find('.item-id-kesalahan').val().trim();
                var nama = $(this).find('.item-nama').val().trim();
                var status = $(this).find('.item-status').val();

                if (!nama || !status) {
                    hasError = true;
                    return false;
                }

                var itemData = {
                    id: 'new',
                    IdKategoriKesalahan: idKesalahan ? idKesalahan.toUpperCase() : '',
                    NamaKategoriKesalahan: nama,
                    Status: status
                };

                items.push(itemData);
            });

            if (hasError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Semua field harus diisi'
                });
                return;
            }

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal harus ada satu item'
                });
                return;
            }

            // Prepare data untuk submit
            var formData = {
                IdKategoriMateri: $('#addIdKategoriMateri').val(),
                NilaiMin: $('#addNilaiMin').val(),
                NilaiMax: $('#addNilaiMax').val(),
                items: items
            };

            // Submit
            $.ajax({
                url: '<?= base_url('backend/munaqosah/saveKategoriKesalahanGroup') ?>',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#modalAddKesalahan').modal('hide');
                        loadData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menyimpan data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        });

        // Edit Button Click
        $(document).on('click', '.edit-kesalahan-btn', function() {
            var id = $(this).data('id');
            var idKesalahan = $(this).data('id-kesalahan');
            var nama = $(this).data('nama');
            var idKategori = $(this).data('id-kategori');
            var nilaiMin = $(this).data('nilai-min');
            var nilaiMax = $(this).data('nilai-max');
            var status = $(this).data('status');

            $('#editId').val(id);
            $('#editIdKategoriKesalahan').val(idKesalahan);
            $('#editNamaKategoriKesalahan').val(nama);
            $('#editNilaiMin').val(nilaiMin);
            $('#editNilaiMax').val(nilaiMax);
            $('#editStatus').val(status);

            // Load and select kategori materi
            loadKategoriMateri('#editIdKategoriMateri', idKategori);

            $('#modalEditKesalahan').modal('show');
        });

        // Edit Form Submit
        $('#formEditKesalahan').on('submit', function(e) {
            e.preventDefault();

            // Validasi form
            var validationResult = validateNilaiForm('#formEditKesalahan');
            if (!validationResult.valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: validationResult.message
                });
                return;
            }

            var id = $('#editId').val();

            // Submit form
            $.ajax({
                url: '<?= base_url('backend/munaqosah/updateKategoriKesalahan/') ?>' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#modalEditKesalahan').modal('hide');
                        loadData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mengupdate data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        });

        // Reset form saat modal ditutup
        $('#modalEditGroup').on('hidden.bs.modal', function() {
            $('#formEditGroup')[0].reset();
            $('#groupItemsList').html('');
            $('#formEditGroup').removeData('group-key');
        });

        // Edit Group Button Click
        $(document).on('click', '.edit-group-btn', function() {
            var groupKey = $(this).data('group-key');
            var group = globalGroupedData[groupKey];

            if (!group || !group.items || group.items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Data group tidak ditemukan'
                });
                return;
            }

            // Set shared fields
            var firstItem = group.items[0];
            $('#editGroupIdKategoriMateri').val(firstItem.IdKategoriMateri);
            $('#editGroupNilaiMin').val(firstItem.NilaiMin);
            $('#editGroupNilaiMax').val(firstItem.NilaiMax);

            // Load kategori materi dropdown
            loadKategoriMateri('#editGroupIdKategoriMateri', firstItem.IdKategoriMateri);

            // Render items
            renderGroupItems(group.items);

            // Simpan group key untuk submit
            $('#formEditGroup').data('group-key', groupKey);

            $('#modalEditGroup').modal('show');
        });

        // Render Group Items
        function renderGroupItems(items) {
            var html = '';
            $.each(items, function(index, item) {
                html += '<div class="item-row mb-3 p-3 border rounded" data-item-id="' + item.id + '">';
                html += '<div class="d-flex justify-content-between align-items-start mb-2">';
                html += '<h6 class="mb-0">Item #' + (index + 1) + '</h6>';
                html += '<button type="button" class="btn btn-sm btn-danger remove-item-btn" data-item-id="' + item.id + '">';
                html += '<i class="fas fa-trash"></i> Hapus';
                html += '</button>';
                html += '</div>';
                html += '<div class="row">';
                html += '<div class="col-md-6">';
                html += '<div class="form-group">';
                html += '<label>ID Kategori Kesalahan</label>';
                html += '<input type="text" class="form-control item-id-kesalahan" value="' + item.IdKategoriKesalahan + '" data-original="' + item.IdKategoriKesalahan + '" readonly>';
                html += '<input type="hidden" class="item-id" value="' + item.id + '">';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-md-6">';
                html += '<div class="form-group">';
                html += '<label>Nama Kategori Kesalahan <span class="text-danger">*</span></label>';
                html += '<input type="text" class="form-control item-nama" value="' + item.NamaKategoriKesalahan + '" required>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '<div class="row">';
                html += '<div class="col-md-6">';
                html += '<div class="form-group">';
                html += '<label>Status <span class="text-danger">*</span></label>';
                html += '<select class="form-control item-status">';
                html += '<option value="Aktif"' + (item.Status == 'Aktif' ? ' selected' : '') + '>Aktif</option>';
                html += '<option value="Tidak Aktif"' + (item.Status == 'Tidak Aktif' ? ' selected' : '') + '>Tidak Aktif</option>';
                html += '</select>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#groupItemsList').html(html);
        }

        // Add New Item Button
        $('#btnAddNewItem').on('click', function() {
            // Get next ID dari server
            $.ajax({
                url: '<?= base_url('backend/munaqosah/getNextIdKategoriKesalahan') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var nextId = response.nextId;
                        var newItemHtml = '<div class="item-row mb-3 p-3 border rounded border-primary" data-item-id="new">';
                        newItemHtml += '<div class="d-flex justify-content-between align-items-start mb-2">';
                        newItemHtml += '<h6 class="mb-0 text-primary">Item Baru</h6>';
                        newItemHtml += '<button type="button" class="btn btn-sm btn-danger remove-item-btn" data-item-id="new">';
                        newItemHtml += '<i class="fas fa-trash"></i> Hapus';
                        newItemHtml += '</button>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="row">';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>ID Kategori Kesalahan</label>';
                        newItemHtml += '<input type="text" class="form-control item-id-kesalahan" value="' + nextId + '" readonly style="background-color: #e9ecef;">';
                        newItemHtml += '<small class="form-text text-muted">ID otomatis di-generate</small>';
                        newItemHtml += '<input type="hidden" class="item-id" value="new">';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>Nama Kategori Kesalahan <span class="text-danger">*</span></label>';
                        newItemHtml += '<input type="text" class="form-control item-nama" required>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '<div class="row">';
                        newItemHtml += '<div class="col-md-6">';
                        newItemHtml += '<div class="form-group">';
                        newItemHtml += '<label>Status <span class="text-danger">*</span></label>';
                        newItemHtml += '<select class="form-control item-status">';
                        newItemHtml += '<option value="Aktif" selected>Aktif</option>';
                        newItemHtml += '<option value="Tidak Aktif">Tidak Aktif</option>';
                        newItemHtml += '</select>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';
                        newItemHtml += '</div>';

                        $('#groupItemsList').append(newItemHtml);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mendapatkan ID berikutnya'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mendapatkan ID: ' + error
                    });
                }
            });
        });

        // Remove Item Button
        $(document).on('click', '.remove-item-btn', function() {
            var itemId = $(this).data('item-id');
            var itemRow = $(this).closest('.item-row');

            if (itemId === 'new') {
                itemRow.remove();
            } else {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus item ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        itemRow.remove();
                    }
                });
            }
        });

        // Edit Group Form Submit
        $('#formEditGroup').on('submit', function(e) {
            e.preventDefault();

            // Validasi shared fields
            var validationResult = validateNilaiForm('#formEditGroup');
            if (!validationResult.valid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: validationResult.message
                });
                return;
            }

            // Kumpulkan data items
            var items = [];
            var hasNewItem = false;
            var hasError = false;

            $('.item-row').each(function() {
                var itemId = $(this).find('.item-id').val();
                var idKesalahan = $(this).find('.item-id-kesalahan').val().trim();
                var nama = $(this).find('.item-nama').val().trim();
                var status = $(this).find('.item-status').val();

                // Untuk item baru, ID sudah auto-generated, jadi tidak perlu validasi
                if (itemId !== 'new' && !idKesalahan) {
                    hasError = true;
                    return false;
                }

                if (!nama || !status) {
                    hasError = true;
                    return false;
                }

                var itemData = {
                    id: itemId,
                    IdKategoriKesalahan: idKesalahan ? idKesalahan.toUpperCase() : '',
                    NamaKategoriKesalahan: nama,
                    Status: status
                };

                if (itemId === 'new') {
                    hasNewItem = true;
                }

                items.push(itemData);
            });

            if (hasError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Semua field harus diisi'
                });
                return;
            }

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal harus ada satu item'
                });
                return;
            }

            // Prepare data untuk submit
            var formData = {
                IdKategoriMateri: $('#editGroupIdKategoriMateri').val(),
                NilaiMin: $('#editGroupNilaiMin').val(),
                NilaiMax: $('#editGroupNilaiMax').val(),
                items: items
            };

            // Submit
            $.ajax({
                url: '<?= base_url('backend/munaqosah/updateKategoriKesalahanGroup') ?>',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#modalEditGroup').modal('hide');
                        loadData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menyimpan data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        });

        // Load Kategori Materi
        function loadKategoriMateri(selectId = '#addIdKategoriMateri', selectedValue = null) {
            $.ajax({
                url: '<?= base_url('backend/kategori-materi/get-kategori-materi-dropdown') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var html = '<option value="">Pilih Kategori Materi</option>';
                        $.each(response.data, function(index, item) {
                            var selected = (selectedValue && item.IdKategoriMateri == selectedValue) ? 'selected' : '';
                            html += '<option value="' + item.IdKategoriMateri + '" ' + selected + '>' + item.NamaKategoriMateri + '</option>';
                        });
                        $(selectId).html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading kategori materi:', error);
                }
            });
        }

        // Load Data
        function loadData() {
            $.ajax({
                url: '<?= base_url('backend/munaqosah/getKategoriKesalahan') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Urutkan data berdasarkan IdKategoriMateri, NilaiMin, NilaiMax, dan IdKategoriKesalahan
                        var sortedData = response.data.sort(function(a, b) {
                            // Urutkan berdasarkan IdKategoriMateri
                            if (a.IdKategoriMateri != b.IdKategoriMateri) {
                                return (a.IdKategoriMateri || '').localeCompare(b.IdKategoriMateri || '');
                            }
                            // Jika IdKategoriMateri sama, urutkan berdasarkan NilaiMin
                            if (a.NilaiMin != b.NilaiMin) {
                                return (a.NilaiMin || 0) - (b.NilaiMin || 0);
                            }
                            // Jika NilaiMin sama, urutkan berdasarkan NilaiMax
                            if (a.NilaiMax != b.NilaiMax) {
                                return (a.NilaiMax || 0) - (b.NilaiMax || 0);
                            }
                            // Jika semua sama, urutkan berdasarkan IdKategoriKesalahan
                            return (a.IdKategoriKesalahan || '').localeCompare(b.IdKategoriKesalahan || '');
                        });

                        // Kelompokkan data berdasarkan IdKategoriMateri, NilaiMin, dan NilaiMax
                        var groupedData = [];
                        var currentGroup = null;

                        $.each(sortedData, function(index, item) {
                            var groupKey = (item.IdKategoriMateri || '') + '_' + (item.NilaiMin || '') + '_' + (item.NilaiMax || '');

                            if (!currentGroup || currentGroup.key != groupKey) {
                                // Buat grup baru
                                if (currentGroup) {
                                    groupedData.push(currentGroup);
                                }
                                currentGroup = {
                                    key: groupKey,
                                    items: []
                                };
                            }

                            currentGroup.items.push(item);
                        });

                        // Tambahkan grup terakhir
                        if (currentGroup) {
                            groupedData.push(currentGroup);
                        }

                        // Simpan grouped data ke variabel global
                        globalGroupedData = {};
                        $.each(groupedData, function(index, group) {
                            globalGroupedData[group.key] = group;
                        });

                        // Render tabel dengan merge cell
                        var rowCount = 0;
                        var html = '';

                        $.each(groupedData, function(groupIndex, group) {
                            var groupSize = group.items.length;

                            $.each(group.items, function(itemIndex, item) {
                                html += '<tr>';

                                // No
                                html += '<td>' + (rowCount + 1) + '</td>';

                                // ID Kategori Kesalahan
                                html += '<td>' + item.IdKategoriKesalahan + '</td>';

                                // Nama Kategori Kesalahan
                                html += '<td>' + item.NamaKategoriKesalahan + '</td>';

                                // Kategori Materi (merge cell) - hanya pada baris pertama grup
                                if (itemIndex === 0) {
                                    html += '<td rowspan="' + groupSize + '" style="vertical-align: middle;">' + (item.NamaKategoriMateri || '-') + '</td>';
                                }

                                // Nilai Min (merge cell) - hanya pada baris pertama grup
                                if (itemIndex === 0) {
                                    html += '<td rowspan="' + groupSize + '" style="vertical-align: middle; text-align: center;">' + (item.NilaiMin || '-') + '</td>';
                                }

                                // Nilai Max (merge cell) - hanya pada baris pertama grup
                                if (itemIndex === 0) {
                                    html += '<td rowspan="' + groupSize + '" style="vertical-align: middle; text-align: center;">' + (item.NilaiMax || '-') + '</td>';
                                }

                                // Status
                                html += '<td><span class="badge badge-' + (item.Status == 'Aktif' ? 'success' : 'danger') + '">' + item.Status + '</span></td>';

                                // Updated At
                                html += '<td>' + item.updated_at + '</td>';

                                // Aksi
                                html += '<td>';
                                html += '<div class="btn-group" role="group">';
                                // Tombol Edit Group hanya pada baris pertama
                                if (itemIndex === 0) {
                                    html += '<button type="button" class="btn btn-info btn-sm edit-group-btn" ';
                                    html += 'data-group-key="' + group.key + '" ';
                                    html += 'title="Edit Group">';
                                    html += '<i class="fas fa-layer-group"></i>';
                                    html += '</button>';
                                }
                                html += '<button type="button" class="btn btn-warning btn-sm edit-kesalahan-btn" ';
                                html += 'data-id="' + item.id + '" ';
                                html += 'data-id-kesalahan="' + item.IdKategoriKesalahan + '" ';
                                html += 'data-nama="' + item.NamaKategoriKesalahan + '" ';
                                html += 'data-id-kategori="' + item.IdKategoriMateri + '" ';
                                html += 'data-nilai-min="' + (item.NilaiMin || '') + '" ';
                                html += 'data-nilai-max="' + (item.NilaiMax || '') + '" ';
                                html += 'data-status="' + item.Status + '">';
                                html += '<i class="fas fa-edit"></i>';
                                html += '</button>';
                                html += '<button type="button" class="btn btn-danger btn-sm" onclick="deleteKesalahan(' + item.id + ')">';
                                html += '<i class="fas fa-trash"></i>';
                                html += '</button>';
                                html += '</div>';
                                html += '</td>';
                                html += '</tr>';

                                rowCount++;
                            });
                        });

                        $('#tableKesalahan tbody').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                }
            });
        }

        // Validasi Nilai Min dan Max
        function validateNilaiForm(formId) {
            var nilaiMin = parseInt($(formId + ' input[name="NilaiMin"]').val());
            var nilaiMax = parseInt($(formId + ' input[name="NilaiMax"]').val());

            // Validasi nilai min >= 40
            if (nilaiMin < 40) {
                return {
                    valid: false,
                    message: 'Nilai minimum harus minimal 40'
                };
            }

            // Validasi nilai max <= 99
            if (nilaiMax > 99) {
                return {
                    valid: false,
                    message: 'Nilai maximum harus maksimal 99'
                };
            }

            // Validasi nilai min tidak boleh sama dengan max
            if (nilaiMin == nilaiMax) {
                return {
                    valid: false,
                    message: 'Nilai minimum dan maximum tidak boleh sama'
                };
            }

            // Validasi nilai min harus lebih kecil dari max
            if (nilaiMin >= nilaiMax) {
                return {
                    valid: false,
                    message: 'Nilai minimum harus lebih kecil dari nilai maximum'
                };
            }

            return {
                valid: true,
                message: ''
            };
        }

        // Delete Kategori Kesalahan
        window.deleteKesalahan = function(id) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/deleteKategoriKesalahan/') ?>' + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                loadData();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Gagal menghapus data'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan: ' + error
                            });
                        }
                    });
                }
            });
        };
    });
</script>
<?= $this->endSection() ?>