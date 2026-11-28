<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Filter & Export -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-2 d-flex flex-wrap justify-content-between align-items-center">
            <form method="get" class="form-inline flex-wrap">
                <label class="mr-2 font-weight-bold text-sm"><i class="fas fa-filter mr-1"></i> Filter:</label>
                <select name="tahun_ajaran" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua TA</option>
                    <?php foreach ($tahunAjaranList as $ta): ?>
                        <option value="<?= esc($ta['IdTahunAjaran']) ?>" <?= $idTahunAjaran == $ta['IdTahunAjaran'] ? 'selected' : '' ?>>
                            TA <?= esc($ta['IdTahunAjaran']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="semester" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                    <option value="Genap" <?= $semester === 'Genap' ? 'selected' : '' ?>>Genap</option>
                    <option value="Tahunan" <?= $semester === 'Tahunan' ? 'selected' : '' ?>>Tahunan</option>
                </select>
                <select name="jenis" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($listJenis as $j): ?>
                        <option value="<?= $j['Id'] ?>" <?= (string) $idJenis === (string) $j['Id'] ? 'selected' : '' ?>>
                            <?= esc($j['NamaPenghargaan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Status</option>
                    <option value="final" <?= $status === 'final' ? 'selected' : '' ?>>Final</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm mb-1"><i class="fas fa-search mr-1"></i> Tampilkan</button>
            </form>

            <div class="mb-1">
                <?php $qs = http_build_query(['tahun_ajaran' => $idTahunAjaran, 'semester' => $semester, 'jenis' => $idJenis, 'status' => $status]); ?>
                <a href="<?= base_url('backend/penghargaan/laporan/export-csv?' . $qs) ?>" class="btn btn-success btn-sm mr-1">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel (CSV)
                </a>
                <a href="<?= base_url('backend/penghargaan/laporan/export-pdf?' . $qs) ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rekap per Jenis -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-chart-pie text-info mr-2"></i>Rekap per Jenis Penghargaan</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0 text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Jenis Penghargaan</th>
                                <th class="text-right">Jumlah Penerima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekapJenis)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($rekapJenis as $nama => $jml): ?>
                                    <tr>
                                        <td><?= esc($nama) ?></td>
                                        <td class="text-right"><span class="badge badge-primary"><?= esc($jml) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Santri Berprestasi -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-trophy text-warning mr-2"></i>Top Santri Penerima Terbanyak</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0 text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Santri</th>
                                <th>Kelas</th>
                                <th class="text-right">Total Penghargaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topSantri)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topSantri as $ts): ?>
                                    <tr>
                                        <td><strong><?= esc($ts['NamaSantri']) ?></strong></td>
                                        <td><?= esc($ts['NamaKelas']) ?></td>
                                        <td class="text-right"><span class="badge badge-warning"><?= esc($ts['Total']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Penerima -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold"><i class="fas fa-list text-primary mr-2"></i>Daftar Penerima Penghargaan (<?= count($penerima) ?>)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Nomor Seri</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>Penghargaan</th>
                            <th>Periode</th>
                            <th class="text-center">Rank</th>
                            <th class="text-right">Nilai</th>
                            <th class="text-right">% Hadir</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($penerima)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data penerima.</td></tr>
                        <?php else: ?>
                            <?php foreach ($penerima as $r): ?>
                                <tr>
                                    <td><code><?= esc($r['NomorSeri']) ?></code></td>
                                    <td><strong><?= esc($r['NamaSantri']) ?></strong></td>
                                    <td><?= esc($r['NamaKelas'] ?? '-') ?></td>
                                    <td><?= esc($r['NamaPenghargaan']) ?></td>
                                    <td><?= esc($r['Semester']) ?> · <?= esc($r['IdTahunAjaran']) ?></td>
                                    <td class="text-center">#<?= esc($r['Peringkat']) ?></td>
                                    <td class="text-right"><?= number_format((float) $r['NilaiRataRata'], 2) ?></td>
                                    <td class="text-right"><?= number_format((float) $r['PersenKehadiran'], 2) ?>%</td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $r['StatusSertifikat'] === 'final' ? 'success' : ($r['StatusSertifikat'] === 'draft' ? 'warning' : 'danger') ?>">
                                            <?= esc(ucfirst($r['StatusSertifikat'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold"><i class="fas fa-history text-secondary mr-2"></i>Riwayat Penerbitan &amp; Audit Trail</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Jenis Penghargaan</th>
                            <th>Periode</th>
                            <th>Penerima</th>
                            <th>Di-generate Oleh</th>
                            <th>Di-release Oleh</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditTrail)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat penerbitan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($auditTrail as $a): ?>
                                <tr>
                                    <td><?= esc($a['NamaPenghargaan']) ?></td>
                                    <td><?= esc($a['Semester']) ?> · <?= esc($a['IdTahunAjaran']) ?></td>
                                    <td><?= esc($a['JumlahPenerima']) ?> santri</td>
                                    <td><?= esc($a['GeneratorName'] ?? 'System') ?> <small class="text-muted">(<?= esc(date('d/m/Y H:i', strtotime($a['TanggalGenerate']))) ?>)</small></td>
                                    <td>
                                        <?php if (!empty($a['TanggalRelease'])): ?>
                                            <?= esc($a['ReleaserName'] ?? 'Admin') ?> <small class="text-muted">(<?= esc(date('d/m/Y H:i', strtotime($a['TanggalRelease']))) ?>)</small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-<?= $a['Status'] === 'final' ? 'success' : ($a['Status'] === 'draft' ? 'warning' : 'danger') ?>"><?= esc(ucfirst($a['Status'])) ?></span></td>
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
