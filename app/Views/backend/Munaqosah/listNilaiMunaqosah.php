<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Card Informasi Alur Proses -->
            <div class="col-12">
                <div class="card card-info collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Panduan Alur Proses Nilai Munaqosah
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                                <ol class="mb-4">
                                    <li class="mb-2">
                                        <strong>Filter Data Kelulusan:</strong>
                                        <ul class="mt-2">
                                            <li>Pilih <strong>Tahun Ajaran</strong> yang ingin dilihat data kelulusannya</li>
                                            <li>Pilih <strong>TPQ</strong> untuk memfilter berdasarkan TPQ tertentu (opsional, pilih "Semua TPQ" untuk semua)</li>
                                            <li>Pilih <strong>Type Ujian</strong> (Munaqosah/Pra-Munaqosah) - otomatis terisi untuk Operator/Kepala TPQ jika setting tidak aktif</li>
                                            <li>Klik tombol <span class="badge badge-primary"><i class="fas fa-sync-alt"></i> Muat</span> untuk memuat data kelulusan</li>
                                            <li>Data akan otomatis dimuat saat filter berubah</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Lihat Statistik Kelulusan:</strong>
                                        <ul class="mt-2">
                                            <li>Card statistik menampilkan informasi ringkas:
                                                <ul>
                                                    <li><span class="badge badge-info"><i class="fas fa-users"></i> Total Peserta:</span> Jumlah total peserta ujian</li>
                                                    <li><span class="badge badge-success"><i class="fas fa-check-circle"></i> Lulus:</span> Jumlah peserta yang lulus beserta persentasenya</li>
                                                    <li><span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Belum Lulus:</span> Jumlah peserta yang belum lulus beserta persentasenya</li>
                                                    <li><span class="badge badge-primary"><i class="fas fa-percentage"></i> Rata Nilai Bobot:</span> Rata-rata total bobot semua peserta</li>
                                                </ul>
                                            </li>
                                            <li>Progress bar menunjukkan persentase lulus dan belum lulus secara visual</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Lihat Tabel Kelulusan:</strong>
                                        <ul class="mt-2">
                                            <li>Tabel menampilkan data detail kelulusan dengan kolom:
                                                <ul>
                                                    <li><strong>No Peserta:</strong> Nomor peserta ujian</li>
                                                    <li><strong>Nama Santri:</strong> Nama lengkap peserta</li>
                                                    <li><strong>TPQ:</strong> Nama TPQ peserta</li>
                                                    <li><strong>Type:</strong> Type ujian (Munaqosah/Pra-Munaqosah)</li>
                                                    <li><strong>Thn:</strong> Tahun ajaran</li>
                                                    <li><strong>Kategori Materi:</strong> Kolom dinamis berdasarkan kategori materi ujian:
                                                        <ul>
                                                            <li><strong>Juri 1, Juri 2, dst:</strong> Nilai dari masing-masing juri (disembunyikan untuk Operator pada Type Munaqosah)</li>
                                                            <li><strong>Jml:</strong> Rata-rata nilai dari semua juri</li>
                                                            <li><strong>Bobot:</strong> Nilai bobot setelah dikalikan dengan persentase kategori</li>
                                                        </ul>
                                                    </li>
                                                    <li><strong>Total Bobot:</strong> Total nilai bobot dari semua kategori</li>
                                                    <li><strong>Status Kelulusan:</strong> Status lulus/tidak lulus dengan informasi nilai dan threshold</li>
                                                </ul>
                                            </li>
                                            <li>Nilai 0 akan ditandai dengan warna merah untuk perhatian</li>
                                            <li>Gunakan fitur search dan filter DataTable untuk mencari peserta tertentu</li>
                                        </ul>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Export Data:</strong>
                                        <ul class="mt-2">
                                            <li>Gunakan tombol export di toolbar DataTable:
                                                <ul>
                                                    <li><strong>Column Visibility:</strong> Tampilkan/sembunyikan kolom tertentu</li>
                                                    <li><strong>Excel:</strong> Export data ke file Excel</li>
                                                    <li><strong>Print:</strong> Print tabel kelulusan</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ol>

                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                                    <ul class="mb-0">
                                        <li><strong>Status Kelulusan:</strong>
                                            <ul>
                                                <li><span class="badge badge-success">Lulus (nilai / threshold):</span> Peserta lulus, nilai total bobot memenuhi atau melebihi threshold</li>
                                                <li><span class="badge badge-danger">Tidak Lulus (nilai / threshold):</span> Peserta tidak lulus, nilai total bobot di bawah threshold</li>
                                                <li>Hover pada badge untuk melihat selisih nilai</li>
                                            </ul>
                                        </li>
                                        <li><strong>Filter Berdasarkan Role:</strong>
                                            <ul>
                                                <li><strong>Admin:</strong> Dapat melihat semua TPQ dan Type Ujian, serta kolom juri individual</li>
                                                <li><strong>Operator/Kepala TPQ:</strong> Hanya melihat TPQ sendiri, kolom juri disembunyikan untuk Type Munaqosah</li>
                                            </ul>
                                        </li>
                                        <li><strong>Kolom Dinamis:</strong> Kolom kategori materi akan menyesuaikan dengan konfigurasi munaqosah yang aktif</li>
                                        <li><strong>Bobot Kategori:</strong> Setiap kategori memiliki persentase bobot yang ditampilkan di header (hanya untuk Admin)</li>
                                        <li><strong>Nilai Rata-rata:</strong> Nilai "Jml" adalah rata-rata dari semua juri, nilai 0 tidak dihitung</li>
                                        <li><strong>Sumber Bobot:</strong> Informasi sumber bobot ditampilkan di bawah tabel</li>
                                        <li><strong>Sorting:</strong> Tabel otomatis diurutkan berdasarkan Total Bobot (tertinggi ke terendah)</li>
                                        <li><strong>Scroll Horizontal:</strong> Gunakan scroll horizontal jika tabel terlalu lebar</li>
                                        <li><strong>Print PDF/Surat:</strong> Pastikan data sudah dimuat sebelum mencetak dokumen</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Nilai Munaqosah</h3>
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
                                    <?php if ($isAdmin || ($aktiveTombolKelulusan && ($isOperator || $isKepalaTpq))): ?>
                                        <option value="munaqosah">Munaqosah</option>
                                    <?php endif; ?>
                                    <option value="pra-munaqosah">Pra-Munaqosah</option>
                                </select>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReloadKelulusan" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Hidden input untuk role user -->
                        <input type="hidden" id="userRole" value="<?= (in_groups('Operator') || $isKepalaTpq || (!in_groups('Admin') && session()->get('IdTpq'))) ? 'operator' : 'admin' ?>">

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
        const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';
        const typeUjian = $('#filterTypeUjian').val() || 'munaqosah';

        // Operator/TPQ dan Type Ujian = munaqosah: sembunyikan kolom juri individual
        const hideJuriColumns = isOperator && typeUjian === 'munaqosah';

        let th1 = '<tr>' +
            '<th class="dt-left">No Peserta</th>' +
            '<th class="dt-left">Nama Santri</th>' +
            '<th class="dt-left">TPQ</th>' +
            '<th class="dt-center">Type</th>' +
            '<th class="dt-center">Thn</th>';

        headerCategories.forEach(cat => {
            const weight = cat.weight ? parseFloat(cat.weight) : 0;
            const weightLabel = (isAdmin && weight > 0) ? ` (${weight}% )` : '';
            const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
            // Kolom: Juri (maxJuri kolom jika tidak disembunyikan) + Jml + Bobot
            const colspan = hideJuriColumns ? 2 : (maxJuri + 2);
            th1 += `<th class="dt-center nowrap" colspan="${colspan}">${cat.name}${weightLabel}</th>`;
        });

        th1 += '<th class="dt-center">Total Bobot</th>' +
            '<th class="dt-center">Status Kelulusan</th>' +
            '</tr>';

        let th2 = '<tr>' +
            '<th></th><th></th><th></th><th></th><th></th>';
        headerCategories.forEach(cat => {
            const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
            if (!hideJuriColumns) {
                for (let i = 1; i <= maxJuri; i++) {
                    th2 += `<th class="dt-center">Juri ${i}</th>`;
                }
            }
            th2 += '<th class="dt-center">Jml</th>' +
                '<th class="dt-center">Bobot</th>';
        });
        th2 += '<th></th><th></th></tr>';

        $('#theadKelulusan').html(th1 + th2);
    }

    function buildKelulusanRows(categories, rows) {
        const headerCategories = categories || [];
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';
        const typeUjian = $('#filterTypeUjian').val() || 'munaqosah';

        // Operator/TPQ dan Type Ujian = munaqosah: sembunyikan kolom juri individual
        const hideJuriColumns = isOperator && typeUjian === 'munaqosah';

        const body = [];
        rows.forEach(row => {
            const params = new URLSearchParams({
                NoPeserta: row.NoPeserta || '',
                IdTahunAjaran: row.IdTahunAjaran || '',
                TypeUjian: row.TypeUjian || '',
                IdTpq: row.IdTpq || ''
            }).toString();

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
                `<td class="dt-center">${row.IdTahunAjaran || '-'}</td>`;

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

                // Generate kolom juri secara dinamis (hanya jika tidak disembunyikan)
                if (!hideJuriColumns) {
                    for (let i = 0; i < maxJuri; i++) {
                        const nilai = (scores[i] !== undefined && scores[i] !== null) ? scores[i] : 0;
                        tds += `<td class="dt-center">${fmtScore(nilai)}</td>`;
                    }
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

            // Hitung total kolom berdasarkan maxJuri per kategori dan kondisi hideJuriColumns
            const userRole = $('#userRole').val() || 'admin';
            const isOperator = userRole === 'operator';
            const typeUjian = $('#filterTypeUjian').val() || 'munaqosah';
            const hideJuriColumns = isOperator && typeUjian === 'munaqosah';

            let totalCols = 5; // No Peserta, Nama Santri, TPQ, Type, Thn
            categories.forEach(cat => {
                const maxJuri = (cat && cat.maxJuri) ? parseInt(cat.maxJuri) : 2;
                if (hideJuriColumns) {
                    totalCols += 2; // Hanya Jml + Bobot
                } else {
                    totalCols += maxJuri + 2; // Juri + Jml + Bobot
                }
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
                buttons: ['colvis', 'excel', 'print']
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
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';
        const aktiveTombolKelulusan = <?= ($aktiveTombolKelulusan ?? false) ? 'true' : 'false' ?>;
        const isAdmin = <?= ($isAdmin ?? false) ? 'true' : 'false' ?>;

        const $tpqSelect = $('#filterTpq');
        const $typeUjianSelect = $('#filterTypeUjian');

        // Cek apakah opsi munaqosah ada di dropdown
        const hasMunaqosahOption = $typeUjianSelect.find('option[value="munaqosah"]').length > 0;

        const nonZeroOptions = $tpqSelect.find('option').filter(function() {
            return $(this).val() !== '0';
        });

        if (nonZeroOptions.length === 1) {
            const onlyId = $(nonZeroOptions[0]).val();
            $tpqSelect.val(onlyId).prop('disabled', true);

            // Untuk Operator/TPQ, Type Ujian bisa diubah (tidak disabled)
            // Hanya Admin yang Type Ujian-nya dikunci jika TPQ hanya satu
            if (!isOperator && isAdmin) {
                // Admin: set default dan lock jika TPQ hanya satu
                $typeUjianSelect.val('pra-munaqosah').prop('disabled', true);
            } else if (isOperator) {
                // Operator/TPQ: set default berdasarkan ketersediaan opsi
                // Jika setting tidak aktif, pastikan menggunakan pra-munaqosah
                if (!hasMunaqosahOption || !aktiveTombolKelulusan) {
                    $typeUjianSelect.val('pra-munaqosah');
                }
            }
        } else {
            // Jika ada multiple TPQ, pastikan default sesuai dengan setting
            if (isOperator && (!hasMunaqosahOption || !aktiveTombolKelulusan)) {
                $typeUjianSelect.val('pra-munaqosah');
            }
        }

        $('#btnReloadKelulusan').on('click', loadKelulusan);
        $('#filterTpq').on('change', loadKelulusan);
        $('#filterTypeUjian').on('change', loadKelulusan);

        loadKelulusan();
    });
</script>
<?= $this->endSection(); ?>

