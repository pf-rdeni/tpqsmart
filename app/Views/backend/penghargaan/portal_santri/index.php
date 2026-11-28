<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="card card-outline card-success shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="get" class="form-inline flex-wrap">
                <label class="mr-2 font-weight-bold text-sm"><i class="fas fa-filter mr-1"></i> Filter:</label>
                <input type="text" name="tahun_ajaran" class="form-control form-control-sm mr-2 mb-1" placeholder="Tahun Ajaran (mis: 20252026)" value="<?= esc($idTahunAjaran) ?>">
                <select name="jenis" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Jenis Penghargaan</option>
                    <?php foreach ($listJenis as $j): ?>
                        <option value="<?= $j['Id'] ?>" <?= (string) $idJenis === (string) $j['Id'] ? 'selected' : '' ?>>
                            <?= esc($j['NamaPenghargaan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-success btn-sm mb-1 mr-1"><i class="fas fa-search mr-1"></i> Cari</button>
                <a href="<?= base_url('backend/penghargaan/portal-santri') ?>" class="btn btn-default btn-sm mb-1">Reset</a>
            </form>
        </div>
    </div>

    <?php if (empty($listPenghargaan)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-award fa-3x text-warning mb-3"></i>
                <h5>Belum Ada Penghargaan</h5>
                <p class="mb-0">Sertifikat penghargaan yang telah dirilis resmi oleh lembaga akan muncul di halaman ini.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($listPenghargaan as $item): ?>
                <div class="col-md-6 mb-3">
                    <div class="card card-outline card-warning shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge badge-warning mb-1">
                                        <?= !empty($item['Peringkat']) ? 'Peringkat #' . esc($item['Peringkat']) : esc(ucfirst($item['Kategori'])) ?>
                                    </span>
                                    <h5 class="font-weight-bold mb-0"><?= esc($item['NamaPenghargaan']) ?></h5>
                                </div>
                                <i class="fas fa-medal fa-2x text-warning"></i>
                            </div>
                            <p class="text-sm text-muted mb-2">
                                Semester <?= esc($item['Semester']) ?> · TA <?= esc($item['IdTahunAjaran']) ?>
                                <?= !empty($item['NamaKelas']) ? ' · Kelas ' . esc($item['NamaKelas']) : '' ?>
                            </p>
                            <div class="text-sm mb-2">
                                <span class="mr-3"><i class="fas fa-star text-primary mr-1"></i> Nilai: <strong><?= number_format((float) $item['NilaiRataRata'], 2) ?></strong></span>
                                <span><i class="fas fa-calendar-check text-success mr-1"></i> Hadir: <strong><?= number_format((float) $item['PersenKehadiran'], 2) ?>%</strong></span>
                            </div>
                            <small class="text-muted d-block">
                                No. Seri: <code><?= esc($item['NomorSeri']) ?></code> · Dirilis: <?= esc($item['TanggalRelease'] ? date('d/m/Y', strtotime($item['TanggalRelease'])) : '-') ?>
                            </small>
                        </div>
                        <div class="card-footer bg-white text-right">
                            <a href="<?= base_url('backend/penghargaan/portal-santri/preview/' . $item['Id']) ?>" class="btn btn-info btn-sm mr-1">
                                <i class="fas fa-eye mr-1"></i> Preview
                            </a>
                            <a href="<?= base_url('backend/penghargaan/portal-santri/download/' . $item['Id']) ?>" class="btn btn-danger btn-sm" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>
