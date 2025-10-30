<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kategori Materi Munaqosah</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddKategori">
                                <i class="fas fa-plus"></i> Tambah Kategori Materi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tableKategori" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Kategori Materi</th>
                                        <th>Nama Kategori Materi</th>
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

<!-- Modal Add Kategori Materi -->
<div class="modal fade" id="modalAddKategori" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Materi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKategori">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addIdKategoriMateri">ID Kategori Materi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addIdKategoriMateri" name="IdKategoriMateri" required style="text-transform: uppercase;">
                        <small class="form-text text-muted">ID akan dikonversi ke huruf kapital</small>
                    </div>
                    <div class="form-group">
                        <label for="addNamaKategoriMateri">Nama Kategori Materi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNamaKategoriMateri" name="NamaKategoriMateri" required>
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

<!-- Modal Edit Kategori Materi -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori Materi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKategori">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editIdKategoriMateri">ID Kategori Materi</label>
                        <input type="text" class="form-control" id="editIdKategoriMateri" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNamaKategoriMateri">Nama Kategori Materi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNamaKategoriMateri" name="NamaKategoriMateri" required>
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
    // Load data on page load
    loadData();

    // Add Form Submit
    $('#formAddKategori').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/saveKategoriMateri') ?>',
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
                    $('#modalAddKategori').modal('hide');
                    $('#formAddKategori')[0].reset();
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
    $(document).on('click', '.edit-kategori-btn', function() {
        var id = $(this).data('id');
        var idKategori = $(this).data('id-kategori');
        var nama = $(this).data('nama');
        var status = $(this).data('status');

        $('#editId').val(id);
        $('#editIdKategoriMateri').val(idKategori);
        $('#editNamaKategoriMateri').val(nama);
        $('#editStatus').val(status);

        $('#modalEditKategori').modal('show');
    });

    // Edit Form Submit
    $('#formEditKategori').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#editId').val();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/updateKategoriMateri/') ?>' + id,
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
                    $('#modalEditKategori').modal('hide');
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

    // Load Data
    function loadData() {
        $.ajax({
            url: '<?= base_url('backend/munaqosah/getKategoriMateri') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '';
                    $.each(response.data, function(index, item) {
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + item.IdKategoriMateri + '</td>';
                        html += '<td>' + item.NamaKategoriMateri + '</td>';
                        html += '<td><span class="badge badge-' + (item.Status == 'Aktif' ? 'success' : 'danger') + '">' + item.Status + '</span></td>';
                        html += '<td>' + item.created_at + '</td>';
                        html += '<td>';
                        html += '<div class="btn-group" role="group">';
                        html += '<button type="button" class="btn btn-warning btn-sm edit-kategori-btn" ';
                        html += 'data-id="' + item.id + '" ';
                        html += 'data-id-kategori="' + item.IdKategoriMateri + '" ';
                        html += 'data-nama="' + item.NamaKategoriMateri + '" ';
                        html += 'data-status="' + item.Status + '">';
                        html += '<i class="fas fa-edit"></i>';
                        html += '</button>';
                        html += '<button type="button" class="btn btn-danger btn-sm" onclick="deleteKategori(' + item.id + ')">';
                        html += '<i class="fas fa-trash"></i>';
                        html += '</button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#tableKategori tbody').html(html);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading data:', error);
            }
        });
    }

    // Delete Kategori
    window.deleteKategori = function(id) {
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
                    url: '<?= base_url('backend/munaqosah/deleteKategoriMateri/') ?>' + id,
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


