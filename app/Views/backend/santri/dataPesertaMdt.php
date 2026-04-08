<?php
/**
 * View: Data Peserta MDT
 * URL: backend/santri/dataPesertaMdt
 *
 * Menampilkan daftar peserta MDT dalam DataTable dengan:
 * - Filter MDT (Admin: semua; Operator: dikunci ke MDT sendiri)
 * - Filter Kelas MDT
 * - Export Excel via DataTables Buttons (JSZip + buttons.html5)
 *
 * Kolom Tabel:
 *   NO | NO. INDUK SISWA LOKAL | NAMA SANTRI | LK | PR | NAMA ORANG TUA | TEMPAT TGL LAHIR | JENJANG | ASAL LEMBAGA (MDT) | KEPALA MDT
 */
?>

<?php $this->extend('backend/template/template'); ?>

<?php $this->section('content'); ?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-list-alt text-warning mr-2"></i>
                    Data Peserta MDT
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item">Santri</li>
                    <li class="breadcrumb-item active">Data Peserta MDT</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- === CARD FILTER === -->
        <div class="card card-outline card-warning mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-1"></i>Filter Data
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('backend/santri/dataPesertaMdt') ?>" id="filterForm">
                    <div class="row">

                        <!-- Pilih MDT (hanya tampil untuk Admin) -->
                        <?php if ($isAdmin): ?>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="filterIdTpq">
                                    <i class="fas fa-school mr-1"></i>Pilih MDT
                                    <small class="text-muted">(bisa pilih lebih dari satu)</small>
                                </label>
                                <select class="form-control select2-multiple" id="filterIdTpq"
                                    name="filterIdTpq[]" multiple="multiple"
                                    data-placeholder="-- Semua MDT --">
                                    <?php
                                    // Normalisasi filterIdTpq jadi array
                                    $activeTpq = [];
                                    if (!empty($filterIdTpq)) {
                                        $activeTpq = is_array($filterIdTpq) ? $filterIdTpq : [$filterIdTpq];
                                    }
                                    ?>
                                    <?php foreach ($dataMda as $mda): ?>
                                    <option value="<?= esc($mda['IdTpq']) ?>"
                                        <?= in_array($mda['IdTpq'], $activeTpq) ? 'selected' : '' ?>>
                                        <?= esc($mda['NamaTpq'] ?? '-') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Operator: tampilkan nama MDT mereka (read-only) -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label><i class="fas fa-school mr-1"></i>MDT</label>
                                <input type="text" class="form-control"
                                    value="<?= esc(!empty($dataMda[0]['NamaTpq']) ? $dataMda[0]['NamaTpq'] : '-') ?>"
                                    readonly>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Pilih Kelas MDT (multiple) -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filterIdKelas">
                                    <i class="fas fa-chalkboard mr-1"></i>Pilih Kelas MDT
                                    <small class="text-muted">(bisa pilih lebih dari satu)</small>
                                </label>
                                <select class="form-control select2-multiple" id="filterIdKelas"
                                    name="filterIdKelas[]" multiple="multiple"
                                    data-placeholder="-- Semua Kelas --">
                                    <?php
                                    // Normalisasi filterIdKelas jadi array
                                    $activeKelas = [];
                                    if (!empty($filterIdKelas)) {
                                        $activeKelas = is_array($filterIdKelas) ? $filterIdKelas : [$filterIdKelas];
                                    }
                                    ?>
                                    <?php foreach ($dataKelas as $kelas): ?>
                                    <option value="<?= esc($kelas['IdKelas']) ?>"
                                        <?= in_array($kelas['IdKelas'], $activeKelas) ? 'selected' : '' ?>>
                                        <?= esc($kelas['NamaKelas'] ?? '-') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-warning mr-2" id="btnFilter">
                                        <i class="fas fa-search mr-1"></i>Filter
                                    </button>
                                    <a href="<?= base_url('backend/santri/dataPesertaMdt') ?>"
                                        class="btn btn-secondary">
                                        <i class="fas fa-undo mr-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- === END CARD FILTER === -->

        <!-- === CARD TABEL === -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Daftar Peserta MDT
                    <span class="badge badge-primary ml-2"><?= count($dataSantri) ?> Santri</span>
                </h3>
                <div class="card-tools">
                    <!-- Info filter aktif -->
                    <?php if (!empty($filterIdTpq) || !empty($filterIdKelas)): ?>
                    <span class="badge badge-info mr-2">Filter Aktif</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tabelPesertaMdt"
                        class="table table-bordered table-striped table-hover table-sm w-100"
                        style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2" class="text-center align-middle" style="width:40px;">NO</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:120px;">NO. INDUK SISWA LOKAL</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:150px;">NAMA SANTRI</th>
                                <th colspan="2" class="text-center align-middle">JENIS KELAMIN</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:160px;">NAMA ORANG TUA SANTRI</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:160px;">TEMPAT TGL LAHIR</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:100px;">JENJANG</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:180px;">ASAL LEMBAGA (MDT)</th>
                                <th rowspan="2" class="text-center align-middle" style="min-width:150px;">KEPALA MDT</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width:40px;">LK</th>
                                <th class="text-center" style="width:40px;">PR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataSantri)): ?>
                            <?php $no = 1; foreach ($dataSantri as $s): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= esc($s['IdSantri'] ?? '-') ?></td>
                                <td><?= esc($s['NamaSantri'] ?? '-') ?></td>

                                <!-- LK centang jika laki-laki -->
                                <td class="text-center">
                                    <?= (strtoupper($s['JenisKelamin'] ?? '') === 'LAKI-LAKI') ? '✓' : '' ?>
                                </td>
                                <!-- PR centang jika perempuan -->
                                <td class="text-center">
                                    <?= (strtoupper($s['JenisKelamin'] ?? '') === 'PEREMPUAN') ? '✓' : '' ?>
                                </td>

                                <!-- Nama Orang Tua: Ayah / Ibu -->
                                <td>
                                    <?php
                                    $namaAyah = esc($s['NamaAyah'] ?? '');
                                    $namaIbu  = esc($s['NamaIbu'] ?? '');
                                    if ($namaAyah && $namaIbu) {
                                        echo $namaAyah . ' / ' . $namaIbu;
                                    } elseif ($namaAyah) {
                                        echo $namaAyah;
                                    } else {
                                        echo $namaIbu ?: '-';
                                    }
                                    ?>
                                </td>

                                <!-- Tempat, Tanggal Lahir -->
                                <td>
                                    <?php
                                    $ttl = [];
                                    if (!empty($s['TempatLahirSantri'])) {
                                        $ttl[] = esc($s['TempatLahirSantri']);
                                    }
                                    if (!empty($s['TanggalLahirSantri'])) {
                                        $ttl[] = formatTanggalIndonesia($s['TanggalLahirSantri']);
                                    }
                                    echo implode(', ', $ttl) ?: '-';
                                    ?>
                                </td>

                                <!-- Kolom Jenjang (Fixed: ULA) -->
                                <td class="text-center">ULA</td>
                                <td><?= !empty($s['NamaMdt']) ? 'MDT ' . esc($s['NamaMdt']) : '-' ?></td>
                                <td><?= esc($s['KepalaSekolahMdt'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Tidak ada data peserta MDT yang ditemukan.
                                    <?php if ($isAdmin && empty($filterIdTpq)): ?>
                                    <br><small>Pilih MDT pada filter untuk melihat data, atau biarkan kosong untuk melihat semua data.</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- === END CARD TABEL === -->

    </div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(document).ready(function () {

    // =====================================================
    // Inisialisasi Select2 untuk filter dropdown
    // =====================================================
    
    // Multiple select (Kelas & MDT) — bisa pilih lebih dari satu
    $('.select2-multiple').select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        closeOnSelect: false,       // Tetap buka dropdown saat pilih item
    });


    // =====================================================
    // Inisialisasi DataTable dengan Buttons Export Excel
    // =====================================================
    var table = $('#tabelPesertaMdt').DataTable({
        "responsive": false,
        "scrollX": true,
        "scrollCollapse": true,
        "autoWidth": false,
        "paging": true,
        "pageLength": 25,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "order": [], // jangan sort awal — urutan dari server sudah benar
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
                title: 'Data Peserta MDT',
                className: 'btn btn-success btn-sm',
                filename: 'data_peserta_mdt_<?= date('Ymd_His') ?>',
                exportOptions: {
                    // Kolom yang di-export (semua kolom)
                    columns: ':visible',
                    // Ambil text bersih (strip HTML)
                    format: {
                        body: function(data, row, column, node) {
                            // Bersihkan HTML dari elemen tabel
                            return $(node).text().trim();
                        }
                    }
                },
                customize: function(xlsx) {
                    // Kustomisasi workbook Excel
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    // Bold header row
                    $('row:first c', sheet).attr('s', '2'); // s=2 = bold style
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i>Cetak',
                className: 'btn btn-secondary btn-sm',
                title: 'Data Peserta MDT',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        "columnDefs": [
            { "orderable": false, "targets": [3, 4] }, // Kolom LK dan PR tidak perlu sortable
        ],
        "initComplete": function() {
            // Adjust columns setelah init
            this.api().columns.adjust();
        }
    });

    // Append buttons ke area yang tepat
    table.buttons().container().prependTo('#tabelPesertaMdt_wrapper .row:first-child .col-md-6:first');

    // Fix scroll kolom saat resize
    let resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if ($.fn.DataTable.isDataTable('#tabelPesertaMdt')) {
                table.columns.adjust();
            }
        }, 250);
    });

    // =====================================================
    // Auto-submit filter saat MDT berubah (Admin only)
    // =====================================================
    <?php if ($isAdmin): ?>
    $('#filterIdTpq').on('change', function() {
        // Opsional: bisa auto-submit, tapi lebih baik tunggu klik tombol Filter
        // $('#filterForm').submit();
    });
    <?php endif; ?>

});
</script>
<?php $this->endSection(); ?>
