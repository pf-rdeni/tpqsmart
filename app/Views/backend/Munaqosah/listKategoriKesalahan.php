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
                                        <th>Status</th>
                                        <th>Created At</th>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKesalahan">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addIdKategoriKesalahan">ID Kategori Kesalahan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addIdKategoriKesalahan" name="IdKategoriKesalahan" required style="text-transform: uppercase;">
                        <small class="form-text text-muted">ID akan dikonversi ke huruf kapital</small>
                    </div>
                    <div class="form-group">
                        <label for="addIdKategoriMateri">Kategori Materi <span class="text-danger">*</span></label>
                        <select class="form-control" id="addIdKategoriMateri" name="IdKategoriMateri" required>
                            <option value="">Pilih Kategori Materi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addNamaKategoriKesalahan">Nama Kategori Kesalahan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNamaKategoriKesalahan" name="NamaKategoriKesalahan" required>
                    </div>
                    <div class="form-group">
                        <label for="addStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="addStatus" name="Status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Load kategori materi for dropdown
    loadKategoriMateri();

    // Load data on page load
    loadData();

    // Add Form Submit
    $('#formAddKesalahan').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/saveKategoriKesalahan') ?>',
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
                    $('#modalAddKesalahan').modal('hide');
                    $('#formAddKesalahan')[0].reset();
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
        var status = $(this).data('status');

        $('#editId').val(id);
        $('#editIdKategoriKesalahan').val(idKesalahan);
        $('#editNamaKategoriKesalahan').val(nama);
        $('#editStatus').val(status);

        // Load and select kategori materi
        loadKategoriMateri('#editIdKategoriMateri', idKategori);

        $('#modalEditKesalahan').modal('show');
    });

    // Edit Form Submit
    $('#formEditKesalahan').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#editId').val();
        
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

    // Load Kategori Materi
    function loadKategoriMateri(selectId = '#addIdKategoriMateri', selectedValue = null) {
        $.ajax({
            url: '<?= base_url('backend/munaqosah/getKategoriMateriForDropdown') ?>',
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
                    var html = '';
                    $.each(response.data, function(index, item) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + item.IdKategoriKesalahan + '</td>';
                        html += '<td>' + item.NamaKategoriKesalahan + '</td>';
                        html += '<td>' + (item.NamaKategoriMateri || '-') + '</td>';
                        html += '<td><span class="badge badge-' + (item.Status == 'Aktif' ? 'success' : 'danger') + '">' + item.Status + '</span></td>';
                        html += '<td>' + item.created_at + '</td>';
                        html += '<td>';
                        html += '<div class="btn-group" role="group">';
                        html += '<button type="button" class="btn btn-warning btn-sm edit-kesalahan-btn" ';
                        html += 'data-id="' + item.id + '" ';
                        html += 'data-id-kesalahan="' + item.IdKategoriKesalahan + '" ';
                        html += 'data-nama="' + item.NamaKategoriKesalahan + '" ';
                        html += 'data-id-kategori="' + item.IdKategoriMateri + '" ';
                        html += 'data-status="' + item.Status + '">';
                        html += '<i class="fas fa-edit"></i>';
                        html += '</button>';
                        html += '<button type="button" class="btn btn-danger btn-sm" onclick="deleteKesalahan(' + item.id + ')">';
                        html += '<i class="fas fa-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#tableKesalahan tbody').html(html);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading data:', error);
            }
        });
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


