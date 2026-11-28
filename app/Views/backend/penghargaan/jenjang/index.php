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

    <div class="row">
        <!-- Form Tambah Mapping Jenjang -->
        <div class="col-md-5">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Mapping Jenjang</h3>
                </div>
                <form action="<?= base_url('backend/penghargaan/jenjang/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Jenjang</label>
                            <input type="text" name="NamaJenjang" class="form-control" placeholder="Misal: Jenjang Dasar / Menengah / Atas" required>
                        </div>
                        <div class="form-group">
                            <label>Urutan Tampil</label>
                            <input type="number" name="Urutan" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kelas yang Termasuk</label>
                            <div class="border rounded p-2" style="max-height: 240px; overflow-y: auto;">
                                <?php if (empty($listKelas)): ?>
                                    <p class="text-muted text-sm mb-0">Belum ada data kelas.</p>
                                <?php else: ?>
                                    <?php foreach ($listKelas as $k): ?>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input" id="kls_<?= esc($k['IdKelas']) ?>" name="IdKelas[]" value="<?= esc($k['IdKelas']) ?>">
                                            <label class="custom-control-label" for="kls_<?= esc($k['IdKelas']) ?>">
                                                <?= esc($k['NamaKelas']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Mapping
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Mapping -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-layer-group text-info mr-2"></i>Daftar Jenjang & Kelas</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0 text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px;">Urutan</th>
                                <th>Nama Jenjang</th>
                                <th>Kelas</th>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listJenjang)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada mapping jenjang kelas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listJenjang as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= esc($row['Urutan']) ?></td>
                                        <td><strong><?= esc($row['NamaJenjang']) ?></strong></td>
                                        <td><span class="badge badge-info"><?= esc($row['NamaKelas'] ?? $row['IdKelas']) ?></span></td>
                                        <td class="text-center">
                                            <form action="<?= base_url('backend/penghargaan/jenjang/delete/' . $row['Id']) ?>" method="post" onsubmit="return confirm('Hapus mapping ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                            </form>
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

<?= $this->endSection(); ?>
