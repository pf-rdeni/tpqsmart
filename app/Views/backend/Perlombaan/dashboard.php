<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Statistik Ringkas -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['total_lomba'] ?></h3>
                        <p>Lomba Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan') ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['total_cabang'] ?></h3>
                        <p>Cabang Lomba</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan') ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $stats['total_peserta'] ?></h3>
                        <p>Peserta Terdaftar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="<?= base_url('backend/perlombaan/pendaftaran') ?>" class="small-box-footer">Kelola Peserta <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['total_juri'] ?></h3>
                        <p>Total Juri</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="small-box-footer" style="height: 30px;"></div>
                </div>
            </div>
        </div>

        <!-- Row 2: Quick Navigator (Full Width) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-th"></i> Menu Cepat</h3>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan') ?>" class="btn btn-outline-primary btn-block p-3">
                                    <i class="fas fa-trophy fa-2x d-block mb-1"></i>
                                    <small>Master Lomba</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/pendaftaran') ?>" class="btn btn-outline-info btn-block p-3">
                                    <i class="fas fa-user-plus fa-2x d-block mb-1"></i>
                                    <small>Pendaftaran</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/pengundian') ?>" class="btn btn-outline-success btn-block p-3">
                                    <i class="fas fa-random fa-2x d-block mb-1"></i>
                                    <small>Pengundian</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/setJuri') ?>" class="btn btn-outline-light btn-block p-3" style="border-color: #17a2b8; color: #17a2b8;">
                                    <i class="fas fa-user-cog fa-2x d-block mb-1"></i>
                                    <small>Setting Juri</small>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/monitorNilai') ?>" class="btn btn-outline-warning btn-block p-3">
                                    <i class="fas fa-chart-bar fa-2x d-block mb-1"></i>
                                    <small>Monitoring</small>
                                </a>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-2">
                                <a href="<?= base_url('backend/perlombaan/peringkat') ?>" class="btn btn-outline-danger btn-block p-3">
                                    <i class="fas fa-medal fa-2x d-block mb-1"></i>
                                    <small>Juara & Skor</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Active Lomba Summary (Full Width) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-running"></i> Perlombaan Sedang Berjalan</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped" id="tableLombaAktif">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Nama Lomba</th>
                                    <th class="text-center">Cabang</th>
                                    <th class="text-center">Peserta</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lomba_list)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada lomba aktif</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lomba_list as $l): ?>
                                        <tr class="lomba-row" data-lomba-id="<?= $l['id'] ?>">
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-expand" data-lomba-id="<?= $l['id'] ?>" title="Lihat Cabang">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <strong><?= esc($l['NamaLomba']) ?></strong><br>
                                                <small class="text-muted">
                                                    <?= $l['NamaTpq'] ? esc($l['NamaTpq']) . ' (' . esc($l['KelurahanDesa']) . ')' : 'Lomba Umum/Pusat' ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $l['total_cabang'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success"><?= $l['total_peserta'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary"><?= ucfirst($l['Status']) ?></span>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= base_url('backend/perlombaan/setCabang/' . $l['id']) ?>" class="btn btn-xs btn-info" title="Detail Cabang">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <!-- Hidden row for cabang list -->
                                        <tr class="cabang-row d-none" id="cabang-row-<?= $l['id'] ?>">
                                            <td colspan="6" class="p-0 bg-light">
                                                <div class="cabang-container p-2" id="cabang-container-<?= $l['id'] ?>">
                                                    <div class="text-center py-2">
                                                        <i class="fas fa-spinner fa-spin"></i> Memuat cabang...
                                                    </div>
                                                </div>
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
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Expand/collapse cabang list
    $('.btn-expand').click(function() {
        var btn = $(this);
        var lombaId = btn.data('lomba-id');
        var cabangRow = $('#cabang-row-' + lombaId);
        var cabangContainer = $('#cabang-container-' + lombaId);
        var icon = btn.find('i');
        
        if (cabangRow.hasClass('d-none')) {
            // Expand
            cabangRow.removeClass('d-none');
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            
            // Load cabang data via AJAX if not loaded yet
            if (!cabangContainer.data('loaded')) {
                $.ajax({
                    url: '<?= base_url('backend/perlombaan/getCabangByLomba') ?>/' + lombaId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            var html = '<table class="table table-sm table-bordered mb-0">';
                            html += '<thead class="thead-light"><tr>';
                            html += '<th>Nama Cabang</th>';
                            html += '<th class="text-center">Kategori</th>';
                            html += '<th class="text-center">Batasan</th>';
                            html += '<th class="text-center">Peserta</th>';
                            html += '<th class="text-center">Juri</th>';
                            html += '<th class="text-center">Aksi</th>';
                            html += '</tr></thead><tbody>';
                            
                            response.data.forEach(function(c) {
                                var batasan = '';
                                if (c.NamaKelasMin && c.NamaKelasMax) {
                                    batasan = c.NamaKelasMin + ' - ' + c.NamaKelasMax;
                                } else if (c.UsiaMin && c.UsiaMax) {
                                    batasan = c.UsiaMin + ' - ' + c.UsiaMax + ' Tahun';
                                }
                                
                                html += '<tr>';
                                html += '<td><i class="fas fa-sitemap text-info mr-2"></i>' + c.NamaCabang + '</td>';
                                html += '<td class="text-center"><span class="badge badge-secondary">' + (c.Kategori || '-') + '</span></td>';
                                html += '<td class="text-center"><small>' + (batasan || '-') + '</small></td>';
                                html += '<td class="text-center"><span class="badge badge-success">' + (c.total_peserta || 0) + '</span></td>';
                                html += '<td class="text-center"><span class="badge badge-warning">' + (c.total_juri || 0) + '</span></td>';
                                html += '<td class="text-center">';
                                html += '<div class="btn-group btn-group-sm">';
                                html += '<a href="<?= base_url('backend/perlombaan/setKriteria') ?>/' + c.id + '" class="btn btn-outline-primary btn-xs" title="Kriteria"><i class="fas fa-list-ol"></i></a>';
                                html += '<a href="<?= base_url('backend/perlombaan/setJuri') ?>/' + c.id + '" class="btn btn-outline-warning btn-xs" title="Juri"><i class="fas fa-user-tie"></i></a>';
                                html += '<a href="<?= base_url('backend/perlombaan/pendaftaran') ?>/' + c.id + '" class="btn btn-outline-success btn-xs" title="Pendaftaran"><i class="fas fa-user-plus"></i></a>';
                                html += '<a href="<?= base_url('backend/perlombaan/peringkat') ?>/' + c.id + '" class="btn btn-outline-info btn-xs" title="Juara/Peringkat"><i class="fas fa-medal"></i></a>';
                                html += '</div>';
                                html += '</td>';
                                html += '</tr>';
                            });
                            
                            html += '</tbody></table>';
                            cabangContainer.html(html);
                        } else {
                            cabangContainer.html('<div class="text-center py-2 text-muted"><i class="fas fa-info-circle"></i> Belum ada cabang</div>');
                        }
                        cabangContainer.data('loaded', true);
                    },
                    error: function() {
                        cabangContainer.html('<div class="text-center py-2 text-danger"><i class="fas fa-exclamation-circle"></i> Gagal memuat data</div>');
                    }
                });
            }
        } else {
            // Collapse
            cabangRow.addClass('d-none');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
        }
    });
});
</script>
<?= $this->endSection(); ?>

