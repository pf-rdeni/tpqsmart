<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-certificate text-success mr-2"></i>
                    [<?= esc($jenis['Kode'] ?? '') ?>] <?= esc($jenis['NamaPenghargaan'] ?? '') ?>
                </h3>
                <div class="text-muted text-sm mt-1">
                    Semester <?= esc($sertifikat['Semester']) ?> · TA <?= esc($sertifikat['IdTahunAjaran']) ?> ·
                    Status:
                    <?php if ($sertifikat['Status'] === 'final'): ?>
                        <span class="badge badge-success">Final</span>
                    <?php elseif ($sertifikat['Status'] === 'draft'): ?>
                        <span class="badge badge-warning">Draft</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Dibatalkan</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="ml-auto d-flex flex-wrap gap-1">
                <a href="<?= base_url('backend/penghargaan/sertifikat/cetak-bulk/' . $sertifikat['Id']) ?>" class="btn btn-danger btn-sm mr-1" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak Semua (PDF)
                </a>
                <a href="<?= base_url('backend/penghargaan/sertifikat/cetak-zip/' . $sertifikat['Id']) ?>" class="btn btn-secondary btn-sm mr-1">
                    <i class="fas fa-file-archive mr-1"></i> Download ZIP
                </a>
                <?php if (!empty($roleCtx['isOperatorOrAdmin'])): ?>
                    <?php if ($sertifikat['Status'] === 'draft'): ?>
                        <form action="<?= base_url('backend/penghargaan/sertifikat/release/' . $sertifikat['Id']) ?>" method="post" class="d-inline mr-1" onsubmit="return confirm('Release sertifikat ke portal santri?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check mr-1"></i> Release Final</button>
                        </form>
                    <?php elseif ($sertifikat['Status'] === 'final'): ?>
                        <form action="<?= base_url('backend/penghargaan/sertifikat/cancel/' . $sertifikat['Id']) ?>" method="post" class="d-inline mr-1" onsubmit="return confirm('Batalkan/cabut sertifikat ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-ban mr-1"></i> Cabut</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?= base_url('backend/penghargaan/sertifikat') ?>" class="btn btn-default btn-sm">Kembali</a>
            </div>
        </div>
        <?php if (!empty($roleCtx['isOperatorOrAdmin'])): ?>
        <div class="card-body py-2 bg-light d-flex flex-wrap justify-content-between align-items-center">
            <form action="<?= base_url('backend/penghargaan/sertifikat/set-template/' . $sertifikat['Id']) ?>" method="post" class="form-inline text-sm mr-3 mb-1">
                <?= csrf_field() ?>
                <label class="mr-2 font-weight-bold">Template Desain:</label>
                <select name="IdTemplate" class="form-control form-control-sm mr-2">
                    <option value="">-- Otomatis (Default Aktif) --</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= $t['Id'] ?>" <?= (string) $sertifikat['IdTemplate'] === (string) $t['Id'] ? 'selected' : '' ?>>
                            <?= esc($t['NamaTemplate']) ?> (<?= esc($t['UkuranKertas']) ?> <?= esc($t['Orientasi']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Terapkan Template</button>
            </form>

            <form action="<?= base_url('backend/penghargaan/sertifikat/set-tanggal/' . $sertifikat['Id']) ?>" method="post" class="form-inline text-sm mb-1">
                <?= csrf_field() ?>
                <label class="mr-2 font-weight-bold">Tanggal Sertifikat:</label>
                <input type="date" name="TanggalSertifikat" class="form-control form-control-sm mr-2"
                    value="<?= esc($sertifikat['TanggalSertifikat'] ?? date('Y-m-d', strtotime($sertifikat['TanggalGenerate'] ?? 'now'))) ?>" required>
                <button type="submit" class="btn btn-outline-primary btn-sm">Simpan Tanggal</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">Daftar Penerima (<?= count($penerima) ?> Santri)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 65px;">Rank</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>Nomor Seri</th>
                            <th class="text-right">Nilai</th>
                            <th class="text-right">% Hadir</th>
                            <th class="text-right">Skor</th>
                            <th class="text-center" style="width: 120px;">Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penerima as $p): ?>
                            <tr>
                                <td class="text-center"><span class="badge badge-warning">#<?= esc($p['Peringkat']) ?></span></td>
                                <td><strong><?= esc($p['NamaSantri']) ?></strong></td>
                                <td><?= esc($p['NamaKelas'] ?? '-') ?></td>
                                <td><code><?= esc($p['NomorSeri']) ?></code></td>
                                <td class="text-right"><?= number_format((float) $p['NilaiRataRata'], 2) ?></td>
                                <td class="text-right"><?= number_format((float) $p['PersenKehadiran'], 2) ?>%</td>
                                <td class="text-right font-weight-bold"><?= number_format((float) $p['SkorGabungan'], 2) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('backend/penghargaan/sertifikat/cetak/' . $p['Id']) ?>" class="btn btn-danger btn-xs" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
