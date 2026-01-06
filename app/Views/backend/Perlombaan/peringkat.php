<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Filter & Info Row -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filter & Informasi Cabang</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Filter Section -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lomba</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Lomba --</option>
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
                                <div class="form-group">
                                    <label>Cabang</label>
                                    <select class="form-control" id="selectCabang">
                                        <option value="">-- Pilih Cabang --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Info Section -->
                            <div class="col-md-6">
                                <?php if ($cabang): ?>
                                    <div class="callout callout-info">
                                        <h5><i class="fas fa-info-circle"></i> Info Cabang</h5>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p class="mb-1"><strong>Lomba:</strong> <?= esc($lomba['NamaLomba']) ?></p>
                                                <p class="mb-1"><strong>Cabang:</strong> <?= esc($cabang['NamaCabang']) ?></p>
                                                <p class="mb-1"><strong>Kategori:</strong> <?= $cabang['Kategori'] ?></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php if ($cabang['UsiaMin'] > 0 || $cabang['UsiaMax'] > 0): ?>
                                                    <p class="mb-1"><strong>Usia:</strong> <span class="badge badge-info"><?= $cabang['UsiaMin'] ?> - <?= $cabang['UsiaMax'] ?> Tahun</span></p>
                                                <?php endif; ?>
                                                <?php if (($cabang['KelasMin'] ?? 0) > 0 || ($cabang['KelasMax'] ?? 0) > 0): ?>
                                                    <p class="mb-1"><strong>Kelas:</strong> <span class="badge badge-warning"><?= esc($cabang['NamaKelasMin'] ?: 'Semua') ?> - <?= esc($cabang['NamaKelasMax'] ?: 'Semua') ?></span></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light text-center">
                                        <p class="text-muted mt-3"><i class="fas fa-info-circle"></i> Silakan pilih Lomba dan Cabang terlebih dahulu untuk melihat informasi.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Table Row -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-medal"></i> Peringkat Lomba
                        </h3>
                        <?php if ($cabang && !empty($ranking)): ?>
                        <div class="card-tools">
                            <form action="<?= base_url('backend/perlombaan/batch-download-sertifikat') ?>" method="post" target="_blank" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                                <button type="submit" class="btn btn-tool" title="Download Semua Sertifikat (ZIP)">
                                    <i class="fas fa-file-archive"></i> Download ZIP
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tablePeringkat">
                                <thead>
                                    <tr>
                                        <th width="60" class="text-center">Rank</th>
                                        <th class="text-center">Status</th>
                                        <th>No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th class="text-center">Total Nilai</th>
                                        <th class="text-center">Nilai Bobot</th>
                                        <th class="text-center">Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ranking)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <?= $cabang ? 'Belum ada data nilai' : 'Pilih cabang terlebih dahulu' ?>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ranking as $r): ?>
                                            <tr class="<?= $r['Peringkat'] <= 3 ? 'table-success' : '' ?>">
                                                <td class="text-center">
                                                    <?php if ($r['Peringkat'] == 1): ?>
                                                        <i class="fas fa-trophy text-warning fa-2x"></i>
                                                    <?php elseif ($r['Peringkat'] == 2): ?>
                                                        <i class="fas fa-medal text-secondary fa-lg"></i>
                                                    <?php elseif ($r['Peringkat'] == 3): ?>
                                                        <i class="fas fa-award text-danger fa-lg"></i>
                                                    <?php else: ?>
                                                        <?= $r['Peringkat'] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?= $r['StatusLabel'] ?>
                                                </td>
                                                <td><code><?= esc($r['NoPeserta']) ?></code></td>
                                                <td><?= esc($r['NamaSantri']) ?></td>
                                                <td><?= esc($r['NamaTpq'] ?? '-') ?></td>
                                                <td class="text-center font-weight-bold">
                                                    <?= number_format($r['TotalNilai'], 2) ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary badge-lg" style="font-size: 1.1em;">
                                                        <?= number_format($r['NilaiAkhir'], 2) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('backend/perlombaan/download-sertifikat/' . $r['id']) ?>" target="_blank" class="btn btn-sm btn-info" title="Download Sertifikat">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('backend/perlombaan') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
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
    var storedLombaId = localStorage.getItem('last_lomba_id_peringkat');
    var storedCabangId = localStorage.getItem('last_cabang_id_peringkat');

    // Restore from localStorage if no ID is in URL
    <?php if (!$cabang): ?>
    if (storedCabangId) {
        window.location.href = '<?= base_url('backend/perlombaan/peringkat') ?>/' + storedCabangId;
        return;
    } else if (storedLombaId) {
        $('#selectLomba').val(storedLombaId);
        loadCabangByLomba(storedLombaId);
    }
    <?php else: ?>
    // Update storage with current selection if it differs
    localStorage.setItem('last_lomba_id_peringkat', '<?= $cabang['lomba_id'] ?>');
    localStorage.setItem('last_cabang_id_peringkat', '<?= $cabang['id'] ?>');
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    <?php endif; ?>

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            localStorage.setItem('last_lomba_id_peringkat', lombaId);
            loadCabangByLomba(lombaId);
        } else {
            localStorage.removeItem('last_lomba_id_peringkat');
            localStorage.removeItem('last_cabang_id_peringkat');
            $('#selectCabang').html('<option value="">-- Pilih Cabang --</option>');
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            localStorage.setItem('last_cabang_id_peringkat', cabangId);
            window.location.href = '<?= base_url('backend/perlombaan/peringkat') ?>/' + cabangId;
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
                        html += '<option value="' + item.id + '" ' + selected + '>' + item.NamaCabang + '</option>';
                    });
                    $('#selectCabang').html(html);
                }
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>
