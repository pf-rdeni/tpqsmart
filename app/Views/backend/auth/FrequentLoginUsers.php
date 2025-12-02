<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy"></i> User yang Sering Login
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
                                <li>Halaman ini menampilkan statistik user yang <strong>sering melakukan login</strong> ke sistem</li>
                                <li>Data dihitung berdasarkan jumlah <strong>login sukses</strong> dari tabel auth_logins</li>
                                <li>Anda dapat memfilter berdasarkan periode waktu tertentu</li>
                                <li>User diurutkan berdasarkan jumlah login terbanyak</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-filter"></i> Filter
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="<?= base_url('backend/auth/frequentLoginUsers') ?>" class="form-inline">
                                        <div class="form-group mr-3">
                                            <label for="period" class="mr-2">Periode:</label>
                                            <select name="period" id="period" class="form-control">
                                                <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Semua Waktu</option>
                                                <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                                                <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                                                <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                                                <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>1 Tahun Terakhir</option>
                                            </select>
                                        </div>
                                        <div class="form-group mr-3">
                                            <label for="limit" class="mr-2">Jumlah Data:</label>
                                            <select name="limit" id="limit" class="form-control">
                                                <option value="10" <?= $limit === 10 ? 'selected' : '' ?>>10</option>
                                                <option value="25" <?= $limit === 25 ? 'selected' : '' ?>>25</option>
                                                <option value="50" <?= $limit === 50 ? 'selected' : '' ?>>50</option>
                                                <option value="100" <?= $limit === 100 ? 'selected' : '' ?>>100</option>
                                                <option value="200" <?= $limit === 200 ? 'selected' : '' ?>>200</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                    </form>
                                </div>
                            </div>
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
                                    <span class="info-box-text">Total User Aktif</span>
                                    <span class="info-box-number"><?= number_format($stats['total_active_users'], 0, ',', '.') ?> User</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success elevation-1">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Login Sukses</span>
                                    <span class="info-box-number"><?= number_format($stats['total_logins'], 0, ',', '.') ?> Login</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning elevation-1">
                                    <i class="fas fa-trophy"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Rata-rata Login/User</span>
                                    <span class="info-box-number">
                                        <?= $stats['total_active_users'] > 0 ? number_format($stats['total_logins'] / $stats['total_active_users'], 2, ',', '.') : 0 ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table id="frequentLoginUsersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Groups</th>
                                    <th>Jumlah Login</th>
                                    <th>Login Pertama</th>
                                    <th>Login Terakhir</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($users as $user): ?>
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
                                                </div>
                                            </td>
                                            <td><?= esc($user['username']) ?></td>
                                            <td><?= esc($user['fullname'] ? ucwords(strtolower($user['fullname'])) : '-') ?></td>
                                            <td><?= esc($user['email'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($user['user_groups'])): ?>
                                                    <span class="badge badge-info"><?= esc($user['user_groups']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-success" style="font-size: 14px;">
                                                    <i class="fas fa-sign-in-alt"></i> <?= number_format($user['login_count'], 0, ',', '.') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($user['first_login'])): ?>
                                                    <small><?= date('d/m/Y H:i', strtotime($user['first_login'])) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($user['last_login'])): ?>
                                                    <small><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user['active'] ?? 0): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Tidak Aktif</span>
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
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Wait for table to be fully rendered
        setTimeout(function() {
            const $table = $('#frequentLoginUsersTable');
            
            // Check if table exists and has tbody with rows
            if ($table.length && $table.find('tbody').length) {
                const tbody = $table.find('tbody')[0];
                
                // Only initialize if tbody exists and has content
                if (tbody && (tbody.rows.length > 0 || $table.find('tbody tr').length > 0)) {
                    try {
                        // Initialize DataTable dengan scroll horizontal
                        initializeDataTableScrollX('#frequentLoginUsersTable', [], {
                            "pageLength": <?= $limit ?>,
                            "lengthChange": true,
                            "order": [[6, "desc"]], // Sort by Jumlah Login
                            "language": {
                                "decimal": "",
                                "emptyTable": "Tidak ada data yang tersedia pada tabel",
                                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                                "infoPostFix": "",
                                "thousands": ".",
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

