<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — TPQSmart Survey</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    
    <!-- Toastr & SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">

    <!-- Stand-alone Survey Public CSS -->
    <!-- Stand-alone Survey Public CSS -->
    <style>
/* Stand-alone Public Survey Stylesheet */

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    color: #2d3748;
    position: relative;
    overflow-x: hidden;
}

/* Background Bubbles for Premium Visuals */
.bg-bubble {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: -1;
    opacity: 0.35;
}
.bg-bubble-1 {
    top: -10%;
    left: -10%;
    width: 400px;
    height: 400px;
    background-color: var(--theme-color);
}
.bg-bubble-2 {
    bottom: -10%;
    right: -10%;
    width: 500px;
    height: 500px;
    background-color: #20c997;
}

/* Survey Main Container Card (Glassmorphism) */
.survey-public-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.survey-public-card:hover {
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
}
.survey-header-strip {
    height: 10px;
    background-color: var(--theme-color);
    width: 100%;
}

/* Typography Custom */
h1, h2, h3, h4, h5 {
    font-family: 'Outfit', sans-serif;
    color: #1a202c;
}

.survey-title {
    font-weight: 700;
    font-size: 2rem;
}
.survey-desc {
    color: #4a5568;
    font-size: 1.05rem;
    line-height: 1.6;
}

/* Question Section Title */
.survey-section-title {
    background-color: rgba(var(--theme-color-rgb), 0.06);
    border-left: 5px solid var(--theme-color);
    padding: 10px 18px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 1.25rem;
    color: #2d3748;
}

/* Question Row block */
.survey-question-item {
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #ffffff;
    margin-bottom: 18px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.survey-question-item:hover {
    border-color: rgba(var(--theme-color-rgb), 0.4);
    box-shadow: 0 4px 12px rgba(var(--theme-color-rgb), 0.03);
}
.question-title {
    font-weight: 600;
    font-size: 1.05rem;
    color: #2d3748;
    margin-bottom: 8px;
}
.question-required-star {
    color: #e53e3e;
    margin-left: 2px;
}
.question-help-desc {
    font-size: 0.85rem;
    color: #718096;
    margin-bottom: 12px;
}

/* Inputs & Form styling */
.form-control {
    border-radius: 8px;
    border: 1.5px solid #cbd5e0;
    padding: 10px 14px;
    transition: all 0.2s;
}
.form-control:focus {
    border-color: var(--theme-color);
    box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb), 0.15);
}

/* Hide number input spinners */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}

/* Custom Checkbox/Radio */
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--theme-color);
    border-color: var(--theme-color);
}
.custom-control-label {
    padding-top: 2px;
    cursor: pointer;
    font-size: 0.98rem;
    user-select: none;
}
.custom-control {
    margin-bottom: 8px;
}

/* Linear Scale styling */
.linear-scale-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 15px 10px;
    background-color: #f7fafc;
    border-radius: 8px;
    border: 1px solid #edf2f7;
}
.scale-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
}
.scale-item label {
    cursor: pointer;
    font-weight: 500;
}
.scale-item input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Star rating custom styling */
.star-rating-widget {
    direction: rtl;
    display: inline-flex;
}
.star-rating-widget input {
    display: none;
}
.star-rating-widget label {
    font-size: 2rem;
    color: #cbd5e0;
    cursor: pointer;
    transition: color 0.15s;
    margin-left: 5px;
}
.star-rating-widget label:hover,
.star-rating-widget label:hover ~ label,
.star-rating-widget input:checked ~ label {
    color: #ecc94b;
}

/* Grid Multiple/Checkbox */
.grid-table {
    width: 100%;
    margin-bottom: 0;
}
.grid-table th {
    font-weight: 600;
    font-size: 0.9rem;
    background-color: #f7fafc;
    text-align: center;
}
.grid-table td {
    vertical-align: middle !important;
    text-align: center;
}
.grid-table td:first-child {
    text-align: left;
    font-weight: 500;
}
.grid-table input[type="radio"],
.grid-table input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Progress bar dynamic styling */
.progress-bar-custom {
    height: 8px;
    border-radius: 4px;
    background-color: #edf2f7;
    overflow: hidden;
    margin-bottom: 20px;
}
.progress-bar-fill {
    height: 100%;
    background-color: var(--theme-color);
    transition: width 0.3s ease;
}

