<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengajuan Insentif Guru</h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/guru/showBerkasLampiran') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-folder-open"></i> Berkas Lampiran
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php if (in_groups('Admin') || in_groups('Operator')): ?>
                <div class="row mb-3">
                    <?php if (in_groups('Admin')): ?>
                        <div class="col-md-3">
                            <label for="filterTpq" class="form-label">Filter TPQ</label>
                            <select id="filterTpq" class="form-control form-control-sm">
                                <option value="">Semua TPQ</option>
                                <?php if (!empty($tpq)) : foreach ($tpq as $dataTpq): ?>
                                        <option value="<?= esc($dataTpq['IdTpq']) ?>">
                                            <?= esc($dataTpq['NamaTpq']) ?> - <?= esc($dataTpq['KelurahanDesa'] ?? '-') ?>
                                        </option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <label for="filterPenerimaInsentif" class="form-label">Filter Penerima Insentif</label>
                        <select id="filterPenerimaInsentif" class="form-control form-control-sm">
                            <option value="">Semua Jenis</option>
                            <option value="Guru Ngaji">Guru Ngaji</option>
                            <option value="Mubaligh">Mubaligh</option>
                            <option value="Fardu Kifayah">Fardu Kifayah</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="btnPrintBulk" data-toggle="modal" data-target="#modalPrintBulk">
                                <i class="fas fa-print"></i> Print Semua Berkas untuk Semua Guru
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <table id="tabelPengajuanInsentif" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Print</th>
                        <th>NIK / Nama</th>
                        <th>Penerima Insentif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($guru as $item) :
                        $guruData = $item['guru'];
                        $hasKtp = $item['hasKtp'];
                        $hasBpr = $item['hasBpr'];
                        $hasKk = $item['hasKk'];
                    ?>
                        <tr data-idtpq="<?= esc($guruData['IdTpq'] ?? '') ?>">
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btn-print-all d-flex flex-column align-items-center justify-content-center"
                                    data-id-guru="<?= $guruData['IdGuru'] ?>"
                                    data-nama-guru="<?= esc($guruData['Nama']) ?>"
                                    data-penerima-insentif="<?= esc($guruData['JenisPenerimaInsentif'] ?? '') ?>"
                                    data-has-ktp="<?= $hasKtp ? '1' : '0' ?>"
                                    data-has-bpr="<?= $hasBpr ? '1' : '0' ?>"
                                    data-has-kk="<?= $hasKk ? '1' : '0' ?>"
                                    title="Print semua dokumen dalam 1 PDF"
                                    style="min-height: 60px; padding: 5px;">
                                    <i class="fas fa-file-pdf mb-1"></i>
                                    <small style="font-size: 9px; line-height: 1.1;">Print Semua Berkas</small>
                                </button>
                            </td>
                            <td>
                                <?= $guruData['IdGuru'] ?><br>
                                <strong><?= ucwords(strtolower($guruData['Nama'])) ?></strong><br>
                                <small style="color: #666;"><?= $guruData['TempatTugas'] ?? '-' ?></small>
                            </td>
                            <td>
                                <select class="form-control form-control-sm penerima-insentif" data-id-guru="<?= $guruData['IdGuru'] ?>">
                                    <option value="">Pilih Penerima Insentif</option>
                                    <option value="Guru Ngaji" <?= ($guruData['JenisPenerimaInsentif'] ?? '') == 'Guru Ngaji' ? 'selected' : '' ?>>Guru Ngaji</option>
                                    <option value="Mubaligh" <?= ($guruData['JenisPenerimaInsentif'] ?? '') == 'Mubaligh' ? 'selected' : '' ?>>Mubaligh</option>
                                    <option value="Fardu Kifayah" <?= ($guruData['JenisPenerimaInsentif'] ?? '') == 'Fardu Kifayah' ? 'selected' : '' ?>>Fardu Kifayah</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 5px;">
                                    <!-- Row 1: Buttons (4 buttons) -->
                                    <div style="display: flex; gap: 5px; justify-content: space-between;">
                                        <a href="<?= base_url('backend/guru/printSuratPernyataanAsn/' . $guruData['IdGuru']) ?>"
                                            class="btn btn-sm btn-primary btn-pdf-action"
                                            style="flex: 1;"
                                            data-id-guru="<?= $guruData['IdGuru'] ?>"
                                            target="_blank"
                                            title="Surat Pernyataan Tidak Berstatus ASN">
                                            <i class="fas fa-file-pdf"></i> ASN
                                        </a>
                                        <a href="<?= base_url('backend/guru/printSuratPernyataanInsentif/' . $guruData['IdGuru']) ?>"
                                            class="btn btn-sm btn-info btn-pdf-action"
                                            style="flex: 1;"
                                            data-id-guru="<?= $guruData['IdGuru'] ?>"
                                            target="_blank"
                                            title="Surat Pernyataan Tidak Sedang Menerima Insentif">
                                            <i class="fas fa-file-pdf"></i> Insentif
                                        </a>
                                        <a href="<?= base_url('backend/guru/printSuratRekomendasi/' . $guruData['IdGuru']) ?>"
                                            class="btn btn-sm btn-success btn-pdf-action btn-rekomendasi"
                                            style="flex: 1;"
                                            data-id-guru="<?= $guruData['IdGuru'] ?>"
                                            data-penerima-insentif="<?= esc($guruData['JenisPenerimaInsentif'] ?? '') ?>"
                                            target="_blank"
                                            title="Surat Rekomendasi">
                                            <i class="fas fa-file-pdf"></i> Rekomendasi
                                        </a>
                                        <a href="<?= base_url('backend/guru/printLampiran/' . $guruData['IdGuru']) ?>"
                                            class="btn btn-sm btn-warning btn-lampiran"
                                            style="flex: 1;"
                                            data-id-guru="<?= $guruData['IdGuru'] ?>"
                                            target="_blank"
                                            title="Lampiran KTP dan Rekening BPR">
                                            <i class="fas fa-paperclip"></i> Lampiran
                                        </a>
                                    </div>
                                    <!-- Row 2: Keterangan (4 info) -->
                                    <div style="display: flex; gap: 5px; justify-content: space-between;">
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Pernyataan Tidak Berstatus ASN</small>
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Pernyataan Tidak Terima Insentif Lain</small>
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Rekomendasi Guru TPQ</small>
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">
                                            Lampiran KTP
                                            <?php if ($hasKtp): ?>
                                                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                                            <?php endif; ?>
                                            BPR
                                            <?php if ($hasBpr): ?>
                                                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                                            <?php endif; ?>
                                            KK
                                            <?php if ($hasKk): ?>
                                                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                                            <?php endif; ?>
                                            <br>
                                            No Rek BPR:
                                            <?php if (!empty($guruData['NoRekBpr'])): ?>
                                                <span style="color: #007bff;"><?= esc($guruData['NoRekBpr']) ?></span>
                                            <?php else: ?>
                                                <span style="color: #dc3545;">xxxxxxxxxx</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th style="text-align: center;">Print</th>
                        <th>NIK / Nama</th>
                        <th>Penerima Insentif</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Update Penerima Insentif saat dropdown berubah
    $(document).on('change', '.penerima-insentif', function() {
        const select = $(this);
        const idGuru = select.data('id-guru');
        const jenisPenerimaInsentif = select.val();

        // Disable select saat proses update
        select.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('backend/guru/updatePenerimaInsentif') ?>',
            type: 'POST',
            data: {
                IdGuru: idGuru,
                JenisPenerimaInsentif: jenisPenerimaInsentif
            },
            success: function(response) {
                if (response.success) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Tampilkan notifikasi error
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal memperbarui Penerima Insentif'
                    });
                    // Revert ke nilai sebelumnya
                    select.val(select.data('old-value'));
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memperbarui data'
                });
                // Revert ke nilai sebelumnya
                select.val(select.data('old-value'));
            },
            complete: function() {
                // Enable select setelah proses selesai
                select.prop('disabled', false);
            }
        });
    });

    // Simpan nilai lama saat focus
    $(document).on('focus', '.penerima-insentif', function() {
        $(this).data('old-value', $(this).val());
    });

    // Update data-penerima-insentif pada link Rekomendasi saat dropdown berubah
    $(document).on('change', '.penerima-insentif', function() {
        const select = $(this);
        const idGuru = select.data('id-guru');
        const jenisPenerimaInsentif = select.val();

        // Update data attribute pada link Rekomendasi di baris yang sama
        const row = select.closest('tr');
        const btnRekomendasi = row.find('.btn-rekomendasi');
        btnRekomendasi.attr('data-penerima-insentif', jenisPenerimaInsentif);
    });

    // Validasi semua button PDF - cek apakah Penerima Insentif sudah dipilih
    $(document).on('click', '.btn-pdf-action', function(e) {
        const btn = $(this);
        const row = btn.closest('tr');
        const selectPenerimaInsentif = row.find('.penerima-insentif');
        const penerimaInsentif = selectPenerimaInsentif.val();

        // Cek apakah Penerima Insentif sudah dipilih
        if (!penerimaInsentif || penerimaInsentif === '') {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                html: '<p>Silakan pilih <strong>Penerima Insentif</strong> terlebih dahulu sebelum membuat surat.</p>',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3085d6'
            });

            return false;
        }

        // Validasi khusus untuk tombol Rekomendasi - hanya untuk Guru Ngaji
        if (btn.hasClass('btn-rekomendasi')) {
            if (penerimaInsentif !== 'Guru Ngaji') {
                e.preventDefault();

                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    html: '<p>Surat Rekomendasi hanya dapat dibuat untuk <strong>Penerima Insentif: Guru Ngaji</strong>.</p>' +
                        '<p>Silakan pilih <strong>"Guru Ngaji"</strong> pada kolom Penerima Insentif terlebih dahulu.</p>',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6'
                });

                return false;
            }
        }
    });

    // Validasi tombol Lampiran - cek apakah berkas sudah ada
    $(document).on('click', '.btn-lampiran', function(e) {
        e.preventDefault(); // Prevent default dulu

        const btn = $(this);
        const idGuru = btn.data('id-guru');
        const href = btn.attr('href');

        // Cek apakah berkas KTP dan BPR sudah ada
        $.ajax({
            url: '<?= base_url('backend/guru/checkBerkasLampiran') ?>',
            type: 'POST',
            data: {
                IdGuru: idGuru
            },
            success: function(response) {
                if (!response.success) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        html: '<p>' + response.message + '</p>',
                        showCancelButton: true,
                        confirmButtonText: response.uploadUrl ? '<i class="fas fa-upload"></i> Ke Halaman Upload Berkas' : 'Mengerti',
                        confirmButtonColor: '#3085d6',
                        cancelButtonText: 'Tutup',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed && response.uploadUrl) {
                            // Redirect ke halaman upload berkas
                            window.location.href = response.uploadUrl;
                        }
                    });
                } else {
                    // Jika berhasil, buka link
                    window.open(href, '_blank');
                }
            },
            error: function() {
                // Jika error, tetap buka link (validasi akan dilakukan di server)
                window.open(href, '_blank');
            }
        });
    });

    // Print All Documents per Guru dalam 1 PDF
    $(document).on('click', '.btn-print-all', function(e) {
        e.preventDefault();

        const idGuru = $(this).data('id-guru');
        const namaGuru = $(this).data('nama-guru');
        const penerimaInsentif = $(this).data('penerima-insentif');
        const hasKtp = $(this).data('has-ktp') == 1;
        const hasBpr = $(this).data('has-bpr') == 1;
        const hasKk = $(this).data('has-kk') == 1;

        // Validasi minimal harus ada Penerima Insentif
        if (!penerimaInsentif || penerimaInsentif === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                html: 'Silakan pilih <strong>Penerima Insentif</strong> terlebih dahulu untuk guru <strong>' + namaGuru + '</strong>',
                confirmButtonText: 'Mengerti'
            });
            return;
        }

        // Validasi berkas lampiran
        if (!hasKtp || !hasBpr) {
            let missingFiles = [];
            if (!hasKtp) missingFiles.push('KTP');
            if (!hasBpr) missingFiles.push('Buku Rekening BPR');

            Swal.fire({
                icon: 'warning',
                title: 'Berkas Tidak Lengkap',
                html: '<p>Berkas berikut belum diupload untuk <strong>' + namaGuru + '</strong>:</p>' +
                    '<ul style="text-align: left;">' +
                    missingFiles.map(f => '<li>' + f + '</li>').join('') +
                    '</ul>' +
                    '<p>Silakan upload berkas terlebih dahulu.</p>',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-upload"></i> Ke Halaman Upload Berkas',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Tutup',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('backend/guru/showBerkasLampiran') ?>';
                }
            });
            return;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Memproses PDF...',
            html: 'Sedang menggabungkan semua dokumen menjadi 1 PDF untuk <strong>' + namaGuru + '</strong>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Generate PDF
        const printUrl = '<?= base_url('backend/guru/printAllDocuments') ?>/' + idGuru;

        fetch(printUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal generate PDF');
                }
                return response.blob();
            })
            .then(blob => {
                Swal.close();

                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                // Format: NAMA_IDGURU.pdf (semua uppercase)
                // Bersihkan nama: hanya huruf, angka, dan underscore
                const namaBersih = namaGuru.replace(/\s+/g, '_').replace(/[^A-Z0-9_]/gi, '').toUpperCase();
                const namaFile = namaBersih + '_' + idGuru.toString() + '.pdf';
                a.download = namaFile;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'PDF berhasil didownload',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat generate PDF: ' + error.message
                });
            });
    });

    // Inisialisasi DataTable dengan scroll horizontal
    document.addEventListener('DOMContentLoaded', function() {
        const table = initializeDataTableScrollX("#tabelPengajuanInsentif", [], {
            "pageLength": 25,
            "lengthChange": true
        });

        // Filter kombinasi TPQ dan Penerima Insentif
        <?php if (in_groups('Admin')): ?>
            const filterTpq = $('#filterTpq');
        <?php endif; ?>
        const filterPenerimaInsentif = $('#filterPenerimaInsentif');
        let combinedFilterFunction = null;

        // Fungsi untuk apply filter gabungan
        function applyCombinedFilter() {
            // Remove existing filter if any
            if (combinedFilterFunction !== null) {
                const searchFunctions = $.fn.dataTable.ext.search;
                for (let i = searchFunctions.length - 1; i >= 0; i--) {
                    if (searchFunctions[i] === combinedFilterFunction) {
                        searchFunctions.splice(i, 1);
                        break;
                    }
                }
                combinedFilterFunction = null;
            }

            let selectedTpq = '';
            <?php if (in_groups('Admin')): ?>
                selectedTpq = filterTpq.val();
            <?php endif; ?>
            const selectedPenerimaInsentif = filterPenerimaInsentif.val();

            // Jika ada filter yang dipilih
            if (selectedTpq !== '' || selectedPenerimaInsentif !== '') {
                combinedFilterFunction = function(settings, data, dataIndex) {
                    // Hanya terapkan filter untuk tabel ini
                    if (!settings || !settings.nTable || settings.nTable.id !== 'tabelPengajuanInsentif') {
                        return true;
                    }

                    try {
                        // Gunakan data array dari DataTables, bukan DOM
                        // data[0] = NIK/Nama column (index 0)
                        // data[1] = Penerima Insentif column (index 1)

                        // Untuk mendapatkan IdTpq dan Penerima Insentif dari DOM
                        const row = table.row(dataIndex).node();
                        if (!row) {
                            return true;
                        }

                        // Filter TPQ - dari data-idtpq attribute
                        let tpqMatch = true;
                        if (selectedTpq !== '') {
                            const rowIdTpq = $(row).attr('data-idtpq');
                            tpqMatch = (rowIdTpq === selectedTpq);
                        }

                        // Filter Penerima Insentif - dari select value di kolom
                        let penerimaMatch = true;
                        if (selectedPenerimaInsentif !== '') {
                            const selectElement = $(row).find('.penerima-insentif');
                            const rowPenerimaInsentif = selectElement.val();
                            penerimaMatch = (rowPenerimaInsentif === selectedPenerimaInsentif);
                        }

                        // Kedua filter harus match (AND logic)
                        return tpqMatch && penerimaMatch;
                    } catch (e) {
                        console.error('Error in combined filter:', e);
                        return true;
                    }
                };

                // Add combined filter
                $.fn.dataTable.ext.search.push(combinedFilterFunction);
            }

            // Redraw table - ini akan memfilter semua data, tidak hanya page saat ini
            table.draw();
        }

        // Event handlers untuk filter
        <?php if (in_groups('Admin')): ?>
            filterTpq.on('change', function() {
                applyCombinedFilter();
            });
        <?php endif; ?>

        filterPenerimaInsentif.on('change', function() {
            applyCombinedFilter();
        });
    });

    // Print Semua Berkas untuk Semua Guru Handler
    <?php if (in_groups('Admin') || in_groups('Operator')): ?>
        <?php
        $isOperator = in_groups('Operator');
        $operatorIdTpq = $isOperator ? session()->get('IdTpq') : null;
        ?>
        $(document).ready(function() {
            // Key untuk localStorage
            const STORAGE_KEY = 'bulkInsentifFilter';

            // Fungsi untuk menyimpan filter ke localStorage
            function saveFilterToStorage() {
                const filterTpqValue = <?php if ($isOperator): ?>[<?= $operatorIdTpq ?>] <?php else: ?>($('#bulkFilterTpq').val() || []) <?php endif; ?>;
                const filterData = {
                    fileTypes: [],
                    jenisPenerimaInsentif: $('#bulkJenisPenerimaInsentif').val() || '',
                    filterTpq: filterTpqValue,
                    singleZip: $('#checkSingleZip').is(':checked')
                };

                // Ambil file types yang dicentang
                $('input[name="fileTypes[]"]:checked').each(function() {
                    filterData.fileTypes.push($(this).val());
                });

                // Simpan ke localStorage
                localStorage.setItem(STORAGE_KEY, JSON.stringify(filterData));
                console.log('Filter saved to localStorage:', filterData);
            }

            // Fungsi untuk memuat filter dari localStorage
            function loadFilterFromStorage() {
                try {
                    const savedData = localStorage.getItem(STORAGE_KEY);
                    if (savedData) {
                        const filterData = JSON.parse(savedData);
                        console.log('Filter loaded from localStorage:', filterData);

                        // Restore file types
                        if (filterData.fileTypes && filterData.fileTypes.length > 0) {
                            $('input[name="fileTypes[]"]').prop('checked', false);
                            filterData.fileTypes.forEach(function(fileType) {
                                $('input[name="fileTypes[]"][value="' + fileType + '"]').prop('checked', true);
                            });
                        }

                        // Restore jenis penerima insentif
                        if (filterData.jenisPenerimaInsentif) {
                            $('#bulkJenisPenerimaInsentif').val(filterData.jenisPenerimaInsentif).trigger('change');
                        }

                        // Restore filter TPQ (hanya untuk Admin)
                        <?php if (in_groups('Admin')): ?>
                            if (filterData.filterTpq && filterData.filterTpq.length > 0) {
                                $('#bulkFilterTpq').val(filterData.filterTpq).trigger('change');
                            }
                        <?php endif; ?>

                        // Restore single ZIP checkbox
                        if (filterData.singleZip !== undefined) {
                            $('#checkSingleZip').prop('checked', filterData.singleZip);
                        }
                    }
                } catch (e) {
                    console.error('Error loading filter from localStorage:', e);
                }
            }

            // Initialize Select2 untuk TPQ filter (hanya untuk Admin)
            <?php if (in_groups('Admin')): ?>
                $('#bulkFilterTpq').select2({
                    placeholder: 'Pilih TPQ (kosongkan untuk semua TPQ)',
                    allowClear: true,
                    width: '100%'
                });
            <?php else: ?>
                // Untuk Operator, set TPQ otomatis dan disable dropdown
                $('#bulkFilterTpq').val([<?= $operatorIdTpq ?>]).trigger('change');
                $('#bulkFilterTpq').prop('disabled', true);
            <?php endif; ?>

            // Load filter saat modal dibuka
            $('#modalPrintBulk').on('show.bs.modal', function() {
                loadFilterFromStorage();
            });

            // Simpan filter saat perubahan
            $('input[name="fileTypes[]"]').on('change', function() {
                saveFilterToStorage();
            });

            $('#bulkJenisPenerimaInsentif').on('change', function() {
                saveFilterToStorage();
            });

            <?php if (in_groups('Admin')): ?>
                $('#bulkFilterTpq').on('change', function() {
                    saveFilterToStorage();
                });
            <?php endif; ?>

            $('#checkSingleZip').on('change', function() {
                saveFilterToStorage();
            });

            // Prevent form submit
            $('#formPrintBulk').on('submit', function(e) {
                e.preventDefault();
                return false;
            });

            $(document).on('click', '#btnSubmitPrintBulk', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Print Semua Berkas untuk Semua Guru button clicked');

                const fileTypes = [];
                $('input[name="fileTypes[]"]:checked').each(function() {
                    fileTypes.push($(this).val());
                });

                console.log('File types selected:', fileTypes);

                if (fileTypes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal satu file yang akan di print'
                    });
                    return false;
                }

                const jenisPenerimaInsentif = $('#bulkJenisPenerimaInsentif').val();
                const singleZip = $('#checkSingleZip').is(':checked');
                let bulkFilterTpq;
                <?php if ($isOperator): ?>
                    // Untuk Operator, gunakan TPQ mereka sendiri
                    bulkFilterTpq = [<?= $operatorIdTpq ?>];
                <?php else: ?>
                    bulkFilterTpq = $('#bulkFilterTpq').val(); // Array jika multiple select
                <?php endif; ?>

                // Validasi: Jenis Penerima Insentif wajib dipilih
                if (!jenisPenerimaInsentif || jenisPenerimaInsentif === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih Jenis Penerima Insentif terlebih dahulu'
                    });
                    return false;
                }

                // Validasi data terlebih dahulu
                Swal.fire({
                    title: 'Memvalidasi...',
                    html: 'Sedang memeriksa ketersediaan data...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Check data terlebih dahulu
                let checkUrl = '<?= base_url('backend/guru/checkBulkInsentifData') ?>?';
                if (jenisPenerimaInsentif) {
                    checkUrl += 'jenisPenerimaInsentif=' + encodeURIComponent(jenisPenerimaInsentif);
                }
                if (bulkFilterTpq && bulkFilterTpq.length > 0) {
                    if (jenisPenerimaInsentif) {
                        checkUrl += '&';
                    }
                    // Jika multiple, kirim sebagai array
                    if (Array.isArray(bulkFilterTpq)) {
                        bulkFilterTpq.forEach(function(tpqId, index) {
                            if (index === 0 && !jenisPenerimaInsentif) {
                                checkUrl += 'filterTpq[]=' + encodeURIComponent(tpqId);
                            } else {
                                checkUrl += '&filterTpq[]=' + encodeURIComponent(tpqId);
                            }
                        });
                    } else {
                        checkUrl += 'filterTpq=' + encodeURIComponent(bulkFilterTpq);
                    }
                }

                fetch(checkUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        // Cek status code terlebih dahulu
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('HTTP Error:', response.status, text);
                                throw new Error('HTTP ' + response.status + ': ' + (text.substring(0, 200) || 'Terjadi kesalahan pada server'));
                            });
                        }

                        // Cek content type
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.error('Non-JSON response:', text);
                                throw new Error('Server mengembalikan response yang tidak valid. Silakan cek log untuk detail.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Tidak Ditemukan',
                                html: data.message,
                                confirmButtonText: 'Mengerti',
                                width: '600px'
                            });
                            return false;
                        }

                        // Jika data ada, lanjutkan proses print
                        console.log('Data validation passed:', data.count, 'guru ditemukan');

                        // Build URL untuk print
                        let url = '<?= base_url('backend/guru/printBulkInsentif') ?>?';
                        url += 'fileTypes=' + fileTypes.join(',');
                        if (jenisPenerimaInsentif) {
                            url += '&jenisPenerimaInsentif=' + encodeURIComponent(jenisPenerimaInsentif);
                        }
                        if (singleZip) {
                            url += '&singleZip=1';
                        }
                        if (bulkFilterTpq && bulkFilterTpq.length > 0) {
                            // Jika multiple, kirim sebagai array
                            if (Array.isArray(bulkFilterTpq)) {
                                bulkFilterTpq.forEach(function(tpqId) {
                                    url += '&filterTpq[]=' + encodeURIComponent(tpqId);
                                });
                            } else {
                                url += '&filterTpq=' + encodeURIComponent(bulkFilterTpq);
                            }
                        }

                        console.log('Print URL:', url);

                        // Close modal
                        $('#modalPrintBulk').modal('hide');

                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Sedang membuat PDF bulk untuk ' + data.count + ' guru, mohon tunggu...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Gunakan fetch untuk download dengan error handling
                        return fetch(url, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                console.log('Response status:', response.status);
                                console.log('Response headers:', response.headers);

                                // Cek jika response adalah JSON (error)
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json().then(data => {
                                        throw new Error(data.message || 'Terjadi kesalahan');
                                    });
                                }

                                // Cek status code
                                if (!response.ok) {
                                    return response.text().then(text => {
                                        throw new Error('HTTP ' + response.status + ': ' + text.substring(0, 200));
                                    });
                                }

                                // Ambil nama file dari header Content-Disposition
                                const contentDisposition = response.headers.get('content-disposition');
                                let downloadFilename = 'Pengajuan_Insentif_' + new Date().getTime() + '.zip';

                                if (contentDisposition) {
                                    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                                    if (filenameMatch && filenameMatch[1]) {
                                        // Hapus quotes jika ada
                                        downloadFilename = filenameMatch[1].replace(/['"]/g, '');
                                        console.log('Filename from header:', downloadFilename);
                                    }
                                }

                                // Jika sukses, download file
                                return response.blob().then(blob => {
                                    return {
                                        blob: blob,
                                        filename: downloadFilename
                                    };
                                });
                            })
                            .then(result => {
                                console.log('Download berhasil, blob size:', result.blob.size);
                                console.log('Download filename:', result.filename);

                                if (result.blob.size === 0) {
                                    throw new Error('File ZIP kosong. Silakan cek log untuk detail error.');
                                }

                                // Buat URL untuk download
                                const downloadUrl = window.URL.createObjectURL(result.blob);
                                const a = document.createElement('a');
                                a.href = downloadUrl;
                                a.download = result.filename;
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                window.URL.revokeObjectURL(downloadUrl);

                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'File ZIP berhasil diunduh',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: '<p>Terjadi kesalahan saat membuat PDF bulk:</p><p><strong>' + error.message + '</strong></p><p>Silakan cek log atau hubungi administrator.</p>',
                                    confirmButtonText: 'Mengerti'
                                });
                            });
                    })
                    .catch(error => {
                        console.error('Validation error:', error);
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Validasi',
                            html: '<p>Terjadi kesalahan saat memvalidasi data:</p><p><strong>' + error.message + '</strong></p>',
                            confirmButtonText: 'Mengerti'
                        });
                    });

                return false;
            });
        });
    <?php endif; ?>
