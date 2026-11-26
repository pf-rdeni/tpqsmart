<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$taLabel = !empty($IdTahunAjaran) && strlen($IdTahunAjaran) === 8
    ? substr($IdTahunAjaran, 0, 4) . '/' . substr($IdTahunAjaran, 4, 4)
    : ($IdTahunAjaran ?? '-');
$semLabel = $Semester ?? '-';
$updateEmisUrl = base_url('backend/santri/showSantriEmis') . (!empty($IdTpq) && !empty($isAdmin) ? '?IdTpq=' . urlencode($IdTpq) : '');
?>

<div class="col-12">
    <!-- Header & Filter Periode -->
    <div class="card card-primary card-outline shadow-sm mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                <h3 class="card-title font-weight-bold my-1">
                    <i class="fas fa-chart-pie text-primary mr-1"></i> Dashboard Informasi Data EMIS
                    <?php if (empty($isAdmin) && !empty($namaTpqAktif)) : ?>
                        <span class="badge badge-primary ml-2"><?= esc($namaTpqAktif) ?></span>
                    <?php endif; ?>
                    <span class="badge badge-info ml-2">
                        <i class="fas fa-calendar-alt mr-1"></i> TA <?= esc($taLabel) ?> · Semester <?= esc($semLabel) ?>
                    </span>
                </h3>
                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <a href="<?= $updateEmisUrl ?>" class="btn btn-warning btn-sm font-weight-bold">
                        <i class="fas fa-sync-alt mr-1"></i> Buka Update Emis
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-2">
            <form method="get" action="<?= base_url('backend/santri/dashboardEmis') ?>" id="formFilterDashboardEmis" class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Tahun Ajaran</label>
                    <select name="IdTahunAjaran" class="form-control form-control-sm" onchange="this.form.submit()">
                        <?php foreach (($availableTahunAjaran ?? []) as $ta) : ?>
                            <?php $taFmt = strlen($ta) === 8 ? substr($ta, 0, 4) . '/' . substr($ta, 4, 4) : $ta; ?>
                            <option value="<?= esc($ta) ?>" <?= ($IdTahunAjaran === $ta) ? 'selected' : '' ?>><?= esc($taFmt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Semester</label>
                    <select name="Semester" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="Ganjil" <?= ($Semester === 'Ganjil') ? 'selected' : '' ?>>Ganjil</option>
                        <option value="Genap" <?= ($Semester === 'Genap') ? 'selected' : '' ?>>Genap</option>
                    </select>
                </div>
                <?php if (!empty($isAdmin)) : ?>
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Filter Lembaga / TPQ</label>
                        <select name="IdTpq" class="form-control form-control-sm select2bs4" onchange="this.form.submit()">
                            <option value="">-- Semua Lembaga / TPQ --</option>
                            <?php foreach (($dataTpq ?? []) as $t) : ?>
                                <option value="<?= esc($t['IdTpq']) ?>" <?= ((string) $IdTpq === (string) $t['IdTpq']) ? 'selected' : '' ?>>
                                    <?= esc($t['NamaTpq']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Kategori Konflik</label>
                    <select name="kategori" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori Konflik --</option>
                        <option value="terdaftar_lembaga_lain" <?= ($kategori === 'terdaftar_lembaga_lain') ? 'selected' : '' ?>>Terdaftar di Lembaga Lain</option>
                        <option value="aktif_lembaga_sendiri" <?= ($kategori === 'aktif_lembaga_sendiri') ? 'selected' : '' ?>>Sudah Aktif di Lembaga</option>
                        <option value="lainnya" <?= ($kategori === 'lainnya') ? 'selected' : '' ?>>Error Validasi / Lainnya</option>
                    </select>
                </div>
                <div class="col-md-1 col-sm-12 mb-2 text-right">
                    <a href="<?= base_url('backend/santri/dashboardEmis') ?>" class="btn btn-outline-secondary btn-sm btn-block" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Row 1: KPI Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-info shadow-sm mb-0 h-100">
                <div class="inner">
                    <h3><?= number_format($summary['total_santri'] ?? 0) ?></h3>
                    <p>Total Santri Aktif</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="small-box-footer text-left px-3 py-1 small">
                    Periode TA <?= esc($taLabel) ?> (<?= esc($semLabel) ?>)
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-success shadow-sm mb-0 h-100">
                <div class="inner">
                    <h3><?= number_format($summary['emis_selesai'] ?? 0) ?> <small style="font-size: 55%;">(<?= $summary['pct_emis_selesai'] ?? 0 ?>%)</small></h3>
                    <p>Daftar EMIS Selesai</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="small-box-footer text-left px-3 py-1 small">
                    Alamat EMIS Selesai: <strong><?= number_format($summary['alamat_selesai'] ?? 0) ?></strong> (<?= $summary['pct_alamat_selesai'] ?? 0 ?>%)
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-warning shadow-sm mb-0 h-100">
                <div class="inner">
                    <h3><?= number_format($summary['emis_belum'] ?? 0) ?> <small style="font-size: 55%;">(<?= $summary['pct_emis_belum'] ?? 0 ?>%)</small></h3>
                    <p>Daftar EMIS Belum</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
                <a href="<?= $updateEmisUrl ?>" class="small-box-footer">
                    Proses di Update Emis <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-danger shadow-sm mb-0 h-100">
                <div class="inner">
                    <h3><?= number_format($summary['emis_konflik'] ?? 0) ?> <small style="font-size: 55%;">(<?= $summary['pct_emis_konflik'] ?? 0 ?>%)</small></h3>
                    <p>Konflik EMIS (Perlu Eskalasi)</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="#sectionKonflikEmis" class="small-box-footer">
                    Lihat Daftar Konflik <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Row 2: Ringkasan Progres & Rekapitulasi -->
    <div class="row">
        <!-- Panel Kiri: Progres & Update Terakhir -->
        <div class="col-lg-4 mb-3">
            <div class="card card-outline card-info shadow-sm h-100 mb-0">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-tasks text-info mr-1"></i> Ringkasan Progres & Aktivitas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small font-weight-bold mb-1">
                            <span>Pendaftaran Santri EMIS</span>
                            <span><?= $summary['pct_emis_selesai'] ?? 0 ?>% Selesai</span>
                        </div>
                        <div class="progress" style="height: 18px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $summary['pct_emis_selesai'] ?? 0 ?>%" title="Selesai: <?= $summary['emis_selesai'] ?? 0 ?>">
                                <?= ($summary['pct_emis_selesai'] ?? 0) >= 12 ? ($summary['emis_selesai'] ?? 0) : '' ?>
                            </div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $summary['pct_emis_belum'] ?? 0 ?>%" title="Belum: <?= $summary['emis_belum'] ?? 0 ?>">
                                <?= ($summary['pct_emis_belum'] ?? 0) >= 12 ? ($summary['emis_belum'] ?? 0) : '' ?>
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $summary['pct_emis_konflik'] ?? 0 ?>%" title="Konflik: <?= $summary['emis_konflik'] ?? 0 ?>">
                                <?= ($summary['pct_emis_konflik'] ?? 0) >= 10 ? ($summary['emis_konflik'] ?? 0) : '' ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span><i class="fas fa-square text-success"></i> Selesai (<?= $summary['emis_selesai'] ?? 0 ?>)</span>
                            <span><i class="fas fa-square text-warning"></i> Belum (<?= $summary['emis_belum'] ?? 0 ?>)</span>
                            <span><i class="fas fa-square text-danger"></i> Konflik (<?= $summary['emis_konflik'] ?? 0 ?>)</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small font-weight-bold mb-1">
                            <span>Update Alamat EMIS</span>
                            <span><?= $summary['pct_alamat_selesai'] ?? 0 ?>% Selesai</span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: <?= $summary['pct_alamat_selesai'] ?? 0 ?>%"></div>
                        </div>
                        <div class="small text-muted mt-1">
                            <?= number_format($summary['alamat_selesai'] ?? 0) ?> selesai dari <?= number_format($summary['total_santri'] ?? 0) ?> santri
                        </div>
                    </div>

                    <hr>
                    <div class="small">
                        <div class="font-weight-bold text-muted mb-1"><i class="fas fa-history mr-1"></i> Aktivitas Terakhir</div>
                        <div>Update Daftar: <strong><?= !empty($summary['last_update_emis']) ? date('d M Y, H:i', strtotime($summary['last_update_emis'])) : '-' ?></strong></div>
                        <div>Update Alamat: <strong><?= !empty($summary['last_update_alamat']) ? date('d M Y, H:i', strtotime($summary['last_update_alamat'])) : '-' ?></strong></div>
                        <div>Oleh Operator: <strong><?= esc($summary['last_updated_by'] ?? '-') ?></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Kanan: Rekap per TPQ (Admin) atau per Kelas (Operator) -->
        <div class="col-lg-8 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 mb-0">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <?php if (!empty($isAdmin) && empty($IdTpq)) : ?>
                            <i class="fas fa-mosque text-primary mr-1"></i> Rekap Progres per Lembaga (TPQ)
                        <?php else : ?>
                            <i class="fas fa-chalkboard-teacher text-primary mr-1"></i> Rekap Progres per Kelas
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body table-responsive p-2">
                    <?php if (!empty($isAdmin) && empty($IdTpq)) : ?>
                        <table id="tabelRekapTpqEmis" class="table table-sm table-bordered table-hover table-striped w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama TPQ</th>
                                    <th class="text-center">Santri</th>
                                    <th class="text-center">Selesai</th>
                                    <th class="text-center">Belum</th>
                                    <th class="text-center">Konflik</th>
                                    <th class="text-center">Alamat Ok</th>
                                    <th class="text-center">Progres</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach (($rekapTpq ?? []) as $rt) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold"><?= esc($rt['NamaTpq']) ?></td>
                                        <td class="text-center"><?= (int) $rt['total_santri'] ?></td>
                                        <td class="text-center text-success font-weight-bold"><?= (int) $rt['emis_selesai'] ?></td>
                                        <td class="text-center text-warning font-weight-bold"><?= (int) $rt['emis_belum'] ?></td>
                                        <td class="text-center">
                                            <?php if ((int) $rt['emis_konflik'] > 0) : ?>
                                                <span class="badge badge-danger"><?= (int) $rt['emis_konflik'] ?></span>
                                            <?php else : ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-info"><?= (int) $rt['alamat_selesai'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= ($rt['pct_selesai'] >= 80 ? 'success' : ($rt['pct_selesai'] >= 40 ? 'warning' : 'secondary')) ?>">
                                                <?= $rt['pct_selesai'] ?>%
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="<?= base_url('backend/santri/dashboardEmis?IdTahunAjaran=' . urlencode($IdTahunAjaran) . '&Semester=' . urlencode($Semester) . '&IdTpq=' . urlencode($rt['IdTpq'])) ?>" class="btn btn-xs btn-outline-primary" title="Filter Dashboard ke TPQ ini">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('backend/santri/showSantriEmis?IdTpq=' . urlencode($rt['IdTpq'])) ?>" class="btn btn-xs btn-outline-warning" title="Buka Update Emis TPQ ini">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <table id="tabelRekapKelasEmis" class="table table-sm table-bordered table-hover table-striped w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Total Santri</th>
                                    <th class="text-center">Selesai</th>
                                    <th class="text-center">Belum</th>
                                    <th class="text-center">Konflik</th>
                                    <th class="text-center">Alamat Selesai</th>
                                    <th class="text-center">% Progres</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach (($rekapKelas ?? []) as $rk) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold"><?= esc($rk['NamaKelas']) ?></td>
                                        <td class="text-center"><?= (int) $rk['total_santri'] ?></td>
                                        <td class="text-center text-success font-weight-bold"><?= (int) $rk['emis_selesai'] ?></td>
                                        <td class="text-center text-warning font-weight-bold"><?= (int) $rk['emis_belum'] ?></td>
                                        <td class="text-center">
                                            <?php if ((int) $rk['emis_konflik'] > 0) : ?>
                                                <span class="badge badge-danger"><?= (int) $rk['emis_konflik'] ?></span>
                                            <?php else : ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-info"><?= (int) $rk['alamat_selesai'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= ($rk['pct_selesai'] >= 80 ? 'success' : ($rk['pct_selesai'] >= 40 ? 'warning' : 'secondary')) ?>">
                                                <?= $rk['pct_selesai'] ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Laporan Daftar Konflik Data EMIS (Siap Eskalasi) -->
    <div class="card card-danger card-outline shadow-sm" id="sectionKonflikEmis">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                <h3 class="card-title font-weight-bold my-1">
                    <i class="fas fa-exclamation-triangle text-danger mr-1"></i>
                    Laporan Daftar Konflik Data EMIS (Siap Eskalasi)
                    <span class="badge badge-danger ml-2"><?= count($konflikList ?? []) ?> Santri</span>
                </h3>
                <div class="card-tools d-flex align-items-center flex-wrap" style="gap: 6px;">
                    <button type="button" id="btnCopyKonflikMultiple" class="btn btn-success btn-sm" <?= empty($konflikList) ? 'disabled' : '' ?>>
                        <i class="fab fa-whatsapp mr-1"></i> Copy Format WA (Multiple)
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($konflikList)) : ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle mr-1"></i> Tidak ada data santri berstatus <strong>Konflik</strong> pada filter periode ini.
                </div>
            <?php else : ?>
                <div class="alert alert-light border small py-2 mb-3">
                    <i class="fas fa-info-circle text-info mr-1"></i>
                    Centang beberapa baris lalu klik <strong>Copy Format WA (Multiple)</strong> untuk menyalin daftar terpilih, atau langsung klik tanpa mencentang untuk menyalin semua baris yang tampil.
                </div>
                <div class="table-responsive">
                    <table id="tabelKonflikEmis" class="table table-bordered table-hover table-sm w-100">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 28px;"><input type="checkbox" id="checkAllKonflik" title="Pilih semua"></th>
                                <th style="width: 35px;">No</th>
                                <th>TPQ/MDA & Kelas</th>
                                <th>Identitas Santri</th>
                                <th>Kategori</th>
                                <th>Kendala / Pesan Konflik (Catatan)</th>
                                <th>Update Terakhir</th>
                                <th class="text-center" style="width: 135px;">Aksi Eskalasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($konflikList as $k) : ?>
                                <?php
                                $namaTpqRow = $k['NamaTpq'] ?? '-';
                                $namaSantriRow = $k['NamaSantri'] ?? '-';
                                $nikRow = $k['NikSantri'] ?: '-';
                                $nisnRow = $k['NISN'] ?: '-';
                                $kelasRow = $k['NamaKelas'] ?: '-';
                                $kendalaRow = trim((string) ($k['Catatan'] ?? '')) ?: 'Konflik EMIS (tanpa catatan detail)';
                                $ttlRow = trim(($k['TempatLahirSantri'] ?? '') . ', ' . (!empty($k['TanggalLahirSantri']) ? date('d-m-Y', strtotime($k['TanggalLahirSantri'])) : '-'), ', ');
                                ?>
                                <tr data-tpq="<?= esc($namaTpqRow) ?>"
                                    data-nama="<?= esc($namaSantriRow) ?>"
                                    data-nik="<?= esc($nikRow) ?>"
                                    data-kelas="<?= esc($kelasRow) ?>"
                                    data-kendala="<?= esc($kendalaRow) ?>">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="check-konflik-item">
                                    </td>
                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold"><?= esc($namaTpqRow) ?></div>
                                        <span class="badge badge-light border">Kelas: <?= esc($kelasRow) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold"><?= esc($namaSantriRow) ?></div>
                                        <div class="small text-muted">NIK: <code><?= esc($nikRow) ?></code></div>
                                        <div class="small text-muted">NISN: <code><?= esc($nisnRow) ?></code></div>
                                        <div class="small text-muted">TTL: <?= esc($ttlRow) ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-<?= esc($k['KategoriBadge'] ?? 'secondary') ?>">
                                            <?= esc($k['KategoriLabel'] ?? 'Lainnya') ?>
                                        </span>
                                    </td>
                                    <td class="align-middle" style="min-width: 240px;">
                                        <div class="small text-dark"><?= nl2br(esc($kendalaRow)) ?></div>
                                    </td>
                                    <td class="align-middle small text-nowrap">
                                        <div><?= !empty($k['TglUpdateEmis']) ? date('d M Y, H:i', strtotime($k['TglUpdateEmis'])) : '-' ?></div>
                                        <div class="text-muted">Oleh: <?= esc($k['UpdatedBy'] ?? '-') ?></div>
                                    </td>
                                    <td class="text-center align-middle text-nowrap">
                                        <button type="button" class="btn btn-success btn-xs btn-copy-wa-single mb-1" title="Copy Info Eskalasi (Single)">
                                            <i class="fab fa-whatsapp"></i> Copy WA
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-xs btn-tindak-lanjut mb-1"
                                                data-id-santri="<?= esc($k['IdSantri']) ?>"
                                                data-id-tpq="<?= esc($k['IdTpq']) ?>"
                                                data-nama="<?= esc($namaSantriRow) ?>"
                                                data-catatan="<?= esc($kendalaRow) ?>"
                                                title="Tindak Lanjut / Resolusi Konflik">
                                            <i class="fas fa-tools"></i> Tindak Lanjut
                                        </button>
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

<!-- Modal Tindak Lanjut Konflik -->
<div class="modal fade" id="modalResolveKonflikEmis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-tools mr-1"></i> Tindak Lanjut Konflik EMIS</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="resolveIdSantri">
                <input type="hidden" id="resolveIdTpq">
                <div class="mb-2">
                    <label class="small text-muted mb-0">Nama Santri</label>
                    <div id="resolveNamaSantri" class="font-weight-bold"></div>
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold">Ubah Status Menjadi</label>
                    <select id="resolveStatusBaru" class="form-control form-control-sm">
                        <option value="belum">Belum (Siap dicoba ulang di Update Emis)</option>
                        <option value="selesai">Selesai (Sudah ditangani langsung di EMIS)</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Catatan Tindak Lanjut</label>
                    <textarea id="resolveCatatanBaru" class="form-control form-control-sm" rows="3" placeholder="Tuliskan catatan resolusi atau biarkan sesuai catatan sebelumnya..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" id="btnSimpanResolveKonflik" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(function () {
    if ($('.select2bs4').length && $.fn.select2) {
        $('.select2bs4').select2({ theme: 'bootstrap4', width: '100%' });
    }

    if ($('#tabelRekapTpqEmis').length) {
        initializeDataTableUmum('#tabelRekapTpqEmis', true, false, ['excel', 'pdf'], { pageLength: 10, order: [[5, 'desc']] });
    }
    if ($('#tabelRekapKelasEmis').length) {
        initializeDataTableUmum('#tabelRekapKelasEmis', false, false, [], { order: [] });
    }
    if ($('#tabelKonflikEmis').length) {
        initializeDataTableUmum('#tabelKonflikEmis', true, true, [
            {
                extend: 'excel',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
                extend: 'pdf',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
                extend: 'csv',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            }
        ], {
            pageLength: 25,
            order: [[1, 'asc']],
            columnDefs: [{ orderable: false, targets: [0, 7] }]
        });
    }

    function copyToClipboard(text, successMsg) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                Swal.fire({ icon: 'success', title: 'Tersalin!', text: successMsg, timer: 1800, showConfirmButton: false });
            }).catch(function () {
                fallbackCopy(text, successMsg);
            });
        } else {
            fallbackCopy(text, successMsg);
        }
    }

    function fallbackCopy(text, successMsg) {
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();
        document.execCommand('copy');
        $temp.remove();
        Swal.fire({ icon: 'success', title: 'Tersalin!', text: successMsg, timer: 1800, showConfirmButton: false });
    }

    // Format Single: TPQ/MDA, Nama, NIK, Kelas, Kendala
    $(document).on('click', '.btn-copy-wa-single', function () {
        var $tr = $(this).closest('tr');
        var msg = [
            '*ESKALASI KONFLIK EMIS*',
            'TPQ/MDA : ' + $tr.data('tpq'),
            'Nama    : ' + $tr.data('nama'),
            'NIK     : ' + $tr.data('nik'),
            'Kelas   : ' + $tr.data('kelas'),
            'Kendala : "' + $tr.data('kendala') + '"'
        ].join('\n');
        copyToClipboard(msg, 'Info eskalasi santri disalin ke clipboard (siap paste ke WhatsApp).');
    });

    // Check/Uncheck All
    $('#checkAllKonflik').on('change', function () {
        $('.check-konflik-item').prop('checked', $(this).is(':checked'));
    });

    // Format Multiple: TPQ/MDA, Nama, NIK, Kelas, Kendala
    $('#btnCopyKonflikMultiple').on('click', function () {
        var $checked = $('#tabelKonflikEmis tbody tr').filter(function () {
            return $(this).find('.check-konflik-item').is(':checked');
        });
        var $rows = $checked.length > 0 ? $checked : $('#tabelKonflikEmis tbody tr');

        if ($rows.length === 0) {
            Swal.fire({ icon: 'info', title: 'Kosong', text: 'Tidak ada data konflik untuk disalin.' });
            return;
        }

        var blocks = [];
        $rows.each(function (idx) {
            var $tr = $(this);
            blocks.push([
                (idx + 1) + '. TPQ/MDA : ' + $tr.data('tpq'),
                '   Nama    : ' + $tr.data('nama'),
                '   NIK     : ' + $tr.data('nik'),
                '   Kelas   : ' + $tr.data('kelas'),
                '   Kendala : "' + $tr.data('kendala') + '"'
            ].join('\n'));
        });

        var msg = '*ESKALASI KONFLIK EMIS (' + $rows.length + ' Santri)*\n\n' + blocks.join('\n\n');
        copyToClipboard(msg, $rows.length + ' data konflik disalin ke clipboard (siap paste ke WhatsApp).');
    });

    // Modal Tindak Lanjut
    $(document).on('click', '.btn-tindak-lanjut', function () {
        $('#resolveIdSantri').val($(this).data('id-santri'));
        $('#resolveIdTpq').val($(this).data('id-tpq'));
        $('#resolveNamaSantri').text($(this).data('nama'));
        $('#resolveStatusBaru').val('belum');
        $('#resolveCatatanBaru').val($(this).data('catatan'));
        $('#modalResolveKonflikEmis').modal('show');
    });

    $('#btnSimpanResolveKonflik').on('click', function () {
        var btn = $(this);
        btn.prop('disabled', true);
        $.post('<?= base_url('backend/santri/resolveKonflikEmisAjax') ?>', {
            id_santri: $('#resolveIdSantri').val(),
            id_tpq: $('#resolveIdTpq').val(),
            status: $('#resolveStatusBaru').val(),
            catatan: $('#resolveCatatanBaru').val(),
            id_tahun_ajaran: '<?= esc($IdTahunAjaran) ?>',
            semester: '<?= esc($Semester) ?>'
        }, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                $('#modalResolveKonflikEmis').modal('hide');
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(function () { window.location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: (res && res.message) ? res.message : 'Gagal menyimpan perubahan.' });
            }
        }, 'json').fail(function (xhr) {
            btn.prop('disabled', false);
            var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan jaringan.';
            Swal.fire({ icon: 'error', title: 'Gagal', text: errMsg });
        });
    });
});
</script>
<?= $this->endSection(); ?>
