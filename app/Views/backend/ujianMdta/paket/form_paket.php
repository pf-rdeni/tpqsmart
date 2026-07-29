<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling AdminLTE Native Form Control & Custom Select Match */
    .form-control-adminlte, 
    select.form-control, 
    select.custom-select,
    select.form-select {
        height: calc(2.25rem + 2px) !important;
        padding: .375rem .75rem !important;
        font-size: 1rem !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: #495057 !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: .25rem !important;
        box-shadow: none !important;
        appearance: auto !important;
        -webkit-appearance: auto !important;
    }
    select.form-control:focus, 
    select.custom-select:focus,
    select.form-select:focus,
    input.form-control:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-edit text-success me-2"></i>
                    <?= isset($paket) ? 'Edit Paket Soal' : 'Form Konfigurasi Paket Soal Baru' ?>
                </h4>
                <small class="text-muted">Konfigurasi nama paket, materi, kelas, dan aturan dasar pengerjaan</small>
            </div>
            <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Paket
            </a>
        </div>
    </div>

    <!-- Alert Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan pengisian:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Konfigurasi Paket Soal -->
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 text-success fw-bold">
                <i class="fas fa-cog me-2"></i>Pengaturan & Metadata Paket Soal
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="post" action="<?= isset($paket) ? base_url("backend/ujian-mdta/paket/update/{$paket['id']}") : base_url('backend/ujian-mdta/paket/save') ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Nama Paket Soal -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Nama Paket Soal <span class="text-danger">*</span></label>
                        <input type="text" name="NamaPaket" class="form-control form-control-adminlte" placeholder="Contoh: PAKET UJIAN FINAL FIQIH KELAS 1 MDTA" required value="<?= old('NamaPaket', $paket['NamaPaket'] ?? '') ?>">
                        <small class="text-muted">Gunakan nama yang jelas agar mudah diidentifikasi saat membuat jadwal ujian.</small>
                    </div>

                    <!-- Kelas & Materi -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Kelas Target <span class="text-danger">*</span></label>
                        <select name="IdKelas" class="form-control custom-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['IdKelas'] ?>" <?= old('IdKelas', $paket['IdKelas'] ?? '') == $k['IdKelas'] ? 'selected' : '' ?>>
                                    <?= esc($k['NamaKelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Materi Pelajaran <span class="text-danger">*</span></label>
                        <select name="IdMateri" class="form-control custom-select" required>
                            <option value="">-- Pilih Materi Pelajaran --</option>
                            <?php foreach ($materiList as $m): ?>
                                <option value="<?= $m['IdMateri'] ?>" <?= old('IdMateri', $paket['IdMateri'] ?? '') == $m['IdMateri'] ? 'selected' : '' ?>>
                                    <?= esc($m['NamaMateri']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="IdTahunAjaran" class="form-control custom-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($taList as $ta): ?>
                                <option value="<?= $ta ?>" <?= old('IdTahunAjaran', $paket['IdTahunAjaran'] ?? session()->get('IdTahunAjaran')) == $ta ? 'selected' : '' ?>>
                                    <?= convertTahunAjaran($ta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr class="my-3">

                    <!-- Aturan Jawaban & Acak Default -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Mode Pilihan Jawaban <span class="text-danger">*</span></label>
                        <select name="ModeJawaban" class="form-control custom-select">
                            <option value="ABCD" <?= old('ModeJawaban', $paket['ModeJawaban'] ?? 'ABCD') == 'ABCD' ? 'selected' : '' ?>>A - B - C - D (4 Opsi)</option>
                            <option value="ABCDE" <?= old('ModeJawaban', $paket['ModeJawaban'] ?? '') == 'ABCDE' ? 'selected' : '' ?>>A - B - C - D - E (5 Opsi)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Skala Nilai Maksimum</label>
                        <input type="number" name="SkalaNilai" class="form-control form-control-adminlte" value="<?= old('SkalaNilai', $paket['SkalaNilai'] ?? 100) ?>" min="10" max="1000">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Skor Tidak Menjawab</label>
                        <input type="number" step="0.01" name="SkorTidakMenjawab" class="form-control form-control-adminlte" value="<?= old('SkorTidakMenjawab', $paket['SkorTidakMenjawab'] ?? 0) ?>">
                        <small class="text-muted">Default 0</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Status Paket</label>
                        <select name="Status" class="form-control custom-select">
                            <option value="aktif" <?= old('Status', $paket['Status'] ?? 'aktif') == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= old('Status', $paket['Status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Non-Aktif</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light p-3 border shadow-xs rounded-3">
                            <label class="form-label fw-bold text-dark mb-1">Acak Soal Default</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="AcakSoalDefault" id="AcakSoalDefault" value="1" <?= old('AcakSoalDefault', $paket['AcakSoalDefault'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label fw-semibold text-secondary" for="AcakSoalDefault">Acak urutan soal secara otomatis saat santri mengerjakan ujian</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light p-3 border shadow-xs rounded-3">
                            <label class="form-label fw-bold text-dark mb-1">Acak Jawaban Default</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="AcakJawabanDefault" id="AcakJawabanDefault" value="1" <?= old('AcakJawabanDefault', $paket['AcakJawabanDefault'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label fw-semibold text-secondary" for="AcakJawabanDefault">Acak pilihan jawaban (A, B, C, D) per santri</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card bg-light border-primary p-3 border shadow-xs rounded-3">
                            <label class="form-label fw-bold text-primary mb-2">
                                <i class="fas fa-eye me-1"></i> Jangkauan & Visibilitas Paket Soal <span class="text-danger">*</span>
                            </label>

                            <?php
                            $defaultIsGlobal = !empty($isUserAdmin) ? 1 : 0;
                            $currentIsGlobal = (int)(old('IsGlobal', $paket['IsGlobal'] ?? $defaultIsGlobal));
                            ?>

                            <div class="d-flex gap-4 align-items-center flex-wrap">
                                <!-- Opsi 1: Privat TPQ Sendiri -->
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="isGlobalPrivate" name="IsGlobal" value="0" <?= $currentIsGlobal === 0 ? 'checked' : '' ?>>
                                    <label for="isGlobalPrivate" class="custom-control-label fw-bold text-dark">
                                        <i class="fas fa-lock text-success me-1"></i> Halaman TPQ Saya Sendiri (Privat / Lokal)
                                    </label>
                                </div>

                                <!-- Opsi 2: Global / Seluruh TPQ -->
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="isGlobalPublic" name="IsGlobal" value="1" <?= $currentIsGlobal === 1 ? 'checked' : '' ?>>
                                    <label for="isGlobalPublic" class="custom-control-label fw-bold text-dark">
                                        <i class="fas fa-globe text-primary me-1"></i> Bagikan / Publikasikan ke Seluruh TPQ (Global)
                                    </label>
                                </div>
                            </div>

                            <small class="text-muted d-block mt-2">
                                <?php if (!empty($isUserAdmin)): ?>
                                    <strong>Catatan Admin:</strong> Pilih <em>"Halaman TPQ Saya Sendiri"</em> untuk draf internal admin, atau <em>"Bagikan ke Seluruh TPQ"</em> agar paket soal pusat ini dapat dilihat dan digunakan oleh seluruh lembaga TPQ.
                                <?php else: ?>
                                    <strong>Catatan Lembaga:</strong> Pilih <em>"Halaman TPQ Saya Sendiri"</em> agar paket ini hanya muncul di akun TPQ Anda. Pilih <em>"Bagikan ke Seluruh TPQ"</em> jika ingin berbagi paket soal ini ke TPQ lain.
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Petunjuk Pengerjaan (CKEditor 5) -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Petunjuk Pengerjaan Ujian (Opsional)</label>
                        <textarea name="PetunjukPengerjaan" id="editorPetunjuk" class="form-control" rows="4"><?= old('PetunjukPengerjaan', $paket['PetunjukPengerjaan'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-default">Batal</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Konfigurasi Paket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('#editorPetunjuk')) {
        ClassicEditor
            .create(document.querySelector('#editorPetunjuk'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
            })
            .catch(error => {
                console.error('CKEditor Init Error:', error);
            });
    }
});
</script>

<?= $this->endSection(); ?>
