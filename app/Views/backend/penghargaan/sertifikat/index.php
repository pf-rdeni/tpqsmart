<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
            <h3 class="card-title font-weight-bold"><i class="fas fa-certificate text-success mr-2"></i>Daftar Batch Sertifikat Penghargaan</h3>
            <div class="ml-auto d-flex align-items-center">
                <form method="get" class="form-inline mr-2">
                    <select name="status" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="final" <?= $status === 'final' ? 'selected' : '' ?>>Final (Dirilis)</option>
                        <option value="dibatal" <?= $status === 'dibatal' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </form>
                <?php if (!empty($roleCtx['isOperatorOrAdmin']) || !empty($roleCtx['isWaliKelas'])): ?>
                    <a href="<?= base_url('backend/penghargaan/generate') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-magic mr-1"></i> Generate Baru
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Jenis Penghargaan</th>
                            <th>Periode</th>
                            <th>Template</th>
                            <th class="text-center">Penerima</th>
                            <th class="text-center">Status</th>
                            <th>Tgl Generate</th>
                            <th class="text-center" style="width: 260px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-certificate fa-2x mb-2 d-block"></i>
                                    Belum ada batch sertifikat. Klik <strong>Generate Baru</strong> untuk membuat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $row): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-light border mr-1"><?= esc($row['Kode']) ?></span>
                                        <strong><?= esc($row['NamaPenghargaan']) ?></strong>
                                    </td>
                                    <td><?= esc($row['Semester']) ?> · TA <?= esc($row['IdTahunAjaran']) ?></td>
                                    <td><?= esc($row['NamaTemplate'] ?? 'Default Aktif') ?></td>
                                    <td class="text-center"><span class="badge badge-info"><?= esc($row['TotalPenerima']) ?> santri</span></td>
                                    <td class="text-center">
                                        <?php if ($row['Status'] === 'final'): ?>
                                            <span class="badge badge-success">Final</span>
                                        <?php elseif ($row['Status'] === 'draft'): ?>
                                            <span class="badge badge-warning">Draft</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc(date('d/m/Y H:i', strtotime($row['TanggalGenerate']))) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('backend/penghargaan/sertifikat/detail/' . $row['Id']) ?>" class="btn btn-info btn-xs" title="Detail & Penerima">
                                            <i class="fas fa-list"></i> Detail
                                        </a>
                                        <a href="<?= base_url('backend/penghargaan/sertifikat/cetak-bulk/' . $row['Id']) ?>" class="btn btn-danger btn-xs" target="_blank" title="Cetak Semua (1 PDF)">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        <a href="<?= base_url('backend/penghargaan/sertifikat/cetak-zip/' . $row['Id']) ?>" class="btn btn-secondary btn-xs" title="Download ZIP">
                                            <i class="fas fa-file-archive"></i> ZIP
                                        </a>
                                        <?php if ($row['Status'] === 'draft' && !empty($roleCtx['isOperatorOrAdmin'])): ?>
                                            <form action="<?= base_url('backend/penghargaan/sertifikat/release/' . $row['Id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Release sertifikat ini agar terlihat di portal santri?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-xs" title="Release Final"><i class="fas fa-check"></i> Release</button>
                                            </form>
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

<?= $this->endSection(); ?>
