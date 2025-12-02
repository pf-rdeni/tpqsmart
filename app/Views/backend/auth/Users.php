<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
// Function to get badge color for group
function getGroupBadgeColor($groupName) {
    $groupColors = [
        'Admin' => 'danger',
        'Operator' => 'primary',
        'Guru' => 'success',
        'Kepala TPQ' => 'warning',
        'Santri' => 'info',
        'default' => 'secondary'
    ];
    
    $groupName = trim($groupName);
    foreach ($groupColors as $key => $color) {
        if (stripos($groupName, $key) !== false) {
            return $color;
        }
    }
    return $groupColors['default'];
}

// Function to render groups as badges
function renderGroupsAsBadges($groupsString) {
    if (empty($groupsString)) {
        return '<span class="badge badge-secondary">Tidak ada group</span>';
    }
    
    $groups = array_map('trim', explode(',', $groupsString));
    $badges = [];
    
    foreach ($groups as $group) {
        if (!empty($group)) {
            $color = getGroupBadgeColor($group);
            $badges[] = '<span class="badge badge-' . $color . '">' . esc($group) . '</span>';
        }
    }
    
    return !empty($badges) ? implode(' ', $badges) : '<span class="badge badge-secondary">Tidak ada group</span>';
}
?>
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
                                            <?= renderGroupsAsBadges($user['user_groups'] ?? '') ?>
                                        </td>
                                        <td>
                                            <?php if ($user['active'] ?? 0): ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary edit-user-groups" data-user-id="<?= esc($user['id']) ?>" data-username="<?= esc($user['username']) ?>">
                                                    <i class="fas fa-edit"></i> Edit Groups
                                                </button>
                                                <button class="btn btn-sm btn-danger reset-password" data-user-id="<?= esc($user['id']) ?>" data-username="<?= esc($user['username']) ?>">
                                                    <i class="fas fa-redo"></i> Reset Password
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

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password untuk User: <span id="resetPasswordUsername"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="resetPasswordForm">
                <div class="modal-body">
                    <input type="hidden" id="resetPasswordUserId" name="user_id">
                    <div class="form-group">
                        <label>Default Password:</label>
                        <input type="text" class="form-control" id="defaultPassword" name="default_password" value="TpqSmart123" required>
                        <small class="form-text text-muted">Password default yang akan digunakan untuk user ini</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="forceResetOnReset" name="force_reset" value="1" checked>
                        <label class="form-check-label" for="forceResetOnReset">
                            Wajibkan user mengganti password saat login berikutnya
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize DataTable dengan scroll horizontal
        initializeDataTableScrollX('#usersTable', [], {
            "pageLength": 25,
            "lengthChange": true,
            "language": {
                "decimal": "",
                "emptyTable": "Tidak ada data yang tersedia pada tabel",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                }
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data user',
                        confirmButtonText: 'OK'
                    });
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Group user berhasil diupdate',
                            confirmButtonText: 'OK',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mengupdate group user',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengupdate groups user',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Reset Password
        $('.reset-password').on('click', function() {
            const userId = $(this).data('user-id');
            const username = $(this).data('username');
            
            $('#resetPasswordUsername').text(username);
            $('#resetPasswordUserId').val(userId);
            $('#defaultPassword').val('TpqSmart123');
            $('#forceResetOnReset').prop('checked', true);
            $('#resetPasswordModal').modal('show');
        });

        // Submit Reset Password Form
        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            Swal.fire({
                title: 'Konfirmasi Reset Password',
                text: 'Password user akan direset ke password default. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset Password',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/auth/resetPassword') ?>',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                $('#resetPasswordModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    html: response.message + '<br><br><strong>Password Default: ' + response.default_password + '</strong>',
                                    confirmButtonText: 'OK',
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal reset password',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
})(jQuery);
</script>
<?= $this->endSection(); ?>

