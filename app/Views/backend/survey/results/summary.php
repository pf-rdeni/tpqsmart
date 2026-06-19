<?php if ($total_response === 0): ?>
    <div class="text-center py-5 text-muted">
        <i class="fas fa-info-circle fa-3x mb-3 text-secondary"></i>
        <h5>Belum Ada Tanggapan</h5>
        <p class="small">Data analitik akan terbuat otomatis setelah responden mengirimkan jawaban.</p>
    </div>
<?php else: ?>
    <!-- Daily Response Line Chart -->
    <div class="card card-outline card-info shadow-xs mb-4">
        <div class="card-header py-2">
            <h6 class="card-title font-weight-bold text-info mb-0"><i class="fas fa-chart-line mr-1"></i> Tren Tanggapan (Harian)</h6>
        </div>
        <div class="card-body py-2 px-3">
            <div style="height: 160px; position: relative;">
                <canvas id="daily-timeline-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Loop through questions to render analytics -->
    <?php foreach ($questions as $q): ?>
        <?php 
        if (in_array($q['question_type'], ['image_display', 'video_display'])) continue; 
        $qId = $q['id'];
        $qSum = $summary[$qId] ?? [];
        ?>
        <div class="card card-outline card-secondary shadow-xs mb-3">
            <div class="card-body">
                <h6 class="font-weight-bold text-dark mb-2 ql-view"><?= $q['question_text'] ?></h6>
                <?php if ($q['description']): ?>
                    <div class="text-muted small mt-0 mb-3 ql-view"><?= $q['description'] ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- Analytics content based on type -->
                    <?php if (in_array($q['question_type'], ['text_short', 'text_paragraph']) && empty($qSum['is_numeric_chart'])): ?>
                        <div class="col-12">
                            <span class="text-muted small d-block mb-2"><i class="far fa-comments mr-1"></i> Daftar Jawaban Terkini (Maksimal 10):</span>
                            <div class="p-3 bg-light rounded" style="max-height: 250px; overflow-y: auto;">
                                <?php if (empty($qSum['answers'])): ?>
                                    <span class="text-muted small">Tidak ada respon.</span>
                                <?php else: ?>
                                    <?php foreach (array_slice($qSum['answers'], 0, 10) as $ans): ?>
                                        <div class="py-2 border-bottom small text-dark"><?= esc($ans) ?></div>
                                    <?php endforeach; ?>
                                    <?php if (count($qSum['answers']) > 10): ?>
                                        <div class="text-center pt-2">
                                            <a href="javascript:void(0)" onclick="$('#tab-responses-link').tab('show')" class="small font-weight-bold text-primary">Lihat semua <?= count($qSum['answers']) ?> jawaban &rarr;</a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php elseif (in_array($q['question_type'], ['multiple_choice', 'checkbox', 'dropdown', 'linear_scale', 'rating']) || !empty($qSum['is_numeric_chart'])): ?>
                        <!-- Chart col -->
                        <div class="col-md-5 d-flex justify-content-center align-items-center mb-3 mb-md-0">
                            <div style="width: 100%; max-width: 250px; height: 180px; position: relative;">
                                <canvas id="chart-sub-<?= $qId ?>"></canvas>
                            </div>
                        </div>
                        <!-- Data Table col -->
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm small mb-0">
                                    <thead>
                                        <tr>
                                            <th>Opsi Jawaban</th>
                                            <th style="width: 20%" class="text-center">Respon</th>
                                            <th style="width: 20%" class="text-center">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $labels = $qSum['labels'] ?? [];
                                        $counts = $qSum['counts'] ?? [];
                                        $totalQ = array_sum($counts) ?: 1;
                                        ?>
                                        <?php foreach ($labels as $idx => $label): ?>
                                            <?php 
                                            $cnt = $counts[$idx] ?? 0;
                                            $pct = round(($cnt / $totalQ) * 100, 1);
                                            ?>
                                            <tr>
                                                <td><?= esc($label) ?></td>
                                                <td class="text-center font-weight-bold"><?= $cnt ?></td>
                                                <td class="text-center"><?= $pct ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif (in_array($q['question_type'], ['grid_multiple', 'grid_checkbox'])): ?>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-striped text-center small mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Pernyataan Baris</th>
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
                                                    <td>
                                                        <span class="badge badge-light border px-2 font-weight-bold">
                                                            <?= $colStats[$col] ?? 0 ?>
                                                        </span>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($q['question_type'] === 'file_upload'): ?>
                        <div class="col-12">
                            <span class="text-muted small d-block mb-2"><i class="fas fa-paperclip mr-1 text-primary"></i> Daftar File yang Diunggah (Maksimal 5):</span>
                            <div class="p-2 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                                <?php if (empty($qSum['files'])): ?>
                                    <span class="text-muted small">Tidak ada berkas.</span>
                                <?php else: ?>
                                    <?php foreach (array_slice($qSum['files'], 0, 5) as $file): ?>
                                        <div class="d-flex align-items-center justify-content-between py-1 border-bottom small">
                                            <span class="text-truncate mr-3 text-dark"><?= esc($file['file_name']) ?></span>
                                            <a href="<?= base_url($file['file_path']) ?>" class="btn btn-xs btn-outline-info" target="_blank">
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($qSum['files']) > 5): ?>
                                        <div class="text-center pt-2">
                                            <a href="javascript:void(0)" onclick="$('#tab-responses-link').tab('show')" class="small font-weight-bold text-primary">Lihat semua <?= count($qSum['files']) ?> berkas di data respon &rarr;</a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php elseif (in_array($q['question_type'], ['master_tpq', 'master_guru', 'master_santri'])): ?>
                        <div class="col-12">
                            <span class="text-muted small d-block mb-2"><i class="fas fa-database mr-1 text-info"></i> Frekuensi Pemilihan Data Master (Top 5):</span>
                            <div class="p-2 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                                <?php if (empty($qSum['counts'])): ?>
                                    <span class="text-muted small">Tidak ada data.</span>
                                <?php else: ?>
                                    <?php 
                                    arsort($qSum['counts']);
                                    $topCounts = array_slice($qSum['counts'], 0, 5, true);
                                    ?>
                                    <?php foreach ($topCounts as $name => $cnt): ?>
                                        <div class="d-flex align-items-center justify-content-between py-1 border-bottom small">
                                            <span class="text-dark font-weight-bold"><?= esc($name) ?></span>
                                            <span class="badge badge-primary"><?= $cnt ?> kali</span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Charts Scripts -->
    <script>
    (function() {
        // Register the chartjs-plugin-datalabels
        Chart.register(ChartDataLabels);

        // Render timeline daily response chart
        const dailyCtx = document.getElementById('daily-timeline-chart');
        if (dailyCtx) {
            const dailyData = <?= json_encode($daily_data) ?>;
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'Jumlah Respon Harian',
                        data: dailyData.map(d => d.count),
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false }
                    }
                }
            });
        }

        // Render sub charts
        const summaryData = <?= json_encode($summary) ?>;
        const questions = <?= json_encode($questions) ?>;

        questions.forEach(q => {
            const qId = q.id;
            const qSum = summaryData[qId];
            if (!qSum) return;

            const isNumeric = !!qSum.is_numeric_chart;
            if (!['multiple_choice', 'checkbox', 'dropdown', 'linear_scale', 'rating'].includes(q.question_type) && !isNumeric) return;
            
            if (!qSum.labels || qSum.labels.length === 0) return;

            const ctx = document.getElementById(`chart-sub-${qId}`);
            if (!ctx) return;

            const colors = [
                '#4285F4', '#34A853', '#FBBC05', '#EA4335',
                '#9b5de5', '#f15bb5', '#00bbf9', '#00f5d4'
            ];

            const isCheckbox = q.question_type === 'checkbox';
            const chartType = (isCheckbox || isNumeric) ? 'bar' : 'doughnut';

            const config = {
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
                        legend: { display: false },
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
                config.options.indexAxis = 'y';
                config.options.scales = {
                    x: { beginAtZero: true, ticks: { precision: 0 }, grace: '10%' }
                };
            } else if (isNumeric) {
                config.options.scales = {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grace: '10%' }
                };
            }

            new Chart(ctx, config);
        });
    })();
    </script>
<?php endif; ?>
