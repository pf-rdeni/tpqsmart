<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-tags text-primary mr-2"></i>Daftar Kategori Surat</h3>
                    <?php if ($isAdmin): ?>
                        <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#modalAddKategori">
                            <i class="fas fa-plus mr-1"></i> Tambah Kategori
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableKategori">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Kategori</th>
                                <th style="width: 100px;">Kode</th>
                                <th style="width: 120px;">Jenis Surat</th>
                                <th style="width: 80px;" class="text-center">Icon</th>
                                <th style="width: 100px;" class="text-center">Status</th>
                                <?php if ($isAdmin): ?>
                                    <th style="width: 140px;" class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($kategori as $k): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($k['NamaKategori']) ?></strong>
                                        <?php if (!empty($k['Deskripsi'])): ?>
                                            <small class="text-muted d-block"><?= esc($k['Deskripsi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-light border font-weight-bold"><?= esc($k['Kode']) ?></span></td>
                                    <td>
                                        <?php if ($k['JenisSurat'] === 'Keluar'): ?>
                                            <span class="badge badge-primary"><i class="fas fa-arrow-up"></i> Keluar</span>
                                        <?php elseif ($k['JenisSurat'] === 'Masuk'): ?>
                                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                                        <?php else: ?>
                                            <span class="badge badge-info"><i class="fas fa-arrows-alt-v"></i> Keduanya</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= esc($k['Warna']) ?> p-2">
                                            <i class="<?= esc($k['Icon']) ?>"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($k['IsActive']): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($isAdmin): ?>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-xs btn-edit" 
                                                    data-id="<?= $k['Id'] ?>"
                                                    data-nama="<?= esc($k['NamaKategori']) ?>"
                                                    data-kode="<?= esc($k['Kode']) ?>"
                                                    data-jenis="<?= esc($k['JenisSurat']) ?>"
                                                    data-deskripsi="<?= esc($k['Deskripsi']) ?>"
                                                    data-icon="<?= esc($k['Icon']) ?>"
                                                    data-warna="<?= esc($k['Warna']) ?>"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-<?= $k['IsActive'] ? 'secondary' : 'success' ?> btn-xs btn-toggle"
                                                    data-id="<?= $k['Id'] ?>"
                                                    title="<?= $k['IsActive'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs btn-delete" 
                                                    data-id="<?= $k['Id'] ?>"
                                                    data-nama="<?= esc($k['NamaKategori']) ?>"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalAddKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Kategori Surat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKategori">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="NamaKategori" class="form-control" placeholder="Contoh: Undangan Rapat" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Kode Singkat <span class="text-danger">*</span></label>
                            <input type="text" name="Kode" class="form-control text-uppercase" placeholder="Contoh: UND" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jenis Surat <span class="text-danger">*</span></label>
                            <select name="JenisSurat" class="form-control" required>
                                <option value="Keluar">Surat Keluar</option>
                                <option value="Masuk">Surat Masuk</option>
                                <option value="Keduanya">Keduanya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Icon FontAwesome</label>
                            <input type="text" name="Icon" class="form-control" value="fas fa-envelope" placeholder="fas fa-envelope">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Warna Badge</label>
                            <select name="Warna" class="form-control">
                                <option value="primary">Biru (Primary)</option>
                                <option value="success">Hijau (Success)</option>
                                <option value="warning">Kuning (Warning)</option>
                                <option value="danger">Merah (Danger)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="secondary">Abu-abu (Secondary)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="Deskripsi" class="form-control" rows="2" placeholder="Keterangan singkat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Kategori Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKategori">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="NamaKategori" id="editNama" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Kode Singkat <span class="text-danger">*</span></label>
                            <input type="text" name="Kode" id="editKode" class="form-control text-uppercase" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jenis Surat <span class="text-danger">*</span></label>
                            <select name="JenisSurat" id="editJenis" class="form-control" required>
                                <option value="Keluar">Surat Keluar</option>
                                <option value="Masuk">Surat Masuk</option>
                                <option value="Keduanya">Keduanya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Icon FontAwesome</label>
                            <input type="text" name="Icon" id="editIcon" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Warna Badge</label>
                            <select name="Warna" id="editWarna" class="form-control">
                                <option value="primary">Biru (Primary)</option>
                                <option value="success">Hijau (Success)</option>
                                <option value="warning">Kuning (Warning)</option>
                                <option value="danger">Merah (Danger)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="secondary">Abu-abu (Secondary)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="Deskripsi" id="editDeskripsi" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold" id="btnSaveEdit">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#tableKategori').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
        });
    }

    // Submit Add Kategori
    $('#formAddKategori').on('submit', function(e) {
        e.preventDefault();
        $('#btnSaveAdd').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/administrasi/kategori/store') ?>',
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
                $('#btnSaveAdd').prop('disabled', false).text('Simpan');
            }
        });
    });

    // Populate Edit Modal
    $('.btn-edit').on('click', function() {
        $('#editId').val($(this).data('id'));
        $('#editNama').val($(this).data('nama'));
        $('#editKode').val($(this).data('kode'));
        $('#editJenis').val($(this).data('jenis'));
        $('#editDeskripsi').val($(this).data('deskripsi'));
        $('#editIcon').val($(this).data('icon'));
        $('#editWarna').val($(this).data('warna'));
        $('#modalEditKategori').modal('show');
    });

    // Submit Edit Kategori
    $('#formEditKategori').on('submit', function(e) {
        e.preventDefault();
        let id = $('#editId').val();
        $('#btnSaveEdit').prop('disabled', true).text('Memperbarui...');

        $.ajax({
            url: '<?= base_url('backend/administrasi/kategori/update') ?>/' + id,
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
                $('#btnSaveEdit').prop('disabled', false).text('Perbarui');
            }
        });
    });

    // Toggle Active
    $('.btn-toggle').on('click', function() {
        let id = $(this).data('id');
        $.post('<?= base_url('backend/administrasi/kategori/toggle') ?>/' + id, function(res) {
            if (res.success) {
                location.reload();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json');
    });

    // Delete Kategori
    $('.btn-delete').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Kategori?',
            text: 'Kategori "' + nama + '" akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/kategori/delete') ?>/' + id, function(res) {
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
