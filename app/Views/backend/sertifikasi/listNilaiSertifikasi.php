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
                                                        $badgeClass = '';
                                                        $displayNilai = 0;

                                                        if ($nilaiMateri !== null && $nilaiMateri !== '') {
                                                            $displayNilai = floatval($nilaiMateri);
                                                            $badgeClass = $displayNilai >= 70 ? 'success' : ($displayNilai >= 60 ? 'warning' : 'danger');
                                                        } else {
                                                            // Jika belum ada nilai, tampilkan 0 dengan badge merah
                                                            $displayNilai = 0;
                                                            $badgeClass = 'danger';
                                                        }
                                                        ?>
                                                        <td class="text-center" data-export="<?= number_format($displayNilai, 2) ?>" data-id-nilai="<?= esc($idNilai) ?>" data-id-materi="<?= esc($materi['IdMateri']) ?>" data-nama-materi="<?= esc($materi['NamaMateri']) ?>" data-no-peserta="<?= esc($peserta['noTest']) ?>" data-nama-guru="<?= esc($peserta['NamaGuru']) ?>">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <span class="badge badge-<?= $badgeClass ?> mr-2">
                                                                    <?= number_format($displayNilai, 2) ?>
                                                                </span>
                                                                <?php if ($idNilai): ?>
                                                                    <button type="button" class="btn btn-sm btn-primary btn-edit-nilai" data-toggle="modal" data-target="#modalEditNilai" title="Edit Nilai">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
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
                'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json',
                'buttons': {
                    excel: 'Export ke Excel',
                    pdf: 'Export ke PDF'
                }
            }
        });

        // Handle klik tombol edit nilai
        $(document).on('click', '.btn-edit-nilai', function() {
            var $td = $(this).closest('td');
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
        });

        // Handle simpan nilai
        $('#btnSaveNilai').on('click', function() {
            var idNilai = $('#editIdNilai').val();
            var nilaiBaru = $('#editNilaiBaru').val();

            // Validasi
            if (!idNilai) {
                alert('ID Nilai tidak ditemukan');
                return;
            }

            if (!nilaiBaru || nilaiBaru === '') {
                alert('Nilai baru harus diisi');
                return;
            }

            var nilaiFloat = parseFloat(nilaiBaru);
            if (isNaN(nilaiFloat) || nilaiFloat < 0 || nilaiFloat > 100) {
                alert('Nilai harus berupa angka antara 0-100');
                return;
            }

            // Disable button
            $(this).prop('disabled', true).text('Menyimpan...');

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
                        // Tutup modal
                        $('#modalEditNilai').modal('hide');
                        
                        // Reload halaman untuk update data
                        location.reload();
                    } else {
                        var errorMsg = response.message || 'Gagal mengupdate nilai';
                        if (response.details) {
                            errorMsg += '\nDetail: ' + response.details;
                        }
                        alert(errorMsg);
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
                    
                    alert(errorMsg);
                    $('#btnSaveNilai').prop('disabled', false).text('Simpan');
                }
            });
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
    }
</style>
<?= $this->endSection(); ?>