/* Image/Video block in Form */
.form-media-box {
    margin-bottom: 15px;
    text-align: center;
}
.form-media-box img {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

/* Button Premium styling */
.btn-theme {
    background-color: var(--theme-color);
    color: #ffffff;
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 24px;
    border: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(var(--theme-color-rgb), 0.25);
}
.btn-theme:hover {
    background-color: var(--theme-color);
    color: #ffffff;
    filter: brightness(0.9);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(var(--theme-color-rgb), 0.35);
}
.btn-theme:active {
    transform: translateY(0);
}

.btn-theme-outline {
    background-color: transparent;
    color: var(--theme-color);
    border: 2px solid var(--theme-color);
    border-radius: 8px;
    font-weight: 600;
    padding: 8px 24px;
    transition: all 0.2s;
}
.btn-theme-outline:hover {
    background-color: var(--theme-color);
    color: #ffffff;
    text-decoration: none;
}

/* Thank you page styles */
.thank-you-icon {
    font-size: 4.5rem;
    color: #48bb78;
    animation: scaleUp 0.4s ease-out;
}

@keyframes scaleUp {
    0% { transform: scale(0.7); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

/* Closed page styling */
.closed-icon {
    font-size: 4.5rem;
    color: #e53e3e;
}

/* Quill HTML Viewer Style */
.ql-view p {
    margin-bottom: 0.5rem;
}
.ql-view p:last-child {
    margin-bottom: 0;
}
.ql-view ul, .ql-view ol {
    padding-left: 20px;
    margin-bottom: 0.5rem;
}
.ql-view ul {
    list-style-type: disc;
}
.ql-view ol {
    list-style-type: decimal;
}
.ql-view strong {
    font-weight: bold;
}
.ql-view em {
    font-style: italic;
}
.ql-view u {
    text-decoration: underline;
}

/* Select2 validation style */
.select2-container .select2-selection.is-invalid {
    border-color: #e53e3e !important;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.15) !important;
}

    </style>

    <!-- Dynamic Theme Color Variable -->
    <style>
        :root {
            --theme-color: <?= esc($survey['theme_color'] ?? '#4285F4') ?>;
            --theme-color-rgb: <?= hexToRgb($survey['theme_color'] ?? '#4285F4') ?>;
        }
    </style>
</head>
<body>
    
    <!-- Subtle Background Elements -->
    <div class="bg-bubble bg-bubble-1"></div>
    <div class="bg-bubble bg-bubble-2"></div>
    
    <div class="container py-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toastr & SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <!-- Custom Survey Public JS -->
    <script>
        const BASE_URL = '<?= base_url() ?>';
        const SURVEY_KEY = '<?= $survey['survey_key'] ?>';
    </script>
    <script>
/**
 * Survey Public Form JavaScript
 * Handles Multi-section pagination, LocalStorage drafts, Cascading Dropdowns, and AJAX file uploads.
 */

let currentSectionIndex = 0;
let sectionsList = [];

$(document).ready(function() {
    initPublicForm();
});

function initPublicForm() {
    sectionsList = $('.section-slide');
    
    // Restrict non-numeric inputs for numeric fields (block 'e', 'E', '+')
    $(document).on('keydown keypress', 'input[type="number"]', function(e) {
        if (e.key === 'e' || e.key === 'E' || e.key === '+') {
            e.preventDefault();
        }
    });

    $(document).on('paste', 'input[type="number"]', function(e) {
        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text');
        if (isNaN(parseFloat(pastedData)) && !/^-?\d*([.,]\d+)?$/.test(pastedData)) {
            e.preventDefault();
        }
    });

    // 1. Pagination Setup
    if (sectionsList.length > 1) {
        showSection(0);
    } else {
        $('.progress-bar-custom').hide();
    }
    
    // 2. Select2 Initialization for database master dropdowns
    $('.select2-master').each(function() {
        $(this).select2({
            theme: 'bootstrap4',
            placeholder: $(this).data('placeholder') || 'Pilih data...'
        });
    });

    // Hide cascading select questions initially if no TPQ is selected
    $('.select2-master[data-linked-tpq-q-id]').each(function() {
        const targetSelect = $(this);
        const linkedTpqQId = targetSelect.data('linked-tpq-q-id');
        const tpqSelect = $(`.master-tpq-select[data-q-id="${linkedTpqQId}"]`);
        const targetQuestionItem = targetSelect.closest('.survey-question-item');
        
        if (tpqSelect.length > 0 && !tpqSelect.val()) {
            targetQuestionItem.hide();
        }
    });

    // 3. Cascading Dropdown Bindings (TPQ -> Guru/Santri)
    $(document).on('change', '.master-tpq-select', function() {
        const tpqId = $(this).val();
        const questionId = $(this).data('q-id');
        
        // Find linked guru or santri select elements
        $(`.select2-master[data-linked-tpq-q-id="${questionId}"]`).each(function() {
            const targetSelect = $(this);
            const targetQuestionItem = targetSelect.closest('.survey-question-item');
            const targetType = targetSelect.hasClass('master-guru-select') ? 'guru' : 'santri';
            
            // Clear current options
            targetSelect.empty().append('<option value="">Loading...</option>').trigger('change');
            
            if (!tpqId) {
                targetSelect.empty().append('<option value="">-- Pilih TPQ Terlebih Dahulu --</option>').trigger('change');
                targetQuestionItem.slideUp();
                return;
            }

            targetQuestionItem.slideDown();

            $.ajax({
                url: `${BASE_URL}survey/api/master`,
                method: 'POST',
                data: { type: targetType, tpq_id: tpqId, survey_key: SURVEY_KEY },
                success: function(response) {
                    if (response.success) {
                        targetSelect.empty().append('<option value="">Pilih data...</option>');
                        response.data.forEach(item => {
                            const desc = item.kelas ? ` (${item.kelas})` : '';
                            const disabledAttr = item.disabled ? ' disabled' : '';
                            targetSelect.append(`<option value="${item.id}" data-name="${item.name}"${disabledAttr}>${item.name}${desc}</option>`);
                        });
                        targetSelect.trigger('change');
                        
                        // Restore draft if any for this question
                        restoreDraftForLinkedSelect(targetSelect);
                    } else {
                        targetSelect.empty().append('<option value="">Gagal memuat data</option>').trigger('change');
                    }
                },
                error: function() {
                    targetSelect.empty().append('<option value="">Kesalahan server</option>').trigger('change');
                }
            });
        });
    });

    // 4. Set Names automatically on master select change (for respondent identity)
    $(document).on('change', '.select2-master', function() {
        const selectedOption = $(this).find('option:selected');
        const name = selectedOption.data('name');
        const id = $(this).val();

        // Set hidden fields for respondent identification if it matches survey settings
        if ($(this).hasClass('master-tpq-select')) {
            $('input[name="respondent_tpq_id"]').val(id);
            // If there's no guru or santri select on the page, the TPQ itself is the target
            if ($('.master-guru-select').length === 0 && $('.master-santri-select').length === 0) {
                $('input[name="respondent_ref_id"]').val(id);
                $('input[name="respondent_name"]').val(name);
            } else if ($('input[name="respondent_name"]').length > 0 && !$('input[name="respondent_name"]').val()) {
                $('input[name="respondent_name"]').val(name);
            }
        } else if ($(this).hasClass('master-guru-select') || $(this).hasClass('master-santri-select')) {
            $('input[name="respondent_ref_id"]').val(id);
            $('input[name="respondent_name"]').val(name);
        }

        saveDraft();

        // Cek duplikasi berdasarkan ref_id
        const refId = $('input[name="respondent_ref_id"]').val();
        const submitBtn = $('.btn-submit-survey');
        const nextBtn = $('.btn-next-section');
        const selectEl = $(this);

        if (refId) {
            $.ajax({
                url: `${BASE_URL}survey/api/check-duplicate`,
                method: 'POST',
                data: { survey_key: SURVEY_KEY, ref_id: refId },
                success: function(response) {
                    if (response.success && response.is_duplicate) {
                        Swal.fire({
                            title: 'Sudah Pernah Mengisi',
                            text: response.message || 'Identitas ini sudah terdeteksi mengisi survey ini.',
                            icon: 'warning'
                        });
                        selectEl.addClass('is-invalid');
                        submitBtn.prop('disabled', true);
                        nextBtn.prop('disabled', true);
                    } else {
                        selectEl.removeClass('is-invalid');
                        submitBtn.prop('disabled', false);
                        nextBtn.prop('disabled', false);
                    }
                }
            });
        } else {
            selectEl.removeClass('is-invalid');
            submitBtn.prop('disabled', false);
            nextBtn.prop('disabled', false);
        }
    });

    // 5. File Upload Handler
    $(document).on('change', '.file-upload-input', function(e) {
        const fileInput = this;
        const file = fileInput.files[0];
        if (!file) return;

        const questionId = $(this).data('q-id');
        const progressDiv = $(`#upload-progress-${questionId}`);
        const progressBar = progressDiv.find('.progress-bar');
        const previewDiv = $(`#upload-preview-${questionId}`);
        const hiddenInput = $(`#hidden-q-${questionId}`);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('question_id', questionId);
        formData.append('survey_key', SURVEY_KEY);

        progressDiv.removeClass('d-none');
        progressBar.css('width', '0%');
        previewDiv.empty();

        $.ajax({
            url: `${BASE_URL}survey/api/upload`,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        progressBar.css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                progressDiv.addClass('d-none');
                if (response.success) {
                    // Store file details as JSON in hidden input
                    const fileValue = JSON.stringify({
                        file_name: response.file_name,
                        file_path: response.file_path
                    });
                    hiddenInput.val(fileValue);
                    
                    previewDiv.html(`
                        <div class="alert alert-success py-1 px-2 mb-0 mt-2 small d-flex align-items-center">
                            <i class="fas fa-check-circle mr-2"></i> ${file.name} (Unggahan Berhasil)
                        </div>
                    `);
                    
                    saveDraft();
                } else {
                    toastr.error(response.message || 'Gagal mengunggah file.');
                    fileInput.value = '';
                }
            },
            error: function() {
                progressDiv.addClass('d-none');
                toastr.error('Kesalahan koneksi saat mengunggah.');
                fileInput.value = '';
            }
        });
    });

    // 6. Duplicate checking on unique field input blur
    $(document).on('blur', '.unique-verification-input', function() {
        const value = $(this).val().trim();
        const input = $(this);
        const submitBtn = $('.btn-submit-survey');

        if (!value) return;

        $.ajax({
            url: `${BASE_URL}survey/api/check-duplicate`,
            method: 'POST',
            data: { survey_key: SURVEY_KEY, value: value },
            success: function(response) {
                if (response.success && response.is_duplicate) {
                    Swal.fire({
                        title: 'Sudah Pernah Mengisi',
                        text: response.message || 'Anda terdeteksi sudah pernah mengisi survey ini.',
                        icon: 'warning'
                    });
                    input.addClass('is-invalid');
                    submitBtn.prop('disabled', true);
                } else {
                    input.removeClass('is-invalid');
                    submitBtn.prop('disabled', false);
                }
            }
        });
    });

    // 7. Auto-save Drafts (local storage bindings) & Clear validation highlights & Validate custom rules
    $(document).on('input change', 'input:not([type="file"]), textarea, select', function() {
        saveDraft();
        $(this).removeClass('is-invalid');
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
        }

        // Custom rules real-time validation
        const input = $(this);
        const rulesAttr = input.attr('data-rules');
        if (rulesAttr) {
            const value = input.val() ? input.val().trim() : '';
            let rules = {};
            try { rules = JSON.parse(rulesAttr); } catch (e) { rules = {}; }
            
            if (rules.rule_type && rules.rule_type !== 'none' && value !== '') {
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
            }
        }
    });

    // 8. Restore Drafts on Load
    restoreDraft();

    // Trigger updateNextButtonText on all slides initially
    $('.section-slide').each(function() {
        updateNextButtonText($(this));
    });

    // Option change listener to update next buttons re-actively
    $(document).on('change', 'input[type="radio"], select', function() {
        const slide = $(this).closest('.section-slide');
        if (slide.length > 0) {
            updateNextButtonText(slide);
        }
    });

    // 9. Pagination Clicks
    let navigationHistory = [];

    $('.btn-next-section').on('click', function() {
        if (validateSection(currentSectionIndex)) {
            const currentSlide = $(sectionsList[currentSectionIndex]);
            let nextDestination = null;

            // Check selected radio buttons
            const selectedRadio = currentSlide.find('input[type="radio"]:checked');
            if (selectedRadio.length > 0 && selectedRadio.data('next-section')) {
                nextDestination = selectedRadio.data('next-section');
            }

            // Check selected dropdowns
            currentSlide.find('select').each(function() {
                const selectedOpt = $(this).find('option:selected');
                if (selectedOpt.length > 0 && selectedOpt.data('next-section')) {
                    nextDestination = selectedOpt.data('next-section');
                }
            });

            navigationHistory.push(currentSectionIndex);

            if (nextDestination === 'submit') {
                // Trigger form submit
                $('#public-survey-form').submit();
            } else if (nextDestination) {
                const targetSlide = $(`.section-slide[data-section-id="${nextDestination}"]`);
                if (targetSlide.length > 0) {
                    const targetIdx = sectionsList.index(targetSlide);
                    if (targetIdx !== -1) {
                        currentSectionIndex = targetIdx;
                        showSection(currentSectionIndex);
                    } else {
                        currentSectionIndex++;
                        showSection(currentSectionIndex);
                    }
                } else {
                    currentSectionIndex++;
                    showSection(currentSectionIndex);
                }
            } else {
                currentSectionIndex++;
                showSection(currentSectionIndex);
            }
        }
    });

    $('.btn-prev-section').on('click', function() {
        if (navigationHistory.length > 0) {
            currentSectionIndex = navigationHistory.pop();
        } else {
            currentSectionIndex = Math.max(0, currentSectionIndex - 1);
        }
        showSection(currentSectionIndex);
    });

    // 10. Form Submission
    $('#public-survey-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateSection(currentSectionIndex)) {
            return;
        }

        const form = $(this);
        const submitBtn = form.find('.btn-submit-survey');
        const btnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...');

        // Clear any existing active_sections hidden inputs first
        form.find('input[name="active_sections[]"]').remove();

        // Add active sections as hidden inputs
        const visitedIndices = [...navigationHistory, currentSectionIndex];
        visitedIndices.forEach(idx => {
            const slide = $(sectionsList[idx]);
            const secId = slide.data('section-id');
            if (secId) {
                form.append(`<input type="hidden" name="active_sections[]" value="${secId}">`);
            }
        });

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Clear draft on successful submit
                    localStorage.removeItem(`survey_draft_${SURVEY_KEY}`);
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal Mengirim',
                        text: response.message || 'Terjadi kesalahan saat menyimpan tanggapan Anda.',
                        icon: 'error'
                    });
                    submitBtn.prop('disabled', false).html(btnHtml);
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Kesalahan Server',
                    text: 'Tidak dapat terhubung ke server.',
                    icon: 'error'
                });
                submitBtn.prop('disabled', false).html(btnHtml);
            }
        });
    });
}

