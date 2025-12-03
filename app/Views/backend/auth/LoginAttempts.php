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
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm btn-primary" id="resetFilters">
                                            <i class="fas fa-redo"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Filters -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="filterDevice">Filter Device:</label>
                                            <select id="filterDevice" class="form-control form-control-sm">
                                                <option value="">Semua Device</option>
                                                <?php if (!empty($device_browser_stats['device_stats'])): ?>
                                                    <?php foreach ($device_browser_stats['device_stats'] as $device => $count): ?>
                                                        <option value="<?= esc($device) ?>"><?= esc($device) ?> (<?= number_format($count, 0, ',', '.') ?>)</option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="filterBrowser">Filter Browser:</label>
                                            <select id="filterBrowser" class="form-control form-control-sm">
                                                <option value="">Semua Browser</option>
                                                <?php if (!empty($device_browser_stats['top_browsers'])): ?>
                                                    <?php foreach ($device_browser_stats['top_browsers'] as $browser => $count): ?>
                                                        <option value="<?= esc($browser) ?>"><?= esc($browser) ?> (<?= number_format($count, 0, ',', '.') ?>)</option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="filterStatus">Filter Status:</label>
                                            <select id="filterStatus" class="form-control form-control-sm">
                                                <option value="">Semua Status</option>
                                                <option value="1">Berhasil</option>
                                                <option value="0">Gagal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-secondary" id="applyFilters">
                                                    <i class="fas fa-filter"></i> Terapkan Filter
                                                </button>
                                            </div>
                                        </div>
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
                                                    <th>Device</th>
                                                    <th>OS Version</th>
                                                    <th>Brand/Merek</th>
                                                    <th>Browser</th>
                                                    <th>Web Version</th>
                                                    <th>Status</th>
                                                    <th>Tanggal & Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($attempts)): ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center">Tidak ada data</td>
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
                                                            <td data-device="<?= esc($attempt['device_info'] ?? 'Unknown') ?>">
                                                                <?php if (!empty($attempt['device_info']) && $attempt['device_info'] !== 'Unknown' && $attempt['device_info'] !== '-'): ?>
                                                                    <span class="badge badge-info"
                                                                        <?php if (!empty($attempt['device_detail'])): ?>
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="<?= esc($attempt['device_detail']) ?>"
                                                                        <?php endif; ?>>
                                                                        <i class="fas fa-<?= strtolower($attempt['device_info']) === 'mobile' || strtolower($attempt['device_info']) === 'android' || strtolower($attempt['device_info']) === 'iphone' ? 'mobile-alt' : (strtolower($attempt['device_info']) === 'tablet' || strtolower($attempt['device_info']) === 'ipad' ? 'tablet-alt' : 'desktop') ?>"></i> <?= esc($attempt['device_info']) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($attempt['os_version'])): ?>
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-code-branch"></i> <?= esc($attempt['os_version']) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($attempt['device_brand']) || !empty($attempt['device_model'])): ?>
                                                                    <?php if (!empty($attempt['device_brand'])): ?>
                                                                        <span class="badge badge-secondary">
                                                                            <i class="fas fa-tag"></i> <?= esc($attempt['device_brand']) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($attempt['device_model'])): ?>
                                                                        <br><small class="text-muted" style="font-size: 0.75rem;">
                                                                            <i class="fas fa-mobile-alt"></i> <?= esc($attempt['device_model']) ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td data-browser="<?= esc($attempt['browser_info'] ?? 'Unknown') ?>">
                                                                <?php if (!empty($attempt['browser_info']) && $attempt['browser_info'] !== 'Unknown' && $attempt['browser_info'] !== '-'): ?>
                                                                    <span class="badge badge-primary">
                                                                        <i class="fas fa-globe"></i> <?= esc($attempt['browser_info']) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($attempt['browser_version']) && $attempt['browser_version'] !== '-'): ?>
                                                                    <small class="text-muted">v<?= esc($attempt['browser_version']) ?></small>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td data-status="<?= ($attempt['success'] ?? 0) ? '1' : '0' ?>">
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

        let table;
        let customFilterFunction = null;

        $(document).ready(function() {
            // Initialize Login Attempts Table (order by Tanggal & Waktu - column index 9)
            initDataTableWithOverflowScroll('#loginAttemptsTable', 25, true, 9, 'Error initializing Login Attempts DataTable:')
                .then(function(dataTable) {
                    table = dataTable;
                })
                .catch(function(error) {
                    console.error('Error initializing table:', error);
                    // Fallback: try to get existing DataTable instance
                    if ($.fn.DataTable.isDataTable('#loginAttemptsTable')) {
                        table = $('#loginAttemptsTable').DataTable();
                    }
                });

            // Apply filters function
            function applyFilters() {
                if (!table) {
                    console.warn('Table not initialized yet');
                    return;
                }

                const deviceFilter = $('#filterDevice').val();
                const browserFilter = $('#filterBrowser').val();
                const statusFilter = $('#filterStatus').val();

                // Remove existing custom filter if any
                if (customFilterFunction !== null) {
                    $.fn.dataTable.ext.search.pop();
                    customFilterFunction = null;
                }

                // Create new custom filter function
                customFilterFunction = function(settings, data, dataIndex) {
                    const row = table.row(dataIndex).node();

                    // Device filter
                    if (deviceFilter) {
                        const deviceValue = $(row).find('td[data-device]').attr('data-device');
                        if (deviceValue !== deviceFilter) {
                            return false;
                        }
                    }

                    // Browser filter
                    if (browserFilter) {
                        const browserValue = $(row).find('td[data-browser]').attr('data-browser');
                        if (browserValue !== browserFilter) {
                            return false;
                        }
                    }

                    // Status filter
                    if (statusFilter !== '') {
                        const statusValue = $(row).find('td[data-status]').attr('data-status');
                        if (statusValue !== statusFilter) {
                            return false;
                        }
                    }

                    return true;
                };

                // Add custom filter
                $.fn.dataTable.ext.search.push(customFilterFunction);

                // Redraw table
                table.draw();
            }

            // Remove all filters function
            function removeFilters() {
                if (!table) {
                    return;
                }

                // Remove custom filter
                if (customFilterFunction !== null) {
                    $.fn.dataTable.ext.search.pop();
                    customFilterFunction = null;
                }

                // Reset filter dropdowns
                $('#filterDevice').val('');
                $('#filterBrowser').val('');
                $('#filterStatus').val('');

                // Redraw table
                table.draw();
            }

            // Apply filters button
            $('#applyFilters').on('click', function() {
                applyFilters();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                removeFilters();
            });

            // Apply filter on Enter key in dropdowns
            $('#filterDevice, #filterBrowser, #filterStatus').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    applyFilters();
                }
            });

            // Initialize tooltips for device details
            $('[data-toggle="tooltip"]').tooltip();
        });
    })(jQuery);
</script>
<?= $this->endSection(); ?>