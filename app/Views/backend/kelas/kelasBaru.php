<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">

    <!-- Informasi Proses Flow -->
    <div class="card card-info card-outline collapsed-card mb-3">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Informasi Proses Registrasi Santri Baru
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <h5><i class="fas fa-list-ol"></i> Cara Menggunakan:</h5>
                    <ol class="mb-3">
                        <li class="mb-2">
                            <strong>Filter Data (Opsional)</strong>
                            <ul class="mt-1">
                                <li>Gunakan filter di bagian atas untuk mengelompokkan data: Tahun Ajaran, TPQ, Kelas, atau Jenis Kelamin</li>
                                <li>Jika Anda Operator/Kepala TPQ, filter TPQ sudah otomatis ter-set</li>
                                <li>Lihat statistik di filter card untuk melihat jumlah data yang difilter</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Pilih Santri</strong>
                            <ul class="mt-1">
                                <li>Centang checkbox di baris santri yang ingin diproses</li>
                                <li>Atau gunakan checkbox <strong>"Pilih Semua"</strong> di header untuk memilih semua santri di seluruh halaman</li>
                                <li>Lihat counter <strong>"Terpilih"</strong> untuk mengetahui jumlah santri yang dipilih</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Periksa/Koreksi Kelas</strong>
                            <ul class="mt-1">
                                <li>Lihat kolom <strong>"Kelas Diajukan"</strong> - jika sudah benar, tidak perlu diubah</li>
                                <li>Jika perlu koreksi, pilih kelas yang benar di dropdown <strong>"Kelas Koreksi"</strong></li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Register</strong>
                            <ul class="mt-1">
                                <li>Klik tombol <strong>"Register"</strong> di bagian bawah</li>
                                <li>Konfirmasi jumlah santri yang akan diproses</li>
                                <li>Sistem akan memproses santri yang dipilih dan mengaktifkannya di kelas yang ditentukan</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="alert alert-info mb-3">
                        <h6><i class="icon fas fa-lightbulb"></i> Tips:</h6>
                        <ul class="mb-0 small">
                            <li>Anda bisa memproses beberapa santri sekaligus atau semua sekaligus</li>
                            <li>Santri yang sudah diproses akan hilang dari halaman ini (karena sudah masuk kelas)</li>
                            <li>Data nilai semester Ganjil dan Genap akan otomatis dibuat (kosong, siap diisi)</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <h6><i class="icon fas fa-exclamation-triangle"></i> Catatan:</h6>
                        <ul class="mb-0 small">
                            <li>Hanya menampilkan santri yang <strong>belum masuk kelas</strong></li>
                            <li>Setelah disimpan, santri akan <strong>langsung aktif</strong> di sistem</li>
                            <li>Pastikan materi pelajaran untuk setiap kelas sudah dikonfigurasi</li>
                            <li>Akses terbatas untuk <strong>Admin</strong> dan <strong>Operator</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Hitung statistik dari data santri
    $totalSantri = count($dataSantri);
    $statistikKelas = [];
    $statistikTpq = [];
    $statistikJenisKelamin = ['Laki-laki' => 0, 'Perempuan' => 0];
    $uniqueKelas = [];
    $uniqueTpq = [];

    foreach ($dataSantri as $santri) {
        // Statistik per kelas
        $namaKelas = $santri['NamaKelas'] ?? 'Tidak Diketahui';
        if (!isset($statistikKelas[$namaKelas])) {
            $statistikKelas[$namaKelas] = 0;
            $uniqueKelas[$santri['IdKelas']] = $namaKelas;
        }
        $statistikKelas[$namaKelas]++;

        // Statistik per TPQ
        $namaTpq = $santri['NamaTpq'] ?? 'Tidak Diketahui';
        if (!isset($statistikTpq[$namaTpq])) {
            $statistikTpq[$namaTpq] = 0;
            $uniqueTpq[$santri['IdTpq']] = $namaTpq;
        }
        $statistikTpq[$namaTpq]++;

        // Statistik jenis kelamin
        $jenisKelamin = $santri['JenisKelamin'] ?? '';
        if (stripos($jenisKelamin, 'Laki') !== false || stripos($jenisKelamin, 'L') !== false) {
            $statistikJenisKelamin['Laki-laki']++;
        } elseif (stripos($jenisKelamin, 'Perempuan') !== false || stripos($jenisKelamin, 'P') !== false) {
            $statistikJenisKelamin['Perempuan']++;
        }
    }
    ?>

    <!-- Filter Card dengan Informasi Statistik -->
    <div class="card card-primary card-outline mb-3">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filter & Informasi Data Santri Baru
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Statistik Overview -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Santri Baru</span>
                            <span class="info-box-number" id="totalSantriCount"><?= $totalSantri ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-male"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Laki-laki</span>
                            <span class="info-box-number" id="lakiLakiCount"><?= $statistikJenisKelamin['Laki-laki'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-box bg-gradient-pink">
                        <span class="info-box-icon"><i class="fas fa-female"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Perempuan</span>
                            <span class="info-box-number" id="perempuanCount"><?= $statistikJenisKelamin['Perempuan'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-check-square"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Terpilih</span>
                            <span class="info-box-number" id="selectedCount">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filterTahunAjaran">Filter Tahun Ajaran</label>
                        <select class="form-control select2" id="filterTahunAjaran" name="filterTahunAjaran" style="width: 100%;">
                            <option value="">Semua Tahun Ajaran</option>
                            <?php
                            if (isset($tahunAjaranList) && !empty($tahunAjaranList)):
                                foreach ($tahunAjaranList as $ta):
                                    // Convert tahun ajaran format (contoh: 20252026 menjadi 2025/2026)
                                    $taFormatted = strlen($ta) == 8 ? substr($ta, 0, 4) . '/' . substr($ta, 4) : $ta;

                                    // Default pilih tahun ajaran saat ini
                                    $isSelected = ($ta == $tahunAjaranSaatIni) ? 'selected' : '';

                                    // Label khusus untuk tahun ajaran saat ini dan berikutnya
                                    $label = $taFormatted;
                                    if (isset($tahunAjaranSaatIni) && $ta == $tahunAjaranSaatIni) {
                                        $label = $taFormatted . ' (Saat Ini)';
                                    } elseif (isset($tahunAjaranBerikutnya) && $ta == $tahunAjaranBerikutnya) {
                                        $label = $taFormatted . ' (Berikutnya)';
                                    }
                            ?>
                                    <option value="<?= $ta ?>" <?= $isSelected ?>><?= esc($label) ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filterTpq">Filter TPQ</label>
                        <select class="form-control select2" id="filterTpq" name="filterTpq" style="width: 100%;">
                            <option value="">Semua TPQ</option>
                            <?php
                            $userTpqSelected = false;
                            foreach ($uniqueTpq as $idTpq => $namaTpq):
                                // Auto-select TPQ jika user login dengan IdTpq tertentu (bukan admin)
                                $isSelected = '';
                                if (isset($IdTpq) && $IdTpq > 0 && $IdTpq == $idTpq) {
                                    $isSelected = 'selected';
                                    $userTpqSelected = true;
                                }
                            ?>
                                <option value="<?= $idTpq ?>" <?= $isSelected ?>><?= esc($namaTpq) ?> (<?= $statistikTpq[$namaTpq] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="userTpqId" value="<?= isset($IdTpq) && $IdTpq > 0 ? $IdTpq : '' ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filterKelas">Filter Kelas</label>
                        <select class="form-control select2" id="filterKelas" name="filterKelas" style="width: 100%;">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($uniqueKelas as $idKelas => $namaKelas): ?>
                                <option value="<?= $idKelas ?>"><?= esc($namaKelas) ?> (<?= $statistikKelas[$namaKelas] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filterJenisKelamin">Filter Jenis Kelamin</label>
                        <select class="form-control" id="filterJenisKelamin" name="filterJenisKelamin" style="width: 100%;">
                            <option value="">Semua</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-info" id="btnResetFilter">
                            <i class="fas fa-redo"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Checkbox Actions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-success" id="btnSelectAll">
                            <i class="fas fa-check-double"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="btnDeselectAll">
                            <i class="fas fa-times"></i> Batal Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="btnSelectFiltered">
                            <i class="fas fa-check"></i> Pilih yang Difilter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set Santri Baru TPQ <?= $dataTpq[0]['NamaTpq'] ?></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('backend/kelas/setKelasSantriBaru') ?>" method="POST" id="formSetKelas">
                <table id="kenaikanKelas" class="table table-bordered table-striped">
                    <?php
                    $tableHeaders = '
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="checkAll" title="Pilih Semua">
                        </th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Ayah</th>
                        <th>Kelas Diajukan</th>
                        <th>Kelas Koreksi</th>
                        <th>Nama TPQ</th>
                        <th>Nama Kel/Desa</th>
                    </tr>
                ';
                    ?>
                    <thead>
                        <?= $tableHeaders ?>
                    </thead>
                    <tbody>
                        <?php foreach ($dataSantri as $santri) : ?>
                            <tr data-kelas="<?= $santri['IdKelas'] ?>" data-tpq="<?= $santri['IdTpq'] ?>" data-jenis-kelamin="<?= esc(strtolower($santri['JenisKelamin'])) ?>">
                                <td class="text-center">
                                    <input type="checkbox"
                                        name="selectedSantri[]"
                                        value="<?= $santri['IdSantri'] ?>"
                                        class="checkbox-santri"
                                        data-id-santri="<?= $santri['IdSantri'] ?>"
                                        checked>
                                </td>
                                <td><?= esc($santri['NamaSantri']); ?></td>
                                <td><?= esc($santri['JenisKelamin']); ?></td>
                                <td><?= esc($santri['NamaAyah']); ?></td>
                                <td><?= esc($santri['NamaKelas']); ?></td>
                                <td>
                                    <input type="hidden" name="IdTpq[<?= $santri['IdSantri']; ?>]" value="<?= $santri['IdTpq']; ?>">
                                    <select name="IdKelas[<?= $santri['IdSantri']; ?>]" class="form-control select2" style="width: 100%;" required>
                                        <option value="" disabled>Pilih kelas</option>
                                        <?php
                                        foreach ($dataKelas as $kelas): ?>
                                            <option value="<?= $kelas['IdKelas'] ?>"
                                                <?= ($kelas['NamaKelas'] == $santri['NamaKelas']) ? 'selected' : '' ?>>
                                                <?= $kelas['NamaKelas'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= esc($santri['NamaTpq']); ?></td>
                                <td><?= esc($santri['NamaKelDesa']); ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <?= $tableHeaders ?>
                    </tfoot>
                </table>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Register <span id="submitCount">0</span>
                    </button>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    let table;

    $(document).ready(function() {
        // Initialize DataTable with custom options
        table = $('#kenaikanKelas').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 25,
            "order": [
                [1, "asc"]
            ], // Sort by Nama Santri column
            "columnDefs": [{
                "targets": 0, // Checkbox column
                "orderable": false,
                "searchable": false
            }],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });

        // Initialize Select2 for dropdowns
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Update counter
        function updateCounters() {
            // Get all filtered rows from DataTable (all pages)
            const allFilteredRows = getAllFilteredRows();
            let totalFiltered = 0;
            let totalChecked = 0;
            let lakiLaki = 0;
            let perempuan = 0;

            // Count all filtered rows (all pages)
            allFilteredRows.each(function() {
                totalFiltered++;

                const checkbox = $(this).find('.checkbox-santri');
                if (checkbox.is(':checked')) {
                    totalChecked++;
                }

                // Count jenis kelamin
                const jenisKelamin = $(this).find('td:eq(2)').text().toLowerCase().trim();
                if (jenisKelamin.includes('laki') || jenisKelamin === 'l' || jenisKelamin.startsWith('l')) {
                    lakiLaki++;
                } else if (jenisKelamin.includes('perempuan') || jenisKelamin === 'p' || jenisKelamin.startsWith('p')) {
                    perempuan++;
                }
            });

            // Update counters
            $('#selectedCount').text(totalChecked);
            $('#submitCount').text(totalChecked);
            $('#totalSantriCount').text(totalFiltered);
            $('#lakiLakiCount').text(lakiLaki);
            $('#perempuanCount').text(perempuan);
        }

        // Custom filter function
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // Only apply to our table
                if (settings.nTable.id !== 'kenaikanKelas') {
                    return true;
                }

                const row = table.row(dataIndex).node();
                if (!row) return true;

                const filterTahunAjaran = $('#filterTahunAjaran').val();
                const filterTpq = $('#filterTpq').val();
                const filterKelas = $('#filterKelas').val();
                const filterJenisKelamin = $('#filterJenisKelamin').val().toLowerCase();

                const rowKelas = $(row).data('kelas');
                const rowTpq = $(row).data('tpq');
                const rowTahunAjaran = $(row).data('tahun-ajaran'); // Untuk masa depan jika ada field tahun ajaran
                const jenisKelaminText = $(row).find('td:eq(2)').text().toLowerCase();

                // Filter tahun ajaran (untuk persiapan di masa depan jika data santri baru memiliki tahun ajaran)
                // Saat ini, semua santri baru akan masuk ke tahun ajaran saat ini, jadi filter ini tidak aktif
                if (filterTahunAjaran && rowTahunAjaran && rowTahunAjaran != filterTahunAjaran) {
                    return false;
                }

                // Filter TPQ
                if (filterTpq && rowTpq != filterTpq) {
                    return false;
                }

                // Filter kelas
                if (filterKelas && rowKelas != filterKelas) {
                    return false;
                }

                // Filter jenis kelamin
                if (filterJenisKelamin) {
                    if (filterJenisKelamin === 'laki-laki') {
                        if (!jenisKelaminText.includes('laki') && jenisKelaminText !== 'l') {
                            return false;
                        }
                    } else if (filterJenisKelamin === 'perempuan') {
                        if (!jenisKelaminText.includes('perempuan') && jenisKelaminText !== 'p') {
                            return false;
                        }
                    }
                }

                return true;
            }
        );

        // Apply filters function
        function applyFilters() {
            table.draw();
            updateCounters();
        }

        // Remove all filters
        function removeFilters() {
            // Reset filter tahun ajaran ke default (tahun ajaran saat ini)
            const tahunAjaranSaatIni = '<?= isset($tahunAjaranSaatIni) ? $tahunAjaranSaatIni : '' ?>';
            if (tahunAjaranSaatIni) {
                $('#filterTahunAjaran').val(tahunAjaranSaatIni).trigger('change.select2');
            } else {
                $('#filterTahunAjaran').val('').trigger('change.select2');
            }

            // Reset filter TPQ, tapi jika user login dengan IdTpq tertentu, tetap set ke TPQ-nya
            const userTpqId = $('#userTpqId').val();
            if (userTpqId && userTpqId !== '' && userTpqId !== '0') {
                $('#filterTpq').val(userTpqId).trigger('change.select2');
            } else {
                $('#filterTpq').val('').trigger('change.select2');
            }

            $('#filterKelas').val('').trigger('change.select2');
            $('#filterJenisKelamin').val('');
            table.draw();
            updateCounters();
        }

        // Auto-set filter dan trigger initial filter
        // Filter tahun ajaran default (tahun ajaran saat ini) sudah ter-select dari server side
        // Filter TPQ juga sudah ter-select jika user login dengan IdTpq tertentu
        setTimeout(function() {
            applyFilters();
        }, 500);

        // Filter event handlers
        $('#filterTahunAjaran, #filterTpq, #filterKelas, #filterJenisKelamin').on('change', function() {
            applyFilters();
        });

        // Reset filter
        $('#btnResetFilter').on('click', function() {
            removeFilters();
        });

        // Helper function to get all filtered rows (all pages)
        function getAllFilteredRows() {
            // Menggunakan DataTable API untuk mendapatkan semua rows yang sesuai filter (semua halaman)
            return table.rows({
                search: 'applied'
            }).nodes().to$();
        }

        // Helper function to get visible rows (current page only)
        function getVisibleRows() {
            const visibleRows = table.rows({
                search: 'applied'
            }).nodes();
            return $(visibleRows).filter(':visible');
        }

        // Function to select/deselect all checkboxes in all pages
        function selectAllCheckboxesInAllPages(isChecked) {
            // Pilih semua checkbox di semua halaman yang sesuai filter
            getAllFilteredRows().find('.checkbox-santri').prop('checked', isChecked);
        }

        // Check all checkbox - select all pages
        $('#checkAll').on('change', function() {
            const isChecked = $(this).is(':checked');
            // Pilih semua di semua halaman
            selectAllCheckboxesInAllPages(isChecked);
            updateCounters();
        });

        // Select all button - select all filtered rows in all pages
        $('#btnSelectAll').on('click', function() {
            // Pilih semua checkbox di semua halaman yang sesuai filter
            selectAllCheckboxesInAllPages(true);
            $('#checkAll').prop('checked', true);
            updateCounters();
        });

        // Deselect all button - uncheck all in all pages
        $('#btnDeselectAll').on('click', function() {
            // Hapus centang semua checkbox di semua halaman
            selectAllCheckboxesInAllPages(false);
            $('#checkAll').prop('checked', false);
            updateCounters();
        });

        // Select filtered button - select all filtered rows in all pages
        $('#btnSelectFiltered').on('click', function() {
            // Pilih semua checkbox di semua halaman yang sesuai filter
            selectAllCheckboxesInAllPages(true);
            $('#checkAll').prop('checked', true);
            updateCounters();
        });

        // Individual checkbox change
        $(document).on('change', '.checkbox-santri', function() {
            updateCounters();

            // Update checkAll checkbox state based on all filtered rows (all pages)
            const allFilteredCheckboxes = getAllFilteredRows().find('.checkbox-santri');
            const totalFiltered = allFilteredCheckboxes.length;
            const totalChecked = allFilteredCheckboxes.filter(':checked').length;

            // Update checkAll state: checked jika semua checkbox di semua halaman tercentang
            $('#checkAll').prop('checked', totalFiltered > 0 && totalChecked === totalFiltered);
        });

        // Form submission - only process checked items
        $('#formSetKelas').on('submit', function(e) {
            e.preventDefault();

            // Count all checked checkboxes (not just visible)
            const checkedBoxes = $('.checkbox-santri:checked');

            if (checkedBoxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu santri untuk diproses!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }

            // Collect information about selected santri
            let selectedSantriInfo = [];
            let totalWithKelas = 0;
            let totalWithoutKelas = 0;

            checkedBoxes.each(function() {
                const row = $(this).closest('tr');
                const namaSantri = row.find('td:eq(1)').text().trim();
                const kelasDiajukan = row.find('td:eq(4)').text().trim();
                const kelasKoreksi = row.find('select[name*="IdKelas"]').val();
                const kelasKoreksiText = row.find('select[name*="IdKelas"] option:selected').text();

                if (!kelasKoreksi || kelasKoreksi === '') {
                    totalWithoutKelas++;
                } else {
                    totalWithKelas++;
                }
            });

            // Build confirmation message
            let confirmHtml = '<div class="text-left">';
            confirmHtml += '<p class="mb-3"><strong>Jumlah santri yang akan diproses: ' + checkedBoxes.length + '</strong></p>';

            if (totalWithoutKelas > 0) {
                confirmHtml += '<div class="alert alert-danger mb-3" style="padding: 10px; font-size: 0.9rem;">';
                confirmHtml += '<i class="fas fa-exclamation-triangle"></i> <strong>Peringatan:</strong> Ada ' + totalWithoutKelas + ' santri yang belum memiliki kelas yang dipilih!';
                confirmHtml += '</div>';
            }

            confirmHtml += '<ul class="mb-0" style="max-height: 200px; overflow-y: auto;">';

            // Show first 5 selected santri as example
            let count = 0;
            checkedBoxes.each(function() {
                if (count < 5) {
                    const row = $(this).closest('tr');
                    const namaSantri = row.find('td:eq(1)').text().trim();
                    const kelasKoreksiText = row.find('select[name*="IdKelas"] option:selected').text() || 'Belum dipilih';
                    confirmHtml += '<li>' + namaSantri + ' → Kelas: ' + kelasKoreksiText + '</li>';
                    count++;
                }
            });

            if (checkedBoxes.length > 5) {
                confirmHtml += '<li><em>... dan ' + (checkedBoxes.length - 5) + ' santri lainnya</em></li>';
            }

            confirmHtml += '</ul>';
            confirmHtml += '</div>';

            // Show confirmation with Swal
            Swal.fire({
                title: 'Konfirmasi Proses Santri Baru',
                html: confirmHtml,
                icon: totalWithoutKelas > 0 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Ya, Proses Sekarang',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: totalWithoutKelas > 0 ? '#dc3545' : '#28a745',
                cancelButtonColor: '#6c757d',
                width: '600px',
                reverseButtons: true,
                allowOutsideClick: false,
                didOpen: () => {
                    // Scroll to top of content
                    const content = Swal.getHtmlContainer();
                    if (content) {
                        content.scrollTop = 0;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Check again if there are santri without class
                    if (totalWithoutKelas > 0) {
                        // Final warning
                        Swal.fire({
                            title: 'Peringatan!',
                            html: '<p>Ada <strong>' + totalWithoutKelas + ' santri</strong> yang belum memiliki kelas yang dipilih.</p><p>Apakah Anda yakin ingin melanjutkan? Santri tanpa kelas akan dilewati.</p>',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d'
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                // Disable unchecked checkboxes so they won't be submitted
                                $('.checkbox-santri:not(:checked)').each(function() {
                                    const idSantri = $(this).data('id-santri');
                                    $(this).closest('tr').find('select[name="IdKelas[' + idSantri + ']"]').prop('disabled', true);
                                    $(this).closest('tr').find('input[name="IdTpq[' + idSantri + ']"]').prop('disabled', true);
                                });

                                // Show loading
                                Swal.fire({
                                    title: 'Memproses...',
                                    text: 'Mohon tunggu, sedang memproses data santri...',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Submit form
                                $('#formSetKelas')[0].submit();
                            }
                        });
                    } else {
                        // All santri have classes, proceed directly
                        // Disable unchecked checkboxes so they won't be submitted
                        $('.checkbox-santri:not(:checked)').each(function() {
                            const idSantri = $(this).data('id-santri');
                            $(this).closest('tr').find('select[name="IdKelas[' + idSantri + ']"]').prop('disabled', true);
                            $(this).closest('tr').find('input[name="IdTpq[' + idSantri + ']"]').prop('disabled', true);
                        });

                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu, sedang memproses data santri...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        $('#formSetKelas')[0].submit();
                    }
                }
            });
        });

        // Update counters on table draw (when pagination changes)
        table.on('draw', function() {
            updateCounters();

            // Update checkAll checkbox state based on all filtered rows (all pages)
            const allFilteredCheckboxes = getAllFilteredRows().find('.checkbox-santri');
            const totalFiltered = allFilteredCheckboxes.length;
            const totalChecked = allFilteredCheckboxes.filter(':checked').length;

            // Update checkAll state: checked jika semua checkbox di semua halaman tercentang
            $('#checkAll').prop('checked', totalFiltered > 0 && totalChecked === totalFiltered);
        });

        // Initial counter update
        setTimeout(function() {
            updateCounters();
        }, 500);

        // Display flash messages with Swal
        <?php
        // Handle flashdata pesan (HTML format)
        $flashPesan = session()->getFlashdata('pesan');
        if ($flashPesan):
            // Extract type from HTML
            $icon = 'info';
            $title = 'Informasi';

            if (strpos($flashPesan, 'alert-success') !== false) {
                $icon = 'success';
                $title = 'Berhasil!';
            } elseif (strpos($flashPesan, 'alert-danger') !== false) {
                $icon = 'error';
                $title = 'Error!';
            } elseif (strpos($flashPesan, 'alert-warning') !== false) {
                $icon = 'warning';
                $title = 'Peringatan!';
            } elseif (strpos($flashPesan, 'alert-info') !== false) {
                $icon = 'info';
                $title = 'Informasi';
            }

            // Extract text content (remove HTML tags)
            $textContent = strip_tags($flashPesan);
        ?>
            Swal.fire({
                title: '<?= $title ?>',
                text: <?= json_encode($textContent) ?>,
                icon: '<?= $icon ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '<?= $icon === 'success' ? '#28a745' : ($icon === 'error' ? '#dc3545' : ($icon === 'warning' ? '#ffc107' : '#17a2b8')) ?>',
                width: '500px'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: <?= json_encode(session()->getFlashdata('success')) ?>,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                width: '500px'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Error!',
                text: <?= json_encode(session()->getFlashdata('error')) ?>,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545',
                width: '500px'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')): ?>
            Swal.fire({
                title: 'Peringatan!',
                text: <?= json_encode(session()->getFlashdata('warning')) ?>,
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                width: '500px'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            Swal.fire({
                title: 'Informasi',
                text: <?= json_encode(session()->getFlashdata('info')) ?>,
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#17a2b8',
                width: '500px'
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>