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
                                <option value="munaqosah" selected>Munaqosah</option>
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

        <div id="statContent" style="display:none;">
            <!-- RINGKASAN -->
            <div class="row" id="summaryCards"></div>

            <!-- CHART UMUM -->
            <h5 class="stat-section"><i class="fas fa-layer-group mr-1"></i> Chart Umum</h5>
            <div id="generalCharts"></div>

            <!-- CHART DETAIL PER MATERI -->
            <h5 class="stat-section"><i class="fas fa-book-open mr-1"></i> Detail per Jenis Materi</h5>
            <div id="detailCharts"></div>
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
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 15
                            }
                        },
                        tooltip: {
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

        function addChartCard(container, id, title, labels, datasets, ctxFn) {
            const card = document.createElement('div');
            card.className = 'card chart-card';
            card.innerHTML = `
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <strong class="small">${esc(title)}</strong>
                <div>
                    <button class="btn btn-xs btn-outline-secondary btn-tbl" type="button"><i class="fas fa-table"></i> Tabel</button>
                    <button class="btn btn-xs btn-outline-success btn-csv" type="button"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn btn-xs btn-outline-primary btn-png" type="button"><i class="fas fa-image"></i> PNG</button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-box"><canvas id="${id}"></canvas></div>
                <div class="tbl-wrap mt-3" style="display:none;">${tableHtml(labels, datasets)}</div>
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
                    }));
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
            if (window.jspdf && window.jspdf.jsPDF) return Promise.resolve(window.jspdf.jsPDF);
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
                s.onload = () => resolve(window.jspdf.jsPDF);
                s.onerror = () => reject(new Error('Gagal memuat library PDF (cek koneksi internet).'));
                document.head.appendChild(s);
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

        async function exportPdf() {
            if (!DATA || !charts.length) return;
            const btn = $('btnPdf');
            const old = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Membuat PDF…';
            try {
                const JsPDF = await loadJsPdf();
                const doc = new JsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                });
                const W = 210,
                    H = 297,
                    M = 12,
                    CW = W - M * 2;
                const tpqSel = $('filterTpq');
                const tpqText = tpqSel.options[tpqSel.selectedIndex] ? tpqSel.options[tpqSel.selectedIndex].text : '-';
                const typeText = (DATA.types || []).map(fmtType).join(', ') || '-';
                const bins = buildBins().map(b => cleanPdfText(b.label)).join('  |  ');
                const matText = selectedCats ?
                    DATA.categories.filter(c => selectedCats.includes(c.id)).map(c => c.name).join(', ') :
                    'Semua materi';

                // Header
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
                    const bw = (CW - 3 * 3) / boxes.length;
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

                // Chart
                charts.forEach(ch => {
                    const cv = ch.canvas;
                    const h = CW * cv.height / cv.width;
                    if (y + h > H - 14) {
                        doc.addPage();
                        y = M;
                    }
                    doc.addImage(whiteDataUrl(cv), 'PNG', M, y, CW, h, undefined, 'FAST');
                    y += h + 4;
                });

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
        $('btnPdf').addEventListener('click', exportPdf);
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