// =============================================================
// Pagination Logic
// =============================================================

function showSection(index) {
    const currentSlide = $(sectionsList[index]);
    sectionsList.addClass('d-none');
    currentSlide.removeClass('d-none');
    
    // Update progress bar
    const progressPercent = Math.round(((index + 1) / sectionsList.length) * 100);
    $('.progress-bar-fill').css('width', progressPercent + '%');

    // Update next button text reactive state
    updateNextButtonText(currentSlide);

    // Scroll to top of form
    $('html, body').animate({ scrollTop: $('.survey-public-card').offset().top - 20 }, 300);
}

function validateSection(index) {
    const section = $(sectionsList[index]);
    let isValid = true;

    // Validate standard required input/select/textarea fields in this section (e.g. Identitas Responden)
    let standardRequiredValid = true;
    section.find('input[required], select[required], textarea[required]').each(function() {
        if ($(this).is(':hidden') || $(this).closest('.survey-question-item').is(':hidden')) {
            return true;
        }

        const value = $(this).val();
        if (!value || (typeof value === 'string' && value.trim() === '')) {
            standardRequiredValid = false;
            $(this).addClass('is-invalid');
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }
            
            let labelText = '';
            const id = $(this).attr('id');
            if (id) {
                const label = section.find(`label[for="${id}"]`);
                if (label.length > 0) {
                    labelText = label.text().replace('*', '').trim();
                }
            }
            if (!labelText) {
                labelText = $(this).attr('placeholder') || 'Kolom wajib';
            }

            toastr.error(`Silakan isi / pilih "${labelText}" terlebih dahulu.`);
            
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('open');
            } else {
                $(this).focus();
            }
            
            return false; // Break loop
        } else {
            $(this).removeClass('is-invalid');
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }
        }
    });

    if (!standardRequiredValid) {
        return false;
    }

    // Validate required questions in this section
    section.find('.survey-question-item[data-required="1"]').each(function() {
        const questionId = $(this).data('q-id');
        const questionType = $(this).data('type');
        const qText = $(this).find('.question-title').text().replace('*', '').trim();
        let answered = false;

        // Skip validation if the question is hidden (e.g. conditional cascading field)
        if ($(this).is(':hidden')) {
            return true;
        }

        if (['text_short', 'text_paragraph', 'date', 'time', 'master_tpq', 'master_guru', 'master_santri'].includes(questionType)) {
            const input = $(this).find('.form-control, select');
            if (input.val() && input.val().trim() !== '') {
                answered = true;
            }
        }
        
        else if (['multiple_choice', 'linear_scale'].includes(questionType)) {
            if ($(this).find('input[type="radio"]:checked').length > 0) {
                answered = true;
            }
        }
        
        else if (questionType === 'checkbox') {
            if ($(this).find('input[type="checkbox"]:checked').length > 0) {
                answered = true;
            }
        }
        
        else if (questionType === 'rating') {
            if ($(this).find('input[type="radio"]:checked').length > 0) {
                answered = true;
            }
        }
        
        else if (questionType === 'file_upload') {
            if ($(this).find('input[type="hidden"]').val() !== '') {
                answered = true;
            }
        }
        
        else if (['grid_multiple', 'grid_checkbox'].includes(questionType)) {
            // Each row must have a selected option
            const rows = $(this).find('table tbody tr');
            let rowsAnswered = 0;
            rows.each(function() {
                if ($(this).find('input:checked').length > 0) {
                    rowsAnswered++;
                }
            });
            if (rowsAnswered === rows.length) {
                answered = true;
            }
        }
        
        else if (['image_display', 'video_display'].includes(questionType)) {
            // Content fields don't need answers
            answered = true;
        }

        if (!answered) {
            isValid = false;
            $(this).addClass('border-danger');
            toastr.error(`Pertanyaan "${qText}" wajib diisi.`);
            
            // Scroll to un-answered question
            $('html, body').animate({
                scrollTop: $(this).offset().top - 100
            }, 300);
            
            return false; // Break loop
        } else {
            $(this).removeClass('border-danger');
        }
    });

    if (!isValid) {
        return false;
    }

    // Validate custom validation rules for text_short questions in this section
    section.find('.survey-question-item[data-type="text_short"]').each(function() {
        const item = $(this);
        const input = item.find('input[type="text"]');
        const value = input.val() ? input.val().trim() : '';
        const rulesAttr = input.attr('data-rules');
        const qText = item.find('.question-title').text().replace('*', '').trim();

        if (item.is(':hidden') || !rulesAttr) {
            return true;
        }

        let rules = {};
        try {
            rules = JSON.parse(rulesAttr);
        } catch (e) {
            rules = {};
        }

        if (!rules.rule_type || rules.rule_type === 'none') {
            return true;
        }

        if (value === '') {
            return true;
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
            isValid = false;
            input.addClass('is-invalid');
            input.next('.invalid-feedback').text(errorMsg);
            toastr.error(`Pertanyaan "${qText}": ${errorMsg}`);
            
            // Scroll to invalid question
            $('html, body').animate({
                scrollTop: item.offset().top - 100
            }, 300);
            
            return false; // Break loop
        } else {
            input.removeClass('is-invalid');
        }
    });

    return isValid;
}

