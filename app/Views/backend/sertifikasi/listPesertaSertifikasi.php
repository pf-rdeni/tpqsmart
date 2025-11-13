<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Statistik -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($total_peserta) ?></h3>
                                <p>Total Peserta</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($sudah_test) ?></h3>
                                <p>Sudah Test</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($belum_test) ?></h3>
                                <p>Belum Test</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?= $total_peserta > 0 ? number_format(($sudah_test / $total_peserta) * 100, 1) : 0 ?>%</h3>
                                <p>Persentase Sudah Test</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> List Peserta Sertifikasi
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info" id="btnFilterAll">
                                    <i class="fas fa-list"></i> Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="btnFilterSudahTest">
                                    <i class="fas fa-check-circle"></i> Sudah Test
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" id="btnFilterBelumTest">
                                    <i class="fas fa-clock"></i> Belum Test
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="tablePesertaSertifikasi">
                                <thead>
                                    <tr>
                                        <th>No Peserta</th>
                                        <th>Nama Guru</th>
                                        <th>No Rek</th>
                                        <th>Nama TPQ</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Kecamatan</th>
                                        <th>Status Test</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($peserta_data)): ?>
                                        <?php foreach ($peserta_data as $peserta): ?>
                                            <tr data-status="<?= $peserta['sudahTest'] ? 'sudah-test' : 'belum-test' ?>">
                                                <td data-export="<?= esc($peserta['NoPeserta']) ?>"><strong><?= esc($peserta['NoPeserta']) ?></strong></td>
                                                <td data-export="<?= esc($peserta['Nama']) ?>"><?= esc($peserta['Nama']) ?></td>
                                                <td data-export="<?= esc($peserta['NoRek'] ?? '-') ?>"><?= esc($peserta['NoRek'] ?? '-') ?></td>
                                                <td data-export="<?= esc($peserta['NamaTpq'] ?? '-') ?>"><?= esc($peserta['NamaTpq'] ?? '-') ?></td>
                                                <td data-export="<?= esc($peserta['JenisKelamin'] ?? '-') ?>"><?= esc($peserta['JenisKelamin'] ?? '-') ?></td>
                                                <td data-export="<?= esc($peserta['Kecamatan'] ?? '-') ?>"><?= esc($peserta['Kecamatan'] ?? '-') ?></td>
                                                <td class="text-center">
                                                    <?php if ($peserta['sudahTest']): ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Sudah Test
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-clock"></i> Belum Test
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <p class="text-muted">Tidak ada data peserta</p>
                                            </td>
                                        </tr>
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
        var table = $('#tablePesertaSertifikasi').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 25,
            'order': [[0, 'asc']],
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'List Peserta Sertifikasi',
                    filename: 'List_Peserta_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'List Peserta Sertifikasi',
                    filename: 'List_Peserta_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [10, 10, 10, 10];
                    }
                }
            ],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json',
                'buttons': {
                    excel: 'Export ke Excel',
                    pdf: 'Export ke PDF'
                }
            }
        });

        // Custom filter function
        var filterStatus = function(settings, data, dataIndex) {
            var status = $(table.row(dataIndex).node()).attr('data-status');
            if (currentFilter === 'all') {
                return true;
            } else if (currentFilter === 'sudah-test') {
                return status === 'sudah-test';
            } else if (currentFilter === 'belum-test') {
                return status === 'belum-test';
            }
            return true;
        };

        var currentFilter = 'all';
        
        // Filter button handlers
        $('#btnFilterAll').on('click', function() {
            currentFilter = 'all';
            $.fn.dataTable.ext.search.pop(); // Remove old filter
            table.draw();
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#btnFilterSudahTest').on('click', function() {
            currentFilter = 'sudah-test';
            $.fn.dataTable.ext.search.pop(); // Remove old filter
            $.fn.dataTable.ext.search.push(filterStatus);
            table.draw();
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#btnFilterBelumTest').on('click', function() {
            currentFilter = 'belum-test';
            $.fn.dataTable.ext.search.pop(); // Remove old filter
            $.fn.dataTable.ext.search.push(filterStatus);
            table.draw();
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
        });

        // Set default filter to "All"
        $('#btnFilterAll').addClass('active');
    });
</script>
<style>
    .dt-buttons {
        margin-bottom: 10px;
    }
    .dt-buttons .btn {
        margin-right: 5px;
    }
    .btn-group .btn.active {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
<?= $this->endSection(); ?>

