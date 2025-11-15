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

                    <!-- Logger Threshold Settings -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-cog"></i> Pengaturan Logger</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="loggerThreshold">Log Threshold Level:</label>
                                                <select class="form-control" id="loggerThreshold">
                                                    <option value="0">0 - Disabled (No Logging)</option>
                                                    <option value="1">1 - Emergency Only</option>
                                                    <option value="2">2 - Alert</option>
                                                    <option value="3">3 - Critical</option>
                                                    <option value="4">4 - Runtime Errors</option>
                                                    <option value="5">5 - Warnings</option>
                                                    <option value="6">6 - Notices</option>
                                                    <option value="7">7 - Info</option>
                                                    <option value="8">8 - Debug</option>
                                                    <option value="9">9 - All Messages</option>
                                                </select>
                                                <small class="form-text text-muted">
                                                    Current: <strong id="currentThresholdDisplay"><?= is_array($currentThreshold) ? implode(', ', $currentThreshold) : $currentThreshold ?></strong>
                                                    <?php if ($thresholdOverride !== null): ?>
                                                        <span class="badge badge-warning">Overridden</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Default</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="button" class="btn btn-primary" id="btnUpdateThreshold">
                                                        <i class="fas fa-save"></i> Update Threshold
                                                    </button>
                                                    <?php if ($thresholdOverride !== null): ?>
                                                        <button type="button" class="btn btn-secondary" id="btnResetThreshold">
                                                            <i class="fas fa-undo"></i> Reset ke Default
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Threshold Info:</label>
                                                <div class="info-box bg-light">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Default (<?= ENVIRONMENT === 'production' ? 'Production' : 'Development' ?>):</span>
                                                        <span class="info-box-number"><?= ENVIRONMENT === 'production' ? '7 (Info)' : '9 (All)' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-2" id="thresholdMessage" style="display: none;">
                                        <i class="fas fa-info-circle"></i> <span id="thresholdMessageText"></span>
                                    </div>
                                </div>
                            </div>
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

        // Load current logger threshold
        function loadCurrentThreshold() {
            $.ajax({
                url: '<?= base_url('backend/logviewer/getLoggerThreshold') ?>',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#loggerThreshold').val(response.currentThreshold);
                        updateThresholdDisplay(response);
                    }
                }
            });
        }

        // Update threshold display
        function updateThresholdDisplay(response) {
            let thresholdText = isNaN(response.currentThreshold) ?
                response.currentThreshold.join(', ') :
                response.currentThreshold;
            $('#currentThresholdDisplay').html(thresholdText);

            if (response.isOverridden) {
                $('#currentThresholdDisplay').next().remove();
                $('#currentThresholdDisplay').after('<span class="badge badge-warning">Overridden</span>');
                if ($('#btnResetThreshold').length === 0) {
                    $('#btnUpdateThreshold').after('<button type="button" class="btn btn-secondary" id="btnResetThreshold"><i class="fas fa-undo"></i> Reset ke Default</button>');
                }
            } else {
                $('#currentThresholdDisplay').next().remove();
                $('#currentThresholdDisplay').after('<span class="badge badge-secondary">Default</span>');
                $('#btnResetThreshold').remove();
            }
        }

        // Update threshold
        $('#btnUpdateThreshold').click(function() {
            const threshold = $('#loggerThreshold').val();

            if (!threshold) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih threshold level terlebih dahulu'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('backend/logviewer/updateLoggerThreshold') ?>',
                method: 'POST',
                data: {
                    threshold: threshold,
                    action: 'set'
                },
                success: function(response) {
                    if (response.success) {
                        $('#thresholdMessageText').text(response.message);
                        $('#thresholdMessage').removeClass('alert-danger').addClass('alert-success').show();
                        updateThresholdDisplay(response);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        $('#thresholdMessageText').text(response.message || 'Gagal mengupdate threshold');
                        $('#thresholdMessage').removeClass('alert-success').addClass('alert-danger').show();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengupdate threshold'
                    });
                }
            });
        });

        // Reset threshold
        $(document).on('click', '#btnResetThreshold', function() {
            Swal.fire({
                title: 'Reset Threshold?',
                text: 'Threshold akan direset ke nilai default berdasarkan environment.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/logviewer/updateLoggerThreshold') ?>',
                        method: 'POST',
                        data: {
                            action: 'reset'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#thresholdMessageText').text(response.message);
                                $('#thresholdMessage').removeClass('alert-danger').addClass('alert-success').show();
                                updateThresholdDisplay(response);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        });

        // Initialize threshold value
        $('#loggerThreshold').val(<?= is_array($currentThreshold) ? json_encode($currentThreshold) : $currentThreshold ?>);

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