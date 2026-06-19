<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Alerts -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Survey Utama</h3>
                    </div>
                    <form action="<?= base_url('backend/survey/store') ?>" method="POST" id="createSurveyForm">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul Survey <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="Contoh: Survey Evaluasi Kinerja Guru Semester Ganjil" 
                                       value="<?= old('title') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi Survey</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Tuliskan tujuan survey, petunjuk pengisian, dll."><?= old('description') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="target_type">Target Responden</label>
                                        <select class="form-control" id="target_type" name="target_type">
                                            <option value="public" <?= old('target_type') === 'public' ? 'selected' : '' ?>>Publik (Siapapun via link)</option>
                                            <option value="tpq" <?= old('target_type') === 'tpq' ? 'selected' : '' ?>>Lembaga TPQ (Operator/Pimpinan)</option>
                                            <option value="guru" <?= old('target_type') === 'guru' ? 'selected' : '' ?>>Guru TPQ</option>
                                            <option value="santri" <?= old('target_type') === 'santri' ? 'selected' : '' ?>>Santri TPQ</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Menentukan pembatasan dan pengelompokan master data yang diizinkan mengisi.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="theme_color">Tema Warna Form</label>
                                        <div class="input-group my-colorpicker2">
                                            <input type="color" class="form-control" id="theme_color" name="theme_color" 
                                                   value="<?= old('theme_color') ?? '#4285F4' ?>" style="height: 38px; padding: 2px;">
                                        </div>
                                        <small class="form-text text-muted">
                                            Warna tema utama pada banner/header form publik.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= base_url('backend/survey') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary ml-auto">
                                Lanjutkan ke Builder <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
