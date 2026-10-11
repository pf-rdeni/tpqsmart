<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .kpi-card {
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .table-matrix thead th {
        vertical-align: middle;
        text-align: center;
        font-size: 12px;
        white-space: nowrap;
    }
    .table-matrix tbody td {
        vertical-align: middle;
        font-size: 13px;
    }
    .col-header-harian {
        background-color: #e8f4fd !important;
        color: #0d6efd !important;
        border-bottom: 2px solid #0d6efd !important;
    }
    .col-header-uts {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border-bottom: 2px solid #ffc107 !important;
    }
    .col-header-uas {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border-bottom: 2px solid #dc3545 !important;
    }
    .col-header-raport {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
        border-bottom: 2px solid #198754 !important;
    }
    .score-cell {
        font-weight: 600;
        text-align: center;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background: #fff !important;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header Page & Navigation -->
    <div class="row mb-3 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-book-open text-success me-2"></i> Buku Nilai & Leger Evaluasi MDTA
                </h4>
                <small class="text-muted">
                    Rekapitulasi lengkap Ujian Harian, Pekanan, UTS, UAS, dan simulasi pembobotan nilai raport
                </small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Jadwal Ujian
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalAturBobot" data-bs-toggle="modal" data-bs-target="#modalAturBobot">
                    <i class="fas fa-sliders-h me-1"></i> Atur Bobot Raport
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak Leger
                </button>
                <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" onclick="triggerTarikNilaiKeRaport()">
                    <i class="fas fa-calculator me-1"></i> Tarik ke Raport
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-outline card-success shadow-sm mb-3 no-print">
        <div class="card-body p-3">
            <form method="get" action="<?= base_url('backend/ujian-mdta/buku-nilai') ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1"><i class="fas fa-school text-success me-1"></i> Pilih Kelas Peserta</label>
                    <select name="kelas" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= $k['IdKelas'] ?>" <?= $idKelas == $k['IdKelas'] ? 'selected' : '' ?>>
                                <?= esc($k['NamaKelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1"><i class="fas fa-book text-primary me-1"></i> Mata Pelajaran MDTA</label>
                    <select name="materi" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                        <?php foreach ($materiList as $m): ?>
                            <option value="<?= $m['IdMateri'] ?>" <?= $idMateri == $m['IdMateri'] ? 'selected' : '' ?>>
                                <?= esc($m['NamaMateri']) ?> (<?= esc($m['Kategori']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1"><i class="fas fa-calendar-alt text-warning me-1"></i> Semester</label>
                    <select name="semester" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                        <option value="1" <?= $semesterParam === '1' || strtolower((string)$semesterParam) === 'ganjil' ? 'selected' : '' ?>>Semester 1 (Ganjil)</option>
                        <option value="2" <?= $semesterParam === '2' || strtolower((string)$semesterParam) === 'genap' ? 'selected' : '' ?>>Semester 2 (Genap)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1"><i class="fas fa-clock text-info me-1"></i> Tahun Ajaran</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= convertTahunAjaran($idTahunAjaran) ?>" readonly>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-2 mb-3">
        <!-- Card 1: Total Santri -->
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card kpi-card border-0 shadow-sm bg-white p-3">
                <small class="text-muted text-uppercase fw-semibold" style="font-size:10px;">Total Santri</small>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalSantri ?></h3>
                    <i class="fas fa-users text-secondary fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Agenda Evaluasi -->
        <div class="col-md-3 col-sm-4 col-6">
            <div class="card kpi-card border-0 shadow-sm bg-white p-3">
                <small class="text-muted text-uppercase fw-semibold" style="font-size:10px;">Agenda Evaluasi Masuk</small>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h4 class="fw-bold mb-0 text-primary">
                        <?= count($agendaUjian) ?> <small class="text-muted fs-6" style="font-size:12px;">Ujian</small>
                    </h4>
                    <span class="badge bg-light text-dark border">
                        <?= count($agendaHarian) ?> UH | <?= count($agendaUTS) ?> UTS | <?= count($agendaUAS) ?> UAS
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Rata-Rata Kelas -->
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card kpi-card border-0 shadow-sm bg-white p-3">
                <small class="text-muted text-uppercase fw-semibold" style="font-size:10px;">Rata-Rata Kelas</small>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="fw-bold mb-0 text-success"><?= $rataRataKelas ?></h3>
                    <i class="fas fa-chart-line text-success fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Kelengkapan Nilai -->
        <div class="col-md-2 col-sm-6 col-6">
            <div class="card kpi-card border-0 shadow-sm bg-white p-3">
                <small class="text-muted text-uppercase fw-semibold" style="font-size:10px;">Nilai Lengkap</small>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h4 class="fw-bold mb-0 text-info"><?= $santriLengkapCount ?> / <?= $totalSantri ?></h4>
                    <i class="fas fa-check-circle text-info fs-4"></i>
                </div>
            </div>
        </div>

        <!-- Card 5: Bobot Aktif -->
        <div class="col-md-3 col-sm-6 col-12">
            <div class="card kpi-card border-0 shadow-sm bg-light p-3 border-start border-4 border-primary cursor-pointer" data-toggle="modal" data-target="#modalAturBobot" data-bs-toggle="modal" data-bs-target="#modalAturBobot" title="Klik untuk ubah persentase bobot">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size:10px;">Rumus Bobot Raport</small>
                        <div class="fw-bold text-primary mt-1" style="font-size:13px;">
                            Harian: <?= $bobotSetting['BobotHarian'] ?>% &bull; UTS: <?= $bobotSetting['BobotUTS'] ?>% &bull; UAS: <?= $bobotSetting['BobotUAS'] ?>%
                        </div>
                    </div>
                    <i class="fas fa-cog text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card: Leger Nilai Terpadu -->
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="fas fa-table text-success me-2"></i>Matriks Leger: <?= esc($materiInfo['NamaMateri'] ?? 'Mata Pelajaran') ?> &mdash; <?= esc($kelasInfo['NamaKelas'] ?? 'Kelas') ?>
                </h5>
            </div>
            <div class="d-flex gap-2 align-items-center no-print">
                <span class="badge bg-light text-dark border"><i class="fas fa-calendar me-1"></i><?= $semesterParam === '2' ? 'Semester Genap' : 'Semester Ganjil' ?></span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle mb-0 table-matrix">
                    <thead>
                        <tr>
                            <!-- Info Santri -->
                            <th rowspan="2" style="width: 40px;">No</th>
                            <th rowspan="2" style="min-width: 110px;">NISN / ID</th>
                            <th rowspan="2" style="min-width: 180px;" class="text-start">Nama Santri</th>

                            <!-- Grup Ujian Harian & Pekanan -->
                            <?php $totalColHarian = max(1, count($agendaHarian)) + 1; ?>
                            <th colspan="<?= $totalColHarian ?>" class="col-header-harian">
                                <i class="fas fa-book-reader me-1"></i> Ujian Harian & Pekanan (Bobot <?= $bobotSetting['BobotHarian'] ?>%)
                            </th>

                            <!-- Grup UTS -->
                            <?php $totalColUTS = max(1, count($agendaUTS)); ?>
                            <th colspan="<?= $totalColUTS ?>" class="col-header-uts">
                                <i class="fas fa-file-alt me-1"></i> UTS (Bobot <?= $bobotSetting['BobotUTS'] ?>%)
                            </th>

                            <!-- Grup UAS -->
                            <?php $totalColUAS = max(1, count($agendaUAS)); ?>
                            <th colspan="<?= $totalColUAS ?>" class="col-header-uas">
                                <i class="fas fa-trophy me-1"></i> UAS (Bobot <?= $bobotSetting['BobotUAS'] ?>%)
                            </th>

                            <!-- Grup Raport -->
                            <th colspan="3" class="col-header-raport">
                                <i class="fas fa-graduation-cap me-1"></i> Nilai Raport Terpadu
                            </th>
                            <th rowspan="2" style="width: 60px;" class="no-print">Detail</th>
                        </tr>
                        <tr>
                            <!-- Subkolom Harian -->
                            <?php if (empty($agendaHarian)): ?>
                                <th class="col-header-harian text-muted"><small>Belum Ada Agenda</small></th>
                            <?php else: ?>
                                <?php foreach ($agendaHarian as $idxH => $agH): ?>
                                    <th class="col-header-harian" title="<?= esc($agH['NamaUjian']) ?>">
                                        <small class="fw-bold">UH <?= $idxH + 1 ?></small>
                                    </th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <th class="col-header-harian bg-primary text-white"><small class="fw-bold">Rata UH</small></th>

                            <!-- Subkolom UTS -->
                            <?php if (empty($agendaUTS)): ?>
                                <th class="col-header-uts text-muted"><small>Belum Ada</small></th>
                            <?php else: ?>
                                <?php foreach ($agendaUTS as $agT): ?>
                                    <th class="col-header-uts" title="<?= esc($agT['NamaUjian']) ?>"><small class="fw-bold">Nilai UTS</small></th>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Subkolom UAS -->
                            <?php if (empty($agendaUAS)): ?>
                                <th class="col-header-uas text-muted"><small>Belum Ada</small></th>
                            <?php else: ?>
                                <?php foreach ($agendaUAS as $agA): ?>
                                    <th class="col-header-uas" title="<?= esc($agA['NamaUjian']) ?>"><small class="fw-bold">Nilai UAS</small></th>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Subkolom Raport -->
                            <th class="col-header-raport bg-success text-white"><small class="fw-bold">Nilai Akhir</small></th>
                            <th class="col-header-raport"><small class="fw-bold">Raport Eksis</small></th>
                            <th class="col-header-raport"><small class="fw-bold">Status Sinkron</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($legerSantri)): ?>
                            <tr>
                                <td colspan="<?= 3 + $totalColHarian + $totalColUTS + $totalColUAS + 4 ?>" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fs-2 mb-2"></i><br>
                                    Tidak ada data santri pada kelas ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($legerSantri as $no => $ls): ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?= $no + 1 ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($ls['NISN'] ?: "ID: {$ls['IdSantri']}") ?></span>
                                    </td>
                                    <td class="text-start">
                                        <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none" onclick="openDetailSantriModal('<?= $ls['IdSantri'] ?>', '<?= esc($ls['NamaSantri'], 'js') ?>')">
                                            <?= esc($ls['NamaSantri']) ?>
                                        </a>
                                    </td>

                                    <!-- Nilai Harian -->
                                    <?php if (empty($agendaHarian)): ?>
                                        <td class="text-center text-muted">-</td>
                                    <?php else: ?>
                                        <?php foreach ($agendaHarian as $agH): ?>
                                            <?php $score = $ls['scoresHarian'][$agH['id']] ?? null; ?>
                                            <td class="score-cell <?= $score !== null && $score < 70 ? 'text-danger' : '' ?>">
                                                <?= $score !== null ? $score : '<span class="text-muted">-</span>' ?>
                                            </td>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <!-- Rata Harian -->
                                    <td class="score-cell bg-light fw-bold text-primary">
                                        <?= $ls['avgHarian'] !== null ? $ls['avgHarian'] : '<span class="text-muted">-</span>' ?>
                                    </td>

                                    <!-- Nilai UTS -->
                                    <?php if (empty($agendaUTS)): ?>
                                        <td class="text-center text-muted">-</td>
                                    <?php else: ?>
                                        <?php foreach ($agendaUTS as $agT): ?>
                                            <?php $scoreT = $ls['scoresUTS'][$agT['id']] ?? null; ?>
                                            <td class="score-cell fw-bold text-warning">
                                                <?= $scoreT !== null ? $scoreT : '<span class="text-muted">-</span>' ?>
                                            </td>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Nilai UAS -->
                                    <?php if (empty($agendaUAS)): ?>
                                        <td class="text-center text-muted">-</td>
                                    <?php else: ?>
                                        <?php foreach ($agendaUAS as $agA): ?>
                                            <?php $scoreA = $ls['scoresUAS'][$agA['id']] ?? null; ?>
                                            <td class="score-cell fw-bold text-danger">
                                                <?= $scoreA !== null ? $scoreA : '<span class="text-muted">-</span>' ?>
                                            </td>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Nilai Akhir Raport -->
                                    <td class="score-cell bg-success text-white fw-bold fs-6">
                                        <?= $ls['nilaiAkhir'] !== null ? $ls['nilaiAkhir'] : '<span class="opacity-50">-</span>' ?>
                                    </td>

                                    <!-- Nilai Raport Eksisting di tbl_nilai -->
                                    <td class="score-cell text-secondary">
                                        <?= $ls['nilaiRaportEksisting'] !== null ? $ls['nilaiRaportEksisting'] : '<span class="text-muted small">Kosong</span>' ?>
                                    </td>

                                    <!-- Status Sinkron -->
                                    <td class="text-center">
                                        <?php if ($ls['nilaiAkhir'] === null): ?>
                                            <span class="badge bg-secondary">Belum Nilai</span>
                                        <?php elseif ($ls['isTersinkron']): ?>
                                            <span class="badge bg-success" title="Nilai raport sudah sama persis dengan kalkulasi"><i class="fas fa-check me-1"></i>Tersinkron</span>
                                        <?php elseif ($ls['nilaiRaportEksisting'] === null): ?>
                                            <span class="badge bg-danger" title="Belum pernah ditarik ke tbl_nilai">Belum Tarik</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" title="Ada nilai baru yang belum diperbarui di raport">Perlu Update</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Detail Modal Trigger -->
                                    <td class="text-center no-print">
                                        <button type="button" class="btn btn-outline-info btn-xs" onclick="openDetailSantriModal('<?= $ls['IdSantri'] ?>', '<?= esc($ls['NamaSantri'], 'js') ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
            <span class="text-muted small">
                <i class="fas fa-info-circle text-primary me-1"></i>
                Klik tombol <strong>"Tarik ke Raport"</strong> di pojok kanan atas untuk memperbarui nilai raport seluruh santri sekaligus.
            </span>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-xs" onclick="triggerTarikNilaiKeRaport()">
                <i class="fas fa-calculator me-1"></i> Tarik & Hitung Nilai ke Raport
            </button>
        </div>
    </div>
</div>

<!-- MODAL ATUR BOBOT RAPORT -->
<div class="modal fade no-print" id="modalAturBobot" tabindex="-1" role="dialog" aria-labelledby="modalAturBobotLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalAturBobotLabel">
                    <i class="fas fa-sliders-h me-2"></i> Pengaturan Bobot Penilaian Raport
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSimpanBobot">
                <?= csrf_field() ?>
                <input type="hidden" name="idKelas" value="<?= $idKelas ?>">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Tentukan persentase kontribusi masing-masing kategori ujian terhadap nilai akhir raport santri untuk <strong>Kelas <?= esc($kelasInfo['NamaKelas'] ?? '-') ?></strong>. Total persentase harus tepat <strong>100%</strong>.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Bobot Ujian Harian / Pekanan (%)</label>
                        <div class="input-group">
                            <input type="number" name="bobotHarian" id="inputBobotHarian" class="form-control" min="0" max="100" value="<?= $bobotSetting['BobotHarian'] ?>" oninput="updateTotalBobotCheck()" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Akumulasi rata-rata seluruh ujian harian, ulangan bab, dan tugas.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Bobot Ujian Tengah Semester (UTS) (%)</label>
                        <div class="input-group">
                            <input type="number" name="bobotUTS" id="inputBobotUTS" class="form-control" min="0" max="100" value="<?= $bobotSetting['BobotUTS'] ?>" oninput="updateTotalBobotCheck()" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Bobot Ujian Akhir Semester (UAS) (%)</label>
                        <div class="input-group">
                            <input type="number" name="bobotUAS" id="inputBobotUAS" class="form-control" min="0" max="100" value="<?= $bobotSetting['BobotUAS'] ?>" oninput="updateTotalBobotCheck()" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-0">
                        <span class="fw-bold">Total Bobot:</span>
                        <span class="fw-bold fs-5 text-success" id="labelTotalBobot">100%</span>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="simpanBobotRaportAjax()">
                        <i class="fas fa-save me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAIL PERSONAL SANTRI -->
<div class="modal fade no-print" id="modalDetailSantri" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-graduate me-2"></i> Rincian Nilai: <span id="modalDetailNamaSantri"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modalDetailSantriContent">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fs-3 mb-2"></i><br>
                    Memuat riwayat evaluasi santri...
                </div>
            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
function updateTotalBobotCheck() {
    let h = parseInt($('#inputBobotHarian').val() || 0);
    let t = parseInt($('#inputBobotUTS').val() || 0);
    let a = parseInt($('#inputBobotUAS').val() || 0);
    let tot = h + t + a;

    let label = $('#labelTotalBobot');
    label.text(tot + '%');
    if (tot === 100) {
        label.removeClass('text-danger').addClass('text-success');
    } else {
        label.removeClass('text-success').addClass('text-danger');
    }
}

function simpanBobotRaportAjax() {
    let h = parseInt($('#inputBobotHarian').val() || 0);
    let t = parseInt($('#inputBobotUTS').val() || 0);
    let a = parseInt($('#inputBobotUAS').val() || 0);
    if (h + t + a !== 100) {
        Swal.fire('Perhatian', `Total persentase bobot harus tepat 100% (Saat ini: ${h + t + a}%).`, 'warning');
        return;
    }

    let formData = $('#formSimpanBobot').serialize();
    $.ajax({
        url: '<?= base_url("backend/ujian-mdta/simpan-bobot-raport") ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire('Berhasil', res.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan bobot.', 'error');
        }
    });
}

function openDetailSantriModal(idSantri, namaSantri) {
    $('#modalDetailNamaSantri').text(namaSantri);
    $('#modalDetailSantri').modal('show');
    $('#modalDetailSantriContent').html(`
        <div class="text-center py-4 text-muted">
            <i class="fas fa-spinner fa-spin fs-3 mb-2"></i><br>
            Memuat riwayat evaluasi santri...
        </div>
    `);

    $.ajax({
        url: '<?= base_url("backend/ujian-mdta/buku-nilai/detail-santri") ?>/' + idSantri + '/<?= $idMateri ?>',
        type: 'GET',
        data: {
            semester: '<?= $semesterParam ?>',
            tahun: '<?= $idTahunAjaran ?>'
        },
        dataType: 'json',
        success: function(res) {
            if (res.success && res.komponen) {
                let rowsHtml = '';
                if (res.komponen.length === 0) {
                    rowsHtml = `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada riwayat ujian yang tercatat untuk santri ini.</td></tr>`;
                } else {
                    res.komponen.forEach((k, idx) => {
                        rowsHtml += `
                            <tr>
                                <td class="text-center">${idx + 1}</td>
                                <td><span class="badge bg-secondary text-uppercase">${k.Kategori}</span></td>
                                <td><strong>${k.NamaKomponen}</strong></td>
                                <td class="text-center"><span class="badge bg-light text-dark border">${k.Metode} (${k.ModePenilaian})</span></td>
                                <td class="text-center fw-bold fs-6 text-success">${k.Nilai}</td>
                            </tr>
                        `;
                    });
                }

                let statusRaportBadge = res.nilaiRaport !== null 
                    ? `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Tercatat di Raport: ${res.nilaiRaport}</span>` 
                    : `<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Belum Ditarik ke Raport</span>`;

                let html = `
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2 p-2 bg-light rounded border">
                        <div>
                            <small class="text-muted d-block">Santri: <strong>${res.santri.NamaSantri}</strong> &bull; NISN: ${res.santri.NISN || '-'}</small>
                            <small class="text-muted d-block">Mata Pelajaran: <strong>${res.materi.NamaMateri}</strong></small>
                        </div>
                        <div>
                            ${statusRaportBadge}
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40" class="text-center">No</th>
                                    <th>Kategori</th>
                                    <th>Nama Agenda Evaluasi</th>
                                    <th class="text-center">Metode</th>
                                    <th width="90" class="text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                        </table>
                    </div>
                `;
                $('#modalDetailSantriContent').html(html);
            } else {
                $('#modalDetailSantriContent').html('<div class="alert alert-danger">Gagal memuat rincian data santri.</div>');
            }
        },
        error: function() {
            $('#modalDetailSantriContent').html('<div class="alert alert-danger">Terjadi kesalahan pada server.</div>');
        }
    });
}

function triggerTarikNilaiKeRaport() {
    Swal.fire({
        title: 'Tarik Nilai Kelas Ini ke Raport?',
        html: `Sistem akan menghitung nilai akhir berdasarkan bobot aktif (<strong>UH: <?= $bobotSetting['BobotHarian'] ?>%</strong>, <strong>UTS: <?= $bobotSetting['BobotUTS'] ?>%</strong>, <strong>UAS: <?= $bobotSetting['BobotUAS'] ?>%</strong>) dan menyimpannya secara otomatis ke buku raport santri.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-calculator me-1"></i> Ya, Hitung & Tarik Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghitung & Menyimpan ke Raport...',
                html: 'Mohon tunggu sejenak, sedang meng-update data raport...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '<?= base_url("backend/nilai/copy-ujian-mdta-bulk-kelas") ?>',
                type: 'POST',
                data: {
                    IdKelas: '<?= $idKelas ?>',
                    Semester: '<?= $semesterParam ?>',
                    IdTahunAjaran: '<?= $idTahunAjaran ?>',
                    IdTpq: '<?= session()->get("IdTpq") ?>'
                },
                dataType: 'json',
                success: function(saveRes) {
                    if (saveRes.success) {
                        Swal.fire({
                            title: '🎉 Penarikan Berhasil!',
                            html: `Berhasil meng-update <strong>${saveRes.totalImported} data Nilai Raport</strong> untuk kelas ini.<br><br><span class="text-success"><i class="fas fa-check-circle me-1"></i> Halaman akan dimuat ulang.</span>`,
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Informasi', saveRes.message || 'Gagal menarik nilai ke raport.', 'info');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat menarik nilai ke raport.', 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection(); ?>
