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
                                                <td class="text-center" data-export="<?= $peserta['sudahTest'] ? 'Sudah Test' : 'Belum Test' ?>" data-status="<?= $peserta['sudahTest'] ? 'sudah-test' : 'belum-test' ?>">
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
        var currentFilter = 'all';
        var currentFilterText = 'Semua';

        // Function to get filter text
        function getFilterText(filter) {
            switch (filter) {
                case 'sudah-test':
                    return 'Sudah Test';
                case 'belum-test':
                    return 'Belum Test';
                default:
                    return 'Semua';
            }
        }

        // Function to get export title and filename
        function getExportInfo() {
            var filterText = getFilterText(currentFilter);
            var dateStr = new Date().toISOString().split('T')[0];
            return {
                title: 'List Peserta Sertifikasi - ' + filterText,
                filename: 'List_Peserta_Sertifikasi_' + filterText.replace(/\s+/g, '_') + '_' + dateStr,
                filterText: filterText
            };
        }

        var table = $('#tablePesertaSertifikasi').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 25,
            'order': [
                [0, 'asc']
            ],
            'dom': 'Bfrtip',
            'buttons': [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    action: function(e, dt, button, config) {
                        var exportInfo = getExportInfo();
                        config.title = exportInfo.title;
                        config.filename = exportInfo.filename;
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    },
                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            filter: 'applied', // Hanya export data yang terfilter
                            search: 'applied' // Hanya export data yang sesuai search
                        },
                        format: {
                            body: function(data, row, column, node) {
                                // Untuk kolom Status Test (kolom ke-7, index 6), tambahkan prefix untuk conditional formatting
                                if (column === 6 && node) {
                                    var statusAttr = $(node).attr('data-status');
                                    var exportValue = $(node).attr('data-export') || $(data).text();

                                    // Tambahkan prefix untuk memudahkan conditional formatting di Excel
                                    if (statusAttr === 'sudah-test') {
                                        return '✓ ' + exportValue; // Prefix untuk Sudah Test
                                    } else if (statusAttr === 'belum-test') {
                                        return '⚠ ' + exportValue; // Prefix untuk Belum Test
                                    }
                                    return exportValue;
                                }

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
                    action: function(e, dt, button, config) {
                        var exportInfo = getExportInfo();
                        config.title = exportInfo.title;
                        config.filename = exportInfo.filename;

                        // Dapatkan index rows yang terfilter berdasarkan status
                        var filteredIndexes = [];
                        dt.rows({
                            search: 'applied'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            var $row = $(this.node());
                            var rowStatus = $row.attr('data-status');

                            var shouldInclude = false;
                            if (currentFilter === 'all') {
                                shouldInclude = true;
                            } else if (currentFilter === 'sudah-test' && rowStatus === 'sudah-test') {
                                shouldInclude = true;
                            } else if (currentFilter === 'belum-test' && rowStatus === 'belum-test') {
                                shouldInclude = true;
                            }

                            if (shouldInclude) {
                                filteredIndexes.push(rowIdx);
                            }
                        });

                        // Set rows function untuk hanya export rows yang terfilter
                        config.exportOptions = config.exportOptions || {};
                        config.exportOptions.rows = function(idx, data, node) {
                            return filteredIndexes.indexOf(idx) !== -1;
                        };
                        config.exportOptions.modifier = {
                            filter: 'applied',
                            search: 'applied'
                        };

                        // Panggil action default
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    },
                    title: function() {
                        return getExportInfo().title;
                    },
                    filename: function() {
                        return getExportInfo().filename;
                    },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            filter: 'applied',
                            search: 'applied'
                        },
                        rows: function(idx, data, node) {
                            // Filter berdasarkan data-status attribute dari row
                            var $row = $(node).closest('tr');
                            var rowStatus = $row.attr('data-status');

                            if (currentFilter === 'all') {
                                return true;
                            } else if (currentFilter === 'sudah-test') {
                                return rowStatus === 'sudah-test';
                            } else if (currentFilter === 'belum-test') {
                                return rowStatus === 'belum-test';
                            }
                            return true;
                        },
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
                        try {
                            doc.defaultStyle.fontSize = 8;
                            doc.defaultStyle.alignment = 'left'; // Default alignment semua text rata kiri
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.alignment = 'left'; // Header rata kiri
                            doc.pageMargins = [10, 10, 10, 10];

                            var filterText = getFilterText(currentFilter);

                            // Set columnStyles untuk alignment semua kolom
                            if (!doc.styles) {
                                doc.styles = {};
                            }
                            if (!doc.styles.columnStyles) {
                                doc.styles.columnStyles = {};
                            }
                            // Set semua kolom rata kiri
                            for (var col = 0; col < 7; col++) {
                                doc.styles.columnStyles[col] = {
                                    alignment: 'left',
                                    cellPadding: 2
                                };
                            }

                            // Override default table cell alignment
                            if (!doc.styles.tableBodyEven) {
                                doc.styles.tableBodyEven = {};
                            }
                            if (!doc.styles.tableBodyOdd) {
                                doc.styles.tableBodyOdd = {};
                            }
                            doc.styles.tableBodyEven.alignment = 'left';
                            doc.styles.tableBodyOdd.alignment = 'left';

                            // Update title dan set alignment kolom
                            if (doc.content && Array.isArray(doc.content) && doc.content.length > 0) {
                                var tableFound = false;

                                // Cari tabel dan update alignment
                                for (var i = 0; i < doc.content.length; i++) {
                                    // Update title
                                    if (doc.content[i] && doc.content[i].text && typeof doc.content[i].text === 'string') {
                                        if (doc.content[i].text.includes('List Peserta Sertifikasi')) {
                                            doc.content[i].text = 'List Peserta Sertifikasi - ' + filterText;
                                        }
                                    }

                                    // Cari tabel
                                    if (!tableFound && doc.content[i] && doc.content[i].table && doc.content[i].table.body && Array.isArray(doc.content[i].table.body)) {
                                        var tableBody = doc.content[i].table.body;

                                        if (tableBody.length > 0) {
                                            // Set alignment untuk semua baris (termasuk header)
                                            for (var j = 0; j < tableBody.length; j++) {
                                                if (tableBody[j] && Array.isArray(tableBody[j]) && tableBody[j].length >= 7) {
                                                    // Set alignment untuk semua kolom (semua rata kiri)
                                                    for (var colIdx = 0; colIdx < 7; colIdx++) {
                                                        var cell = tableBody[j][colIdx];

                                                        if (typeof cell === 'object' && cell !== null && !Array.isArray(cell)) {
                                                            // Pastikan alignment di-set ke left (override jika sudah ada)
                                                            cell.alignment = 'left';
                                                            // Pastikan text ada
                                                            if (!cell.text && cell !== null) {
                                                                cell.text = String(cell);
                                                            }
                                                        } else {
                                                            // Convert string ke object dengan alignment left
                                                            tableBody[j][colIdx] = {
                                                                text: String(cell || ''),
                                                                alignment: 'left'
                                                            };
                                                        }
                                                    }

                                                    // Tambahkan warna text untuk kolom Status Test (index 6) jika bukan header
                                                    if (j > 0) { // Skip header row (index 0)
                                                        var statusCell = tableBody[j][6];
                                                        var statusText = '';

                                                        // Ambil text dari cell (cell sudah diubah menjadi object di loop sebelumnya)
                                                        if (typeof statusCell === 'object' && statusCell !== null && !Array.isArray(statusCell)) {
                                                            statusText = statusCell.text || String(statusCell);
                                                        } else {
                                                            statusText = String(statusCell || '');
                                                        }

                                                        // Set warna text berdasarkan status (tanpa background)
                                                        // Karena cell sudah diubah menjadi object di loop sebelumnya, kita langsung update
                                                        if (statusText.toLowerCase().includes('sudah test')) {
                                                            // Hijau untuk Sudah Test
                                                            tableBody[j][6].color = '#28a745'; // Text hijau
                                                            // Hapus fillColor jika ada
                                                            if (tableBody[j][6].fillColor) {
                                                                delete tableBody[j][6].fillColor;
                                                            }
                                                        } else if (statusText.toLowerCase().includes('belum test')) {
                                                            // Merah untuk Belum Test
                                                            tableBody[j][6].color = '#dc3545'; // Text merah
                                                            // Hapus fillColor jika ada
                                                            if (tableBody[j][6].fillColor) {
                                                                delete tableBody[j][6].fillColor;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            // Set column widths
                                            if (!doc.content[i].table.widths) {
                                                doc.content[i].table.widths = Array(7).fill('*');
                                            }

                                            // Tambahkan text filter sebelum tabel
                                            doc.content.splice(i, 0, {
                                                text: 'Filter: ' + filterText,
                                                style: {
                                                    fontSize: 9,
                                                    bold: true,
                                                    margin: [0, 0, 0, 5]
                                                }
                                            });

                                            tableFound = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        } catch (error) {
                            console.error('Error in PDF customize:', error);
                        }
                    }
                }
            ],
            'language': {
                'emptyTable': 'Tidak ada data peserta',
                'info': 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                'infoEmpty': 'Menampilkan 0 sampai 0 dari 0 data',
                'infoFiltered': '(difilter dari _MAX_ total data)',
                'lengthMenu': 'Tampilkan _MENU_ data',
                'loadingRecords': 'Memuat...',
                'processing': 'Memproses...',
                'search': 'Cari:',
                'zeroRecords': 'Tidak ada data yang ditemukan',
                'paginate': {
                    'first': 'Pertama',
                    'last': 'Terakhir',
                    'next': 'Selanjutnya',
                    'previous': 'Sebelumnya'
                },
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

        // Filter button handlers
        $('#btnFilterAll').on('click', function() {
            currentFilter = 'all';
            currentFilterText = 'Semua';
            $.fn.dataTable.ext.search.pop(); // Remove old filter
            table.draw();
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#btnFilterSudahTest').on('click', function() {
            currentFilter = 'sudah-test';
            currentFilterText = 'Sudah Test';
            $.fn.dataTable.ext.search.pop(); // Remove old filter
            $.fn.dataTable.ext.search.push(filterStatus);
            table.draw();
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#btnFilterBelumTest').on('click', function() {
            currentFilter = 'belum-test';
            currentFilterText = 'Belum Test';
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