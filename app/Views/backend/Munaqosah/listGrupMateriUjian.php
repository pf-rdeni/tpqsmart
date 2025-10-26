<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Grup Materi Ujian</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddGrupMateri">
                                    <i class="fas fa-plus"></i> Tambah Grup Materi
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableGrupMateri" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Grup Materi</th>
                                            <th>Nama Materi Grup</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($grupMateri as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['IdGrupMateriUjian'] ?></td>
                                            <td><?= $row['NamaMateriGrup'] ?></td>
                                            <td>
                                                <span class="badge badge-<?= $row['Status'] == 'Aktif' ? 'success' : 'danger' ?>">
                                                    <?= $row['Status'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-warning btn-sm edit-grup-materi-btn" 
                                                            data-id="<?= $row['id'] ?>"
                                                            data-id-grup="<?= htmlspecialchars($row['IdGrupMateriUjian'], ENT_QUOTES) ?>"
                                                            data-nama-grup="<?= htmlspecialchars($row['NamaMateriGrup'], ENT_QUOTES) ?>"
                                                            data-status="<?= htmlspecialchars($row['Status'], ENT_QUOTES) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deleteGrupMateri(<?= $row['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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

<!-- Modal Add Grup Materi -->
<div class="modal fade" id="modalAddGrupMateri" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Grup Materi Ujian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddGrupMateri">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addIdGrupMateriUjian">ID Grup Materi Ujian <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="addIdGrupMateriUjian" name="IdGrupMateriUjian" required readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="btnGenerateId">
                                    <i class="fas fa-sync"></i> Generate
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">ID akan di-generate otomatis</small>
                    </div>
                    <div class="form-group">
                        <label for="addNamaMateriGrup">Nama Materi Grup <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNamaMateriGrup" name="NamaMateriGrup" required style="text-transform: uppercase;">
                        <small class="form-text text-muted">Nama akan dikonversi ke huruf kapital</small>
                    </div>
                    <div class="form-group">
                        <label for="addStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="addStatus" name="Status" required>
                            <option value="">Pilih Status</option>
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

<!-- Modal Edit Grup Materi -->
<div class="modal fade" id="modalEditGrupMateri" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Grup Materi Ujian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditGrupMateri">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editIdGrupMateriUjian">ID Grup Materi Ujian</label>
                        <input type="text" class="form-control" id="editIdGrupMateriUjian" name="IdGrupMateriUjian" readonly style="background-color: #f8f9fa;">
                        <small class="form-text text-muted">ID Grup Materi tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editNamaMateriGrup">Nama Materi Grup <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNamaMateriGrup" name="NamaMateriGrup" required style="text-transform: uppercase;">
                        <small class="form-text text-muted">Nama akan dikonversi ke huruf kapital</small>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="editStatus" name="Status" required>
                            <option value="">Pilih Status</option>
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
    // Initialize DataTable
    $('#tableGrupMateri').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[ 0, "asc" ]]
    });

    // Auto-generate ID when modal opens
    $('#modalAddGrupMateri').on('show.bs.modal', function() {
        generateNextId();
    });

    // Generate ID button click
    $('#btnGenerateId').on('click', function() {
        generateNextId();
    });

    // Function to generate next ID
    function generateNextId() {
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-next-id-grup-materi') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addIdGrupMateriUjian').val(response.next_id);
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal generate ID',
                    icon: 'error'
                });
            }
        });
    }

    // Auto uppercase for nama materi grup fields
    $('#addNamaMateriGrup, #editNamaMateriGrup').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Disable ID field saat edit
    $('#editIdGrupMateriUjian').on('focus', function() {
        this.blur();
    });

    // Add Grup Materi form
    $('#formAddGrupMateri').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/save-grup-materi-ujian') ?>',
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
                } else if (response.duplicate_name) {
                    // Tampilkan informasi detail untuk nama duplikat
                    Swal.fire({
                        title: 'Nama Grup Materi Sudah Ada!',
                        html: '<div style="text-align: left;">' +
                              '<p><strong>Nama grup materi yang Anda masukkan sudah digunakan.</strong></p>' +
                              '<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">' +
                              '<p><strong>Nama yang sudah ada:</strong> ' + response.existing_name + '</p>' +
                              '<p><strong>Saran:</strong> ' + response.suggestion + '</p>' +
                              '</div>' +
                              '<p style="color: #6c757d; font-size: 14px;">' +
                              'Sistem tidak membedakan huruf besar dan kecil. Silakan gunakan nama yang berbeda.' +
                              '</p>' +
                              '</div>',
                        icon: 'warning',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#ffc107'
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

    // Edit Grup Materi button click
    $(document).on('click', '.edit-grup-materi-btn', function() {
        var id = $(this).data('id');
        var idGrup = $(this).data('id-grup');
        var namaGrup = $(this).data('nama-grup');
        var status = $(this).data('status');
        
        $('#editId').val(id);
        $('#editIdGrupMateriUjian').val(idGrup);
        $('#editNamaMateriGrup').val(namaGrup);
        $('#editStatus').val(status);
        $('#modalEditGrupMateri').modal('show');
    });

    // Edit Grup Materi form
    $('#formEditGrupMateri').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#editId').val();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/update-grup-materi-ujian/') ?>' + id,
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
                } else if (response.duplicate_name) {
                    // Tampilkan informasi detail untuk nama duplikat
                    Swal.fire({
                        title: 'Nama Grup Materi Sudah Ada!',
                        html: '<div style="text-align: left;">' +
                              '<p><strong>Nama grup materi yang Anda masukkan sudah digunakan.</strong></p>' +
                              '<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">' +
                              '<p><strong>Nama yang sudah ada:</strong> ' + response.existing_name + '</p>' +
                              '<p><strong>Saran:</strong> ' + response.suggestion + '</p>' +
                              '</div>' +
                              '<p style="color: #6c757d; font-size: 14px;">' +
                              'Sistem tidak membedakan huruf besar dan kecil. Silakan gunakan nama yang berbeda.' +
                              '</p>' +
                              '</div>',
                        icon: 'warning',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#ffc107'
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

function deleteGrupMateri(id) {
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
                url: '<?= base_url('backend/munaqosah/delete-grup-materi-ujian/') ?>' + id,
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
                        var grupInfo = response.grup_info || {};
                        
                        Swal.fire({
                            title: 'Tidak Dapat Dihapus!',
                            html: '<div style="text-align: left;">' +
                                  '<p><strong>Grup materi tidak dapat dihapus karena sudah digunakan dalam sistem.</strong></p>' +
                                  '<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">' +
                                  '<p><strong>ID Grup Materi:</strong> ' + grupInfo.IdGrupMateriUjian + '</p>' +
                                  '<p><strong>Nama Grup:</strong> ' + grupInfo.NamaMateriGrup + '</p>' +
                                  '<p><strong>Digunakan di:</strong> ' + usageCount + ' data materi munaqosah</p>' +
                                  '</div>' +
                                  '<p style="color: #6c757d; font-size: 14px;">' +
                                  'Untuk menghapus grup materi ini, Anda harus menghapus terlebih dahulu semua data materi yang menggunakan grup ini.' +
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