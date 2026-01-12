<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header bg-gradient-primary">
            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Statistik Presensi Guru</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="<?= base_url('backend/guru/statistik-presensi') ?>">
                <div class="row">
                    <!-- Periode Filter -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="periode"><i class="fas fa-calendar-alt mr-1"></i>Periode</label>
                            <select name="periode" id="periode" class="form-control" onchange="this.form.submit()">
                                <option value="harian" <?= ($filterPeriode ?? '') == 'harian' ? 'selected' : '' ?>>Per Hari (7 Hari)</option>
                                <option value="mingguan" <?= ($filterPeriode ?? '') == 'mingguan' ? 'selected' : '' ?>>Per Minggu (4 Minggu)</option>
                                <option value="bulanan" <?= ($filterPeriode ?? 'bulanan') == 'bulanan' ? 'selected' : '' ?>>Per Bulan</option>
                                <option value="semester" <?= ($filterPeriode ?? '') == 'semester' ? 'selected' : '' ?>>Per Semester</option>
                                <option value="tahunan" <?= ($filterPeriode ?? '') == 'tahunan' ? 'selected' : '' ?>>Per Tahun Ajaran</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tipe Kegiatan Filter -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tipe_kegiatan"><i class="fas fa-tags mr-1"></i>Jenis Jadwal</label>
                            <select name="tipe_kegiatan" id="tipe_kegiatan" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="harian" <?= ($filterTipeKegiatan ?? '') == 'harian' ? 'selected' : '' ?>>Harian</option>
                                <option value="mingguan" <?= ($filterTipeKegiatan ?? '') == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                                <option value="bulanan" <?= ($filterTipeKegiatan ?? '') == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                                <option value="tahunan" <?= ($filterTipeKegiatan ?? '') == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                                <option value="sekali" <?= ($filterTipeKegiatan ?? '') == 'sekali' ? 'selected' : '' ?>>Sekali</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kegiatan Filter -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kegiatan"><i class="fas fa-clipboard-list mr-1"></i>Kegiatan</label>
                            <select name="kegiatan" id="kegiatan" class="form-control select2" style="width: 100%;">
                                <option value="">-- Semua Kegiatan --</option>
                                <?php foreach ($kegiatanList as $kegiatan): ?>
                                    <option value="<?= $kegiatan['Id'] ?>" <?= ($filterKegiatan == $kegiatan['Id']) ? 'selected' : '' ?>>
                                        <?= esc($kegiatan['NamaKegiatan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Lingkup Filter -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="lingkup"><i class="fas fa-globe mr-1"></i>Lingkup</label>
                            <select name="lingkup" id="lingkup" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="Umum" <?= ($filterLingkup ?? '') == 'Umum' ? 'selected' : '' ?>>Umum (Semua TPQ)</option>
                                <option value="TPQ" <?= ($filterLingkup ?? '') == 'TPQ' ? 'selected' : '' ?>>TPQ Sendiri</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($isAdmin && !empty($tpqList)): ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tpq"><i class="fas fa-school mr-1"></i>TPQ</label>
                            <select name="tpq" id="tpq" class="form-control select2" style="width: 100%;">
                                <option value="">-- Semua TPQ --</option>
                                <?php foreach ($tpqList as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" <?= ($filterTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                        <?= esc($tpq['NamaTpq']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_dari"><i class="fas fa-calendar mr-1"></i>Dari</label>
                            <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="<?= $filterTanggalDari ?>">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_sampai"><i class="fas fa-calendar mr-1"></i>Sampai</label>
                            <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="<?= $filterTanggalSampai ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="<?= base_url('backend/guru/statistik-presensi') ?>" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <hr>

            <!-- Summary Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info">
                        <div class="inner">
                            <h3><?= $summaryStats['totalHariEfektif'] ?? 0 ?></h3>
                            <p>Hari Efektif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3><?= $summaryStats['rataRataKehadiran'] ?? 0 ?><sup style="font-size: 20px">%</sup></h3>
                            <p>Rata-rata Kehadiran</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3><?= $summaryStats['totalKegiatan'] ?? 0 ?></h3>
                            <p>Total Kegiatan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3><?= $summaryStats['totalGuru'] ?? 0 ?></h3>
                            <p>Total Guru</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Hadir</span>
                            <span class="info-box-number"><?= $stats['hadir'] ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description"><?= $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100, 1) : 0 ?>% dari total</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Izin</span>
                            <span class="info-box-number"><?= $stats['izin'] ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $stats['total'] > 0 ? round(($stats['izin'] / $stats['total']) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description"><?= $stats['total'] > 0 ? round(($stats['izin'] / $stats['total']) * 100, 1) : 0 ?>% dari total</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Sakit</span>
                            <span class="info-box-number"><?= $stats['sakit'] ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $stats['total'] > 0 ? round(($stats['sakit'] / $stats['total']) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description"><?= $stats['total'] > 0 ? round(($stats['sakit'] / $stats['total']) * 100, 1) : 0 ?>% dari total</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Alfa</span>
                            <span class="info-box-number"><?= $stats['alfa'] ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $stats['total'] > 0 ? round(($stats['alfa'] / $stats['total']) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description"><?= $stats['total'] > 0 ? round(($stats['alfa'] / $stats['total']) * 100, 1) : 0 ?>% dari total</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Trend Chart -->
                <div class="col-md-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Tren Kehadiran</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="trendChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Distribution Chart -->
                <div class="col-md-4">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Distribusi Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="distributionChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Per-Kegiatan Chart -->
                <div class="col-md-4">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Kehadiran Per Kegiatan</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($perKegiatanStats)): ?>
                                <canvas id="perKegiatanChart" style="min-height: 250px;"></canvas>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data kegiatan dalam rentang tanggal ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guru Ranking Table - Full Width Row -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy mr-2"></i>Ranking Kehadiran Guru</h3>
                            <div class="card-tools">
                                <?php if (!empty($guruRanking)): ?>
                                <button type="button" class="btn btn-success btn-sm" id="btnWaList" title="Kirim Rekap ke WhatsApp">
                                    <i class="fab fa-whatsapp"></i> Kirim Rekap
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">

                            <?php if (!empty($guruRanking)): ?>
                                <table id="guruRankingTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Nama Guru</th>
                                            <th>TPQ</th>
                                            <th class="text-center">Hadir</th>
                                            <th class="text-center">Izin</th>
                                            <th class="text-center">Sakit</th>
                                            <th class="text-center">Alfa</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1;
                                        foreach ($guruRanking as $guru): 
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank <= 3): ?>
                                                        <span class="badge badge-<?= $rank == 1 ? 'warning' : ($rank == 2 ? 'secondary' : 'danger') ?>">
                                                            <i class="fas fa-medal"></i> <?= $rank ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <?= $rank ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc(ucwords(strtolower($guru['nama']))) ?></td>
                                                <td><small><?= esc($guru['tpq']) ?></small></td>
                                                <td class="text-center"><span class="badge badge-success"><?= $guru['hadir'] ?></span></td>
                                                <td class="text-center"><span class="badge badge-warning"><?= $guru['izin'] ?></span></td>
                                                <td class="text-center"><span class="badge badge-info"><?= $guru['sakit'] ?></span></td>
                                                <td class="text-center"><span class="badge badge-danger"><?= $guru['alfa'] ?></span></td>
                                                <td class="text-center"><strong><?= $guru['total'] ?></strong></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $guru['persentase'] >= 80 ? 'success' : ($guru['persentase'] >= 60 ? 'warning' : 'danger') ?>">
                                                        <?= $guru['persentase'] ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php 
                                        $rank++;
                                        endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> Tidak ada data guru dalam rentang tanggal ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


            <?php if (!empty($locationData)): ?>
            <!-- Map Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i>Peta Sebaran Lokasi Absensi</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="attendance-map" style="height: 400px; border-radius: 8px;"></div>
                            <p class="text-muted text-center mt-2 mb-0 small">
                                <i class="fas fa-info-circle mr-1"></i>
                                Menampilkan <?= count($locationData) ?> lokasi absensi guru
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-dark collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-table mr-2"></i>Detail Lokasi Absensi</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="locationTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>TPQ</th>
                                        <th>Status</th>
                                        <th>Waktu Absensi</th>
                                        <th>Koordinat</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($locationData as $loc): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($loc['nama']) ?></td>
                                            <td><?= esc($loc['tpq']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $loc['status'] == 'Hadir' ? 'success' : ($loc['status'] == 'Izin' ? 'warning' : 'primary') ?>">
                                                    <?= esc($loc['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($loc['waktu']) ?></td>
                                            <td>
                                                <small><?= $loc['lat'] ?>, <?= $loc['lng'] ?></small>
                                            </td>
                                            <td><?= esc($loc['keterangan']) ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($stats['total'] == 0): ?>
                <div class="alert alert-info">
                    <i class="icon fas fa-info-circle"></i>
                    <?php if (empty($filterTanggalDari) && empty($filterTanggalSampai) && empty($filterKegiatan)): ?>
                        Silakan pilih filter untuk melihat statistik presensi.
                    <?php else: ?>
                        Tidak ada data absensi dalam rentang tanggal yang dipilih.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal WhatsApp Rekap Kehadiran -->
<div class="modal fade" id="waRekapModal" tabindex="-1" role="dialog" aria-labelledby="waRekapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="waRekapModalLabel"><i class="fab fa-whatsapp mr-2"></i>Kirim Rekap Kehadiran via WhatsApp</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($isAdmin && !empty($tpqList)): ?>
                <div class="form-group" id="waTpqSelectGroup">
                    <label><i class="fas fa-school mr-1"></i>Pilih TPQ</label>
                    <select class="form-control" id="waTpqSelect">
                        <option value="">-- Semua TPQ --</option>
                        <?php foreach ($tpqList as $tpq): ?>
                            <option value="<?= esc($tpq['IdTpq']) ?>" data-nama="<?= esc($tpq['NamaTpq']) ?>">
                                <?= esc($tpq['NamaTpq']) ?><?= !empty($tpq['KelurahanDesa']) ? ' - ' . esc($tpq['KelurahanDesa']) : '' ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label><i class="fas fa-paper-plane mr-1"></i>Kirim Ke</label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" id="btnModeLaporan">
                            <input type="radio" name="waMode" value="laporan" checked> <i class="fas fa-list mr-1"></i>Laporan List
                        </label>
                        <label class="btn btn-outline-primary" id="btnModeIndividu">
                            <input type="radio" name="waMode" value="individu"> <i class="fas fa-user mr-1"></i>Individu
                        </label>
                    </div>
                    <small class="text-muted mt-1 d-block" id="waModeHint">Kirim rekap list semua guru</small>
                </div>

                <div class="form-group" id="waGuruSelectGroup" style="display: none;">
                    <label>Pilih Guru</label>
                    <select class="form-control select2-wa" id="waGuruSelect" style="width: 100%;">
                        <option value="">-- Pilih Guru --</option>
                        <?php if (!empty($guruRanking)): ?>
                            <?php foreach ($guruRanking as $guru): ?>
                                <option value="<?= esc($guru['id']) ?>" 
                                        data-nama="<?= esc($guru['nama']) ?>"
                                        data-tpq="<?= esc($guru['tpq']) ?>"
                                        data-idtpq="<?= esc($guru['idTpq'] ?? '') ?>"
                                        data-hadir="<?= $guru['hadir'] ?>"
                                        data-izin="<?= $guru['izin'] ?>"
                                        data-sakit="<?= $guru['sakit'] ?>"
                                        data-alfa="<?= $guru['alfa'] ?>"
                                        data-total="<?= $guru['total'] ?>"
                                        data-persentase="<?= $guru['persentase'] ?>">
                                    <?= esc(ucwords(strtolower($guru['nama']))) ?> (<?= $guru['persentase'] ?>%)
                                </option>
                            <?php endforeach; ?>


                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pesan</label>
                    <textarea class="form-control" id="waRekapMessage" rows="12" style="font-family: monospace;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="sendWaRekap()"><i class="fab fa-whatsapp"></i> Kirim WhatsApp</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('scripts'); ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- pdfmake for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<!-- DataTables Buttons HTML5 and Print -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Initialize DataTable for Location
    <?php if (!empty($locationData)): ?>
    $('#locationTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "order": [[1, 'asc']]
    });
    <?php endif; ?>

    // Initialize DataTable for Guru Ranking with Export Buttons
    <?php if (!empty($guruRanking)): ?>
    $('#guruRankingTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "order": [[8, 'desc']], // Order by percentage descending
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"B>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                filename: 'Ranking_Kehadiran_Guru_<?= date('d-m-Y') ?>',
                title: 'Ranking Kehadiran Guru',
                messageTop: 'Periode: <?= $filterTanggalDari ?> s/d <?= $filterTanggalSampai ?>',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                filename: 'Ranking_Kehadiran_Guru_<?= date('d-m-Y') ?>',
                title: 'Ranking Kehadiran Guru',
                messageTop: 'Periode: <?= $filterTanggalDari ?> s/d <?= $filterTanggalSampai ?>',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm',
                title: 'Ranking Kehadiran Guru',
                messageTop: 'Periode: <?= $filterTanggalDari ?> s/d <?= $filterTanggalSampai ?>',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                }
            },
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    }
                }
            }
        ],

        "language": {
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "search": "Cari:",
            "paginate": {
                "first": "Awal",
                "last": "Akhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
    <?php endif; ?>


    // Chart.js - Trend Chart
    <?php if (!empty($trendData['labels'])): ?>
    var trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trendData['labels']) ?>,
            datasets: [{
                label: 'Jumlah Hadir',
                data: <?= json_encode($trendData['hadir']) ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#28a745',
                pointRadius: 4
            }, {
                label: '% Kehadiran',
                data: <?= json_encode($trendData['persentase']) ?>,
                borderColor: '#007bff',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false,
                tension: 0.3,
                pointBackgroundColor: '#007bff',
                pointRadius: 3,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 1) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Persentase (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    <?php endif; ?>

    // Chart.js - Distribution Chart (Doughnut)
    var distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
            datasets: [{
                data: [<?= $stats['hadir'] ?>, <?= $stats['izin'] ?>, <?= $stats['sakit'] ?>, <?= $stats['alfa'] ?>],
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var value = context.parsed;
                            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Chart.js - Per-Kegiatan Chart
    <?php if (!empty($perKegiatanStats)): ?>
    var perKegiatanCtx = document.getElementById('perKegiatanChart').getContext('2d');
    new Chart(perKegiatanCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(function($k) { return $k['nama']; }, array_slice($perKegiatanStats, 0, 8))) ?>,
            datasets: [{
                label: '% Kehadiran',
                data: <?= json_encode(array_map(function($k) { return $k['persentase']; }, array_slice($perKegiatanStats, 0, 8))) ?>,
                backgroundColor: <?= json_encode(array_map(function($k) { 
                    return $k['persentase'] >= 80 ? '#28a745' : ($k['persentase'] >= 60 ? '#ffc107' : '#dc3545');
                }, array_slice($perKegiatanStats, 0, 8))) ?>,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Kehadiran: ' + context.parsed.x + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Persentase Kehadiran (%)'
                    }
                }
            }
        }
    });
    <?php endif; ?>

    // Initialize Map
    <?php if (!empty($locationData)): ?>
    const locationData = <?= json_encode($locationData) ?>;
    
    if (locationData && locationData.length > 0) {
        let sumLat = 0, sumLng = 0;
        locationData.forEach(loc => {
            sumLat += loc.lat;
            sumLng += loc.lng;
        });
        const centerLat = sumLat / locationData.length;
        const centerLng = sumLng / locationData.length;

        const map = L.map('attendance-map').setView([centerLat, centerLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        const markers = L.markerClusterGroup({
            chunkedLoading: true,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        const iconColors = {
            'Hadir': '#28a745',
            'Izin': '#ffc107',
            'Sakit': '#007bff'
        };

        locationData.forEach(loc => {
            const color = iconColors[loc.status] || '#6c757d';
            
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [25, 25],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([loc.lat, loc.lng], { icon: customIcon });

            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 class="mb-2 font-weight-bold">${loc.nama}</h6>
                    <p class="mb-1 small"><i class="fas fa-school mr-1"></i> ${loc.tpq}</p>
                    <p class="mb-1 small"><i class="far fa-clock mr-1"></i> ${loc.waktu}</p>
                    <p class="mb-1 small">
                        <span class="badge badge-${loc.status === 'Hadir' ? 'success' : (loc.status === 'Izin' ? 'warning' : 'primary')}">${loc.status}</span>
                    </p>
                    ${loc.keterangan ? `<p class="mb-0 small text-muted font-italic">"${loc.keterangan}"</p>` : ''}
                </div>
            `;

            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        });

        map.addLayer(markers);

        if (locationData.length > 1) {
            const bounds = markers.getBounds();
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
    <?php endif; ?>

    // ========== WhatsApp Rekap Functions ==========
    // Data guru ranking dari PHP
    var guruRankingData = <?= json_encode($guruRanking ?? []) ?>;
    var periodeTanggal = '<?= $filterTanggalDari ?? '' ?> s/d <?= $filterTanggalSampai ?? '' ?>';
    
    // Initialize Select2 for WA modal
    $('.select2-wa').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#waRekapModal')
    });

    // Open WA Modal
    $('#btnWaList').click(function() {
        $('#waTpqSelect').val('');
        $('input[name="waMode"][value="laporan"]').prop('checked', true);
        $('#btnModeLaporan').addClass('active');
        $('#btnModeIndividu').removeClass('active');
        $('#waGuruSelectGroup').hide();
        $('#waGuruSelect').val('').trigger('change');
        filterGuruByTpq(); // Reset filter
        updateModeHint();
        updateWaRekapMessage();
        $('#waRekapModal').modal('show');
    });

    // Filter guru by TPQ (for Admin)
    $('#waTpqSelect').change(function() {
        filterGuruByTpq();
        updateModeHint();
        updateWaRekapMessage();
    });

    function filterGuruByTpq() {
        var selectedTpqId = $('#waTpqSelect').val();
        
        $('#waGuruSelect option').each(function() {
            var optionTpqId = $(this).data('idtpq') || '';
            // Show if: no TPQ selected, or idTpq matches, or this is the placeholder option
            if (!selectedTpqId || optionTpqId == selectedTpqId || $(this).val() == '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset selection
        $('#waGuruSelect').val('').trigger('change');
    }


    // Toggle mode Laporan List / Individu
    $('input[name="waMode"]').change(function() {
        var mode = $(this).val();
        if (mode == 'individu') {
            $('#waGuruSelectGroup').show();
            filterGuruByTpq(); // Apply TPQ filter to guru list
        } else {
            $('#waGuruSelectGroup').hide();
            $('#waGuruSelect').val('').trigger('change');
        }
        updateModeHint();
        updateWaRekapMessage();
    });


    // Update hint text berdasarkan mode dan TPQ yang dipilih
    function updateModeHint() {
        var mode = $('input[name="waMode"]:checked').val();
        var selectedTpqName = $('#waTpqSelect option:selected').data('nama') || '';
        var hint = '';
        
        if (mode == 'laporan') {
            if (selectedTpqName) {
                hint = 'Kirim rekap list guru dari TPQ: ' + selectedTpqName;
            } else {
                hint = 'Kirim rekap list semua guru';
            }
        } else {
            if (selectedTpqName) {
                hint = 'Pilih guru dari TPQ: ' + selectedTpqName;
            } else {
                hint = 'Pilih guru untuk kirim rekap individu';
            }
        }
        $('#waModeHint').text(hint);
    }


    // Update message when guru selection changes
    $('#waGuruSelect').change(function() {
        updateWaRekapMessage();
    });


    function updateWaRekapMessage() {
        var mode = $('input[name="waMode"]:checked').val();
        var selectedGuru = $('#waGuruSelect option:selected');
        var guruId = selectedGuru.val();
        var selectedTpq = $('#waTpqSelect').val();
        var selectedTpqName = $('#waTpqSelect option:selected').data('nama') || '';
        
        var msg = "Assalamualaikum";
        
        if (mode == 'laporan') {
            // Pesan untuk laporan list: Daftar guru (filtered by TPQ if selected)

            msg += " Bapak/Ibu Guru,\n\n";
            msg += "*📊 REKAP KEHADIRAN GURU*\n";
            if (selectedTpqName) {
                msg += "TPQ: " + selectedTpqName + "\n";
            }
            msg += "Periode: " + periodeTanggal + "\n";
            msg += "━━━━━━━━━━━━━━━━━━\n\n";
            
            if (guruRankingData.length > 0) {
                var rank = 1;
                guruRankingData.forEach(function(guru) {
                    // Filter by TPQ if selected
                    if (selectedTpqName && guru.tpq != selectedTpqName) {
                        return; // Skip guru from other TPQ
                    }
                    
                    var medal = rank <= 3 ? ['🥇', '🥈', '🥉'][rank-1] : rank + '.';
                    var namaGuru = guru.nama.split(' ').map(function(w) { 
                        return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); 
                    }).join(' ');
                    
                    msg += medal + " *" + namaGuru + "*\n";
                    msg += "   ✅ Hadir: " + guru.hadir + " | ❌ Alfa: " + guru.alfa + "\n";
                    msg += "   📈 Kehadiran: *" + guru.persentase + "%*\n\n";
                    rank++;
                });
            }
            
            msg += "━━━━━━━━━━━━━━━━━━\n";
            msg += "Terima kasih atas kerjasamanya. 🙏";
            
        } else if (guruId) {
            // Pesan untuk guru individual
            var namaGuru = selectedGuru.data('nama');
            var hadir = selectedGuru.data('hadir');
            var izin = selectedGuru.data('izin');
            var sakit = selectedGuru.data('sakit');
            var alfa = selectedGuru.data('alfa');
            var total = selectedGuru.data('total');
            var persentase = selectedGuru.data('persentase');
            
            namaGuru = namaGuru.split(' ').map(function(w) { 
                return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase(); 
            }).join(' ');
            
            msg += " Ustadz/Ustadzah *" + namaGuru + "*,\n\n";
            msg += "*📊 REKAP KEHADIRAN ANDA*\n";
            msg += "Periode: " + periodeTanggal + "\n";
            msg += "━━━━━━━━━━━━━━━━━━\n\n";
            msg += "✅ Hadir: *" + hadir + "* kali\n";
            msg += "📝 Izin: *" + izin + "* kali\n";
            msg += "🏥 Sakit: *" + sakit + "* kali\n";
            msg += "❌ Alfa: *" + alfa + "* kali\n\n";
            msg += "📊 *Total: " + total + " kali*\n";
            msg += "📈 *Persentase Kehadiran: " + persentase + "%*\n\n";
            msg += "━━━━━━━━━━━━━━━━━━\n";
            msg += "Terima kasih atas dedikasinya. 🙏";
            
        } else {
            // Pesan default: Semua guru (untuk dikirim satu per satu)
            msg += ",\n\nSilakan pilih guru untuk melihat pesan rekap kehadiran.";
        }
        
        $('#waRekapMessage').val(msg);
    }
});

function sendWaRekap() {
    var isGroup = $('#waCheckGroup').is(':checked');
    var message = $('#waRekapMessage').val();
    var url = "";

    if (isGroup) {
        // Tanpa nomor: user pilih kontak sendiri
        url = "https://wa.me/?text=" + encodeURIComponent(message);
    } else {
        var guruId = $('#waGuruSelect').val();
        if (!guruId) {
            toastr.warning('Silakan pilih guru terlebih dahulu atau centang opsi Kirim ke Grup.');
            return;
        }
        // Untuk kirim ke guru individual, kita perlu nomor HP dari controller
        // Sementara, buka dengan pilih kontak
        url = "https://wa.me/?text=" + encodeURIComponent(message);
    }
    
    window.open(url, '_blank');
    $('#waRekapModal').modal('hide');
}
</script>
<?= $this->endSection(); ?>

