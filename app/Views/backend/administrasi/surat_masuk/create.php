<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= implode('<br>', session()->getFlashdata('errors')) ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-import text-success mr-2"></i>Formulir Pencatatan Surat Masuk
                    </h3>
                </div>
                <form action="<?= base_url('backend/administrasi/surat-masuk/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Nomor Agenda / Registrasi <span class="text-danger">*</span></label>
                                <input type="text" name="NoAgenda" class="form-control font-weight-bold" 
                                       value="<?= esc(old('NoAgenda', $suggestedAgenda)) ?>" required>
                                <small class="text-muted">Nomor urut arsip surat masuk</small>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal Diterima <span class="text-danger">*</span></label>
                                <input type="date" name="TanggalTerima" class="form-control" 
                                       value="<?= esc(old('TanggalTerima', date('Y-m-d'))) ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Kategori Surat</label>
                                <select name="IdKategori" class="form-control">
                                    <?php foreach ($kategoriList as $k): ?>
                                        <option value="<?= $k['Id'] ?>"><?= esc($k['NamaKategori']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Asal Surat / Nama Instansi Pengirim <span class="text-danger">*</span></label>
                                <input type="text" name="AsalSurat" class="form-control font-weight-bold" 
                                       placeholder="Contoh: Kantor Kemenag Kab. Bintan / Camat" 
                                       value="<?= esc(old('AsalSurat')) ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nomor Surat Pengirim <span class="text-danger">*</span></label>
                                <input type="text" name="NomorSurat" class="form-control" 
                                       placeholder="Nomor yang tertera pada surat fisik" 
                                       value="<?= esc(old('NomorSurat')) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label>Perihal / Hal Surat <span class="text-danger">*</span></label>
                                <input type="text" name="Hal" class="form-control font-weight-bold" 
                                       placeholder="Contoh: Undangan Koordinasi Guru TPQ Se-Kecamatan" 
                                       value="<?= esc(old('Hal')) ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal yang Tertera pada Surat</label>
                                <input type="date" name="Tanggal" class="form-control" 
                                       value="<?= esc(old('Tanggal', date('Y-m-d'))) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Lampiran</label>
                                <input type="text" name="Lampiran" class="form-control" 
                                       placeholder="Contoh: 1 Berkas / 2 Lembar / -" 
                                       value="<?= esc(old('Lampiran')) ?>">
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Upload Berkas Scan Surat (PDF / JPG / PNG, Max 5MB)</label>
                                <input type="file" name="FileScan" class="form-control-file" accept=".pdf,image/*">
                                <small class="text-muted">Lampirkan scan surat fisik agar dapat ditinjau oleh staf penerima disposisi.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan / Ringkasan Isi Surat</label>
                            <textarea name="Catatan" class="form-control" rows="3" placeholder="Tuliskan intisari, waktu kegiatan, tempat, atau catatan penting lainnya..."><?= esc(old('Catatan')) ?></textarea>
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="form-group">
                                <label>Lembaga Terkait</label>
                                <select name="IdTpq" class="form-control">
                                    <?php foreach ($listTpq as $tpq): ?>
                                        <option value="<?= $tpq['IdTpq'] ?>" <?= $tpq['IdTpq'] == $idTpq ? 'selected' : '' ?>>
                                            <?= esc($tpq['NamaTpq']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="<?= base_url('backend/administrasi/surat-masuk') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Surat Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
