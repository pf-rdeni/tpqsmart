<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-check-circle"></i> <?= $page_title ?></h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="8%">Poto Profil</th>
                    <th width="10%">Aksi</th>
                    <th>Nama Santri</th>
                    <th>JK</th>
                    <th>TTL</th>
                    <th>Nama Ayah</th>
                    <th>Kelurahan</th>
                    <th>No HP Ayah/Ibu</th>
                    <?php if (in_groups('admin')) : ?>
                        <th>TPQ</th>
                    <?php endif; ?>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($dataSantri as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-center">
                        <?php if (!empty($row['PhotoProfil']) && file_exists(FCPATH . 'uploads/santri/' . $row['PhotoProfil'])): ?>
                            <img src="<?= base_url('uploads/santri/' . $row['PhotoProfil']) ?>" alt="Foto" class="rounded border" style="width: 45px; height: 60px; object-fit: cover; border-width: 1px !important; padding: 1px;">
                        <?php else: ?>
                            <img src="<?= base_url('images/no-photo.jpg') ?>" alt="No Foto" class="rounded border" style="width: 45px; height: 60px; object-fit: cover; border-width: 1px !important; padding: 1px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('backend/santri/perbandinganDataSantri/' . $row['id']) ?>" class="btn btn-primary btn-sm btn-block mb-1">
                            <i class="fas fa-edit"></i> Verifikasi
                        </a>
                    </td>
                    <td><?= esc($row['NamaSantri']) ?></td>
                    <td><?= esc($row['JenisKelamin']) ?></td>
                    <td><?= esc($row['TempatLahirSantri']) ?>, <?= !empty($row['TanggalLahirSantri']) ? formatTanggalIndonesia($row['TanggalLahirSantri'], 'd F Y') : '-' ?></td>
                    <td><?= esc($row['NamaAyah']) ?></td>
                    <td><?= esc($row['KelurahanDesaSantri']) ?></td>
                    <td>
                        <?php 
                        $hp = [];
                        if (!empty($row['NoHpAyah'])) {
                            $hp[] = 'A: ' . esc($row['NoHpAyah']);
                        }
                        if (!empty($row['NoHpIbu'])) {
                            $hp[] = 'I: ' . esc($row['NoHpIbu']);
                        }
                        echo implode('<br>', $hp);
                        ?>
                    </td>
                    <?php if (in_groups('admin')) : ?>
                        <td><?= esc($row['NamaTpq']) ?></td>
                    <?php endif; ?>
                    <td>
                        <?php 
                        switch ($row['Status']) {
                            case '1': // Valid / Aktif
                            case 'Sudah Diverifikasi':
                                echo '<span class="badge badge-success">Sudah Diverifikasi</span>';
                                break;
                            case '0': // Pending / Baru
                            case null:
                            case 'Belum Diverifikasi':
                                echo '<span class="badge badge-warning">Belum Diverifikasi</span>';
                                break;
                            case '2': // Revisi 
                            case 'Perlu Revisi':
                            case 'Revisi':
                            case 'Perlu Perbaikan':
                                echo '<span class="badge badge-danger">Perlu Perbaikan</span>';
                                break;
                            default:
                                echo '<span class="badge badge-secondary">' . esc($row['Status']) . '</span>';
                                break;
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    const CURRENT_OPERATOR = '<?= esc($operatorName) ?>';




    // Fix untuk header DataTable yang tidak align saat sidebar di-toggle
    $(document).ready(function() {
        const tableSelector = '#example1';
        
        function adjustTable() {
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().columns.adjust();
            }
        }

        // 1. Listen to AdminLTE pushmenu events
        $(document).on('collapsed.lte.pushmenu shown.lte.pushmenu', function() {
            setTimeout(adjustTable, 300); // Wait for transition to finish
        });

        // 2. Listen to window resize
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(adjustTable, 100);
        });

        // 3. Fallback: Check for container size changes
        const wrapper = document.querySelector('.content-wrapper');
        if (wrapper) {
            const observer = new ResizeObserver(() => {
                adjustTable();
            });
            observer.observe(wrapper);
        }
    });
    
    $(function () {
        // Define custom initComplete to exclude first 3 columns (No, Photo, Aksi)
        var customInitComplete = function() {
            var api = this.api();
            var savedFilters = JSON.parse(localStorage.getItem('verifikasiDataSantri_filters') || '{}');

            api.columns().every(function() {
                var column = this;
                var colIdx = column.index();

                // Skip columns 0 (No), 1 (Poto Profil), 2 (Aksi)
                if (colIdx < 3) return;

                var select = $('<select class="form-control form-control-sm mt-1"><option value="">Semua</option></select>')
                    .appendTo($(column.header()))
                    .on('change', function() {
                        var rawVal = $(this).val();
                        var val = $.fn.dataTable.util.escapeRegex(rawVal);
                        
                        // Save to localStorage
                        var currentFilters = JSON.parse(localStorage.getItem('verifikasiDataSantri_filters') || '{}');
                        if (rawVal === "") {
                            delete currentFilters[colIdx];
                        } else {
                            currentFilters[colIdx] = rawVal;
                        }
                        localStorage.setItem('verifikasiDataSantri_filters', JSON.stringify(currentFilters));

                        // Use partial match search instead of exact match to handle HTML tags around text
                        // Or rely on smart search if we are searching for text content
                        column
                            .search(val ? val : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function(d, j) {
                    var val = d;
                    var text = d;
                    
                    // Check if value contains HTML tag
                    if (d.includes('<')) {
                        // Extract text content from HTML
                        text = $('<div>').html(d).text().trim();
                        // Use the clean text as the value for the dropdown
                        val = $.fn.dataTable.util.escapeRegex(text);
                    } else {
                        val = $.fn.dataTable.util.escapeRegex(d);
                    }
                    
                    // Add option if it doesn't already exist in the select (simple check)
                    // Note: This unique check is basic, strict DataTables implementation normally iterates unique data.
                    // Since we iterate unique() data, just guarding against empty text.
                    if (text && select.find('option[value="' + val + '"]').length === 0) {
                        select.append('<option value="' + val + '">' + text + '</option>');
                    }
                });

                // Restore saved filter
                if (savedFilters[colIdx]) {
                    var savedVal = savedFilters[colIdx];
                    // Check if value actually exists in options to avoid setting invalid state
                    if (select.find('option[value="' + savedVal + '"]').length > 0) {
                        select.val(savedVal);
                        var searchVal = $.fn.dataTable.util.escapeRegex(savedVal);
                        column.search(searchVal ? searchVal : '', true, false);
                    }
                }
            });

            // Draw table once after restoring filters if any
            if (Object.keys(savedFilters).length > 0) {
                api.draw();
            }
        };

        if (typeof initializeDataTableWithFilter === 'function') {
            initializeDataTableWithFilter("#example1", true, ["excel", "pdf", "print", "colvis"], {
                "initComplete": customInitComplete,
                "responsive": false,
                "scrollX": true
            });
        } else {
            $("#example1").DataTable({
                "responsive": false, 
                "lengthChange": true, 
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "initComplete": customInitComplete
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        }
    });
</script>
<?= $this->endSection(); ?>
