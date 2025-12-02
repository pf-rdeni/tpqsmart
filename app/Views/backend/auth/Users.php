<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Manajemen User
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Fullname</th>
                                    <th>Groups</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= esc($user['id']) ?></td>
                                        <td><?= esc($user['username'] ?? '-') ?></td>
                                        <td><?= esc($user['email'] ?? '-') ?></td>
                                        <td><?= esc($user['fullname'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($user['user_groups'])): ?>
                                                <span class="badge badge-info"><?= esc($user['user_groups']) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Tidak ada group</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['active'] ?? 0): ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-user-groups" data-user-id="<?= esc($user['id']) ?>" data-username="<?= esc($user['username']) ?>">
                                                <i class="fas fa-edit"></i> Edit Groups
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

<!-- Modal Edit User Groups -->
<div class="modal fade" id="editUserGroupsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Groups untuk User: <span id="modalUsername"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editUserGroupsForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="form-group">
                        <label>Pilih Groups:</label>
                        <div id="groupsCheckboxes">
                            <?php foreach ($groups as $group): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="group_ids[]" value="<?= esc($group['id']) ?>" id="group_<?= esc($group['id']) ?>">
                                    <label class="form-check-label" for="group_<?= esc($group['id']) ?>">
                                        <?= esc($group['name']) ?>
                                        <?php if (!empty($group['description'])): ?>
                                            <small class="text-muted">(<?= esc($group['description']) ?>)</small>
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
    $('#usersTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });

    // Edit user groups
    $('.edit-user-groups').on('click', function() {
        const userId = $(this).data('user-id');
        const username = $(this).data('username');
        
        $('#modalUsername').text(username);
        $('#userId').val(userId);
        
        // Reset checkboxes
        $('input[name="group_ids[]"]').prop('checked', false);
        
        // Load user groups
        $.ajax({
            url: '<?= base_url('backend/auth/getUser') ?>/' + userId,
            method: 'GET',
            success: function(response) {
                if (response.success && response.user.groups) {
                    response.user.groups.forEach(function(group) {
                        $('#group_' + group.id).prop('checked', true);
                    });
                }
                $('#editUserGroupsModal').modal('show');
            },
            error: function() {
                alert('Gagal memuat data user');
            }
        });
    });

    // Submit form
    $('#editUserGroupsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/auth/updateUserGroups') ?>',
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
                alert('Gagal mengupdate groups user');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>

