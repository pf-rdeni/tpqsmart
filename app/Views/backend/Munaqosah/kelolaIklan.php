<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb & Header Title -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h4 class="m-0 text-dark"><i class="fas fa-images mr-2 text-primary"></i>Kelola Iklan Monitoring Antrian</h4>
        </div>
        <div class="col-md-6 col-sm-12 text-md-right">
            <?php
                $monitorUrl = ($selectedTpq === '0')
                    ? base_url('backend/munaqosah/monitoring-status-antrian?tahun=' . urlencode($selectedTahun) . '&type=munaqosah&tpq=0')
                    : base_url('backend/munaqosah/monitoring-status-antrian?tahun=' . urlencode($selectedTahun) . '&type=pra-munaqosah&tpq=' . urlencode($selectedTpq));
            ?>
            <a href="<?= $monitorUrl ?>" 
               target="_blank" 
               class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Buka Layar Monitoring
            </a>
        </div>
    </div>

    <!-- Filter & Scope TPQ Selector Card -->
    <div class="card card-outline card-secondary mb-3 shadow-sm">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center">
                <div class="col-md-8 col-sm-12 d-flex align-items-center flex-wrap">
                    <span class="mr-2 font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-1"></i>Target Pengelolaan Iklan:</span>
                    <?php if ($canSelectTpq): ?>
                        <select id="selectTargetTpq" class="form-control form-control-sm font-weight-bold d-inline-block" style="width: auto; max-width: 360px;">
                            <option value="0" <?= ($selectedTpq === '0') ? 'selected' : '' ?>>
                                [0] - Ujian Munaqosah (Pusat / Gabungan)
                            </option>
                            <?php foreach ($tpqList as $tpq): ?>
                                <option value="<?= esc($tpq['IdTpq']) ?>" <?= ($selectedTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                    [<?= esc($tpq['IdTpq']) ?>] - <?= esc($tpq['NamaTpq']) ?> (Pra-Munaqosah)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <span class="badge badge-info p-2 font-size-sm">
                            <i class="fas fa-building mr-1"></i> <?= esc($selectedTpqName) ?> [IdTpq: <?= esc($selectedTpq) ?>]
                        </span>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-12 text-md-right mt-2 mt-md-0">
                    <?php if ($selectedTpq === '0'): ?>
                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Mode: Munaqosah Pusat (IdTpq: 0)</span>
                    <?php else: ?>
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Mode: Pra-Munaqosah (TPQ: <?= esc($selectedTpq) ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Scope Alert -->
    <?php if ($selectedTpq === '0'): ?>
        <div class="alert alert-info py-2 px-3 mb-3 shadow-sm border-left" style="border-left: 4px solid #17a2b8 !important;">
            <i class="fas fa-info-circle mr-1 text-info"></i>
            Anda sedang mengelola iklan untuk <strong>Ujian Munaqosah (Pusat / Gabungan)</strong>. Iklan ini <strong>hanya</strong> akan ditampilkan pada Layar Monitoring Ujian Munaqosah (IdTpq = 0).
        </div>
    <?php else: ?>
        <div class="alert alert-success py-2 px-3 mb-3 shadow-sm border-left" style="border-left: 4px solid #28a745 !important;">
            <i class="fas fa-info-circle mr-1 text-success"></i>
            Anda sedang mengelola iklan untuk <strong><?= esc($selectedTpqName) ?> [IdTpq: <?= esc($selectedTpq) ?>]</strong>. Iklan ini <strong>hanya</strong> akan ditampilkan pada Layar Monitoring Pra-Munaqosah TPQ ini.
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Kolom Kiri: Form Upload & Pengaturan Display -->
        <div class="col-lg-5 col-md-6 mb-3">
            <!-- Card Form Upload Media -->
            <div class="card card-outline card-primary mb-3 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload Media Iklan</h3>
                </div>
                <div class="card-body">
                    <form id="formUploadIklan" enctype="multipart/form-data">
                        <input type="hidden" name="tpq" value="<?= esc($selectedTpq) ?>">
                        <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">

                        <!-- Pemilihan Sumber Media -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold d-block">Sumber Media Iklan:</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons" id="sourceTypeGroup">
                                <label class="btn btn-outline-primary active btn-sm">
                                    <input type="radio" name="source_type" value="local" checked> <i class="fas fa-file-upload mr-1"></i> Berkas
                                </label>
                                <label class="btn btn-outline-danger btn-sm">
                                    <input type="radio" name="source_type" value="youtube"> <i class="fab fa-youtube mr-1"></i> YouTube
                                </label>
                                <label class="btn btn-outline-warning text-dark btn-sm">
                                    <input type="radio" name="source_type" value="gdrive"> <i class="fab fa-google-drive mr-1"></i> G-Drive
                                </label>
                                <label class="btn btn-outline-info btn-sm">
                                    <input type="radio" name="source_type" value="external"> <i class="fas fa-link mr-1"></i> Link Gambar
                                </label>
                            </div>
                        </div>

                        <!-- 1. Input Berkas Lokal -->
                        <div class="source-container" id="sourceLocal">
                            <div class="form-group">
                                <label class="font-weight-bold">File Media <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="iklanFileInput" name="iklan_file" accept=".jpg,.jpeg,.png,.webp,.mp4" required>
                                    <label class="custom-file-label" for="iklanFileInput">Pilih file gambar (16:9)...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Format gambar: <strong>JPG, PNG, WebP</strong> (maks. 5MB, disarankan lanskap 16:9).<br>
                                    Format video: <strong>MP4</strong> (maks. 25MB) aktif jika opsi video diaktifkan.
                                </small>
                            </div>
                        </div>

                        <!-- 2. Input YouTube -->
                        <div class="source-container" id="sourceYoutube" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold text-danger"><i class="fab fa-youtube mr-1"></i> Tautan Video YouTube <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" name="youtube_url" id="iklanYoutubeUrl" placeholder="Contoh: https://www.youtube.com/watch?v=xxxx atau https://youtu.be/xxxx">
                                <small class="form-text text-muted">
                                    Mendukung video YouTube biasa atau YouTube Shorts. Video akan diputar otomatis tanpa kontrol tombol.
                                </small>
                            </div>
                        </div>

                        <!-- 3. Input Google Drive -->
                        <div class="source-container" id="sourceGdrive" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold text-warning text-dark"><i class="fab fa-google-drive mr-1"></i> Link Gambar Google Drive <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" name="gdrive_url" id="iklanGdriveUrl" placeholder="Contoh: https://drive.google.com/file/d/xxxx/view?usp=sharing">
                                <small class="form-text text-muted">
                                    Pastikan hak akses file di Google Drive diset publik (<strong>Anyone with the link / Siapa saja yang memiliki link</strong>).
                                </small>
                            </div>
                        </div>

                        <!-- 4. Input URL Gambar Eksternal -->
                        <div class="source-container" id="sourceExternal" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold text-info"><i class="fas fa-link mr-1"></i> Tautan URL Gambar Langsung <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" name="external_url" id="iklanExternalUrl" placeholder="Contoh: https://domain-lembaga.com/poster.jpg">
                                <small class="form-text text-muted">
                                    Tautan langsung file gambar dari internet (format .jpg, .png, .webp).
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Judul / Keterangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" id="iklanJudul" placeholder="Contoh: Brosur PSB TPQ Smart TA <?= esc($selectedTahun) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" id="btnSubmitUpload">
                            <i class="fas fa-save mr-1"></i> Simpan Media untuk <?= ($selectedTpq === '0') ? 'Munaqosah' : 'Pra-Munaqosah' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Pengaturan Display -->
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sliders-h mr-1"></i> Pengaturan Tampilan & Pemutaran</h3>
                </div>
                <div class="card-body">
                    <form id="formIklanSettings">
                        <input type="hidden" name="tpq" value="<?= esc($selectedTpq) ?>">

                        <div class="form-group">
                            <label class="font-weight-bold">Durasi per Slide</label>
                            <select class="form-control" id="settingInterval" name="iklan_interval">
                                <option value="5" <?= ($settings['iklan_interval'] == 5) ? 'selected' : '' ?>>5 detik</option>
                                <option value="10" <?= ($settings['iklan_interval'] == 10) ? 'selected' : '' ?>>10 detik (Standar)</option>
                                <option value="15" <?= ($settings['iklan_interval'] == 15) ? 'selected' : '' ?>>15 detik</option>
                                <option value="30" <?= ($settings['iklan_interval'] == 30) ? 'selected' : '' ?>>30 detik</option>
                                <option value="60" <?= ($settings['iklan_interval'] == 60) ? 'selected' : '' ?>>1 menit</option>
                            </select>
                            <small class="text-muted">Berapa detik gambar ditampilkan sebelum beralih ke slide berikutnya.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Mode Putar (Playback)</label>
                            <select class="form-control" id="settingMode" name="iklan_mode">
                                <option value="loop" <?= ($settings['iklan_mode'] == 'loop') ? 'selected' : '' ?>>Loop Berurutan (Slide 1, 2, 3...)</option>
                                <option value="random" <?= ($settings['iklan_mode'] == 'random') ? 'selected' : '' ?>>Acak / Shuffle</option>
                                <option value="single" <?= ($settings['iklan_mode'] == 'single') ? 'selected' : '' ?>>Salah Satu Gambar Tetap (Static)</option>
                            </select>
                        </div>

                        <div class="form-group" id="settingSingleRow" style="<?= ($settings['iklan_mode'] == 'single') ? '' : 'display: none;' ?>">
                            <label class="font-weight-bold">Pilih Gambar Tetap</label>
                            <select class="form-control" id="settingSingleId" name="iklan_single_id">
                                <option value="0">-- Pilih salah satu gambar --</option>
                                <?php foreach ($iklanList as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ($settings['iklan_single_id'] == $m['id']) ? 'selected' : '' ?>>
                                        <?= esc($m['Judul']) ?> (<?= $m['MediaType'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Gambar yang dipilih akan terus tampil di layar tanpa berganti.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Dukungan Video MP4</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="settingVideoEnabled" name="iklan_video_enabled" <?= !empty($settings['iklan_video_enabled']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="settingVideoEnabled">Izinkan unggah file video profil (.mp4)</label>
                            </div>
                            <small class="text-muted">Default: Nonaktif. Aktifkan jika ingin memutar video profil lembaga.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Penyesuaian Rasio Gambar (Aspect Ratio)</label>
                            <select class="form-control" id="settingFit" name="iklan_fit">
                                <option value="contain_blur" <?= (($settings['iklan_fit'] ?? 'contain_blur') == 'contain_blur') ? 'selected' : '' ?>>
                                    Proporsional + Latar Belakang Blur (Rekomendasi)
                                </option>
                                <option value="fill" <?= (($settings['iklan_fit'] ?? '') == 'fill') ? 'selected' : '' ?>>
                                    Paksa Regangkan (Stretch ke Bingkai 16:9 Penuh)
                                </option>
                                <option value="cover" <?= (($settings['iklan_fit'] ?? '') == 'cover') ? 'selected' : '' ?>>
                                    Isi Penuh Bingkai (Crop / Potong Bagian Sisi)
                                </option>
                                <option value="contain" <?= (($settings['iklan_fit'] ?? '') == 'contain') ? 'selected' : '' ?>>
                                    Proporsional + Latar Hitam (Letterbox)
                                </option>
                            </select>
                            <small class="text-muted">Menentukan cara menampilkan gambar yang rasionya tidak 16:9 (misal foto persegi / vertikal) agar tetap pas di bingkai layar.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Format Default Header Strip</label>
                            <select class="form-control" id="settingHeaderFormat" name="header_format">
                                <option value="A" <?= ($settings['header_format'] == 'A') ? 'selected' : '' ?>>Format A (Dua Kolom: Progress Kiri, Ruangan Kanan)</option>
                                <option value="B" <?= ($settings['header_format'] == 'B') ? 'selected' : '' ?>>Format B (Pita Bertingkat: Baris Nilai & Baris Ruangan)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-block" id="btnSaveSettings">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabel Daftar Media Aktif -->
        <div class="col-lg-7 col-md-6 mb-3">
            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                        <i class="fas fa-list mr-1"></i> Daftar Media Iklan
                        <small class="text-muted ml-2 font-weight-normal d-none d-sm-inline"><i class="fas fa-info-circle mr-1"></i>Tarik baris <i class="fas fa-grip-vertical"></i> untuk mengubah urutan</small>
                    </h3>
                    <button type="button" class="btn btn-tool" onclick="loadTableMedia()" title="Refresh Tabel">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="tableIklan">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 35px;" class="text-center" title="Geser baris untuk mengubah urutan"><i class="fas fa-arrows-alt text-muted"></i></th>
                                    <th style="width: 35px;" class="text-center">No</th>
                                    <th style="width: 85px;" class="text-center">Preview</th>
                                    <th>Judul & Berkas</th>
                                    <th style="width: 140px;" class="text-center">Target & Tipe</th>
                                    <th style="width: 105px;" class="text-center">Status</th>
                                    <th style="width: 55px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyIklan">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data media...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .iklan-thumb {
        width: 85px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .drag-handle {
        cursor: grab;
        padding: 8px 4px !important;
        user-select: none;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .drag-handle:hover i {
        color: #007bff !important;
    }
    .iklan-row.ui-sortable-helper {
        background-color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(0,0,0,0.2) !important;
        display: table;
    }
    .iklan-row-placeholder {
        background-color: #f1f8ff !important;
        border: 2px dashed #007bff !important;
        height: 55px;
    }
</style>

<!-- jQuery UI untuk Drag and Drop Sortable Baris Tabel -->
<script src="<?= base_url('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>

<script>
$(function() {
    const listUrl = '<?= base_url('backend/munaqosah/get-iklan-list-ajax') ?>';
    const uploadUrl = '<?= base_url('backend/munaqosah/upload-iklan-ajax') ?>';
    const deleteUrl = '<?= base_url('backend/munaqosah/delete-iklan-ajax') ?>';
    const settingsUrl = '<?= base_url('backend/munaqosah/update-iklan-settings-ajax') ?>';
    const toggleStatusUrl = '<?= base_url('backend/munaqosah/toggle-iklan-status-ajax') ?>';
    const reorderUrl = '<?= base_url('backend/munaqosah/reorder-iklan-ajax') ?>';

    const currentTpq = '<?= esc($selectedTpq) ?>';
    const currentTahun = '<?= esc($selectedTahun) ?>';
    const canSelectTpq = <?= $canSelectTpq ? 'true' : 'false' ?>;

    // Filter TPQ Switcher Event
    if (canSelectTpq) {
        $('#selectTargetTpq').on('change', function() {
            const val = $(this).val();
            window.location.href = '<?= base_url('backend/munaqosah/iklan') ?>?tahun=' + encodeURIComponent(currentTahun) + '&tpq=' + encodeURIComponent(val);
        });
    }

    // Source Type Switcher Handler
    $('input[name="source_type"]').on('change', function() {
        const val = $(this).val();
        $('.source-container').hide();

        $('#iklanFileInput').prop('required', false);
        $('#iklanYoutubeUrl').prop('required', false);
        $('#iklanGdriveUrl').prop('required', false);
        $('#iklanExternalUrl').prop('required', false);

        if (val === 'local') {
            $('#sourceLocal').slideDown(200);
            $('#iklanFileInput').prop('required', true);
        } else if (val === 'youtube') {
            $('#sourceYoutube').slideDown(200);
            $('#iklanYoutubeUrl').prop('required', true);
        } else if (val === 'gdrive') {
            $('#sourceGdrive').slideDown(200);
            $('#iklanGdriveUrl').prop('required', true);
        } else if (val === 'external') {
            $('#sourceExternal').slideDown(200);
            $('#iklanExternalUrl').prop('required', true);
        }
    });

    // Helper untuk invalidate cache & broadcast perubahan ke layar monitoring secara instan
    function clearLocalIklanCache() {
        const targetType = (currentTpq === '0') ? 'munaqosah' : 'pra-munaqosah';
        sessionStorage.removeItem('iklanMediaCache_' + targetType + '_' + currentTpq);
        sessionStorage.removeItem('iklanMediaVersion_' + targetType + '_' + currentTpq);
        sessionStorage.removeItem('iklanMediaCache');
        sessionStorage.removeItem('iklanMediaVersion');
        // Broadcast sinyal perubahan ke tab layar monitoring secara instan (0 delay via storage event)
        localStorage.setItem('iklanLastChange', Date.now());
    }

    // Load tabel data media (sertakan all=1 agar yang nonaktif tetap bisa dikelola)
    window.loadTableMedia = function() {
        $('#tbodyIklan').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data media...</td></tr>');
        const query = '?tahun=' + encodeURIComponent(currentTahun) + '&tpq=' + encodeURIComponent(currentTpq) + '&all=1';
        $.getJSON(listUrl + query, function(res) {
            if (res.success) {
                renderTable(res.data);
                updateSingleSelectOptions(res.data);
            } else {
                $('#tbodyIklan').html('<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat media iklan.</td></tr>');
            }
        }).fail(function() {
            $('#tbodyIklan').html('<tr><td colspan="7" class="text-center py-4 text-danger">Terjadi kesalahan koneksi server.</td></tr>');
        });
    };

    function renderTable(data) {
        if (!data || data.length === 0) {
            $('#tbodyIklan').html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-info-circle mr-1"></i> Belum ada media iklan untuk target ini. Silakan upload melalui form di sebelah kiri.</td></tr>');
            return;
        }

        let html = '';
        data.forEach(function(item, idx) {
            let preview = '';
            const srcType = item.SourceType || 'local';
            const thumbUrl = item.thumb || item.url;
            const viewUrl = item.url;
            const isActive = parseInt(item.IsActive) === 1;

            if (srcType === 'youtube') {
                const ytWatch = 'https://www.youtube.com/watch?v=' + (item.videoId || '');
                preview = '<a href="' + ytWatch + '" target="_blank" title="Lihat di YouTube">' +
                          '<img src="' + thumbUrl + '" class="iklan-thumb" alt="' + $('<div>').text(item.Judul).html() + '">' +
                          '</a>';
            } else if (item.MediaType === 'video') {
                preview = '<span class="badge badge-secondary p-2"><i class="fas fa-film mr-1"></i> Video</span>';
            } else {
                preview = '<a href="' + viewUrl + '" target="_blank" title="Lihat ukuran penuh">' +
                          '<img src="' + thumbUrl + '" class="iklan-thumb" alt="' + $('<div>').text(item.Judul).html() + '" onerror="this.onerror=null;this.src=\'data:image/svg+xml;utf8,<svg xmlns=\\\'http://www.w3.org/2000/svg\\\' width=\\\'85\\\' height=\\\'50\\\'><rect width=\\\'100%\\\' height=\\\'100%\\\' fill=\\\'%23eee\\\'/><text x=\\\'50%\\\' y=\\\'50%\\\' fill=\\\'%23999\\\' text-anchor=\\\'middle\\\' dy=\\\'.3em\\\' font-size=\\\'10\\\'>Gambar</text></svg>\';">' +
                          '</a>';
            }

            let targetBadge = '';
            if (String(item.IdTpq) === '0' || !item.IdTpq) {
                targetBadge = '<span class="badge badge-primary"><i class="fas fa-university mr-1"></i> Munaqosah (0)</span>';
            } else {
                targetBadge = '<span class="badge badge-info"><i class="fas fa-mosque mr-1"></i> Pra-Munaqosah (' + item.IdTpq + ')</span>';
            }

            let typeBadge = '';
            if (srcType === 'youtube') {
                typeBadge = '<span class="badge badge-danger"><i class="fab fa-youtube mr-1"></i> YouTube</span>';
            } else if (srcType === 'gdrive') {
                typeBadge = '<span class="badge badge-warning text-dark"><i class="fab fa-google-drive mr-1"></i> G-Drive</span>';
            } else if (srcType === 'external') {
                typeBadge = '<span class="badge badge-info"><i class="fas fa-link mr-1"></i> Web Link</span>';
            } else if (item.MediaType === 'video') {
                typeBadge = '<span class="badge badge-primary"><i class="fas fa-film mr-1"></i> Video MP4</span>';
            } else {
                typeBadge = '<span class="badge badge-secondary"><i class="fas fa-image mr-1"></i> Berkas</span>';
            }

            let detailSub = '';
            if (srcType === 'youtube') {
                detailSub = '<small class="text-danger"><i class="fab fa-youtube mr-1"></i> ID: ' + (item.videoId || '-') + '</small>';
            } else if (srcType === 'gdrive') {
                detailSub = '<small class="text-warning text-dark"><i class="fab fa-google-drive mr-1"></i> Google Drive</small>';
            } else if (srcType === 'external') {
                detailSub = '<small class="text-info text-truncate d-inline-block" style="max-width: 250px;" title="' + item.ExternalUrl + '"><i class="fas fa-link mr-1"></i> ' + item.ExternalUrl + '</small>';
            } else {
                detailSub = '<small class="text-muted"><i class="fas fa-file mr-1"></i> ' + (item.FileName || '-') + '</small>';
            }

            // Switch toggle status Aktif / Tidak Aktif
            const statusSwitch = '<div class="custom-control custom-switch" title="Klik untuk mengaktifkan / menonaktifkan">' +
                                 '<input type="checkbox" class="custom-control-input switch-status" id="switchStatus_' + item.id + '" data-id="' + item.id + '" ' + (isActive ? 'checked' : '') + '>' +
                                 '<label class="custom-control-label font-weight-bold ' + (isActive ? 'text-success' : 'text-muted') + '" for="switchStatus_' + item.id + '" id="labelStatus_' + item.id + '">' +
                                 (isActive ? 'Aktif' : 'Tidak Aktif') +
                                 '</label>' +
                                 '</div>';

            html += '<tr data-id="' + item.id + '" class="iklan-row">';
            html += '<td class="text-center align-middle drag-handle" title="Tahan dan geser untuk mengatur urutan"><i class="fas fa-grip-vertical text-muted"></i></td>';
            html += '<td class="text-center align-middle row-number">' + (idx + 1) + '</td>';
            html += '<td class="text-center align-middle">' + preview + '</td>';
            html += '<td class="align-middle"><strong>' + $('<div>').text(item.Judul).html() + '</strong><br>' + detailSub + '</td>';
            html += '<td class="text-center align-middle"><div>' + targetBadge + '</div><div class="mt-1">' + typeBadge + '</div></td>';
            html += '<td class="text-center align-middle">' + statusSwitch + '</td>';
            html += '<td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + item.id + '" title="Hapus"><i class="fas fa-trash"></i></button></td>';
            html += '</tr>';
        });

        $('#tbodyIklan').html(html);

        // Inisialisasi drag-and-drop sortable baris tabel
        initSortableTable();

        // Bind switch status change event (langsung tersimpan ke database via AJAX)
        $('.switch-status').off('change').on('change', function() {
            const $switch = $(this);
            const id = $switch.data('id');
            const isChecked = $switch.is(':checked');
            const $label = $('#labelStatus_' + id);
            const newStatusVal = isChecked ? 1 : 0;

            $switch.prop('disabled', true);

            $.post(toggleStatusUrl, {
                id: id,
                is_active: newStatusVal
            }, function(res) {
                $switch.prop('disabled', false);
                if (res.success) {
                    clearLocalIklanCache();
                    if (isChecked) {
                        $label.text('Aktif').removeClass('text-muted').addClass('text-success font-weight-bold');
                    } else {
                        $label.text('Tidak Aktif').removeClass('text-success font-weight-bold').addClass('text-muted');
                    }

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: res.message || 'Status iklan berhasil disimpan.'
                    });
                } else {
                    $switch.prop('checked', !isChecked);
                    Swal.fire('Gagal!', res.message || 'Gagal mengubah status iklan.', 'error');
                }
            }, 'json').fail(function() {
                $switch.prop('disabled', false);
                $switch.prop('checked', !isChecked);
                Swal.fire('Error!', 'Terjadi kesalahan koneksi server saat mengubah status.', 'error');
            });
        });

        // Bind delete event
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Media?',
                text: 'Media iklan ini akan dihapus dari server dan tidak akan ditampilkan lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        });
    }

    function updateSingleSelectOptions(data) {
        const $sel = $('#settingSingleId');
        const currentVal = $sel.val();
        $sel.find('option:not(:first)').remove();
        data.forEach(function(item) {
            const srcLabel = (item.SourceType && item.SourceType !== 'local') ? (' - ' + item.SourceType.toUpperCase()) : '';
            const opt = $('<option>').val(item.id).text(item.Judul + ' (' + item.MediaType + srcLabel + ')');
            if (item.id == currentVal) {
                opt.prop('selected', true);
            }
            $sel.append(opt);
        });
    }

    function initSortableTable() {
        if (typeof $.fn.sortable === 'undefined') {
            console.warn('jQuery UI Sortable belum dimuat.');
            return;
        }

        if ($('#tbodyIklan').hasClass('ui-sortable')) {
            $('#tbodyIklan').sortable('destroy');
        }

        $('#tbodyIklan').sortable({
            handle: '.drag-handle',
            items: 'tr.iklan-row',
            placeholder: 'iklan-row-placeholder',
            axis: 'y',
            cursor: 'grabbing',
            opacity: 0.85,
            helper: function(e, tr) {
                const $originals = tr.children();
                const $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            start: function(e, ui) {
                ui.placeholder.html('<td colspan="7">&nbsp;</td>');
            },
            update: function(e, ui) {
                // Perbarui visual nomor urut tabel
                $('#tbodyIklan tr.iklan-row').each(function(index) {
                    $(this).find('.row-number').text(index + 1);
                });

                // Kumpulkan urutan ID
                const orderIds = [];
                $('#tbodyIklan tr.iklan-row').each(function() {
                    const id = $(this).data('id');
                    if (id) {
                        orderIds.push(id);
                    }
                });

                if (orderIds.length <= 1) return;

                // Kirim AJAX reorder
                $.ajax({
                    url: reorderUrl,
                    type: 'POST',
                    data: { ids: orderIds },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            clearLocalIklanCache();
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: res.message || 'Urutan media iklan berhasil diperbarui.'
                            });
                        } else {
                            Swal.fire('Gagal!', res.message || 'Gagal menyimpan urutan media.', 'error');
                            loadTableMedia();
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan koneksi server saat memperbarui urutan.', 'error');
                        loadTableMedia();
                    }
                });
            }
        });
    }

    function deleteItem(id) {
        $.post(deleteUrl, { id: id }, function(res) {
            if (res.success) {
                Swal.fire('Terhapus!', res.message || 'Media iklan berhasil dihapus.', 'success');
                clearLocalIklanCache();
                loadTableMedia();
            } else {
                Swal.fire('Gagal!', res.message || 'Gagal menghapus media.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error!', 'Terjadi kesalahan pada server saat menghapus.', 'error');
        });
    }

    // Input file label
    $('#iklanFileInput').on('change', function() {
        const fileName = $(this).val().split('\\').pop() || $(this).val().split('/').pop();
        $(this).next('.custom-file-label').text(fileName || 'Pilih file...');
    });

    // Toggle row single image
    $('#settingMode').on('change', function() {
        if ($(this).val() === 'single') {
            $('#settingSingleRow').slideDown(200);
        } else {
            $('#settingSingleRow').slideUp(200);
        }
    });

    // Form Upload Submit
    $('#formUploadIklan').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.set('tahun', currentTahun);
        formData.set('tpq', currentTpq);

        const $btn = $('#btnSubmitUpload');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Media untuk ' + (currentTpq === '0' ? 'Munaqosah' : 'Pra-Munaqosah'));
                if (res.success) {
                    Swal.fire('Berhasil!', res.message || 'Media iklan berhasil disimpan.', 'success');
                    clearLocalIklanCache();
                    $('#formUploadIklan')[0].reset();
                    $('.custom-file-label').text('Pilih file gambar (16:9)...');

                    // Reset radio to local
                    $('input[name="source_type"][value="local"]').prop('checked', true).trigger('change');
                    $('#sourceTypeGroup label').removeClass('active');
                    $('#sourceTypeGroup label:first').addClass('active');

                    loadTableMedia();
                } else {
                    Swal.fire('Gagal!', res.message || 'Gagal menyimpan media.', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Media untuk ' + (currentTpq === '0' ? 'Munaqosah' : 'Pra-Munaqosah'));
                Swal.fire('Error!', 'Terjadi kesalahan server saat menyimpan data.', 'error');
            }
        });
    });

    // Form Settings Submit
    $('#formIklanSettings').on('submit', function(e) {
        e.preventDefault();
        const data = {
            iklan_interval: $('#settingInterval').val(),
            iklan_mode: $('#settingMode').val(),
            iklan_single_id: $('#settingSingleId').val(),
            iklan_video_enabled: $('#settingVideoEnabled').is(':checked') ? 1 : 0,
            header_format: $('#settingHeaderFormat').val(),
            iklan_fit: $('#settingFit').val(),
            tpq: currentTpq
        };

        const $btn = $('#btnSaveSettings');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.post(settingsUrl, data, function(res) {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Pengaturan');
            if (res.success) {
                Swal.fire('Tersimpan!', res.message || 'Pengaturan berhasil disimpan.', 'success');
                // Simpan juga ke localStorage
                localStorage.setItem('monitoringIklanInterval', data.iklan_interval);
                localStorage.setItem('monitoringIklanMode', data.iklan_mode);
                localStorage.setItem('monitoringIklanSingleId', data.iklan_single_id);
                localStorage.setItem('monitoringHeaderFormat', data.header_format);
                localStorage.setItem('monitoringIklanFit', data.iklan_fit);
            } else {
                Swal.fire('Gagal!', res.message || 'Gagal menyimpan pengaturan.', 'error');
            }
        }, 'json').fail(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Pengaturan');
            Swal.fire('Error!', 'Terjadi kesalahan server saat menyimpan pengaturan.', 'error');
        });
    });

    // Initial load
    loadTableMedia();
});
</script>
<?= $this->endSection(); ?>
