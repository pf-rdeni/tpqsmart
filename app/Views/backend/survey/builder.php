<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Quill Rich Text Editor (Google Forms-like) -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<!-- Custom Survey Builder Styles -->
<link rel="stylesheet" href="<?= base_url('template/backend/dist/css/survey-builder.css') ?>">

<section class="content pt-2">
    <div class="container-fluid">
        <!-- Builder Info & Quick Actions Row -->
        <div class="row align-items-center mb-3">
            <div class="col-6">
                <span class="badge badge-warning font-weight-normal px-2 py-1 shadow-xs" id="save-status"><i class="fas fa-sync-alt fa-spin mr-1"></i> Loading...</span>
            </div>
            <div class="col-6 text-right">
                <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('backend/survey') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <a href="<?= base_url('backend/survey/settings/' . $survey['id']) ?>" class="btn btn-info">
                        <i class="fas fa-cog mr-1"></i> Pengaturan
                    </a>
                    <a href="<?= base_url('backend/survey/preview/' . $survey['id']) ?>" target="_blank" class="btn btn-success">
                        <i class="fas fa-eye mr-1"></i> Preview Form
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar: Toolbox -->
            <div class="col-md-3">
                <div class="card card-primary card-outline sticky-top shadow-sm" style="top: 15px; z-index: 1020;">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">Tipe Pertanyaan</h3>
                    </div>
                    <div class="card-body p-2" id="toolbox">
                        <div class="toolbox-section-title px-2 py-1 small text-muted font-weight-bold uppercase">Teks & Pilihan</div>
                        <div class="toolbox-item" data-type="text_short">
                            <i class="fas fa-font text-info mr-2"></i> Jawaban Singkat
                        </div>
                        <div class="toolbox-item" data-type="text_paragraph">
                            <i class="fas fa-align-left text-info mr-2"></i> Paragraf
                        </div>
                        <div class="toolbox-item" data-type="multiple_choice">
                            <i class="far fa-dot-circle text-primary mr-2"></i> Pilihan Ganda
                        </div>
                        <div class="toolbox-item" data-type="checkbox">
                            <i class="far fa-check-square text-success mr-2"></i> Kotak Centang
                        </div>
                        <div class="toolbox-item" data-type="dropdown">
                            <i class="fas fa-chevron-circle-down text-warning mr-2"></i> Dropdown
                        </div>

                        <div class="toolbox-section-title px-2 py-1 mt-2 small text-muted font-weight-bold uppercase">Media & Skala</div>
                        <div class="toolbox-item" data-type="file_upload">
                            <i class="fas fa-upload text-danger mr-2"></i> Upload File
                        </div>
                        <div class="toolbox-item" data-type="linear_scale">
                            <i class="fas fa-ellipsis-h text-purple mr-2"></i> Skalar Linier
                        </div>
                        <div class="toolbox-item" data-type="rating">
                            <i class="fas fa-star text-warning mr-2"></i> Rating Bintang
                        </div>
                        <div class="toolbox-item" data-type="grid_multiple">
                            <i class="fas fa-th text-pink mr-2"></i> Kisi Pilihan Ganda
                        </div>
                        <div class="toolbox-item" data-type="grid_checkbox">
                            <i class="fas fa-th-large text-teal mr-2"></i> Petak Kotak Centang
                        </div>

                        <div class="toolbox-section-title px-2 py-1 mt-2 small text-muted font-weight-bold uppercase">Tanggal & Waktu</div>
                        <div class="toolbox-item" data-type="date">
                            <i class="far fa-calendar-alt text-primary mr-2"></i> Pemilih Tanggal
                        </div>
                        <div class="toolbox-item" data-type="time">
                            <i class="far fa-clock text-lightblue mr-2"></i> Pemilih Waktu
                        </div>

                        <div class="toolbox-section-title px-2 py-1 mt-2 small text-muted font-weight-bold uppercase">Master Data Terintegrasi</div>
                        <div class="toolbox-item" data-type="master_tpq">
                            <i class="fas fa-mosque text-emerald mr-2"></i> Dropdown TPQ
                        </div>
                        <div class="toolbox-item" data-type="master_guru">
                            <i class="fas fa-chalkboard-teacher text-indigo mr-2"></i> Dropdown Guru
                        </div>
                        <div class="toolbox-item" data-type="master_santri">
                            <i class="fas fa-child text-orange mr-2"></i> Dropdown Santri
                        </div>

                        <div class="toolbox-section-title px-2 py-1 mt-2 small text-muted font-weight-bold uppercase">Konten Non-Pertanyaan</div>
                        <div class="toolbox-item" data-type="image_display">
                            <i class="far fa-image text-gray mr-2"></i> Tampilkan Gambar
                        </div>
                        <div class="toolbox-item" data-type="video_display">
                            <i class="fab fa-youtube text-red mr-2"></i> Tampilkan Video
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="col-md-9">
                <!-- Header Card Survey -->
                <div class="card card-outline shadow-sm mb-3" style="border-top: 5px solid <?= esc($survey['theme_color'] ?? '#4285F4') ?>;">
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <input type="text" class="form-control form-control-lg border-0 px-0 font-weight-bold text-dark survey-title-input" 
                                   id="survey-title" value="<?= esc($survey['title']) ?>" placeholder="Judul Survey Tanpa Nama" style="font-size: 1.8rem; box-shadow: none;">
                        </div>
                        <div class="form-group mb-0">
                            <!-- Quill Rich Text Editor for Survey Description -->
                            <div class="quill-survey-desc-editor">
                                <div class="quill-editor-area" style="font-size: 1.05rem;"><?= $survey['description'] ?></div>
                            </div>
                            <input type="hidden" id="survey-description" value="<?= esc($survey['description']) ?>">
                        </div>
                    </div>
                </div>

                <!-- Main Canvas Drop Zone -->
                <div id="survey-canvas" class="survey-canvas-dropzone pb-5">
                    <!-- Dynamic Sections and Questions list will be loaded here via JS -->
                </div>

                <!-- Add Section Button -->
                <div class="text-center my-4">
                    <button type="button" class="btn btn-secondary btn-flat shadow-sm px-4" id="btn-add-section">
                        <i class="fas fa-folder-plus mr-1"></i> Tambahkan Bagian Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: Edit Validation & Settings for Question -->
<div class="modal fade" id="questionSettingsModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalTitle">Pengaturan Pertanyaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="question-settings-form">
                <input type="hidden" name="question_id" id="settings-q-id">
                <div class="modal-body" id="modal-settings-content">
                    <!-- Dynamic fields based on question type will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SortableJS library for Drag & Drop support -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<!-- Quill Rich Text Editor -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<!-- Custom JavaScript for builder logic -->
<script>
    const SURVEY_ID = <?= $survey['id'] ?>;
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('template/backend/dist/js/survey-builder.js') ?>"></script>
<?= $this->endSection() ?>
