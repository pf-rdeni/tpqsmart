<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Step 1: Pilih Lomba & Cabang -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-search"></i> Langkah 1: Pilih Lomba & Cabang</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lomba (Aktif)</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Lomba --</option>
                                        <?php foreach ($lomba_list as $i => $l): ?>
                                            <?php 
                                                $tpqLabel = !empty($l['NamaTpq']) 
                                                    ? ' - TPQ ' . $l['NamaTpq'] . ' - ' . ($l['KelurahanDesa'] ?? '')
                                                    : ' - Umum';
                                            ?>
                                            <option value="<?= $l['id'] ?>" <?= ($lomba && $lomba['id'] == $l['id']) ? 'selected' : '' ?>>
                                                <?= esc($l['NamaLomba']) ?><?= $tpqLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cabang</label>
                                    <select class="form-control" id="selectCabang">
                                        <option value="">-- Pilih Cabang --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($cabang): ?>
            <!-- Step 2: Form Pendaftaran -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-success" id="cardDaftarSantri">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-plus"></i> Langkah 2: Daftarkan Santri</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" id="btnCollapseCard">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="cardDaftarSantriBody">
                            <!-- Info Cabang Full Row -->
                            <div class="alert alert-info mb-4">
                                <h5><i class="icon fas fa-info"></i> Info Cabang</h5>
                                <div class="row">
                                    <div class="col-md-3">Kategori: <strong><?= $cabang['Kategori'] ?></strong></div>
                                    <div class="col-md-3">Tipe: <strong><?= $cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu') ?></strong></div>
                                    <div class="col-md-3">Batasan: <strong>
                                        <?php if ((!empty($cabang['KelasMin']) && $cabang['KelasMin'] != 0) || (!empty($cabang['KelasMax']) && $cabang['KelasMax'] != 0)): ?>
                                            <?= esc($cabang['NamaKelasMin'] ?? '?') ?> - <?= esc($cabang['NamaKelasMax'] ?? '?') ?> (Kelas)
                                        <?php else: ?>
                                            <?= $cabang['UsiaMin'] ?> - <?= $cabang['UsiaMax'] ?> Thn (Usia)
                                        <?php endif; ?>
                                    </strong></div>
                                    <div class="col-md-3">Max Peserta: <strong><?= $cabang['MaxPeserta'] == 0 ? 'Unlimited' : $cabang['MaxPeserta'] ?></strong></div>
                                </div>
                                <?php if (!empty($cabang['MaxPerTpq']) && $cabang['MaxPerTpq'] > 0): ?>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-info-circle"></i> 
                                            Kuota per TPQ: <strong><?= $cabang['MaxPerTpq'] ?></strong> 
                                            <?= strtolower($cabang['Tipe'] ?? ($cabang['TipePeserta'] ?? 'Individu')) === 'kelompok' ? 'kelompok' : 'peserta' ?>
                                            | Terpakai: <strong id="quotaUsed"><?= $quota_used ?? 0 ?></strong>
                                            | Sisa: <strong id="quotaRemaining"><?= max(0, ($cabang['MaxPerTpq'] ?? 0) - ($quota_used ?? 0)) ?></strong>
                                        </span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>



                            <!-- Step 2: Pilih TPQ (Admin Only) -->
                            <?php if (!empty($is_admin) && $is_admin): ?>
                            <div class="form-group row pl-2">
                                <label class="col-sm-2 col-form-label">Pilih TPQ</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2" id="filterTpq" style="width: 100%;">
                                        <option value="">-- Pilih TPQ --</option>
                                        <?php foreach ($tpq_list as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>">
                                                <?= esc($tpq['NamaTpq']) . (!empty($tpq['KelurahanDesa']) ? ' - ' . esc($tpq['KelurahanDesa']) : '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-12">
                                    
                                    <!-- Table Santri Available -->
                                    <div id="modeTable">
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <input type="text" id="filterTableSantri" class="form-control form-control-sm" placeholder="Filter Nama/Kelas...">
                                            </div>
                                            <div class="col-md-8 text-right">
                                                <small class="text-muted">
                                                    Info: <span id="selectedCount">0</span> santri dipilih
                                                </small>
                                            </div>
                                        </div>
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-bordered table-hover table-sm text-sm" id="tableSantriAvailable">
                                                <thead class="bg-light sticky-top">
                                                    <tr>
                                                        <th class="text-center" width="40">
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input" type="checkbox" id="checkAllSantri">
                                                                <label class="custom-control-label" for="checkAllSantri"></label>
                                                            </div>
                                                        </th>
                                                        <th>Nama Santri</th>
                                                        <th class="text-center">Jenis Kelamin</th>
                                                        <th>TPQ</th>
                                                        <th>Info</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Generated via JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-success btn-block mt-4" id="btnDaftarkan" disabled>
                                        <i class="fas fa-save"></i> Simpan Pendaftaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Peserta -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i> Daftar Peserta Terdaftar
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-info"><?= count($peserta_list) ?> Peserta</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablePeserta">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Tipe</th>
                                            <th>No Peserta</th>
                                            <th>Nama Santri</th>
                                            <th>Jenis Kelamin</th>
                                            <th>TPQ</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($peserta_list)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                                    Belum ada peserta terdaftar untuk cabang ini
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php 
                                            // Grouping Logic
                                            $finalList = [];
                                            $orderedList = []; 
                                            foreach ($peserta_list as $p) {
                                                if (($p['TipePendaftaran'] ?? 'individu') === 'kelompok') {
                                                    $grupUrut = $p['GrupUrut'] ?? '0';
                                                    $key = $p['IdTpq'] . '_' . $grupUrut;
                                                    
                                                    if (!isset($finalList[$key])) {
                                                         $finalList[$key] = [
                                                             'is_group' => true,
                                                             'NamaGrup' => $p['NamaGrup'] ?? ('Grup '.$grupUrut),
                                                             'NamaTpq' => $p['NamaTpq'] ?? '',
                                                             'StatusPendaftaran' => $p['StatusPendaftaran'],
                                                             'cabang_id' => $p['cabang_id'],
                                                             'IdTpq' => $p['IdTpq'],
                                                             'GrupUrut' => $grupUrut,
                                                             'members' => []
                                                         ];
                                                         $orderedList[] = &$finalList[$key];
                                                    }
                                                    $finalList[$key]['members'][] = $p;
                                                } else {
                                                    // Individu
                                                    $orderedList[] = [
                                                        'is_group' => false,
                                                        'data' => $p
                                                    ];
                                                }
                                            }
                                            ?>

                                            <?php foreach ($orderedList as $i => $item): ?>
                                                <?php if ($item['is_group']): ?>
                                                    <!-- Group Row -->
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td><?= $i + 1 ?></td>
                                                        <td><span class="badge badge-primary">Kelompok</span></td>
                                                        <td><i class="expandable-table-caret fas fa-caret-right fa-fw"></i></td>
                                                        <td><strong><?= esc($item['NamaGrup']) ?></strong> <span class="text-muted">(<?= count($item['members']) ?> Anggota)</span></td>
                                                        <td>-</td>
                                                        <td><small><?= esc($item['NamaTpq']) ?></small></td>
                                                        <td>
                                                            <span class="badge badge-<?= $item['StatusPendaftaran'] === 'valid' ? 'success' : ($item['StatusPendaftaran'] === 'pending' ? 'warning' : 'danger') ?>">
                                                                <?= ucfirst($item['StatusPendaftaran']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-warning btn-xs btn-cancel-group" 
                                                                    data-cabang="<?= $item['cabang_id'] ?>" 
                                                                    data-tpq="<?= $item['IdTpq'] ?>" 
                                                                    data-grupurut="<?= $item['GrupUrut'] ?>"
                                                                    data-namagrup="<?= esc($item['NamaGrup']) ?>"
                                                                    title="Hapus Grup">
                                                                <i class="fas fa-users-slash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="expandable-body">
                                                        <td colspan="8">
                                                            <div class="p-2" style="background-color: #f4f6f9;">
                                                                <table class="table table-sm table-bordered mb-0 bg-white">
                                                                    <thead>
                                                                        <tr class="text-muted text-center" style="background-color: #e9ecef;">
                                                                            <th width="120">No Peserta</th>
                                                                            <th>Nama Anggota</th>
                                                                            <th width="100">L/P</th>
                                                                            <th width="100">Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($item['members'] as $member): ?>
                                                                            <tr>
                                                                                <td class="text-center"><code><?= esc($member['NoPeserta']) ?></code></td>
                                                                                <td><?= esc($member['NamaSantri']) ?></td>
                                                                                <td class="text-center text-capitalize"><?= $member['JenisKelamin'] ?></td>
                                                                                <td class="text-center">
                                                                                    <div class="btn-group btn-group-xs">
                                                                                        <button type="button" class="btn btn-info btn-edit-peserta" 
                                                                                                data-id="<?= $member['id'] ?>"
                                                                                                data-nama="<?= esc($member['NamaSantri']) ?>"
                                                                                                data-namagrup="<?= esc($item['NamaGrup']) ?>"
                                                                                                title="Ganti Anggota">
                                                                                            <i class="fas fa-exchange-alt"></i>
                                                                                        </button>
                                                                                        <button type="button" class="btn btn-danger btn-cancel-peserta" 
                                                                                                data-id="<?= $member['id'] ?>" 
                                                                                                title="Hapus Anggota">
                                                                                            <i class="fas fa-user-minus"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <!-- Individu Row -->
                                                    <?php $peserta = $item['data']; ?>
                                                    <tr>
                                                        <td><?= $i + 1 ?></td>
                                                        <td><span class="badge badge-secondary">Individu</span></td>
                                                        <td><code><?= esc($peserta['NoPeserta']) ?></code></td>
                                                        <td><strong><?= esc($peserta['NamaSantri']) ?></strong></td>
                                                        <td><?= $peserta['JenisKelamin'] ?></td>
                                                        <td><small><?= esc($peserta['NamaTpq'] ?? '-') ?></small></td>
                                                        <td>
                                                            <span class="badge badge-<?= $peserta['StatusPendaftaran'] === 'valid' ? 'success' : ($peserta['StatusPendaftaran'] === 'pending' ? 'warning' : 'danger') ?>">
                                                                <?= ucfirst($peserta['StatusPendaftaran']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs">
                                                                <button type="button" class="btn btn-info btn-edit-peserta" 
                                                                        data-id="<?= $peserta['id'] ?>"
                                                                        data-nama="<?= esc($peserta['NamaSantri']) ?>"
                                                                        data-namagrup=""
                                                                        title="Ganti Peserta">
                                                                    <i class="fas fa-exchange-alt"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-cancel-peserta" data-id="<?= $peserta['id'] ?>" title="Batalkan">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                           <!-- Footer content if needed -->
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Edit/Ganti Peserta -->
<div class="modal fade" id="modalEditPeserta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> Ganti Peserta</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editPesertaId">
                <div class="alert alert-info">
                    <strong>Peserta Saat Ini:</strong> <span id="currentPesertaNama"></span>
                    <span id="currentPesertaGrup"></span>
                </div>
                <div class="form-group">
                    <label>Pilih Santri Pengganti</label>
                    <input type="text" id="filterEditSantri" class="form-control form-control-sm mb-2" placeholder="Filter Nama...">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm" id="tableEditSantri">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th width="50">Pilih</th>
                                    <th>Nama Santri</th>
                                    <th>L/P</th>
                                    <th>TPQ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Generated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanGantiPeserta" disabled>
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi JS
    var maxPeserta = <?= ($cabang['MaxPeserta'] ?? 0) ?>;
    
    // LocalStorage Keys
    const KEY_LOMBA = 'last_pendaftaran_lomba_id';
    const KEY_CABANG = 'last_pendaftaran_cabang_id';

    var storedLombaId = localStorage.getItem(KEY_LOMBA);
    var storedCabangId = localStorage.getItem(KEY_CABANG);

    // Restore from localStorage if no ID is in URL
    <?php if (!$cabang): ?>
    if (storedCabangId) {
        window.location.href = '<?= base_url('backend/perlombaan/pendaftaran') ?>/' + storedCabangId;
        return;
    } else if (storedLombaId) {
        $('#selectLomba').val(storedLombaId);
        loadCabangByLomba(storedLombaId);
    } else if ($('#selectLomba option').length > 1) {
        // Fallback to first available lomba if no storage
        $('#selectLomba option:eq(1)').prop('selected', true).trigger('change');
    }
    <?php else: ?>
    // Update storage with current selection
    localStorage.setItem(KEY_LOMBA, '<?= $cabang['lomba_id'] ?>');
    localStorage.setItem(KEY_CABANG, '<?= $cabang['id'] ?>');
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    loadSantriList();
    <?php endif; ?>
    
    // Init Select2
    $('.select2').select2({ theme: 'bootstrap4' });
    
    // Admin Filter TPQ Change
    $('#filterTpq').change(function() {
        loadSantriList();
    });

    // Collapse State Persistence for Step 2 Card
    var collapseKey = 'pendaftaran_card_collapsed';
    var card = $('#cardDaftarSantri');
    
    // Restore state on load
    if (card.length && localStorage.getItem(collapseKey) === 'true') {
        card.addClass('collapsed-card');
        card.find('.card-body').hide();
        card.find('.card-footer').hide();
        card.find('#btnCollapseCard i').removeClass('fa-minus').addClass('fa-plus');
    }

    // Toggle Collapse
    card.on('collapsed.lte.cardwidget', function() {
        localStorage.setItem(collapseKey, 'true');
    });
    card.on('expanded.lte.cardwidget', function() {
        localStorage.setItem(collapseKey, 'false');
    });

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            localStorage.setItem(KEY_LOMBA, lombaId);
            loadCabangByLomba(lombaId);
        } else {
            $('#selectCabang').html('<option value="">-- Pilih Cabang --</option>');
            localStorage.removeItem(KEY_LOMBA);
            localStorage.removeItem(KEY_CABANG);
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            localStorage.setItem(KEY_CABANG, cabangId);
            window.location.href = '<?= base_url('backend/perlombaan/pendaftaran') ?>/' + cabangId;
        }
    });

    function loadCabangByLomba(lombaId, selectedId = null) {
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getCabangByLomba') ?>/' + lombaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<option value="">-- Pilih Cabang --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + (item.DisplayLabel || item.NamaCabang) + '</option>';
                    });
                    $('#selectCabang').html(html);
                }
            }
        });
    }

    function loadSantriList() {
        var filterTpq = $('#filterTpq').val();
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getSantriForRegistration') ?>',
            type: 'POST',
            data: { 
                cabang_id: <?= $cabang_id ?? 0 ?>,
                filterTpq: filterTpq
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update Quota Info
                    if (response.quota_info) {
                        $('#quotaUsed').text(response.quota_info.used);
                        $('#quotaRemaining').text(response.quota_info.remaining);
                    }
                    
                    // Reset Table
                    var tbody = $('#tableSantriAvailable tbody');
                    tbody.empty();
                    $('#selectedCount').text('0');
                    $('#btnDaftarkan').prop('disabled', true);
                    $('#checkAllSantri').prop('checked', false);

                    if(response.data.length === 0) {
                         tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada santri yang memenuhi kriteria</td></tr>');
                    }

                    response.data.forEach(function(item) {
                        // Populate Table Row
                        var id = item.IdSantri;
                        var info = item.NamaKelas ?? (typeof item.IdKelas !== 'undefined' ? 'Kls ' + item.IdKelas : '-'); 
                        var usia = ''; 
                        if(item.TanggalLahirSantri) {
                             var birthDate = new Date(item.TanggalLahirSantri);
                             var ageDifMs = Date.now() - birthDate.getTime();
                             var ageDate = new Date(ageDifMs);
                             usia = Math.abs(ageDate.getUTCFullYear() - 1970) + ' Thn';
                        }
                        
                        var tr = `
                            <tr>
                                <td class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input check-santri" type="checkbox" id="chk_${id}" value="${id}">
                                        <label class="custom-control-label" for="chk_${id}"></label>
                                    </div>
                                </td>
                                <td>${item.NamaSantri}</td>
                                <td class="text-center text-sm text-capitalize">${item.JenisKelamin || '-'}</td>
                                <td class="text-sm"><small>${item.NamaTpq ?? '-'}</small></td>
                                <td class="text-sm">${info} <br> <small class="text-muted">${usia}</small></td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });
                }
            }
        });
    }

    // Filter Table
    $('#filterTableSantri').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tableSantriAvailable tbody tr").filter(function() {
            var text = $(this).text().toLowerCase(); 
            $(this).toggle(text.indexOf(value) > -1)
        });
    });

    // Checkbox Events
    $(document).on('change', '.check-santri', function() {
        updateSelectionState();
    });

    // Check All
    $('#checkAllSantri').change(function() {
        var isChecked = $(this).prop('checked');
        var visibleRows = $('#tableSantriAvailable tbody tr:visible');
        
        if (isChecked) {
            var checkboxes = visibleRows.find('.check-santri:not(:checked)');
            // Respect Max Limit
            var currentChecked = $('.check-santri:checked').length;
            var allowed = maxPeserta > 0 ? (maxPeserta - currentChecked) : 9999;
            
            checkboxes.each(function(i, elem) {
                if (i < allowed) {
                    $(elem).prop('checked', true);
                }
            });
        } else {
            // Uncheck ALL visible (even if previously checked)
            visibleRows.find('.check-santri:checked').prop('checked', false);
        }
        updateSelectionState();
    });
    
    function updateSelectionState() {
        var checkedCount = $('.check-santri:checked').length;
        $('#selectedCount').text(checkedCount);
        $('#btnDaftarkan').prop('disabled', checkedCount === 0);
        
        // Check Limit Warning (only if manual check logic needs it, but simpler to just count)
        if (maxPeserta > 0 && checkedCount > maxPeserta) {
             Swal.fire('Limit Tercapai', 'Maksimal ' + maxPeserta + ' peserta. Mohon kurangi pilihan.', 'warning');
             $('#btnDaftarkan').prop('disabled', true);
        }
    }

    $('#btnDaftarkan').click(function() {
        // Collect checked IDs
        var selectedSantri = [];
        $('.check-santri:checked').each(function() {
            selectedSantri.push($(this).val());
        });
        
        if (selectedSantri.length === 0) return;
        
        if (maxPeserta > 0 && selectedSantri.length > maxPeserta) {
             Swal.fire('Gagal', 'Jumlah peserta melebihi batas maksimal (' + maxPeserta + ')', 'error');
             return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mendaftarkan...');

        // Detect if this is Kelompok type
        var tipeCabang = '<?= strtolower($cabang['Tipe'] ?? 'individu') ?>';
        
        if (tipeCabang === 'kelompok') {
            // Batch registration for groups - all santri go to same grup
            $.ajax({
                url: '<?= base_url('backend/perlombaan/registerGroupPeserta') ?>',
                type: 'POST',
                data: {
                    cabang_id: <?= $cabang_id ?? 0 ?>,
                    santri_ids: selectedSantri,
                    filterTpq: $('#filterTpq').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Pendaftaran');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Pendaftaran');
                }
            });
        } else {
            // Individual registration - process one by one
            var successCount = 0;
            var failCount = 0;
            var totalToProcess = selectedSantri.length;
            var processedCount = 0;

            function registerNext(index) {
                if (index >= selectedSantri.length) {
                    var message = 'Berhasil: ' + successCount + ' peserta';
                    if (failCount > 0) {
                        message += ', Gagal: ' + failCount + ' peserta';
                    }
                    Swal.fire('Selesai', message, successCount > 0 ? 'success' : 'error').then(() => location.reload());
                    return;
                }

                $.ajax({
                    url: '<?= base_url('backend/perlombaan/registerPeserta') ?>',
                    type: 'POST',
                    data: {
                        cabang_id: <?= $cabang_id ?? 0 ?>,
                        IdSantri: selectedSantri[index],
                        filterTpq: $('#filterTpq').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            successCount++;
                        } else {
                            failCount++;
                        }
                    },
                    error: function() {
                        failCount++;
                    },
                    complete: function() {
                        processedCount++;
                        btn.html('<i class="fas fa-spinner fa-spin"></i> Mendaftarkan ' + processedCount + '/' + totalToProcess + '...');
                        registerNext(index + 1);
                    }
                });
            }

            registerNext(0);
        }
    });

    $('.btn-cancel-peserta').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Batalkan pendaftaran?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/perlombaan/cancelPeserta') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Delete entire group
    $('.btn-cancel-group').click(function() {
        var cabangId = $(this).data('cabang');
        var idTpq = $(this).data('tpq');
        var grupUrut = $(this).data('grupurut');
        var namaGrup = $(this).data('namagrup');
        
        Swal.fire({
            title: 'Hapus Seluruh ' + namaGrup + '?',
            text: 'Semua anggota grup ini akan dihapus dari pendaftaran',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/perlombaan/cancelGroup') ?>',
                    type: 'POST',
                    data: {
                        cabang_id: cabangId,
                        id_tpq: idTpq,
                        grup_urut: grupUrut
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Edit Participant - Open Modal
    var selectedReplacementId = null;
    
    $('.btn-edit-peserta').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var namaGrup = $(this).data('namagrup');
        
        $('#editPesertaId').val(id);
        $('#currentPesertaNama').text(nama);
        if (namaGrup) {
            $('#currentPesertaGrup').html(' <span class="badge badge-primary">' + namaGrup + '</span>');
        } else {
            $('#currentPesertaGrup').html('');
        }
        
        // Load eligible santri for replacement
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getSantriForRegistration') ?>',
            type: 'POST',
            data: { cabang_id: <?= $cabang_id ?? 0 ?> },
            dataType: 'json',
            success: function(response) {
                var tbody = $('#tableEditSantri tbody');
                tbody.empty();
                selectedReplacementId = null;
                $('#btnSimpanGantiPeserta').prop('disabled', true);
                
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        var tr = `
                            <tr class="row-select-santri" data-id="${item.IdSantri}">
                                <td class="text-center">
                                    <input type="radio" name="selectReplacement" value="${item.IdSantri}">
                                </td>
                                <td>${item.NamaSantri}</td>
                                <td>${(item.JenisKelamin && item.JenisKelamin.toUpperCase() == 'LAKI-LAKI') ? 'L' : 'P'}</td>
                                <td><small>${item.NamaTpq ?? '-'}</small></td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });
                } else {
                    tbody.html('<tr><td colspan="4" class="text-center text-muted">Tidak ada santri pengganti yang tersedia</td></tr>');
                }
                
                $('#modalEditPeserta').modal('show');
            }
        });
    });

    // Handle radio selection in edit modal
    $(document).on('change', 'input[name="selectReplacement"]', function() {
        selectedReplacementId = $(this).val();
        $('#btnSimpanGantiPeserta').prop('disabled', false);
    });

    // Filter santri in edit modal
    $('#filterEditSantri').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#tableEditSantri tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Save replacement
    $('#btnSimpanGantiPeserta').click(function() {
        if (!selectedReplacementId) return;
        
        var pesertaId = $('#editPesertaId').val();
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('backend/perlombaan/updatePeserta') ?>',
            type: 'POST',
            data: {
                id: pesertaId,
                new_santri_id: selectedReplacementId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
