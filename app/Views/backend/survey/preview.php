<?= $this->extend('frontend/survey/template/survey_template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-9">
        <!-- Preview Banner Notice -->
        <div class="alert alert-warning shadow-sm mb-4 d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-eye mr-2"></i> <strong>Mode Preview Admin</strong>. Anda melihat tampilan form responden. Submit dinonaktifkan.
            </div>
            <button onclick="window.close()" class="btn btn-warning btn-sm border-dark">Tutup Preview</button>
        </div>

        <!-- Render Survey Card Form -->
        <div class="card survey-public-card">
            <div class="survey-header-strip"></div>
            <div class="card-body p-4 p-md-5">
                <h1 class="survey-title"><?= esc($survey['title']) ?></h1>
                <div class="survey-desc ql-view mt-3"><?= $survey['description'] ?></div>
            </div>
        </div>

        <!-- Progress Bar if multi-section -->
        <?php if (count($sections) > 1 && $survey['show_progress_bar']): ?>
            <div class="progress-bar-custom shadow-xs">
                <div class="progress-bar-fill" style="width: 0%;"></div>
            </div>
        <?php endif; ?>

        <!-- Form questions wrapper -->
        <form id="public-survey-form" class="preview-mode-form" onsubmit="return false;">
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

            <!-- Render questions without section first or if no sections exist -->
            <?php if (count($unsectionedQuestions) > 0 || count($sections) === 0): ?>
                <div class="section-slide">
                    <?php foreach ($unsectionedQuestions as $q): ?>
                        <?= view('frontend/survey/question_item', ['q' => $q]) ?>
                    <?php endforeach; ?>
                    
                    <?php if (count($sections) > 0): ?>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-theme btn-next-section">Berikutnya <i class="fas fa-arrow-right ml-1"></i></button>
                        </div>
                    <?php else: ?>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-theme btn-submit-survey" disabled>Kirim Jawaban (Preview)</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Render Section slides -->
            <?php foreach ($sections as $index => $sec): ?>
                <?php 
                $questionsInSection = $sectionQuestions[$sec['id']] ?? []; 
                $isFirstSection = ($index === 0 && count($unsectionedQuestions) === 0);
                $isLastSection = ($index === count($sections) - 1);
                ?>
                <div class="section-slide <?= !$isFirstSection ? 'd-none' : '' ?>" data-section-id="<?= $sec['id'] ?>">
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
                        <?php if (!$isFirstSection): ?>
                            <button type="button" class="btn btn-theme-outline btn-prev-section"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if (!$isLastSection): ?>
                            <button type="button" class="btn btn-theme btn-next-section">Berikutnya <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-theme btn-submit-survey" disabled>Kirim Jawaban (Preview)</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
