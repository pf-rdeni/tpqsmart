<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Token Reset Password
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/auth') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="passwordResetsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Token</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Tanggal Expire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tokens)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tokens as $token): ?>
                                        <tr>
                                            <td><?= esc($token['user_id'] ?? '-') ?></td>
                                            <td><code><?= esc(substr($token['token'] ?? '-', 0, 20)) ?>...</code></td>
                                            <td><?= esc($token['created_at'] ?? '-') ?></td>
                                            <td><?= esc($token['expires_at'] ?? '-') ?></td>
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
    $('#passwordResetsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[2, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });
});
</script>
<?= $this->endSection(); ?>

