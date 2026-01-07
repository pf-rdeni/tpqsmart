<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Filter Kegiatan & Perlombaan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filter Kegiatan & Perlombaan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kegiatan</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Kegiatan --</option>
                                        <?php foreach ($lomba_list as $l): ?>
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
                                    <label>Perlombaan</label>
                                    <select class="form-control" id="selectCabang" <?= !$cabang ? 'disabled' : '' ?>>
                                        <option value="">-- Pilih Perlombaan --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Info Summary -->
        <?php if ($cabang): ?>
        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= count($nilai_data) ?></h3>
                        <p>Total Data Nilai</p>
                    </div>
                    <div class="icon"><i class="fas fa-star"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= count($juri_list) ?></h3>
                        <p>Juri Terdaftar</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-tie"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= count($kriteria_list) ?></h3>
                        <p>Kriteria Penilaian</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-<?= $display_mode == 3 ? 'danger' : 'secondary' ?>">
                    <div class="inner">
                        <h3>Mode <?= $display_mode ?></h3>
                        <p><?php
                            if ($display_mode == 1) echo 'Single Juri';
                            elseif ($display_mode == 2) echo 'Multi Juri - Sama';
                            else echo 'Multi Juri - Beda';
                        ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-table"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Row 3: Data Nilai Table -->
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table"></i> Data Nilai Juri
                            <?php if ($cabang): ?>
                                <span class="badge badge-light ml-2"><?= esc($lomba['NamaLomba']) ?> - <?= esc($cabang['NamaCabang']) ?></span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (!$cabang): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                    Pilih cabang terlebih dahulu untuk melihat data nilai
                                </div>
                            <?php elseif (empty($nilai_data)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Belum ada data nilai untuk cabang ini
                                </div>
                            <?php elseif ($display_mode == 1): ?>
                                <!-- MODE 1: Single Juri -->
                                <?= $this->include('backend/Perlombaan/_monitorNilaiMode1') ?>
                            <?php elseif ($display_mode == 2): ?>
                                <!-- MODE 2: Multiple Juris, Same Kriteria -->
                                <?= $this->include('backend/Perlombaan/_monitorNilaiMode2') ?>
                            <?php else: ?>
                                <!-- MODE 3: Multiple Juris, Different Kriteria -->
                                <?= $this->include('backend/Perlombaan/_monitorNilaiMode3') ?>
                            <?php endif; ?>
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
$(document).ready(function() {
    var storedLombaId = localStorage.getItem('last_lomba_id_monitor');
    var storedCabangId = localStorage.getItem('last_cabang_id_monitor');

    // Restore from localStorage if no ID is in URL
    <?php if (!$cabang): ?>
    if (storedCabangId) {
        location.replace('<?= base_url('backend/perlombaan/monitorNilai') ?>/' + storedCabangId);
        return;
    } else if (storedLombaId) {
        $('#selectLomba').val(storedLombaId);
        loadCabangByLomba(storedLombaId);
    }
    <?php else: ?>
    // Update storage with current selection if it differs
    localStorage.setItem('last_lomba_id_monitor', '<?= $cabang['lomba_id'] ?>');
    localStorage.setItem('last_cabang_id_monitor', '<?= $cabang['id'] ?>');
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    <?php endif; ?>

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            localStorage.setItem('last_lomba_id_monitor', lombaId);
            loadCabangByLomba(lombaId);
        } else {
            localStorage.removeItem('last_lomba_id_monitor');
            localStorage.removeItem('last_cabang_id_monitor');
            $('#selectCabang').html('<option value="">-- Pilih Perlombaan --</option>').prop('disabled', true);
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            localStorage.setItem('last_cabang_id_monitor', cabangId);
            window.location.href = '<?= base_url('backend/perlombaan/monitorNilai') ?>/' + cabangId;
        }
    });

    function loadCabangByLomba(lombaId, selectedId = null) {
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getCabangByLomba') ?>/' + lombaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<option value="">-- Pilih Perlombaan --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + (item.DisplayLabel || item.NamaCabang) + '</option>';
                    });
                    $('#selectCabang').html(html).prop('disabled', false);
                }
            }
        });
    }

    <?php if ($cabang && !empty($nilai_data) && $display_mode != 2): ?>
    $('#tableNilai').DataTable({
        "order": [[1, "asc"]],
        "pageLength": 25,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection(); ?>
