<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Login Attempts -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-history"></i> Riwayat Login Terbaru
                                    </h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th>Email/Username</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recent_logins)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($recent_logins as $login): ?>
                                                    <tr>
                                                        <td><?= esc($login['ip_address'] ?? '-') ?></td>
                                                        <td><?= esc($login['email'] ?? '-') ?></td>
                                                        <td>
                                                            <?php if ($login['success'] ?? 0): ?>
                                                                <span class="badge badge-success">Berhasil</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Gagal</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= esc($login['date'] ?? '-') ?></td>
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

