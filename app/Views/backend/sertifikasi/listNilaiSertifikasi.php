<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> List Nilai Sertifikasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="tableNilaiSertifikasi">
                                <thead>
                                    <tr>
                                        <th>UserName Juri</th>
                                        <th>No Peserta</th>
                                        <th>Nama Guru</th>
                                        <th>No Rek</th>
                                        <th>Nama TPQ</th>
                                        <?php if (!empty($all_materi)): ?>
                                            <?php foreach ($all_materi as $materi): ?>
                                                <th>Nilai <?= esc($materi['NamaMateri']) ?></th>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <th>Jumlah</th>
                                        <th>Rata-Rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($nilai_data)): ?>
                                        <?php foreach ($nilai_data as $peserta): ?>
                                            <tr>
                                                <td data-export="<?= esc($peserta['usernameJuri'] ?? '-') ?>"><?= esc($peserta['usernameJuri'] ?? '-') ?></td>
                                                <td data-export="<?= esc($peserta['noTest']) ?>"><strong><?= esc($peserta['noTest']) ?></strong></td>
                                                <td data-export="<?= esc($peserta['NamaGuru']) ?>"><?= esc($peserta['NamaGuru']) ?></td>
                                                <td data-export="<?= esc($peserta['NoRek']) ?>"><?= esc($peserta['NoRek']) ?></td>
                                                <td data-export="<?= esc($peserta['NamaTpq']) ?>"><?= esc($peserta['NamaTpq']) ?></td>
                                                <?php if (!empty($all_materi)): ?>
                                                    <?php foreach ($all_materi as $materi): ?>
                                                        <?php
                                                        $nilaiMateri = $peserta['nilaiByMateri'][$materi['IdMateri']] ?? null;
                                                        $idNilai = $peserta['idNilaiByMateri'][$materi['IdMateri']] ?? null;
                                                        $catatan = $peserta['catatanByMateri'][$materi['IdMateri']] ?? null;
                                                        // Cek apakah catatan ada dan tidak kosong
                                                        $hasCatatan = !empty($catatan) && trim($catatan) !== '';
                                                        $badgeClass = '';
                                                        $displayNilai = 0;

                                                        if ($nilaiMateri !== null && $nilaiMateri !== '') {
                                                            $displayNilai = floatval($nilaiMateri);
                                                            // Range warna badge:
                                                            // Merah <= 64
                                                            // Kuning 65-75
                                                            // Hijau 75-85
                                                            // Biru Terang 85-100
                                                            if ($displayNilai <= 64) {
                                                                $badgeClass = 'danger'; // Merah
                                                            } elseif ($displayNilai >= 65 && $displayNilai < 75) {
                                                                $badgeClass = 'warning'; // Kuning
                                                            } elseif ($displayNilai >= 75 && $displayNilai < 85) {
                                                                $badgeClass = 'success'; // Hijau
                                                            } else { // >= 85
                                                                $badgeClass = 'primary'; // Biru Terang
                                                            }
                                                        } else {
                                                            // Jika belum ada nilai, tampilkan 0 dengan badge merah
                                                            $displayNilai = 0;
                                                            $badgeClass = 'danger';
                                                        }
                                                        ?>
                                                        <td class="text-center" data-export="<?= number_format($displayNilai, 2) ?>" data-id-nilai="<?= esc($idNilai) ?>" data-id-materi="<?= esc($materi['IdMateri']) ?>" data-nama-materi="<?= esc($materi['NamaMateri']) ?>" data-no-peserta="<?= esc($peserta['noTest']) ?>" data-nama-guru="<?= esc($peserta['NamaGuru']) ?>" data-catatan="<?= $hasCatatan ? htmlspecialchars($catatan, ENT_QUOTES, 'UTF-8') : '' ?>">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <span class="badge badge-<?= $badgeClass ?> mr-2">
                                                                    <?= number_format($displayNilai, 2) ?>
                                                                </span>
                                                                <?php if ($idNilai): ?>
                                                                    <button type="button" class="btn btn-sm btn-edit-nilai <?= $hasCatatan ? 'btn-edit-has-catatan' : '' ?>" data-toggle="modal" data-target="#modalEditNilai" title="Edit Nilai" style="background-color: transparent; border: none; padding: 0.25rem 0.5rem;">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <?php if ($hasCatatan): ?>
                                                                        <button type="button" class="btn btn-sm btn-restore-nilai" data-id-nilai="<?= esc($idNilai) ?>" data-catatan="<?= htmlspecialchars($catatan, ENT_QUOTES, 'UTF-8') ?>" data-toggle="modal" data-target="#modalRestoreNilai" title="Kembalikan ke Nilai Sebelumnya" style="background-color: transparent; border: none; padding: 0.25rem 0.5rem; margin-left: 2px;">
                                                                            <i class="fas fa-undo" style="color: #28a745;"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <td class="text-center" data-export="<?= number_format($peserta['jumlah'], 2) ?>">
                                                    <strong><?= number_format($peserta['jumlah'], 2) ?></strong>
                                                </td>
                                                <td class="text-center" data-export="<?= number_format($peserta['rataRata'], 2) ?>">
                                                    <strong><?= number_format($peserta['rataRata'], 2) ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?= 5 + (count($all_materi ?? [])) + 2 ?>" class="text-center">
                                                <p class="text-muted">Tidak ada data nilai sertifikasi</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Restore Nilai -->