// =============================================================
// LocalStorage Draft Caching
// =============================================================

function saveDraft() {
    // Only save if it's the actual form and not the preview page
    if ($('#public-survey-form').length === 0) return;

    const draft = {};
    
    // Core inputs
    $('input:not([type="file"]):not([type="password"]), textarea').each(function() {
        if ($(this).attr('name')) {
            if ($(this).attr('type') === 'radio' || $(this).attr('type') === 'checkbox') {
                if ($(this).is(':checked')) {
                    draft[$(this).attr('id') || $(this).attr('name') + '_' + $(this).val()] = true;
                }
            } else {
                draft[$(this).attr('name')] = $(this).val();
            }
        }
    });

    // Dropdown selects
    $('select').each(function() {
        if ($(this).attr('name')) {
            draft[$(this).attr('name')] = $(this).val();
        }
    });

    localStorage.setItem(`survey_draft_${SURVEY_KEY}`, JSON.stringify(draft));
}

function restoreDraft() {
    if ($('#public-survey-form').length === 0) return;

    const draftStr = localStorage.getItem(`survey_draft_${SURVEY_KEY}`);
    if (!draftStr) return;

    try {
        const draft = JSON.parse(draftStr);
        
        // Restore radio & checkbox inputs
        $('input[type="radio"], input[type="checkbox"]').each(function() {
            const id = $(this).attr('id');
            const nameValue = $(this).attr('name') + '_' + $(this).val();
            if (draft[id] || draft[nameValue]) {
                $(this).prop('checked', true).trigger('change');
            }
        });

        // Restore text inputs / textareas / select dropdowns
        $('input[type="text"], input[type="email"], input[type="tel"], input[type="datetime-local"], input[type="date"], input[type="time"], input[type="hidden"], textarea').each(function() {
            const name = $(this).attr('name');
            if (draft[name] !== undefined) {
                $(this).val(draft[name]).trigger('input').trigger('change');
            }
        });

        // Select elements (non-select2)
        $('select:not(.select2-master)').each(function() {
            const name = $(this).attr('name');
            if (draft[name] !== undefined) {
                $(this).val(draft[name]).trigger('change');
            }
        });

        // Trigger TPQ dropdowns to cascade load gurus/santri
        $('.master-tpq-select').each(function() {
            const name = $(this).attr('name');
            if (draft[name]) {
                $(this).val(draft[name]).trigger('change');
            }
        });

        // Restore file upload previews
        $('input[type="hidden"]').each(function() {
            const qId = $(this).data('q-id');
            const val = $(this).val();
            if (val && val.startsWith('{')) {
                try {
                    const fileObj = JSON.parse(val);
                    $(`#upload-preview-${qId}`).html(`
                        <div class="alert alert-success py-1 px-2 mb-0 mt-2 small d-flex align-items-center">
                            <i class="fas fa-check-circle mr-2"></i> ${fileObj.file_name} (Tersimpan)
                        </div>
                    `);
                } catch(e){}
            }
        });

    } catch(e) {
        console.error("Gagal memulihkan draf pengisian", e);
    }
}

