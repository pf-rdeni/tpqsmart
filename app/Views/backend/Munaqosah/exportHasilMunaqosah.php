<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Export Hasil Munaqosah</h3>
                        <div class="d-flex">
                            <div class="mr-2">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <select id="filterTahunAjaran" class="form-control form-control-sm">
                                    <?php if (!empty($tahunAjaranDropdown)) : foreach ($tahunAjaranDropdown as $tahun): ?>
                                            <option value="<?= esc($tahun) ?>" <?= ($tahun === $current_tahun_ajaran) ? 'selected' : '' ?>><?= esc($tahun) ?></option>
                                        <?php endforeach;
                                    else: ?>
                                        <option value="<?= esc($current_tahun_ajaran) ?>"><?= esc($current_tahun_ajaran) ?></option>
                                    <?php endif; ?>
                                </select>
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
                            <div class="mr-2">
                                <label class="mb-0 small">Status</label>
                                <select id="filterStatus" class="form-control form-control-sm">
                                    <option value="all">Semua Status</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="belum-lulus">Belum Lulus</option>
                                </select>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReloadExport" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
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

                        <div class="table-responsive" id="exportTableWrapper">
                            <table id="tblExport" class="table table-bordered table-striped" style="width:100%">
                                <thead id="theadExport"></thead>
                                <tbody id="tbodyExport"></tbody>
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

    /* Vertical text untuk header kolom nilai */
    .th-vertical {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        white-space: nowrap;
        height: 150px;
        min-width: 40px;
        vertical-align: middle;
        text-align: center;
    }

    .th-vertical th {
        padding: 10px 5px;
    }
