<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url('backend/luckydraw/kegiatan') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Data Kegiatan</h6>
        </div>
        <div class="card-body">
            
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
                $isEdit = isset($kegiatan);
                $action = $isEdit ? base_url('backend/luckydraw/kegiatan/update/' . $kegiatan->id) : base_url('backend/luckydraw/kegiatan/store');
            ?>

            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group row">
                    <label for="nama_kegiatan" class="col-sm-2 col-form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" value="<?= old('nama_kegiatan', $isEdit ? $kegiatan->nama_kegiatan : '') ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tanggal_kegiatan" class="col-sm-2 col-form-label">Tanggal <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?= old('tanggal_kegiatan', $isEdit ? $kegiatan->tanggal_kegiatan : date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tempat_pelaksanaan" class="col-sm-2 col-form-label">Tempat</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tempat_pelaksanaan" name="tempat_pelaksanaan" value="<?= old('tempat_pelaksanaan', $isEdit ? $kegiatan->tempat_pelaksanaan : '') ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="kupon_min" class="col-sm-2 col-form-label">Kupon Minimal <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="kupon_min" name="kupon_min" value="<?= old('kupon_min', $isEdit ? $kegiatan->kupon_min : '1000') ?>" required>
                        <small class="form-text text-muted">Contoh: 1000</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="kupon_max" class="col-sm-2 col-form-label">Kupon Maksimal <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="kupon_max" name="kupon_max" value="<?= old('kupon_max', $isEdit ? $kegiatan->kupon_max : '5000') ?>" required>
                        <small class="form-text text-muted">Contoh: 5000</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" <?= old('status', $isEdit ? $kegiatan->status : '') == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= old('status', $isEdit ? $kegiatan->status : '') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                        <small class="form-text text-muted">Anda dapat mengaktifkan lebih dari satu kegiatan.</small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
