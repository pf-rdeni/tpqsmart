<?= $this->extend('frontend/template/publicTemplate'); ?>

<?= $this->section('content'); ?>
    <style>
        .header-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .card-teacher {
            transition: transform 0.2s;
        }
        .card-teacher:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-header {
            background-color: #f4f6f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 5px solid;
        }
        .section-belum { border-color: #dc3545; }
        .section-sudah { border-color: #28a745; }
    </style>

            <?php if (!empty($hasAction) && $hasAction): ?>
                
                <div class="header-title">
                    <h2 class="display-5"><?= esc($kegiatan['NamaKegiatan']) ?></h2>
                    <div class="d-flex justify-content-center flex-wrap align-items-center mb-3 text-muted" style="font-size: 1.1rem; gap: 15px;">
                        <div>
                            <i class="far fa-calendar-alt mr-1"></i> <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?>
                        </div>
                        <div>
                            <span class="badge badge-info" style="font-size: 100%;">
                                <i class="far fa-clock mr-1"></i> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?>
                            </span>
                        </div>
                        <?php if(!empty($kegiatan['Tempat'])): ?>
                        <div>
                            <i class="fas fa-map-marker-alt mr-1 text-danger"></i> <strong><?= esc($kegiatan['Tempat']) ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if(!empty($kegiatan['Detail'])): ?>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <p class="text-muted font-italic bg-light p-2 border rounded">
                                    <i class="fas fa-info-circle mr-1 text-info"></i> <?= nl2br(esc((string)$kegiatan['Detail'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                    $total = $stats['total'] > 0 ? $stats['total'] : 1; // Prevent division by zero
                    $pctHadir = number_format(($stats['hadir'] / $total) * 100, 1);
                    $pctIzin  = number_format(($stats['izin'] / $total) * 100, 1);
                    $pctSakit = number_format(($stats['sakit'] / $total) * 100, 1);
                    $pctAlfa  = number_format(($stats['alfa'] / $total) * 100, 1);
                ?>

                <!-- Statistics Widgets -->
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $stats['total'] ?></h3>
                                <p>Total Guru</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $stats['hadir'] ?> <span style="font-size: 0.6em;">/ <?= $pctHadir ?>%</span></h3>
                                <p>Hadir</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $stats['izin'] ?> <span style="font-size: 0.6em;">/ <?= $pctIzin ?>%</span></h3>
                                <p>Izin</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= $stats['sakit'] ?> <span style="font-size: 0.6em;">/ <?= $pctSakit ?>%</span></h3>
                                <p>Sakit</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-procedures"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= $stats['alfa'] ?> <span style="font-size: 0.6em;">/ <?= $pctAlfa ?>%</span></h3>
                                <p>Belum Hadir</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Kehadiran</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="card-body" style="display: none;">
                                <?php 
                                    // Determine layout mode: General (Many TPQs) vs Specific (Single TPQ)
                                    // If more than 1 TPQ, assume General (Side-by-side: Table | Stacked Charts)
                                    // If 1 TPQ, assume Specific (Stacked: Table / Side-by-side Charts)
                                    $tpqCount = isset($statsTpq) ? count($statsTpq) : 0;
                                    $isGeneral = $tpqCount > 1; 
                                ?>
                                <div class="row">
                                    <div class="<?= $isGeneral ? 'col-md-6' : 'col-12' ?>">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm text-center">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 5%">No</th>
                                                        <th class="text-left">Nama TPQ</th>
                                                        <th style="width: 15%">Total Guru</th>
                                                        <th style="width: 40%">Statistik (Hadir vs Tidak Hadir)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1; if(isset($statsTpq) && !empty($statsTpq)): foreach ($statsTpq as $tpq => $s): 
                                                        $total = $s['total'] > 0 ? $s['total'] : 1;
                                                        $hadir = $s['hadir'];
                                                        $tidakHadir = $s['izin'] + $s['sakit'] + $s['alfa'];
                                                        
                                                        $pctHadir = ($hadir / $total) * 100;
                                                        $pctTidakHadir = ($tidakHadir / $total) * 100;
                                                    ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td class="text-left"><?= esc($tpq) ?></td>
                                                        <td><?= $s['total'] ?></td>
                                                        <td class="align-middle">
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctHadir ?>%" aria-valuenow="<?= $pctHadir ?>" aria-valuemin="0" aria-valuemax="100" title="Hadir: <?= number_format($pctHadir, 1) ?>%">
                                                                    <?= round($pctHadir) > 0 ? round($pctHadir) . '%' : '' ?>
                                                                </div>
                                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $pctTidakHadir ?>%" aria-valuenow="<?= $pctTidakHadir ?>" aria-valuemin="0" aria-valuemax="100" title="Tidak Hadir: <?= number_format($pctTidakHadir, 1) ?>%">
                                                                    <?= round($pctTidakHadir) > 0 ? round($pctTidakHadir) . '%' : '' ?>
                                                                </div>
                                                            </div>
                                                            <small class="d-flex justify-content-between">
                                                                <span class="text-success"><?= $hadir ?> Hadir</span>
                                                                <span class="text-danger"><?= $tidakHadir ?> Tidak</span>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; else: ?>
                                                    <tr>
                                                        <td colspan="4">Tidak ada data statistik.</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="<?= $isGeneral ? 'col-md-6' : 'col-12' ?>">
                                        <div class="row">
                                            <div class="<?= $isGeneral ? 'col-12 mb-4' : 'col-md-6' ?>">
                                                <h6 class="text-center font-weight-bold">Kehadiran</h6>
                                                <canvas id="chart-overall" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                            </div>
                                            <div class="<?= $isGeneral ? 'col-12' : 'col-md-6' ?>">
                                                <h6 class="text-center font-weight-bold">Ketidakhadiran</h6>
                                                <canvas id="chart-absence" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Cari Nama Guru, TPQ, atau Desa...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum Hadir Section -->
                <h4 class="section-header section-belum text-danger"><i class="fas fa-user-times"></i> Belum Hadir (<span id="count-belum"><?= count($belumHadir) ?></span>)</h4>
                <div class="row" id="list-belum">
                    <?php foreach ($belumHadir as $guru): ?>
                        <div class="col-md-3 col-sm-6 teacher-col" id="card-<?= $guru->Id ?>">
                            <div class="card card-outline card-danger card-teacher h-100">
                                <div class="card-body box-profile text-center d-flex flex-column">
                                    <div class="text-center mb-2">
                                         <?php 
                                            // Icon based on gender (fallback)
                                            $iconClass = 'fa-user'; // Default
                                            $iconColor = '#6c757d'; // Gray default
                                            
                                            if (!empty($guru->JenisKelamin)) {
                                                if (strtolower($guru->JenisKelamin) == 'perempuan' || strtolower($guru->JenisKelamin) == 'p') {
                                                    $iconClass = 'fa-user-graduate'; // Muslim woman icon
                                                    $iconColor = '#e83e8c'; // Pink
                                                } else {
                                                    $iconClass = 'fa-user-tie'; // Muslim man icon  
                                                    $iconColor = '#007bff'; // Blue
                                                }
                                            }
                                            
                                            // Check for LinkPhoto
                                            $linkPhoto = $guru->LinkPhoto ?? '';
                                            $hasPhoto = !empty($linkPhoto);
                                         ?>
                                        <?php if ($hasPhoto): ?>
                                            <!-- Profile Photo with fallback to icon (3:4 ratio) -->
                                            <img src="<?= base_url('uploads/profil/user/' . $linkPhoto) ?>" 
                                                 class="profile-user-img img-fluid"
                                                 style="height: 120px; width: 90px; object-fit: cover; cursor: pointer; border: 3px solid <?= $iconColor ?>; border-radius: 8px;"
                                                 onclick="this.closest('.card-body').querySelector('button').click()"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="profile-user-img img-fluid align-items-center justify-content-center"
                                                 style="display: none; height: 120px; width: 90px; background: linear-gradient(135deg, <?= $iconColor ?>15 0%, <?= $iconColor ?>30 100%); cursor: pointer; border: 3px solid <?= $iconColor ?>; border-radius: 8px;"
                                                 onclick="this.closest('.card-body').querySelector('button').click()">
                                                <i class="fas <?= $iconClass ?> fa-3x" style="color: <?= $iconColor ?>;"></i>
                                            </div>
                                        <?php else: ?>
                                            <!-- Icon only (no photo) - 3:4 ratio -->
                                            <div class="profile-user-img img-fluid d-flex align-items-center justify-content-center"
                                                 style="height: 120px; width: 90px; background: linear-gradient(135deg, <?= $iconColor ?>15 0%, <?= $iconColor ?>30 100%); cursor: pointer; border: 3px solid <?= $iconColor ?>; border-radius: 8px;"
                                                 onclick="this.closest('.card-body').querySelector('button').click()">
                                                <i class="fas <?= $iconClass ?> fa-3x" style="color: <?= $iconColor ?>;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="profile-username text-center font-weight-bold"><?= ucwords(strtolower(esc((string)$guru->NamaGuru))) ?></h5>
                                    
                                    <div class="mt-auto">
                                        <?php if(!empty($guru->NamaTpq)): ?>
                                            <p class="text-muted text-center mb-0 small"><i class="fas fa-school mr-1"></i> <?= "TPQ " . ucwords(strtolower(esc((string)$guru->NamaTpq))) ?></p>
                                        <?php endif; ?>
                                        <?php if(!empty($guru->KelurahanDesa)): ?>
                                            <p class="text-muted text-center mb-2 small"><i class="fas fa-map-marker-alt mr-1"></i> <?= "Kel/Desa " . ucwords(strtolower(esc((string)$guru->KelurahanDesa))) ?></p>
                                        <?php endif; ?>

                                        <!-- WhatsApp Link Removed for Privacy -->
                                        
                                        <div class="row mt-3">
                                            <div class="col-6 pl-1 d-flex">
                                                <button 
                                                    onclick="kirimHadirLangsung('<?= $guru->Id ?>')"
                                                    class="btn btn-success btn-block h-100 d-flex flex-column align-items-center justify-content-center py-3" title="Hadir">
                                                    <i class="fas fa-check-circle fa-lg mb-1"></i>
                                                    <b>Hadir</b>
                                                </button>
                                            </div>
                                            <div class="col-6 pr-1 d-flex">
                                                <button 
                                                    onclick="bukaModalTidakHadir(this)"
                                                    data-id="<?= $guru->Id ?>"
                                                    data-nama="<?= ucwords(strtolower(esc((string)$guru->NamaGuru))) ?>"
                                                    data-icon-class="<?= $iconClass ?>"
                                                    data-icon-color="<?= $iconColor ?>"
                                                    data-tpq="<?= esc($guru->NamaTpq) ?>"
                                                    data-loc="<?= esc($guru->KelurahanDesa) ?>"
                                                    class="btn btn-danger btn-block text-white h-100 d-flex flex-column align-items-center justify-content-center py-3" title="Tidak Hadir">
                                                    <i class="fas fa-question-circle fa-lg mb-1"></i>
                                                    <b>Absen</b>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-12 text-center" id="no-data-belum" style="display:none;"><p class="text-muted">Tidak ada data ditemukan</p></div>
                </div>
                <!-- Pagination for Belum Hadir -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination-belum"></ul>
                </nav>


                <hr class="my-4">

                <!-- Sudah Hadir Section -->
                <h4 class="section-header section-sudah text-success"><i class="fas fa-user-check"></i> Sudah Hadir (<span id="count-sudah"><?= count($sudahHadir) ?></span>)</h4>
                
                <!-- Filter Buttons for Sudah Hadir -->
                <?php 
                    $countHadir = 0;
                    $countIzin = 0;
                    $countSakit = 0;
                    foreach($sudahHadir as $g) {
                        if($g->StatusKehadiran == 'Hadir') $countHadir++;
                        elseif($g->StatusKehadiran == 'Izin') $countIzin++;
                        elseif($g->StatusKehadiran == 'Sakit') $countSakit++;
                    }
                ?>
                <div class="mb-3" id="filter-buttons-sudah">
                    <button class="btn btn-info btn-sm mr-2" data-status="all" data-color="info" onclick="updateSudahHadirFilter('all', this)">Semua (<?= count($sudahHadir) ?>)</button>
                    <button class="btn btn-outline-success btn-sm mr-2" data-status="Hadir" data-color="success" onclick="updateSudahHadirFilter('Hadir', this)">Hadir (<?= $countHadir ?>)</button>
                    <button class="btn btn-outline-warning btn-sm mr-2" data-status="Izin" data-color="warning" onclick="updateSudahHadirFilter('Izin', this)">Izin (<?= $countIzin ?>)</button>
                    <button class="btn btn-outline-primary btn-sm" data-status="Sakit" data-color="primary" onclick="updateSudahHadirFilter('Sakit', this)">Sakit (<?= $countSakit ?>)</button>
                </div>

                <div class="row" id="list-sudah">
                    <?php foreach ($sudahHadir as $guru): ?>
                        <?php
                            $status = $guru->StatusKehadiran;
                            $cardClass = 'card-success'; // Default Hadir
                            $badgeClass = 'badge-success';

                            if ($status == 'Izin') {
                                $cardClass = 'card-warning';
                                $badgeClass = 'badge-warning';
                            } elseif ($status == 'Sakit') {
                                $cardClass = 'card-primary'; // Blue
                                $badgeClass = 'badge-primary';
                            }
                        ?>
                        <div class="col-md-3 col-sm-6 teacher-col" data-status="<?= $status ?>">
                            <div class="card card-outline <?= $cardClass ?> h-100">
                                <div class="card-body box-profile d-flex flex-column">
                                    <div class="text-center mb-2">
                                        <?php 
                                            // Icon based on gender (fallback)
                                            $iconClass = 'fa-user'; // Default
                                            $iconColor = '#6c757d'; // Gray default
                                            
                                            if (!empty($guru->JenisKelamin)) {
                                                if (strtolower($guru->JenisKelamin) == 'perempuan' || strtolower($guru->JenisKelamin) == 'p') {
                                                    $iconClass = 'fa-user-graduate'; // Muslim woman icon
                                                    $iconColor = '#e83e8c'; // Pink
                                                } else {
                                                    $iconClass = 'fa-user-tie'; // Muslim man icon
                                                    $iconColor = '#007bff'; // Blue
                                                }
                                            }
                                            
                                            // Check for LinkPhoto
                                            $linkPhoto = $guru->LinkPhoto ?? '';
                                            $hasPhoto = !empty($linkPhoto);
                                        ?>
                                        <?php if ($hasPhoto): ?>
                                            <!-- Profile Photo with fallback to icon (3:4 ratio) -->
                                            <img src="<?= base_url('uploads/profil/user/' . $linkPhoto) ?>" 
                                                 style="width: 48px; height: 64px; object-fit: cover; border: 2px solid <?= $iconColor ?>; border-radius: 6px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                            <div class="align-items-center justify-content-center" 
                                                 style="display: none; width: 48px; height: 64px; background: linear-gradient(135deg, <?= $iconColor ?>15 0%, <?= $iconColor ?>30 100%); border: 2px solid <?= $iconColor ?>; border-radius: 6px;">
                                                <i class="fas <?= $iconClass ?> fa-lg" style="color: <?= $iconColor ?>;"></i>
                                            </div>
                                        <?php else: ?>
                                            <!-- Icon only (no photo) - 3:4 ratio -->
                                            <div class="d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 48px; height: 64px; background: linear-gradient(135deg, <?= $iconColor ?>15 0%, <?= $iconColor ?>30 100%); border: 2px solid <?= $iconColor ?>; border-radius: 6px;">
                                                <i class="fas <?= $iconClass ?> fa-lg" style="color: <?= $iconColor ?>;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="text-center font-weight-bold mb-1" style="font-size: 1rem;"><?= ucwords(strtolower(esc((string)$guru->NamaGuru))) ?></h5>
                                    
                                    <div class="mt-auto">
                                        <?php if(!empty($guru->NamaTpq)): ?>
                                            <p class="text-muted text-center mb-0 small"><i class="fas fa-school mr-1"></i> <?= ucwords(strtolower(esc((string)$guru->NamaTpq))) ?></p>
                                        <?php endif; ?>
                                        
                                        <p class="text-muted text-center small mb-0 mt-2">
                                            <i class="far fa-clock"></i> <?= date('H:i', strtotime($guru->WaktuAbsen ?? 'now')) ?>
                                            <span class="badge <?= $badgeClass ?> ml-1"><?= $guru->StatusKehadiran ?></span>
                                        </p>
                                        <?php if(!empty($guru->Keterangan) && false): // Hidden as requested ?>
                                            <p class="text-muted text-center small mb-0 font-italic">"<?= esc($guru->Keterangan) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-12 text-center" id="no-data-sudah" style="display:none;"><p class="text-muted">Tidak ada data ditemukan</p></div>
                </div>
                <!-- Pagination for Sudah Hadir -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination-sudah"></ul>
                </nav>

            <?php else: ?>
                <div class="alert alert-warning mt-5 text-center">
                    <h4><i class="icon fas fa-exclamation-triangle"></i> Info</h4>
                    <?= $message ?? 'Tidak ada data.' ?>
                </div>
            <?php endif; ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- ChartJS DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    const ITEMS_PER_PAGE = 8;
    
    // Data for Charts
    const statsData = {
        hadir: <?= $stats['hadir'] ?>,
        izin: <?= $stats['izin'] ?>,
        sakit: <?= $stats['sakit'] ?>,
        alfa: <?= $stats['alfa'] ?>
    };

    let currentSudahHadirFilter = 'all';

    $(document).ready(function() {
        // Register the plugin globally
        Chart.register(ChartDataLabels);

        // ... (existing chart code maps to previous code) ...

        // Chart configuration ...
        const datalabelsConfig = {
            color: '#fff',
            font: {
                weight: 'bold',
                size: 14
            },
            formatter: (value, ctx) => {
                let sum = 0;
                let dataArr = ctx.chart.data.datasets[0].data;
                dataArr.map(data => {
                    sum += data;
                });
                if (sum === 0) return '0%';
                let percentage = (value * 100 / sum).toFixed(1) + "%";
                // Only show if value > 0
                return value > 0 ? percentage : '';
            }
        };

        // Chart 1: Overall (Donut)
        const canvasOverall = document.getElementById('chart-overall');
        if (canvasOverall) {
            const ctxOverall = canvasOverall.getContext('2d');
            new Chart(ctxOverall, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan'],
                    datasets: [{
                        data: [statsData.hadir, statsData.izin, statsData.sakit, statsData.alfa],
                        backgroundColor: ['#28a745', '#ffc107', '#007bff', '#dc3545'],
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10 } },
                        datalabels: datalabelsConfig
                    }
                }
            });
        }

        // Chart 2: Absence Details (Pie)
        const canvasAbsence = document.getElementById('chart-absence');
        if (canvasAbsence) {
            const ctxAbsence = canvasAbsence.getContext('2d');
            new Chart(ctxAbsence, {
                type: 'pie',
                data: {
                    labels: ['Izin', 'Sakit', 'Tanpa Keterangan'],
                    datasets: [{
                        data: [statsData.izin, statsData.sakit, statsData.alfa],
                        backgroundColor: ['#ffc107', '#007bff', '#dc3545'],
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10 } },
                        datalabels: datalabelsConfig
                    }
                }
            });
        }

        // Initialize pagination
        initPagination('list-belum', 'pagination-belum');
        initPagination('list-sudah', 'pagination-sudah');

        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            // Filter both lists
            filterList('list-belum', value);
            filterList('list-sudah', value);

            // Re-initialize pagination after filtering
            initPagination('list-belum', 'pagination-belum');
            initPagination('list-sudah', 'pagination-sudah');
        });
    });

    function updateSudahHadirFilter(status, btn) {
        currentSudahHadirFilter = status;
        
        // Reset all buttons to outline
        $('#filter-buttons-sudah button').each(function() {
            const color = $(this).data('color');
            $(this).removeClass(`btn-${color}`).addClass(`btn-outline-${color}`);
        });

        // Set active button to solid
        const activeColor = $(btn).data('color');
        $(btn).removeClass(`btn-outline-${activeColor}`).addClass(`btn-${activeColor}`);

        // Apply filters
        const searchValue = $('#searchInput').val().toLowerCase();
        filterList('list-sudah', searchValue);
        
        // Re-init pagination
        initPagination('list-sudah', 'pagination-sudah');
    }

    function filterList(listId, value) {
        const list = document.getElementById(listId);
        const items = list.getElementsByClassName('teacher-col');
        let visibleCount = 0;

        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const text = item.textContent || item.innerText;
            
            // Check text match
            const textMatch = text.toLowerCase().indexOf(value) > -1;
            
            // Check status match (only for list-sudah)
            const statusMatch = (listId === 'list-sudah') 
                ? (currentSudahHadirFilter === 'all' || item.dataset.status === currentSudahHadirFilter)
                : true;

            if (textMatch && statusMatch) {
                item.classList.remove('d-none');
                item.classList.add('d-block'); // Ensure it's marked as visible for pagination
                // Mark items as 'matched' or 'unmatched'
                item.dataset.matched = "true";
                visibleCount++;
            } else {
                item.classList.add('d-none');
                item.classList.remove('d-block');
                item.dataset.matched = "false";
            }
        }
        
        // Show/Hide "No Data" message
        if (listId === 'list-belum') {
             $('#no-data-belum').toggle(visibleCount === 0);
        } else {
             $('#no-data-sudah').toggle(visibleCount === 0);
        }
    }

    function initPagination(listId, paginationId) {
        const list = document.getElementById(listId);
        // Only select items that match the search (or all if no search)
        // using selector that checks if style display is not none is tricky with classes.
        // Let's use the data-matched attribute we set, or default to all.
        let items = Array.from(list.getElementsByClassName('teacher-col'));
        
        // Filter by matched status if search is active (checked via dataset)
        // If dataset.matched is undefined, assume true (initial load)
        let visibleItems = items.filter(item => item.dataset.matched !== "false");
        
        const pageCount = Math.ceil(visibleItems.length / ITEMS_PER_PAGE);
        const pagination = document.getElementById(paginationId);
        pagination.innerHTML = '';

        if (pageCount <= 1) {
            // Show all visible items, hide pagination if only 1 page
            visibleItems.forEach(item => item.classList.remove('d-none'));
            return;
        }

        // Show first page
        showPage(visibleItems, 1);

        // Generate Buttons
        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement('li');
            li.className = 'page-item';
            if (i === 1) li.classList.add('active');
            
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerText = i;
            a.onclick = (e) => {
                e.preventDefault();
                showPage(visibleItems, i);
                
                // Update active class
                const currentActive = pagination.querySelector('.active');
                if (currentActive) currentActive.classList.remove('active');
                li.classList.add('active');
            };

            li.appendChild(a);
            pagination.appendChild(li);
        }
    }

    function showPage(items, page) {
        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;

        // First, hide all items involved in this pagination set
        // (We only touch the items that were candidates for this pagination)
        items.forEach(item => item.classList.add('d-none'));

        // Then show the specific slice
        for (let i = start; i < end && i < items.length; i++) {
            items[i].classList.remove('d-none');
        }
    }


    // Function for direct "Hadir" submission
    function kirimHadirLangsung(id) {
        // Show loading state immediately
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang memproses kehadiran...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Call performAbsensi directly
        performAbsensi(id, 'Hadir');
    }

    // Function for "Tidak Hadir" modal
    async function bukaModalTidakHadir(btn) {
        // Extract data from button
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const iconClass = btn.getAttribute('data-icon-class');
        const iconColor = btn.getAttribute('data-icon-color');
        const tpq = btn.getAttribute('data-tpq');
        const loc = btn.getAttribute('data-loc');
        
        // Build HTML for Profile Info with Icon
        let profileHtml = `
            <div class="text-center mb-4">
                <div class="mb-3 d-flex justify-content-center">
                    <div class="d-inline-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px; background: linear-gradient(135deg, ${iconColor}15 0%, ${iconColor}30 100%); border-radius: 50%; border: 3px solid ${iconColor};">
                        <i class="fas ${iconClass} fa-3x" style="color: ${iconColor};"></i>
                    </div>
                </div>
                <h4 class="font-weight-bold mb-2">${nama}</h4>
        `;

        // Helper for Title Case
        const toTitleCase = (str) => {
            return str.replace(/\w\S*/g, function(txt){
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        };

        if (tpq || loc) {
            let infoText = '';
            if (tpq) infoText += `TPQ ${toTitleCase(tpq)}`;
            if (tpq && loc) infoText += ', Kel/Desa ';
            if (loc) infoText += toTitleCase(loc);

            profileHtml += `<p class="text-muted mb-0"><i class="fas fa-school mr-1"></i> ${infoText}</p>`;
        }

        profileHtml += `</div>`;

        await Swal.fire({
            html: `
                ${profileHtml}
                <hr>
                <div class="form-group text-left">
                    <label for="catatan-tidak-hadir" class="font-weight-bold text-muted small">Catatan (Wajib Diisi):</label>
                    <textarea id="catatan-tidak-hadir" class="form-control" rows="3" placeholder="Contoh: Izin sakit demam..."></textarea>
                    <div class="invalid-feedback" style="display:none;">Catatan harus diisi!</div>
                </div>
                <div class="row mt-3">
                    <div class="col-6 px-1">
                        <button id="btn-izin" class="btn btn-warning btn-block font-weight-bold py-2 text-white">Izin</button>
                    </div>
                    <div class="col-6 px-1">
                        <button id="btn-sakit" class="btn btn-danger btn-block font-weight-bold py-2">Sakit</button>
                    </div>
                </div>
                <div id="process-loading" class="mt-3" style="display:none;">
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                    <p class="text-muted small mt-1 mb-0">Menyimpan data...</p> 
                </div>
            `,
            showConfirmButton: false, // Hide default buttons
            showCancelButton: false,  
            showCloseButton: true,
            didOpen: () => {
                const popup = Swal.getHtmlContainer();
                const catatanInput = popup.querySelector('#catatan-tidak-hadir');
                const errorMsg = popup.querySelector('.invalid-feedback');

                const validateAndSubmit = (status) => {
                    const catatan = catatanInput.value.trim();
                    if (!catatan) {
                        catatanInput.classList.add('is-invalid');
                        errorMsg.style.display = 'block';
                        catatanInput.focus();
                        return;
                    }
                    
                    catatanInput.classList.remove('is-invalid');
                    errorMsg.style.display = 'none';

                    // Show loading inside modal
                    const loadingDiv = popup.querySelector('#process-loading');
                    loadingDiv.style.display = 'block';
                    
                    // Disable inputs
                    catatanInput.disabled = true;
                    popup.querySelectorAll('button').forEach(btn => btn.disabled = true);

                    // Submit
                    performAbsensi(id, status, catatan);
                };
                
                popup.querySelector('#btn-sakit').addEventListener('click', () => {
                    validateAndSubmit('Sakit');
                });

                popup.querySelector('#btn-izin').addEventListener('click', () => {
                   validateAndSubmit('Izin');
                });
                
                // Remove error on input
                catatanInput.addEventListener('input', () => {
                    if(catatanInput.value.trim()) {
                        catatanInput.classList.remove('is-invalid');
                        errorMsg.style.display = 'none';
                    }
                });
            }
        });
    }

    function performAbsensi(id, status, keterangan = '') {
        const popup = Swal.getHtmlContainer();
        // Check if we are in the profile card popup (has #process-loading)
        const loadingDiv = popup ? popup.querySelector('#process-loading') : null;

        if (loadingDiv) {
            // Inline loading for Hadir/Sakit
            loadingDiv.style.display = 'block';
            const loadingText = loadingDiv.querySelector('p');
            if (loadingText) loadingText.textContent = 'Mendapatkan lokasi...';
            // Disable buttons
            const buttons = popup.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);
        } else {
            // Standard loading for Izin (since it's a new Swal)
            Swal.fire({
                title: 'Memproses...',
                text: 'Mendapatkan lokasi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Function to submit attendance with or without location
        function submitAttendance(latitude = null, longitude = null) {
            if (loadingDiv) {
                const loadingText = loadingDiv.querySelector('p');
                if (loadingText) loadingText.textContent = 'Menyimpan data...';
            } else {
                Swal.update({
                    text: 'Menyimpan data...'
                });
            }

            $.ajax({
                url: '<?= base_url('presensi/hadir') ?>',
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    keterangan: keterangan,
                    latitude: latitude,
                    longitude: longitude,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status ' + status + ' berhasil dicatat.',
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            willClose: () => {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message || 'Terjadi kesalahan.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan koneksi.',
                        'error'
                    );
                }
            });
        }

        // Try to get geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Success - got location
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    submitAttendance(latitude, longitude);
                },
                function(error) {
                    // Error or denied - continue without location
                    console.warn('Geolocation error:', error.message);
                    submitAttendance(null, null);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            // Browser doesn't support geolocation
            console.warn('Geolocation not supported by this browser.');
            submitAttendance(null, null);
        }
    }
</script>
<?= $this->endSection(); ?>
