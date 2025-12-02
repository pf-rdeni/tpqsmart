<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-check"></i> User Online
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Card (Collapsed by default) -->
                    <div class="card card-info card-outline collapsed-card mb-3">
                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Informasi
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <ul class="mb-0">
                                <li>Halaman ini menampilkan semua user yang memiliki <strong>session aktif</strong> (belum expired)</li>
                                <li>Status ditentukan berdasarkan <strong>Last Activity</strong> (waktu modifikasi session file):</li>
                                <ul>
                                    <li><span class="badge badge-success">Active</span>: User aktif dalam 5 menit terakhir</li>
                                    <li><span class="badge badge-info">Idle</span>: User idle 5-15 menit</li>
                                    <li><span class="badge badge-warning">Away</span>: User idle 15-<?= esc($session_expiration_minutes) ?> menit (masih dalam session expiration)</li>
                                </ul>
                                <li>Session expiration: <strong><?= esc($session_expiration_minutes) ?> menit</strong> (<?= round($session_expiration_minutes / 60, 1) ?> jam)</li>
                                <li>User dengan session yang sudah expired tidak akan ditampilkan</li>
                                <li>Data diperbarui otomatis setiap 30 detik</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total User Online</span>
                                    <span class="info-box-number"><?= count($online_users) ?> User</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success elevation-1">
                                    <i class="fas fa-user-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active (idle < 5 menit)</span>
                                    <span class="info-box-number"><?= count(array_filter($online_users, function($u) { return $u['status'] === 'active'; })) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning elevation-1">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Idle/Away</span>
                                    <span class="info-box-number"><?= count(array_filter($online_users, function($u) { return $u['status'] !== 'active'; })) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Online Users Table -->
                    <div class="table-responsive">
                        <table id="onlineUsersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Groups</th>
                                    <th>Last Activity</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($online_users)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada user yang sedang online</td>
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
                                                             style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;"
                                                             onerror="this.style.display='none';">
                                                    <?php else: ?>
                                                        <i class="fas fa-user-circle" style="font-size: 40px; color: #6c757d; margin-right: 10px;"></i>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?= esc($user['fullname'] ?? $user['username']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($user['username']) ?></td>
                                            <td><?= esc($user['email'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($user['user_groups'])): ?>
                                                    <?= esc($user['user_groups']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
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
                                                <code><?= esc($user['ip_address'] ?? '-') ?></code>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= esc($user['status_badge']) ?>">
                                                    <i class="fas fa-circle"></i> <?= esc($user['status_label']) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Idle: <?= esc($user['idle_minutes']) ?> menit
                                                </small>
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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#onlineUsersTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[5, "desc"]], // Sort by Last Activity
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
<?= $this->endSection(); ?>

