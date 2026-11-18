<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-address-book text-success mr-2"></i>Database Tujuan Surat
                    </h3>
                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAddTujuan">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Tujuan
                        </button>
                    </div>
                </div>

                <!-- Nav Tabs Kategori Filter -->
                <div class="card-body pb-0">
                    <ul class="nav nav-pills" id="filterTujuanTabs">
                        <li class="nav-item">
                            <a class="nav-link active filter-tab" href="javascript:void(0)" data-filter="">Semua Kategori</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-tab" href="javascript:void(0)" data-filter="Pejabat Pemerintah">Pejabat Pemerintah</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-tab" href="javascript:void(0)" data-filter="Lembaga">Lembaga</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-tab" href="javascript:void(0)" data-filter="Internal">Internal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filter-tab" href="javascript:void(0)" data-filter="Perorangan">Perorangan</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableTujuan">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Tujuan</th>
                                <th>Kategori</th>
                                <th>Jabatan</th>
                                <th>Instansi</th>
                                <th>Alamat / Kota</th>
                                <th>Kontak</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($tujuan as $t): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($t['NamaTujuan']) ?></strong></td>
                                    <td>
                                        <span class="badge badge-info"><?= esc($t['KategoriTujuan']) ?></span>
                                    </td>
                                    <td><?= esc($t['Jabatan'] ?: '-') ?></td>
                                    <td><?= esc($t['Instansi'] ?: '-') ?></td>
                                    <td>
                                        <?= esc($t['Alamat'] ?: '') ?>
                                        <?= !empty($t['Kota']) ? '<br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> ' . esc($t['Kota']) . '</small>' : '' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($t['NoHp'])): ?>
                                            <div><i class="fas fa-phone-alt text-success fa-xs mr-1"></i><?= esc($t['NoHp']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($t['Email'])): ?>
                                            <div><i class="fas fa-envelope text-primary fa-xs mr-1"></i><?= esc($t['Email']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-xs btn-edit-tujuan"
                                                data-id="<?= $t['Id'] ?>"
                                                data-nama="<?= esc($t['NamaTujuan']) ?>"
                                                data-kategori="<?= esc($t['KategoriTujuan']) ?>"
                                                data-jabatan="<?= esc($t['Jabatan']) ?>"
                                                data-instansi="<?= esc($t['Instansi']) ?>"
                                                data-alamat="<?= esc($t['Alamat']) ?>"
                                                data-kota="<?= esc($t['Kota']) ?>"
                                                data-nohp="<?= esc($t['NoHp']) ?>"
                                                data-email="<?= esc($t['Email']) ?>"
                                                data-idtpq="<?= esc($t['IdTpq']) ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs btn-delete-tujuan"
                                                data-id="<?= $t['Id'] ?>"
                                                data-nama="<?= esc($t['NamaTujuan']) ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Tujuan -->
<div class="modal fade" id="modalAddTujuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Tujuan Surat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddTujuan">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Tujuan / Pejabat <span class="text-danger">*</span></label>
                            <input type="text" name="NamaTujuan" class="form-control" placeholder="Contoh: Sudarno, S.Pd" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kategori Tujuan <span class="text-danger">*</span></label>
                            <select name="KategoriTujuan" class="form-control" required>
                                <option value="Pejabat Pemerintah">Pejabat Pemerintah</option>
                                <option value="Lembaga">Lembaga</option>
                                <option value="Internal">Internal</option>
                                <option value="Perorangan" selected>Perorangan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jabatan</label>
                            <input type="text" name="Jabatan" class="form-control" placeholder="Contoh: Kepala Bagian KESRA">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Instansi / Lembaga</label>
                            <input type="text" name="Instansi" class="form-control" placeholder="Contoh: Kantor Bupati Bintan">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label>Alamat Lengkap</label>
                            <textarea name="Alamat" class="form-control" rows="2" placeholder="Jalan, No, RT/RW..."></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Kota / Kabupaten</label>
                            <input type="text" name="Kota" class="form-control" placeholder="Contoh: Bintan">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Handphone / WhatsApp</label>
                            <input type="text" name="NoHp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-control" placeholder="nama@email.com">
                        </div>
                    </div>
                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label>Terikat TPQ Tertentu (Kosongkan jika Umum / Global)</label>
                            <select name="IdTpq" class="form-control">
                                <option value="">-- Umum / Semua Lembaga --</option>
                                <?php foreach ($listTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold" id="btnSaveAddTujuan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Tujuan -->
<div class="modal fade" id="modalEditTujuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Tujuan Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditTujuan">
                <input type="hidden" name="id" id="editTujuanId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Tujuan / Pejabat <span class="text-danger">*</span></label>
                            <input type="text" name="NamaTujuan" id="editTujuanNama" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kategori Tujuan <span class="text-danger">*</span></label>
                            <select name="KategoriTujuan" id="editTujuanKategori" class="form-control" required>
                                <option value="Pejabat Pemerintah">Pejabat Pemerintah</option>
                                <option value="Lembaga">Lembaga</option>
                                <option value="Internal">Internal</option>
                                <option value="Perorangan">Perorangan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jabatan</label>
                            <input type="text" name="Jabatan" id="editTujuanJabatan" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Instansi / Lembaga</label>
                            <input type="text" name="Instansi" id="editTujuanInstansi" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label>Alamat Lengkap</label>
                            <textarea name="Alamat" id="editTujuanAlamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Kota / Kabupaten</label>
                            <input type="text" name="Kota" id="editTujuanKota" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Handphone / WhatsApp</label>
                            <input type="text" name="NoHp" id="editTujuanNoHp" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input type="email" name="Email" id="editTujuanEmail" class="form-control">
                        </div>
                    </div>
                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label>Terikat TPQ Tertentu (Kosongkan jika Umum / Global)</label>
                            <select name="IdTpq" id="editTujuanIdTpq" class="form-control">
                                <option value="">-- Umum / Semua Lembaga --</option>
                                <?php foreach ($listTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold" id="btnSaveEditTujuan">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let dtTujuan = null;
    if ($.fn.DataTable) {
        dtTujuan = $('#tableTujuan').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
        });

        // Filter tab category
        $('.filter-tab').on('click', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            let filterVal = $(this).data('filter');
            dtTujuan.column(2).search(filterVal).draw();
        });
    }

    // Submit Add Tujuan
    $('#formAddTujuan').on('submit', function(e) {
        e.preventDefault();
        $('#btnSaveAddTujuan').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/administrasi/tujuan/store') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                } else {
                    let errMsg = res.message;
                    if (res.errors) {
                        errMsg += '<br>' + Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', errMsg, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            },
            complete: function() {
                $('#btnSaveAddTujuan').prop('disabled', false).text('Simpan');
            }
        });
    });

    // Populate Edit Modal
    $('.btn-edit-tujuan').on('click', function() {
        $('#editTujuanId').val($(this).data('id'));
        $('#editTujuanNama').val($(this).data('nama'));
        $('#editTujuanKategori').val($(this).data('kategori'));
        $('#editTujuanJabatan').val($(this).data('jabatan'));
        $('#editTujuanInstansi').val($(this).data('instansi'));
        $('#editTujuanAlamat').val($(this).data('alamat'));
        $('#editTujuanKota').val($(this).data('kota'));
        $('#editTujuanNoHp').val($(this).data('nohp'));
        $('#editTujuanEmail').val($(this).data('email'));
        $('#editTujuanIdTpq').val($(this).data('idtpq'));
        $('#modalEditTujuan').modal('show');
    });

    // Submit Edit Tujuan
    $('#formEditTujuan').on('submit', function(e) {
        e.preventDefault();
        let id = $('#editTujuanId').val();
        $('#btnSaveEditTujuan').prop('disabled', true).text('Memperbarui...');

        $.ajax({
            url: '<?= base_url('backend/administrasi/tujuan/update') ?>/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                } else {
                    let errMsg = res.message;
                    if (res.errors) {
                        errMsg += '<br>' + Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', errMsg, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            },
            complete: function() {
                $('#btnSaveEditTujuan').prop('disabled', false).text('Perbarui');
            }
        });
    });

    // Delete Tujuan
    $('.btn-delete-tujuan').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Tujuan Surat?',
            text: 'Data tujuan "' + nama + '" akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/tujuan/delete') ?>/' + id, function(res) {
                    if (res.success) {
                        Swal.fire('Terhapus', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
