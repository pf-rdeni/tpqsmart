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
                    <div class="table-responsive">
                        <table id="loginAttemptsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Email/Username</th>
                                    <th>User ID</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attempts)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attempts as $attempt): ?>
                                        <tr>
                                            <td><?= esc($attempt['ip_address'] ?? '-') ?></td>
                                            <td><?= esc($attempt['email'] ?? '-') ?></td>
                                            <td><?= esc($attempt['user_id'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($attempt['success'] ?? 0): ?>
                                                    <span class="badge badge-success">Berhasil</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Gagal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($attempt['date'] ?? '-') ?></td>
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
    $('#loginAttemptsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[4, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });
});
</script>
<?= $this->endSection(); ?>

