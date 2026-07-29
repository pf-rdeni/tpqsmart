<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-archive text-secondary me-2"></i> Arsip Paket Soal MDTA</h4>
                <small class="text-muted">Daftar paket soal yang diarsipkan</small>
            </div>
            <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Paket Soal
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($paketList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada paket soal di arsip</h5>
                </div>
<style>
    .table-responsive-custom {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 767.98px) {
        .table th, .table td {
            white-space: nowrap;
            vertical-align: middle;
        }
    }
</style>

                <div class="table-responsive table-responsive-custom">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-secondary align-middle">
                            <tr>
                                <th>No</th>
                                <th>Nama Paket Soal</th>
                                <th>Kelas</th>
                                <th>Materi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paketList as $i => $paket): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= esc($paket['NamaPaket']) ?></td>
                                    <td><?= esc($paket['NamaKelas'] ?? '-') ?></td>
                                    <td><?= esc($paket['NamaMateri'] ?? '-') ?></td>
                                    <td><span class="badge bg-secondary">Arsip</span></td>
                                    <td>
                                        <form method="post" action="<?= base_url("backend/ujian-mdta/paket/restore/{$paket['id']}") ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-undo me-1"></i> Restore (Aktifkan Kembali)
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
