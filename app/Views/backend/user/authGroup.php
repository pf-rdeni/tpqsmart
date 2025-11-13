<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users-cog"></i> Pengaturan Auth Group
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                            <i class="fas fa-plus"></i> Tambah Group
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tblAuthGroup" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Nama Group</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($auth_groups as $group): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($group['id']) ?></strong></td>
                                    <td><?= esc($group['name']) ?></td>
                                    <td><?= esc($group['description'] ?? '-') ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="editGroup(<?= $group['id'] ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $group['id'] ?>, '<?= esc($group['name']) ?>')">
                                            <i class="fas fa-trash"></i> Hapus
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

<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Tambah Auth Group</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambahGroup" onsubmit="event.preventDefault(); simpanGroup();">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name_tambah">Nama Group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_tambah" name="name" required placeholder="Masukkan nama group">
                        <small class="form-text text-muted">Nama group harus unik</small>
                    </div>
                    <div class="form-group">
                        <label for="description_tambah">Deskripsi</label>
                        <textarea class="form-control" id="description_tambah" name="description" rows="3" placeholder="Masukkan deskripsi (opsional)"></textarea>
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

<!-- Modal Edit -->
<div class="modal fade" id="modal-edit" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">Edit Auth Group</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditGroup" onsubmit="event.preventDefault(); updateGroup();">
                <input type="hidden" id="id_edit" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name_edit">Nama Group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_edit" name="name" required placeholder="Masukkan nama group">
                        <small class="form-text text-muted">Nama group harus unik</small>
                    </div>
                    <div class="form-group">
                        <label for="description_edit">Deskripsi</label>
                        <textarea class="form-control" id="description_edit" name="description" rows="3" placeholder="Masukkan deskripsi (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#tblAuthGroup').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 25,
            'order': [[1, 'asc']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    });

    function simpanGroup() {
        var formData = {
            name: $('#name_tambah').val(),
            description: $('#description_tambah').val()
        };

        if (!formData.name) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nama group tidak boleh kosong'
            });
            return;
        }

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/user/createAuthGroup') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    }

    function editGroup(id) {
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/user/getAuthGroup') ?>/' + id,
            type: 'GET',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    $('#id_edit').val(response.group.id);
                    $('#name_edit').val(response.group.name);
                    $('#description_edit').val(response.group.description || '');
                    $('#modal-edit').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    }

    function updateGroup() {
        var formData = {
            id: $('#id_edit').val(),
            name: $('#name_edit').val(),
            description: $('#description_edit').val()
        };

        if (!formData.id || !formData.name) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data tidak lengkap'
            });
            return;
        }

        Swal.fire({
            title: 'Mengupdate...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/user/updateAuthGroup') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Group "' + name + '" akan dihapus. Pastikan group ini tidak digunakan oleh user manapun.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteGroup(id);
            }
        });
    }

    function deleteGroup(id) {
        Swal.fire({
            title: 'Menghapus...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/user/deleteAuthGroup') ?>/' + id,
            type: 'DELETE',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                var errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    }

    // Reset form saat modal ditutup
    $('#modal-tambah').on('hidden.bs.modal', function() {
        $('#formTambahGroup')[0].reset();
    });

    $('#modal-edit').on('hidden.bs.modal', function() {
        $('#formEditGroup')[0].reset();
    });
</script>
<?= $this->endSection(); ?>

