<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sign-in-alt"></i> Riwayat Login
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Login Attempts Statistics -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= number_format($login_attempts_stats['total_attempts'] ?? 0, 0, ',', '.') ?></h3>
                                    <p>Total Attempts</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    <span class="text-muted">Semua Waktu</span>
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
                                <a href="#" class="small-box-footer">
                                    <span class="text-muted">Semua Waktu</span>
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
                                <a href="#" class="small-box-footer">
                                    <span class="text-muted">Semua Waktu</span>
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
                                <a href="#" class="small-box-footer">
                                    <span class="text-muted">Berhasil: <?= number_format($login_attempts_stats['today_successful'] ?? 0, 0, ',', '.') ?> | Gagal: <?= number_format($login_attempts_stats['today_failed'] ?? 0, 0, ',', '.') ?></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Device & Browser Statistics -->
                    <?php if (!empty($device_browser_stats) && $device_browser_stats['total_with_user_agent'] > 0): ?>
                        <div class="row mt-4">
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

                    <!-- Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-table"></i> Daftar Riwayat Login
                                    </h3>
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
                                                <?php if (empty($attempts)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Tidak ada data</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($attempts as $attempt): ?>
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
                                                                    <span class="badge badge-info">
                                                                        <i class="fas fa-<?= strtolower($attempt['device_info']) === 'mobile' ? 'mobile-alt' : (strtolower($attempt['device_info']) === 'tablet' ? 'tablet-alt' : 'desktop') ?>"></i> <?= esc($attempt['device_info']) ?>
                                                                    </span>
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
            // Initialize Login Attempts Table (order by Tanggal & Waktu - column index 5)
            initDataTableWithOverflowScroll('#loginAttemptsTable', 25, true, 5, 'Error initializing Login Attempts DataTable:');
        });
    })(jQuery);
</script>
<?= $this->endSection(); ?>