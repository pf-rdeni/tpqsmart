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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/ujian-mdta/paket') ?>">Paket Soal</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>"><?= esc($paket['NamaPaket']) ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= isset($soal) ? 'Edit Soal' : 'Tambah Soal' ?></li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-edit text-success me-2"></i>
                    <?= isset($soal) ? "Edit Soal No. {$soal['NomorSoal']}" : "Form Input Soal Pilihan Ganda / Esai" ?>
                </h4>
            </div>
            <a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Soal
            </a>
        </div>
    </div>

    <!-- Alert Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= isset($soal) ? base_url("backend/ujian-mdta/soal/update/{$soal['id']}") : base_url("backend/ujian-mdta/paket/{$paket['id']}/soal/save") ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Card Input Utama Soal -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0 text-success fw-bold">
                    <i class="fas fa-question-circle me-2"></i>Uraian Pertanyaan & Metadata Soal
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <!-- Nomor & Metadata -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Nomor Soal</label>
                        <input type="number" name="NomorSoal" class="form-control form-control-adminlte" value="<?= old('NomorSoal', $nomorSoal) ?>" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Jenis Soal <span class="text-danger">*</span></label>
                        <select name="JenisSoal" id="selectJenisSoal" class="form-control custom-select fw-bold text-primary" required>
                            <option value="pilihan_ganda" <?= old('JenisSoal', $soal['JenisSoal'] ?? 'pilihan_ganda') == 'pilihan_ganda' ? 'selected' : '' ?>>Pilihan Ganda</option>
                            <option value="esai" <?= old('JenisSoal', $soal['JenisSoal'] ?? '') == 'esai' ? 'selected' : '' ?>>Esai / Uraian</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Tingkat Kesulitan</label>
                        <select name="TingkatKesulitan" class="form-control custom-select">
                            <option value="mudah" <?= old('TingkatKesulitan', $soal['TingkatKesulitan'] ?? '') == 'mudah' ? 'selected' : '' ?>>Mudah</option>
                            <option value="sedang" <?= old('TingkatKesulitan', $soal['TingkatKesulitan'] ?? 'sedang') == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                            <option value="sulit" <?= old('TingkatKesulitan', $soal['TingkatKesulitan'] ?? '') == 'sulit' ? 'selected' : '' ?>>Sulit</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Indikator Capaian (Opsional)</label>
                        <input type="text" name="IndikatorCapaian" class="form-control form-control-adminlte" placeholder="Misal: Memahami rukun wudhu" value="<?= old('IndikatorCapaian', $soal['IndikatorCapaian'] ?? '') ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Acak Posisi Soal?</label>
                        <div class="custom-control custom-switch mt-1">
                            <input type="checkbox" class="custom-control-input" name="AcakSoal" id="AcakSoal" value="1" <?= old('AcakSoal', $soal['AcakSoal'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="custom-control-label fw-semibold text-secondary" for="AcakSoal">Ya, diacak</label>
                        </div>
                    </div>

                    <!-- Uraian Soal (Summernote/CKEditor) -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Uraian Pertanyaan <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-1">Mendukung Format Teks, Tabel, Gambar, dan Formula.</small>
                        <textarea name="UraianSoal" id="editorUraianSoal" class="form-control" rows="6"><?= old('UraianSoal', $soal['UraianSoal'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Pilihan Jawaban -->
        <div class="card card-outline card-primary shadow-sm mb-4" id="cardPilihanJawaban">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-list-ul me-2"></i>Pilihan Jawaban Pilihan Ganda (Mode: <?= $paket['ModeJawaban'] ?>)
                </h5>
                <small class="text-muted">Pilih satu radio button sebagai <strong>Jawaban Benar</strong>.</small>
            </div>
            <div class="card-body">
                <?php
                $opsiList = ($paket['ModeJawaban'] == 'ABCDE') ? ['A', 'B', 'C', 'D', 'E'] : ['A', 'B', 'C', 'D'];
                
                // Map pilihan jawaban yang sudah ada jika edit
                $pilihanMap = [];
                $jawabanBenarHuruf = 'A';
                if (!empty($pilihanList)) {
                    foreach ($pilihanList as $p) {
                        $pilihanMap[$p['HurufPilihan']] = $p['TeksPilihan'];
                        if ($p['IsBenar'] == 1) {
                            $jawabanBenarHuruf = $p['HurufPilihan'];
                        }
                    }
                }
                ?>

                <div class="row g-4">
                    <?php foreach ($opsiList as $huruf): ?>
                        <div class="col-md-12 border p-3 rounded bg-light-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary fs-6 px-3">Pilihan <?= $huruf ?></span>
                                    <div class="form-check form-check-inline ms-3">
                                        <input class="form-check-input" type="radio" name="jawaban_benar" id="radio_<?= $huruf ?>" value="<?= $huruf ?>" <?= old('jawaban_benar', $jawabanBenarHuruf) == $huruf ? 'checked' : '' ?> required>
                                        <label class="form-check-label fw-bold text-success" for="radio_<?= $huruf ?>">
                                            <i class="fas fa-check-circle me-1"></i> Jawaban Benar
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <textarea name="pilihan[<?= $huruf ?>]" id="editorPilihan_<?= $huruf ?>" class="form-control editor-pilihan" rows="2"><?= old("pilihan.{$huruf}", $pilihanMap[$huruf] ?? '') ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Card Pembahasan -->
        <div class="card card-outline card-info shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 text-info fw-bold">
                    <i class="fas fa-lightbulb me-2"></i>Pembahasan & Kunci Jawaban (Opsional)
                </h5>
            </div>
            <div class="card-body">
                <label class="form-label fw-bold">Uraian Pembahasan</label>
                <small class="text-muted d-block mb-1">Ditampilkan kepada santri setelah ujian selesai atau pada laporan evaluasi.</small>
                <textarea name="Pembahasan" id="editorPembahasan" class="form-control" rows="4"><?= old('Pembahasan', $soal['Pembahasan'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>" class="btn btn-default px-4">Batal</a>
            <button type="submit" class="btn btn-success px-4 fw-bold">
                <i class="fas fa-save me-1"></i> Simpan Soal
            </button>
        </div>
    </form>
</div>

<style>
/* CSS Pendukung Resizing & Alignment Gambar CKEditor 5 */
.ck-content .image,
figure.image {
    margin: 10px auto;
    display: table;
    max-width: 100%;
}

.ck-content .image-style-side,
figure.image-style-side {
    float: right;
    margin-left: 15px;
    max-width: 50%;
}

.ck-content .image-style-inline,
figure.image-style-inline {
    display: inline-block;
}

.ck-content .image img,
figure.image img {
    height: auto !important;
    max-width: 100%;
}

/* Pastikan style width buatan CKEditor resize dihormati */
figure.image[style*="width"],
.ck-content figure.image[style*="width"] {
    max-width: 100% !important;
}
</style>

<style>
/* Styling Tambahan untuk Popover Toolbar Gambar Summernote */
.note-popover .popover-content, .card-header.note-toolbar {
    padding: 6px 10px !important;
}
.note-editor .note-editing-area img {
    max-width: 100%;
}
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function () {
    // Fungsi inisialisasi Summernote Editor dengan Popover Resizing Toolbar (100%, 50%, 25%, Float, Delete)
    function initSummernoteEditor(selector, minHeight) {
        $(selector).summernote({
            height: minHeight || 180,
            dialogsInBody: true,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']]
            ],
            fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Traditional Arabic', 'Amiri', 'Scheherazade', 'Times New Roman'],
            fontNamesIgnoreCheck: ['Traditional Arabic', 'Amiri', 'Scheherazade'],
            popover: {
                image: [
                    ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ]
            },
            callbacks: {
                onImageUpload: function (files) {
                    const $editor = $(this);
                    for (let i = 0; i < files.length; i++) {
                        uploadImageToSummernote(files[i], $editor);
                    }
                }
            }
        });
    }

    // Fungsi upload gambar via AJAX untuk Summernote
    function uploadImageToSummernote(file, $editor) {
        const data = new FormData();
        data.append('upload', file);
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('backend/ujian-mdta/soal/upload-gambar') ?>', {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.url) {
                $editor.summernote('insertImage', res.url);
            } else {
                alert(res.error ? (res.error.message || res.error) : 'Gagal upload gambar.');
            }
        })
        .catch(err => {
            console.error('Upload Error:', err);
            alert('Gagal mengunggah gambar ke server.');
        });
    }

    // Inisialisasi Summernote untuk Uraian Pertanyaan, Pembahasan, & Pilihan Jawaban
    initSummernoteEditor('#editorUraianSoal', 200);
    initSummernoteEditor('#editorPembahasan', 150);
    $('.editor-pilihan').each(function () {
        initSummernoteEditor(this, 120);
    });

    // Toggle Tampilan Card Pilihan Jawaban sesuai Jenis Soal
    function toggleJenisSoalCard() {
        const jenis = $('#selectJenisSoal').val();
        if (jenis === 'esai') {
            $('#cardPilihanJawaban').slideUp(200);
            $('input[name="jawaban_benar"]').prop('required', false);
        } else {
            $('#cardPilihanJawaban').slideDown(200);
            $('input[name="jawaban_benar"]').prop('required', true);
        }
    }
    $('#selectJenisSoal').on('change', toggleJenisSoalCard);
    toggleJenisSoalCard();
});
</script>
<?= $this->endSection(); ?>
