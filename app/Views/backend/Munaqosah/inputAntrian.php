<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Input Antrian Munaqosah</h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('backend/munaqosah/save-antrian') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="NoPeserta">Nomor Peserta <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="NoPeserta" name="NoPeserta"
                                            value="<?= old('NoPeserta') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IdTahunAjaran">Tahun Ajaran <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="IdTahunAjaran" name="IdTahunAjaran"
                                            value="<?= old('IdTahunAjaran', $current_tahun ?? '2024/2025') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="IdGrupMateriUjian">Grup Materi Ujian <span class="text-danger">*</span></label>
                                        <select class="form-control" id="IdGrupMateriUjian" name="IdGrupMateriUjian" required>
                                            <option value="">Pilih Grup</option>
                                            <?php foreach ($groups as $group): ?>
                                                <option value="<?= $group['IdGrupMateriUjian'] ?>"
                                                    <?= old('IdGrupMateriUjian') == $group['IdGrupMateriUjian'] ? 'selected' : '' ?>>
                                                    <?= $group['NamaMateriGrup'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="TypeUjian">Type Ujian <span class="text-danger">*</span></label>
                                        <select class="form-control" id="TypeUjian" name="TypeUjian" required>
                                            <?php foreach ($types as $value => $label): ?>
                                                <option value="<?= $value ?>" <?= old('TypeUjian', 'pra-munaqosah') == $value ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="NamaKategoriMateri">Kategori Materi Ujian <span class="text-danger">*</span></label>
                                        <input type="hidden" id="IdKategoriMateri" name="IdKategoriMateri" value="<?= old('IdKategoriMateri') ?>">
                                        <input type="text" class="form-control" id="NamaKategoriMateri" name="NamaKategoriMateri"
                                            value="<?= old('NamaKategoriMateri') ?>" placeholder="Otomatis dari registrasi" readonly>
                                        <small class="form-text text-muted">Diisi otomatis berdasarkan registrasi peserta.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="Keterangan">Keterangan</label>
                                        <textarea class="form-control" id="Keterangan" name="Keterangan" rows="3"><?= old('Keterangan') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="<?= base_url('backend/munaqosah/antrian') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>