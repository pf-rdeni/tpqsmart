<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
// Function to get badge color for group
function getGroupBadgeColor($groupName)
{
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
function renderGroupsAsBadges($groupsString)
{
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

                    <!-- Login Attempts Statistics -->
                    <div class="row mt-3">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= number_format($login_attempts_stats['total_attempts'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Total Attempts</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= number_format($login_attempts_stats['successful_logins'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Login Berhasil</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= number_format($login_attempts_stats['failed_logins'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Login Gagal</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= number_format($login_attempts_stats['today_attempts'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Attempts Hari Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="small-box-footer">
                                    Berhasil: <?= number_format($login_attempts_stats['today_successful'] ?? 0, 0, ',', '.') ?> | Gagal: <?= number_format($login_attempts_stats['today_failed'] ?? 0, 0, ',', '.') ?> <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Device & Browser Statistics -->
                    <?php if (!empty($device_browser_stats) && $device_browser_stats['total_with_user_agent'] > 0): ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-mobile-alt"></i> Statistik Device
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($device_browser_stats['device_stats'] as $device => $count): ?>
                                            <?php if ($count > 0): ?>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>
                                                            <i class="fas fa-<?= strtolower($device) === 'mobile' ? 'mobile-alt' : (strtolower($device) === 'tablet' ? 'tablet-alt' : (strtolower($device) === 'bot' ? 'robot' : 'desktop')) ?>"></i>
                                                            <strong><?= esc($device) ?></strong>
                                                        </span>
                                                        <span>
                                                            <strong><?= number_format($count, 0, ',', '.') ?></strong>
                                                            <small class="text-muted">(<?= $device_browser_stats['device_percentages'][$device] ?>%)</small>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-<?= strtolower($device) === 'mobile' ? 'info' : (strtolower($device) === 'tablet' ? 'warning' : (strtolower($device) === 'bot' ? 'secondary' : 'primary')) ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= $device_browser_stats['device_percentages'][$device] ?>%"
                                                             aria-valuenow="<?= $device_browser_stats['device_percentages'][$device] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?= $device_browser_stats['device_percentages'][$device] ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-globe"></i> Top Browser
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($device_browser_stats['top_browsers'])): ?>
                                            <?php $browserColors = ['Chrome' => 'success', 'Firefox' => 'warning', 'Safari' => 'info', 'Edge' => 'primary', 'Opera' => 'danger']; ?>
                                            <?php foreach ($device_browser_stats['top_browsers'] as $browser => $count): ?>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>
                                                            <i class="fas fa-globe"></i>
                                                            <strong><?= esc($browser) ?></strong>
                                                        </span>
                                                        <span>
                                                            <strong><?= number_format($count, 0, ',', '.') ?></strong>
                                                            <small class="text-muted">(<?= $device_browser_stats['browser_percentages'][$browser] ?? 0 ?>%)</small>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-<?= $browserColors[$browser] ?? 'secondary' ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= $device_browser_stats['browser_percentages'][$browser] ?? 0 ?>%"
                                                             aria-valuenow="<?= $device_browser_stats['browser_percentages'][$browser] ?? 0 ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?= $device_browser_stats['browser_percentages'][$browser] ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">Tidak ada data browser</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

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
                                <div class="card-body p-0">
                                    <div style="overflow-x: auto;">
                                        <table id="onlineUsersTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">No</th>
                                                <th>Username / Nama</th>
                                                <th>Groups</th>
                                                <th>Last Activity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($online_users)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada user yang sedang online</td>
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
                                                                    <strong><?= esc($user['username']) ?></strong>
                                                                    <?php if (!empty($user['fullname'])): ?>
                                                                        <br><small class="text-muted"><?= esc(ucwords(strtolower($user['fullname']))) ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
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
                                        <div style="overflow-x: auto;">
                                            <table id="top5FrequentLoginTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px">No</th>
                                                    <th>Username / Nama</th>
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
                                                                <div>
                                                                    <strong><?= esc($user['username']) ?></strong>
                                                                    <?php if (!empty($user['fullname'])): ?>
                                                                        <br><small class="text-muted"><?= esc(ucwords(strtolower($user['fullname']))) ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
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
                        </div>
                    <?php endif; ?>

                    <!-- Riwayat Login -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-sign-in-alt"></i> Riwayat Login
                                    </h3>
                                    <div class="card-tools">
                                        <a href="<?= base_url('backend/auth/loginAttempts') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt"></i> Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div style="overflow-x: auto;">
                                        <table id="loginAttemptsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">No</th>
                                                <th>Username / Nama</th>
                                                <th>IP Address</th>
                                                <th>Device & Browser</th>
                                                <th>Status</th>
                                                <th>Tanggal & Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($login_attempts)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php $no = 1; ?>
                                                <?php foreach ($login_attempts as $attempt): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if (!empty($attempt['user_image']) && $attempt['user_image'] !== 'default.svg'): ?>
                                                                    <img src="<?= base_url('uploads/profil/user/' . $attempt['user_image']) ?>"
                                                                        class="img-circle elevation-2"
                                                                        alt="User Image"
                                                                        style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;"
                                                                        onerror="this.style.display='none';">
                                                                <?php else: ?>
                                                                    <i class="fas fa-user-circle" style="font-size: 30px; color: #6c757d; margin-right: 8px;"></i>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <?php if (!empty($attempt['username'])): ?>
                                                                        <strong><?= esc($attempt['username']) ?></strong>
                                                                        <?php if (!empty($attempt['fullname'])): ?>
                                                                            <br><small class="text-muted"><?= esc(ucwords(strtolower($attempt['fullname']))) ?></small>
                                                                        <?php endif; ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">-</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= esc($attempt['ip_address'] ?? '-') ?></td>
                                                            <td>
                                                                <?php if (!empty($attempt['device_info']) && $attempt['device_info'] !== '-'): ?>
                                                                    <span class="badge badge-info" 
                                                                          <?php if (!empty($attempt['device_detail'])): ?>
                                                                              data-toggle="tooltip" 
                                                                              data-placement="top" 
                                                                              title="<?= esc($attempt['device_detail']) ?>"
                                                                          <?php endif; ?>>
                                                                        <i class="fas fa-<?= strtolower($attempt['device_info']) === 'mobile' || strtolower($attempt['device_info']) === 'android' || strtolower($attempt['device_info']) === 'iphone' ? 'mobile-alt' : (strtolower($attempt['device_info']) === 'tablet' || strtolower($attempt['device_info']) === 'ipad' ? 'tablet-alt' : 'desktop') ?>"></i> <?= esc($attempt['device_info']) ?>
                                                                    </span>
                                                                    <?php if (!empty($attempt['device_detail'])): ?>
                                                                        <br><small class="text-muted" style="font-size: 0.7rem;">
                                                                            <?php if (!empty($attempt['device_brand'])): ?>
                                                                                <i class="fas fa-mobile-alt"></i> <?= esc($attempt['device_brand']) ?>
                                                                                <?php if (!empty($attempt['device_model'])): ?>
                                                                                    <?= esc($attempt['device_model']) ?>
                                                                                <?php endif; ?>
                                                                                <?php if (!empty($attempt['os_version'])): ?>
                                                                                    <br><i class="fas fa-code-branch"></i> <?= esc($attempt['os_version']) ?>
                                                                                <?php endif; ?>
                                                                            <?php elseif (!empty($attempt['os_version'])): ?>
                                                                                <i class="fas fa-code-branch"></i> <?= esc($attempt['os_version']) ?>
                                                                            <?php endif; ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-globe"></i> <?= esc($attempt['browser_info']) ?>
                                                                        <?php if (!empty($attempt['browser_version']) && $attempt['browser_version'] !== '-'): ?>
                                                                            v<?= esc($attempt['browser_version']) ?>
                                                                        <?php endif; ?>
                                                                    </small>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        <td>
                                                            <?php if ($attempt['success'] ?? 0): ?>
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check-circle"></i> Berhasil
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">
                                                                    <i class="fas fa-times-circle"></i> Gagal
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-order="<?= !empty($attempt['date']) ? strtotime($attempt['date']) : 0 ?>">
                                                            <?php if (!empty($attempt['date'])): ?>
                                                                <small><?= date('d/m/Y H:i:s', strtotime($attempt['date'])) ?></small>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
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
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    (function($) {
        'use strict';

        $(document).ready(function() {
            // Initialize Online Users Table (order by Last Activity - column index 3)
            initDataTableWithOverflowScroll('#onlineUsersTable', 10, true, 3, 'Error initializing Online Users DataTable:');

            // Initialize Top 5 Frequent Login Users Table (order by Jumlah Login - column index 3)
            initDataTableWithOverflowScroll('#top5FrequentLoginTable', 5, false, 3, 'Error initializing Top 5 DataTable:');

            // Initialize Login Attempts Table (order by Tanggal & Waktu - column index 5)
            initDataTableWithOverflowScroll('#loginAttemptsTable', 10, true, 5, 'Error initializing Login Attempts DataTable:');

            // Initialize tooltips for device details
            $('[data-toggle="tooltip"]').tooltip();
        });
    })(jQuery);
</script>
<?= $this->endSection(); ?>