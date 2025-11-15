<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> Log Viewer
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/logviewer/download?date=' . $date) ?>" class="btn btn-sm btn-success" title="Download Log">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="logDate">Pilih Tanggal:</label>
                            <input type="date" class="form-control" id="logDate" value="<?= esc($date) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="logFilter">Filter Level:</label>
                            <select class="form-control select2" id="logFilter" multiple="multiple" data-placeholder="Pilih level log..." style="width: 100%;">
                                <option value="ERROR">ERROR</option>
                                <option value="WARNING">WARNING</option>
                                <option value="INFO">INFO</option>
                                <option value="DEBUG">DEBUG</option>
                                <option value="CRITICAL">CRITICAL</option>
                                <option value="ALERT">ALERT</option>
                                <option value="EMERGENCY">EMERGENCY</option>
                                <option value="NOTICE">NOTICE</option>
                            </select>
                            <small class="form-text text-muted">Kosongkan untuk menampilkan semua level</small>
                        </div>
                        <div class="col-md-2">
                            <label for="logSearch">Cari Kata Kunci:</label>
                            <input type="text" class="form-control" id="logSearch" placeholder="Cari di log...">
                        </div>
                        <div class="col-md-2">
                            <label for="logLines">Jumlah Baris:</label>
                            <select class="form-control" id="logLines">
                                <option value="500">500</option>
                                <option value="1000" selected>1000</option>
                                <option value="2000">2000</option>
                                <option value="5000">5000</option>
                                <option value="10000">10000</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="btnRefresh">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Statistik Log</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total</span>
                                                    <span class="info-box-number" id="statTotal"><?= number_format($logStats['total'] ?? 0) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-info-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">INFO</span>
                                                    <span class="info-box-number" id="statInfo"><?= number_format($logStats['info'] ?? 0) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">ERROR</span>
                                                    <span class="info-box-number" id="statError"><?= number_format($logStats['error'] ?? 0) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">WARNING</span>
                                                    <span class="info-box-number" id="statWarning"><?= number_format($logStats['warning'] ?? 0) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-secondary"><i class="fas fa-bug"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">DEBUG</span>
                                                    <span class="info-box-number" id="statDebug"><?= number_format($logStats['debug'] ?? 0) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-purple"><i class="fas fa-file-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Ukuran File</span>
                                                    <span class="info-box-number" id="statSize"><?= isset($logStats['fileSize']) ? number_format($logStats['fileSize'] / 1024, 2) . ' KB' : '0 KB' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Files List -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-outline card-secondary collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-folder"></i> Daftar File Log</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>File</th>
                                                    <th>Ukuran</th>
                                                    <th>Diubah</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($logFiles as $file): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($file['date'])) ?></td>
                                                        <td><?= esc($file['filename']) ?></td>
                                                        <td><?= number_format($file['size'] / 1024, 2) . ' KB' ?></td>
                                                        <td><?= date('d/m/Y H:i:s', $file['modified']) ?></td>
                                                        <td>
                                                            <a href="<?= base_url('backend/logviewer?date=' . $file['date']) ?>" class="btn btn-xs btn-primary">
                                                                <i class="fas fa-eye"></i> Lihat
                                                            </a>
                                                            <a href="<?= base_url('backend/logviewer/download?date=' . $file['date']) ?>" class="btn btn-xs btn-success">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Content -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-code"></i> Konten Log - <?= date('d/m/Y', strtotime($date)) ?>
                                        <small class="ml-2" id="logInfo">Memuat...</small>
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnAutoRefresh" title="Auto Refresh">
                                            <i class="fas fa-sync-alt"></i> Auto Refresh: <span id="autoRefreshStatus">OFF</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="logContent" class="log-viewer">
                                        <?= $logContent['content'] ?? 'Tidak ada log untuk tanggal yang dipilih.' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.log-viewer {
    background-color: #1e1e1e;
    color: #d4d4d4;
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    padding: 15px;
    border-radius: 4px;
    max-height: 600px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.log-line {
    margin: 2px 0;
    padding: 2px 5px;
    border-left: 3px solid transparent;
}

.log-line.log-error {
    background-color: rgba(220, 53, 69, 0.1);
    border-left-color: #dc3545;
    color: #ff6b6b;
}

.log-line.log-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border-left-color: #ffc107;
    color: #ffd93d;
}

