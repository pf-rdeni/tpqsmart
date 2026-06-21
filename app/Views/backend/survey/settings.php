<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Alerts -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('message') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('backend/survey/save-settings/' . $survey['id']) ?>" method="POST" id="survey-settings-form">
            <div class="row">
                <!-- Left Column: Settings Options -->
                <div class="col-md-8">
                    <!-- General Settings -->
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-sliders-h mr-1 text-primary"></i> Pengaturan Umum</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul Survey</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= esc($survey['title']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= esc($survey['description']) ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status Aktif</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="draft" <?= $survey['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                            <option value="active" <?= $survey['status'] === 'active' ? 'selected' : '' ?>>Aktif (Bisa Diisi)</option>
                                            <option value="inactive" <?= $survey['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif / Ditutup</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="theme_color">Warna Tema Banner</label>
                                        <input type="color" class="form-control" id="theme_color" name="theme_color" value="<?= esc($survey['theme_color']) ?>" style="height: 38px; padding: 2px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quota & Dates Limit -->
                    <div class="card card-warning card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="far fa-calendar-alt mr-1 text-warning"></i> Batasan Waktu & Kuota</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Tanggal & Waktu Mulai</label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                               value="<?= $survey['start_date'] ? date('Y-m-d\TH:i', strtotime($survey['start_date'])) : '' ?>">
                                        <small class="text-muted">Kosongkan jika ingin segera aktif.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">Tanggal & Waktu Selesai</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                               value="<?= $survey['end_date'] ? date('Y-m-d\TH:i', strtotime($survey['end_date'])) : '' ?>">
                                        <small class="text-muted">Kosongkan jika tidak ada batas waktu.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group w-50">
                                <label for="max_responses">Batas Maksimal Respon (Kuota)</label>
                                <input type="number" class="form-control" id="max_responses" name="max_responses" 
                                       value="<?= $survey['max_responses'] ?>" placeholder="Contoh: 100">
                                <small class="text-muted">Form ditutup otomatis jika kuota terpenuhi. Kosongkan untuk tanpa batas.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Respondent Verification Options -->
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-user-shield mr-1 text-info"></i> Proteksi & Verifikasi Responden</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="allow_anonymous" name="allow_anonymous" <?= $survey['allow_anonymous'] == 1 ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-normal" for="allow_anonymous">Izinkan Pengisian Anonim</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="limit_one_response" name="limit_one_response" <?= $survey['limit_one_response'] == 1 ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-normal" for="limit_one_response">Batasi 1 Jawaban per Responden</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="allow_edit_response" name="allow_edit_response" <?= $survey['allow_edit_response'] == 1 ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-normal" for="allow_edit_response">Izinkan Responden Edit Jawaban</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unique_field_type">Verifikasi Responden Publik Via</label>
                                        <select class="form-control" id="unique_field_type" name="unique_field_type">
                                            <option value="none" <?= $survey['unique_field_type'] === 'none' ? 'selected' : '' ?>>Tanpa Verifikasi</option>
                                            <option value="email" <?= $survey['unique_field_type'] === 'email' ? 'selected' : '' ?>>Alamat Email Unik</option>
                                            <option value="phone" <?= $survey['unique_field_type'] === 'phone' ? 'selected' : '' ?>>Nomor HP/WhatsApp Unik</option>
                                        </select>
                                    </div>
                                    <div class="custom-control custom-checkbox" id="field-required-container" style="display: <?= $survey['unique_field_type'] !== 'none' ? 'block' : 'none' ?>;">
                                        <input type="checkbox" class="custom-control-input" id="unique_field_required" name="unique_field_required" <?= $survey['unique_field_required'] == 1 ? 'checked' : '' ?>>
                                        <label class="custom-control-label font-weight-normal text-danger" for="unique_field_required">Wajib diisi untuk submit</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Distribution -->
                    <div class="card card-purple card-outline shadow-sm" style="border-top-color: #6f42c1;">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold" style="color: #6f42c1;"><i class="fas fa-bullseye mr-1"></i> Target Distribusi</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="target_type">Jenis Target Data</label>
                                <select class="form-control w-50" id="target_type" name="target_type">
                                    <option value="public" <?= $survey['target_type'] === 'public' ? 'selected' : '' ?>>Publik (Tidak Terbatas)</option>
                                    <option value="tpq" <?= $survey['target_type'] === 'tpq' ? 'selected' : '' ?>>Lembaga TPQ</option>
                                    <option value="guru" <?= $survey['target_type'] === 'guru' ? 'selected' : '' ?>>Guru TPQ</option>
                                    <option value="santri" <?= $survey['target_type'] === 'santri' ? 'selected' : '' ?>>Santri TPQ</option>
                                </select>
                            </div>

                            <!-- Target specific TPQs selection -->
                            <div class="form-group" id="tpq-target-selection" style="display: <?= $survey['target_type'] !== 'public' ? 'block' : 'none' ?>;">
                                <label>Distribusi Ke Lembaga TPQ Mana Saja?</label>
                                <div class="select2-purple">
                                    <select class="form-control select2" multiple="multiple" name="targets[tpq][]" data-placeholder="Pilih TPQ Target..." style="width: 100%;">
                                        <!-- Will load via JS/PHP -->
                                    </select>
                                </div>
                                <small class="text-muted">Pilih satu atau beberapa TPQ target. Kosongkan untuk mendistribusikan ke SEMUA TPQ.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Public Results settings -->
                    <div class="card card-teal card-outline shadow-sm" style="border-top-color: #20c997;">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold" style="color: #20c997;"><i class="fas fa-chart-pie mr-1"></i> Halaman Hasil Publik</h3>
                        </div>
                        <div class="card-body">
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="public_result_enabled" name="public_result_enabled" <?= $survey['public_result_enabled'] == 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold" for="public_result_enabled">Aktifkan Halaman Hasil Publik</label>
                            </div>
                            
                            <div class="form-group" id="public-result-mode-container" style="display: <?= $survey['public_result_enabled'] == 1 ? 'block' : 'none' ?>;">
                                <label>Mode Hasil Publik</label>
                                <div class="row ml-1">
                                    <div class="custom-control custom-radio mr-4">
                                        <input class="custom-control-input" type="radio" id="mode-summary" name="public_result_mode" value="summary" <?= $survey['public_result_mode'] === 'summary' ? 'checked' : '' ?>>
                                        <label for="mode-summary" class="custom-control-label font-weight-normal">
                                            <strong>Mode Ringkasan (Summary)</strong><br>
                                            <span class="text-muted small">Hanya menampilkan siapa yang sudah/belum mengisi untuk saling memonitor. Tanpa menampilkan grafik isi jawaban.</span>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio mt-2">
                                        <input class="custom-control-input" type="radio" id="mode-detail" name="public_result_mode" value="detail" <?= $survey['public_result_mode'] === 'detail' ? 'checked' : '' ?>>
                                        <label for="mode-detail" class="custom-control-label font-weight-normal">
                                            <strong>Mode Detail Jawaban (Detail)</strong><br>
                                            <span class="text-muted small">Menampilkan ringkasan isi jawaban berupa grafik/chart dan daftar pengisian responden.</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Form Confirmation Page Text -->
                    <div class="card card-secondary card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-check-double mr-1 text-secondary"></i> Halaman Terima Kasih</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="confirmation_message">Pesan Konfirmasi (Setelah Submit)</label>
                                <textarea class="form-control" id="confirmation_message" name="confirmation_message" rows="3" 
                                          placeholder="Contoh: Terimakasih! Jawaban Anda telah kami catat."><?= esc($survey['confirmation_message'] ?? 'Terima kasih! Tanggapan Anda telah berhasil dikirim.') ?></textarea>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="show_progress_bar" name="show_progress_bar" <?= $survey['show_progress_bar'] == 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-normal" for="show_progress_bar">Tampilkan Progress Bar Pengisian</label>
                            </div>
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" id="shuffle_questions" name="shuffle_questions" <?= $survey['shuffle_questions'] == 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-normal" for="shuffle_questions">Acak Urutan Pertanyaan</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Share Info & Save Actions -->
                <div class="col-md-4">
                    <!-- Save Actions -->
                    <div class="card card-success shadow-sm">
                        <div class="card-body text-center p-3">
                            <button type="submit" class="btn btn-success btn-block btn-lg shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                            </button>
                            <a href="<?= base_url('backend/survey/edit/' . $survey['id']) ?>" class="btn btn-default btn-block mt-2">
                                <i class="fas fa-edit"></i> Edit Form di Builder
                            </a>
                        </div>
                    </div>

                    <!-- Share URL Info -->
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-share-alt mr-1 text-primary"></i> Link Distribusi</h3>
                        </div>
                        <div class="card-body text-center p-3">
                            <?php
                            $waTextPublic = "Silakan mengisi survey *" . $survey['title'] . "* melalui tautan berikut: " . $public_url;
                            $waUrlPublic = "https://api.whatsapp.com/send?text=" . urlencode($waTextPublic);

                            $waTextResult = "Berikut adalah hasil tanggapan survey *" . $survey['title'] . "*: " . $result_url;
                            $waUrlResult = "https://api.whatsapp.com/send?text=" . urlencode($waTextResult);
                            ?>
                            <div class="form-group">
                                <label class="text-left d-block">URL Pengisian Survey:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light" id="public-link" value="<?= $public_url ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="button" id="copy-link-btn" title="Salin Link">
                                            <i class="far fa-copy"></i>
                                        </button>
                                        <a href="<?= $public_url ?>" target="_blank" class="btn btn-outline-primary btn-sm" id="public-link-href" title="Buka di Tab Baru">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="<?= $waUrlPublic ?>" target="_blank" class="btn btn-success btn-sm" id="public-link-wa" title="Bagikan ke WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="text-left d-block text-warning"><i class="fas fa-unlock-alt mr-1"></i> URL Pengisian Bypass Admin:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light" id="bypass-link" value="<?= $public_url ?>?bypass=1" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-warning btn-sm" type="button" id="copy-bypass-btn" title="Salin Link Bypass">
                                            <i class="far fa-copy text-white"></i>
                                        </button>
                                        <a href="<?= $public_url ?>?bypass=1" target="_blank" class="btn btn-outline-warning btn-sm" id="bypass-link-href" title="Buka di Tab Baru">
                                            <i class="fas fa-external-link-alt font-weight-bold"></i>
                                        </a>
                                    </div>
                                </div>
                                <small class="text-muted text-left d-block mt-1">Gunakan link ini untuk mengisi survey meskipun survey ditutup, berakhir, atau kuota penuh.</small>
                            </div>
                            
                            <div class="form-group mt-3" id="result-link-group" style="display: <?= $survey['public_result_enabled'] == 1 ? 'block' : 'none' ?>;">
                                <label class="text-left d-block text-success">URL Hasil Publik:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light" id="result-link" value="<?= $result_url ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-success btn-sm" type="button" id="copy-result-btn" title="Salin Link">
                                            <i class="far fa-copy"></i>
                                        </button>
                                        <a href="<?= $result_url ?>" target="_blank" class="btn btn-outline-success btn-sm" id="result-link-href" title="Buka di Tab Baru">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="<?= $waUrlResult ?>" target="_blank" class="btn btn-success btn-sm" id="result-link-wa" title="Bagikan ke WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm btn-block mt-3" id="btn-regenerate-key">
                                <i class="fas fa-sync-alt mr-1"></i> Regenerasi URL Key
                            </button>
                        </div>
                    </div>


                </div>
            </div>
        </form>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
$(document).ready(function() {
    // Initialize Select2 target TPQ list
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Load TPQ list via AJAX
    $.ajax({
        url: '<?= base_url('backend/survey/builder/master-tpq') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('select[name="targets[tpq][]"]');
                select.empty();
                
                // Existing selected targets
                const selectedTargets = <?= json_encode(array_column($targets, 'target_ref_id')) ?>;

                response.data.forEach(t => {
                    const selected = selectedTargets.includes(t.id) ? 'selected' : '';
                    select.append(`<option value="${t.id}" ${selected}>${t.name}</option>`);
                });
                select.trigger('change');
            }
        }
    });



    // Copy Helper using the visible inputs (ensures 100% compatibility across HTTP/HTTPS and mobile device restrictions)
    function copyFromInput(inputId, successMsg) {
        const linkInput = document.getElementById(inputId);
        if (!linkInput) return;

        // Try modern Clipboard API first (requires secure context HTTPS or localhost)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(linkInput.value).then(function() {
                toastr.success(successMsg);
            }).catch(function() {
                fallbackCopyFromInput(linkInput, successMsg);
            });
        } else {
            fallbackCopyFromInput(linkInput, successMsg);
        }
    }

    function fallbackCopyFromInput(linkInput, successMsg) {
        // Temporarily remove readonly attribute to allow select/copy on all devices
        const isReadOnly = linkInput.hasAttribute('readonly');
        if (isReadOnly) {
            linkInput.removeAttribute('readonly');
        }

        linkInput.focus();
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // Mobile/iOS selection range

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                toastr.success(successMsg);
            } else {
                toastr.warning('Gagal otomatis. Silakan salin teks yang telah terblok.');
            }
        } catch (err) {
            toastr.warning('Gagal otomatis. Silakan salin teks yang telah terblok.');
        }

        // Restore readonly attribute
        if (isReadOnly) {
            linkInput.setAttribute('readonly', 'readonly');
        }
    }

    // Copy links
    $('#copy-link-btn').on('click', function() {
        copyFromInput('public-link', 'Link pendaftaran disalin ke clipboard.');
    });

    $('#copy-bypass-btn').on('click', function() {
        copyFromInput('bypass-link', 'Link bypass admin disalin ke clipboard.');
    });

    $('#copy-result-btn').on('click', function() {
        copyFromInput('result-link', 'Link hasil publik disalin ke clipboard.');
    });

    // Show/Hide target selection based on target type
    $('#target_type').on('change', function() {
        if ($(this).val() === 'public') {
            $('#tpq-target-selection').slideUp();
        } else {
            $('#tpq-target-selection').slideDown();
        }
    });

    // Show/Hide verification required switch
    $('#unique_field_type').on('change', function() {
        if ($(this).val() === 'none') {
            $('#field-required-container').slideUp();
            $('#unique_field_required').prop('checked', false);
        } else {
            $('#field-required-container').slideDown();
        }
    });

    // Show/Hide public results mode selection
    $('#public_result_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#public-result-mode-container').slideDown();
            $('#result-link-group').slideDown();
        } else {
            $('#public-result-mode-container').slideUp();
            $('#result-link-group').slideUp();
        }
    });

    // Regenerate survey key
    $('#btn-regenerate-key').on('click', function() {
        Swal.fire({
            title: 'Regenerasi URL Key?',
            text: 'Link yang lama tidak akan bisa diakses kembali setelah kunci diubah!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Regenerasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/survey/regenerate-key/' . $survey['id']) ?>',
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            $('#public-link').val(response.public_url);
                            $('#bypass-link').val(response.public_url + '?bypass=1');
                            $('#result-link').val(response.result_url);

                            $('#public-link-href').attr('href', response.public_url);
                            $('#bypass-link-href').attr('href', response.public_url + '?bypass=1');
                            $('#result-link-href').attr('href', response.result_url);

                            const titleVal = $('#title').val() || '<?= esc($survey['title']) ?>';
                            const waTextPublic = encodeURIComponent('Silakan mengisi survey *' + titleVal + '* melalui tautan berikut: ' + response.public_url);
                            $('#public-link-wa').attr('href', 'https://api.whatsapp.com/send?text=' + waTextPublic);

                            const waTextResult = encodeURIComponent('Berikut adalah hasil tanggapan survey *' + titleVal + '*: ' + response.result_url);
                            $('#result-link-wa').attr('href', 'https://api.whatsapp.com/send?text=' + waTextResult);

                            toastr.success('URL Key berhasil di-regenerasi.');
                        } else {
                            toastr.error('Gagal meregenerasi URL.');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
