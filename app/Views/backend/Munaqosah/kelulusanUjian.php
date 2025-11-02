<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Kelulusan Ujian</h3>
                        <div class="d-flex">
                            <div class="mr-2">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>">
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">TPQ</label>
                                <select id="filterTpq" class="form-control form-control-sm">
                                    <option value="0">Semua TPQ</option>
                                    <?php if (!empty($tpqDropdown)) : foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">Type Ujian</label>
                                <select id="filterTypeUjian" class="form-control form-control-sm">
                                    <option value="munaqosah">Munaqosah</option>
                                    <option value="pra-munaqosah">Pra-Munaqosah</option>
                                </select>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReloadKelulusan" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Peserta</span>
                                        <span class="info-box-number" id="statTotalPeserta">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:100%"></div>
                                        </div>
                                        <span class="progress-description">Terdata</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Lulus</span>
                                        <span class="info-box-number" id="statLulus">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barLulus" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descLulus">0% lulus</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Belum Lulus</span>
                                        <span class="info-box-number" id="statBelumLulus">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barBelumLulus" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descBelumLulus">0% belum lulus</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rata Nilai Bobot</span>
                                        <span class="info-box-number" id="statRerataBobot">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:100%"></div>
                                        </div>
                                        <span class="progress-description">Rerata total bobot</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" id="kelulusanTableWrapper">
                            <table id="tblKelulusan" class="table table-bordered table-striped" style="width:100%">
                                <thead id="theadKelulusan"></thead>
                                <tbody id="tbodyKelulusan"></tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Sumber bobot: <span id="bobotSourceLabel">-</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php
// Cek apakah user adalah admin
$isAdmin = function_exists('in_groups') && in_groups('Admin');
?>
<style>
    .nilai-0 {
        background-color: #f8d7da !important;
        color: #dc3545;
        font-weight: 600;
    }

    .badge-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
    }

    .nowrap {
        white-space: nowrap;
    }

    .dt-center {
        text-align: center;
    }

    .dt-right {
        text-align: right;
    }

    .dt-left {
        text-align: left;
    }
