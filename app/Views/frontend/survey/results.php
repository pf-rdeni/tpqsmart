<?= $this->extend('frontend/survey/template/survey_template') ?>

<?= $this->section('content') ?>
<!-- Chart.js for detail charts -->
<?php if ($survey['public_result_mode'] === 'detail'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-10">
        
        <!-- Header Banner Survey Card -->
        <div class="card survey-public-card" style="border-top: 6px solid var(--theme-color);">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-3">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <span class="badge badge-light border text-muted px-2 py-1 mr-2"><i class="fas fa-chart-pie mr-1 text-primary"></i> Halaman Hasil Publik</span>
                        <span class="badge badge-pill badge-primary">Total Respon: <?= $total_response ?></span>
                    </div>
                    <a href="<?= base_url('survey/' . $survey['survey_key']) ?>" class="btn btn-theme-outline btn-sm rounded-pill py-1 px-3">
                        <i class="fas fa-edit mr-1"></i> Isi Survey
                    </a>
                </div>
                <h1 class="survey-title"><?= esc($survey['title']) ?></h1>
                <?php if ($survey['description']): ?>
                    <div class="survey-desc ql-view mt-3"><?= $survey['description'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation Tabs if Detail mode is active -->
        <?php if ($survey['public_result_mode'] === 'detail'): ?>
            <ul class="nav nav-pills mb-4 justify-content-center shadow-xs p-2 bg-white rounded-pill" id="resultTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active rounded-pill px-4" id="summary-tab" data-toggle="tab" href="#summary-content" role="tab" aria-selected="true">
                        <i class="fas fa-chart-bar mr-2"></i> Ringkasan Jawaban
                    </a>
                </li>
                <?php if (in_array($target_type, ['guru', 'santri', 'tpq'])): ?>
                    <li class="nav-item ml-2">
                        <a class="nav-link rounded-pill px-4" id="status-tab" data-toggle="tab" href="#status-content" role="tab" aria-selected="false">
                            <i class="fas fa-users-cog mr-2"></i> Status Pengisian
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <div class="tab-content" id="resultTabsContent">
            <!-- TAB 1: SUMMARY GRAPHICS (Only shown/active in detail mode) -->
            <?php if ($survey['public_result_mode'] === 'detail'): ?>
                <div class="tab-pane fade show active" id="summary-content" role="tabpanel">
                    <?php if ($total_response === 0): ?>
                        <div class="card survey-public-card text-center p-5">
                            <div class="card-body">
                                <i class="fas fa-info-circle text-muted fa-3x mb-3"></i>
                                <h4>Belum Ada Tanggapan</h4>
                                <p class="text-muted">Grafik hasil akan ditampilkan setelah responden mengisi survey ini.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Loop questions to generate charts -->
                        <?php foreach ($questions as $q): ?>
                            <?php 
                            if (in_array($q['question_type'], ['image_display', 'video_display'])) continue;
                            
                            // Sembunyikan pertanyaan isian teks, kecuali jika berupa angka (is_numeric_chart)
                            $isText = in_array($q['question_type'], ['text_short', 'text_paragraph']);
                            $qId = $q['id'];
                            $qSum = $summary[$qId] ?? [];
                            $isNumericChart = !empty($qSum['is_numeric_chart']);
                            
                            echo "<!-- Debug QID: {$qId} | Type: {$q['question_type']} | isText: " . ($isText ? 'yes' : 'no') . " | isNumericChart: " . ($isNumericChart ? 'yes' : 'no') . " | qSum: " . json_encode($qSum) . " -->";
                            
                            if ($isText && !$isNumericChart) {
                                continue;
                            }
                            ?>
                            <div class="card survey-public-card mb-4">
                                <div class="card-body p-4">
                                    <h5 class="font-weight-bold mb-3 ql-view"><?= $q['question_text'] ?></h5>
                                    
                                    <?php if (in_array($q['question_type'], ['text_short', 'text_paragraph']) && !$isNumericChart): ?>
                                        <!-- Show list of text answers -->
                                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;" class="p-3 bg-light">
                                            <?php if (empty($qSum['answers'])): ?>
                                                <span class="text-muted small">Tidak ada jawaban.</span>
                                            <?php else: ?>
                                                <?php foreach ($qSum['answers'] as $ans): ?>
                                                    <div class="border-bottom py-2 small"><?= esc($ans) ?></div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif (in_array($q['question_type'], ['multiple_choice', 'checkbox', 'dropdown', 'linear_scale', 'rating']) || ($isText && $isNumericChart)): ?>
                                        <!-- Render Chart.js Canvas -->
                                        <div class="d-flex justify-content-center">
                                            <div style="width: 100%; max-width: 500px; height: 250px;">
                                                <canvas id="chart-<?= $qId ?>"></canvas>
                                            </div>
                                        </div>
                                    <?php elseif (in_array($q['question_type'], ['grid_multiple', 'grid_checkbox'])): ?>
                                        <!-- Render Grid Table stats -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm table-striped text-center small mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Baris</th>
                                                        <?php foreach ($qSum['columns'] ?? [] as $col): ?>
                                                            <th><?= esc($col) ?></th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($qSum['rows'] ?? [] as $row => $colStats): ?>
                                                        <tr>
                                                            <td class="text-left font-weight-bold"><?= esc($row) ?></td>
                                                            <?php foreach ($qSum['columns'] ?? [] as $col): ?>
                                                                <td><?= $colStats[$col] ?? 0 ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php elseif ($q['question_type'] === 'file_upload'): ?>
                                        <div class="alert alert-light py-2 px-3 small border">
                                            <i class="fas fa-file mr-2 text-info"></i> <?= count($qSum['files'] ?? []) ?> file terunggah. File di-kelola oleh administrator.
                                        </div>
                                    <?php elseif (in_array($q['question_type'], ['master_tpq', 'master_guru', 'master_santri'])): ?>
                                        <!-- Master Data selections stats -->
                                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;" class="p-3 bg-light">
                                            <?php if (empty($qSum['counts'])): ?>
                                                <span class="text-muted small">Tidak ada data terdeteksi.</span>
                                            <?php else: ?>
                                                <?php foreach ($qSum['counts'] as $name => $cnt): ?>
                                                    <div class="d-flex justify-content-between border-bottom py-1 small">
                                                        <span><?= esc($name) ?></span>
                                                        <span class="badge badge-light border"><?= $cnt ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- TAB 2 / MAIN CONTENT: FILLING STATUS (Shown always for targets, active/shown in summary mode) -->
            <?php
            $isStatusActive = ($survey['public_result_mode'] === 'summary');
            ?>
            <?php if (in_array($target_type, ['guru', 'santri', 'tpq'])): ?>
                <div class="tab-pane fade <?= $isStatusActive ? 'show active' : '' ?>" id="status-content" role="tabpanel">
                    
                    <!-- Filter and Stats Row -->
                    <div class="card survey-public-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="form-group mb-0">
                                        <label for="public-tpq-filter" class="font-weight-bold">Filter Lembaga TPQ:</label>
                                        <select class="form-control" id="public-tpq-filter">
                                            <option value="">-- Tampilkan Semua TPQ --</option>
                                            <?php foreach ($tpqs as $tpq): ?>
                                                <option value="<?= $tpq['IdTpq'] ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <h4 class="mb-1 font-weight-bold" id="filling-ratio">0 / 0</h4>
                                    <div class="progress progress-sm w-50 ml-auto d-none d-md-flex" style="height: 6px;">
                                        <div class="progress-bar bg-success" id="ratio-progress-bar" style="width: 0%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Double columns lists: Already vs Not Yet -->
                    <div class="row">
                        <!-- Column 1: Already Filled -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-xs" style="border-radius: 12px; border: 1px solid #c3e6cb;">
                                <div class="card-header bg-success text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                                    <h5 class="mb-0 text-white font-weight-bold"><i class="fas fa-check-circle mr-2"></i> Sudah Mengisi</h5>
                                </div>
                                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;" id="list-sudah">
                                    <!-- Dynamic list of fillers -->
                                </div>
                            </div>
                        </div>

                        <!-- Column 2: Not Yet Filled -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-xs" style="border-radius: 12px; border: 1px solid #f5c6cb;">
                                <div class="card-header bg-danger text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                                    <h5 class="mb-0 text-white font-weight-bold"><i class="fas fa-times-circle mr-2"></i> Belum Mengisi</h5>
                                </div>
                                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;" id="list-belum">
                                    <!-- Dynamic list of non-fillers -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    
    // 1. Initial Load of Filling Status if target is applicable
    <?php if (in_array($target_type, ['guru', 'santri', 'tpq'])): ?>
        loadFillingStatus();

        $('#public-tpq-filter').on('change', function() {
            loadFillingStatus($(this).val());
        });
    <?php endif; ?>

    // 2. Initialize ChartJS charts in detail mode
    <?php if ($survey['public_result_mode'] === 'detail' && $total_response > 0): ?>
        renderSummaryCharts();
    <?php endif; ?>
});

