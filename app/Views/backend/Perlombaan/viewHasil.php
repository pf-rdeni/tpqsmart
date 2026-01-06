<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Filter Card (Pilih Lomba & Cabang) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Pilih Lomba & Cabang</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
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

        <!-- Row 2: Results Card (Hasil Penilaian) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-ol"></i> Hasil Penilaian
                        </h3>
                        <div class="card-tools">
                            <form action="<?= base_url('backend/perlombaan/batch-download-sertifikat') ?>" method="post" target="_blank" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                                <button type="submit" class="btn btn-tool" title="Download Semua Sertifikat (ZIP)">
                                    <i class="fas fa-file-archive"></i> Download ZIP
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Status</th>
                                        <th>No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th>Nilai Bobot</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($hasil_list)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($hasil_list as $h): ?>
                                            <tr>
                                                <td class="text-center"><?= $h['Peringkat'] ?></td>
                                                <td class="text-center"><?= $h['StatusLabel'] ?></td>
                                                <td><code><?= esc($h['NoPeserta']) ?></code></td>
                                                <td><?= esc($h['NamaSantri']) ?></td>
                                                <td><?= esc($h['NamaTpq'] ?? '-') ?></td>
                                                <td class="text-center"><strong><?= number_format($h['NilaiAkhir'], 2) ?></strong></td>
                                                <td>
                                                    <a href="<?= base_url('backend/perlombaan/download-sertifikat/' . $h['id']) ?>" class="btn btn-sm btn-primary" title="Download Sertifikat" target="_blank">
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
    var storedLombaId = localStorage.getItem('last_lomba_id_hasil');
    var storedCabangId = localStorage.getItem('last_cabang_id_hasil');

    // Restore from localStorage if no ID is in URL
    <?php if (!$cabang): ?>
    if (storedCabangId) {
        window.location.href = '<?= base_url('backend/perlombaan/viewHasil') ?>/' + storedCabangId;
        return;
    } else if (storedLombaId) {
        $('#selectLomba').val(storedLombaId);
        loadCabangByLomba(storedLombaId);
    }
    <?php else: ?>
    // Update storage with current selection if it differs
    localStorage.setItem('last_lomba_id_hasil', '<?= $cabang['lomba_id'] ?>');
    localStorage.setItem('last_cabang_id_hasil', '<?= $cabang['id'] ?>');
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    <?php endif; ?>

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            localStorage.setItem('last_lomba_id_hasil', lombaId);
            loadCabangByLomba(lombaId);
        } else {
            localStorage.removeItem('last_lomba_id_hasil');
            localStorage.removeItem('last_cabang_id_hasil');
            $('#selectCabang').html('<option value="">-- Pilih Cabang --</option>');
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            localStorage.setItem('last_cabang_id_hasil', cabangId);
            window.location.href = '<?= base_url('backend/perlombaan/viewHasil') ?>/' + cabangId;
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
