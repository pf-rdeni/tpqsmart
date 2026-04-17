<?php
/**
 * View: Preview Data Download Kustom
 * URL: backend/extra/previewDownload (POST)
 *
 * Menampilkan hasil seleksi kolom dalam DataTable dengan:
 * - Kolom sesuai pilihan user (nama kustom)
 * - Export Excel via DataTables Buttons (JSZip + buttons.html5)
 * - Filter TPQ aktif ditampilkan di header
 */
helper('nilai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — TPQ Smart</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?= base_url('/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/template/backend/dist/css/adminlte.min.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/plugins/datatables-colreorder/css/colReorder.bootstrap4.min.css') ?>">
    <style>
        body {
            background: #f4f6f9;
            padding: 20px;
        }
        .preview-header {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: #fff;
            border-radius: 8px;
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(23,162,184,.3);
        }
        .preview-header h3 {
            margin: 0 0 4px 0;
            font-size: 1.4rem;
        }
        .preview-header p {
            margin: 0;
            opacity: .85;
            font-size: .9rem;
        }
        .card-preview {
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
        }
        table.dataTable thead th {
            white-space: nowrap;
        }
        .dt-buttons .btn {
            margin-right: 4px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="hold-transition">

    <!-- Header -->
    <div class="preview-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3>
                    <i class="fas fa-table mr-2"></i>
                    <?= esc($page_title) ?>
                </h3>
                <p>
                    <i class="fas fa-columns mr-1"></i>
                    <?= count($columnDefs) ?> kolom &nbsp;|&nbsp;
                    <i class="fas fa-list mr-1"></i>
                    <?= count($rawData) ?> baris data
                    <?php if (!empty($filterIdTpq)): ?>
                    &nbsp;|&nbsp;
                    <i class="fas fa-filter mr-1"></i>
                    Filter TPQ aktif
                    <?php endif; ?>
                </p>
            </div>
            <div class="no-print mt-2 mt-md-0">
                <button onclick="window.close()" class="btn btn-sm btn-light">
                    <i class="fas fa-times mr-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Info Kolom Terpilih -->
    <div class="card card-outline card-info card-preview mb-3 no-print">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-columns mr-1"></i>Kolom yang Ditampilkan
            </h5>
        </div>
        <div class="card-body py-2">
            <div class="d-flex flex-wrap">
                <?php foreach ($columnDefs as $i => $col): ?>
                <span class="badge badge-secondary mr-1 mb-1 py-1 px-2">
                    <?= $i + 1 ?>. <?= esc($col['label'] ?? $col['db']) ?>
                    <small class="text-muted ml-1">(<?= esc($col['db']) ?>)</small>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card card-outline card-primary card-preview">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-table mr-1"></i>
                <?= esc($page_title) ?>
                <span class="badge badge-primary ml-2"><?= count($rawData) ?> baris</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="previewTable" class="table table-bordered table-striped table-hover table-sm w-100"
                    style="font-size: 12px;">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:45px;">NO</th>
                            <?php foreach ($columnDefs as $col): ?>
                            <th><?= esc($col['label'] ?? $col['db']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rawData)): ?>
                        <?php $no = 1; foreach ($rawData as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <?php foreach ($columnDefs as $col): ?>
                            <?php
                            $dbField = $col['db'] ?? '';
                            $value   = $row[$dbField] ?? '-';
                            
                            // Format tanggal jika field mengandung kata "Tanggal"
                            if (strpos($dbField, 'Tanggal') !== false && !empty($value) && $value !== '-' && $value !== '0000-00-00') {
                                $fmtTgl = $formatTanggal ?? 'indo';
                                if ($fmtTgl === 'indo') {
                                    $value = formatTanggalIndonesia($value);
                                } elseif ($fmtTgl !== 'Y-m-d') {
                                    $value = date($fmtTgl, strtotime($value));
                                }
                            }

                            // Format Jenis Kelamin
                            if ($dbField === 'JenisKelamin' && !empty($value) && $value !== '-') {
                                $jk = strtoupper(trim($value));
                                $fmtJk = $formatJk ?? 'full';
                                if ($fmtJk === 'L/P') {
                                    $value = ($jk === 'LAKI-LAKI' || $jk === 'L' || $jk === 'LK') ? 'L' : 'P';
                                } elseif ($fmtJk === 'LK/PR') {
                                    $value = ($jk === 'LAKI-LAKI' || $jk === 'L' || $jk === 'LK') ? 'LK' : 'PR';
                                } else {
                                    $value = ($jk === 'L' || $jk === 'LK' || $jk === 'LAKI-LAKI') ? 'Laki-laki' : 'Perempuan';
                                }
                            }

                            // Format Teks / Nama (Title Case, Uppercase, dsb)
                            $textFields = [
                                'NamaSantri', 'NamaAyah', 'NamaIbu', 'NamaWali', 'TempatLahirSantri', 'AlamatSantri',
                                'KelurahanDesaSantri', 'KecamatanSantri', 'KabupatenKotaSantri', 'ProvinsiSantri',
                                'PendidikanAyah', 'PekerjaanUtamaAyah', 'PendidikanIbu', 'PekerjaanUtamaIbu',
                                'Nama', 'TempatLahir', 'TempatTugas', 'PendidikanTerakhir', 'JurusanPendidikanTerakhir',
                                'Alamat', 'KelurahanDesa', 'Kecamatan', 'KabupatenKota', 'Kabupaten', 'Provinsi', 'NamaIbuKandung', 'NamaAyahKandung',
                                'JenisKelamin'
                            ];
                            if ((in_array($dbField, $textFields) || strpos($dbField, 'Tanggal') !== false) && !empty($value) && $value !== '-') {
                                $fmtTeks = $formatTeks ?? 'titlecase';
                                if ($fmtTeks === 'titlecase') {
                                    // Menggunakan delimiter khusus agar gelar/singkatan seperti "S.Pd.I" atau "D'Andre" formatnya tetap rapi
                                    $value = ucwords(strtolower(trim($value)), " \t\r\n\f\v-.,'");
                                } elseif ($fmtTeks === 'uppercase') {
                                    $value = strtoupper(trim($value));
                                }
                            }

                            // Format Active
                            if ($dbField === 'Active') {
                                $value = ($value == 1) ? 'Aktif' : 'Tidak Aktif';
                            }
                            // Format Status santri
                            if ($dbField === 'Status' && $dataType === 'santri') {
                                $statusMap = [0 => 'Belum Diproses', 1 => 'Aktif', 2 => 'Alumni', 3 => 'Keluar'];
                                $value = $statusMap[$value] ?? $value;
                            }
                            ?>
                            <td><?= esc($value ?? '-') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="<?= count($columnDefs) + 1 ?>" class="text-center text-muted py-4">
                                <i class="fas fa-inbox mr-1"></i>Tidak ada data yang ditemukan
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('/plugins/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/jszip/jszip.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-colreorder/js/dataTables.colReorder.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/datatables-colreorder/js/colReorder.bootstrap4.min.js') ?>"></script>
    <script>
    $(document).ready(function () {
        var table = $('#previewTable').DataTable({
            "colReorder": true,
            "responsive": false,
            "scrollX": true,
            "scrollCollapse": true,
            "autoWidth": false,
            "paging": true,
            "pageLength": 50,
            "lengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Semua"]],
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "order": [],
            "language": {
                "search": "Cari:",
                "paginate": {
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "zeroRecords": "Tidak ditemukan data yang sesuai"
            },
            "dom": 'Blfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i>Download Excel',
                    title: '<?= esc($page_title) ?>',
                    className: 'btn btn-success btn-sm',
                    filename: '<?= esc($filePrefix) ?>_<?= date('Ymd_His') ?>',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                let val = $(node).text().trim();
                                
                                // Pola RegExp:
                                // 1. Angka panjang >= 12 (NIK, NISN)
                                // 2. Tanggal format Y-m-d, d-m-Y, atau d/m/Y (contoh: 2024-12-01, 12-01-2024)
                                // 3. Tanggal berformat teks (contoh: 17 Agustus 2024)
                                let isLongNum = /^\d{12,}$/.test(val);
                                let isDateNum = /^\d{2,4}[-\/]\d{2}[-\/]\d{2,4}$/.test(val);
                                let isDateTxt = /^\d{1,2}\s[a-zA-Z]+\s\d{4}$/.test(val);

                                if (isLongNum || isDateNum || isDateTxt) {
                                    // Berikan karakter zero-width non-joiner agar Excel membacanya murni sebagai Teks,
                                    // sehingga mencegah Excel merusak nominal/NIP dan juga tidak memformat ulang tanggal (auto date conversion)
                                    return '\u200C' + val;
                                }
                                return val;
                            }
                        }
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '2');
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print mr-1"></i>Cetak',
                    title: '<?= esc($page_title) ?>',
                    className: 'btn btn-secondary btn-sm',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="fas fa-copy mr-1"></i>Copy',
                    className: 'btn btn-info btn-sm',
                    exportOptions: { columns: ':visible' }
                }
            ],
            "initComplete": function() {
                this.api().columns.adjust();
            }
        });

        table.buttons().container().prependTo('#previewTable_wrapper .row:first-child .col-md-6:first');

        let resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if ($.fn.DataTable.isDataTable('#previewTable')) {
                    table.columns.adjust();
                }
            }, 250);
        });
    });
    </script>

</body>
</html>
