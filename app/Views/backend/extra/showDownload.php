<?php
/**
 * View: Download Kustomisasi Data
 * URL: backend/extra/showDownload
 *
 * Fitur:
 * - Pilih sumber data: Santri atau Guru
 * - Filter berdasarkan TPQ (Admin: bisa pilih banyak; Operator: dikunci ke TPQ sendiri)
 * - Pilih field sumber database dan beri nama baru (label kustom)
 * - Preview hasil dalam DataTable dengan export Excel
 */
?>

<?php $this->extend('backend/template/template'); ?>

<?php $this->section('content'); ?>


<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>

        <!-- === WIZARD FORM === -->
        <form id="downloadForm" method="POST" action="<?= base_url('backend/extra/previewDownload') ?>" target="_blank">
            <?= csrf_field() ?>
            <input type="hidden" name="field_mappings" id="fieldMappingsJson">

            <!-- STEP 1: Pilih Sumber Data & Filter TPQ -->
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge badge-primary mr-2">1</span>
                        Pilih Sumber Data &amp; Filter TPQ
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Pilih Tipe Data -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-database mr-1"></i>Sumber Data <span class="text-danger">*</span></label>
                                <select name="data_type" id="dataTypeSelect" class="form-control" required>
                                    <option value="">-- Pilih Sumber Data --</option>
                                    <option value="santri">📚 Data Santri</option>
                                    <option value="guru">👨‍🏫 Data Guru</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter TPQ -->
                        <?php if ($isAdmin): ?>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label><i class="fas fa-school mr-1"></i>Filter TPQ
                                    <small class="text-muted">(kosong = semua TPQ)</small>
                                </label>
                                <select class="form-control select2-tpq" name="filter_tpq[]"
                                    multiple="multiple" id="filterTpq"
                                    data-placeholder="-- Semua TPQ --">
                                    <?php foreach ($listTpq as $tpq): ?>
                                    <option value="<?= esc($tpq['IdTpq']) ?>">
                                        <?= esc($tpq['NamaTpq'] ?? '-') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label><i class="fas fa-school mr-1"></i>TPQ</label>
                                <?php
                                $namaTpqSesi = '-';
                                foreach ($listTpq as $t) {
                                    if ($t['IdTpq'] == $sessionIdTpq) {
                                        $namaTpqSesi = $t['NamaTpq'] ?? '-';
                                        break;
                                    }
                                }
                                ?>
                                <input type="text" class="form-control" value="<?= esc($namaTpqSesi) ?>" readonly>
                                <input type="hidden" name="filter_tpq[]" value="<?= esc($sessionIdTpq) ?>">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Filter Khusus Santri -->
                    <div class="row mt-2" id="santriFilters" style="display:none;">
                        <div class="col-md-12">
                            <hr>
                            <label class="text-info"><i class="fas fa-sliders-h mr-1"></i> Filter Tambahan Data Santri</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label>Tahun Ajaran <small class="text-muted">(kosong = semua)</small></label>
                                <select class="form-control select2-filter" id="filterTahunAjaran" name="filter_tahun_ajaran[]" multiple="multiple" data-placeholder="-- Semua Tahun Ajaran --" style="width: 100%;">
                                    <?php foreach ($listTahunAjaran as $ta): ?>
                                    <?php if(!empty($ta['IdTahunAjaran'])): ?>
                                    <option value="<?= esc($ta['IdTahunAjaran']) ?>"><?= esc($ta['IdTahunAjaran']) ?></option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0)" class="text-xs text-primary" onclick="$('#filterTahunAjaran').find('option').prop('selected', true); $('#filterTahunAjaran').trigger('change');">Pilih Semua</a> | 
                                <a href="javascript:void(0)" class="text-xs text-danger" onclick="$('#filterTahunAjaran').val(null).trigger('change');">Kosongkan</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label>Kelas <small class="text-muted">(kosong = semua)</small></label>
                                <select class="form-control select2-filter" id="filterKelas" name="filter_kelas[]" multiple="multiple" data-placeholder="-- Semua Kelas --" style="width: 100%;">
                                    <?php foreach ($listKelas as $k): ?>
                                    <option value="<?= esc($k['IdKelas']) ?>"><?= esc($k['NamaKelas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0)" class="text-xs text-primary" onclick="$('#filterKelas').find('option').prop('selected', true); $('#filterKelas').trigger('change');">Pilih Semua</a> | 
                                <a href="javascript:void(0)" class="text-xs text-danger" onclick="$('#filterKelas').val(null).trigger('change');">Kosongkan</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status Aktif</label>
                                <select class="form-control" name="filter_status_aktif">
                                    <option value="all">-- Semua Status --</option>
                                    <option value="1" selected>Aktif (1)</option>
                                    <option value="0">Tidak Aktif / Baru (0)</option>
                                    <option value="2">Alumni (2)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Format Global -->
                    <div class="row mt-2" id="formatGlobal">
                        <div class="col-md-12">
                            <hr>
                            <label class="text-secondary"><i class="fas fa-magic mr-1"></i> Opsi Format Tampilan Output (Excel)</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Format Tanggal</label>
                                <select class="form-control" name="format_tanggal">
                                    <option value="indo" selected>17 Agustus 2024 (Teks Indonesia)</option>
                                    <option value="d-m-Y">17-08-2024</option>
                                    <option value="d/m/Y">17/08/2024</option>
                                    <option value="Y-m-d">2024-08-17 (Sesuai Database)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Format Jenis Kelamin</label>
                                <select class="form-control" name="format_jk">
                                    <option value="full" selected>Laki-laki / Perempuan</option>
                                    <option value="LK/PR">LK / PR</option>
                                    <option value="L/P">L / P</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Format Huruf Teks (Nama, Alamat)</label>
                                <select class="form-control" name="format_teks">
                                    <option value="titlecase" selected>Awal Kapital (Deni Rusandi)</option>
                                    <option value="uppercase">KAPITAL SEMUA (DENI RUSANDI)</option>
                                    <option value="original">Sesuai Database (deNi RUSANDI)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Pilih & Rename Field -->
            <div class="card card-outline card-warning mb-3" id="fieldSelectionCard" style="display:none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge badge-warning text-dark mr-2">2</span>
                        Pilih &amp; Kustomisasi Kolom
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Centang field yang ingin ditampilkan, lalu ubah <strong>Nama Kolom</strong> sesuai kebutuhan.
                        Urutan kolom bisa diatur dengan drag &amp; drop.
                    </div>

                    <!-- Tombol aksi cepat -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-success mr-1" id="btnCheckAll">
                            <i class="fas fa-check-square mr-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger mr-1" id="btnUncheckAll">
                            <i class="fas fa-square mr-1"></i>Hapus Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetLabels">
                            <i class="fas fa-undo mr-1"></i>Reset Nama
                        </button>
                    </div>

                    <!-- Container Field untuk Santri -->
                    <div id="santriFieldContainer">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover" id="santriFieldTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:40px;text-align:center;">⬍</th>
                                        <th style="width:40px;text-align:center;">Pilih</th>
                                        <th>Field Database</th>
                                        <th>Nama Kolom (dapat diubah)</th>
                                    </tr>
                                </thead>
                                <tbody id="santriFieldBody">
                                    <?php foreach ($santriFields as $dbField => $defaultLabel): ?>
                                    <tr class="field-row" data-db="<?= esc($dbField) ?>" data-default-label="<?= esc($defaultLabel) ?>">
                                        <td class="text-center drag-handle" style="cursor:grab;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" class="field-checkbox"
                                                data-db="<?= esc($dbField) ?>">
                                        </td>
                                        <td>
                                            <code><?= esc($dbField) ?></code>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm field-label"
                                                value="<?= esc($defaultLabel) ?>"
                                                placeholder="Nama kolom di Excel...">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Container Field untuk Guru -->
                    <div id="guruFieldContainer" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover" id="guruFieldTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:40px;text-align:center;">⬍</th>
                                        <th style="width:40px;text-align:center;">Pilih</th>
                                        <th>Field Database</th>
                                        <th>Nama Kolom (dapat diubah)</th>
                                    </tr>
                                </thead>
                                <tbody id="guruFieldBody">
                                    <?php foreach ($guruFields as $dbField => $defaultLabel): ?>
                                    <tr class="field-row" data-db="<?= esc($dbField) ?>" data-default-label="<?= esc($defaultLabel) ?>">
                                        <td class="text-center drag-handle" style="cursor:grab;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" class="field-checkbox"
                                                data-db="<?= esc($dbField) ?>">
                                        </td>
                                        <td>
                                            <code><?= esc($dbField) ?></code>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm field-label"
                                                value="<?= esc($defaultLabel) ?>"
                                                placeholder="Nama kolom di Excel...">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Preview & Download -->
            <div class="card card-outline card-success mb-3" id="actionCard" style="display:none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge badge-success mr-2">3</span>
                        Preview &amp; Download
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik tombol <strong>"Tampilkan Preview"</strong> untuk melihat hasil di tab baru.
                        Di halaman preview tersedia tombol <strong>Download Excel</strong>.
                    </p>
                    <div id="fieldSummaryBox" class="mb-3"></div>
                    <button type="submit" class="btn btn-success btn-lg" id="btnPreview">
                        <i class="fas fa-eye mr-2"></i>Tampilkan Preview &amp; Download Excel
                    </button>
                </div>
            </div>

        </form>

        <!-- Kartu Download Laporan Khusus (Tersistem) -->
        <h5 class="mt-4 mb-3 text-secondary"><i class="fas fa-file-contract mr-2"></i> Format Laporan Instansi / Pusat</h5>
        <div class="row">
            <!-- Kartu MDT -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 shadow-sm border-info" style="border-top: 3px solid #17a2b8;">
                    <div class="card-body text-center">
                        <i class="fas fa-list-alt fa-3x text-info mb-3"></i>
                        <h5 class="text-bold">Data Peserta MDT</h5>
                        <p class="text-muted text-sm">
                            Halaman khusus untuk mencetak dan mendownload Format Data Santri menggunakan penamaan dan urutan standar sistem MDT (Madrasah Diniyah Takmiliyah).
                        </p>
                        <a href="<?= base_url('backend/santri/dataPesertaMdt') ?>" class="btn btn-info btn-block mt-auto">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Format MDT
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Tempat untuk laporan pusat lain di masa depan dapat ditambahkan di bawah sini -->
        </div>

    </div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<!-- Script drag and drop menggunakan jQuery UI bawaan template -->
<script>
$(document).ready(function () {

    // =========================================================
    // Inisialisasi Select2
    // =========================================================
    <?php if ($isAdmin): ?>
    $('.select2-tpq').select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        closeOnSelect: false,
    });
    <?php endif; ?>

    $('.select2-filter').select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        closeOnSelect: false,
    });

    // =========================================================
    // Perubahan tipe data → tampilkan / sembunyikan field
    // =========================================================
    $('#dataTypeSelect').on('change', function () {
        const val = $(this).val();
        if (!val) {
            $('#fieldSelectionCard').hide();
            $('#actionCard').hide();
            return;
        }
        $('#fieldSelectionCard').show();
        if (val === 'santri') {
            $('#santriFieldContainer').show();
            $('#guruFieldContainer').hide();
            $('#santriFilters').stop().slideDown();
        } else {
            $('#santriFieldContainer').hide();
            $('#guruFieldContainer').show();
            $('#santriFilters').stop().slideUp();
        }
        updateActionCard();
    });

    // =========================================================
    // Checkbox field → update action card
    // =========================================================
    $(document).on('change', '.field-checkbox', function () {
        updateActionCard();
    });

    $(document).on('input', '.field-label', function () {
        updateActionCard();
    });

    // =========================================================
    // Tombol Pilih Semua / Hapus Semua / Reset
    // =========================================================
    $('#btnCheckAll').on('click', function () {
        getActiveBody().find('.field-checkbox').prop('checked', true);
        updateActionCard();
    });
    $('#btnUncheckAll').on('click', function () {
        getActiveBody().find('.field-checkbox').prop('checked', false);
        updateActionCard();
    });
    $('#btnResetLabels').on('click', function () {
        getActiveBody().find('.field-row').each(function () {
            $(this).find('.field-label').val($(this).data('default-label'));
        });
        updateActionCard();
    });

    // =========================================================
    // Sortable (drag & drop urutan baris menggunakan jQuery UI Sortable)
    // =========================================================
    $('#santriFieldBody, #guruFieldBody').sortable({
        handle: '.drag-handle',
        placeholder: "ui-state-highlight",
        cursor: "move",
        update: function(event, ui) {
            updateActionCard();
        }
    }).disableSelection();

    // =========================================================
    // Helper: Dapat body tbody yang sedang aktif
    // =========================================================
    function getActiveBody() {
        const type = $('#dataTypeSelect').val();
        return type === 'santri' ? $('#santriFieldBody') : $('#guruFieldBody');
    }

    // =========================================================
    // Update ringkasan dan tampilkan Step 3
    // =========================================================
    function updateActionCard() {
        const mappings = buildMappings();
        if (mappings.length === 0) {
            $('#actionCard').hide();
            return;
        }
        $('#actionCard').show();

        // Buat ringkasan
        let html = '<p class="mb-1"><strong>' + mappings.length + ' kolom dipilih:</strong></p>';
        html += '<div class="d-flex flex-wrap">';
        mappings.forEach(function (m, i) {
            html += '<span class="badge badge-secondary mr-1 mb-1">' + (i + 1) + '. ' + $('<div>').text(m.label).html() + '</span>';
        });
        html += '</div>';
        $('#fieldSummaryBox').html(html);
    }

    // =========================================================
    // Bangun array mappings dari baris yang tercentang
    // =========================================================
    function buildMappings() {
        const mappings = [];
        getActiveBody().find('.field-row').each(function () {
            const cb = $(this).find('.field-checkbox');
            if (cb.is(':checked')) {
                const db    = $(this).data('db');
                const label = $(this).find('.field-label').val().trim() || $(this).data('default-label');
                mappings.push({ db: db, label: label });
            }
        });
        return mappings;
    }

    // =========================================================
    // Submit form → set JSON ke hidden input
    // =========================================================
    $('#downloadForm').on('submit', function (e) {
        const mappings = buildMappings();
        if (mappings.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Pilih minimal satu field untuk ditampilkan.',
            });
            return false;
        }
        $('#fieldMappingsJson').val(JSON.stringify(mappings));
    });

});
</script>
<?php $this->endSection(); ?>