<div class="modal fade" id="modalRestoreNilai" tabindex="-1" role="dialog" aria-labelledby="modalRestoreNilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRestoreNilaiLabel">Kembalikan ke Nilai Sebelumnya</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Pilih nilai yang ingin dikembalikan dari history perubahan di bawah ini.
                </div>
                <div id="restoreHistoryList">
                    <!-- History akan diisi oleh JavaScript -->
                </div>
                <input type="hidden" id="restoreIdNilai" name="idNilai">
                <input type="hidden" id="restoreNilaiDipilih" name="nilaiDipilih">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnConfirmRestore" disabled>Kembalikan Nilai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Nilai -->
<div class="modal fade" id="modalEditNilai" tabindex="-1" role="dialog" aria-labelledby="modalEditNilaiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditNilaiLabel">Edit Nilai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditNilai">
                    <input type="hidden" id="editIdNilai" name="idNilai">
                    <div class="form-group">
                        <label for="editNoPeserta">No Peserta</label>
                        <input type="text" class="form-control" id="editNoPeserta" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNamaGuru">Nama Guru</label>
                        <input type="text" class="form-control" id="editNamaGuru" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNamaMateri">Materi</label>
                        <input type="text" class="form-control" id="editNamaMateri" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNilaiBaru">Nilai Baru <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editNilaiBaru" name="nilaiBaru" min="0" max="100" step="0.01" required>
                        <small class="form-text text-muted">Masukkan nilai antara 0-100</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveNilai">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        var table = $('#tableNilaiSertifikasi').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 25,
            'order': [
                [1, 'asc']
            ],
            'dom': 'Bfrtip',
            'buttons': [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'List Nilai Sertifikasi',
                    filename: 'List_Nilai_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Gunakan data-export jika ada, jika tidak ambil text dari node
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                // Hapus tag HTML dari data (seperti badge, strong, dll)
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'List Nilai Sertifikasi',
                    filename: 'List_Nilai_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Gunakan data-export jika ada, jika tidak ambil text dari node
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                // Hapus tag HTML dari data (seperti badge, strong, dll)
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [10, 10, 10, 10];
                    }
                }
            ],
            'language': {
                // Konfigurasi bahasa manual untuk menghindari masalah CORS
                'buttons': {
                    excel: 'Export ke Excel',
                    pdf: 'Export ke PDF'
                },
                'emptyTable': 'Tidak ada data yang tersedia pada tabel',
                'info': 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                'infoEmpty': 'Menampilkan 0 sampai 0 dari 0 entri',
                'infoFiltered': '(disaring dari _MAX_ entri keseluruhan)',
                'lengthMenu': 'Tampilkan _MENU_ entri',
                'loadingRecords': 'Sedang memuat...',
                'processing': 'Sedang memproses...',
                'search': 'Cari:',
                'zeroRecords': 'Tidak ditemukan data yang sesuai',
                'paginate': {
                    'first': 'Pertama',
                    'last': 'Terakhir',
                    'next': 'Selanjutnya',
                    'previous': 'Sebelumnya'
                },
                'aria': {
                    'sortAscending': ': aktifkan untuk mengurutkan kolom naik',
                    'sortDescending': ': aktifkan untuk mengurutkan kolom turun'
                }
            }
        });

        // Handle klik tombol edit nilai
        $(document).on('click', '.btn-edit-nilai', function() {
            var $td = $(this).closest('td');
            var $row = $td.closest('tr');
            var idNilai = $td.data('id-nilai');
            var idMateri = $td.data('id-materi');
            var namaMateri = $td.data('nama-materi');
            var noPeserta = $td.data('no-peserta');
            var namaGuru = $td.data('nama-guru');

            // Ambil nilai saat ini dari badge
            var nilaiSaatIni = parseFloat($td.find('.badge').text().trim()) || 0;

            // Set nilai ke form
            $('#editIdNilai').val(idNilai);
            $('#editNoPeserta').val(noPeserta);
            $('#editNamaGuru').val(namaGuru);
            $('#editNamaMateri').val(namaMateri);
            $('#editNilaiBaru').val(nilaiSaatIni);

            // Simpan referensi ke element untuk update nanti
            $('#editIdNilai').data('$td', $td);
            $('#editIdNilai').data('$row', $row);
            // Simpan nilai lama untuk perbandingan
            $('#editIdNilai').data('nilaiLama', nilaiSaatIni);
        });

        // Focus ke input nilai setelah modal dibuka (hanya sekali)
        $('#modalEditNilai').on('shown.bs.modal', function() {
            $('#editNilaiBaru').focus().select();
        });

        // Fungsi untuk menyimpan nilai
        function simpanNilai() {
            var idNilai = $('#editIdNilai').val();
            var nilaiBaru = $('#editNilaiBaru').val();

            // Validasi
            if (!idNilai) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'ID Nilai tidak ditemukan'
                });
                return;
            }

            if (!nilaiBaru || nilaiBaru === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Nilai baru harus diisi'
                });
                return;
            }

            var nilaiFloat = parseFloat(nilaiBaru);
            if (isNaN(nilaiFloat) || nilaiFloat < 0 || nilaiFloat > 100) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Nilai harus berupa angka antara 0-100'
                });
                return;
            }

            // Cek apakah nilai berubah
            var nilaiLama = $('#editIdNilai').data('nilaiLama') || 0;
            if (Math.abs(nilaiFloat - nilaiLama) < 0.01) {
                // Nilai sama, tampilkan konfirmasi
                Swal.fire({
                    icon: 'info',
                    title: 'Nilai Tidak Berubah',
                    text: 'Nilai yang Anda masukkan sama dengan nilai sebelumnya (' + nilaiLama.toFixed(2) + '). Apakah Anda ingin keluar dari form edit atau tetap ingin mengedit?',
                    showCancelButton: true,
                    confirmButtonText: 'Keluar',
                    cancelButtonText: 'Tetap Edit',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User memilih keluar
                        $('#modalEditNilai').modal('hide');
                    }
                    // Jika cancel, tetap di form (tidak perlu melakukan apa-apa)
                });
                return;
            }

            // Disable button
            $('#btnSaveNilai').prop('disabled', true).text('Menyimpan...');

            // Kirim request
            $.ajax({
                url: '<?= base_url('backend/sertifikasi/updateNilai') ?>',
                type: 'POST',
                data: {
                    idNilai: idNilai,
                    nilaiBaru: nilaiFloat,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Ambil referensi element yang disimpan
                        var $td = $('#editIdNilai').data('$td');
                        var $row = $('#editIdNilai').data('$row');
                        var nilaiBaru = parseFloat(response.data.nilaiBaru);

                        // Update badge nilai
                        var $badge = $td.find('.badge');
                        // Range warna badge:
                        // Merah <= 64
                        // Kuning 65-75
                        // Hijau 75-85
                        // Biru Terang 85-100
                        var badgeClass;
                        if (nilaiBaru <= 64) {
                            badgeClass = 'danger'; // Merah
                        } else if (nilaiBaru >= 65 && nilaiBaru < 75) {
                            badgeClass = 'warning'; // Kuning
                        } else if (nilaiBaru >= 75 && nilaiBaru < 85) {
                            badgeClass = 'success'; // Hijau
                        } else { // >= 85
                            badgeClass = 'primary'; // Biru Terang
                        }
                        $badge.removeClass('badge-success badge-warning badge-danger badge-info badge-primary')
                            .addClass('badge-' + badgeClass)
                            .text(nilaiBaru.toFixed(2));

                        // Update data-export untuk export
                        $td.attr('data-export', nilaiBaru.toFixed(2));

                        // Hitung ulang jumlah dan rata-rata
                        var totalNilai = 0;
                        var totalMateri = <?= count($all_materi ?? []) ?>;

                        // Ambil semua nilai materi dari row yang sama
                        // Kolom nilai materi adalah yang memiliki data-id-materi
                        $row.find('td[data-id-materi]').each(function() {
                            var $badge = $(this).find('.badge');
                            if ($badge.length > 0) {
                                // Ambil nilai dari badge
                                var nilai = parseFloat($badge.text().trim()) || 0;
                                totalNilai += nilai;
                            } else {
                                // Jika tidak ada badge, nilai = 0
                                totalNilai += 0;
                            }
                        });

                        // Update jumlah (kolom kedua dari akhir)
                        var $jumlahTd = $row.find('td').eq($row.find('td').length - 2);
                        $jumlahTd.find('strong').text(totalNilai.toFixed(2));
                        $jumlahTd.attr('data-export', totalNilai.toFixed(2));

                        // Update rata-rata (kolom terakhir)
                        // Rata-rata = total nilai / jumlah semua materi
                        var rataRata = totalMateri > 0 ? totalNilai / totalMateri : 0;
                        var $rataRataTd = $row.find('td').last();
                        $rataRataTd.find('strong').text(rataRata.toFixed(2));
                        $rataRataTd.attr('data-export', rataRata.toFixed(2));

                        // Update warna icon edit menjadi merah karena sudah ada catatan
                        var $btnEdit = $td.find('.btn-edit-nilai');
                        $btnEdit.addClass('btn-edit-has-catatan');

                        // Ambil catatan terbaru dari database untuk menampilkan icon restore
                        $.ajax({
                            url: '<?= base_url('backend/sertifikasi/getCatatan') ?>',
                            type: 'POST',
                            data: {
                                idNilai: idNilai,
                                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                            },
                            dataType: 'json',
                            success: function(catatanResponse) {
                                if (catatanResponse.success && catatanResponse.data.hasCatatan) {
                                    // Gunakan 'Catatan' (capital C) sesuai dengan response dari controller
                                    var catatan = catatanResponse.data.Catatan || catatanResponse.data.catatan || '';

                                    // Tampilkan icon restore jika ada catatan
                                    var $restoreBtn = $td.find('.btn-restore-nilai');
                                    if ($restoreBtn.length === 0) {
                                        // Buat button restore jika belum ada
                                        var restoreBtnHtml = '<button type="button" class="btn btn-sm btn-restore-nilai" data-id-nilai="' + idNilai + '" data-toggle="modal" data-target="#modalRestoreNilai" title="Kembalikan ke Nilai Sebelumnya" style="background-color: transparent; border: none; padding: 0.25rem 0.5rem; margin-left: 2px;">';
                                        restoreBtnHtml += '<i class="fas fa-undo" style="color: #28a745;"></i>';
                                        restoreBtnHtml += '</button>';
                                        $btnEdit.after(restoreBtnHtml);

                                        // Update data-catatan di td
                                        $td.attr('data-catatan', catatan);
                                    } else {
                                        // Update data-catatan di button dan td
                                        $restoreBtn.attr('data-catatan', catatan);
                                        $td.attr('data-catatan', catatan);
                                    }
                                }
                            },
                            error: function() {
                                console.error('Gagal mengambil catatan dari database');
                            }
                        });

                        // Tutup modal
                        $('#modalEditNilai').modal('hide');

                        // Tampilkan notifikasi sukses dengan SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Nilai berhasil diupdate dari ' + response.data.nilaiLama.toFixed(2) + ' menjadi ' + response.data.nilaiBaru.toFixed(2),
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        var errorMsg = response.message || 'Gagal mengupdate nilai';
                        if (response.details) {
                            errorMsg += '\nDetail: ' + response.details;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                        $('#btnSaveNilai').prop('disabled', false).text('Simpan');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);

                    var errorMsg = 'Terjadi kesalahan saat mengupdate nilai';

                    // Coba parse response jika ada
                    if (xhr.responseJSON) {
                        errorMsg = xhr.responseJSON.message || errorMsg;
                        if (xhr.responseJSON.details) {
                            errorMsg += '\nDetail: ' + xhr.responseJSON.details;
                        }
                    } else if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                            if (response.details) {
                                errorMsg += '\nDetail: ' + response.details;
                            }
                        } catch (e) {
                            // Jika bukan JSON, tampilkan response text
                            if (xhr.status === 500) {
                                errorMsg += '\nError 500: Server Error';
                            } else if (xhr.status === 404) {
                                errorMsg += '\nError 404: Endpoint tidak ditemukan';
                            } else if (xhr.status === 403) {
                                errorMsg += '\nError 403: Akses ditolak (mungkin masalah CSRF token)';
                            }
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                    $('#btnSaveNilai').prop('disabled', false).text('Simpan');
                }
            });
        }

        // Handle simpan nilai dari tombol
        $('#btnSaveNilai').on('click', function() {
            simpanNilai();
        });

        // Handle Enter key di input nilai
        $('#editNilaiBaru').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                simpanNilai();
            }
        });

        // Prevent form submit default
        $('#formEditNilai').on('submit', function(e) {
            e.preventDefault();
            simpanNilai();
        });

        // Fungsi untuk parse catatan dan mendapatkan history nilai (format JSON)
        function parseCatatanHistory(catatan) {
            var history = [];
            if (!catatan || catatan.trim() === '') {
                return history;
            }

            try {
                // Parse JSON
                var data = JSON.parse(catatan);

                console.log('Parsed JSON data:', data);

                if (!data || !data.history || !Array.isArray(data.history)) {
                    console.warn('Invalid JSON structure or missing history array');
                    return history;
                }

                // Convert JSON format ke format yang digunakan di UI
                data.history.forEach(function(item) {
                    var historyItem = {
                        nilaiSebelum: null,
                        nilaiSesudah: null,
                        waktu: null,
                        diubahOleh: null,
                        sumber: null,
                        materi: null,
                        isOriginal: false,
                        isRestore: false,
                        nomorUrut: null
                    };

                    // Handle original
                    if (item.type === 'original') {
                        historyItem.isOriginal = true;
                        historyItem.nilaiSebelum = parseFloat(item.nilai || 0);
                        historyItem.nilaiSesudah = parseFloat(item.nilai || 0);
                        historyItem.materi = item.materi || '';
                        if (item.groupMateri) {
                            historyItem.materi += (historyItem.materi ? ' (' + item.groupMateri + ')' : item.groupMateri);
                        }
                        historyItem.sumber = item.sumber || '';
                        historyItem.waktu = item.timestamp || '';
                    }
                    // Handle perubahan
                    else if (item.type === 'perubahan') {
                        historyItem.isOriginal = false;
                        historyItem.isRestore = false;
                        historyItem.nomorUrut = item.nomor || null;
                        historyItem.nilaiSebelum = parseFloat(item.nilaiSebelum || 0);
                        historyItem.nilaiSesudah = parseFloat(item.nilaiSesudah || 0);
                        historyItem.materi = item.materi || '';
                        if (item.groupMateri) {
                            historyItem.materi += (historyItem.materi ? ' (' + item.groupMateri + ')' : item.groupMateri);
                        }
                        historyItem.sumber = item.sumber || '';
                        historyItem.waktu = item.waktu || '';
                        historyItem.diubahOleh = item.diubahOleh || '';
                    }
                    // Handle restore
                    else if (item.type === 'restore') {
                        historyItem.isOriginal = false;
                        historyItem.isRestore = true;
                        historyItem.nomorUrut = item.nomor || null;
                        historyItem.nilaiSebelum = parseFloat(item.nilaiSebelum || 0); // Nilai sebelum restore
                        historyItem.nilaiSesudah = parseFloat(item.nilaiSesudah || 0); // Nilai setelah restore (nilai yang direstore)
                        historyItem.materi = item.materi || '';
                        if (item.groupMateri) {
                            historyItem.materi += (historyItem.materi ? ' (' + item.groupMateri + ')' : item.groupMateri);
                        }
                        historyItem.waktu = item.waktu || '';
                        historyItem.diubahOleh = item.direstoreOleh || '';
                        // Untuk restore, nilai yang akan dikembalikan adalah nilaiSesudah (nilai yang direstore)
                    }

                    // Tambahkan ke history jika valid (pastikan ada nilai sebelum)
                    if (historyItem.nilaiSebelum !== null || historyItem.isOriginal) {
                        history.push(historyItem);
                    } else {
                        console.warn('History item tidak valid, dilewati:', item);
                    }
                });

                console.log('Total history items setelah parsing:', history.length);
                console.log('Detail history items:', history.map(function(item, idx) {
                    return {
                        index: idx,
                        type: item.isOriginal ? 'original' : (item.isRestore ? 'restore' : 'perubahan'),
                        nomor: item.nomorUrut,
                        nilaiSebelum: item.nilaiSebelum,
                        nilaiSesudah: item.nilaiSesudah,
                        waktu: item.waktu
                    };
                }));

                // History sudah dalam urutan yang benar (original pertama, kemudian perubahan/restore)
                // Tidak perlu reverse karena JSON sudah terurut
                return history;
            } catch (e) {
                console.error('Error parsing catatan JSON:', e);
                console.error('Catatan yang gagal di-parse:', catatan);

                // Coba decode HTML entities jika ada
                try {
                    var decodedCatatan = $('<textarea/>').html(catatan).text();
                    if (decodedCatatan !== catatan) {
                        console.log('Mencoba decode HTML entities...');
                        var data = JSON.parse(decodedCatatan);
                        if (data && data.history && Array.isArray(data.history)) {
                            // Re-parse dengan data yang sudah di-decode
                            return parseCatatanHistory(decodedCatatan);
                        }
                    }
                } catch (e2) {
                    console.error('Gagal decode HTML entities:', e2);
                }

                return history;
            }
        }

        // Handle klik tombol restore nilai
        $(document).on('click', '.btn-restore-nilai', function() {
            var $btn = $(this);
            var $td = $btn.closest('td');
            var idNilai = $btn.data('id-nilai') || $td.data('id-nilai');
            var $row = $td.closest('tr');

            if (!idNilai) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'ID Nilai tidak ditemukan'
                });
                return;
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Ambil catatan langsung dari database
            $.ajax({
                url: '<?= base_url('backend/sertifikasi/getCatatan') ?>',
                type: 'POST',
                data: {
                    idNilai: idNilai,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    console.log('Response getCatatan (full):', JSON.stringify(response, null, 2));
                    console.log('Response getCatatan (object):', response);
                    console.log('Response.data:', response.data);
                    console.log('Response.data keys:', Object.keys(response.data || {}));

                    if (!response.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Gagal mengambil catatan dari database'
                        });
                        return;
                    }

                    if (!response.data) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: 'Data catatan tidak ditemukan dalam response'
                        });
                        return;
                    }

                    // Coba ambil catatan dengan berbagai kemungkinan key (case-sensitive)
                    var catatan = '';
                    if (response.data.hasOwnProperty('Catatan') && response.data.Catatan !== undefined && response.data.Catatan !== null) {
                        catatan = response.data.Catatan;
                        console.log('Catatan ditemukan di response.data.Catatan, length:', catatan ? catatan.length : 0);
                    } else if (response.data.hasOwnProperty('catatan') && response.data.catatan !== undefined && response.data.catatan !== null) {
                        catatan = response.data.catatan;
                        console.log('Catatan ditemukan di response.data.catatan, length:', catatan ? catatan.length : 0);
                    } else {
                        console.warn('Catatan tidak ditemukan!');
                        console.log('response.data.Catatan:', response.data.Catatan);
                        console.log('response.data.catatan:', response.data.catatan);
                        console.log('All keys in response.data:', Object.keys(response.data));
                    }

                    // Pastikan catatan adalah string
                    catatan = String(catatan || '');

                    var hasCatatan = response.data.hasCatatan || false;

                    console.log('Catatan final (string):', catatan);
                    console.log('Catatan type:', typeof catatan);
                    console.log('Catatan length:', catatan.length);
                    console.log('Catatan trimmed length:', catatan.trim().length);
                    console.log('Has Catatan (from response):', hasCatatan);
                    console.log('Catatan Length (from response):', response.data.catatanLength || 0);
                    console.log('ID Nilai:', response.data.idNilai);

                    if (!hasCatatan || !catatan || catatan.trim() === '') {
                        var catatanPreview = catatan ? (catatan.length > 200 ? catatan.substring(0, 200) + '...' : catatan) : '(kosong/null)';
                        var isJson = false;
                        try {
                            if (catatan) {
                                JSON.parse(catatan);
                                isJson = true;
                            }
                        } catch (e) {
                            isJson = false;
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            html: 'Tidak ada catatan yang ditemukan untuk nilai ini.<br><br>' +
                                '<strong>Informasi:</strong><br>' +
                                '<small>ID Nilai: ' + (response.data.idNilai || idNilai) + '<br>' +
                                'Panjang catatan: ' + (response.data.catatanLength || 0) + ' karakter<br>' +
                                'Format JSON: ' + (isJson ? 'Valid' : 'Tidak valid/kosong') + '<br><br>' +
                                '<strong>Preview catatan:</strong><br>' +
                                '<pre style="text-align: left; font-size: 0.8em; max-height: 150px; overflow-y: auto; background: #f5f5f5; padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word;">' +
                                (catatanPreview.replace(/</g, '&lt;').replace(/>/g, '&gt;')) +
                                '</pre></small>',
                            width: '600px'
                        });
                        return;
                    }

                    // Catatan ditemukan, lanjutkan parsing

                    // Parse catatan untuk mendapatkan history
                    var history = parseCatatanHistory(catatan);

                    console.log('History setelah parsing:', history);

                    if (history.length === 0) {
                        var catatanPreview = catatan ? (catatan.length > 200 ? catatan.substring(0, 200) + '...' : catatan) : '(kosong)';
                        var isJson = false;
                        try {
                            JSON.parse(catatan);
                            isJson = true;
                        } catch (e) {
                            isJson = false;
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            html: 'Tidak ada history perubahan yang ditemukan.<br><br>' +
                                '<strong>Status:</strong> ' + (isJson ? 'Format JSON valid' : 'Format bukan JSON atau tidak valid') + '<br>' +
                                '<strong>Panjang catatan:</strong> ' + (catatan ? catatan.length : 0) + ' karakter<br><br>' +
                                '<strong>Preview catatan:</strong><br>' +
                                '<pre style="text-align: left; font-size: 0.8em; max-height: 200px; overflow-y: auto; background: #f5f5f5; padding: 10px; border-radius: 5px;">' +
                                (catatanPreview.replace(/</g, '&lt;').replace(/>/g, '&gt;')) +
                                '</pre>',
                            footer: 'Silakan periksa format catatan di database. Format yang diharapkan adalah JSON dengan struktur {"history": [...]}',
                            width: '600px'
                        });
                        return;
                    }

                    // Set ID nilai
                    $('#restoreIdNilai').val(idNilai);
                    $('#restoreIdNilai').data('$td', $td);
                    $('#restoreIdNilai').data('$row', $row);
                    $('#restoreNilaiDipilih').val('');
                    $('#btnConfirmRestore').prop('disabled', true);

                    // Update data-catatan di button dan td untuk referensi selanjutnya
                    // Pastikan catatan tidak kosong sebelum disimpan
                    if (catatan && catatan.trim() !== '') {
                        $btn.attr('data-catatan', catatan);
                        $td.attr('data-catatan', catatan);
                    }

                    // Hitung total perubahan dan restore
                    var totalPerubahan = 0;
                    var totalRestore = 0;
                    history.forEach(function(item) {
                        if (item.isRestore) {
                            totalRestore++;
                        } else if (!item.isOriginal) {
                            totalPerubahan++;
                        }
                    });

                    // Tampilkan history
                    var historyHtml = '<div class="list-group">';
                    if (history.length === 0) {
                        historyHtml += '<div class="alert alert-warning">Tidak ada history perubahan yang ditemukan</div>';
                    } else {
                        // Tampilkan informasi summary
                        if (totalPerubahan > 0 || totalRestore > 0) {
                            historyHtml += '<div class="alert alert-info mb-3">';
                            historyHtml += '<strong>Ringkasan History:</strong> ';
                            if (totalPerubahan > 0) {
                                historyHtml += '<span class="badge badge-secondary">' + totalPerubahan + ' Perubahan</span> ';
                            }
                            if (totalRestore > 0) {
                                historyHtml += '<span class="badge badge-success">' + totalRestore + ' Restore</span> ';
                            }
                            historyHtml += '</div>';
                        }

                        // Tampilkan semua history items
                        history.forEach(function(item, index) {
                            var isSelected = index === 0 ? 'active' : '';
                            var badgeClass = item.isOriginal ? 'badge-primary' : (item.isRestore ? 'badge-success' : 'badge-secondary');
                            var label = '';
                            if (item.isOriginal) {
                                label = 'Nilai Original (Awal)';
                            } else if (item.isRestore) {
                                // Tampilkan nomor restore dengan jelas
                                if (item.nomorUrut) {
                                    label = 'Restore #' + item.nomorUrut;
                                } else {
                                    // Fallback: hitung manual jika nomorUrut tidak ada
                                    var countRestore = 0;
                                    for (var i = 0; i <= index; i++) {
                                        if (history[i].isRestore) countRestore++;
                                    }
                                    label = 'Restore #' + countRestore;
                                }
                            } else {
                                // Tampilkan nomor perubahan dengan jelas
                                if (item.nomorUrut) {
                                    label = 'Perubahan #' + item.nomorUrut;
                                } else {
                                    // Fallback: hitung manual jika nomorUrut tidak ada
                                    var countPerubahan = 0;
                                    for (var i = 0; i <= index; i++) {
                                        if (!history[i].isOriginal && !history[i].isRestore) countPerubahan++;
                                    }
                                    label = 'Perubahan #' + countPerubahan;
                                }
                            }

                            console.log('History item #' + index + ':', {
                                type: item.isOriginal ? 'original' : (item.isRestore ? 'restore' : 'perubahan'),
                                label: label,
                                nilaiSebelum: item.nilaiSebelum,
                                nilaiSesudah: item.nilaiSesudah,
                                nomorUrut: item.nomorUrut
                            });

                            // Tentukan nilai yang akan dikembalikan
                            var nilaiRestore = item.isOriginal ? item.nilaiSebelum : (item.isRestore ? item.nilaiSesudah : item.nilaiSebelum);
                            historyHtml += '<a href="#" class="list-group-item list-group-item-action restore-history-item ' + isSelected + '" data-nilai="' + nilaiRestore + '" data-index="' + index + '">';
                            historyHtml += '<div class="d-flex w-100 justify-content-between align-items-center">';
                            historyHtml += '<div class="flex-grow-1">';
                            historyHtml += '<h6 class="mb-1">';
                            historyHtml += '<span class="badge ' + badgeClass + ' mr-2">' + label + '</span>';
                            var nilaiRestore = item.isOriginal ? item.nilaiSebelum : (item.isRestore ? item.nilaiSesudah : item.nilaiSebelum);
                            historyHtml += 'Kembalikan ke: <strong class="text-primary" style="font-size: 1.1em;">' + nilaiRestore.toFixed(2) + '</strong>';
                            if (item.nilaiSesudah !== null && item.nilaiSesudah !== item.nilaiSebelum && !item.isOriginal && !item.isRestore) {
                                historyHtml += ' <span class="text-muted">(dari ' + item.nilaiSebelum.toFixed(2) + ' menjadi ' + item.nilaiSesudah.toFixed(2) + ')</span>';
                            } else if (item.isRestore && item.nilaiSebelum !== item.nilaiSesudah) {
                                historyHtml += ' <span class="text-muted">(dari ' + item.nilaiSebelum.toFixed(2) + ' menjadi ' + item.nilaiSesudah.toFixed(2) + ')</span>';
                            }
                            historyHtml += '</h6>';
                            if (item.waktu) {
                                historyHtml += '<p class="mb-1 text-muted"><small><i class="fas fa-clock"></i> ' + item.waktu;
                                if (item.diubahOleh) {
                                    historyHtml += ' oleh <strong>' + item.diubahOleh + '</strong>';
                                }
                                historyHtml += '</small></p>';
                            }
                            if (item.sumber) {
                                historyHtml += '<p class="mb-1 text-muted"><small><i class="fas fa-user-tie"></i> Sumber: ' + item.sumber + '</small></p>';
                            }
                            historyHtml += '</div>';
                            historyHtml += '<div class="ml-3">';
                            historyHtml += '<i class="fas fa-check-circle text-success" style="font-size: 1.5rem; opacity: 0;"></i>';
                            historyHtml += '</div>';
                            historyHtml += '</div>';
                            historyHtml += '</a>';
                        });
                    }
                    historyHtml += '</div>';

                    $('#restoreHistoryList').html(historyHtml);

                    // Set nilai pertama sebagai default
                    if (history.length > 0) {
                        var firstItem = history[0];
                        var nilaiDefault = firstItem.isOriginal ? firstItem.nilaiSebelum : (firstItem.isRestore ? firstItem.nilaiSesudah : firstItem.nilaiSebelum);
                        $('#restoreNilaiDipilih').val(nilaiDefault);
                        $('#btnConfirmRestore').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error mengambil catatan:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal mengambil catatan dari database'
                    });
                }
            });
        });

        // Handle klik item history
        $(document).on('click', '.restore-history-item', function(e) {
            e.preventDefault();
            $('.restore-history-item').removeClass('active');
            $(this).addClass('active');

            var nilaiDipilih = parseFloat($(this).data('nilai'));
            $('#restoreNilaiDipilih').val(nilaiDipilih);
            $('#btnConfirmRestore').prop('disabled', false);
        });

        // Handle konfirmasi restore
        $('#btnConfirmRestore').on('click', function() {
            var idNilai = $('#restoreIdNilai').val();
            var nilaiDipilih = parseFloat($('#restoreNilaiDipilih').val());
            var $td = $('#restoreIdNilai').data('$td');
            var $row = $('#restoreIdNilai').data('$row');

            if (!idNilai || isNaN(nilaiDipilih)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silakan pilih nilai yang ingin dikembalikan'
                });
                return;
            }

            // Disable button
            $(this).prop('disabled', true).text('Memproses...');

            // Kirim request restore
            $.ajax({
                url: '<?= base_url('backend/sertifikasi/restoreNilai') ?>',
                type: 'POST',
                data: {
                    idNilai: idNilai,
                    nilaiAsli: nilaiDipilih,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var nilaiBaru = parseFloat(response.data.nilaiSesudah);

                        // Update badge nilai
                        var $badge = $td.find('.badge');
                        var badgeClass;
                        if (nilaiBaru <= 64) {
                            badgeClass = 'danger';
                        } else if (nilaiBaru >= 65 && nilaiBaru < 75) {
                            badgeClass = 'warning';
                        } else if (nilaiBaru >= 75 && nilaiBaru < 85) {
                            badgeClass = 'success';
                        } else {
                            badgeClass = 'primary';
                        }
                        $badge.removeClass('badge-success badge-warning badge-danger badge-info badge-primary')
                            .addClass('badge-' + badgeClass)
                            .text(nilaiBaru.toFixed(2));

                        // Update data-export untuk export
                        $td.attr('data-export', nilaiBaru.toFixed(2));

                        // Update warna icon edit menjadi merah karena sudah ada catatan
                        var $btnEdit = $td.find('.btn-edit-nilai');
                        $btnEdit.addClass('btn-edit-has-catatan');

                        // Hitung ulang jumlah dan rata-rata
                        var totalNilai = 0;
                        var totalMateri = <?= count($all_materi ?? []) ?>;

                        // Ambil semua nilai materi dari row yang sama
                        // Kolom nilai materi adalah yang memiliki data-id-materi
                        $row.find('td[data-id-materi]').each(function() {
                            var $badge = $(this).find('.badge');
                            if ($badge.length > 0) {
                                // Ambil nilai dari badge
                                var nilai = parseFloat($badge.text().trim()) || 0;
                                totalNilai += nilai;
                            } else {
                                // Jika tidak ada badge, nilai = 0
                                totalNilai += 0;
                            }
                        });

                        // Update jumlah (kolom kedua dari akhir)
                        var $jumlahTd = $row.find('td').eq($row.find('td').length - 2);
                        $jumlahTd.find('strong').text(totalNilai.toFixed(2));
                        $jumlahTd.attr('data-export', totalNilai.toFixed(2));

                        // Update rata-rata (kolom terakhir)
                        // Rata-rata = total nilai / jumlah semua materi
                        var rataRata = totalMateri > 0 ? totalNilai / totalMateri : 0;
                        var $rataRataTd = $row.find('td').last();
                        $rataRataTd.find('strong').text(rataRata.toFixed(2));
                        $rataRataTd.attr('data-export', rataRata.toFixed(2));

                        // Update icon restore jika ada catatan baru
                        // Ambil catatan terbaru dari database untuk menampilkan icon restore
                        $.ajax({
                            url: '<?= base_url('backend/sertifikasi/getCatatan') ?>',
                            type: 'POST',
                            data: {
                                idNilai: idNilai,
                                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                            },
                            dataType: 'json',
                            success: function(catatanResponse) {
                                if (catatanResponse.success && catatanResponse.data.hasCatatan) {
                                    // Gunakan 'Catatan' (capital C) sesuai dengan response dari controller
                                    var catatan = catatanResponse.data.Catatan || catatanResponse.data.catatan || '';
                                    
                                    // Tampilkan icon restore jika ada catatan
                                    var $restoreBtn = $td.find('.btn-restore-nilai');
                                    if ($restoreBtn.length === 0) {
                                        // Buat button restore jika belum ada
                                        var restoreBtnHtml = '<button type="button" class="btn btn-sm btn-restore-nilai" data-id-nilai="' + idNilai + '" data-toggle="modal" data-target="#modalRestoreNilai" title="Kembalikan ke Nilai Sebelumnya" style="background-color: transparent; border: none; padding: 0.25rem 0.5rem; margin-left: 2px;">';
                                        restoreBtnHtml += '<i class="fas fa-undo" style="color: #28a745;"></i>';
                                        restoreBtnHtml += '</button>';
                                        var $btnEdit = $td.find('.btn-edit-nilai');
                                        $btnEdit.after(restoreBtnHtml);
                                        
                                        // Update data-catatan di td
                                        $td.attr('data-catatan', catatan);
                                    } else {
                                        // Update data-catatan di button dan td
                                        $restoreBtn.attr('data-catatan', catatan);
                                        $td.attr('data-catatan', catatan);
                                    }
                                }
                            },
                            error: function() {
                                console.error('Gagal mengambil catatan dari database setelah restore');
                            }
                        });

                        // Tutup modal
                        $('#modalRestoreNilai').modal('hide');

                        // Tampilkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Nilai berhasil dikembalikan ke ' + nilaiDipilih.toFixed(2),
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        // TIDAK reload halaman, tabel sudah di-update langsung
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Gagal mengembalikan nilai'
                        });
                        $('#btnConfirmRestore').prop('disabled', false).text('Kembalikan Nilai');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);

                    var errorMsg = 'Terjadi kesalahan saat mengembalikan nilai';
                    if (xhr.responseJSON) {
                        errorMsg = xhr.responseJSON.message || errorMsg;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                    $('#btnConfirmRestore').prop('disabled', false).text('Kembalikan Nilai');
                }
            });
        });

        // Reset modal restore saat ditutup
        $('#modalRestoreNilai').on('hidden.bs.modal', function() {
            $('#restoreHistoryList').html('');
            $('#restoreIdNilai').val('');
            $('#restoreNilaiDipilih').val('');
            $('#btnConfirmRestore').prop('disabled', true).text('Kembalikan Nilai');
        });

        // Reset form saat modal ditutup
        $('#modalEditNilai').on('hidden.bs.modal', function() {
            $('#formEditNilai')[0].reset();
            $('#btnSaveNilai').prop('disabled', false).text('Simpan');
        });
    });
