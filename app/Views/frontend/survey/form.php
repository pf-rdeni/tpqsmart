<?= $this->extend('frontend/survey/template/survey_template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-9">
        
        <!-- Header Banner Survey Card -->
        <div class="card survey-public-card" style="border-top: 6px solid var(--theme-color);">
            <div class="card-body p-4 p-md-5">
                <h1 class="survey-title"><?= esc($survey['title']) ?></h1>
                <?php if ($survey['description']): ?>
                    <div class="survey-desc ql-view mt-3"><?= $survey['description'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Bar (Dynamic) -->
        <?php if (count($sections) > 1 && $survey['show_progress_bar']): ?>
            <div class="progress-bar-custom shadow-xs">
                <div class="progress-bar-fill" style="width: 0%;"></div>
            </div>
        <?php endif; ?>

        <!-- Public Survey Form -->
        <form action="<?= base_url('survey/submit') ?>" method="POST" id="public-survey-form">
            <!-- Hidden Fields for Verification & Master Data References -->
            <input type="hidden" name="survey_key" value="<?= esc($survey['survey_key']) ?>">
            <input type="hidden" name="respondent_ref_id" value="">
            <input type="hidden" name="respondent_tpq_id" value="">
            <?php if ($survey['target_type'] !== 'public'): ?>
                <input type="hidden" name="respondent_name" value="">
            <?php endif; ?>

            <?php
            // Group questions by section
            $sectionQuestions = [];
            $unsectionedQuestions = [];
            foreach ($questions as $q) {
                if ($q['section_id']) {
                    $sectionQuestions[$q['section_id']][] = $q;
                } else {
                    $unsectionedQuestions[] = $q;
                }
            }
            ?>

            <!-- Slide 0: General Info & Verification if active -->
            <?php
            $targetType = $survey['target_type'];
            $showSlide0 = ($targetType !== 'public' || $survey['allow_anonymous'] == 0 || $survey['unique_field_type'] !== 'none');
            ?>
            <?php if ($showSlide0): ?>
                <div class="section-slide">
                    <div class="survey-section-title mb-4">
                        <h4>Identitas Responden</h4>
                        <p class="text-muted small mb-0">Silakan isi identitas Anda sebelum memulai pengisian survey.</p>
                    </div>

                    <div class="survey-question-item">
                        <?php if ($targetType !== 'public'): ?>
                            <!-- Master Data Identitas Dropdowns -->
                            <?php if (in_array($targetType, ['tpq', 'guru', 'santri'])): ?>
                                <div class="form-group">
                                    <label for="identitas_tpq" class="font-weight-bold">Pilih Lembaga TPQ Anda <span class="text-danger">*</span></label>
                                    <select class="form-control select2-master master-tpq-select" id="identitas_tpq" data-q-id="identitas_tpq" data-placeholder="Pilih Lembaga TPQ..." required>
                                        <option value="">-- Pilih Lembaga TPQ --</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if ($targetType === 'guru'): ?>
                                <div class="form-group mt-3 survey-question-item">
                                    <label for="identitas_guru" class="font-weight-bold">Pilih Nama Guru <span class="text-danger">*</span></label>
                                    <select class="form-control select2-master master-guru-select" id="identitas_guru" data-q-id="identitas_guru" data-linked-tpq-q-id="identitas_tpq" data-placeholder="Pilih Guru..." required>
                                        <option value="">-- Pilih Guru --</option>
                                    </select>
                                </div>
                            <?php elseif ($targetType === 'santri'): ?>
                                <div class="form-group mt-3 survey-question-item">
                                    <label for="identitas_santri" class="font-weight-bold">Pilih Nama Santri <span class="text-danger">*</span></label>
                                    <select class="form-control select2-master master-santri-select" id="identitas_santri" data-q-id="identitas_santri" data-linked-tpq-q-id="identitas_tpq" data-placeholder="Pilih Santri..." required>
                                        <option value="">-- Pilih Santri --</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Generic Public Identitas Fields -->
                            <?php if ($survey['allow_anonymous'] == 0): ?>
                                <div class="form-group">
                                    <label for="resp_name" class="font-weight-bold">Nama Lengkap Anda <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control w-75" id="resp_name" name="respondent_name" placeholder="Masukkan nama lengkap..." required>
                                </div>
                            <?php endif; ?>

                            <?php if ($survey['unique_field_type'] === 'email'): ?>
                                <div class="form-group mt-3">
                                    <label for="resp_email" class="font-weight-bold">Alamat Email <?= $survey['unique_field_required'] == 1 ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <input type="email" class="form-control w-75 unique-verification-input" id="resp_email" name="respondent_email" 
                                           placeholder="nama@email.com" <?= $survey['unique_field_required'] == 1 ? 'required' : '' ?>>
                                    <small class="form-text text-muted">Satu email hanya diperbolehkan mengirim satu tanggapan.</small>
                                </div>
                            <?php elseif ($survey['unique_field_type'] === 'phone'): ?>
                                <div class="form-group mt-3">
                                    <label for="resp_phone" class="font-weight-bold">Nomor HP / WhatsApp <?= $survey['unique_field_required'] == 1 ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <input type="tel" class="form-control w-75 unique-verification-input" id="resp_phone" name="respondent_phone" 
                                           placeholder="Contoh: 08123456789" <?= $survey['unique_field_required'] == 1 ? 'required' : '' ?>>
                                    <small class="form-text text-muted">Satu nomor HP hanya diperbolehkan mengirim satu tanggapan.</small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-theme btn-next-section">Mulai Mengisi <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Slide 1: Questions without section (if any) -->
            <?php if (count($unsectionedQuestions) > 0 || (count($sections) === 0 && !$showSlide0)): ?>
                <div class="section-slide <?= $showSlide0 ? 'd-none' : '' ?>">
                    <?php foreach ($unsectionedQuestions as $q): ?>
                        <?= view('frontend/survey/question_item', ['q' => $q]) ?>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <?php if ($showSlide0): ?>
                            <button type="button" class="btn btn-theme-outline btn-prev-section"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if (count($sections) > 0): ?>
                            <button type="button" class="btn btn-theme btn-next-section">Berikutnya <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-theme btn-submit-survey">Kirim Jawaban <i class="fas fa-paper-plane ml-1"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Slide 2 onwards: Sections Slides -->
            <?php foreach ($sections as $index => $sec): ?>
                <?php
                $questionsInSection = $sectionQuestions[$sec['id']] ?? [];
                
                // Determine if this slide should be active initially
                $isFirstQuestionSlide = (count($unsectionedQuestions) === 0 && !$showSlide0);
                $isSlideActive = ($index === 0 && $isFirstQuestionSlide);
                $isLastSection = ($index === count($sections) - 1);
                ?>
                <div class="section-slide <?= !$isSlideActive ? 'd-none' : '' ?>" data-section-id="<?= $sec['id'] ?>">
                    <div class="survey-section-title mb-4">
                        <h4><?= esc($sec['title']) ?></h4>
                        <?php if ($sec['description']): ?>
                            <div class="text-muted small mb-0 ql-view"><?= $sec['description'] ?></div>
                        <?php endif; ?>
                    </div>

                    <?php foreach ($questionsInSection as $q): ?>
                        <?= view('frontend/survey/question_item', ['q' => $q]) ?>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-theme-outline btn-prev-section"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>

                        <?php if (!$isLastSection): ?>
                            <button type="button" class="btn btn-theme btn-next-section">Berikutnya <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-theme btn-submit-survey">Kirim Jawaban <i class="fas fa-paper-plane ml-1"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Fetch master TPQ data on public load
    $.ajax({
        url: '<?= base_url('survey/api/master') ?>',
        method: 'POST',
        data: { type: 'tpq', survey_key: SURVEY_KEY },
        success: function(response) {
            if (response.success) {
                const selects = $('.master-tpq-select');
                selects.empty().append('<option value="">-- Pilih Lembaga TPQ --</option>');
                response.data.forEach(t => {
                    const disabledAttr = t.disabled ? ' disabled' : '';
                    selects.append(`<option value="${t.id}" data-name="${t.name}"${disabledAttr}>${t.name}</option>`);
                });
                selects.trigger('change');
                
                // If draft contains tpq select, trigger restoration again
                restoreDraft();
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