</script>

<!-- Modal Print Semua Berkas untuk Semua Guru (Untuk Admin dan Operator) -->
<?php if (in_groups('Admin') || in_groups('Operator')): ?>
    <?php
    $isOperator = in_groups('Operator');
    $operatorIdTpq = $isOperator ? session()->get('IdTpq') : null;
    ?>
    <div class="modal fade" id="modalPrintBulk" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Print Semua Berkas untuk Semua Guru - Pengajuan Insentif</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formPrintBulk">
                        <div class="form-group">
                            <label>Pilih File yang Akan di Print:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkAsn" name="fileTypes[]" value="asn" checked>
                                <label class="form-check-label" for="checkAsn">
                                    Surat Pernyataan Tidak Berstatus ASN
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkInsentif" name="fileTypes[]" value="insentif" checked>
                                <label class="form-check-label" for="checkInsentif">
                                    Surat Pernyataan Tidak Sedang Menerima Insentif
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkRekomendasi" name="fileTypes[]" value="rekomendasi" checked>
                                <label class="form-check-label" for="checkRekomendasi">
                                    Surat Rekomendasi
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkLampiran" name="fileTypes[]" value="lampiran" checked>
                                <label class="form-check-label" for="checkLampiran">
                                    Lampiran KTP dan Rekening BPR
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bulkJenisPenerimaInsentif">Filter Jenis Penerima Insentif: <span class="text-danger">*</span></label>
                            <select class="form-control" id="bulkJenisPenerimaInsentif" name="jenisPenerimaInsentif" required>
                                <option value="">-- Pilih Jenis Penerima Insentif --</option>
                                <option value="Guru Ngaji">Guru Ngaji</option>
                                <option value="Mubaligh">Mubaligh</option>
                                <option value="Fardu Kifayah">Fardu Kifayah</option>
                            </select>
                            <small class="form-text text-muted">Wajib dipilih salah satu jenis penerima insentif</small>
                        </div>
                        <div class="form-group">
                            <label>Filter TPQ:</label>
                            <?php if ($isOperator): ?>
                                <select class="form-control" id="bulkFilterTpq" name="filterTpq[]" disabled style="width: 100%;">
                                    <?php
                                    // Tampilkan hanya TPQ operator
                                    if (!empty($tpq)) :
                                        foreach ($tpq as $dataTpq):
                                            if ($dataTpq['IdTpq'] == $operatorIdTpq):
                                    ?>
                                                <option value="<?= esc($dataTpq['IdTpq']) ?>" selected>
                                                    <?= esc($dataTpq['NamaTpq']) ?> - <?= esc($dataTpq['KelurahanDesa'] ?? '-') ?>
                                                </option>
                                    <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                                <small class="form-text text-muted">Anda hanya dapat melihat data TPQ Anda sendiri.</small>
                            <?php else: ?>
                                <select class="form-control select2" id="bulkFilterTpq" name="filterTpq[]" multiple="multiple" style="width: 100%;">
                                    <?php if (!empty($tpq)) : foreach ($tpq as $dataTpq): ?>
                                            <option value="<?= esc($dataTpq['IdTpq']) ?>">
                                                <?= esc($dataTpq['NamaTpq']) ?> - <?= esc($dataTpq['KelurahanDesa'] ?? '-') ?>
                                            </option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                                <small class="form-text text-muted">Pilih satu atau lebih TPQ. File ZIP akan dikelompokkan berdasarkan TPQ yang dipilih. Kosongkan untuk semua TPQ.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkSingleZip" name="singleZip" value="1">
                                <label class="form-check-label" for="checkSingleZip">
                                    <strong>Semua Guru dalam Satu Folder ZIP (Tidak Dikelompokkan per TPQ)</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Jika dicentang, semua PDF guru akan dimasukkan dalam satu folder ZIP, bukan dikelompokkan berdasarkan TPQ.
                            </small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Info:</strong> Setiap guru akan memiliki 1 file PDF yang berisi gabungan dari file yang dipilih (maksimal 4 file).
                            File akan di-zip berdasarkan filter TPQ (kecuali opsi "Semua dalam Satu ZIP" dicentang).
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitPrintBulk">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>