<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Filter & Info Row -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filter & Informasi Perlombaan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Filter Section -->
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
                                <div class="form-group">
                                    <label>Perlombaan</label>
                                    <select class="form-control" id="selectCabang">
                                        <option value="">-- Pilih Perlombaan --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Info Section -->
                            <div class="col-md-6">
                                <?php if ($cabang): ?>
                                    <div class="callout callout-info">
                                        <h5><i class="fas fa-info-circle"></i> Info Perlombaan</h5>
                                        <p class="mb-1"><strong>Nama Perlombaan:</strong> <?= esc($cabang['NamaCabang']) ?></p>
                                        <?php if ($cabang['UsiaMin'] > 0 || $cabang['UsiaMax'] > 0): ?>
                                            <p class="mb-1"><strong>Usia:</strong> <span class="badge badge-info"><?= $cabang['UsiaMin'] ?> - <?= $cabang['UsiaMax'] ?> Tahun</span></p>
                                        <?php endif; ?>
                                        <?php if (($cabang['KelasMin'] ?? 0) > 0 || ($cabang['KelasMax'] ?? 0) > 0): ?>
                                            <p class="mb-1"><strong>Kelas:</strong> <span class="badge badge-warning"><?= esc($cabang['NamaKelasMin'] ?: 'Semua') ?> - <?= esc($cabang['NamaKelasMax'] ?: 'Semua') ?></span></p>
                                        <?php endif; ?>
                                        <p class="mb-0"><strong>Kategori:</strong> <?= $cabang['Kategori'] ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light text-center">
                                        <p class="text-muted mt-3"><i class="fas fa-info-circle"></i> Silakan pilih Kegiatan dan Perlombaan terlebih dahulu untuk melihat informasi.</p>
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
                            <i class="fas fa-medal"></i> Peringkat Perlombaan
                        </h3>
                        <?php if ($cabang && !empty($ranking)): ?>
                        <div class="card-tools">
                            <form id="formBatchDownload" action="<?= base_url('backend/perlombaan/batch-download-sertifikat') ?>" method="post" target="_blank" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                                <button type="submit" class="btn btn-tool" title="Download Semua Sertifikat (ZIP)">
                                    <i class="fas fa-file-archive"></i> Download Semua Sertifikat
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
                                                <?= $cabang ? 'Belum ada data nilai' : 'Pilih perlombaan terlebih dahulu' ?>
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
                                                    <a href="<?= base_url('backend/perlombaan/download-sertifikat/' . $r['id']) ?>" class="btn btn-sm btn-info btn-download-sertifikat" data-cabang="<?= $cabang['id'] ?>" title="Download Sertifikat">
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
        location.replace('<?= base_url('backend/perlombaan/peringkat') ?>/' + storedCabangId);
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
            $('#selectCabang').html('<option value="">-- Pilih Perlombaan --</option>');
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
                    var html = '<option value="">-- Pilih Perlombaan --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + (item.DisplayLabel || item.NamaCabang) + '</option>';
                    });
                    $('#selectCabang').html(html);
                }
            }
        });
    }


    // ==================== CERTIFICATE DOWNLOAD HANDLERS ====================

    // Helper: Validate Template Existence
    function validateTemplate(cabangId, onSuccess) {
        Swal.fire({
            title: 'Memeriksa Template...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/perlombaan/check-certificate-template') ?>/' + cabangId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    onSuccess();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Template Belum Siap',
                        text: response.message || 'Template sertifikat belum dikonfigurasi untuk perlombaan ini.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memeriksa status template. Silakan coba lagi.',
                });
            }
        });
    }

    // Batch Download Handler
    $('#formBatchDownload').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var cabangId = $(this).find('input[name="cabang_id"]').val();

        validateTemplate(cabangId, function() {
            Swal.fire({
                title: 'Sedang Menggenerate Sertifikat...',
                html: 'Proses ini mungkin memakan waktu beberapa saat.<br>Mohon jangan tutup halaman ini.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form logic
            // Since we intercepted submit, we can just remove handler and submit, or trigger native submit
            // But we want to convert it to a "download" flow.
            // Actually, we can just let it submit to _blank after validation!
            
            // Re-submit programmatically
            form.submit();
            
            // Close swal after a short delay or let it stay? 
            // Since it opens in new tab, we should probably reset Swal in THIS tab after a few seconds
            setTimeout(function() {
                Swal.close();
            }, 3000);
        });
    });

    // Individual Download Handler
    $(document).on('click', '.btn-download-sertifikat', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var cabangId = $(this).data('cabang');

        validateTemplate(cabangId, function() {
            Swal.fire({
                title: 'Sedang Menggenerate Sertifikat...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Open in new tab/window
            window.open(url, '_blank');
            
            // Close swal
            setTimeout(function() {
                Swal.close();
            }, 2000);
        });
    });

});
</script>
<?= $this->endSection(); ?>
