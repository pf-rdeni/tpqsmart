<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Survey Summary Info Card -->
        <div class="card card-primary card-outline shadow-sm mb-3">
            <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 font-weight-bold"><?= esc($survey['title']) ?></h5>
                    <span class="text-muted small">Target: <strong class="text-uppercase"><?= $survey['target_type'] ?></strong> | Status: <strong class="text-uppercase text-info"><?= $survey['status'] ?></strong></span>
                </div>
                <div class="text-right">
                    <h4 class="mb-0 font-weight-bold text-primary"><?= $total_response ?></h4>
                    <span class="text-muted small">Total Responden</span>
                </div>
            </div>
        </div>

        <!-- Tab Panel navigation -->
        <div class="card card-tabs card-primary card-outline shadow-sm">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="survey-result-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-summary-link" data-toggle="pill" href="#tab-summary" role="tab" 
                           data-url="<?= base_url("backend/survey/results/summary/{$survey['id']}") ?>">
                            <i class="fas fa-chart-pie mr-1"></i> Ringkasan (Charts)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-responses-link" data-toggle="pill" href="#tab-responses" role="tab" 
                           data-url="<?= base_url("backend/survey/results/responses/{$survey['id']}") ?>">
                            <i class="fas fa-list mr-1"></i> Tanggapan Individu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-dynamic-link" data-toggle="pill" href="#tab-dynamic" role="tab" 
                           data-url="<?= base_url("backend/survey/results/dynamic-table/{$survey['id']}") ?>">
                            <i class="fas fa-table mr-1"></i> Tabel Dinamis
                        </a>
                    </li>
                    <?php if (in_array($survey['target_type'], ['guru', 'santri', 'tpq'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-status-link" data-toggle="pill" href="#tab-status" role="tab" 
                               data-url="<?= base_url("backend/survey/results/filling-status/{$survey['id']}") ?>">
                                <i class="fas fa-check-double mr-1"></i> Status Pengisian
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-public-settings-link" data-toggle="pill" href="#tab-public-settings" role="tab" 
                           data-url="<?= base_url("backend/survey/results/public-settings/{$survey['id']}") ?>">
                            <i class="fas fa-globe mr-1"></i> Publikasi Hasil
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="survey-result-tabs-content">
                    <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">
                        <div class="text-center py-5 text-muted tab-loading-placeholder">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <div>Memuat ringkasan jawaban...</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-responses" role="tabpanel">
                        <div class="text-center py-5 text-muted tab-loading-placeholder">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <div>Memuat daftar tanggapan...</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-dynamic" role="tabpanel">
                        <div class="text-center py-5 text-muted tab-loading-placeholder">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <div>Memuat tabel dinamis...</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-status" role="tabpanel">
                        <div class="text-center py-5 text-muted tab-loading-placeholder">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <div>Memuat status pengisian responden...</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-public-settings" role="tabpanel">
                        <div class="text-center py-5 text-muted tab-loading-placeholder">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <div>Memuat pengaturan hasil publik...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Include ChartJS in backend results -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
$(document).ready(function() {
    // Helper to load tab content via AJAX
    function loadTabContent(tabLink) {
        const url = tabLink.attr('data-url');
        const targetPane = $(tabLink.attr('href'));
        
        // Only load if it hasn't been loaded yet or is explicitly refreshed
        if (targetPane.find('.tab-loading-placeholder').length > 0) {
            targetPane.load(url, function(response, status, xhr) {
                if (status === "error") {
                    targetPane.html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data: ${xhr.statusText}
                        </div>
                    `);
                }
            });
        }
    }

    // Load active tab initially
    loadTabContent($('#tab-summary-link'));

    // Handle tab change events
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        loadTabContent($(this));
    });
});
</script>
<?= $this->endSection() ?>
