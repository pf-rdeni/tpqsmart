<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-ol"></i> Kriteria Penilaian - <?= esc($cabang['NamaCabang']) ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary mr-1" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddKriteria">
                                <i class="fas fa-plus"></i> Tambah Kriteria
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-<?= abs($total_bobot - 100) < 0.01 ? 'success' : 'warning' ?>">
                            <i class="fas fa-info-circle"></i> 
                            Total Bobot: <strong><?= $total_bobot ?>%</strong>
                            <?php if (abs($total_bobot - 100) >= 0.01): ?>
                                <br><small class="text-danger">Total bobot harus = 100%</small>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="100">Bobot (%)</th>
                                        <th width="120">Range Nilai</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($kriteria_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada kriteria</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($kriteria_list as $kriteria): ?>
                                            <tr>
                                                <td class="text-center"><?= $kriteria['Urutan'] ?></td>
                                                <td><?= esc($kriteria['NamaKriteria']) ?></td>
                                                <td class="text-center"><strong><?= $kriteria['Bobot'] ?>%</strong></td>
                                                <td class="text-center"><?= $kriteria['NilaiMin'] ?> - <?= $kriteria['NilaiMax'] ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-warning btn-edit-kriteria"
                                                                data-id="<?= $kriteria['id'] ?>"
                                                                data-nama="<?= esc($kriteria['NamaKriteria']) ?>"
                                                                data-bobot="<?= $kriteria['Bobot'] ?>"
                                                                data-min="<?= $kriteria['NilaiMin'] ?>"
                                                                data-max="<?= $kriteria['NilaiMax'] ?>"
                                                                data-urutan="<?= $kriteria['Urutan'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-delete-kriteria" data-id="<?= $kriteria['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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

<!-- Modal Add Kriteria -->
<div class="modal fade" id="modalAddKriteria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Tambah Kriteria</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formAddKriteria">
                <div class="modal-body">
                    <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                    <div class="form-group">
                        <label>Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="NamaKriteria" required placeholder="Contoh: Tajwid, Makhraj, Adab">
                    </div>
                    <div class="form-group">
                        <label>Bobot (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="Bobot" step="0.01" min="0" max="100" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Min</label>
                                <input type="number" class="form-control" name="NilaiMin" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Max</label>
                                <input type="number" class="form-control" name="NilaiMax" value="100">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="Urutan" value="<?= count($kriteria_list) + 1 ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kriteria -->
<div class="modal fade" id="modalEditKriteria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Kriteria</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditKriteria">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editKriteriaId">
                    <div class="form-group">
                        <label>Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="NamaKriteria" id="editNamaKriteria" required>
                    </div>
                    <div class="form-group">
                        <label>Bobot (%)</label>
                        <input type="number" class="form-control" name="Bobot" id="editBobot" step="0.01">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Min</label>
                                <input type="number" class="form-control" name="NilaiMin" id="editNilaiMin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Max</label>
                                <input type="number" class="form-control" name="NilaiMax" id="editNilaiMax">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="Urutan" id="editUrutan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#formAddKriteria').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('backend/perlombaan/storeKriteria') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    $('.btn-edit-kriteria').click(function() {
        $('#editKriteriaId').val($(this).data('id'));
        $('#editNamaKriteria').val($(this).data('nama'));
        $('#editBobot').val($(this).data('bobot'));
        $('#editNilaiMin').val($(this).data('min'));
        $('#editNilaiMax').val($(this).data('max'));
        $('#editUrutan').val($(this).data('urutan'));
        $('#modalEditKriteria').modal('show');
    });

    $('#formEditKriteria').submit(function(e) {
        e.preventDefault();
        var id = $('#editKriteriaId').val();
        $.ajax({
            url: '<?= base_url('backend/perlombaan/updateKriteria') ?>/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    $('.btn-delete-kriteria').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus kriteria ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/perlombaan/deleteKriteria') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
