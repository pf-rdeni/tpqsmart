<!-- Target stats and ratio bar -->
<div class="row align-items-center mb-3">
    <div class="col-md-6 mb-2 mb-md-0">
        <div class="form-group mb-0">
            <label for="backend-tpq-filter" class="font-weight-bold small">Filter Lembaga TPQ Target:</label>
            <select class="form-control form-control-sm w-75" id="backend-tpq-filter">
                <option value="">-- Tampilkan Semua TPQ --</option>
                <?php foreach ($tpqs as $tpq): ?>
                    <option value="<?= $tpq['IdTpq'] ?>"><?= esc($tpq['NamaTpq']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6 text-md-right">
        <h5 class="mb-1 font-weight-bold text-primary" id="bk-filling-ratio">0 / 0</h5>
        <div class="progress progress-sm w-50 ml-auto d-none d-md-flex" style="height: 6px;">
            <div class="progress-bar bg-success" id="bk-ratio-progress-bar" style="width: 0%;"></div>
        </div>
    </div>
</div>

<!-- Target double columns list -->
<div class="row">
    <!-- Column 1: Already Filled -->
    <div class="col-md-6 mb-3">
        <div class="card card-success card-outline shadow-xs">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 font-weight-bold text-success"><i class="fas fa-check mr-1"></i> Sudah Mengisi</h6>
            </div>
            <div class="card-body p-2" style="max-height: 450px; overflow-y: auto;" id="bk-list-sudah">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>

    <!-- Column 2: Not Yet Filled -->
    <div class="col-md-6 mb-3">
        <div class="card card-danger card-outline shadow-xs">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Belum Mengisi</h6>
            </div>
            <div class="card-body p-2" style="max-height: 450px; overflow-y: auto;" id="bk-list-belum">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadBackendFillingStatus();

    $('#backend-tpq-filter').on('change', function() {
        loadBackendFillingStatus($(this).val());
    });
});

function loadBackendFillingStatus(tpqId = '') {
    const listSudah = $('#bk-list-sudah');
    const listBelum = $('#bk-list-belum');
    
    listSudah.html('<div class="text-center py-4 text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat...</div>');
    listBelum.html('<div class="text-center py-4 text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat...</div>');

    $.ajax({
        url: '<?= base_url("backend/survey/results/filling-status-data/{$survey['id']}") ?>',
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
                
                $('#bk-filling-ratio').text(`${sudah.length} dari ${total} Responden (${percent}%)`);
                $('#bk-ratio-progress-bar').css('width', percent + '%');

                // Render sudah
                if (sudah.length === 0) {
                    listSudah.html('<div class="text-center py-3 text-muted small">Belum ada target yang mengisi.</div>');
                } else {
                    sudah.forEach(item => {
                        listSudah.append(`
                            <div class="p-2 border-bottom d-flex align-items-center justify-content-between hover-bg-light">
                                <div>
                                    <div class="font-weight-bold text-dark small">${item.name}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">${item.tpq_name || '-'}</div>
                                </div>
                                <span class="badge badge-success px-2"><i class="fas fa-check mr-1"></i>Sudah</span>
                            </div>
                        `);
                    });
                }

                // Render belum
                if (belum.length === 0) {
                    listBelum.html('<div class="text-center py-3 text-success font-weight-bold small"><i class="fas fa-star mr-1"></i> Semua target telah mengirimkan jawaban.</div>');
                } else {
                    belum.forEach(item => {
                        listBelum.append(`
                            <div class="p-2 border-bottom d-flex align-items-center justify-content-between hover-bg-light">
                                <div>
                                    <div class="font-weight-bold text-dark small">${item.name}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">${item.tpq_name || '-'}</div>
                                </div>
                                <span class="badge badge-danger px-2"><i class="fas fa-exclamation mr-1"></i>Belum</span>
                            </div>
                        `);
                    });
                }
            }
        }
    });
}
</script>
<style>
.hover-bg-light:hover {
    background-color: #f8f9fa;
}
</style>