function restoreDraftForLinkedSelect(targetSelect) {
    const draftStr = localStorage.getItem(`survey_draft_${SURVEY_KEY}`);
    if (!draftStr) return;

    try {
        const draft = JSON.parse(draftStr);
        const name = targetSelect.attr('name');
        if (draft[name]) {
            targetSelect.val(draft[name]).trigger('change');
        }
    } catch(e){}
}

function updateNextButtonText(slide) {
    const nextBtn = slide.find('.btn-next-section');
    if (nextBtn.length === 0) return;

    let isSubmitDestination = false;

    // Check radio buttons
    const selectedRadio = slide.find('input[type="radio"]:checked');
    if (selectedRadio.length > 0 && selectedRadio.data('next-section') === 'submit') {
        isSubmitDestination = true;
    }

    // Check dropdowns
    slide.find('select').each(function() {
        const selectedOpt = $(this).find('option:selected');
        if (selectedOpt.length > 0 && selectedOpt.data('next-section') === 'submit') {
            isSubmitDestination = true;
        }
    });

    if (isSubmitDestination) {
        nextBtn.html('Kirim Jawaban <i class="fas fa-paper-plane ml-1"></i>');
    } else {
        nextBtn.html('Berikutnya <i class="fas fa-arrow-right ml-1"></i>');
    }
}

    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
<?php
// Helper php function directly inside view to convert Hex to RGB for css transparency
function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}
?>
