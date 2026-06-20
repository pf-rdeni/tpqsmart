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

<!-- TPQ Progress Table Card -->
<div class="card card-primary card-outline shadow-sm mb-3" id="bk-tpq-progress-card" style="display: none;">
    <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-list-ol mr-1"></i> Progres Pengisian per TPQ / Lembaga</h6>
        <button type="button" class="btn btn-xs btn-success btn-share-wa-progress shadow-xs" title="Kirim Laporan Progres ke WhatsApp">
            <i class="fab fa-whatsapp mr-1"></i> Kirim WA
        </button>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table table-hover table-striped mb-0 text-left small align-middle">
                <thead>
                    <tr>
                        <th>Nama TPQ / Lembaga</th>
                        <th style="width: 45%;">Progres (Sudah vs Belum)</th>
                        <th style="width: 25%; text-align: right;">Detail</th>
                    </tr>
                </thead>
                <tbody id="bk-tpq-progress-body">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
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
let currentBackendTpqProgress = [];

$(document).ready(function() {
    loadBackendFillingStatus();

    $('#backend-tpq-filter').on('change', function() {
        loadBackendFillingStatus($(this).val());
    });

    // Share to WhatsApp click handler
    $(document).off('click', '.btn-share-wa-progress').on('click', '.btn-share-wa-progress', function() {
        if (!currentBackendTpqProgress || currentBackendTpqProgress.length === 0) {
            toastr.warning('Tidak ada data progres untuk dikirim.');
            return;
        }

        const surveyTitle = '<?= esc($survey['title']) ?>';
        const totalText = $('#bk-filling-ratio').text();
        
        let text = `*PROGRES PENGISIAN SURVEI*\n`;
        text += `Survei: *${surveyTitle}*\n`;
        text += `Tanggal: ${new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}\n\n`;
        text += `*Detail Progres per TPQ/Lembaga:*\n`;

        currentBackendTpqProgress.forEach((tpq, idx) => {
            const pct = tpq.total > 0 ? Math.round((tpq.filled / tpq.total) * 100) : 0;
            const unfilled = tpq.total - tpq.filled;
            text += `${idx + 1}. *${tpq.name}*\n`;
            text += `   - Sudah: ${tpq.filled}\n`;
            text += `   - Belum: ${unfilled}\n`;
            text += `   - Progres: ${pct}%\n\n`;
        });

        text += `*Total Keseluruhan:* ${totalText}`;

        const waUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`;
        window.open(waUrl, '_blank');
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

                // --- Calculate progress per TPQ ---
                const tpqMap = {};

                // Process filled targets
                sudah.forEach(item => {
                    const tId = item.tpq_id || 'other';
                    const tName = item.tpq_name || 'Lainnya';
                    if (!tpqMap[tId]) {
                        tpqMap[tId] = { id: tId, name: tName, filled: 0, total: 0 };
                    }
                    tpqMap[tId].filled++;
                    tpqMap[tId].total++;
                });

                // Process unfilled targets
                belum.forEach(item => {
                    const tId = item.tpq_id || 'other';
                    const tName = item.tpq_name || 'Lainnya';
                    if (!tpqMap[tId]) {
                        tpqMap[tId] = { id: tId, name: tName, filled: 0, total: 0 };
                    }
                    tpqMap[tId].total++;
                });

                // Convert to array and sort by name alphabetically
                const tpqList = Object.values(tpqMap);
                tpqList.sort((a, b) => a.name.localeCompare(b.name));
                
                currentBackendTpqProgress = tpqList;

                // Render TPQ progress table rows
                const progressBody = $('#bk-tpq-progress-body');
                progressBody.empty();

                if (tpqList.length === 0) {
                    $('#bk-tpq-progress-card').hide();
                } else {
                    $('#bk-tpq-progress-card').show();
                    tpqList.forEach(tpq => {
                        const pct = tpq.total > 0 ? Math.round((tpq.filled / tpq.total) * 100) : 0;
                        const unfilledCount = tpq.total - tpq.filled;

                        progressBody.append(`
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">${tpq.name}</td>
                                <td class="align-middle">
                                    <div class="progress shadow-xs" style="height: 16px; background-color: #f8d7da; border-radius: 8px; border: 1px solid #f5c6cb; overflow: hidden;" title="Sudah: ${tpq.filled}, Belum: ${unfilledCount}">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: ${pct}%; height: 100%; border-radius: 0;" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td class="text-right align-middle text-nowrap">
                                    <span class="badge badge-success px-2 py-1">${tpq.filled} <i class="fas fa-check mr-1"></i></span>
                                    <span class="badge badge-danger px-2 py-1">${unfilledCount} <i class="fas fa-times mr-1"></i></span>
                                    <span class="text-muted font-weight-bold ml-1" style="min-width: 40px; display: inline-block;">${pct}%</span>
                                </td>
                            </tr>
                        `);
                    });
                }

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
