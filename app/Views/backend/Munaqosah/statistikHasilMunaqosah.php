<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$tahunList = !empty($tahunAjaranList) ? $tahunAjaranList : [$current_tahun_ajaran];
$defaultTahun = in_array($current_tahun_ajaran, $tahunList, true) ? $current_tahun_ajaran : $tahunList[0];
?>
<section class="content">
    <div class="container-fluid">

        <!-- FILTER -->
        <div class="card card-outline card-primary stat-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Statistik Hasil Munaqosah</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-12 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small mb-0 font-weight-bold" for="filterTahunAjaran">Tahun Ajaran <span class="text-muted font-weight-normal">(Multiple)</span></label>
                            <div>
                                <a href="#" id="taSemua" class="small font-weight-bold mr-1">Semua</a> |
                                <a href="#" id="taCurrent" class="small mx-1">T.A Aktif</a> |
                                <a href="#" id="taReset" class="small ml-1 text-muted">Kosongkan</a>
                            </div>
                        </div>
                        <select id="filterTahunAjaran" class="form-control form-control-sm select2" multiple="multiple" data-placeholder="Pilih satu atau lebih Tahun Ajaran..." style="width: 100%;">
                            <?php foreach ($tahunList as $t): ?>
                                <?php
                                $label = preg_match('/^\d{8}$/', (string)$t) ? 'T.A ' . substr($t, 2, 2) . '/' . substr($t, 6, 2) : $t;
                                ?>
                                <option value="<?= esc($t) ?>" <?= $t === $defaultTahun ? 'selected' : '' ?>>
                                    <?= esc($label) ?> (<?= esc($t) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small mb-1 font-weight-bold" for="filterTpq">TPQ</label>
                        <select id="filterTpq" class="form-control form-control-sm" <?= $isAdmin ? '' : 'disabled' ?>>
                            <?php if ($isAdmin): ?><option value="0">Semua TPQ</option><?php endif; ?>
                            <?php if (!empty($tpqDropdown)): foreach ($tpqDropdown as $tpq): ?>
                                    <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small mb-0 font-weight-bold" for="filterTypeUjian">Type Ujian <span class="text-muted font-weight-normal">(Multiple)</span></label>
                            <div>
                                <a href="#" id="typeSemua" class="small font-weight-bold mr-1">Semua</a> |
                                <a href="#" id="typeReset" class="small ml-1 text-muted">Kosongkan</a>
                            </div>
                        </div>
                        <select id="filterTypeUjian" class="form-control form-control-sm select2" multiple="multiple" data-placeholder="Pilih Type Ujian..." style="width: 100%;">
                            <option value="pra-munaqosah" selected>Pra-Munaqosah</option>
                            <?php if ($isAdmin || ($aktiveTombolKelulusan && ($isOperator || $isKepalaTpq))): ?>
                                <option value="munaqosah" <?= $isAdmin ? 'selected' : '' ?>>Munaqosah</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 mb-2 d-flex align-items-end">
                        <div class="w-100">
                            <button id="btnMuat" class="btn btn-primary btn-sm btn-block"><i class="fas fa-sync-alt"></i> Muat Data</button>
                            <button id="btnPdf" class="btn btn-danger btn-sm btn-block mt-1" disabled><i class="fas fa-file-pdf"></i> Export PDF</button>
                        </div>
                    </div>
                </div>

                <div class="row" id="rowMateriFilter" style="display:none;">
                    <div class="col-12 mb-2">
                        <label class="small mb-1 font-weight-bold">Jenis Materi
                            <a href="#" id="matSemua" class="small ml-2">Semua</a> |
                            <a href="#" id="matNone" class="small">Kosongkan</a>
                        </label>
                        <div class="chip-wrap" id="materiChips"></div>
                    </div>
                </div>

                <hr class="my-2">

                <!-- Pengaturan tampilan -->
                <div class="row">
                    <div class="col-lg-4 col-md-12 mb-2">
                        <label class="small mb-1 font-weight-bold" for="inpThreshold">Rentang Nilai (batas, pisahkan koma)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="inpThreshold" class="form-control" value="65, 70" placeholder="contoh: 60, 70, 80, 90">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" id="btnTerapkan" type="button">Terapkan</button>
                                <button class="btn btn-outline-secondary" id="btnReset" type="button" title="Kembali ke default 65 / 70">Reset</button>
                            </div>
                        </div>
                        <div class="mt-1">
                            <span class="small text-muted">Preset:</span>
                            <a href="#" class="badge badge-light preset" data-v="65,70">Default 65/70</a>
                            <a href="#" class="badge badge-light preset" data-v="60,70,80,90">60/70/80/90</a>
                            <a href="#" class="badge badge-light preset" data-v="50,65,80">50/65/80</a>
                            <a href="#" class="badge badge-light preset" data-v="70">Lulus 70</a>
                        </div>
                        <div class="small mt-1" id="binPreview"></div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="small mb-1 font-weight-bold" for="selChartType">Jenis Chart</label>
                        <select id="selChartType" class="form-control form-control-sm">
                            <option value="grouped">Batang berkelompok</option>
                            <option value="combo">Batang &amp; Garis (Kombinasi)</option>
                            <option value="stacked">Batang bertumpuk</option>
                            <option value="line">Garis</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="small mb-1 font-weight-bold" for="selValueMode">Nilai</label>
                        <select id="selValueMode" class="form-control form-control-sm">
                            <option value="count">Jumlah</option>
                            <option value="percent">Persentase</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="small mb-1 font-weight-bold" for="selGeneralMode">Chart Umum</label>
                        <select id="selGeneralMode" class="form-control form-control-sm">
                            <option value="materi">Per Materi</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 mb-2 d-flex align-items-end">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="swZero">
                            <label class="custom-control-label small" for="swZero">Pisahkan belum dinilai (0)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="statLoading" class="text-center py-4" style="display:none;">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="small text-muted mt-2">Memuat data statistik…</div>
        </div>
        <div id="statEmpty" class="alert alert-info" style="display:none;"></div>

        <!-- MODAL DETAIL -->
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content" style="border-radius:12px;">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="dmTitle">Detail</h5>
                            <div class="small text-muted" id="dmSub"></div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="search" id="dmSearch" class="form-control form-control-sm mb-2" style="max-width:260px" placeholder="Cari nama / no peserta…">
                        <div id="dmLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                        <div id="dmError" class="alert alert-danger" style="display:none;"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped mb-0" id="dmTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th style="width:45px">No</th>
                                        <th>No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th>Tahun</th>
                                        <th>Type</th>
                                        <th>Materi</th>
                                        <th class="text-center">Nilai</th>
                                        <th class="text-center" style="width:55px">Detail</th>
                                    </tr>
                                </thead>
                                <tbody id="dmBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" id="dmNewTab"><i class="fas fa-external-link-alt"></i> Buka di Tab Baru</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL PENGATURAN EXPORT PDF -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 490px;">
                <div class="modal-content" style="border-radius:12px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.2);">
                    <div class="modal-header bg-danger text-white py-2 px-3">
                        <h6 class="modal-title font-weight-bold mb-0"><i class="fas fa-file-pdf mr-1"></i> Opsi Export PDF Statistik</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-list-ol text-primary mr-1"></i> Cetak Daftar Santri ke Dokumen PDF?</label>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optSantriAll" name="pdfSantriMode" class="custom-control-input" value="all" checked>
                                <label class="custom-control-label small" for="optSantriAll"><strong>Ya, sertakan untuk semua materi</strong></label>
                            </div>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optSantriActive" name="pdfSantriMode" class="custom-control-input" value="active">
                                <label class="custom-control-label small" for="optSantriActive">Hanya materi yang tombol "Daftar Santri"-nya sedang dibuka di layar</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="optSantriNone" name="pdfSantriMode" class="custom-control-input" value="none">
                                <label class="custom-control-label small" for="optSantriNone">Tidak (Hanya grafik dan ringkasan nilai)</label>
                            </div>
                        </div>

                        <div id="wrapPdfRentang" class="p-2 mb-3 rounded" style="background:#f1f5f9; border:1px solid #cbd5e1;">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-filter text-info mr-1"></i> Rentang Nilai Santri yang Dicetak:</label>
                            <select id="selPdfBinFilter" class="form-control form-control-sm font-weight-bold">
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Daftar nama santri yang dicetak pada materi akan disaring sesuai pilihan ini.</small>
                        </div>

                        <div id="wrapPdfScorePrivacy" class="p-2 mb-3 rounded" style="background:#f8fafc; border:1px solid #cbd5e1;">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="chkPdfShowScore" checked>
                                <label class="custom-control-label small font-weight-bold text-dark" for="chkPdfShowScore">
                                    <i class="fas fa-eye text-primary mr-1"></i> Tampilkan Angka Nilai di Tabel Daftar
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                                <i class="fas fa-user-shield text-success mr-1"></i> Nonaktifkan (uncheck) jika dokumen akan dibagikan ke umum guna menjaga kerahasiaan / privasi nilai santri.
                            </small>
                        </div>

                        <div class="p-2 mb-3 rounded" style="background:#fff5f5; border:1px solid #fed7d7;">
                            <label class="small font-weight-bold text-danger mb-1 d-block"><i class="fas fa-clipboard-check mr-1"></i> Lembar Matriks Evaluasi Kekurangan Santri:</label>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optPdfMatriksKurang" name="pdfMatriksMode" class="custom-control-input" value="kurang_only" checked>
                                <label class="custom-control-label small" for="optPdfMatriksKurang"><strong>Ya, khusus santri yang nilainya sesuai rentang pilihan</strong> (Rekomendasi)</label>
                            </div>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optPdfMatriksAll" name="pdfMatriksMode" class="custom-control-input" value="all">
                                <label class="custom-control-label small" for="optPdfMatriksAll">Ya, sertakan semua santri</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="optPdfMatriksNone" name="pdfMatriksMode" class="custom-control-input" value="none">
                                <label class="custom-control-label small" for="optPdfMatriksNone">Tidak (Jangan sertakan lembar matriks)</label>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-file-alt text-secondary mr-1"></i> Orientasi Halaman:</label>
                            <div class="d-flex" style="gap:15px;">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="optOriPortrait" name="pdfOrientation" class="custom-control-input" value="portrait" checked>
                                    <label class="custom-control-label small" for="optOriPortrait">Potret (Portrait A4)</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="optOriLandscape" name="pdfOrientation" class="custom-control-input" value="landscape">
                                    <label class="custom-control-label small" for="optOriLandscape">Lanskap (Landscape A4)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm font-weight-bold" id="btnProsesExportPdf"><i class="fas fa-download mr-1"></i> Unduh PDF</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="statContent" style="display:none;">
            <!-- RINGKASAN -->
            <div class="row" id="summaryCards"></div>

            <!-- CHART UMUM -->
            <h5 class="stat-section"><i class="fas fa-layer-group mr-1"></i> Chart Umum</h5>
            <div id="generalCharts"></div>

            <!-- CHART DETAIL PER MATERI -->
            <h5 class="stat-section"><i class="fas fa-book-open mr-1"></i> Detail per Jenis Materi</h5>
            <div id="detailCharts"></div>

            <!-- MATRIKS EVALUASI & PEMETAAN KEKURANGAN MATERI SANTRI -->
            <h5 class="stat-section mt-4"><i class="fas fa-clipboard-check mr-1"></i> Matriks Evaluasi & Pemetaan Kekurangan Materi</h5>
            <div class="card card-outline card-danger stat-card mb-4" id="cardMatriksEvaluasi">
                <div class="card-header d-flex justify-content-between align-items-center py-2 flex-wrap" style="gap:8px;">
                    <div>
                        <strong class="small text-danger"><i class="fas fa-user-times mr-1"></i> Daftar Rekapitulasi Kekurangan Materi per Santri</strong>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                        <button class="btn btn-xs btn-outline-success font-weight-bold" id="btnCsvMatriks" type="button"><i class="fas fa-file-csv mr-1"></i> Unduh CSV Matriks</button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 p-2 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; gap:8px;">
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                            <div class="d-flex align-items-center">
                                <label class="small font-weight-bold mb-0 mr-1 text-dark" for="selMatriksThreshold">Batas Standar:</label>
                                <select id="selMatriksThreshold" class="form-control form-control-sm font-weight-bold" style="width:130px;"></select>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="small font-weight-bold mb-0 mr-1 text-dark" for="selMatriksFilterMode">Tampilkan:</label>
                                <select id="selMatriksFilterMode" class="form-control form-control-sm" style="width:190px;">
                                    <option value="kurang_only" selected>Hanya yang Kurang (≥ 1 Materi)</option>
                                    <option value="all">Semua Santri</option>
                                    <option value="kritis">Kurang Kritis (≥ 2 Materi)</option>
                                    <option value="tuntas">Hanya yang Tuntas Semua (0 Kurang)</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                            <div class="custom-control custom-switch" title="Tampilkan atau sembunyikan angka nilai santri di tabel">
                                <input type="checkbox" class="custom-control-input" id="chkMatriksShowScore" checked>
                                <label class="custom-control-label small font-weight-bold text-muted mb-0" for="chkMatriksShowScore" style="cursor:pointer;">Tampilkan Angka Nilai</label>
                            </div>
                            <input type="search" id="inpSearchMatriks" class="form-control form-control-sm" placeholder="Cari santri / no peserta…" style="width:180px;">
                        </div>
                    </div>

                    <div class="small text-muted mb-2 font-weight-bold" id="infoMatriksCount"></div>

                    <div class="table-responsive bg-white rounded border" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-sm table-bordered table-hover mb-0 text-nowrap" id="tblMatriksEvaluasi">
                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 2;">
                                <tr id="trMatriksHead"></tr>
                            </thead>
                            <tbody id="tbMatriksBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .stat-card {
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(30, 60, 120, .08);
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
        min-height: calc(1.8125rem + 2px);
        padding: 2px 4px;
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        background: #3b6fe0;
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 2px 8px;
        font-size: 0.8rem;
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 4px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fecaca;
    }

    .chip-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip {
        margin: 0;
        cursor: pointer;
    }

    .chip input {
        display: none;
    }

    .chip span {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: .82rem;
        background: #eef1f6;
        color: #4a5568;
        border: 1px solid #dde3ed;
        transition: all .15s ease;
        user-select: none;
    }

    .chip:hover span {
        background: #e0e8f7;
    }

    .chip input:checked+span {
        background: linear-gradient(135deg, #3b6fe0, #5b8def);
        color: #fff;
        border-color: #3b6fe0;
        box-shadow: 0 2px 6px rgba(59, 111, 224, .35);
    }

    .stat-section {
        margin: 18px 0 10px;
        font-weight: 700;
        color: #2d3748;
        border-left: 4px solid #3b6fe0;
        padding-left: 10px;
    }

    .sum-box {
        border-radius: 12px;
        color: #fff;
        padding: 14px 16px;
        margin-bottom: 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
        position: relative;
        overflow: hidden;
        transition: transform .2s ease;
    }

    .sum-box:hover {
        transform: translateY(-3px);
    }

    .sum-box .val {
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .sum-box .lbl {
        font-size: .8rem;
        opacity: .92;
    }

    .sum-box i {
        position: absolute;
        right: 12px;
        top: 10px;
        font-size: 2rem;
        opacity: .25;
    }

    .chart-card {
        border-radius: 12px;
        box-shadow: 0 3px 14px rgba(30, 60, 120, .07);
        margin-bottom: 18px;
    }

    .chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef1f6;
    }

    .chart-box {
        position: relative;
        height: 340px;
    }

    .chart-card table {
        font-size: .8rem;
    }
</style>
<script>
    (function() {
        'use strict';

        const CFG = {
            dataUrl: '<?= base_url('backend/munaqosah/statistik-hasil-data') ?>',
            detailDataUrl: '<?= base_url('backend/munaqosah/statistik-hasil-detail-data') ?>',
            detailPageUrl: '<?= base_url('backend/munaqosah/statistik-hasil-detail') ?>',
            pesertaUrl: '<?= base_url('backend/munaqosah/kelulusan-peserta') ?>',
            isAdmin: <?= $isAdmin ? 'true' : 'false' ?>
        };
        const LS_KEY = 'statHasilMunaqosahV1';
        const ZERO_COLOR = '#9e9e9e';

        const defaults = {
            thresholds: [65, 70],
            separateZero: false,
            chartType: 'grouped',
            valueMode: 'count',
            generalMode: 'materi'
        };
        let settings = Object.assign({}, defaults, loadLS());
        let DATA = null;
        let selectedCats = null; // null = semua
        let charts = [];

        const $ = id => document.getElementById(id);
        // Tampilan tahun ajaran: 20252026 -> T.A 25/26 (nilai asli tetap untuk parameter)
        const fmtTA = t => /^\d{8}$/.test(String(t)) ? 'T.A ' + String(t).slice(2, 4) + '/' + String(t).slice(6, 8) : String(t);
        const fmtType = t => t === 'pra-munaqosah' ? 'Pra-Munaqosah' : (t === 'munaqosah' ? 'Munaqosah' : t);
        const fmtTypeShort = t => t === 'pra-munaqosah' ? 'Pra' : (t === 'munaqosah' ? 'Mun' : t);
        const badgeType = t => t === 'munaqosah'
            ? '<span class="badge badge-success">Munaqosah</span>'
            : '<span class="badge badge-info">Pra-Munaqosah</span>';
        const esc = s => String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        function loadLS() {
            try {
                return JSON.parse(localStorage.getItem(LS_KEY)) || {};
            } catch (e) {
                return {};
            }
        }

        function saveLS() {
            try {
                localStorage.setItem(LS_KEY, JSON.stringify(settings));
            } catch (e) {}
        }

        // ---------- Rentang & bin ----------
        function parseThresholds(str) {
            const arr = String(str).split(/[,;\s]+/).map(Number).filter(n => Number.isFinite(n) && n > 0 && n <= 100);
            return [...new Set(arr)].sort((a, b) => a - b);
        }

        function fmt(n) {
            return Number.isInteger(n) ? String(n) : String(n);
        }

        function buildBins() {
            const th = settings.thresholds;
            const bins = [];
            if (settings.separateZero) bins.push({
                label: 'Belum dinilai (0)',
                color: ZERO_COLOR,
                zero: true
            });
            const n = th.length + 1;
            const colors = n === 3 ? ['#e53935', '#fbc02d', '#2e9d4f'] :
                Array.from({
                    length: n
                }, (_, i) => n === 1 ? '#2e9d4f' : `hsl(${Math.round(i * 125 / (n - 1))},72%,44%)`);
            for (let i = 0; i < n; i++) {
                let label;
                if (i === 0) label = '<' + fmt(th[0]);
                else if (i === n - 1) label = '≥' + fmt(th[n - 2]);
                else label = '≥' + fmt(th[i - 1]) + ' - <' + fmt(th[i]);
                bins.push({
                    label,
                    color: colors[i],
                    zero: false,
                    min: i === 0 ? null : th[i - 1],
                    max: i === n - 1 ? null : th[i],
                    nz: !!settings.separateZero
                });
            }
            return bins;
        }

        function binIndex(v, bins) {
            const th = settings.thresholds;
            const off = settings.separateZero ? 1 : 0;
            if (settings.separateZero && v <= 0) return 0;
            let i = 0;
            while (i < th.length && v >= th[i]) i++;
            return i + off;
        }

        function renderBinPreview() {
            const bins = buildBins();
            $('binPreview').innerHTML = bins.map(b => `<span class="badge mr-1" style="background:${b.color};color:#fff">${esc(b.label)}</span>`).join('');
        }

        // ---------- Pair builder ----------
        function getPairs() {
            const pairs = [];
            if (!DATA) return pairs;
            const sel = selectedCats ? new Set(selectedCats) : null;
            DATA.rows.forEach(r => {
                Object.keys(r.avg).forEach(cat => {
                    if (sel && !sel.has(cat)) return;
                    pairs.push({
                        y: r.y,
                        type: r.type || (DATA.meta ? DATA.meta.TypeUjian : ''),
                        tpq: r.tpq,
                        np: r.np,
                        cat,
                        v: Number(r.avg[cat]) || 0
                    });
                });
            });
            return pairs;
        }

        function countBins(pairs, bins) {
            const c = new Array(bins.length).fill(0);
            pairs.forEach(p => c[binIndex(p.v, bins)]++);
            return c;
        }

        function buildDatasets(groups, bins) {
            const counts = groups.map(g => countBins(g, bins));
            return bins.map((b, bi) => ({
                label: b.label,
                color: b.color,
                bin: b,
                data: counts.map(c => {
                    if (settings.valueMode === 'percent') {
                        const tot = c.reduce((a, x) => a + x, 0);
                        return tot ? Math.round(c[bi] * 1000 / tot) / 10 : 0;
                    }
                    return c[bi];
                })
            }));
        }

        // Helper transparansi warna untuk combo chart
        function colorWithAlpha(color, alpha) {
            if (!color) return color;
            if (color.startsWith('#')) {
                let c = color.substring(1);
                if (c.length === 3) c = c.split('').map(x => x + x).join('');
                const num = parseInt(c, 16);
                return `rgba(${(num >> 16) & 255}, ${(num >> 8) & 255}, ${num & 255}, ${alpha})`;
            } else if (color.startsWith('hsl')) {
                return color.replace('hsl', 'hsla').replace(')', `, ${alpha})`);
            }
            return color;
        }

        // ---------- Chart ----------
        const labelPlugin = {
            id: 'valueLabels',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                const stacked = !!chart.options.scales.x.stacked;
                const pct = settings.valueMode === 'percent';
                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                ctx.textAlign = 'center';
                chart.data.datasets.forEach((ds, i) => {
                    if (ds.isComboLine) return; // hindari label dobel pada garis tren mode combo
                    const meta = chart.getDatasetMeta(i);
                    if (meta.hidden) return;
                    meta.data.forEach((el, idx) => {
                        const v = ds.data[idx];
                        if (v === null || v === undefined) return;
                        if (stacked && !v) return;
                        const txt = pct ? v + '%' : v;
                        if (stacked) {
                            ctx.fillStyle = '#111';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(txt, el.x, (el.y + el.base) / 2);
                        } else {
                            ctx.fillStyle = '#333';
                            ctx.textBaseline = 'bottom';
                            ctx.fillText(txt, el.x, el.y - 3);
                        }
                    });
                });
                ctx.restore();
            }
        };

        function chartConfig(labels, datasets, title, onPick) {
            const stacked = settings.chartType === 'stacked';
            const line = settings.chartType === 'line';
            const combo = settings.chartType === 'combo';
            const pct = settings.valueMode === 'percent';

            let chartDatasets = [];
            if (combo) {
                // Tambahkan dataset batang (bar)
                datasets.forEach(d => {
                    chartDatasets.push({
                        type: 'bar',
                        label: d.label,
                        data: d.data,
                        backgroundColor: colorWithAlpha(d.color, 0.78),
                        borderColor: d.color,
                        borderWidth: 1.5,
                        borderRadius: 3,
                        bin: d.bin,
                        order: 2
                    });
                });
                // Tambahkan dataset garis (line tren)
                datasets.forEach(d => {
                    chartDatasets.push({
                        type: 'line',
                        label: d.label + ' (Tren)',
                        data: d.data,
                        borderColor: d.color,
                        backgroundColor: d.color,
                        borderWidth: 2.5,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: d.color,
                        pointBorderWidth: 2,
                        bin: d.bin,
                        order: 1,
                        isComboLine: true
                    });
                });
            } else {
                chartDatasets = datasets.map(d => ({
                    label: d.label,
                    data: d.data,
                    backgroundColor: d.color,
                    borderColor: d.color,
                    borderWidth: line ? 2.5 : 0,
                    tension: .3,
                    fill: false,
                    pointRadius: line ? 4 : 0,
                    borderRadius: line ? 0 : 3,
                    bin: d.bin
                }));
            }

            return {
                type: line ? 'line' : 'bar',
                data: {
                    labels,
                    datasets: chartDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    devicePixelRatio: Math.max(window.devicePixelRatio || 1, 2.5),
                    animation: {
                        duration: 500
                    },
                    onClick: (evt, els) => {
                        if (!onPick || !els.length) return;
                        onPick(els[0].index, els[0].datasetIndex);
                    },
                    onHover: (evt, els) => {
                        if (evt.native && evt.native.target) evt.native.target.style.cursor = (onPick && els.length) ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                filter: (item, chartData) => {
                                    const ds = chartData.datasets[item.datasetIndex];
                                    return !ds || !ds.isComboLine;
                                }
                            },
                            onClick: (evt, item, legend) => {
                                const chart = legend.chart;
                                const idx = item.datasetIndex;
                                const meta = chart.getDatasetMeta(idx);
                                meta.hidden = meta.hidden === null ? !chart.data.datasets[idx].hidden : null;

                                // Jika mode combo, sinkronkan juga garis tren pasangannya saat legenda diklik
                                if (combo) {
                                    const numBins = datasets.length;
                                    const pairedIdx = idx + numBins;
                                    if (chart.data.datasets[pairedIdx]) {
                                        const pairedMeta = chart.getDatasetMeta(pairedIdx);
                                        pairedMeta.hidden = meta.hidden;
                                    }
                                }
                                chart.update();
                            }
                        },
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 15
                            }
                        },
                        tooltip: {
                            filter: (tooltipItem) => {
                                return !tooltipItem.dataset.isComboLine;
                            },
                            callbacks: {
                                label: c => `${c.dataset.label}: ${c.parsed.y}${pct ? '%' : ''}`,
                                footer: () => onPick ? 'Klik untuk melihat daftar peserta' : ''
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked
                        },
                        y: {
                            stacked,
                            beginAtZero: true,
                            max: pct && stacked ? 100 : undefined,
                            grace: line || combo || !stacked ? '12%' : 0,
                            ticks: {
                                precision: pct ? 1 : 0
                            }
                        }
                    }
                },
                plugins: [labelPlugin]
            };
        }

        function tableHtml(labels, datasets) {
            const pct = settings.valueMode === 'percent';
            let h = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Kelas</th>' +
                labels.map(l => `<th class="text-center">${esc(l)}</th>`).join('') + '</tr></thead><tbody>';
            datasets.forEach(d => {
                h += `<tr><td><span class="badge" style="background:${d.color};color:#fff">${esc(d.label)}</span></td>` +
                    d.data.map(v => `<td class="text-center">${v}${pct ? '%' : ''}</td>`).join('') + '</tr>';
            });
            return h + '</tbody></table></div>';
        }

        function addChartCard(container, id, title, labels, datasets, ctxFn, extra) {
            const card = document.createElement('div');
            card.className = 'card chart-card';
            card.id = 'card_' + id;
            const isMateri = extra && extra.catId;

            let pesertaBtnHtml = '';
            if (isMateri) {
                pesertaBtnHtml = `<button class="btn btn-xs btn-outline-info btn-peserta mr-1" type="button" title="Tampilkan/sembunyikan daftar nama santri"><i class="fas fa-users"></i> Daftar Santri</button>`;
            }

            card.innerHTML = `
            <div class="card-header d-flex justify-content-between align-items-center py-2 flex-wrap" style="gap: 4px;">
                <strong class="small">${esc(title)}</strong>
                <div>
                    ${pesertaBtnHtml}
                    <button class="btn btn-xs btn-outline-secondary btn-tbl" type="button"><i class="fas fa-table"></i> Tabel</button>
                    <button class="btn btn-xs btn-outline-success btn-csv" type="button"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn btn-xs btn-outline-primary btn-png" type="button"><i class="fas fa-image"></i> PNG</button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-box"><canvas id="${id}"></canvas></div>
                <div class="tbl-wrap mt-3" style="display:none;">${tableHtml(labels, datasets)}</div>
                ${isMateri ? `
                <div class="peserta-wrap mt-3" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:8px;">
                        <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                            <span class="small font-weight-bold text-dark"><i class="fas fa-filter text-primary"></i> Rentang Nilai:</span>
                            <div class="bin-filter-pills d-inline-flex flex-wrap" style="gap:4px;"></div>
                        </div>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <div class="custom-control custom-switch d-inline-flex align-items-center mr-1" title="Sembunyikan/Tampilkan kolom nilai santri untuk privasi">
                                <input type="checkbox" class="custom-control-input chk-show-santri-val" id="swVal_${id}" checked>
                                <label class="custom-control-label small text-muted mb-0 font-weight-bold" for="swVal_${id}" style="font-size:0.75rem; cursor:pointer;">Nilai</label>
                            </div>
                            <input type="search" class="form-control form-control-sm inp-search-santri" placeholder="Cari santri / no peserta…" style="width:170px;">
                            <button class="btn btn-xs btn-outline-success btn-csv-santri" type="button" title="Unduh CSV daftar santri"><i class="fas fa-file-csv"></i> CSV</button>
                        </div>
                    </div>
                    <div class="small text-muted mb-2 info-count-santri"></div>
                    <div class="table-responsive bg-white rounded border" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover mb-0 tbl-santri">
                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>No Peserta</th>
                                    <th>Nama Santri</th>
                                    <th>TPQ</th>
                                    <th>Tahun</th>
                                    <th>Type</th>
                                    <th class="text-center col-santri-val">Nilai</th>
                                    <th class="text-center" style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                ` : ''}
            </div>`;
            container.appendChild(card);
            const canvas = card.querySelector('canvas');
            let inst = null;
            const onPick = ctxFn ? (idx, dsIdx) => {
                const ds = (inst && inst.data.datasets) ? inst.data.datasets[dsIdx] : datasets[dsIdx];
                if (!ds || !ds.data[idx]) return; // batang 0 tidak dibuka
                openDetail(ctxFn(idx), ds.bin, title);
            } : null;
            inst = new Chart(canvas, chartConfig(labels, datasets, title, onPick));
            charts.push(inst);

            card.querySelector('.btn-tbl').onclick = () => {
                const w = card.querySelector('.tbl-wrap');
                w.style.display = w.style.display === 'none' ? 'block' : 'none';
            };
            card.querySelector('.btn-png').onclick = () => {
                const a = document.createElement('a');
                // latar putih agar PNG tidak transparan
                const tmp = document.createElement('canvas');
                tmp.width = canvas.width;
                tmp.height = canvas.height;
                const t = tmp.getContext('2d');
                t.fillStyle = '#fff';
                t.fillRect(0, 0, tmp.width, tmp.height);
                t.drawImage(canvas, 0, 0);
                a.href = tmp.toDataURL('image/png');
                a.download = title.replace(/[^\w\-]+/g, '_') + '.png';
                a.click();
            };
            card.querySelector('.btn-csv').onclick = () => {
                const q = v => '"' + String(v).replace(/"/g, '""') + '"';
                const lines = [
                    [q('Kelas')].concat(labels.map(q)).join(',')
                ];
                datasets.forEach(d => lines.push([q(d.label)].concat(d.data).join(',')));
                const blob = new Blob(['\ufeff' + lines.join('\n')], {
                    type: 'text/csv;charset=utf-8;'
                });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = title.replace(/[^\w\-]+/g, '_') + '.csv';
                a.click();
                setTimeout(() => URL.revokeObjectURL(a.href), 500);
            };

            // Inisialisasi daftar santri jika card materi
            if (isMateri) {
                const catId = extra.catId;
                const catName = extra.catName;
                const bins = buildBins();
                const santriItems = [];

                DATA.rows.forEach(r => {
                    if (r.avg && r.avg[catId] !== undefined) {
                        const v = Number(r.avg[catId]) || 0;
                        santriItems.push({
                            np: r.np,
                            nm: r.nm || '-',
                            tpq: r.tpq,
                            tpqName: DATA.tpqs[r.tpq] || r.tpq,
                            y: r.y,
                            type: r.type || (DATA.meta ? (Array.isArray(DATA.meta.TypeUjian) ? DATA.meta.TypeUjian[0] : DATA.meta.TypeUjian) : ''),
                            v: v,
                            binIdx: binIndex(v, bins),
                            catName: catName
                        });
                    }
                });

                const binCounts = new Array(bins.length).fill(0);
                santriItems.forEach(s => binCounts[s.binIdx]++);

                // Default filter: bin 0 (< 65) jika ada, jika tidak default 'all'
                let activeBin = binCounts[0] > 0 ? '0' : 'all';
                let searchTerm = '';

                const pillsWrap = card.querySelector('.bin-filter-pills');
                const inpSearch = card.querySelector('.inp-search-santri');
                const btnCsvSantri = card.querySelector('.btn-csv-santri');
                const countInfo = card.querySelector('.info-count-santri');
                const tbody = card.querySelector('.tbl-santri tbody');
                const thVal = card.querySelector('.tbl-santri .col-santri-val');
                const chkShowVal = card.querySelector('.chk-show-santri-val');
                const pesertaWrap = card.querySelector('.peserta-wrap');

                function getFilteredSantri() {
                    return santriItems.filter(s => {
                        if (activeBin !== 'all' && s.binIdx !== Number(activeBin)) return false;
                        if (searchTerm) {
                            const term = searchTerm.toLowerCase();
                            if (!s.nm.toLowerCase().includes(term) && !s.np.toLowerCase().includes(term) && !s.tpqName.toLowerCase().includes(term)) {
                                return false;
                            }
                        }
                        return true;
                    }).sort((a, b) => a.v - b.v); // Urutkan dari nilai terendah dulu
                }

                function getActiveBinLabel() {
                    if (activeBin === 'all') return 'Semua Nilai';
                    return bins[Number(activeBin)] ? bins[Number(activeBin)].label : 'Semua Nilai';
                }

                function renderPills() {
                    let html = `<button type="button" class="btn btn-xs pill-bin ${activeBin === 'all' ? 'btn-dark' : 'btn-outline-secondary'}" data-bin="all" style="border-radius:14px; font-weight:600; padding:1px 8px;">Semua (${santriItems.length})</button>`;
                    bins.forEach((b, bi) => {
                        const isActive = activeBin === String(bi);
                        const bg = isActive ? b.color : '#ffffff';
                        const color = isActive ? '#ffffff' : '#2d3748';
                        const border = b.color;
                        html += `<button type="button" class="btn btn-xs pill-bin" data-bin="${bi}" style="background:${bg}; color:${color}; border:1.5px solid ${border}; border-radius:14px; font-weight:600; padding:1px 8px; margin-right:3px; box-shadow:${isActive ? '0 2px 5px rgba(0,0,0,.15)' : 'none'};">${esc(b.label)} (${binCounts[bi]})</button>`;
                    });
                    pillsWrap.innerHTML = html;
                    pillsWrap.querySelectorAll('.pill-bin').forEach(btn => {
                        btn.onclick = () => {
                            activeBin = btn.dataset.bin;
                            renderPills();
                            renderSantriRows();
                        };
                    });
                }

                function renderSantriRows() {
                    const filtered = getFilteredSantri();
                    const label = getActiveBinLabel();
                    const showVal = chkShowVal ? chkShowVal.checked : true;
                    if (thVal) thVal.style.display = showVal ? '' : 'none';

                    countInfo.innerHTML = `Menampilkan <strong>${filtered.length}</strong> santri pada rentang <strong>${esc(label)}</strong> (dari total ${santriItems.length} santri)`;

                    const colSpan = showVal ? 8 : 7;
                    if (!filtered.length) {
                        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center text-muted py-3">Tidak ada santri pada rentang nilai ini</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = filtered.map((s, idx) => {
                        const binColor = bins[s.binIdx] ? bins[s.binIdx].color : '#6c757d';
                        const href = CFG.pesertaUrl + '?' + new URLSearchParams({
                            NoPeserta: s.np,
                            IdTahunAjaran: s.y,
                            TypeUjian: s.type || '',
                            IdTpq: s.tpq
                        }).toString();
                        const valTd = showVal ? `<td class="text-center"><span class="badge" style="background:${binColor}; color:#fff; font-size:0.82rem; font-weight:700;">${s.v > 0 ? s.v.toFixed(2) : '0'}</span></td>` : '';
                        return `<tr>
                            <td class="text-muted">${idx + 1}</td>
                            <td>${esc(s.np)}</td>
                            <td><strong>${esc(s.nm)}</strong></td>
                            <td>${esc(s.tpqName)}</td>
                            <td>${esc(fmtTA(s.y))}</td>
                            <td>${badgeType(s.type)}</td>
                            ${valTd}
                            <td class="text-center"><a class="btn btn-xs btn-outline-primary" target="_blank" href="${href}" title="Buka Detail Peserta"><i class="fas fa-eye"></i></a></td>
                        </tr>`;
                    }).join('');
                }

                renderPills();
                renderSantriRows();

                if (chkShowVal) {
                    chkShowVal.onchange = () => renderSantriRows();
                }

                inpSearch.oninput = () => {
                    searchTerm = inpSearch.value.trim();
                    renderSantriRows();
                };

                btnCsvSantri.onclick = () => {
                    const filtered = getFilteredSantri();
                    const label = getActiveBinLabel();
                    const q = v => '"' + String(v).replace(/"/g, '""') + '"';
                    const lines = [
                        ['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Tahun Ajaran', 'Type Ujian', 'Materi', 'Nilai', 'Rentang Nilai'].map(q).join(',')
                    ];
                    filtered.forEach((s, idx) => {
                        lines.push([
                            idx + 1,
                            q(s.np),
                            q(s.nm),
                            q(s.tpqName),
                            q(fmtTA(s.y)),
                            q(fmtType(s.type)),
                            q(catName),
                            s.v,
                            q(cleanPdfText(bins[s.binIdx] ? bins[s.binIdx].label : '-'))
                        ].join(','));
                    });
                    const blob = new Blob(['\ufeff' + lines.join('\n')], {
                        type: 'text/csv;charset=utf-8;'
                    });
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = `Daftar_Santri_${catName.replace(/[^\w\-]+/g, '_')}_${label.replace(/[^\w\-]+/g, '_')}.csv`;
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(a.href), 500);
                };

                card.querySelector('.btn-peserta').onclick = () => {
                    pesertaWrap.style.display = (pesertaWrap.style.display === 'none') ? 'block' : 'none';
                    card.querySelector('.btn-peserta').classList.toggle('active');
                };

                // Expose getter data untuk keperluan Export PDF
                card.getSantriExportData = () => {
                    return {
                        isOpen: pesertaWrap.style.display !== 'none',
                        catName: catName,
                        binLabel: getActiveBinLabel(),
                        items: getFilteredSantri()
                    };
                };

                card.getSantriDataByBin = (binVal) => {
                    let filtered = santriItems;
                    let label = 'Semua Nilai';
                    if (binVal !== 'all' && binVal !== null && binVal !== undefined) {
                        const bIdx = Number(binVal);
                        filtered = santriItems.filter(s => s.binIdx === bIdx);
                        label = bins[bIdx] ? bins[bIdx].label : 'Nilai';
                    }
                    return {
                        catName: catName,
                        binLabel: label,
                        items: filtered.slice().sort((a, b) => a.v - b.v)
                    };
                };
            }
        }

        // ---------- Render ----------
        function render() {
            charts.forEach(c => c.destroy());
            charts = [];
            $('generalCharts').innerHTML = '';
            $('detailCharts').innerHTML = '';
            renderBinPreview();
            if (!DATA) return;

            const bins = buildBins();
            const pairs = getPairs();
            const years = DATA.years || [];
            const yl = years.map(fmtTA);
            const typeOrder = ['pra-munaqosah', 'munaqosah'];
            const rawTypes = DATA.types || (Array.isArray(DATA.meta.TypeUjian) ? DATA.meta.TypeUjian : [DATA.meta.TypeUjian]);
            const types = rawTypes.slice().sort((a, b) => typeOrder.indexOf(a) - typeOrder.indexOf(b));
            const isMultiType = types.length > 1;
            const isMultiYear = years.length > 1;
            const cats = DATA.categories.filter(c => !selectedCats || selectedCats.includes(c.id));

            if (!pairs.length) {
                $('summaryCards').innerHTML = '';
                $('generalCharts').innerHTML = '<div class="alert alert-warning">Tidak ada data untuk filter yang dipilih.</div>';
                return;
            }

            // Membangun daftar series / kolom pembanding
            const seriesCols = [];
            years.forEach(y => {
                types.forEach(t => {
                    let label = '';
                    let desc = '';
                    if (isMultiType && !isMultiYear) {
                        label = fmtType(t);
                        desc = fmtType(t) + ' (' + fmtTA(y) + ')';
                    } else if (isMultiType && isMultiYear) {
                        label = `${fmtTA(y)} (${fmtTypeShort(t)})`;
                        desc = `${fmtTA(y)} • ${fmtType(t)}`;
                    } else {
                        label = fmtTA(y);
                        desc = fmtTA(y) + (isMultiType ? ' • ' + fmtType(t) : '');
                    }
                    seriesCols.push({ y, type: t, label, desc });
                });
            });

            // Ringkasan
            const peserta = new Set(DATA.rows.map(r => r.y + '|' + (r.type || '') + '|' + r.np)).size;
            const zero = pairs.filter(p => p.v <= 0).length;
            const graded = pairs.filter(p => p.v > 0);
            const avg = graded.length ? (graded.reduce((a, p) => a + p.v, 0) / graded.length) : 0;
            const th = settings.thresholds;
            const topLbl = '≥' + fmt(th[th.length - 1]);
            const top = pairs.filter(p => p.v >= th[th.length - 1]).length;
            const box = (bg, ic, val, lbl) => `<div class="col-lg-3 col-6"><div class="sum-box" style="background:${bg}"><i class="fas ${ic}"></i><div class="val">${val}</div><div class="lbl">${lbl}</div></div></div>`;
            $('summaryCards').innerHTML =
                box('linear-gradient(135deg,#3b6fe0,#5b8def)', 'fa-users', peserta, 'Total Peserta Terdaftar') +
                box('linear-gradient(135deg,#7e57c2,#a47be0)', 'fa-calculator', avg.toFixed(2), 'Rata-rata Nilai (yang sudah dinilai)') +
                box('linear-gradient(135deg,#2e9d4f,#52c27a)', 'fa-trophy', top, 'Nilai ' + topLbl + ' (peserta-materi)') +
                box('linear-gradient(135deg,#757575,#9e9e9e)', 'fa-user-clock', zero, 'Belum dinilai / tidak ikut (0)');

            // Chart umum
            const gen = $('generalCharts');
            const catsCsv = selectedCats ? selectedCats.join(',') : '';
            if (settings.generalMode === 'tahun') {
                let genTitle = 'Sebaran Nilai ';
                if (isMultiType && !isMultiYear) {
                    genTitle += 'Pra-Munaqosah vs Munaqosah — ' + yl[0];
                } else {
                    genTitle += 'per Tahun Ajaran' + (isMultiType ? ' & Type' : '') + ' (Semua materi terpilih)';
                }

                const labels = seriesCols.map(s => s.label);
                const groups = seriesCols.map(s => pairs.filter(p => p.y === s.y && p.type === s.type));

                addChartCard(gen, 'cg_tahun', genTitle,
                    labels, buildDatasets(groups, bins),
                    i => ({
                        y: seriesCols[i].y,
                        type: seriesCols[i].type,
                        cat: catsCsv,
                        tpq: '',
                        desc: seriesCols[i].desc + ' • ' + (selectedCats ? 'Materi terpilih' : 'Semua materi')
                    }));
            } else {
                const typeSubtitle = isMultiType ? ' (' + types.map(fmtTypeShort).join(' vs ') + ')' : ' (' + fmtType(types[0]) + ')';
                const ylbl = yl.join(', ') + typeSubtitle;
                addChartCard(gen, 'cg_materi', 'Sebaran Nilai per Materi — ' + ylbl,
                    cats.map(c => c.name), buildDatasets(cats.map(c => pairs.filter(p => p.cat === c.id)), bins),
                    i => ({
                        y: '',
                        type: '',
                        cat: cats[i].id,
                        tpq: '',
                        desc: cats[i].name + ' • ' + ylbl
                    }));
            }
            if (CFG.isAdmin && Object.keys(DATA.tpqs).length > 1) {
                const tpqIds = Object.keys(DATA.tpqs);
                addChartCard(gen, 'cg_tpq', 'Sebaran Nilai per TPQ',
                    tpqIds.map(i => DATA.tpqs[i]), buildDatasets(tpqIds.map(i => pairs.filter(p => p.tpq === i)), bins),
                    i => ({ y: '', type: '', cat: catsCsv, tpq: tpqIds[i], desc: DATA.tpqs[tpqIds[i]] }));
            }

            // Detail per materi
            const det = $('detailCharts');
            cats.forEach((c, i) => {
                let title = c.name + ' — ';
                if (isMultiType && !isMultiYear) {
                    title += yl[0] + ' (Pra-Munaqosah vs Munaqosah)';
                } else if (isMultiYear) {
                    title += 'per Tahun Ajaran' + (isMultiType ? ' & Type' : '');
                } else {
                    title += yl[0] + ' (' + fmtType(types[0]) + ')';
                }

                const labels = seriesCols.map(s => s.label);
                const groups = seriesCols.map(s => pairs.filter(p => p.y === s.y && p.type === s.type && p.cat === c.id));

                addChartCard(det, 'cd_' + i, title,
                    labels, buildDatasets(groups, bins),
                    k => ({
                        y: seriesCols[k].y,
                        type: seriesCols[k].type,
                        cat: c.id,
                        tpq: '',
                        desc: c.name + ' • ' + seriesCols[k].desc
                    }),
                    { catId: c.id, catName: c.name }
                );
            });

            // Render Matriks Evaluasi Kekurangan Materi Santri
            initMatriksThresholdSelect();
            renderMatriksEvaluasi();
        }

        // ---------- Matriks Evaluasi & Pemetaan Kekurangan Santri ----------
        function initMatriksThresholdSelect() {
            const sel = $('selMatriksThreshold');
            if (!sel) return;
            const curVal = sel.value;
            const thList = (settings.thresholds && settings.thresholds.length) ? settings.thresholds : defaults.thresholds;
            sel.innerHTML = thList.map(t => `<option value="${t}">Nilai &lt; ${t}</option>`).join('');
            if (curVal && thList.map(String).includes(String(curVal))) {
                sel.value = curVal;
            } else {
                sel.value = thList[0];
            }
        }

        function getMatriksData(thresholdVal) {
            if (!DATA || !DATA.rows) return { cats: [], items: [] };
            const cats = DATA.categories.filter(c => !selectedCats || selectedCats.includes(c.id));
            const th = Number(thresholdVal);

            const items = DATA.rows.map(r => {
                const scores = {};
                const kurangCats = [];
                let totalKurang = 0;

                cats.forEach(c => {
                    const v = (r.avg && r.avg[c.id] !== undefined) ? Number(r.avg[c.id]) : 0;
                    scores[c.id] = v;
                    // Nilai < threshold (termasuk nilai 0 / tidak ikut) masuk dalam kategori kekurangan
                    if (v < th) {
                        kurangCats.push(c.name);
                        totalKurang++;
                    }
                });

                return {
                    np: r.np,
                    nm: r.nm || '-',
                    tpq: r.tpq,
                    tpqName: DATA.tpqs[r.tpq] || r.tpq,
                    y: r.y,
                    type: r.type || (DATA.meta ? (Array.isArray(DATA.meta.TypeUjian) ? DATA.meta.TypeUjian[0] : DATA.meta.TypeUjian) : ''),
                    scores: scores,
                    kurangCats: kurangCats,
                    totalKurang: totalKurang
                };
            });

            return { cats, items };
        }

        function renderMatriksEvaluasi() {
            const cardEl = $('cardMatriksEvaluasi');
            if (!cardEl) return;
            if (!DATA || !DATA.rows.length) {
                cardEl.style.display = 'none';
                return;
            }
            cardEl.style.display = '';

            const selTh = $('selMatriksThreshold');
            const thVal = selTh ? Number(selTh.value || settings.thresholds[0]) : settings.thresholds[0];
            const filterMode = $('selMatriksFilterMode') ? $('selMatriksFilterMode').value : 'kurang_only';
            const showScore = $('chkMatriksShowScore') ? $('chkMatriksShowScore').checked : true;
            const search = ($('inpSearchMatriks')?.value || '').trim().toLowerCase();

            const { cats, items } = getMatriksData(thVal);

            // Filter
            let filtered = items.filter(s => {
                if (filterMode === 'kurang_only' && s.totalKurang < 1) return false;
                if (filterMode === 'kritis' && s.totalKurang < 2) return false;
                if (filterMode === 'tuntas' && s.totalKurang !== 0) return false;
                if (search) {
                    const q = search;
                    if (!s.nm.toLowerCase().includes(q) && !s.np.toLowerCase().includes(q) && !s.tpqName.toLowerCase().includes(q)) {
                        return false;
                    }
                }
                return true;
            });

            // Urutkan: Santri dengan kekurangan terbanyak di atas
            filtered.sort((a, b) => {
                if (b.totalKurang !== a.totalKurang) return b.totalKurang - a.totalKurang;
                return a.nm.localeCompare(b.nm);
            });

            // Info Count
            const totalKurangSantri = items.filter(s => s.totalKurang > 0).length;
            $('infoMatriksCount').innerHTML = `Menampilkan <strong>${filtered.length}</strong> santri | Total santri yang memiliki nilai &lt; ${thVal} (termasuk 0): <span class="text-danger font-weight-bold">${totalKurangSantri} santri</span> (dari total ${items.length} peserta)`;

            // Render Header
            const trHead = $('trMatriksHead');
            trHead.innerHTML = `
                <th style="width:40px;" class="text-center">No</th>
                <th style="width:90px;">No Peserta</th>
                <th>Nama Santri</th>
                <th>TPQ</th>
                <th style="width:65px;">T.A</th>
                <th style="width:75px;">Type</th>
                ${cats.map(c => `<th class="text-center" style="min-width:90px;">${esc(c.name)}</th>`).join('')}
                <th class="text-center font-weight-bold" style="width:110px;">Total Kurang</th>
            `;

            // Render Body
            const tbBody = $('tbMatriksBody');
            if (!filtered.length) {
                tbBody.innerHTML = `<tr><td colspan="${7 + cats.length}" class="text-center text-muted py-4"><i class="fas fa-check-circle text-success mr-1"></i> Tidak ada data santri yang memenuhi kriteria filter ini.</td></tr>`;
                return;
            }

            tbBody.innerHTML = filtered.map((s, idx) => {
                const matTds = cats.map(c => {
                    const v = s.scores[c.id];
                    if (v < thVal) {
                        const scoreDisplay = (v === 0) ? '0' : v.toFixed(2);
                        return `<td class="text-center table-danger" style="background:#ffebee;">
                            <span class="badge badge-danger" style="font-size:0.8rem;">❌ ${showScore ? scoreDisplay : ''}</span>
                        </td>`;
                    } else {
                        return `<td class="text-center">
                            ${showScore ? `<span class="text-success font-weight-bold" style="font-size:0.82rem;">✅ ${v.toFixed(2)}</span>` : `<span class="text-muted">—</span>`}
                        </td>`;
                    }
                }).join('');

                const badgeTotal = s.totalKurang > 0 ?
                    `<span class="badge badge-danger p-1 font-weight-bold" style="font-size:0.8rem;">${s.totalKurang} Materi</span>` :
                    `<span class="badge badge-success p-1" style="font-size:0.8rem;">0 (Tuntas)</span>`;

                return `<tr>
                    <td class="text-center text-muted">${idx + 1}</td>
                    <td>${esc(s.np)}</td>
                    <td><strong>${esc(s.nm)}</strong></td>
                    <td>${esc(s.tpqName)}</td>
                    <td>${esc(fmtTA(s.y))}</td>
                    <td>${badgeType(s.type)}</td>
                    ${matTds}
                    <td class="text-center">${badgeTotal}</td>
                </tr>`;
            }).join('');
        }

        // Listener Event Matriks Evaluasi
        if ($('selMatriksThreshold')) $('selMatriksThreshold').addEventListener('change', renderMatriksEvaluasi);
        if ($('selMatriksFilterMode')) $('selMatriksFilterMode').addEventListener('change', renderMatriksEvaluasi);
        if ($('chkMatriksShowScore')) $('chkMatriksShowScore').addEventListener('change', renderMatriksEvaluasi);
        if ($('inpSearchMatriks')) $('inpSearchMatriks').addEventListener('input', renderMatriksEvaluasi);

        if ($('btnCsvMatriks')) {
            $('btnCsvMatriks').addEventListener('click', () => {
                const selTh = $('selMatriksThreshold');
                const thVal = selTh ? Number(selTh.value || settings.thresholds[0]) : settings.thresholds[0];
                const showScore = $('chkMatriksShowScore') ? $('chkMatriksShowScore').checked : true;
                const { cats, items } = getMatriksData(thVal);
                if (!items.length) {
                    alert('Tidak ada data untuk diunduh');
                    return;
                }

                // Urutkan
                const sorted = items.slice().sort((a, b) => b.totalKurang - a.totalKurang || a.nm.localeCompare(b.nm));

                const q = v => '"' + String(v).replace(/"/g, '""') + '"';
                const head = ['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Tahun Ajaran', 'Type Ujian']
                    .concat(cats.map(c => c.name))
                    .concat(['Total Materi Kurang', 'Keterangan Materi Kurang (< ' + thVal + ')']);

                const lines = [head.map(q).join(',')];

                sorted.forEach((s, idx) => {
                    const row = [
                        idx + 1,
                        s.np,
                        s.nm,
                        s.tpqName,
                        fmtTA(s.y),
                        fmtType(s.type)
                    ];

                    cats.forEach(c => {
                        const v = s.scores[c.id];
                        if (v < thVal) {
                            const scoreText = (v === 0) ? '0' : v.toFixed(2);
                            row.push(showScore ? 'KURANG (' + scoreText + ')' : 'KURANG');
                        } else {
                            row.push(showScore ? 'TUNTAS (' + v.toFixed(2) + ')' : 'TUNTAS');
                        }
                    });

                    row.push(s.totalKurang);
                    row.push(s.kurangCats.join(', ') || 'Semua Tuntas');

                    lines.push(row.map(q).join(','));
                });

                const blob = new Blob(['\ufeff' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = `Matriks_Evaluasi_Kekurangan_Santri_Batas_${thVal}.csv`;
                a.click();
                setTimeout(() => URL.revokeObjectURL(a.href), 500);
            });
        }

        // ---------- Drill-down modal ----------
        let lastQuery = null; // filter saat data dimuat
        let modalItems = [];
        let modalMeta = {};
        let modalNewTabUrl = '';

        function openDetail(ctx, bin, chartTitle) {
            if (!lastQuery) return;
            const p = new URLSearchParams({
                IdTahunAjaran: ctx.y ? ctx.y : lastQuery.years.join(','),
                IdTpq: lastQuery.tpq,
                TypeUjian: ctx.type ? ctx.type : lastQuery.types.join(','),
                Cat: ctx.cat || '',
                Tpq: ctx.tpq || '',
                Min: bin.min === null || bin.min === undefined ? '' : bin.min,
                Max: bin.max === null || bin.max === undefined ? '' : bin.max,
                Zero: bin.zero ? '1' : '0',
                NZ: bin.nz ? '1' : '0'
            });
            const judul = ctx.desc + ' • Nilai ' + bin.label;
            p.set('Judul', judul);
            const qs = p.toString();
            modalNewTabUrl = CFG.detailPageUrl + '?' + qs;

            $('dmTitle').textContent = judul;
            $('dmSub').textContent = '';
            $('dmSearch').value = '';
            $('dmTable').style.display = 'none';
            $('dmError').style.display = 'none';
            $('dmLoading').style.display = '';
            const modalEl = $('detailModal');
            if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
            window.jQuery(modalEl).modal('show');

            fetch(CFG.detailDataUrl + '?' + qs, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(j => {
                    if (!j.success) throw new Error(j.message || 'Gagal memuat data');
                    modalItems = j.data.items;
                    modalMeta = j.data.meta || {};
                    $('dmTable').style.display = '';
                    renderModalRows();
                })
                .catch(e => {
                    $('dmError').style.display = '';
                    $('dmError').textContent = e.message;
                })
                .finally(() => $('dmLoading').style.display = 'none');
        }

        function renderModalRows() {
            const term = $('dmSearch').value.toLowerCase();
            const rows = modalItems.filter(i => !term || (i.nm + ' ' + i.np).toLowerCase().includes(term));
            $('dmSub').textContent = rows.length + ' data' + (term ? ' (dari ' + modalItems.length + ')' : '');
            $('dmBody').innerHTML = rows.map((i, n) => {
                const href = CFG.pesertaUrl + '?' + new URLSearchParams({
                    NoPeserta: i.np,
                    IdTahunAjaran: i.y,
                    TypeUjian: i.type || modalMeta.TypeUjian || '',
                    IdTpq: i.tpq
                }).toString();
                return `<tr><td>${n + 1}</td><td>${esc(i.np)}</td><td>${esc(i.nm)}</td><td>${esc(i.tpqName)}</td><td>${esc(fmtTA(i.y))}</td><td>${badgeType(i.type)}</td><td>${esc(i.catName)}</td>` +
                    `<td class="text-center">${i.v > 0 ? i.v.toFixed(2) : '<span class="badge badge-secondary">0</span>'}</td>` +
                    `<td class="text-center"><a class="btn btn-xs btn-outline-primary" target="_blank" href="${href}"><i class="fas fa-eye"></i></a></td></tr>`;
            }).join('') || '<tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>';
        }

        // ---------- Load ----------
        function renderMateriChips() {
            const wrap = $('materiChips');
            wrap.innerHTML = DATA.categories.map(c => {
                const on = !selectedCats || selectedCats.includes(c.id);
                return `<label class="chip"><input type="checkbox" class="chk-materi" value="${esc(c.id)}" ${on?'checked':''}><span>${esc(c.name)}</span></label>`;
            }).join('');
            $('rowMateriFilter').style.display = DATA.categories.length ? '' : 'none';
            wrap.querySelectorAll('.chk-materi').forEach(el => el.addEventListener('change', syncMateri));
        }

        function syncMateri() {
            const all = [...document.querySelectorAll('.chk-materi')];
            const on = all.filter(e => e.checked).map(e => e.value);
            selectedCats = (on.length === all.length) ? null : on;
            render();
        }

        function load() {
            if (typeof Chart === 'undefined') {
                alert('Library Chart.js belum termuat.');
                return;
            }
            let years = [];
            if (window.jQuery) {
                const val = window.jQuery('#filterTahunAjaran').val();
                years = Array.isArray(val) ? val : (val ? [val] : []);
            } else {
                years = Array.from(document.querySelectorAll('#filterTahunAjaran option:checked')).map(o => o.value);
            }
            if (!years.length) {
                $('statEmpty').style.display = '';
                $('statEmpty').textContent = 'Pilih minimal satu Tahun Ajaran.';
                $('statContent').style.display = 'none';
                return;
            }

            let types = [];
            if (window.jQuery) {
                const val = window.jQuery('#filterTypeUjian').val();
                types = Array.isArray(val) ? val : (val ? [val] : []);
            } else {
                types = Array.from(document.querySelectorAll('#filterTypeUjian option:checked')).map(o => o.value);
            }
            if (!types.length) {
                $('statEmpty').style.display = '';
                $('statEmpty').textContent = 'Pilih minimal satu Type Ujian.';
                $('statContent').style.display = 'none';
                return;
            }

            const params = new URLSearchParams({
                IdTahunAjaran: years.join(','),
                IdTpq: $('filterTpq').value || '0',
                TypeUjian: types.join(',')
            });
            $('statLoading').style.display = '';
            lastQuery = {
                years,
                tpq: params.get('IdTpq'),
                types
            };
            $('statEmpty').style.display = 'none';
            $('statContent').style.display = 'none';
            $('btnMuat').disabled = true;
            $('btnPdf').disabled = true;

            fetch(CFG.dataUrl + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(json => {
                    if (!json.success) throw new Error(json.message || 'Gagal memuat data');
                    DATA = json.data;
                    const keep = selectedCats ? selectedCats.filter(id => DATA.categories.some(c => c.id === id)) : null;
                    selectedCats = (keep && keep.length) ? keep : null;
                    if (!DATA.rows.length) {
                        $('statEmpty').style.display = '';
                        $('statEmpty').textContent = 'Belum ada data peserta untuk filter yang dipilih.';
                        $('rowMateriFilter').style.display = 'none';
                        return;
                    }
                    renderMateriChips();
                    $('statContent').style.display = '';
                    render();
                    $('btnPdf').disabled = false;
                })
                .catch(err => {
                    $('statEmpty').style.display = '';
                    $('statEmpty').textContent = err.message;
                })
                .finally(() => {
                    $('statLoading').style.display = 'none';
                    $('btnMuat').disabled = false;
                });
        }

        // ---------- Export PDF ----------
        function loadJsPdf() {
            if (window.jspdf && window.jspdf.jsPDF) {
                if (typeof window.jspdf.jsPDF.API.autoTable === 'function' || typeof window.jspdf.autoTable === 'function') {
                    return Promise.resolve(window.jspdf.jsPDF);
                }
            }
            return new Promise((resolve, reject) => {
                const s1 = document.createElement('script');
                s1.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
                s1.onload = () => {
                    const s2 = document.createElement('script');
                    s2.src = 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js';
                    s2.onload = () => resolve(window.jspdf.jsPDF);
                    s2.onerror = () => resolve(window.jspdf.jsPDF);
                    document.head.appendChild(s2);
                };
                s1.onerror = () => reject(new Error('Gagal memuat library PDF (cek koneksi internet).'));
                document.head.appendChild(s1);
            });
        }

        function whiteDataUrl(canvas) {
            const tmp = document.createElement('canvas');
            tmp.width = canvas.width;
            tmp.height = canvas.height;
            const t = tmp.getContext('2d');
            t.fillStyle = '#ffffff';
            t.fillRect(0, 0, tmp.width, tmp.height);
            t.drawImage(canvas, 0, 0);
            return tmp.toDataURL('image/png');
        }

        function cleanPdfText(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/≥/g, '>=')
                .replace(/≤/g, '<=')
                .replace(/[–—]/g, '-')
                .replace(/•/g, '-')
                .replace(/…/g, '...');
        }

        // ---------- Export PDF Interaktif & Modal Dialog ----------
        function openPdfModal() {
            if (!DATA || !charts.length) return;
            const bins = buildBins();
            const sel = $('selPdfBinFilter');
            sel.innerHTML = '<option value="all">Semua Rentang Nilai</option>' +
                bins.map((b, i) => `<option value="${i}">${esc(b.label)}</option>`).join('');

            // Reset pilihan default
            const radioAll = $('optSantriAll');
            if (radioAll) radioAll.checked = true;
            const radioMatriksKurang = $('optPdfMatriksKurang');
            if (radioMatriksKurang) radioMatriksKurang.checked = true;

            if ($('wrapPdfRentang')) $('wrapPdfRentang').style.display = '';
            if ($('wrapPdfScorePrivacy')) $('wrapPdfScorePrivacy').style.display = '';

            if (window.jQuery) {
                window.jQuery('#pdfModal').modal('show');
            }
        }

        function loadJsPdf() {
            if (window.jspdf && window.jspdf.jsPDF) {
                if (typeof window.jspdf.jsPDF.API.autoTable === 'function' || typeof window.jspdf.autoTable === 'function') {
                    return Promise.resolve(window.jspdf.jsPDF);
                }
            }
            return new Promise((resolve, reject) => {
                const s1 = document.createElement('script');
                s1.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
                s1.onload = () => {
                    const s2 = document.createElement('script');
                    s2.src = 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js';
                    s2.onload = () => resolve(window.jspdf.jsPDF);
                    s2.onerror = () => resolve(window.jspdf.jsPDF);
                    document.head.appendChild(s2);
                };
                s1.onerror = () => reject(new Error('Gagal memuat library PDF (cek koneksi internet).'));
                document.head.appendChild(s1);
            });
        }

        function whiteDataUrl(canvas) {
            const tmp = document.createElement('canvas');
            tmp.width = canvas.width;
            tmp.height = canvas.height;
            const t = tmp.getContext('2d');
            t.fillStyle = '#ffffff';
            t.fillRect(0, 0, tmp.width, tmp.height);
            t.drawImage(canvas, 0, 0);
            return tmp.toDataURL('image/png');
        }

        function cleanPdfText(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/≥/g, '>=')
                .replace(/≤/g, '<=')
                .replace(/[–—]/g, '-')
                .replace(/•/g, '-')
                .replace(/…/g, '...');
        }

        async function exportPdf(opts = {}) {
            if (!DATA || !charts.length) return;
            const btn = $('btnPdf');
            const old = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Membuat PDF…';
            try {
                const JsPDF = await loadJsPdf();
                const orientation = opts.orientation || 'portrait';
                const santriMode = opts.mode || 'all';
                const binVal = opts.bin !== undefined ? opts.bin : 'all';

                const doc = new JsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: orientation
                });

                const W = orientation === 'landscape' ? 297 : 210;
                const H = orientation === 'landscape' ? 210 : 297;
                const M = 12;
                const CW = W - M * 2;

                const tpqSel = $('filterTpq');
                const tpqText = tpqSel.options[tpqSel.selectedIndex] ? tpqSel.options[tpqSel.selectedIndex].text : '-';
                const typeText = (DATA.types || []).map(fmtType).join(', ') || '-';
                const bins = buildBins().map(b => cleanPdfText(b.label)).join('  |  ');
                const matText = selectedCats ?
                    DATA.categories.filter(c => selectedCats.includes(c.id)).map(c => c.name).join(', ') :
                    'Semua materi';

                // Header Dokumen
                doc.setFillColor(59, 111, 224);
                doc.rect(0, 0, W, 22, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(16);
                doc.text('Statistik Hasil Munaqosah', M, 11);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text('Dicetak: ' + new Date().toLocaleString('id-ID'), M, 17.5);

                let y = 29;
                doc.setTextColor(40, 40, 40);
                doc.setFontSize(9.5);
                const info = [
                    ['Tahun Ajaran', cleanPdfText(DATA.years.map(fmtTA).join(', '))],
                    ['TPQ', cleanPdfText(tpqText)],
                    ['Type Ujian', cleanPdfText(typeText)],
                    ['Materi', cleanPdfText(matText)],
                    ['Rentang Nilai', bins]
                ];
                info.forEach(([k, v]) => {
                    doc.setFont('helvetica', 'bold');
                    doc.text(k, M, y);
                    doc.setFont('helvetica', 'normal');
                    const lines = doc.splitTextToSize(': ' + v, CW - 32);
                    doc.text(lines, M + 32, y);
                    y += 5 * lines.length;
                });

                // Ringkasan
                y += 2;
                const boxes = [...document.querySelectorAll('#summaryCards .sum-box')];
                if (boxes.length) {
                    const bw = (CW - 3 * (boxes.length - 1)) / boxes.length;
                    boxes.forEach((b, i) => {
                        const x = M + i * (bw + 3);
                        doc.setFillColor(238, 241, 246);
                        doc.roundedRect(x, y, bw, 16, 2, 2, 'F');
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(13);
                        doc.setTextColor(59, 111, 224);
                        doc.text(cleanPdfText(b.querySelector('.val').textContent), x + 3, y + 7);
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(7);
                        doc.setTextColor(80, 80, 80);
                        doc.text(doc.splitTextToSize(cleanPdfText(b.querySelector('.lbl').textContent), bw - 5), x + 3, y + 11.5);
                    });
                    y += 21;
                }

                // Render Chart & Tabel Santri
                charts.forEach(ch => {
                    const cv = ch.canvas;
                    const card = cv.closest('.chart-card');
                    const h = CW * cv.height / cv.width;
                    if (y + h > H - 14) {
                        doc.addPage();
                        y = M;
                    }
                    doc.addImage(whiteDataUrl(cv), 'PNG', M, y, CW, h, undefined, 'FAST');
                    y += h + 4;

                    // Logika Pencetakan Daftar Santri
                    if (santriMode !== 'none' && card) {
                        let sData = null;
                        if (santriMode === 'active' && typeof card.getSantriExportData === 'function') {
                            const act = card.getSantriExportData();
                            if (act && act.isOpen && act.items.length) {
                                sData = act;
                            }
                        } else if (santriMode === 'all' && typeof card.getSantriDataByBin === 'function') {
                            const binData = card.getSantriDataByBin(binVal);
                            if (binData && binData.items.length) {
                                sData = binData;
                            }
                        }

                        if (sData && sData.items.length) {
                            if (y > H - 35) {
                                doc.addPage();
                                y = M;
                            }
                            doc.setFont('helvetica', 'bold');
                            doc.setFontSize(9.5);
                            doc.setTextColor(40, 40, 40);
                            doc.text(cleanPdfText(`Daftar Santri — ${sData.catName} (${sData.binLabel}): ${sData.items.length} santri`), M, y);
                            y += 3.5;

                            if (typeof doc.autoTable === 'function') {
                                const showScore = opts.showScore !== false;
                                const headCols = showScore ?
                                    [['No', 'No Peserta', 'Nama Santri', 'TPQ', 'T.A', 'Type', 'Nilai']] :
                                    [['No', 'No Peserta', 'Nama Santri', 'TPQ', 'T.A', 'Type']];

                                const bodyRows = sData.items.map((s, idx) => {
                                    const row = [
                                        idx + 1,
                                        cleanPdfText(s.np),
                                        cleanPdfText(s.nm),
                                        cleanPdfText(s.tpqName),
                                        cleanPdfText(fmtTA(s.y)),
                                        cleanPdfText(fmtTypeShort(s.type))
                                    ];
                                    if (showScore) {
                                        row.push(s.v > 0 ? s.v.toFixed(2) : '0');
                                    }
                                    return row;
                                });

                                doc.autoTable({
                                    startY: y,
                                    head: headCols,
                                    body: bodyRows,
                                    margin: { left: M, right: M },
                                    styles: { fontSize: 7.5, cellPadding: 1.5, font: 'helvetica' },
                                    headStyles: { fillColor: [59, 111, 224], textColor: 255, fontStyle: 'bold' },
                                    alternateRowStyles: { fillColor: [248, 250, 252] },
                                    theme: 'grid'
                                });
                                y = doc.lastAutoTable.finalY + 6;
                            }
                        }
                    }
                });

                // Lembar Matriks Evaluasi Kekurangan Santri jika dipilih
                const matriksMode = opts.matriksMode || 'kurang_only';
                if (matriksMode !== 'none') {
                    const bins = buildBins();
                    let thVal = Number(settings.thresholds[0]);
                    let thLabel = '< ' + thVal;

                    if (opts.bin !== 'all' && opts.bin !== null && opts.bin !== undefined) {
                        const bIdx = Number(opts.bin);
                        if (bins[bIdx]) {
                            thLabel = cleanPdfText(bins[bIdx].label);
                            if (bins[bIdx].max !== null && bins[bIdx].max !== undefined) {
                                thVal = bins[bIdx].max;
                            } else if (bins[bIdx].min !== null && bins[bIdx].min !== undefined) {
                                thVal = bins[bIdx].min;
                            }
                        }
                    } else {
                        thVal = Number($('selMatriksThreshold')?.value || settings.thresholds[0]);
                        thLabel = '< ' + thVal;
                    }

                    const showScore = opts.showScore !== false;
                    const { cats, items } = getMatriksData(thVal);

                    // Filter santri sesuai mode yang dipilih di modal PDF (khusus yang kurang / semua)
                    const printItems = (matriksMode === 'kurang_only') ? items.filter(s => s.totalKurang > 0) : items;

                    if (printItems.length) {
                        doc.addPage();
                        y = M;

                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(11);
                        doc.setTextColor(59, 111, 224);
                        doc.text(cleanPdfText(`Matriks Evaluasi Kekurangan Materi Santri (${thLabel})`), M, y);
                        y += 4.5;
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(8);
                        doc.setTextColor(80, 80, 80);
                        const subInfo = (matriksMode === 'kurang_only') ?
                            `Tercetak: ${printItems.length} santri yang memiliki nilai ${thLabel} (dari total ${items.length} peserta)` :
                            `Tercetak: Semua peserta (${printItems.length} santri) | Perlu Bimbingan / Nilai ${thLabel}: ${items.filter(s => s.totalKurang > 0).length} santri`;
                        doc.text(cleanPdfText(subInfo), M, y);
                        y += 4;

                        // Urutkan kekurangan terbanyak
                        const sortedItems = printItems.slice().sort((a, b) => b.totalKurang - a.totalKurang || a.nm.localeCompare(b.nm));

                        const headColsMatriks = [
                            ['No', 'No Peserta', 'Nama Santri', 'TPQ', 'T.A', ...cats.map(c => cleanPdfText(c.name)), 'Jml Kurang']
                        ];

                        const bodyRowsMatriks = sortedItems.map((s, idx) => {
                            const row = [
                                idx + 1,
                                cleanPdfText(s.np),
                                cleanPdfText(s.nm),
                                cleanPdfText(s.tpqName),
                                cleanPdfText(fmtTA(s.y))
                            ];

                            cats.forEach(c => {
                                const v = s.scores[c.id];
                                if (v < thVal) {
                                    const vText = (v === 0) ? '0' : v.toFixed(1);
                                    row.push(showScore ? `X (${vText})` : 'X');
                                } else {
                                    row.push(showScore ? `${v.toFixed(1)}` : '-');
                                }
                            });

                            row.push(s.totalKurang > 0 ? `${s.totalKurang}` : '0');
                            return row;
                        });

                        if (typeof doc.autoTable === 'function') {
                            doc.autoTable({
                                startY: y,
                                head: headColsMatriks,
                                body: bodyRowsMatriks,
                                margin: { left: M, right: M },
                                styles: { fontSize: 6.8, cellPadding: 1.2, font: 'helvetica' },
                                headStyles: { fillColor: [220, 53, 69], textColor: 255, fontStyle: 'bold' },
                                alternateRowStyles: { fillColor: [253, 242, 242] },
                                theme: 'grid',
                                didParseCell: function(data) {
                                    if (data.section === 'body') {
                                        const cellText = String(data.cell.raw || '');
                                        if (cellText.startsWith('X')) {
                                            data.cell.styles.textColor = [220, 53, 69];
                                            data.cell.styles.fontStyle = 'bold';
                                        }
                                    }
                                }
                            });
                            y = doc.lastAutoTable.finalY + 6;
                        }
                    }
                }

                // Footer nomor halaman
                const total = doc.getNumberOfPages();
                for (let p = 1; p <= total; p++) {
                    doc.setPage(p);
                    doc.setFontSize(8);
                    doc.setTextColor(130, 130, 130);
                    doc.text('TPQ Smart - Statistik Hasil Munaqosah', M, H - 6);
                    doc.text('Halaman ' + p + ' / ' + total, W - M, H - 6, {
                        align: 'right'
                    });
                }

                const nama = 'Statistik_Munaqosah_' + DATA.years.join('-') + '.pdf';
                doc.save(nama);
            } catch (err) {
                alert(err.message || 'Gagal membuat PDF');
            } finally {
                btn.innerHTML = old;
                btn.disabled = false;
            }
        }

        // ---------- Events ----------
        $('btnPdf').addEventListener('click', openPdfModal);

        // Listener tombol proses di dalam modal PDF
        if ($('btnProsesExportPdf')) {
            $('btnProsesExportPdf').addEventListener('click', () => {
                const sMode = document.querySelector('input[name="pdfSantriMode"]:checked')?.value || 'all';
                const bFilter = $('selPdfBinFilter') ? $('selPdfBinFilter').value : 'all';
                const orient = document.querySelector('input[name="pdfOrientation"]:checked')?.value || 'portrait';
                const showScore = $('chkPdfShowScore') ? $('chkPdfShowScore').checked : true;
                const mMode = document.querySelector('input[name="pdfMatriksMode"]:checked')?.value || 'kurang_only';

                if (window.jQuery) {
                    window.jQuery('#pdfModal').modal('hide');
                }
                exportPdf({
                    mode: sMode,
                    bin: bFilter,
                    orientation: orient,
                    showScore: showScore,
                    matriksMode: mMode
                });
            });
        }

        // Toggle tampilan pilihan rentang & privasi jika opsi "Tidak" dipilih di modal
        document.querySelectorAll('input[name="pdfSantriMode"]').forEach(r => {
            r.addEventListener('change', e => {
                const isNone = (e.target.value === 'none');
                if ($('wrapPdfRentang')) {
                    $('wrapPdfRentang').style.display = isNone ? 'none' : '';
                }
                if ($('wrapPdfScorePrivacy')) {
                    $('wrapPdfScorePrivacy').style.display = isNone ? 'none' : '';
                }
            });
        });

        $('dmSearch').addEventListener('input', renderModalRows);
        $('dmNewTab').addEventListener('click', () => {
            if (modalNewTabUrl) window.open(modalNewTabUrl, '_blank');
            window.jQuery($('detailModal')).modal('hide');
        });
        function applyThreshold(arr) {
            if (!arr.length) {
                alert('Masukkan minimal satu batas nilai (angka 1-100), contoh: 65, 70');
                return;
            }
            settings.thresholds = arr;
            $('inpThreshold').value = arr.join(', ');
            saveLS();
            render();
        }

        $('btnMuat').addEventListener('click', load);
        $('btnTerapkan').addEventListener('click', () => applyThreshold(parseThresholds($('inpThreshold').value)));
        $('inpThreshold').addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyThreshold(parseThresholds($('inpThreshold').value));
            }
        });
        $('btnReset').addEventListener('click', () => applyThreshold(defaults.thresholds.slice()));
        document.querySelectorAll('.preset').forEach(a => a.addEventListener('click', e => {
            e.preventDefault();
            applyThreshold(parseThresholds(a.dataset.v));
        }));
        $('selChartType').addEventListener('change', e => {
            settings.chartType = e.target.value;
            saveLS();
            render();
        });
        $('selValueMode').addEventListener('change', e => {
            settings.valueMode = e.target.value;
            saveLS();
            render();
        });
        $('selGeneralMode').addEventListener('change', e => {
            settings.generalMode = e.target.value;
            saveLS();
            render();
        });
        $('swZero').addEventListener('change', e => {
            settings.separateZero = e.target.checked;
            saveLS();
            render();
        });
        $('matSemua').addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.chk-materi').forEach(c => c.checked = true);
            syncMateri();
        });
        $('matNone').addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.chk-materi').forEach(c => c.checked = false);
            syncMateri();
        });

        // Tombol cepat filter tahun ajaran
        if ($('taSemua')) {
            $('taSemua').addEventListener('click', e => {
                e.preventDefault();
                const all = Array.from(document.querySelectorAll('#filterTahunAjaran option')).map(o => o.value);
                if (window.jQuery) {
                    window.jQuery('#filterTahunAjaran').val(all).trigger('change');
                }
            });
        }
        if ($('taCurrent')) {
            $('taCurrent').addEventListener('click', e => {
                e.preventDefault();
                const cur = '<?= esc($defaultTahun) ?>';
                if (window.jQuery) {
                    window.jQuery('#filterTahunAjaran').val([cur]).trigger('change');
                }
            });
        }
        if ($('taReset')) {
            $('taReset').addEventListener('click', e => {
                e.preventDefault();
                if (window.jQuery) {
                    window.jQuery('#filterTahunAjaran').val([]).trigger('change');
                }
            });
        }

        // Tombol cepat filter Type Ujian
        if ($('typeSemua')) {
            $('typeSemua').addEventListener('click', e => {
                e.preventDefault();
                const all = Array.from(document.querySelectorAll('#filterTypeUjian option')).map(o => o.value);
                if (window.jQuery) {
                    window.jQuery('#filterTypeUjian').val(all).trigger('change');
                }
            });
        }
        if ($('typeReset')) {
            $('typeReset').addEventListener('click', e => {
                e.preventDefault();
                if (window.jQuery) {
                    window.jQuery('#filterTypeUjian').val([]).trigger('change');
                }
            });
        }

        // Inisialisasi Select2 untuk Filter Tahun Ajaran & Type Ujian
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('#filterTahunAjaran').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih satu atau lebih Tahun Ajaran...',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });
            window.jQuery('#filterTypeUjian').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Type Ujian...',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });
        }

        // Inisialisasi kontrol dari settings
        if (!settings.thresholds || !settings.thresholds.length) settings.thresholds = defaults.thresholds.slice();
        $('inpThreshold').value = settings.thresholds.join(', ');
        $('selChartType').value = settings.chartType;
        $('selValueMode').value = settings.valueMode;
        $('selGeneralMode').value = settings.generalMode;
        $('swZero').checked = !!settings.separateZero;
        renderBinPreview();
        load();
    })();
</script>
<?= $this->endSection(); ?>
