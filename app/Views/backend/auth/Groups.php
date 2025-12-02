<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tag"></i> Manajemen Group
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createGroupModal">
                            <i class="fas fa-plus"></i> Tambah Group
                        </button>
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="groupsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groups as $group): ?>
                                    <tr>
                                        <td><?= esc($group['id']) ?></td>
                                        <td><?= esc($group['name']) ?></td>
                                        <td><?= esc($group['description'] ?? '-') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-group" data-group-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>" data-description="<?= esc($group['description'] ?? '') ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-info edit-permissions" data-group-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>">
                                                <i class="fas fa-key"></i> Permissions
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-group" data-group-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>">
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

<!-- Modal Create Group -->
<div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Group Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="createGroupForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Group *</label>
                        <input type="text" class="form-control" name="name" required>
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

<!-- Modal Edit Group -->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Group</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editGroupForm">
                <div class="modal-body">
                    <input type="hidden" id="editGroupId" name="group_id">
                    <div class="form-group">
                        <label>Nama Group *</label>
                        <input type="text" class="form-control" id="editGroupName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" id="editGroupDescription" name="description" rows="3"></textarea>
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

<!-- Modal Edit Permissions -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permissions untuk Group: <span id="modalGroupName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editPermissionsForm">
                <div class="modal-body">
                    <input type="hidden" id="permissionGroupId" name="group_id">
                    <div class="form-group">
                        <label>Pilih Permissions:</label>
                        <div id="permissionsCheckboxes" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($permissions as $permission): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permission_ids[]" value="<?= esc($permission['id']) ?>" id="permission_<?= esc($permission['id']) ?>">
                                    <label class="form-check-label" for="permission_<?= esc($permission['id']) ?>">
                                        <?= esc($permission['name']) ?>
                                        <?php if (!empty($permission['description'])): ?>
                                            <small class="text-muted">(<?= esc($permission['description']) ?>)</small>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#groupsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });

    // Create group
    $('#createGroupForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/createGroup') ?>',
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
                alert('Gagal menambahkan group');
            }
        });
    });

    // Edit group
    $('.edit-group').on('click', function() {
        const groupId = $(this).data('group-id');
        const name = $(this).data('name');
        const description = $(this).data('description');
        
        $('#editGroupId').val(groupId);
        $('#editGroupName').val(name);
        $('#editGroupDescription').val(description);
        $('#editGroupModal').modal('show');
    });

    $('#editGroupForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/updateGroup') ?>',
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
                alert('Gagal mengupdate group');
            }
        });
    });

    // Edit permissions
    $('.edit-permissions').on('click', function() {
        const groupId = $(this).data('group-id');
        const groupName = $(this).data('name');
        
        $('#modalGroupName').text(groupName);
        $('#permissionGroupId').val(groupId);
        
        // Reset checkboxes
        $('input[name="permission_ids[]"]').prop('checked', false);
        
        // Load group permissions
        $.ajax({
            url: '<?= base_url('backend/auth/getGroup') ?>/' + groupId,
            method: 'GET',
            success: function(response) {
                if (response.success && response.group.permissions) {
                    response.group.permissions.forEach(function(permission) {
                        $('#permission_' + permission.id).prop('checked', true);
                    });
                }
                $('#editPermissionsModal').modal('show');
            },
            error: function() {
                alert('Gagal memuat data permissions');
            }
        });
    });

    $('#editPermissionsForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/updateGroupPermissions') ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#editPermissionsModal').modal('hide');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Gagal mengupdate permissions');
            }
        });
    });

    // Delete group
    $('.delete-group').on('click', function() {
        const groupId = $(this).data('group-id');
        const groupName = $(this).data('name');
        
        if (confirm('Apakah Anda yakin ingin menghapus group "' + groupName + '"?')) {
            $.ajax({
                url: '<?= base_url('backend/auth/deleteGroup') ?>/' + groupId,
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
                    alert('Gagal menghapus group');
                }
            });
        }
    });
});
</script>
<?= $this->endSection(); ?>

