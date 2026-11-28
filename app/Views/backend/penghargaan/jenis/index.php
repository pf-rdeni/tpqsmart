<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold"><i class="fas fa-tags text-primary mr-2"></i>Master Jenis Penghargaan</h3>
            <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#modalAddJenis">
                <i class="fas fa-plus mr-1"></i> Tambah Jenis Custom
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm" id="tableJenis">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th style="width: 90px;">Kode</th>
                            <th>Nama Penghargaan</th>
                            <th>Kategori</th>
                            <th>Momen</th>
                            <th class="text-center" style="width: 90px;">Sumber</th>
                            <th class="text-center" style="width: 90px;">Status</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($listJenis as $j): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge badge-light border font-weight-bold"><?= esc($j['Kode']) ?></span></td>
                                <td>
                                    <strong><?= esc($j['NamaPenghargaan']) ?></strong>
                                    <?php if (!empty($j['Deskripsi'])): ?>
                                        <small class="text-muted d-block"><?= esc($j['Deskripsi']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $katBadge = [
                                        'akademik'  => 'primary',
                                        'kehadiran' => 'success',
                                        'gabungan'  => 'warning',
                                        'khusus'    => 'info',
                                    ][$j['Kategori']] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?= $katBadge ?>"><?= esc(ucfirst($j['Kategori'])) ?></span>
                                </td>
                                <td><?= esc(ucfirst($j['Momen'])) ?></td>
                                <td class="text-center">
                                    <?= !empty($j['IsDefault']) ? '<span class="badge badge-secondary">Sistem</span>' : '<span class="badge badge-info">Lembaga</span>' ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $j['IsActive'] ? 'success' : 'secondary' ?> status-badge-<?= $j['Id'] ?>">
                                        <?= $j['IsActive'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-xs btn-edit"
                                        data-id="<?= $j['Id'] ?>"
                                        data-kode="<?= esc($j['Kode']) ?>"
                                        data-nama="<?= esc($j['NamaPenghargaan']) ?>"
                                        data-deskripsi="<?= esc($j['Deskripsi']) ?>"
                                        data-kategori="<?= esc($j['Kategori']) ?>"
                                        data-momen="<?= esc($j['Momen']) ?>"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-<?= $j['IsActive'] ? 'secondary' : 'success' ?> btn-xs btn-toggle"
                                        data-id="<?= $j['Id'] ?>"
                                        title="Toggle Aktif">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <?php if (empty($j['IsDefault'])): ?>
                                        <form action="<?= base_url('backend/penghargaan/jenis/delete/' . $j['Id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jenis penghargaan ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAddJenis" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('backend/penghargaan/jenis/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Tambah Jenis Penghargaan Custom</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode</label>
                    <input type="text" name="Kode" class="form-control" placeholder="Misal: KHS-04" required maxlength="20">
                </div>
                <div class="form-group">
                    <label>Nama Penghargaan</label>
                    <input type="text" name="NamaPenghargaan" class="form-control" required maxlength="150">
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Kategori</label>
                        <select name="Kategori" class="form-control" required>
                            <option value="akademik">Akademik</option>
                            <option value="kehadiran">Kehadiran</option>
                            <option value="gabungan">Gabungan</option>
                            <option value="khusus">Khusus</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Momen</label>
                        <select name="Momen" class="form-control" required>
                            <option value="ganjil">Semester Ganjil</option>
                            <option value="genap">Semester Genap</option>
                            <option value="kenaikan">Kenaikan Kelas</option>
                            <option value="munaqosah">Pasca Munaqosah</option>
                            <option value="kapansaja">Kapan Saja</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="Deskripsi" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditJenis" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditJenis" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Edit Jenis Penghargaan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode</label>
                    <input type="text" name="Kode" id="editKode" class="form-control" required maxlength="20">
                </div>
                <div class="form-group">
                    <label>Nama Penghargaan</label>
                    <input type="text" name="NamaPenghargaan" id="editNama" class="form-control" required maxlength="150">
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Kategori</label>
                        <select name="Kategori" id="editKategori" class="form-control" required>
                            <option value="akademik">Akademik</option>
                            <option value="kehadiran">Kehadiran</option>
                            <option value="gabungan">Gabungan</option>
                            <option value="khusus">Khusus</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Momen</label>
                        <select name="Momen" id="editMomen" class="form-control" required>
                            <option value="ganjil">Semester Ganjil</option>
                            <option value="genap">Semester Genap</option>
                            <option value="kenaikan">Kenaikan Kelas</option>
                            <option value="munaqosah">Pasca Munaqosah</option>
                            <option value="kapansaja">Kapan Saja</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="Deskripsi" id="editDeskripsi" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('formEditJenis').action = '<?= base_url('backend/penghargaan/jenis/update') ?>/' + this.dataset.id;
            document.getElementById('editKode').value = this.dataset.kode;
            document.getElementById('editNama').value = this.dataset.nama;
            document.getElementById('editDeskripsi').value = this.dataset.deskripsi || '';
            document.getElementById('editKategori').value = this.dataset.kategori;
            document.getElementById('editMomen').value = this.dataset.momen;
            $('#modalEditJenis').modal('show');
        });
    });

    document.querySelectorAll('.btn-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch('<?= base_url('backend/penghargaan/jenis/toggle') ?>/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.location.reload();
                } else {
                    alert(res.message || 'Gagal mengubah status');
                }
            });
        });
    });
});
</script>

<?= $this->endSection(); ?>
