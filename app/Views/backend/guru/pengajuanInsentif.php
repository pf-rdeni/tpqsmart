<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengajuan Insentif Guru</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php if (in_groups('Admin')): ?>
                <div class="row mb-3">
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
                </div>
            <?php endif; ?>
            <table id="tabelPengajuanInsentif" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NIK / Nama</th>
                        <th>Penerima Insentif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($guru as $dataGuru) : ?>
                        <tr data-idtpq="<?= esc($dataGuru['IdTpq'] ?? '') ?>">
                            <td>
                                <?= $dataGuru['IdGuru'] ?><br>
                                <strong><?= ucwords(strtolower($dataGuru['Nama'])) ?></strong><br>
                                <small style="color: #666;"><?= $dataGuru['TempatTugas'] ?? '-' ?></small>
                            </td>
                            <td>
                                <select class="form-control form-control-sm penerima-insentif" data-id-guru="<?= $dataGuru['IdGuru'] ?>">
                                    <option value="">Pilih Penerima Insentif</option>
                                    <option value="Guru Ngaji" <?= ($dataGuru['JenisPenerimaInsentif'] ?? '') == 'Guru Ngaji' ? 'selected' : '' ?>>Guru Ngaji</option>
                                    <option value="Mubaligh" <?= ($dataGuru['JenisPenerimaInsentif'] ?? '') == 'Mubaligh' ? 'selected' : '' ?>>Mubaligh</option>
                                    <option value="Fardu Kifayah" <?= ($dataGuru['JenisPenerimaInsentif'] ?? '') == 'Fardu Kifayah' ? 'selected' : '' ?>>Fardu Kifayah</option>
                                </select>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 5px;">
                                    <!-- Row 1: Buttons -->
                                    <div style="display: flex; gap: 5px; justify-content: space-between;">
                                        <a href="<?= base_url('backend/guru/printSuratPernyataanAsn/' . $dataGuru['IdGuru']) ?>"
                                            class="btn btn-sm btn-primary btn-pdf-action"
                                            style="flex: 1;"
                                            data-id-guru="<?= $dataGuru['IdGuru'] ?>"
                                            target="_blank"
                                            title="Surat Pernyataan Tidak Berstatus ASN">
                                            <i class="fas fa-file-pdf"></i> ASN
                                        </a>
                                        <a href="<?= base_url('backend/guru/printSuratPernyataanInsentif/' . $dataGuru['IdGuru']) ?>"
                                            class="btn btn-sm btn-info btn-pdf-action"
                                            style="flex: 1;"
                                            data-id-guru="<?= $dataGuru['IdGuru'] ?>"
                                            target="_blank"
                                            title="Surat Pernyataan Tidak Sedang Menerima Insentif">
                                            <i class="fas fa-file-pdf"></i> Insentif
                                        </a>
                                        <a href="<?= base_url('backend/guru/printSuratRekomendasi/' . $dataGuru['IdGuru']) ?>"
                                            class="btn btn-sm btn-success btn-pdf-action btn-rekomendasi"
                                            style="flex: 1;"
                                            data-id-guru="<?= $dataGuru['IdGuru'] ?>"
                                            data-penerima-insentif="<?= esc($dataGuru['JenisPenerimaInsentif'] ?? '') ?>"
                                            target="_blank"
                                            title="Surat Rekomendasi">
                                            <i class="fas fa-file-pdf"></i> Rekomendasi
                                        </a>
                                    </div>
                                    <!-- Row 2: Keterangan -->
                                    <div style="display: flex; gap: 5px; justify-content: space-between;">
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Pernyataan Tidak Berstatus ASN</small>
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Pernyataan Tidak Terima Insentif Lain</small>
                                        <small style="font-size: 9px; color: #666; line-height: 1.2; flex: 1; text-align: center;">Surat Rekomendasi Guru TPQ</small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
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

    // Inisialisasi DataTable dengan scroll horizontal
    document.addEventListener('DOMContentLoaded', function() {
        const table = initializeDataTableScrollX("#tabelPengajuanInsentif", [], {
            "pageLength": 25,
            "lengthChange": true
        });

        <?php if (in_groups('Admin')): ?>
            // Filter TPQ untuk Admin
            const filterTpq = $('#filterTpq');
            let customFilterFunction = null;

            // Event handler untuk filter TPQ
            filterTpq.on('change', function() {
                const selectedTpq = $(this).val();

                // Remove existing custom filter if any
                if (customFilterFunction !== null) {
                    $.fn.dataTable.ext.search.pop();
                    customFilterFunction = null;
                }

                if (selectedTpq !== '') {
                    // Create new custom filter function
                    customFilterFunction = function(settings, data, dataIndex) {
                        // Hanya terapkan filter untuk tabel ini
                        if (!settings || !settings.nTable || settings.nTable.id !== 'tabelPengajuanInsentif') {
                            return true;
                        }

                        try {
                            // Dapatkan row node langsung dari DataTable
                            const row = table.row(dataIndex).node();
                            if (!row) {
                                return true;
                            }

                            // Ambil data-idtpq dari row
                            const rowIdTpq = $(row).attr('data-idtpq');

                            // Cek apakah rowIdTpq match dengan selectedTpq
                            return rowIdTpq === selectedTpq;
                        } catch (e) {
                            console.error('Error in custom filter:', e, 'dataIndex:', dataIndex);
                            return true;
                        }
                    };

                    // Add custom filter
                    $.fn.dataTable.ext.search.push(customFilterFunction);
                }

                // Redraw table
                table.draw();
            });
        <?php else: ?>
            // Untuk Operator, tidak perlu filter karena sudah otomatis filtered di server
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>