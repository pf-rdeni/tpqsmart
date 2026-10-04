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
                    <div class="col-lg-5 col-md-12 mb-2">
                        <label class="small mb-1 font-weight-bold">Tahun Ajaran <span class="text-muted font-weight-normal">(maks. 12)</span></label>
                        <div class="chip-wrap" id="tahunChips">
                            <?php foreach ($tahunList as $t): ?>
                                <label class="chip">
                                    <input type="checkbox" class="chk-tahun" value="<?= esc($t) ?>" <?= $t === $defaultTahun ? 'checked' : '' ?>>
                                    <span><?= esc($t) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
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
                    <div class="col-lg-2 col-md-6 mb-2">
                        <label class="small mb-1 font-weight-bold" for="filterTypeUjian">Type Ujian</label>
                        <select id="filterTypeUjian" class="form-control form-control-sm">
                            <?php if ($isAdmin || ($aktiveTombolKelulusan && ($isOperator || $isKepalaTpq))): ?>
                                <option value="munaqosah">Munaqosah</option>
                            <?php endif; ?>
                            <option value="pra-munaqosah">Pra-Munaqosah</option>
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
                    zero: false
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
                data: counts.map(c => {
                    if (settings.valueMode === 'percent') {
                        const tot = c.reduce((a, x) => a + x, 0);
                        return tot ? Math.round(c[bi] * 1000 / tot) / 10 : 0;
                    }
                    return c[bi];
                })
            }));
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

        function chartConfig(labels, datasets, title) {
            const stacked = settings.chartType === 'stacked';
            const line = settings.chartType === 'line';
            const pct = settings.valueMode === 'percent';
            return {
                type: line ? 'line' : 'bar',
                data: {
                    labels,
                    datasets: datasets.map(d => ({
                        label: d.label,
                        data: d.data,
                        backgroundColor: d.color,
                        borderColor: d.color,
                        borderWidth: line ? 2.5 : 0,
                        tension: .3,
                        fill: false,
                        pointRadius: line ? 4 : 0,
                        borderRadius: line ? 0 : 3
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 500
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
                                label: c => `${c.dataset.label}: ${c.parsed.y}${pct ? '%' : ''}`
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
                            grace: line || !stacked ? '12%' : 0,
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

        function addChartCard(container, id, title, labels, datasets) {
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
            const inst = new Chart(canvas, chartConfig(labels, datasets, title));
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
            const years = DATA.years;
            const catName = id => (DATA.categories.find(c => c.id === id) || {
                name: id
            }).name;
            const cats = DATA.categories.filter(c => !selectedCats || selectedCats.includes(c.id));

            if (!pairs.length) {
                $('summaryCards').innerHTML = '';
                $('generalCharts').innerHTML = '<div class="alert alert-warning">Tidak ada data untuk filter yang dipilih.</div>';
                return;
            }

            // Ringkasan
            const peserta = new Set(DATA.rows.map(r => r.y + '|' + r.np)).size;
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
            if (settings.generalMode === 'tahun') {
                addChartCard(gen, 'cg_tahun', 'Sebaran Nilai per Tahun Ajaran (semua materi terpilih)',
                    years, buildDatasets(years.map(y => pairs.filter(p => p.y === y)), bins));
            } else {
                const ylbl = years.length === 1 ? years[0] : years.join(', ');
                addChartCard(gen, 'cg_materi', 'Sebaran Nilai per Materi — ' + ylbl,
                    cats.map(c => c.name), buildDatasets(cats.map(c => pairs.filter(p => p.cat === c.id)), bins));
            }
            if (CFG.isAdmin && Object.keys(DATA.tpqs).length > 1) {
                const tpqIds = Object.keys(DATA.tpqs);
                addChartCard(gen, 'cg_tpq', 'Sebaran Nilai per TPQ',
                    tpqIds.map(i => DATA.tpqs[i]), buildDatasets(tpqIds.map(i => pairs.filter(p => p.tpq === i)), bins));
            }

            // Detail per materi
            const det = $('detailCharts');
            cats.forEach((c, i) => {
                addChartCard(det, 'cd_' + i, c.name + ' — per Tahun Ajaran',
                    years, buildDatasets(years.map(y => pairs.filter(p => p.y === y && p.cat === c.id)), bins));
            });
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
            const years = [...document.querySelectorAll('.chk-tahun:checked')].map(e => e.value);
            if (!years.length) {
                $('statEmpty').style.display = '';
                $('statEmpty').textContent = 'Pilih minimal satu Tahun Ajaran.';
                $('statContent').style.display = 'none';
                return;
            }
            const params = new URLSearchParams({
                IdTahunAjaran: years.join(','),
                IdTpq: $('filterTpq').value || '0',
                TypeUjian: $('filterTypeUjian').value
            });
            $('statLoading').style.display = '';
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
            t.fillStyle = '#fff';
            t.fillRect(0, 0, tmp.width, tmp.height);
            t.drawImage(canvas, 0, 0);
            return tmp.toDataURL('image/jpeg', 0.92);
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
                const typeSel = $('filterTypeUjian');
                const typeText = typeSel.options[typeSel.selectedIndex].text;
                const bins = buildBins().map(b => b.label).join('  |  ');
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
                    ['Tahun Ajaran', DATA.years.join(', ')],
                    ['TPQ', tpqText],
                    ['Type Ujian', typeText],
                    ['Materi', matText],
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
                        doc.text(b.querySelector('.val').textContent, x + 3, y + 7);
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(7);
                        doc.setTextColor(80, 80, 80);
                        doc.text(doc.splitTextToSize(b.querySelector('.lbl').textContent, bw - 5), x + 3, y + 11.5);
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
                    doc.addImage(whiteDataUrl(cv), 'JPEG', M, y, CW, h);
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
