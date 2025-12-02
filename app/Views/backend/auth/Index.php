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
        return '<span class="text-muted">-</span>';
    }
    
    $groups = array_map('trim', explode(',', $groupsString));
    $badges = [];
    
    foreach ($groups as $group) {
        if (!empty($group)) {
            $color = getGroupBadgeColor($group);
            $badges[] = '<span class="badge badge-' . $color . '">' . esc($group) . '</span>';
        }
    }
    
    return !empty($badges) ? implode(' ', $badges) : '<span class="text-muted">-</span>';
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> Dashboard Pengaturan MyAuth
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $stats['total_users'] ?></h3>
                                    <p>Total User</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/users') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= $stats['active_users'] ?></h3>
                                    <p>User Aktif</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/users') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= $stats['total_groups'] ?></h3>
                                    <p>Total Group</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/groups') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $stats['total_permissions'] ?></h3>
                                    <p>Total Permission</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/permissions') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Online Statistics -->
                    <div class="row mt-3">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= count($online_users) ?></h3>
                                    <p>User Online</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= count(array_filter($online_users, function ($u) {
                                            return ($u['status_label'] ?? '') === 'Active';
                                        })) ?></h3>
                                    <p>Active (< 5 menit)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= count(array_filter($online_users, function ($u) {
                                            return ($u['status_label'] ?? '') === 'Idle';
                                        })) ?></h3>
                                    <p>Idle (5-15 menit)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= count(array_filter($online_users, function ($u) {
                                            return ($u['status_label'] ?? '') === 'Away';
                                        })) ?></h3>
                                    <p>Away (> 15 menit)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Frequent Login Statistics -->
                    <div class="row mt-3">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-gradient-primary">
                                <div class="inner">
                                    <h3><?= $frequent_login_stats['total_active_users'] ?? 0 ?></h3>
                                    <p>User Aktif (Login)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-gradient-success">
                                <div class="inner">
                                    <h3><?= number_format($frequent_login_stats['total_logins'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Total Login Sukses</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-gradient-info">
                                <div class="inner">
                                    <h3><?= count($frequent_login_stats['top_users'] ?? []) ?></h3>
                                    <p>Top User Login</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-gradient-warning">
                                <div class="inner">
                                    <h3><?= !empty($frequent_login_stats['top_users']) ? number_format($frequent_login_stats['top_users'][0]['login_count'] ?? 0, 0, ',', '.') : 0 ?></h3>
                                    <p>Login Terbanyak</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 Frequent Login Users -->
                    <!-- Quick Links -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-link"></i> Menu Cepat
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/users') ?>" class="btn btn-primary btn-block mb-2">
                                                <i class="fas fa-users"></i> Manajemen User
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/groups') ?>" class="btn btn-success btn-block mb-2">
                                                <i class="fas fa-user-tag"></i> Manajemen Group
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/permissions') ?>" class="btn btn-warning btn-block mb-2">
                                                <i class="fas fa-key"></i> Manajemen Permission
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="btn btn-info btn-block mb-2">
                                                <i class="fas fa-sign-in-alt"></i> Riwayat Login
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="btn btn-success btn-block mb-2">
                                                <i class="fas fa-user-check"></i> User Online
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="btn btn-primary btn-block mb-2">
                                                <i class="fas fa-trophy"></i> User Sering Login
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 User yang Sering Login -->
                    <?php if (!empty($frequent_login_stats['top_users'])): ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-trophy"></i> Top 5 User yang Sering Login
                                    </h3>
                                    <div class="card-tools">
                                        <a href="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>User</th>
                                                <th>Username</th>
                                                <th>Nama</th>
                                                <th>Groups</th>
                                                <th>Jumlah Login</th>
                                                <th>Login Terakhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            <?php foreach ($frequent_login_stats['top_users'] as $user): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($user['user_image']) && $user['user_image'] !== 'default.svg'): ?>
                                                                <img src="<?= base_url('uploads/profil/user/' . $user['user_image']) ?>"
                                                                    class="img-circle elevation-2"
                                                                    alt="User Image"
                                                                    style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;"
                                                                    onerror="this.style.display='none';">
                                                            <?php else: ?>
                                                                <i class="fas fa-user-circle" style="font-size: 30px; color: #6c757d; margin-right: 8px;"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($user['username']) ?></td>
                                                    <td><?= esc($user['fullname'] ? ucwords(strtolower($user['fullname'])) : '-') ?></td>
                                                    <td>
                                                        <?= renderGroupsAsBadges($user['user_groups'] ?? '') ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-sign-in-alt"></i> <?= number_format($user['login_count'], 0, ',', '.') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($user['last_login'])): ?>
                                                            <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Online Users -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-check"></i> User Online
                                    </h3>
                                    <div class="card-tools">
                                        <a href="<?= base_url('backend/auth/onlineUsers') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table id="onlineUsersTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>User</th>
                                                <th>Username</th>
                                                <th>Nama</th>
                                                <th>Groups</th>
                                                <th>Last Activity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($online_users)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada user yang sedang online</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php $no = 1; ?>
                                                <?php foreach ($online_users as $user): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if (!empty($user['user_image']) && $user['user_image'] !== 'default.svg'): ?>
                                                                    <img src="<?= base_url('uploads/profil/user/' . $user['user_image']) ?>"
                                                                        class="img-circle elevation-2"
                                                                        alt="User Image"
                                                                        style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;"
                                                                        onerror="this.style.display='none';">
                                                                <?php else: ?>
                                                                    <i class="fas fa-user-circle" style="font-size: 30px; color: #6c757d; margin-right: 8px;"></i>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <strong><?= esc($user['fullname'] ? ucwords(strtolower($user['fullname'])) : $user['username']) ?></strong>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= esc($user['username']) ?></td>
                                                        <td><?= esc($user['fullname'] ? ucwords(strtolower($user['fullname'])) : '-') ?></td>
                                                        <td>
                                                            <?= renderGroupsAsBadges($user['user_groups'] ?? '') ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $lastActivity = $user['last_activity'];
                                                            $idleMinutes = $user['idle_minutes'];

                                                            // Format waktu idle
                                                            if ($idleMinutes < 1) {
                                                                $idleText = 'Baru saja';
                                                                $idleBadge = 'success';
                                                            } elseif ($idleMinutes < 60) {
                                                                $idleText = round($idleMinutes) . ' menit lalu';
                                                                $idleBadge = $idleMinutes < 5 ? 'success' : ($idleMinutes < 15 ? 'info' : 'warning');
                                                            } else {
                                                                $idleHours = floor($idleMinutes / 60);
                                                                $idleText = $idleHours . ' jam lalu';
                                                                $idleBadge = 'warning';
                                                            }
                                                            ?>
                                                            <span class="badge badge-<?= $idleBadge ?>">
                                                                <i class="fas fa-clock"></i> <?= $idleText ?>
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?= date('d/m/Y H:i:s', strtotime($lastActivity)) ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?= esc($user['status_badge']) ?>">
                                                                <i class="fas fa-circle"></i> <?= esc($user['status_label']) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    (function($) {
        'use strict';

        $(document).ready(function() {
            // Wait for table to be fully rendered
            setTimeout(function() {
                const $table = $('#onlineUsersTable');

                // Check if table exists and has tbody with rows
                if ($table.length && $table.find('tbody').length) {
                    const tbody = $table.find('tbody')[0];

                    // Only initialize if tbody exists and has content
                    if (tbody && (tbody.rows.length > 0 || $table.find('tbody tr').length > 0)) {
                        try {
                            // Initialize DataTable dengan scroll horizontal
                            initializeDataTableScrollX('#onlineUsersTable', [], {
                                "pageLength": 10,
                                "lengthChange": true,
                                "order": [
                                    [5, "desc"]
                                ], // Sort by Last Activity
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
                        } catch (e) {
                            console.error('Error initializing DataTable:', e);
                        }
                    }
                }
            }, 100);
        });
    })(jQuery);
</script>
<?= $this->endSection(); ?>