</script>
<style>
    .dt-buttons {
        margin-bottom: 10px;
    }

    .dt-buttons .btn {
        margin-right: 5px;
    }

    .btn-edit-nilai {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        background-color: transparent !important;
        border: none !important;
        color: #000 !important;
    }

    .btn-edit-nilai:hover {
        background-color: rgba(0, 0, 0, 0.1) !important;
        color: #000 !important;
    }

    .btn-edit-nilai i {
        color: #000 !important;
    }

    /* Warna icon edit jika ada catatan (sudah pernah diubah) */
    .btn-edit-has-catatan i {
        color: #dc3545 !important;
        /* Warna danger (merah) */
    }

    .btn-edit-has-catatan:hover i {
        color: #c82333 !important;
        /* Warna danger lebih gelap saat hover */
    }

    /* Styling untuk history restore */
    .restore-history-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .restore-history-item:hover {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }

    .restore-history-item.active {
        background-color: #e7f3ff;
        border-left: 4px solid #007bff;
        font-weight: 500;
    }

    .restore-history-item.active .fa-check-circle {
        opacity: 1 !important;
    }

    .restore-history-item h6 {
        margin-bottom: 0.5rem;
    }

    .restore-history-item .badge {
        font-size: 0.75rem;
    }
</style>
<?= $this->endSection(); ?>