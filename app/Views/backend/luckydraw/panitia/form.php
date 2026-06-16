<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url('backend/luckydraw/panitia') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Data Panitia</h6>
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
                $isEdit = isset($panitia);
                $action = $isEdit ? base_url('backend/luckydraw/panitia/update/' . $panitia->id) : base_url('backend/luckydraw/panitia/store');
            ?>

            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group row">
                    <label for="fullname" class="col-sm-2 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?= old('fullname', $isEdit ? $panitia->fullname : '') ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $isEdit ? $panitia->username : '') ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?></label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password" <?= !$isEdit ? 'required' : '' ?>>
                        <?php if ($isEdit): ?>
                            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="group_id" class="col-sm-2 col-form-label">Peran (Grup) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-control" id="group_id" name="group_id" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="10" <?= old('group_id', $isEdit ? $panitia->group_id : '') == 10 ? 'selected' : '' ?>>Panitia Pemenang</option>
                            <option value="11" <?= old('group_id', $isEdit ? $panitia->group_id : '') == 11 ? 'selected' : '' ?>>Panitia Verifikasi</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Tugaskan ke Kegiatan <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <?php 
                            $assigned = old('id_kegiatan', $isEdit ? $panitia->assigned_kegiatan : []);
                            if (!is_array($assigned)) $assigned = [$assigned];
                        ?>
                        <?php foreach($kegiatan as $k): ?>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="kegiatan_<?= $k->id ?>" name="id_kegiatan[]" value="<?= $k->id ?>" <?= in_array($k->id, $assigned) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="kegiatan_<?= $k->id ?>"><?= esc($k->nama_kegiatan) ?> (<?= esc($k->status) ?>)</label>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($kegiatan)): ?>
                            <div class="alert alert-warning mb-0">Belum ada kegiatan yang terdaftar. <a href="<?= base_url('backend/luckydraw/kegiatan/create') ?>">Buat kegiatan baru</a> terlebih dahulu.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
