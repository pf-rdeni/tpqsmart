<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Monitoring Munaqosah</h3>
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
                                <button id="btnReload" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistik ringkas (re-use style dari inputNilaiJuri step 1) -->
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
                                        <span class="progress-description">Terregistrasi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sudah Dinilai</span>
                                        <span class="info-box-number" id="statSudah">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barSudah" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descSudah">0% selesai</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Belum Dinilai</span>
                                        <span class="info-box-number" id="statBelum">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barBelum" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description" id="descBelum">0% pending</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Progress</span>
                                        <span class="info-box-number" id="statProgress">-</span>
                                        <div class="progress">
                                            <div class="progress-bar" id="barProgress" style="width:0%"></div>
                                        </div>
                                        <span class="progress-description">Tingkat penyelesaian</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Monitoring -->
                        <div class="table-responsive" id="tblContainer">
                            <table id="tblMonitoring" class="table table-bordered table-striped" style="width:100%">
                                <thead id="theadMonitoring"></thead>
                                <tbody id="tbodyMonitoring"></tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Gunakan tombol Column visibility (ColVis) untuk membuka/menutup kolom nilai per kategori.</small>
                    </div>
                </div>

                <!-- Bagian monitoring tambahan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monitoring Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Total juri aktif: <span id="monTotalJuri">-</span></li>
                            <li>Jumlah TPQ aktif: <span id="monTotalTpq">-</span></li>
                            <li>Terakhir data nilai masuk: <span id="monLastUpdate">-</span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .nilai-0 {
        background-color: #f8d7da !important;
        color: #dc3545;
        font-weight: 600;
    }

    .nowrap {
        white-space: nowrap;
    }

    .dt-center {
        text-align: center;
    }

    .dt-left {
        text-align: left;
    }

    .dt-right {
        text-align: right;
    }
