<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-warning">
            <h3 class="card-title">
                <i class="fas fa-edit"></i> Ubah Absensi Guru
            </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- View Mode: Schedule List -->
            <div id="viewScheduleList">
                <div class="row mb-3">
                    <div class="col-12">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-all" data-toggle="pill" href="#pills-all" role="tab" onclick="loadSchedule('')">Semua</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-rutin" data-toggle="pill" href="#pills-rutin" role="tab" onclick="loadSchedule('Rutin')">Rutin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-sekali" data-toggle="pill" href="#pills-sekali" role="tab" onclick="loadSchedule('Sekali')">Sekali</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="scheduleTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 15%">Lingkup</th>
                                        <th style="width: 10%">Jenis</th>
                                        <th style="width: 15%">Tanggal</th>
                                        <th style="width: 15%">Jam</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Mode: Bulk Editor (Hidden by default) -->
            <div id="viewBulkEditor" style="display: none;">
                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-secondary" onclick="backToList()">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </button>
                        <hr>
                        <h5 class="text-primary font-weight-bold" id="editorTitle">Edit Absensi</h5>
                        <input type="hidden" id="currentKegiatanId">
                        <input type="hidden" id="currentTanggal">
                    </div>
                </div>

                <!-- Search Container -->
                <div class="row mb-4" id="search-container">
                    <?php if ($isAdmin && !empty($tpqList)) : ?>
                    <!-- Admin Only: Filter TPQ -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filterTpq">
                                <i class="fas fa-building"></i> Filter TPQ:
                            </label>
                            <select id="filterTpq" class="form-control form-control-lg select2">
                                <option value="">-- Semua TPQ --</option>
                                <?php foreach ($tpqList as $tpq) : ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?> - <?= $tpq['Alamat'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                    <?php else: ?>
                    <div class="col-12">
                    <?php endif; ?>
    
                        <div class="form-group">
                            <label for="searchGuru">
                                <i class="fas fa-search"></i> Cari Guru:
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="text" id="searchGuru" class="form-control" placeholder="Ketik nama guru..." autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btnSearch">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    <?php if (!$isAdmin) : ?>
                                        <button class="btn btn-info" type="button" id="btnShowAll">
                                            <i class="fas fa-users"></i> Semua Guru
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading State -->
                <div id="loadingState" class="text-center p-5" style="display: none;">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>

                <!-- Guru List Container (Moved INSIDE editor) -->
                <div id="guruList" class="row"></div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<style>
    .guru-card {
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }
    .guru-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .absensi-radio-group {
        display: flex;
        width: 100%;
    }
    .absensi-label {
        flex: 1;
        text-align: center;
        padding: 5px;
        border: 1px solid #ddd;
        cursor: pointer;
        margin-right: -1px;
        font-size: 0.9rem;
        user-select: none;
    }
    .absensi-label:first-child { border-top-left-radius: 5px; border-bottom-left-radius: 5px; }
    .absensi-label:last-child { border-top-right-radius: 5px; border-bottom-right-radius: 5px; }
    
    .absensi-input { display: none; }
    
    .absensi-input:checked + .absensi-label.hadir { background-color: #28a745; color: white; border-color: #28a745; }
    .absensi-input:checked + .absensi-label.izin { background-color: #ffc107; color: black; border-color: #ffc107; }
    .absensi-input:checked + .absensi-label.sakit { background-color: #007bff; color: white; border-color: #007bff; }
    .absensi-input:checked + .absensi-label.alfa { background-color: #dc3545; color: white; border-color: #dc3545; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    let searchTimeout;
    let table;

    $(document).ready(function() {
        // Initialize Select2
        if($('.select2').length) {
            $('.select2').select2({ theme: 'bootstrap4' });
        }

        // Initialize DataTable
        table = $('#scheduleTable').DataTable({
            "responsive": true, 
            "autoWidth": false,
            "ajax": {
                "url": '<?= base_url("backend/guru/get-schedule-list") ?>',
                "type": "GET",
                "data": function(d) {
                    d.type = $('#pills-tab .nav-link.active').text() === 'Semua' ? '' : $('#pills-tab .nav-link.active').text();
                }
            },
            "columns": [
                { "data": "no", "orderable": false },
                { "data": "kegiatan" },
                { "data": "lingkup" },
                { "data": "jenis" },
                { "data": "tanggal", 
                  "render": function(data) {
                        if (!data) return '-';
                        const dateObj = new Date(data);
                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                        return dateObj.toLocaleDateString('id-ID', options);
                  }
                },
                { "data": "jam" },
                { "data": "aksi", "orderable": false }
            ]
        });

        // Event for 'Pilih' button in table
        $('#scheduleTable tbody').on('click', '.btn-pilih', function() {
            const idKegiatan = $(this).data('id');
            const tanggal = $(this).data('tanggal');
            const namaKegiatan = $(this).data('kegiatan');

            // Set hidden inputs
            $('#currentKegiatanId').val(idKegiatan);
            $('#currentTanggal').val(tanggal);
            
            // Set Title
            const dateObj = new Date(tanggal);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('id-ID', options);
            $('#editorTitle').html(`Edit Absensi: <span class="text-dark">${namaKegiatan}</span> <small class="text-muted">(${formattedDate})</small>`);

            // Switch View
            $('#viewScheduleList').hide();
            $('#viewBulkEditor').fadeIn();

            // Load Data
            loadBulkData();
        });

        // Handle Search Input in Editor
        $('#searchGuru').on('input', function() {
            const keyword = $(this).val().trim();
            clearTimeout(searchTimeout);
            
            const idTpq = $('#filterTpq').val();
            const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

            if (keyword.length >= 2 || (idTpq) || (!isAdmin)) {
                searchTimeout = setTimeout(loadBulkData, 500);
            }
        });

        $('#filterTpq').on('change', function() {
             loadBulkData();
        });

        $('#btnSearch').on('click', function() { loadBulkData(); });
        $('#btnShowAll').on('click', function() { 
            $('#searchGuru').val(''); 
            loadBulkData(); 
        });
    });

    function loadSchedule(type) {
        // DataTable will reload automatically due to func in 'data'
        // Just need to trigger ajax reload
        setTimeout(() => {
            table.ajax.reload();
        }, 100); 
    }

    function backToList() {
        $('#viewBulkEditor').hide();
        $('#viewScheduleList').fadeIn();
        // Clear editor state if needed
        $('#guruList').empty();
        $('#searchGuru').val('');
        $('#filterTpq').val('').trigger('change');
    }

    function loadBulkData() {
        // Read from hidden inputs instead of dropdowns
        const idKegiatan = $('#currentKegiatanId').val();
        const tanggal = $('#currentTanggal').val();
        const idTpq = $('#filterTpq').val();
        const keyword = $('#searchGuru').val();
        
        const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
        
        // Validation for Admin Global Search prevention
        if (isAdmin && !idTpq && (!keyword || keyword.length < 2)) {
            return; 
        }

        if (!idKegiatan || !tanggal) return;

        $.ajax({
            url: '<?= base_url("backend/guru/get-bulk-absensi-data") ?>',
            type: 'GET',
            data: {
                IdKegiatan: idKegiatan,
                Tanggal: tanggal,
                IdTpq: idTpq,
                keyword: keyword
            },
            dataType: 'json',
            beforeSend: function() {
                $('#guruList').hide();
                $('#loadingState').show();
            },
            success: function(response) {
                $('#loadingState').hide();
                $('#guruList').show();
                
                if (response.success) {
                    renderGuruCards(response.data);
                } else {
                    $('#guruList').html('<div class="col-12"><div class="alert alert-danger">' + (response.message || 'Gagal memuat data') + '</div></div>');
                }
            },
            error: function() {
                $('#loadingState').hide();
                $('#guruList').html('<div class="col-12"><div class="alert alert-danger">Terjadi kesalahan koneksi</div></div>');
            }
        });
    }

    function renderGuruCards(data) {
        const container = $('#guruList');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12"><div class="alert alert-info text-center">Tidak ada data guru yang ditemukan</div></div>');
            return;
        }

        data.forEach(guru => {
            const status = guru.StatusKehadiran || '';
            const keterangan = guru.Keterangan || '';
            const absensiId = guru.AbsensiId || '';
            const uniqueId = guru.IdGuru; 
            
            // Format time if available
            let waktuDisplay = '';
            if (guru.WaktuAbsen) {
                // Assuming format YYYY-MM-DD HH:mm:ss
                const timePart = guru.WaktuAbsen.split(' ')[1]; // Get HH:mm:ss
                if (timePart) {
                    const shortTime = timePart.substring(0, 5); // HH:mm
                    waktuDisplay = `<span class="badge badge-light border ml-1"><i class="fas fa-clock text-muted"></i> ${shortTime}</span>`;
                }
            }

            const card = `
                <div class="col-md-6 mb-4">
                    <div class="card guru-card h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 mr-3">
                                    <h5 class="card-title font-weight-bold mb-1 text-primary text-capitalize">
                                        ${guru.Nama.toLowerCase()}
                                    </h5>
                                    <p class="card-text text-muted small mb-0 text-capitalize">
                                        <i class="fas fa-building mr-1"></i> ${guru.NamaTpq.toLowerCase()}
                                    </p>
                                    ${guru.AlamatTpq ? `<p class="card-text text-muted small mb-1 text-capitalize"><i class="fas fa-map-marker-alt mr-1"></i> (${guru.AlamatTpq.toLowerCase()})</p>` : ''}
                                    ${waktuDisplay}
                                </div>
                                <div class="flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(guru.Nama)}&background=random&color=fff&size=64" 
                                         class="rounded-circle shadow-sm" 
                                         alt="${guru.Nama}"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </div>
                            </div>
                            
                            <hr class="my-2">
                            
                            <form id="form-${uniqueId}" onsubmit="return false;">
                                <input type="hidden" name="IdAbsensi" value="${absensiId}">
                                <input type="hidden" name="IdGuru" value="${guru.IdGuru}">
                                
                                <div class="form-group mb-2">
                                    <div class="absensi-radio-group">
                                        <input type="radio" name="status_${uniqueId}" id="h_${uniqueId}" value="Hadir" class="absensi-input" ${status === 'Hadir' ? 'checked' : ''} onchange="saveAbsensi('${uniqueId}')">
                                        <label for="h_${uniqueId}" class="absensi-label hadir">Hadir</label>

                                        <input type="radio" name="status_${uniqueId}" id="i_${uniqueId}" value="Izin" class="absensi-input" ${status === 'Izin' ? 'checked' : ''} onchange="saveAbsensi('${uniqueId}')">
                                        <label for="i_${uniqueId}" class="absensi-label izin">Izin</label>

                                        <input type="radio" name="status_${uniqueId}" id="s_${uniqueId}" value="Sakit" class="absensi-input" ${status === 'Sakit' ? 'checked' : ''} onchange="saveAbsensi('${uniqueId}')">
                                        <label for="s_${uniqueId}" class="absensi-label sakit">Sakit</label>

                                        <input type="radio" name="status_${uniqueId}" id="a_${uniqueId}" value="Alfa" class="absensi-input" ${status === 'Alfa' ? 'checked' : ''} onchange="saveAbsensi('${uniqueId}')">
                                        <label for="a_${uniqueId}" class="absensi-label alfa">Alfa</label>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <input type="text" class="form-control form-control-sm" 
                                        id="ket_${uniqueId}" 
                                        placeholder="Keterangan..." 
                                        value="${keterangan}"
                                        onblur="saveAbsensi('${uniqueId}')">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function saveAbsensi(uniqueId) {
        const idKegiatan = $('#currentKegiatanId').val(); // Read from hidden input
        const tanggal = $('#currentTanggal').val(); // Read from hidden input
        
        const idGuru = $(`#form-${uniqueId} input[name="IdGuru"]`).val();
        const status = $(`input[name="status_${uniqueId}"]:checked`).val();
        const keterangan = $(`#ket_${uniqueId}`).val();
        
        if (!status) return; 

        $.ajax({
            url: '<?= base_url("backend/guru/update-absensi") ?>',
            type: 'POST',
            data: {
                IdKegiatan: idKegiatan,
                Tanggal: tanggal,
                IdGuru: idGuru,
                Kehadiran: status,
                Keterangan: keterangan
            },
            success: function(response) {
                if(response.success) {
                   // Success
                }
            },
            error: function() {
                 Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal Menympan', timer: 2000, showConfirmButton: false });
            }
        });
    }
</script>
<?= $this->endSection(); ?>
