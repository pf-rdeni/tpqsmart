<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Manajemen Permission
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createPermissionModal">
                            <i class="fas fa-plus"></i> Tambah Permission
                        </button>
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="permissionsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permissions as $permission): ?>
                                    <tr>
                                        <td><?= esc($permission['id']) ?></td>
                                        <td><?= esc($permission['name']) ?></td>
                                        <td><?= esc($permission['description'] ?? '-') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-permission" data-permission-id="<?= esc($permission['id']) ?>" data-name="<?= esc($permission['name']) ?>" data-description="<?= esc($permission['description'] ?? '') ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-permission" data-permission-id="<?= esc($permission['id']) ?>" data-name="<?= esc($permission['name']) ?>">
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
</div>

<!-- Modal Create Permission -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Permission Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="createPermissionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Permission *</label>
                        <input type="text" class="form-control" name="name" required placeholder="contoh: users.create, posts.edit">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
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

<!-- Modal Edit Permission -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editPermissionForm">
                <div class="modal-body">
                    <input type="hidden" id="editPermissionId" name="permission_id">
                    <div class="form-group">
                        <label>Nama Permission *</label>
                        <input type="text" class="form-control" id="editPermissionName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" id="editPermissionDescription" name="description" rows="3"></textarea>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#permissionsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });

    // Create permission
    $('#createPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/createPermission') ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Gagal menambahkan permission');
            }
        });
    });

    // Edit permission
    $('.edit-permission').on('click', function() {
        const permissionId = $(this).data('permission-id');
        const name = $(this).data('name');
        const description = $(this).data('description');
        
        $('#editPermissionId').val(permissionId);
        $('#editPermissionName').val(name);
        $('#editPermissionDescription').val(description);
        $('#editPermissionModal').modal('show');
    });

    $('#editPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/updatePermission') ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Gagal mengupdate permission');
            }
        });
    });

    // Delete permission
    $('.delete-permission').on('click', function() {
        const permissionId = $(this).data('permission-id');
        const permissionName = $(this).data('name');
        
        if (confirm('Apakah Anda yakin ingin menghapus permission "' + permissionName + '"?')) {
            $.ajax({
                url: '<?= base_url('backend/auth/deletePermission') ?>/' + permissionId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Gagal menghapus permission');
                }
            });
        }
    });
});
</script>
<?= $this->endSection(); ?>

