<div class="modal-header bg-light">
    <h5 class="modal-title font-weight-bold" id="editResponseModalLabel">Edit Tanggapan: <?= esc($response['respondent_name'] ?? 'Anonim') ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="edit-response-form" action="<?= base_url("backend/survey/results/update-response/{$response['id']}") ?>" method="POST">
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <!-- Respondent Metadata Fields -->
        <div class="card card-outline card-warning shadow-xs mb-4">
            <div class="card-header py-1 px-3">
                <span class="text-warning small font-weight-bold"><i class="fas fa-user-edit mr-1"></i> Metadata Responden</span>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Nama Responden</label>
                        <input type="text" class="form-control form-control-sm" name="respondent_name" value="<?= esc($response['respondent_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Lembaga TPQ</label>
                        <select class="form-control form-control-sm" name="respondent_tpq_id">
                            <option value="">-- Lembaga Lain / Publik --</option>
                            <?php foreach ($tpqs as $tpq): ?>
                                <option value="<?= $tpq['id'] ?>" <?= $response['respondent_tpq_id'] == $tpq['id'] ? 'selected' : '' ?>><?= esc($tpq['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Email</label>
                        <input type="email" class="form-control form-control-sm" name="respondent_email" value="<?= esc($response['respondent_email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">No HP / WhatsApp</label>
                        <input type="text" class="form-control form-control-sm" name="respondent_phone" value="<?= esc($response['respondent_phone'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Answers Fields -->
        <div class="answers-edit-wrapper">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-edit mr-1 text-primary"></i> Edit Jawaban Pertanyaan</h6>
            
            <?php foreach ($questions as $idx => $q): ?>
                <?php 
                if (in_array($q['question_type'], ['image_display', 'video_display'])) continue; 
                $key = 'q_' . $q['id'];
                $ans = $answers[$key] ?? null;
                $isRequired = $q['is_required'] == 1;
                $type = $q['question_type'];
                ?>
                <div class="form-group border-bottom pb-3 mb-3">
                    <label class="font-weight-bold text-dark mb-1 small">
                        <?= $idx + 1 ?>. <?= strip_tags($q['question_text']) ?>
                        <?php if ($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                    </label>

                    <div class="pl-2">
                        <?php if ($type === 'text_short'): ?>
                            <?php 
                            $rules = [];
                            if (!empty($q['validation_rules'])) {
                                $rules = is_string($q['validation_rules']) ? json_decode($q['validation_rules'], true) : $q['validation_rules'];
                            }
                            $inputType = 'text';
                            $stepAttr = '';
                            if (!empty($rules['rule_type']) && $rules['rule_type'] === 'number') {
                                $inputType = 'number';
                                $stepAttr = 'step="any"';
                            }
                            ?>
                            <input type="<?= $inputType ?>" <?= $stepAttr ?> class="form-control form-control-sm" name="answers[<?= $key ?>]" value="<?= esc($ans) ?>" <?= $isRequired ? 'required' : '' ?> data-rules='<?= json_encode($rules) ?>'>
                            <div class="invalid-feedback font-weight-bold mt-1"></div>
                        
                        <?php elseif ($type === 'text_paragraph'): ?>
                            <textarea class="form-control form-control-sm" name="answers[<?= $key ?>]" rows="3" <?= $isRequired ? 'required' : '' ?>><?= esc($ans) ?></textarea>

                        <?php elseif ($type === 'date'): ?>
                            <input type="date" class="form-control form-control-sm w-50" name="answers[<?= $key ?>]" value="<?= esc($ans) ?>" <?= $isRequired ? 'required' : '' ?>>

                        <?php elseif ($type === 'time'): ?>
                            <input type="time" class="form-control form-control-sm w-25" name="answers[<?= $key ?>]" value="<?= esc($ans) ?>" <?= $isRequired ? 'required' : '' ?>>

                        <?php elseif ($type === 'checkbox'): ?>
                            <?php foreach ($q['options'] as $oIdx => $opt): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="edit-chk-<?= $q['id'] ?>-<?= $oIdx ?>" name="answers[<?= $key ?>][]" value="<?= esc($opt['option_text']) ?>" <?= is_array($ans) && in_array($opt['option_text'], $ans) ? 'checked' : '' ?> class="custom-control-input">
                                    <label class="custom-control-label small" for="edit-chk-<?= $q['id'] ?>-<?= $oIdx ?>"><?= esc($opt['option_text']) ?></label>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif ($type === 'multiple_choice' || $type === 'dropdown'): ?>
                            <select class="form-control form-control-sm" name="answers[<?= $key ?>]" <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($q['options'] as $opt): ?>
                                    <option value="<?= esc($opt['option_text']) ?>" <?= $ans === $opt['option_text'] ? 'selected' : '' ?>><?= esc($opt['option_text']) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ($type === 'linear_scale' || $type === 'rating'): ?>
                            <?php
                            $min = 1;
                            $max = 5;
                            if ($type === 'linear_scale') {
                                $settings = is_string($q['settings']) ? json_decode($q['settings'], true) : $q['settings'];
                                $min = isset($settings['min']) ? (int)$settings['min'] : 1;
                                $max = isset($settings['max']) ? (int)$settings['max'] : 5;
                            } else {
                                $settings = is_string($q['settings']) ? json_decode($q['settings'], true) : $q['settings'];
                                $max = isset($settings['max_stars']) ? (int)$settings['max_stars'] : 5;
                            }
                            ?>
                            <select class="form-control form-control-sm w-25" name="answers[<?= $key ?>]" <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih --</option>
                                <?php for ($i = $min; $i <= $max; $i++): ?>
                                    <option value="<?= $i ?>" <?= (int)$ans === $i ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>

                        <?php elseif ($type === 'master_tpq'): ?>
                            <select class="form-control form-control-sm" name="answers[<?= $key ?>]" <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih Lembaga TPQ --</option>
                                <?php foreach ($tpqs as $tpq): ?>
                                    <option value="<?= $tpq['id'] ?>" <?= $ans == $tpq['id'] ? 'selected' : '' ?>><?= esc($tpq['name']) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ($type === 'master_guru'): ?>
                            <select class="form-control form-control-sm" name="answers[<?= $key ?>]" <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= $ans == $g['id'] ? 'selected' : '' ?>><?= esc($g['name']) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ($type === 'master_santri'): ?>
                            <select class="form-control form-control-sm" name="answers[<?= $key ?>]" <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih Santri --</option>
                                <?php foreach ($santris as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $ans == $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif (in_array($type, ['grid_multiple', 'grid_checkbox'])): ?>
                            <?php
                            $settings = is_string($q['settings']) ? json_decode($q['settings'], true) : $q['settings'];
                            $rows = $settings['rows'] ?? [];
                            $cols = $settings['columns'] ?? [];
                            $gridAns = $ans ?? [];
                            $gridRows = $gridAns['rows'] ?? [];
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm small mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th></th>
                                            <?php foreach ($cols as $col): ?>
                                                <th class="text-center"><?= esc($col) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $rowIdx => $row): ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= esc($row) ?></td>
                                                <?php foreach ($cols as $colIdx => $col): ?>
                                                    <?php
                                                    $rowAns = $gridRows[$row] ?? null;
                                                    $isChecked = false;
                                                    if ($type === 'grid_multiple') {
                                                        $isChecked = $rowAns === $col;
                                                    } else {
                                                        $isChecked = is_array($rowAns) && in_array($col, $rowAns);
                                                    }
                                                    $gridInputName = $type === 'grid_multiple' 
                                                        ? "answers[{$key}][rows][{$row}]" 
                                                        : "answers[{$key}][rows][{$row}][]";
                                                    $gridInputId = "edit-grid-{$q['id']}-{$rowIdx}-{$colIdx}";
                                                    ?>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-<?= $type === 'grid_multiple' ? 'radio' : 'checkbox' ?> d-inline-block">
                                                            <input type="<?= $type === 'grid_multiple' ? 'radio' : 'checkbox' ?>" 
                                                                   id="<?= $gridInputId ?>"
                                                                   name="<?= $gridInputName ?>" 
                                                                   value="<?= esc($col) ?>" 
                                                                   <?= $isChecked ? 'checked' : '' ?>
                                                                   class="custom-control-input">
                                                            <label class="custom-control-label" for="<?= $gridInputId ?>"></label>
                                                        </div>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php elseif ($type === 'file_upload'): ?>
                            <?php if ($ans && strpos($ans, '{') === 0): ?>
                                <?php $fileObj = json_decode($ans, true); ?>
                                <div class="mb-2">
                                    <span class="small text-muted d-block">File saat ini:</span>
                                    <a href="<?= base_url($fileObj['file_path']) ?>" class="btn btn-xs btn-outline-info" target="_blank">
                                        <i class="fas fa-download mr-1"></i> Unduh File: <?= esc($fileObj['file_name']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="text" class="form-control form-control-sm" name="answers[<?= $key ?>]" value="<?= esc($ans) ?>" placeholder="JSON Path / Nama File jika ingin mengubah manual (biarkan kosong untuk tidak mengubah)">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm btn-save-response"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Restrict non-numeric inputs for numeric fields (block 'e', 'E', '+')
    $('#edit-response-form').on('keydown keypress', 'input[type="number"]', function(e) {
        if (e.key === 'e' || e.key === 'E' || e.key === '+') {
            e.preventDefault();
        }
    });

    $('#edit-response-form').on('paste', 'input[type="number"]', function(e) {
        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text');
        if (isNaN(parseFloat(pastedData)) && !/^-?\d*([.,]\d+)?$/.test(pastedData)) {
            e.preventDefault();
        }
    });

    // Real-time custom rules validation
    $('#edit-response-form').off('input change', 'input[data-rules]').on('input change', 'input[data-rules]', function() {
        const input = $(this);
        const value = input.val() ? input.val().trim() : '';
        const rulesAttr = input.attr('data-rules');
        if (!rulesAttr) return;

        let rules = {};
        try { rules = JSON.parse(rulesAttr); } catch (e) { rules = {}; }
        
        if (!rules.rule_type || rules.rule_type === 'none' || value === '') {
            input.removeClass('is-invalid');
            return;
        }

        const ruleType = rules.rule_type;
        const condition = rules.condition;
        const val1 = rules.value !== undefined ? rules.value : '';
        const val2 = rules.value_2 !== undefined ? rules.value_2 : '';
        let errorMsg = rules.error_message ? rules.error_message.trim() : '';

        let valid = true;

        if (ruleType === 'number') {
            const num = parseFloat(value);
            if (isNaN(num) || !/^-?\d*(\.\d+)?$/.test(value)) {
                valid = false;
                if (!errorMsg) errorMsg = 'Jawaban harus berupa angka.';
            } else {
                const v1 = parseFloat(val1);
                const v2 = parseFloat(val2);
                
                switch (condition) {
                    case 'between':
                        valid = (num >= v1 && num <= v2);
                        if (!errorMsg) errorMsg = `Angka harus antara ${v1} dan ${v2}.`;
                        break;
                    case 'not_between':
                        valid = (num < v1 || num > v2);
                        if (!errorMsg) errorMsg = `Angka tidak boleh antara ${v1} dan ${v2}.`;
                        break;
                    case 'greater_than':
                        valid = (num > v1);
                        if (!errorMsg) errorMsg = `Angka harus lebih dari ${v1}.`;
                        break;
                    case 'greater_than_or_equal':
                        valid = (num >= v1);
                        if (!errorMsg) errorMsg = `Angka harus lebih dari atau sama dengan ${v1}.`;
                        break;
                    case 'less_than':
                        valid = (num < v1);
                        if (!errorMsg) errorMsg = `Angka harus kurang dari ${v1}.`;
                        break;
                    case 'less_than_or_equal':
                        valid = (num <= v1);
                        if (!errorMsg) errorMsg = `Angka harus kurang dari atau sama dengan ${v1}.`;
                        break;
                    case 'equal':
                        valid = (num === v1);
                        if (!errorMsg) errorMsg = `Angka harus sama dengan ${v1}.`;
                        break;
                    case 'not_equal':
                        valid = (num !== v1);
                        if (!errorMsg) errorMsg = `Angka tidak boleh sama dengan ${v1}.`;
                        break;
                    case 'is_integer':
                        valid = Number.isInteger(num) && /^-?\d+$/.test(value);
                        if (!errorMsg) errorMsg = 'Jawaban harus berupa bilangan bulat.';
                        break;
                    case 'is_number':
                        valid = true;
                        break;
                }
            }
        } else if (ruleType === 'text') {
            switch (condition) {
                case 'contains':
                    valid = value.includes(val1);
                    if (!errorMsg) errorMsg = `Teks harus berisi "${val1}".`;
                    break;
                case 'not_contains':
                    valid = !value.includes(val1);
                    if (!errorMsg) errorMsg = `Teks tidak boleh berisi "${val1}".`;
                    break;
                case 'email':
                    valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    if (!errorMsg) errorMsg = 'Masukkan alamat email yang valid.';
                    break;
                case 'url':
                    try {
                        new URL(value);
                        valid = true;
                    } catch (_) {
                        valid = false;
                    }
                    if (!errorMsg) errorMsg = 'Masukkan URL yang valid.';
                    break;
            }
        } else if (ruleType === 'length') {
            const len = value.length;
            const v1 = parseInt(val1);
            switch (condition) {
                case 'min_length':
                    valid = (len >= v1);
                    if (!errorMsg) errorMsg = `Panjang karakter minimal ${v1} karakter.`;
                    break;
                case 'max_length':
                    valid = (len <= v1);
                    if (!errorMsg) errorMsg = `Panjang karakter maksimal ${v1} karakter.`;
                    break;
            }
        }

        if (!valid) {
            input.addClass('is-invalid');
            input.next('.invalid-feedback').text(errorMsg);
        } else {
            input.removeClass('is-invalid');
        }
    });

    $('#edit-response-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        
        // Trigger validation check
        form.find('input[data-rules]').trigger('change');
        if (form.find('.is-invalid').length > 0) {
            toastr.error('Terdapat data tidak valid.');
            return false;
        }

        const submitBtn = form.find('.btn-save-response');
        const btnHtml = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Close the modal cleanly
                    $('#editResponseModal').modal('hide');
                    
                    // Reload tab content after modal is completely closed to avoid orphaned backdrops
                    $('#editResponseModal').one('hidden.bs.modal', function () {
                        const tpqId = $('#filter-tpq').val() || '';
                        const url = `<?= base_url("backend/survey/results/responses/{$survey['id']}") ?>?tpq_id=${tpqId}`;
                        $('#tab-responses').html(`
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                <div>Memuat ulang tanggapan...</div>
                            </div>
                        `).load(url);
                    });
                    
                    // Fallback to force screen unlock if the Bootstrap hidden event is somehow swallowed
                    setTimeout(function() {
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 400);
                } else {
                    toastr.error(response.message || 'Gagal menyimpan perubahan.');
                    submitBtn.prop('disabled', false).html(btnHtml);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan koneksi.');
                submitBtn.prop('disabled', false).html(btnHtml);
            }
        });
    });
});
</script>