</style>
<script>
    let dtInstance = null;

    function fmt(val) {
        return (val === 0 || val === '0') ? '<span class="nilai-0">0</span>' : val;
    }

    function buildHeader(categories) {
        const headerCategories = categories || [];
        let th = '<tr>' +
            '<th class="dt-left">No Peserta</th>' +
            '<th class="dt-left">Nama Santri</th>' +
            '<th class="dt-left">TPQ</th>' +
            '<th class="dt-center">Type</th>' +
            '<th class="dt-center">Thn</th>';
        headerCategories.forEach(k => {
            const label = (k && (k.name || k.NamaKategoriMateri)) ? (k.name || k.NamaKategoriMateri) : (k.id || k.IdKategoriMateri || '-');
            th += `<th class="dt-center nowrap" colspan="2">${label}</th>`;
        });
        th += '</tr>';

        let th2 = '<tr>' +
            '<th></th><th></th><th></th><th></th><th></th>';
        headerCategories.forEach(() => {
            th2 += '<th class="dt-center">Juri 1</th><th class="dt-center">Juri 2</th>';
        });
        th2 += '</tr>';

        $('#theadMonitoring').html(th + th2);
    }

    function buildRows(categories, rows) {
        const headerCategories = categories || [];
        const tbody = [];
        rows.forEach(r => {
            // hitung status row untuk ikon
            let allZero = true; // semua kategori belum ada nilai satupun
            let allComplete = true; // setiap kategori sudah punya 2 nilai (dua juri)
            headerCategories.forEach(cat => {
                const key = cat.id || cat.IdKategoriMateri || cat;
                const sc = r.nilai[key] || [0, 0];
                if ((sc[0] || 0) > 0 || (sc[1] || 0) > 0) {
                    allZero = false;
                }
                if (!((sc[0] || 0) > 0 && (sc[1] || 0) > 0)) {
                    allComplete = false;
                }
            });

            let iconHtml = '';
            if (allZero) {
                iconHtml = '<i class="fas fa-question-circle text-danger mr-1" title="Belum dinilai"></i>';
            } else if (allComplete) {
                iconHtml = '<i class="fas fa-check-circle text-success mr-1" title="Selesai"></i>';
            } else {
                iconHtml = '<i class="fas fa-hourglass-half text-warning mr-1" title="Proses"></i>';
            }

            // status order: 0=belum, 1=proses, 2=selesai
            const statusOrder = allZero ? 0 : (allComplete ? 2 : 1);

            let tds = `<td class=\"dt-left\" data-order=\"${statusOrder}\">${iconHtml}${r.NoPeserta}</td>` +
                `<td class="dt-left">${r.NamaSantri}</td>` +
                `<td class="dt-left">${r.NamaTpq}</td>` +
                `<td class="dt-center">${r.TypeUjian}</td>` +
                `<td class="dt-center">${r.IdTahunAjaran}</td>`;
            headerCategories.forEach(cat => {
                const key = cat.id || cat.IdKategoriMateri || cat;
                let sc = r.nilai[key] || [0, 0];
                tds += `<td class="dt-center">${fmt(sc[0])}</td><td class="dt-center">${fmt(sc[1])}</td>`;
            });
            tbody.push(`<tr>${tds}</tr>`);
        });
        $('#tbodyMonitoring').html(tbody.join(''));
    }

    function loadMonitoring() {
        const th = $('#filterTahunAjaran').val().trim();
        const tpq = $('#filterTpq').val();
        const ty = $('#filterTypeUjian').val();
        const url = '<?= base_url("backend/munaqosah/monitoring-data") ?>' + `?IdTahunAjaran=${encodeURIComponent(th)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(ty)}`;

        Swal.fire({
            title: 'Memuat...',
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
                rows: []
            };
            const headerCategories = data.categories || [];

            // Hancurkan instance lama dan rebuild table skeleton agar DataTables benar-benar refresh
            if (dtInstance) {
                dtInstance.destroy();
                dtInstance = null;
            }
            // Rebuild element table (hindari wrapper sisa DT)
            $('#tblContainer').html(
                '<table id="tblMonitoring" class="table table-bordered table-striped" style="width:100%">\
                    <thead id="theadMonitoring"></thead>\
                    <tbody id="tbodyMonitoring"></tbody>\
                </table>'
            );

            buildHeader(headerCategories);
            buildRows(headerCategories, data.rows);

            // Tentukan kolom nilai (mulai index 5) untuk di-hide secara default
            const nilaiColumnCount = headerCategories.length * 2;
            const hiddenTargets = Array.from({
                length: nilaiColumnCount
            }, (_, i) => i + 5);
            // Siapkan label ColVis: "KATEGORI-JURI 1/2"
            const colMetaMap = {};
            let walker = 5;
            headerCategories.forEach(cat => {
                const label = (cat && (cat.name || cat.NamaKategoriMateri)) ? (cat.name || cat.NamaKategoriMateri) : (cat.id || cat.IdKategoriMateri || '-');
                colMetaMap[walker] = {
                    category: label,
                    juri: 1
                };
                walker++;
                colMetaMap[walker] = {
                    category: label,
                    juri: 2
                };
                walker++;
            });

            // Inisialisasi ulang DataTables setelah table bersih
            dtInstance = $('#tblMonitoring').DataTable({
                scrollX: true,
                order: [
                    [0, 'asc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'colvis',
                        text: 'Column visibility',
                        columns: hiddenTargets,
                        columnText: function(dt, idx, title) {
                            const meta = colMetaMap[idx];
                            if (meta) {
                                return meta.category + ' - JURI ' + meta.juri;
                            }
                            return title;
                        }
                    },
                    'excel', 'print'
                ],
                columnDefs: [{
                    targets: hiddenTargets,
                    visible: false
                }]
            });

            // Hitung statistik dasar dari data yang tampil
            const totalPeserta = data.rows.length;
            let sudah = 0;
            data.rows.forEach(r => {
                // dianggap sudah dinilai jika semua kategori punya minimal 1 nilai > 0 (dua juri prinsipnya, tapi toleransi satu nilai)
                let doneAll = true;
                headerCategories.forEach(cat => {
                    const key = cat.id || cat.IdKategoriMateri || cat;
                    const sc = r.nilai[key] || [0, 0];
                    if ((sc[0] || 0) === 0 && (sc[1] || 0) === 0) {
                        doneAll = false;
                    }
                });
                if (doneAll) sudah++;
            });
            const belum = totalPeserta - sudah;
            const pct = totalPeserta > 0 ? Math.round((sudah / totalPeserta) * 100) : 0;
            const pctBelum = totalPeserta > 0 ? Math.round((belum / totalPeserta) * 100) : 0;
            $('#statTotalPeserta').text(totalPeserta);
            $('#statSudah').text(sudah);
            $('#barSudah').css('width', pct + '%');
            $('#descSudah').text(pct + '% selesai');
            $('#statBelum').text(belum);
            $('#barBelum').css('width', pctBelum + '%');
            $('#descBelum').text(pctBelum + '% pending');
            $('#statProgress').text(pct + '%');
            $('#barProgress').css('width', pct + '%');

            // Monitoring tambahan (ringkas)
            $('#monTotalJuri').text('≈2 per kategori');
            $('#monTotalTpq').text($('#filterTpq option').length - 1);
            $('#monLastUpdate').text(new Date().toLocaleString());
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
        // Jika TPQ hanya satu, set otomatis dan kunci sebagai pra-munaqosah
        const $tpqSel = $('#filterTpq');
        const realTpqOptions = $tpqSel.find('option').filter(function() {
            return $(this).val() !== '0';
        });
        if (realTpqOptions.length === 1) {
            const onlyId = $(realTpqOptions[0]).val();
            $tpqSel.val(onlyId).prop('disabled', true);
            $('#filterTypeUjian').val('pra-munaqosah').prop('disabled', true);
        }

        $('#btnReload').on('click', loadMonitoring);
        $('#filterTypeUjian').on('change', loadMonitoring);
        $('#filterTpq').on('change', loadMonitoring);
        loadMonitoring();
    });
</script>
<?= $this->endSection(); ?>