</style>
<script>
    let exportTable = null;

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

    function formatDate(dateStr) {
        // Tanggal sudah diformat di controller menggunakan formatTanggalIndonesia
        // Jadi langsung return saja
        if (!dateStr || dateStr === '-' || dateStr === '') return '-';
        return dateStr;
    }

    function buildExportHeader(categories) {
        const headerCategories = categories || [];

        let th1 = '<tr>' +
            '<th class="dt-left">No Peserta</th>' +
            '<th class="dt-left">ID Santri</th>' +
            '<th class="dt-left">Nama Santri</th>' +
            '<th class="dt-center">Jenis Kelamin</th>' +
            '<th class="dt-left">Tanggal Lahir</th>' +
            '<th class="dt-left">Tempat Lahir</th>' +
            '<th class="dt-left">Nama Ayah</th>' +
            '<th class="dt-left">Nama TPQ</th>' +
            '<th class="dt-left">Kepala TPQ</th>' +
            '<th class="dt-left">Kelurahan/Desa</th>';

        // Urutan kategori yang diminta
        const categoryOrder = [
            'BACA QURAN',
            'QUR\'AN',
            'QURAN',
            'SURAH PENDEK',
            'PRAKTEK SHOLAT',
            'SHOLAT',
            'DOA',
            'TULIS AL-QURAN',
            'TULIS AL QURAN',
            'IMLA'
        ];

        // Filter dan gabungkan kategori SURAH PENDEK dan AYAT PILIHAN
        const categoryMap = new Map();
        let surahPendekCat = null;
        let ayatPilihanCat = null;

        headerCategories.forEach(cat => {
            const catName = (cat.name || '').toUpperCase();
            if (catName === 'SURAH PENDEK') {
                surahPendekCat = cat;
            } else if (catName === 'AYAT PILIHAN') {
                ayatPilihanCat = cat;
            } else {
                categoryMap.set(catName, cat);
            }
        });

        // Urutkan kategori sesuai urutan yang diminta
        const orderedCategories = [];
        const processedCategories = new Set();

        // Urutan yang diminta: BACA AL-QURAN, SURAH PENDEK, PRAKTEK SHOLAT, DOA, IMLA
        const displayOrder = [{
                keywords: ['QURAN', 'QUR\'AN'],
                label: 'Nilai Baca Al-Quran'
            },
            {
                keywords: ['SHOLAT', 'PRAKTEK'],
                label: 'Nilai Praktek Sholat'
            },
            {
                keywords: ['DOA'],
                label: 'Nilai Doa'
            },
            {
                keywords: ['TULIS', 'IMLA'],
                label: 'Nilai Imla'
            }
        ];

        // Cari dan urutkan kategori sesuai displayOrder
        displayOrder.forEach(orderItem => {
            for (const [catName, cat] of categoryMap.entries()) {
                if (processedCategories.has(catName)) continue;

                const matches = orderItem.keywords.some(keyword =>
                    catName.includes(keyword) || keyword.includes(catName)
                );

                if (matches) {
                    orderedCategories.push({
                        cat: cat,
                        label: orderItem.label
                    });
                    processedCategories.add(catName);
                    break; // Hanya ambil satu kategori yang cocok
                }
            }
        });

        // Tambahkan kategori yang belum diurutkan (jika ada)
        categoryMap.forEach((cat, catName) => {
            if (!processedCategories.has(catName)) {
                orderedCategories.push({
                    cat: cat,
                    label: `Nilai ${cat.name.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')}`
                });
            }
        });

        // Tampilkan Nilai Baca Al-Quran (dari orderedCategories)
        orderedCategories.forEach(item => {
            if (item.label === 'Nilai Baca Al-Quran') {
                th1 += `<th class="dt-center th-vertical">${item.label}</th>`;
            }
        });

        // Tampilkan Nilai Surah Pendek (gabungan dari SURAH PENDEK + AYAT PILIHAN) setelah BACA QURAN
        if (surahPendekCat || ayatPilihanCat) {
            th1 += `<th class="dt-center th-vertical">Nilai Surah Pendek</th>`;
        }

        // Tampilkan kategori lainnya sesuai urutan
        orderedCategories.forEach(item => {
            if (item.label !== 'Nilai Baca Al-Quran') {
                th1 += `<th class="dt-center th-vertical">${item.label}</th>`;
            }
        });

        // Tambahkan kolom Jumlah, Rata-Rata, Total Bobot, dan Status Kelulusan
        th1 += '<th class="dt-center th-vertical">Jumlah</th>' +
            '<th class="dt-center th-vertical">Rata-Rata</th>' +
            '<th class="dt-center th-vertical">Total Bobot</th>' +
            '<th class="dt-center th-vertical">Status Kelulusan</th>' +
            '</tr>';

        $('#theadExport').html(th1);
    }

    function buildExportRows(categories, rows) {
        const headerCategories = categories || [];

        const body = [];
        rows.forEach(row => {
            const totalWeighted = parseFloat(row.total_weighted ?? 0);
            const threshold = parseFloat(row.kelulusan_threshold ?? 0).toFixed(2);
            const status = row.kelulusan_status || '-';
            const diff = parseFloat(row.kelulusan_difference ?? 0).toFixed(2);
            const passed = !!row.kelulusan_met;
            const badgeClass = passed ? 'badge badge-success' : 'badge badge-danger';
            const badgeText = status; // Hanya tampilkan status tanpa nilai bobot

            // Format Jenis Kelamin
            const jenisKelamin = row.JenisKelamin || '-';
            const jenisKelaminText = jenisKelamin === 'L' ? 'Laki-laki' : (jenisKelamin === 'P' ? 'Perempuan' : jenisKelamin);

            let tds = `<td class="dt-left">${row.NoPeserta || '-'}</td>` +
                `<td class="dt-left">${row.IdSantri || '-'}</td>` +
                `<td class="dt-left">${row.NamaSantri || '-'}</td>` +
                `<td class="dt-center">${jenisKelaminText}</td>` +
                `<td class="dt-left">${formatDate(row.TanggalLahirSantri)}</td>` +
                `<td class="dt-left">${row.TempatLahirSantri || '-'}</td>` +
                `<td class="dt-left">${row.NamaAyah || '-'}</td>` +
                `<td class="dt-left">${row.NamaTpq || '-'}</td>` +
                `<td class="dt-left">${row.KepalaSekolah || '-'}</td>` +
                `<td class="dt-left">${row.KelurahanDesaTpq || '-'}</td>`;

            // Urutan kategori yang diminta
            const categoryOrder = [
                'BACA QURAN',
                'QUR\'AN',
                'QURAN',
                'SURAH PENDEK',
                'PRAKTEK SHOLAT',
                'SHOLAT',
                'DOA',
                'TULIS AL-QURAN',
                'TULIS AL QURAN',
                'IMLA'
            ];

            // Filter dan pisahkan kategori
            const categoryMap = new Map();
            let surahPendekCat = null;
            let ayatPilihanCat = null;

            headerCategories.forEach(cat => {
                const catName = (cat.name || '').toUpperCase();
                if (catName === 'SURAH PENDEK') {
                    surahPendekCat = cat;
                } else if (catName === 'AYAT PILIHAN') {
                    ayatPilihanCat = cat;
                } else {
                    categoryMap.set(catName, cat);
                }
            });

            // Urutan yang diminta: BACA AL-QURAN, SURAH PENDEK, PRAKTEK SHOLAT, DOA, IMLA
            const displayOrder = [{
                    keywords: ['QURAN', 'QUR\'AN'],
                    label: 'Nilai Baca Al-Quran'
                },
                {
                    keywords: ['SHOLAT', 'PRAKTEK'],
                    label: 'Nilai Praktek Sholat'
                },
                {
                    keywords: ['DOA'],
                    label: 'Nilai Doa'
                },
                {
                    keywords: ['TULIS', 'IMLA'],
                    label: 'Nilai Imla'
                }
            ];

            // Urutkan kategori sesuai urutan yang diminta
            const orderedCategories = [];
            const processedCategories = new Set();

            // Cari dan urutkan kategori sesuai displayOrder
            displayOrder.forEach(orderItem => {
                for (const [catName, cat] of categoryMap.entries()) {
                    if (processedCategories.has(catName)) continue;

                    const matches = orderItem.keywords.some(keyword =>
                        catName.includes(keyword) || keyword.includes(catName)
                    );

                    if (matches) {
                        orderedCategories.push({
                            cat: cat,
                            label: orderItem.label
                        });
                        processedCategories.add(catName);
                        break; // Hanya ambil satu kategori yang cocok
                    }
                }
            });

            // Tambahkan kategori yang belum diurutkan (jika ada)
            categoryMap.forEach((cat, catName) => {
                if (!processedCategories.has(catName)) {
                    orderedCategories.push({
                        cat: cat,
                        label: `Nilai ${cat.name.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ')}`
                    });
                }
            });

            // Array untuk menyimpan nilai yang ditampilkan (untuk menghitung jumlah dan rata-rata)
            const displayedValues = [];

            // Tampilkan Nilai Baca Al-Quran (dari orderedCategories)
            orderedCategories.forEach(item => {
                if (item.label === 'Nilai Baca Al-Quran') {
                    const catId = item.cat.id || item.cat.IdKategoriMateri;
                    const scores = row.nilai && row.nilai[catId] ? row.nilai[catId] : [];
                    const validScores = scores.filter(s => s > 0);
                    let avg = row.averages && row.averages[catId] !== undefined ? row.averages[catId] : 0;
                    if (validScores.length === 1 && avg !== validScores[0]) {
                        avg = validScores[0];
                    }
                    const avgValue = parseFloat(avg) || 0;
                    displayedValues.push(avgValue);
                    tds += `<td class="dt-center">${fmtDecimal(avg)}</td>`;
                }
            });

            // Tampilkan Nilai Surah Pendek (gabungan dari SURAH PENDEK + AYAT PILIHAN) setelah BACA QURAN
            if (surahPendekCat || ayatPilihanCat) {
                let surahPendekAvg = 0;
                let ayatPilihanAvg = 0;

                if (surahPendekCat) {
                    const surahPendekId = surahPendekCat.id || surahPendekCat.IdKategoriMateri;
                    surahPendekAvg = row.averages && row.averages[surahPendekId] !== undefined ? parseFloat(row.averages[surahPendekId]) : 0;
                }

                if (ayatPilihanCat) {
                    const ayatPilihanId = ayatPilihanCat.id || ayatPilihanCat.IdKategoriMateri;
                    ayatPilihanAvg = row.averages && row.averages[ayatPilihanId] !== undefined ? parseFloat(row.averages[ayatPilihanId]) : 0;
                }

                // Gabungkan dan bagi dua (hanya untuk tampilan)
                const combinedAvg = (surahPendekAvg + ayatPilihanAvg) / 2;
                displayedValues.push(combinedAvg);
                tds += `<td class="dt-center">${fmtDecimal(combinedAvg)}</td>`;
            }

            // Tampilkan kategori lainnya sesuai urutan
            orderedCategories.forEach(item => {
                if (item.label !== 'Nilai Baca Al-Quran') {
                    const catId = item.cat.id || item.cat.IdKategoriMateri;
                    const scores = row.nilai && row.nilai[catId] ? row.nilai[catId] : [];
                    const validScores = scores.filter(s => s > 0);
                    let avg = row.averages && row.averages[catId] !== undefined ? row.averages[catId] : 0;
                    if (validScores.length === 1 && avg !== validScores[0]) {
                        avg = validScores[0];
                    }
                    const avgValue = parseFloat(avg) || 0;
                    displayedValues.push(avgValue);
                    tds += `<td class="dt-center">${fmtDecimal(avg)}</td>`;
                }
            });

            // Hitung Jumlah dan Rata-Rata dari nilai yang ditampilkan
            const jumlah = displayedValues.reduce((sum, val) => sum + val, 0);
            const rataRata = displayedValues.length > 0 ? jumlah / displayedValues.length : 0;

            // Tambahkan kolom Jumlah, Rata-Rata, Total Bobot, dan Status Kelulusan
            tds += `<td class="dt-center dt-right">${fmtDecimal(jumlah)}</td>` +
                `<td class="dt-center dt-right">${fmtDecimal(rataRata)}</td>` +
                `<td class="dt-center dt-right">${fmtDecimal(totalWeighted)}</td>` +
                `<td class="dt-center" data-order="${passed ? 1 : 0}"><span class="badge badge-status ${badgeClass}" title="Total Bobot: ${fmtDecimal(totalWeighted)} / ${threshold}">${badgeText}</span></td>`;

            body.push(`<tr>${tds}</tr>`);
        });

        $('#tbodyExport').html(body.join(''));
    }

    function loadExport() {
        // Ambil tahun ajaran dari filter, fallback ke tahun ajaran saat ini
        let tahun = $('#filterTahunAjaran').val() || '';
        const currentTahunAjaran = '<?= esc($current_tahun_ajaran) ?>';

        // Jika tahun ajaran kosong, gunakan tahun ajaran saat ini sebagai fallback
        if (!tahun && currentTahunAjaran) {
            tahun = currentTahunAjaran;
            $('#filterTahunAjaran').val(tahun);
        }

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

        const url = '<?= base_url('backend/munaqosah/export-hasil-munaqosah-data') ?>' + `?IdTahunAjaran=${encodeURIComponent(tahun)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(type)}`;

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
            let rows = data.rows || [];

            // Filter berdasarkan status
            const statusFilter = $('#filterStatus').val();
            if (statusFilter !== 'all') {
                rows = rows.filter(row => {
                    const status = (row.kelulusan_status || '').toLowerCase();
                    if (statusFilter === 'lulus') {
                        return status === 'lulus';
                    } else if (statusFilter === 'belum-lulus') {
                        return status === 'belum lulus';
                    }
                    return true;
                });
            }

            if (exportTable) {
                exportTable.destroy();
                exportTable = null;
            }

            $('#exportTableWrapper').html(
                '<table id="tblExport" class="table table-bordered table-striped" style="width:100%">' +
                '<thead id="theadExport"></thead>' +
                '<tbody id="tbodyExport"></tbody>' +
                '</table>'
            );

            buildExportHeader(categories);
            buildExportRows(categories, rows);

            // Hitung total kolom: No Peserta, ID Santri, Nama Santri, Jenis Kelamin, Tanggal Lahir, Tempat Lahir, Nama Ayah, Nama TPQ, Kepala TPQ, Kelurahan/Desa (10 kolom)
            // + Kategori (termasuk SURAH PENDEK gabungan) + Jumlah + Rata-Rata + Total Bobot + Status Kelulusan
            let totalCols = 10; // No Peserta, ID Santri, Nama Santri, Jenis Kelamin, Tanggal Lahir, Tempat Lahir, Nama Ayah, Nama TPQ, Kepala TPQ, Kelurahan/Desa

            // Hitung kategori (termasuk SURAH PENDEK jika ada)
            let categoryCount = categories.length;
            const hasSurahPendek = categories.some(cat => (cat.name || '').toUpperCase() === 'SURAH PENDEK');
            const hasAyatPilihan = categories.some(cat => (cat.name || '').toUpperCase() === 'AYAT PILIHAN');
            if (hasSurahPendek && hasAyatPilihan) {
                categoryCount = categoryCount - 1; // Kurangi 1 karena AYAT PILIHAN digabung dengan SURAH PENDEK
            }

            totalCols += categoryCount; // Kategori
            totalCols += 4; // Jumlah + Rata-Rata + Total Bobot + Status Kelulusan

            const jumlahIndex = totalCols - 4; // Kolom Jumlah
            const rataRataIndex = totalCols - 3; // Kolom Rata-Rata
            const totalBobotIndex = totalCols - 2; // Kolom Total Bobot
            const statusIndex = totalCols - 1; // Kolom Status Kelulusan

            exportTable = $('#tblExport').DataTable({
                scrollX: true,
                order: [
                    [rataRataIndex, 'desc']
                ],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    'colvis',
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible',
                            modifier: {
                                search: 'applied',
                                order: 'applied'
                            },
                            format: {
                                body: function(data, row, column, node) {
                                    // Hapus tag HTML dari data (seperti badge, strong, dll)
                                    if (node) {
                                        var text = $(node).text();
                                        return text || data;
                                    }
                                    return data;
                                }
                            }
                        }
                    },
                    'print'
                ],
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

    // Fungsi untuk menyimpan filter ke localStorage
    function saveFiltersToLocalStorage() {
        const filters = {
            tahunAjaran: $('#filterTahunAjaran').val(),
            tpq: $('#filterTpq').val(),
            typeUjian: $('#filterTypeUjian').val(),
            status: $('#filterStatus').val()
        };
        localStorage.setItem('exportHasilMunaqosah_filters', JSON.stringify(filters));
    }

    // Fungsi untuk memuat filter dari localStorage
    function loadFiltersFromLocalStorage() {
        try {
            const saved = localStorage.getItem('exportHasilMunaqosah_filters');
            if (saved) {
                const filters = JSON.parse(saved);
                return filters;
            }
        } catch (e) {
            console.error('Error loading filters from localStorage:', e);
        }
        return null;
    }

    $(function() {
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';
        const aktiveTombolKelulusan = <?= ($aktiveTombolKelulusan ?? false) ? 'true' : 'false' ?>;
        const isAdmin = <?= ($isAdmin ?? false) ? 'true' : 'false' ?>;

        const $tahunAjaranInput = $('#filterTahunAjaran');
        const $tpqSelect = $('#filterTpq');
        const $typeUjianSelect = $('#filterTypeUjian');

        // Cek apakah opsi munaqosah ada di dropdown
        const hasMunaqosahOption = $typeUjianSelect.find('option[value="munaqosah"]').length > 0;

        // Memuat filter dari localStorage
        const savedFilters = loadFiltersFromLocalStorage();

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
                if (!savedFilters || !savedFilters.typeUjian) {
                    $typeUjianSelect.val('pra-munaqosah').prop('disabled', true);
                } else {
                    $typeUjianSelect.val(savedFilters.typeUjian).prop('disabled', true);
                }
            } else if (isOperator) {
                // Operator/TPQ: set default berdasarkan ketersediaan opsi atau dari localStorage
                if (savedFilters && savedFilters.typeUjian) {
                    $typeUjianSelect.val(savedFilters.typeUjian);
                } else if (!hasMunaqosahOption || !aktiveTombolKelulusan) {
                    $typeUjianSelect.val('pra-munaqosah');
                }
            }
        } else {
            // Jika ada multiple TPQ, terapkan filter dari localStorage jika ada
            if (savedFilters) {
                if (savedFilters.tpq) {
                    // Cek apakah nilai TPQ yang disimpan masih valid
                    const optionExists = $tpqSelect.find(`option[value="${savedFilters.tpq}"]`).length > 0;
                    if (optionExists) {
                        $tpqSelect.val(savedFilters.tpq);
                    }
                }
                if (savedFilters.typeUjian) {
                    // Cek apakah nilai Type Ujian yang disimpan masih valid
                    const optionExists = $typeUjianSelect.find(`option[value="${savedFilters.typeUjian}"]`).length > 0;
                    if (optionExists) {
                        $typeUjianSelect.val(savedFilters.typeUjian);
                    } else if (isOperator && (!hasMunaqosahOption || !aktiveTombolKelulusan)) {
                        $typeUjianSelect.val('pra-munaqosah');
                    }
                } else if (isOperator && (!hasMunaqosahOption || !aktiveTombolKelulusan)) {
                    $typeUjianSelect.val('pra-munaqosah');
                }

            } else {
                // Jika ada multiple TPQ, pastikan default sesuai dengan setting
                if (isOperator && (!hasMunaqosahOption || !aktiveTombolKelulusan)) {
                    $typeUjianSelect.val('pra-munaqosah');
                }
            }
        }

        // Terapkan filter Tahun Ajaran dari localStorage jika ada, atau gunakan tahun ajaran saat ini sebagai fallback
        const currentTahunAjaran = '<?= esc($current_tahun_ajaran) ?>';
        if (savedFilters && savedFilters.tahunAjaran) {
            // Cek apakah tahun ajaran dari localStorage masih valid (ada di dropdown)
            const optionExists = $tahunAjaranInput.find(`option[value="${savedFilters.tahunAjaran}"]`).length > 0;
            if (optionExists) {
                $tahunAjaranInput.val(savedFilters.tahunAjaran);
            } else if (currentTahunAjaran) {
                // Jika tidak valid, gunakan tahun ajaran saat ini
                $tahunAjaranInput.val(currentTahunAjaran);
            }
        } else if (currentTahunAjaran) {
            // Jika tidak ada di localStorage, gunakan tahun ajaran saat ini
            $tahunAjaranInput.val(currentTahunAjaran);
        }

        // Load filter status dari localStorage jika ada
        if (savedFilters && savedFilters.status) {
            $('#filterStatus').val(savedFilters.status);
        }

        // Event listener untuk menyimpan filter saat berubah dan memuat data
        $tahunAjaranInput.on('change', function() {
            saveFiltersToLocalStorage();
            loadExport();
        });

        $tpqSelect.on('change', function() {
            saveFiltersToLocalStorage();
            loadExport();
        });

        $typeUjianSelect.on('change', function() {
            saveFiltersToLocalStorage();
            loadExport();
        });

        // Event listener untuk filter status
        $('#filterStatus').on('change', function() {
            saveFiltersToLocalStorage();
            // Reload data dengan filter status yang baru
            loadExport();
        });

        $('#btnReloadExport').on('click', function() {
            saveFiltersToLocalStorage();
            loadExport();
        });

        // Load data saat halaman dimuat
        loadExport();
    });
</script>
<?= $this->endSection(); ?>