</style>
<script>
    let kelulusanTable = null;

    function fmtScore(val) {
        if (val === null || val === undefined) return '-';
        const num = parseFloat(val);
        if (isNaN(num)) return '-';
        return num === 0 ? '<span class="nilai-0">0</span>' : num;
    }

    function fmtDecimal(val) {
        if (val === null || val === undefined) return '-';
        const num = parseFloat(val);
        if (isNaN(num)) return '-';
        return num.toFixed(2);
    }

    function buildKelulusanHeader(categories) {
        const headerCategories = categories || [];
        let th1 = '<tr>' +
            '<th class="dt-left">No Peserta</th>' +
            '<th class="dt-left">Nama Santri</th>' +
            '<th class="dt-left">TPQ</th>' +
            '<th class="dt-center">Type</th>' +
            '<th class="dt-center">Thn</th>' +
            '<th class="dt-center">Aksi</th>';

        headerCategories.forEach(cat => {
            const weight = cat.weight ? parseFloat(cat.weight) : 0;
            const weightLabel = weight > 0 ? ` (${weight}% )` : '';
            const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
            // Kolom: Juri (maxJuri kolom) + Jml + Bobot = maxJuri + 2
            th1 += `<th class="dt-center nowrap" colspan="${maxJuri + 2}">${cat.name}${weightLabel}</th>`;
        });

        th1 += '<th class="dt-center">Total Bobot</th>' +
            '<th class="dt-center">Status Kelulusan</th>' +
            '</tr>';

        let th2 = '<tr>' +
            '<th></th><th></th><th></th><th></th><th></th><th></th>';
        headerCategories.forEach(cat => {
            const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
            for (let i = 1; i <= maxJuri; i++) {
                th2 += `<th class="dt-center">Juri ${i}</th>`;
            }
            th2 += '<th class="dt-center">Jml</th>' +
                '<th class="dt-center">Bobot</th>';
        });
        th2 += '<th></th><th></th></tr>';

        $('#theadKelulusan').html(th1 + th2);
    }

    function buildKelulusanRows(categories, rows) {
        const headerCategories = categories || [];
        const body = [];
        rows.forEach(row => {
            const params = new URLSearchParams({
                NoPeserta: row.NoPeserta || '',
                IdTahunAjaran: row.IdTahunAjaran || '',
                TypeUjian: row.TypeUjian || '',
                IdTpq: row.IdTpq || ''
            }).toString();

            const viewUrl = '<?= base_url('backend/munaqosah/kelulusan-peserta') ?>' + '?' + params;
            const pdfUrl = '<?= base_url('backend/munaqosah/printKelulusanPesertaUjian') ?>' + '?' + params;

            const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
            let actionHtml = '<div class="btn-group btn-group-sm" role="group">' +
                `<a class="btn btn-outline-primary" href="${pdfUrl}" target="_blank"><i class="fas fa-file-pdf"></i> Pdf</a>`;
            if (isAdmin) {
                actionHtml += `<a class="btn btn-outline-secondary" href="${viewUrl}" target="_blank"><i class="fas fa-eye"></i> View</a>`;
            }
            actionHtml += '</div>';

            const totalWeighted = parseFloat(row.total_weighted ?? 0).toFixed(2);
            const threshold = parseFloat(row.kelulusan_threshold ?? 0).toFixed(2);
            const status = row.kelulusan_status || '-';
            const diff = parseFloat(row.kelulusan_difference ?? 0).toFixed(2);
            const passed = !!row.kelulusan_met;
            const badgeClass = passed ? 'badge badge-success' : 'badge badge-danger';
            const badgeText = `${status} (${totalWeighted} / ${threshold})`;

            let tds = `<td class="dt-left">${row.NoPeserta || '-'}</td>` +
                `<td class="dt-left">${row.NamaSantri || '-'}</td>` +
                `<td class="dt-left">${row.NamaTpq || '-'}</td>` +
                `<td class="dt-center">${row.TypeUjian || '-'}</td>` +
                `<td class="dt-center">${row.IdTahunAjaran || '-'}</td>` +
                `<td class="dt-center">${actionHtml}</td>`;

            headerCategories.forEach(cat => {
                const catId = cat.id || cat.IdKategoriMateri;
                const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
                const scores = row.nilai && row.nilai[catId] ? row.nilai[catId] : [];

                // Hitung nilai valid (nilai yang > 0)
                const validScores = scores.filter(s => s > 0);
                let avg = row.averages && row.averages[catId] !== undefined ? row.averages[catId] : 0;

                // Jika hanya ada satu nilai valid dan average tidak sesuai, gunakan nilai tersebut
                if (validScores.length === 1 && avg !== validScores[0]) {
                    avg = validScores[0];
                }

                const weighted = row.weighted && row.weighted[catId] !== undefined ? row.weighted[catId] : 0;

                // Generate kolom juri secara dinamis
                for (let i = 0; i < maxJuri; i++) {
                    const nilai = (scores[i] !== undefined && scores[i] !== null) ? scores[i] : 0;
                    tds += `<td class="dt-center">${fmtScore(nilai)}</td>`;
                }

                tds += `<td class="dt-center">${fmtDecimal(avg)}</td>` +
                    `<td class="dt-center">${fmtDecimal(weighted)}</td>`;
            });

            tds += `<td class="dt-center dt-right" data-order="${totalWeighted}">${fmtDecimal(totalWeighted)}</td>` +
                `<td class="dt-center" data-order="${passed ? 1 : 0}"><span class="badge badge-status ${badgeClass}" title="Selisih ${diff}">${badgeText}</span></td>`;

            body.push(`<tr>${tds}</tr>`);
        });

        $('#tbodyKelulusan').html(body.join(''));
    }

    function loadKelulusan() {
        const tahun = $('#filterTahunAjaran').val().trim();
        const tpq = $('#filterTpq').val();
        const type = $('#filterTypeUjian').val();

        if (!tahun) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi',
                text: 'Tahun ajaran tidak boleh kosong'
            });
            return;
        }

        const url = '<?= base_url('backend/munaqosah/kelulusan-data') ?>' + `?IdTahunAjaran=${encodeURIComponent(tahun)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(type)}`;

        Swal.fire({
            title: 'Memuat... ',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.getJSON(url, function(resp) {
            Swal.close();
            if (!resp.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: resp.message || 'Gagal memuat data'
                });
                return;
            }

            const data = resp.data || {
                categories: [],
                rows: [],
                meta: {}
            };
            const categories = data.categories || [];
            const rows = data.rows || [];

            if (kelulusanTable) {
                kelulusanTable.destroy();
                kelulusanTable = null;
            }

            $('#kelulusanTableWrapper').html(
                '<table id="tblKelulusan" class="table table-bordered table-striped" style="width:100%">' +
                '<thead id="theadKelulusan"></thead>' +
                '<tbody id="tbodyKelulusan"></tbody>' +
                '</table>'
            );

            buildKelulusanHeader(categories);
            buildKelulusanRows(categories, rows);

            // Hitung total kolom berdasarkan maxJuri per kategori
            let totalCols = 6; // No Peserta, Nama Santri, TPQ, Type, Thn, Aksi
            categories.forEach(cat => {
                const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
                totalCols += maxJuri + 2; // Juri + Jml + Bobot
            });
            totalCols += 2; // Total Bobot + Status Kelulusan

            const totalIndex = totalCols - 2; // Kolom Total Bobot
            const statusIndex = totalCols - 1; // Kolom Status Kelulusan

            kelulusanTable = $('#tblKelulusan').DataTable({
                scrollX: true,
                order: [
                    [totalIndex, 'desc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: ['colvis', 'excel', 'print'],
                columnDefs: [{
                    targets: [5],
                    orderable: false,
                    searchable: false
                }]
            });

            const totalPeserta = rows.length;
            let lulus = 0;
            let totalWeightedSum = 0;
            rows.forEach(row => {
                if (row.kelulusan_met) lulus++;
                totalWeightedSum += parseFloat(row.total_weighted ?? 0);
            });
            const belum = totalPeserta - lulus;
            const pctLulus = totalPeserta > 0 ? Math.round((lulus / totalPeserta) * 100) : 0;
            const pctBelum = totalPeserta > 0 ? Math.round((belum / totalPeserta) * 100) : 0;
            const avgTotal = totalPeserta > 0 ? (totalWeightedSum / totalPeserta) : 0;

            $('#statTotalPeserta').text(totalPeserta);
            $('#statLulus').text(lulus);
            $('#barLulus').css('width', pctLulus + '%');
            $('#descLulus').text(pctLulus + '% lulus');
            $('#statBelumLulus').text(belum);
            $('#barBelumLulus').css('width', pctBelum + '%');
            $('#descBelumLulus').text(pctBelum + '% belum');
            $('#statRerataBobot').text(avgTotal.toFixed(2));

            const bobotSource = data.meta && data.meta.bobot_source ? data.meta.bobot_source : '-';
            $('#bobotSourceLabel').text(bobotSource);
        }).fail(function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Tidak dapat memuat data'
            });
        });
    }

    $(function() {
        const $tpqSelect = $('#filterTpq');
        const nonZeroOptions = $tpqSelect.find('option').filter(function() {
            return $(this).val() !== '0';
        });
        if (nonZeroOptions.length === 1) {
            const onlyId = $(nonZeroOptions[0]).val();
            $tpqSelect.val(onlyId).prop('disabled', true);
            $('#filterTypeUjian').val('pra-munaqosah').prop('disabled', true);
        }

        $('#btnReloadKelulusan').on('click', loadKelulusan);
        $('#filterTpq').on('change', loadKelulusan);
        $('#filterTypeUjian').on('change', loadKelulusan);

        loadKelulusan();
    });
</script>
<?= $this->endSection(); ?>