<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><?= $lomba ? 'Edit Lomba' : 'Tambah Lomba Baru' ?></h3>
                    </div>
                    <form action="<?= $lomba ? base_url('backend/perlombaan/updateLomba/' . $lomba['id']) : base_url('backend/perlombaan/storeLomba') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="NamaLomba">Nama Lomba <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="NamaLomba" name="NamaLomba" 
                                       value="<?= old('NamaLomba', $lomba['NamaLomba'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="Deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="Deskripsi" name="Deskripsi" rows="3"><?= old('Deskripsi', $lomba['Deskripsi'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="TanggalMulai">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="TanggalMulai" name="TanggalMulai" 
                                               value="<?= old('TanggalMulai', $lomba['TanggalMulai'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="TanggalSelesai">Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="TanggalSelesai" name="TanggalSelesai" 
                                               value="<?= old('TanggalSelesai', $lomba['TanggalSelesai'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <?php if (in_groups('Admin') && !empty($tpq_list)): ?>
                                <div class="form-group">
                                    <label for="IdTpq">Khusus TPQ (Opsional)</label>
                                    <select class="form-control select2" id="IdTpq" name="IdTpq">
                                        <option value="">-- Untuk Semua TPQ (Pusat) --</option>
                                        <?php foreach ($tpq_list as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>" <?= old('IdTpq', $lomba['IdTpq'] ?? '') == $tpq['IdTpq'] ? 'selected' : '' ?>>
                                                <?= esc($tpq['NamaTpq']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Kosongkan jika lomba untuk semua TPQ</small>
                                </div>
                            <?php endif; ?>

                            <?php if ($lomba): ?>
                                <div class="form-group">
                                    <label for="Status">Status</label>
                                    <select class="form-control" id="Status" name="Status">
                                        <option value="aktif" <?= ($lomba['Status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="selesai" <?= ($lomba['Status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="draft" <?= ($lomba['Status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="<?= base_url('backend/perlombaan') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
