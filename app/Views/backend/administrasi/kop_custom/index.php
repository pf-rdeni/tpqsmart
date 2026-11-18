<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= implode('<br>', session()->getFlashdata('errors')) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-stamp text-info mr-2"></i>Daftar Kop Kepanitiaan / Kegiatan Khusus
                    </h3>
                    <button type="button" class="btn btn-info btn-sm ml-auto font-weight-bold" data-toggle="modal" data-target="#modalAddKop">
                        <i class="fas fa-plus mr-1"></i> Upload Kop Baru
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($kopList)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-image fa-3x mb-3 text-secondary"></i>
                            <h5>Belum ada Kop Kepanitiaan</h5>
                            <p class="mb-3">Upload gambar kop untuk kegiatan panitia, haflah, perlombaan, atau acara khusus lainnya.</p>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalAddKop">
                                <i class="fas fa-upload mr-1"></i> Upload Sekarang
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($kopList as $kop): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm border <?= $kop['IsActive'] ? 'border-info' : 'border-light' ?>">
                                        <!-- Header Image Preview -->
                                        <div class="bg-light p-2 text-center border-bottom" style="height: 120px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                            <?php if (!empty($kop['GambarKop']) && file_exists(FCPATH . 'uploads/kop_custom/' . $kop['GambarKop'])): ?>
                                                <img src="<?= base_url('uploads/kop_custom/' . $kop['GambarKop']) ?>" 
                                                     alt="<?= esc($kop['NamaKop']) ?>" 
                                                     style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-image fa-2x d-block mb-1"></i> Tanpa Gambar</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body py-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title font-weight-bold mb-0 text-truncate" title="<?= esc($kop['NamaKop']) ?>">
                                                    <?= esc($kop['NamaKop']) ?>
                                                </h5>
                                                <?php if ($kop['IsActive']): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Nonaktif</span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="card-text text-muted small mb-0 text-truncate-2">
                                                <?= esc($kop['Keterangan'] ?: 'Tidak ada keterangan.') ?>
                                            </p>
                                        </div>

                                        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-outline-info btn-xs btn-toggle-kop" 
                                                    data-id="<?= $kop['Id'] ?>">
                                                <i class="fas fa-power-off mr-1"></i> <?= $kop['IsActive'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </button>
                                            <div>
                                                <button type="button" class="btn btn-warning btn-xs btn-edit-kop"
                                                        data-id="<?= $kop['Id'] ?>"
                                                        data-nama="<?= esc($kop['NamaKop']) ?>"
                                                        data-keterangan="<?= esc($kop['Keterangan']) ?>"
                                                        data-idtpq="<?= esc($kop['IdTpq']) ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs btn-delete-kop"
                                                        data-id="<?= $kop['Id'] ?>"
                                                        data-nama="<?= esc($kop['NamaKop']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kop -->
<div class="modal fade" id="modalAddKop" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-stamp mr-2"></i>Upload Kop Kepanitiaan Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('backend/administrasi/kop-custom/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kop Kepanitiaan <span class="text-danger">*</span></label>
                        <input type="text" name="NamaKop" class="form-control" placeholder="Contoh: Panitia Haflah Akhirussanah 2026" required>
                    </div>

                    <div class="form-group">
                        <label>Gambar Kop Surat (JPG / PNG, Max 2MB)</label>
                        <input type="file" name="GambarKop" class="form-control-file" accept="image/*" id="fileKopAdd">
                        <small class="form-text text-muted">Gunakan gambar rasio memanjang (header) resolusi minimal 800px lebar.</small>
                    </div>

                    <div class="form-group">
                        <label>Keterangan / Kegiatan</label>
                        <textarea name="Keterangan" class="form-control" rows="2" placeholder="Digunakan untuk kegiatan..."></textarea>
                    </div>

                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label>Lembaga Terkait (Opsional)</label>
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
                    <button type="submit" class="btn btn-info font-weight-bold">Upload & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kop -->
<div class="modal fade" id="modalEditKop" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Kop Kepanitiaan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditKop" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kop Kepanitiaan <span class="text-danger">*</span></label>
                        <input type="text" name="NamaKop" id="editKopNama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Ganti Gambar Kop (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="GambarKop" class="form-control-file" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="Keterangan" id="editKopKeterangan" class="form-control" rows="2"></textarea>
                    </div>

                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label>Lembaga Terkait</label>
                            <select name="IdTpq" id="editKopIdTpq" class="form-control">
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
                    <button type="submit" class="btn btn-warning font-weight-bold">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Populate Edit Kop Modal
    $('.btn-edit-kop').on('click', function() {
        let id = $(this).data('id');
        $('#formEditKop').attr('action', '<?= base_url('backend/administrasi/kop-custom/update') ?>/' + id);
        $('#editKopNama').val($(this).data('nama'));
        $('#editKopKeterangan').val($(this).data('keterangan'));
        $('#editKopIdTpq').val($(this).data('idtpq'));
        $('#modalEditKop').modal('show');
    });

    // Toggle Active
    $('.btn-toggle-kop').on('click', function() {
        let id = $(this).data('id');
        $.post('<?= base_url('backend/administrasi/kop-custom/toggle') ?>/' + id, function(res) {
            if (res.success) {
                location.reload();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json');
    });

    // Delete Kop
    $('.btn-delete-kop').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Kop Kepanitiaan?',
            text: 'Kop "' + nama + '" dan filenya akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/kop-custom/delete') ?>/' + id, function(res) {
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