// =============================================================
// Status Pengisian AJAX Loader
// =============================================================
function loadFillingStatus(tpqId = '') {
    const listSudah = $('#list-sudah');
    const listBelum = $('#list-belum');
    
    listSudah.html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>');
    listBelum.html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</div>');

    $.ajax({
        url: '<?= base_url("survey/{$survey['survey_key']}/filling-status-data") ?>',
        method: 'GET',
        data: { tpq_id: tpqId },
        success: function(response) {
            if (response.success && response.data) {
                listSudah.empty();
                listBelum.empty();

                const sudah = response.data.filled || [];
                const belum = response.data.unfilled || [];

                // Update Ratio Counter
                const total = sudah.length + belum.length;
                const percent = total > 0 ? Math.round((sudah.length / total) * 100) : 0;
                
                $('#filling-ratio').text(`${sudah.length} dari ${total} Responden (${percent}%)`);
                $('#ratio-progress-bar').css('width', percent + '%');

                // Render sudah
                if (sudah.length === 0) {
                    listSudah.html('<div class="text-center py-4 text-muted">Belum ada data.</div>');
                } else {
                    sudah.forEach(item => {
                        listSudah.append(`
                            <div class="d-flex align-items-center justify-content-between p-2 border-bottom hover-bg-light">
                                <div>
                                    <div class="font-weight-bold text-dark small">${item.name}</div>
                                    <div class="text-muted small">${item.tpq_name || '-'}</div>
                                </div>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Sudah</span>
                            </div>
                        `);
                    });
                }

                // Render belum
                if (belum.length === 0) {
                    listBelum.html('<div class="text-center py-4 text-success font-weight-bold">Hebat! Semua target sudah mengisi.</div>');
                } else {
                    belum.forEach(item => {
                        listBelum.append(`
                            <div class="d-flex align-items-center justify-content-between p-2 border-bottom hover-bg-light">
                                <div>
                                    <div class="font-weight-bold text-dark small">${item.name}</div>
                                    <div class="text-muted small">${item.tpq_name || '-'}</div>
                                </div>
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation mr-1"></i>Belum</span>
                            </div>
                        `);
                    });
                }
            }
        }
    });
}

