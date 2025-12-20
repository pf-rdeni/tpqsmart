<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users-cog"></i> Monitoring Penilaian Juri Pasangan
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-0 small">Type Ujian</label>
                                <select id="filterType" class="form-control form-control-sm">
                                    <?php foreach ($types as $key => $label): ?>
                                        <option value="<?= esc($key) ?>" <?= ($selected_type == $key) ? 'selected' : '' ?>><?= esc($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-0 small">Grup Materi</label>
                                <select id="filterGrupMateri" class="form-control form-control-sm">
                                    <option value="">Semua Grup Materi</option>
                                    <?php foreach ($grupList as $grup): ?>
                                        <option value="<?= esc($grup['IdGrupMateriUjian']) ?>" <?= ($selected_grup_materi == $grup['IdGrupMateriUjian']) ? 'selected' : '' ?>><?= esc($grup['NamaMateriGrup']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-0 small">TPQ</label>
                                <select id="filterTpq" class="form-control form-control-sm">
                                    <option value="0">Semua TPQ</option>
                                    <?php foreach ($tpqDropdown as $tpq): ?>
                                        <option value="<?= esc($tpq['IdTpq']) ?>" <?= ($selected_tpq == $tpq['IdTpq']) ? 'selected' : '' ?>><?= esc($tpq['NamaTpq']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="mb-0 small">Refresh Interval</label>
                                <select id="filterRefreshInterval" class="form-control form-control-sm">
                                    <option value="10">10 Detik</option>
                                    <option value="30" selected>30 Detik</option>
                                    <option value="60">1 Menit</option>
                                    <option value="120">2 Menit</option>
                                    <option value="300">5 Menit</option>
                                    <option value="600">10 Menit</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div>
                                    <span class="badge badge-info" id="countdownTimer" style="font-size: 0.9em;">--:--</span>
                                    <small class="text-muted ml-2">Refresh berikutnya</small>
                                </div>
                            </div>
                            <div class="col-md-7 text-right">
                                <button id="btnToggleAutoRefresh" class="btn btn-primary btn-sm mr-2" title="Toggle Auto Refresh">
                                    <i class="fas fa-pause" id="autoRefreshIcon"></i> <span id="autoRefreshText">Stop Auto Refresh</span>
                                </button>
                                <button id="btnLoadData" class="btn btn-info btn-sm mr-2">
                                    <i class="fas fa-sync-alt"></i> Muat Data
                                </button>
                                <button id="btnExportExcel" class="btn btn-success btn-sm" style="display: none;">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="statusTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                    Semua <span id="badgeAll" class="badge badge-secondary ml-1">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="complete-tab" data-toggle="tab" href="#complete" role="tab">
                                    Lengkap <span id="badgeComplete" class="badge badge-success ml-1">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="incomplete-tab" data-toggle="tab" href="#incomplete" role="tab">
                                    Belum Lengkap <span id="badgeIncomplete" class="badge badge-warning ml-1">0</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="statusTabContent">
                            <div class="tab-pane fade show active" id="all" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="tabelMonitoring" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Peserta</th>
                                                <th>Grup Materi</th>
                                                <th>Room ID</th>
                                                <th>Detail Juri Pasangan & Nilai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyMonitoring">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="complete" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="tabelMonitoringComplete" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Peserta</th>
                                                <th>Grup Materi</th>
                                                <th>Room ID</th>
                                                <th>Detail Juri Pasangan & Nilai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyMonitoringComplete">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Klik tab "Lengkap" untuk melihat data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="incomplete" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="tabelMonitoringIncomplete" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Peserta</th>
                                                <th>Grup Materi</th>
                                                <th>Room ID</th>
                                                <th>Detail Juri Pasangan & Nilai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyMonitoringIncomplete">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Klik tab "Belum Lengkap" untuk melihat data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    let allData = [];
    let completeData = [];
    let incompleteData = [];
    let autoRefreshInterval = null;
    let countdownInterval = null;
    let remainingSeconds = 0;
    let autoRefreshEnabled = true; // Default enabled
    
    // DataTables instances
    let dataTableAll = null;
    let dataTableComplete = null;
    let dataTableIncomplete = null;

    // Fungsi untuk format waktu countdown
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    // Fungsi untuk memulai countdown
    function startCountdown(intervalSeconds) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        remainingSeconds = intervalSeconds;
        $('#countdownTimer').text(formatTime(remainingSeconds));

        countdownInterval = setInterval(function() {
            remainingSeconds--;

            if (remainingSeconds <= 0) {
                remainingSeconds = 0;
                $('#countdownTimer').text('00:00');
                clearInterval(countdownInterval);
            } else {
                $('#countdownTimer').text(formatTime(remainingSeconds));
            }
        }, 1000);
    }

    // Fungsi untuk memulai auto refresh
    function startAutoRefresh(intervalSeconds) {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        const intervalMs = intervalSeconds * 1000;

        // Mulai countdown
        startCountdown(intervalSeconds);

        autoRefreshInterval = setInterval(function() {
            // Reset countdown setiap kali refresh dimulai
            startCountdown(intervalSeconds);
            // Load data
            loadData();
        }, intervalMs);
    }

    // Fungsi untuk menghentikan auto refresh
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        $('#countdownTimer').text('--:--');
        remainingSeconds = 0;
    }

    // Fungsi untuk mendapatkan refresh interval
    function getRefreshInterval() {
        return parseInt($('#filterRefreshInterval').val()) || 30;
    }

    // Fungsi untuk menyimpan filter ke localStorage
    function saveFiltersToLocalStorage() {
        const filters = {
            type: $('#filterType').val() || 'munaqosah',
            grupMateri: $('#filterGrupMateri').val() || '',
            tpq: $('#filterTpq').val() || '0',
            refreshInterval: $('#filterRefreshInterval').val() || '30',
            autoRefreshEnabled: autoRefreshEnabled
        };
        localStorage.setItem('monitoringPenilaianJuriPasanganFilters', JSON.stringify(filters));
    }

    // Fungsi untuk menyimpan tab aktif ke localStorage
    function saveActiveTabToLocalStorage(tabId) {
        localStorage.setItem('monitoringPenilaianJuriPasanganActiveTab', tabId);
    }

    // Fungsi untuk memuat tab aktif dari localStorage
    function loadActiveTabFromLocalStorage() {
        const savedTab = localStorage.getItem('monitoringPenilaianJuriPasanganActiveTab');
        if (savedTab) {
            // Aktifkan tab yang tersimpan
            const $tab = $('a[href="#' + savedTab + '"]');
            if ($tab.length) {
                $tab.tab('show');
            }
        }
    }

    // Fungsi untuk memuat filter dari localStorage
    function loadFiltersFromLocalStorage() {
        try {
            const savedFilters = localStorage.getItem('monitoringPenilaianJuriPasanganFilters');
            if (savedFilters) {
                const filters = JSON.parse(savedFilters);

                // Load filter Type
                if (filters.type) {
                    const $typeSel = $('#filterType');
                    if ($typeSel.length && $typeSel.find('option[value="' + filters.type + '"]').length > 0) {
                        $typeSel.val(filters.type);
                    }
                }

                // Load filter Grup Materi
                if (filters.grupMateri) {
                    const $grupSel = $('#filterGrupMateri');
                    if ($grupSel.length && $grupSel.find('option[value="' + filters.grupMateri + '"]').length > 0) {
                        $grupSel.val(filters.grupMateri);
                    }
                }

                // Load filter TPQ
                if (filters.tpq) {
                    const $tpqSel = $('#filterTpq');
                    if ($tpqSel.length && $tpqSel.find('option[value="' + filters.tpq + '"]').length > 0) {
                        $tpqSel.val(filters.tpq);
                    }
                }

                // Load refresh interval
                if (filters.refreshInterval) {
                    const $refreshSel = $('#filterRefreshInterval');
                    if ($refreshSel.length && $refreshSel.find('option[value="' + filters.refreshInterval + '"]').length > 0) {
                        $refreshSel.val(filters.refreshInterval);
                    }
                }

                // Load auto refresh enabled state
                if (filters.autoRefreshEnabled !== undefined) {
                    autoRefreshEnabled = filters.autoRefreshEnabled === true;
                }
            }
        } catch (e) {
            console.error('Error loading filters from localStorage:', e);
        }
    }

    // Fungsi untuk update UI auto refresh button
    function updateAutoRefreshUI() {
        const $btn = $('#btnToggleAutoRefresh');
        const $icon = $('#autoRefreshIcon');
        const $text = $('#autoRefreshText');

        if (autoRefreshEnabled) {
            $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            $icon.removeClass('fa-play').addClass('fa-pause');
            $text.text('Stop Auto Refresh');
        } else {
            $btn.removeClass('btn-primary').addClass('btn-outline-primary');
            $icon.removeClass('fa-pause').addClass('fa-play');
            $text.text('Start Auto Refresh');
        }
    }

    // Fungsi untuk menghapus nilai
    function deleteNilai(noPeserta, idGrupMateriUjian, namaSantri, namaMateriGrup) {
        Swal.fire({
            title: 'Konfirmasi Hapus Nilai',
            html: `Apakah Anda yakin ingin menghapus nilai untuk:<br><br>
                   <strong>${noPeserta} - ${namaSantri}</strong><br>
                   <small>Grup Materi: ${namaMateriGrup}</small><br><br>
                   <span class="text-danger">Tindakan ini tidak dapat dibatalkan!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang menghapus nilai, harap tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const th = $('#filterTahunAjaran').val().trim();
                const ty = $('#filterType').val() || 'munaqosah';

                // Kirim request hapus
                $.ajax({
                    url: '<?= base_url("backend/munaqosah/delete-nilai-penilaian-juri-pasangan") ?>',
                    type: 'POST',
                    data: {
                        NoPeserta: noPeserta,
                        IdGrupMateriUjian: idGrupMateriUjian,
                        IdTahunAjaran: th,
                        TypeUjian: ty
                    },
                    dataType: 'json',
                    success: function(resp) {
                        Swal.close();
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: resp.message || 'Nilai berhasil dihapus',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Reload data
                            loadData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: resp.message || 'Gagal menghapus nilai'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        let errorMsg = 'Terjadi kesalahan saat menghapus nilai';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMsg = errorResponse.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }

    function loadData() {
        const th = $('#filterTahunAjaran').val().trim();
        const ty = $('#filterType').val() || 'munaqosah';
        const grup = $('#filterGrupMateri').val() || '';
        const tpq = $('#filterTpq').val() || '0';

        const url = '<?= base_url("backend/munaqosah/monitoring-penilaian-juri-pasangan-data") ?>' + 
                    `?IdTahunAjaran=${encodeURIComponent(th)}&TypeUjian=${encodeURIComponent(ty)}` +
                    (grup ? `&IdGrupMateriUjian=${encodeURIComponent(grup)}` : '') +
                    (tpq !== '0' ? `&IdTpq=${encodeURIComponent(tpq)}` : '');

        // Destroy DataTables sebelum load data baru
        if ($.fn.DataTable) {
            if (dataTableAll && $.fn.DataTable.isDataTable('#tabelMonitoring')) {
                dataTableAll.destroy();
                dataTableAll = null;
            }
            if (dataTableComplete && $.fn.DataTable.isDataTable('#tabelMonitoringComplete')) {
                dataTableComplete.destroy();
                dataTableComplete = null;
            }
            if (dataTableIncomplete && $.fn.DataTable.isDataTable('#tabelMonitoringIncomplete')) {
                dataTableIncomplete.destroy();
                dataTableIncomplete = null;
            }
        }

        $('#tbodyMonitoring').html('<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        $('#tbodyMonitoringComplete').html('<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        $('#tbodyMonitoringIncomplete').html('<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

        $.getJSON(url, function(resp) {
            if (!resp.success) {
                console.error('Gagal memuat data:', resp);
                let errorMsg = resp.message || 'Gagal memuat data';
                if (resp.details) {
                    errorMsg += '<br><small>' + resp.details + '</small>';
                }
                $('#tbodyMonitoring').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
                $('#tbodyMonitoringComplete').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
                $('#tbodyMonitoringIncomplete').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
                return;
            }

            allData = resp.data || [];
            completeData = allData.filter(item => item.status === 'lengkap');
            incompleteData = allData.filter(item => item.status === 'belum_lengkap');

            // Debug info
            if (resp.debug) {
                console.log('Debug info:', resp.debug);
            }
            console.log('Total data loaded:', allData.length);

            // Update badge
            $('#badgeAll').text(allData.length);
            $('#badgeComplete').text(completeData.length);
            $('#badgeIncomplete').text(incompleteData.length);

            // Render tabel
            renderTable('all', allData);
            renderTable('complete', completeData);
            renderTable('incomplete', incompleteData);

            // Show export button if data exists
            if (allData.length > 0) {
                $('#btnExportExcel').show();
            } else {
                $('#btnExportExcel').hide();
            }

            // Reset countdown setelah data berhasil dimuat
            const interval = getRefreshInterval();
            startCountdown(interval);
        }).fail(function(xhr, status, error) {
            console.error('Error koneksi saat memuat data:', xhr, status, error);
            let errorMsg = 'Error koneksi saat memuat data';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.message) {
                    errorMsg = errorResponse.message;
                }
                if (errorResponse.details) {
                    errorMsg += '<br><small>' + errorResponse.details + '</small>';
                }
            } catch (e) {
                // Use default error message
            }
            $('#tbodyMonitoring').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
            $('#tbodyMonitoringComplete').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
            $('#tbodyMonitoringIncomplete').html('<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</td></tr>');
        });
    }

    function renderTable(type, data) {
        let tbodyId = '#tbodyMonitoring';
        if (type === 'complete') {
            tbodyId = '#tbodyMonitoringComplete';
        } else if (type === 'incomplete') {
            tbodyId = '#tbodyMonitoringIncomplete';
        }
        const tbody = $(tbodyId);
        
        if (data.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>');
            return;
        }

        let html = '';
        data.forEach(function(item, index) {
            let statusBadge = '';
            let rowClass = '';
            
            if (item.status === 'lengkap') {
                statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Lengkap</span>';
            } else if (item.status === 'tidak_ada_pasangan') {
                statusBadge = '<span class="badge badge-secondary"><i class="fas fa-info-circle"></i> Tidak Ada Pasangan Juri</span>';
                rowClass = 'table-secondary';
            } else {
                statusBadge = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Belum Lengkap</span>';
                rowClass = 'table-warning';
            }

            // Build detail juri pasangan dengan nilai yang lebih jelas
            let detailJuri = '<div class="d-flex flex-column" style="gap: 0.5rem;">';
            if (item.pasangan_juri && item.pasangan_juri.length > 0) {
                item.pasangan_juri.forEach(function(juri) {
                    const iconClass = juri.sudah_nilai ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                    const nilaiDisplay = juri.sudah_nilai && juri.Nilai !== null 
                        ? `<strong class="text-primary" style="font-size: 1.1em;">${juri.Nilai}</strong>` 
                        : '<span class="text-muted">-</span>';
                    const tooltip = juri.sudah_nilai && juri.updated_at
                        ? `title="Dinilai pada: ${new Date(juri.updated_at).toLocaleString('id-ID')}"` 
                        : 'title="Belum dinilai"';
                    
                    detailJuri += `<div class="d-flex align-items-center justify-content-between border rounded p-2 mb-1" style="min-width: 250px; background-color: ${juri.sudah_nilai ? '#f8f9fa' : '#fff3cd'};">
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <i class="fas ${iconClass}" style="font-size: 1.1em;"></i>
                            <span class="font-weight-bold">${juri.UsernameJuri}</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 0.5rem;" ${tooltip}>
                            <span class="text-muted small">Nilai:</span>
                            ${nilaiDisplay}
                        </div>
                    </div>`;
                });
            } else {
                detailJuri += '<div class="text-muted p-2"><i class="fas fa-info-circle"></i> Tidak ada pasangan juri aktif di RoomId ini</div>';
            }
            detailJuri += '</div>';

            // Gabungkan No Peserta dengan Nama Santri dan Status (status di bawah nama)
            const pesertaDisplay = `<div class="d-flex flex-column" style="gap: 0.25rem;">
                <div>
                    <strong>${item.NoPeserta}</strong> - ${item.NamaSantri}
                </div>
                <div>
                    ${statusBadge}
                </div>
            </div>`;

            // Tombol hapus nilai
            const btnHapus = `<button class="btn btn-danger btn-sm" onclick="deleteNilai('${item.NoPeserta}', '${item.IdGrupMateriUjian}', '${item.NamaSantri.replace(/'/g, "\\'")}', '${item.NamaMateriGrup.replace(/'/g, "\\'")}')" title="Hapus nilai untuk grup materi ini">
                <i class="fas fa-trash-alt"></i> Hapus Nilai
            </button>`;
            
            html += `<tr class="${rowClass}">
                <td>${index + 1}</td>
                <td>${pesertaDisplay}</td>
                <td>${item.NamaMateriGrup}</td>
                <td><span class="badge badge-info">${item.RoomId}</span></td>
                <td>${detailJuri}</td>
                <td>${btnHapus}</td>
            </tr>`;
        });

        tbody.html(html);
        
        // Inisialisasi atau re-inisialisasi DataTables
        initializeDataTable(type);
    }

    // Fungsi untuk inisialisasi DataTables
    function initializeDataTable(type) {
        let tableId = '#tabelMonitoring';
        
        if (type === 'complete') {
            tableId = '#tabelMonitoringComplete';
            // Destroy existing instance jika ada
            if (dataTableComplete && $.fn.DataTable.isDataTable('#tabelMonitoringComplete')) {
                dataTableComplete.destroy();
                dataTableComplete = null;
            }
        } else if (type === 'incomplete') {
            tableId = '#tabelMonitoringIncomplete';
            // Destroy existing instance jika ada
            if (dataTableIncomplete && $.fn.DataTable.isDataTable('#tabelMonitoringIncomplete')) {
                dataTableIncomplete.destroy();
                dataTableIncomplete = null;
            }
        } else {
            // Destroy existing instance jika ada
            if (dataTableAll && $.fn.DataTable.isDataTable('#tabelMonitoring')) {
                dataTableAll.destroy();
                dataTableAll = null;
            }
        }

        // Cek apakah DataTables library tersedia dan tabel ada
        if ($.fn.DataTable && $(tableId).length > 0) {
            // Cek apakah tabel sudah di-inisialisasi
            if ($.fn.DataTable.isDataTable(tableId)) {
                return; // Sudah di-inisialisasi, skip
            }

            const table = $(tableId).DataTable({
                responsive: true,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                order: [[0, 'asc']], // Sort by No column
                columnDefs: [
                    { orderable: false, targets: [5] } // Kolom Aksi tidak bisa di-sort
                ],
                language: {
                    decimal: ",",
                    emptyTable: "Tidak ada data yang tersedia",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    infoPostFix: "",
                    thousands: ".",
                    lengthMenu: "Tampilkan _MENU_ data",
                    loadingRecords: "Memuat...",
                    processing: "Memproses...",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Simpan instance
            if (type === 'complete') {
                dataTableComplete = table;
            } else if (type === 'incomplete') {
                dataTableIncomplete = table;
            } else {
                dataTableAll = table;
            }
        }
    }

    $(function() {
        // Load filter dari localStorage saat halaman dimuat
        loadFiltersFromLocalStorage();

        // Update UI auto refresh button
        updateAutoRefreshUI();

        // Load tab aktif dari localStorage
        loadActiveTabFromLocalStorage();

        // Load data pertama kali
        loadData();

        // Mulai auto refresh jika enabled
        if (autoRefreshEnabled) {
            const interval = getRefreshInterval();
            startAutoRefresh(interval);
        } else {
            stopAutoRefresh();
        }

        // Event handlers
        $('#btnLoadData').on('click', function() {
            // Reset countdown saat manual refresh
            const interval = getRefreshInterval();
            startCountdown(interval);
            loadData();
        });

        // Simpan filter ke localStorage saat berubah
        $('#filterType, #filterGrupMateri, #filterTpq').on('change', function() {
            saveFiltersToLocalStorage();
            loadData();
        });

        // Handle perubahan refresh interval
        $('#filterRefreshInterval').on('change', function() {
            const newInterval = parseInt($(this).val()) || 30;
            saveFiltersToLocalStorage();
            // Restart auto refresh dengan interval baru jika enabled
            if (autoRefreshEnabled) {
                startAutoRefresh(newInterval);
            } else {
                // Update countdown display meskipun tidak aktif
                $('#countdownTimer').text('--:--');
            }
        });

        // Toggle auto refresh button
        $('#btnToggleAutoRefresh').on('click', function() {
            autoRefreshEnabled = !autoRefreshEnabled;
            saveFiltersToLocalStorage();
            updateAutoRefreshUI();

            if (autoRefreshEnabled) {
                const interval = getRefreshInterval();
                startAutoRefresh(interval);
            } else {
                stopAutoRefresh();
            }
        });

        // Tab change handler - simpan tab aktif ke localStorage dan reinitialize DataTables jika perlu
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('href'); // e.g., "#all" or "#incomplete"
            const tabId = targetTab.substring(1); // Remove # to get "all", "complete", or "incomplete"
            saveActiveTabToLocalStorage(tabId);
            
            // Reinitialize DataTables untuk tab yang aktif
            // Tunggu sedikit agar tab content sudah ter-render
            setTimeout(function() {
                if (tabId === 'complete' && completeData.length > 0) {
                    initializeDataTable('complete');
                } else if (tabId === 'incomplete' && incompleteData.length > 0) {
                    initializeDataTable('incomplete');
                } else if (tabId === 'all' && allData.length > 0) {
                    initializeDataTable('all');
                }
            }, 100);
        });

        // Export Excel (placeholder)
        $('#btnExportExcel').on('click', function() {
            alert('Fitur export Excel akan segera tersedia');
        });

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            stopAutoRefresh();
        });
    });
</script>
<?= $this->endSection(); ?>