.log-line.log-info {
    background-color: rgba(0, 123, 255, 0.1);
    border-left-color: #007bff;
    color: #74c0fc;
}

.log-line.log-debug {
    background-color: rgba(108, 117, 125, 0.1);
    border-left-color: #6c757d;
    color: #adb5bd;
}

.info-box-icon.bg-purple {
    background-color: #6f42c1 !important;
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    let autoRefreshInterval = null;
    let autoRefreshEnabled = false;

    // Format bytes helper
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Initialize Select2 for log filter
    $('#logFilter').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih level log...',
        allowClear: true,
        width: '100%',
        closeOnSelect: false
    });

    // Get selected filters from Select2
    function getSelectedFilters() {
        const selectedFilters = $('#logFilter').val();
        // Jika tidak ada yang dipilih, return null untuk menampilkan semua
        if (!selectedFilters || selectedFilters.length === 0) {
            return null;
        }
        return selectedFilters;
    }

    // Load log content
    function loadLogContent() {
        const date = $('#logDate').val();
        const filters = getSelectedFilters();
        const search = $('#logSearch').val();
        const lines = $('#logLines').val();

        $('#logContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat log...</div>');

        // Prepare data
        const ajaxData = {
            date: date,
            search: search,
            lines: lines
        };
        
        // Add filters as array - CodeIgniter will handle this automatically
        if (filters && filters.length > 0) {
            ajaxData.filters = filters;
        }

        $.ajax({
            url: '<?= base_url('backend/logviewer/getLogContentByDate') ?>',
            method: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: ajaxData,
            success: function(response) {
                if (response.success) {
                    $('#logContent').html(response.content);
                    $('#logInfo').html('Menampilkan ' + (response.shownLines || 0) + ' dari ' + (response.totalLines || 0) + ' baris');
                    
                    // Update statistics
                    if (response.stats) {
                        $('#statTotal').text((response.stats.total || 0).toLocaleString());
                        $('#statInfo').text((response.stats.info || 0).toLocaleString());
                        $('#statError').text((response.stats.error || 0).toLocaleString());
                        $('#statWarning').text((response.stats.warning || 0).toLocaleString());
                        $('#statDebug').text((response.stats.debug || 0).toLocaleString());
                        if (response.stats.fileSize) {
                            $('#statSize').text(formatBytes(response.stats.fileSize));
                        }
                    }

                    // Scroll to bottom
                    const logViewer = document.getElementById('logContent');
                    logViewer.scrollTop = logViewer.scrollHeight;
                } else {
                    $('#logContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#logContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat log.</div>');
            }
        });
    }

    // Refresh button
    $('#btnRefresh').click(function() {
        loadLogContent();
    });

    // Date change
    $('#logDate').change(function() {
        window.location.href = '<?= base_url('backend/logviewer') ?>?date=' + $(this).val();
    });

    // Filter change
    $('#logFilter, #logSearch, #logLines').on('change', function() {
        loadLogContent();
    });

    // Search on Enter
    $('#logSearch').keypress(function(e) {
        if (e.which === 13) {
            loadLogContent();
        }
    });

    // Auto refresh toggle
    $('#btnAutoRefresh').click(function() {
        if (autoRefreshEnabled) {
            // Stop auto refresh
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            autoRefreshEnabled = false;
            $('#autoRefreshStatus').text('OFF');
            $(this).removeClass('btn-warning').addClass('btn-primary');
        } else {
            // Start auto refresh
            autoRefreshEnabled = true;
            $('#autoRefreshStatus').text('ON');
            $(this).removeClass('btn-primary').addClass('btn-warning');
            
            autoRefreshInterval = setInterval(function() {
                loadLogContent();
            }, 5000); // Refresh every 5 seconds
        }
    });

    // Initial load if needed
    // loadLogContent();
});
</script>
<?= $this->endSection(); ?>