// =============================================================
// ChartJS render helper
// =============================================================
function renderSummaryCharts() {
    // Register the chartjs-plugin-datalabels
    Chart.register(ChartDataLabels);

    const summaryData = <?= json_encode($summary) ?>;
    const questions = <?= json_encode($questions) ?>;

    questions.forEach(q => {
        const qId = q.id;
        const qSum = summaryData[qId];
        if (!qSum) return;

        const isNumeric = !!qSum.is_numeric_chart;
        if (!['multiple_choice', 'checkbox', 'dropdown', 'linear_scale', 'rating'].includes(q.question_type) && !isNumeric) return;
        
        if (!qSum.labels || qSum.labels.length === 0) return;

        const ctx = document.getElementById(`chart-${qId}`);
        if (!ctx) return;

        // Colors palette
        const colors = [
            '#4285F4', '#34A853', '#FBBC05', '#EA4335',
            '#9b5de5', '#f15bb5', '#00bbf9', '#00f5d4',
            '#8338ec', '#ff006e', '#ffbe0b', '#3a86c8'
        ];

        // Choose chart type
        // MC / dropdown / linear_scale -> Pie / Doughnut
        // Checkbox -> Horizontal Bar (as multiple selection means sum > total)
        // Numeric -> Vertical Bar (value frequencies)
        const isCheckbox = q.question_type === 'checkbox';
        const chartType = (isCheckbox || isNumeric) ? 'bar' : 'pie';

        const chartConfig = {
            type: chartType,
            data: {
                labels: qSum.labels,
                datasets: [{
                    data: qSum.counts,
                    backgroundColor: colors.slice(0, qSum.labels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: (isCheckbox || isNumeric) ? 'none' : 'right',
                        labels: {
                            boxWidth: 12,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: (isCheckbox || isNumeric) ? '#333' : '#fff',
                        textStrokeColor: (isCheckbox || isNumeric) ? null : 'rgba(0, 0, 0, 0.7)',
                        textStrokeWidth: (isCheckbox || isNumeric) ? 0 : 2.5,
                        anchor: (isCheckbox || isNumeric) ? 'end' : 'center',
                        align: (isCheckbox || isNumeric) ? 'end' : 'center',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${value} (${percentage}%)`;
                        }
                    }
                }
            }
        };

        if (isCheckbox) {
            chartConfig.options.indexAxis = 'y';
            chartConfig.options.scales = {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grace: '10%'
                }
            };
        } else if (isNumeric) {
            chartConfig.options.scales = {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grace: '10%'
                }
            };
        }

        new Chart(ctx, chartConfig);
    });
}
</script>
<style>
.hover-bg-light:hover {
    background-color: #f8f9fa;
}
</style>
<?= $this->endSection() ?>
