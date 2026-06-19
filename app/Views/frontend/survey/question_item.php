<?php
// Parse settings & rules
$settings = [];
if (!empty($q['settings'])) {
    $settings = is_string($q['settings']) ? json_decode($q['settings'], true) : $q['settings'];
}
$rules = [];
if (!empty($q['validation_rules'])) {
    $rules = is_string($q['validation_rules']) ? json_decode($q['validation_rules'], true) : $q['validation_rules'];
}

$type = $q['question_type'];
$key = 'q_' . $q['id'];
$isRequired = $q['is_required'] == 1;

// Skip non-questions wrapper if just image/video display
$isContentField = in_array($type, ['image_display', 'video_display']);
?>

<div class="survey-question-item shadow-xs" data-q-id="<?= $q['id'] ?>" data-type="<?= $type ?>" data-required="<?= $q['is_required'] ?>">
    <?php if (!$isContentField): ?>
        <div class="question-title ql-view">
            <?= $q['question_text'] ?>
            <?php if ($isRequired): ?><span class="question-required-star">*</span><?php endif; ?>
        </div>
        <?php if ($q['description']): ?>
            <div class="question-help-desc ql-view"><?= $q['description'] ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="question-input-wrapper mt-2">
        <?php if ($type === 'text_short'): ?>
            <?php
            $minLen = '';
            $maxLen = '';
            if (!empty($rules['min_length'])) {
                $minLen = $rules['min_length'];
            } elseif (!empty($rules['rule_type']) && $rules['rule_type'] === 'length' && $rules['condition'] === 'min_length') {
                $minLen = $rules['value'];
            }

            if (!empty($rules['max_length'])) {
                $maxLen = $rules['max_length'];
            } elseif (!empty($rules['rule_type']) && $rules['rule_type'] === 'length' && $rules['condition'] === 'max_length') {
                $maxLen = $rules['value'];
            }
            ?>
            <input type="text" class="form-control" name="<?= $key ?>" placeholder="<?= esc($settings['placeholder'] ?? 'Jawaban Anda...') ?>" 
                   <?= $isRequired ? 'required' : '' ?> 
                   <?php if (!empty($minLen)): ?>minlength="<?= $minLen ?>"<?php endif; ?>
                   <?php if (!empty($maxLen)): ?>maxlength="<?= $maxLen ?>"<?php endif; ?>
                   data-rules='<?= json_encode($rules) ?>'>
            <div class="invalid-feedback font-weight-bold mt-1"></div>
        
        <?php elseif ($type === 'text_paragraph'): ?>
            <textarea class="form-control" name="<?= $key ?>" rows="3" placeholder="Jawaban panjang Anda..." 
                      <?= $isRequired ? 'required' : '' ?>
                      <?php if (!empty($rules['min_length'])): ?>minlength="<?= $rules['min_length'] ?>"<?php endif; ?>
                      <?php if (!empty($rules['max_length'])): ?>maxlength="<?= $rules['max_length'] ?>"<?php endif; ?>></textarea>

        <?php elseif ($type === 'multiple_choice'): ?>
            <?php foreach ($q['options'] as $idx => $opt): ?>
                <div class="custom-control custom-radio">
                    <input type="radio" id="opt-<?= $opt['id'] ?>-<?= $idx ?>" name="<?= $key ?>" value="<?= esc($opt['option_text']) ?>" data-next-section="<?= esc($opt['option_value'] ?? '') ?>" class="custom-control-input" <?= $isRequired ? 'required' : '' ?>>
                    <label class="custom-control-label" for="opt-<?= $opt['id'] ?>-<?= $idx ?>"><?= esc($opt['option_text']) ?></label>
                </div>
            <?php endforeach; ?>

        <?php elseif ($type === 'checkbox'): ?>
            <?php foreach ($q['options'] as $idx => $opt): ?>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" id="chk-<?= $opt['id'] ?>-<?= $idx ?>" name="<?= $key ?>[]" value="<?= esc($opt['option_text']) ?>" class="custom-control-input">
                    <label class="custom-control-label" for="chk-<?= $opt['id'] ?>-<?= $idx ?>"><?= esc($opt['option_text']) ?></label>
                </div>
            <?php endforeach; ?>

        <?php elseif ($type === 'dropdown'): ?>
            <select class="form-control" name="<?= $key ?>" <?= $isRequired ? 'required' : '' ?>>
                <option value="">-- Pilih salah satu --</option>
                <?php foreach ($q['options'] as $opt): ?>
                    <option value="<?= esc($opt['option_text']) ?>" data-next-section="<?= esc($opt['option_value'] ?? '') ?>"><?= esc($opt['option_text']) ?></option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($type === 'file_upload'): ?>
            <div class="custom-file">
                <input type="file" class="custom-file-input file-upload-input" id="file-<?= $q['id'] ?>" data-q-id="<?= $q['id'] ?>">
                <label class="custom-file-label" for="file-<?= $q['id'] ?>">Pilih file...</label>
            </div>
            <!-- Upload progress bar -->
            <div class="progress progress-sm mt-2 d-none" id="upload-progress-<?= $q['id'] ?>">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
            </div>
            <!-- File upload preview / status info -->
            <div class="upload-preview-info" id="upload-preview-<?= $q['id'] ?>"></div>
            <!-- Hidden input to store file path response -->
            <input type="hidden" name="<?= $key ?>" id="hidden-q-<?= $q['id'] ?>" data-q-id="<?= $q['id'] ?>" <?= $isRequired ? 'required' : '' ?>>

        <?php elseif ($type === 'linear_scale'): ?>
            <?php
            $min = isset($settings['min']) ? (int)$settings['min'] : 1;
            $max = isset($settings['max']) ? (int)$settings['max'] : 5;
            $minLabel = $settings['min_label'] ?? '';
            $maxLabel = $settings['max_label'] ?? '';
            ?>
            <div class="linear-scale-container">
                <span class="small font-weight-bold text-muted mt-4"><?= esc($minLabel) ?></span>
                <?php for ($i = $min; $i <= $max; $i++): ?>
                    <div class="scale-item text-center">
                        <label class="small text-muted mb-1" for="scale-<?= $q['id'] ?>-<?= $i ?>"><?= $i ?></label>
                        <input type="radio" id="scale-<?= $q['id'] ?>-<?= $i ?>" name="<?= $key ?>" value="<?= $i ?>" <?= $isRequired ? 'required' : '' ?>>
                    </div>
                <?php endfor; ?>
                <span class="small font-weight-bold text-muted mt-4"><?= esc($maxLabel) ?></span>
            </div>

        <?php elseif ($type === 'rating'): ?>
            <?php $maxStars = isset($settings['max_stars']) ? (int)$settings['max_stars'] : 5; ?>
            <div class="star-rating-widget">
                <?php for ($i = $maxStars; $i >= 1; $i--): ?>
                    <input type="radio" id="star-<?= $q['id'] ?>-<?= $i ?>" name="<?= $key ?>" value="<?= $i ?>" <?= $isRequired ? 'required' : '' ?>>
                    <label for="star-<?= $q['id'] ?>-<?= $i ?>"><i class="fas fa-star"></i></label>
                <?php endfor; ?>
            </div>

        <?php elseif (in_array($type, ['grid_multiple', 'grid_checkbox'])): ?>
            <?php
            $rows = $settings['rows'] ?? ['Baris 1'];
            $cols = $settings['columns'] ?? ['Kolom 1'];
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm grid-table">
                    <thead>
                        <tr>
                            <th></th>
                            <?php foreach ($cols as $col): ?>
                                <th><?= esc($col) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $rowIdx => $row): ?>
                            <tr>
                                <td><?= esc($row) ?></td>
                                <?php foreach ($cols as $colIdx => $col): ?>
                                    <?php
                                    $gridName = $type === 'grid_multiple' ? "{$key}[rows][{$row}]" : "{$key}[rows][{$row}][]";
                                    $inputId = "grid-{$q['id']}-{$rowIdx}-{$colIdx}";
                                    ?>
                                    <td>
                                        <input type="<?= $type === 'grid_multiple' ? 'radio' : 'checkbox' ?>" 
                                               id="<?= $inputId ?>" name="<?= $gridName ?>" value="<?= esc($col) ?>"
                                               <?= $isRequired && $type === 'grid_multiple' ? 'required' : '' ?>>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($type === 'date'): ?>
            <input type="date" class="form-control w-50" name="<?= $key ?>" <?= $isRequired ? 'required' : '' ?>>

        <?php elseif ($type === 'time'): ?>
            <input type="time" class="form-control w-25" name="<?= $key ?>" <?= $isRequired ? 'required' : '' ?>>

        <?php elseif ($type === 'master_tpq'): ?>
            <select class="form-control select2-master master-tpq-select" name="<?= $key ?>" data-q-id="<?= $q['id'] ?>" data-placeholder="Pilih Lembaga TPQ..." <?= $isRequired ? 'required' : '' ?>>
                <option value="">-- Pilih Lembaga TPQ --</option>
                <!-- Filled via public JS AJAX -->
            </select>

        <?php elseif ($type === 'master_guru'): ?>
            <?php $linkedTpqQId = $settings['linked_tpq_question_id'] ?? ''; ?>
            <select class="form-control select2-master master-guru-select" name="<?= $key ?>" data-q-id="<?= $q['id'] ?>" 
                    data-linked-tpq-q-id="<?= esc($linkedTpqQId) ?>" data-placeholder="Pilih Guru..." <?= $isRequired ? 'required' : '' ?>>
                <option value="">-- Pilih Guru --</option>
                <!-- Filled via public JS AJAX or disabled if linked tpq not selected -->
            </select>

        <?php elseif ($type === 'master_santri'): ?>
            <?php $linkedTpqQId = $settings['linked_tpq_question_id'] ?? ''; ?>
            <select class="form-control select2-master master-santri-select" name="<?= $key ?>" data-q-id="<?= $q['id'] ?>" 
                    data-linked-tpq-q-id="<?= esc($linkedTpqQId) ?>" data-placeholder="Pilih Santri..." <?= $isRequired ? 'required' : '' ?>>
                <option value="">-- Pilih Santri --</option>
                <!-- Filled via public JS AJAX or disabled if linked tpq not selected -->
            </select>

        <?php elseif ($type === 'image_display'): ?>
            <?php $imgUrl = !empty($settings['image_url']) ? base_url("uploads/survey/images/{$settings['image_url']}") : ''; ?>
            <?php if ($imgUrl): ?>
                <div class="form-media-box">
                    <img src="<?= $imgUrl ?>" class="img-fluid" alt="Branding media">
                    <?php if (!empty($settings['caption'])): ?>
                        <div class="text-muted small mt-2"><?= esc($settings['caption']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php elseif ($type === 'video_display'): ?>
            <?php
            $videoUrl = $settings['video_url'] ?? '';
            $embedUrl = '';
            if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                $regExp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/';
                preg_match($regExp, $videoUrl, $matches);
                if (isset($matches[2]) && strlen($matches[2]) === 11) {
                    $embedUrl = "https://www.youtube.com/embed/" . $matches[2];
                }
            }
            ?>
            <?php if ($embedUrl): ?>
                <div class="form-media-box">
                    <div class="embed-responsive embed-responsive-16by9 mx-auto" style="max-width: 500px;">
                        <iframe class="embed-responsive-item" src="<?= $embedUrl ?>" allowfullscreen></iframe>
                    </div>
                    <?php if (!empty($settings['caption'])): ?>
                        <div class="text-muted small mt-2"><?= esc($settings['caption']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
