/**
 * Survey Form Builder JavaScript
 * Handles AJAX CRUD, Drag-and-Drop sorting, Inline Edits, and Settings Modal.
 */

let surveyData = {
    survey: {},
    sections: [],
    questions: []
};

$(document).ready(function() {
    initBuilder();
});

// =============================================================
// Initialization & Data Loading
// =============================================================

function initBuilder() {
    showSaveStatus('loading', 'Loading data...');
    
    // Fetch survey structure from server
    $.ajax({
        url: `${BASE_URL}backend/survey/builder/data/${SURVEY_ID}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                surveyData.survey = response.survey;
                surveyData.sections = response.sections || [];
                surveyData.questions = response.questions || [];
                
                renderCanvas();
                initSortable();
                initEvents();
                showSaveStatus('saved', 'Tersimpan');
            } else {
                showSaveStatus('error', 'Gagal memuat data');
                toastr.error(response.message || 'Gagal memuat data survey.');
            }
        },
        error: function() {
            showSaveStatus('error', 'Koneksi error');
            toastr.error('Terjadi kesalahan koneksi.');
        }
    });
}

function showSaveStatus(state, message) {
    const statusBadge = $('#save-status');
    statusBadge.removeClass('badge-warning badge-success badge-danger badge-info');
    
    if (state === 'loading') {
        statusBadge.addClass('badge-warning').html(`<i class="fas fa-sync-alt fa-spin mr-1"></i> ${message}`);
    } else if (state === 'saved') {
        statusBadge.addClass('badge-success').html(`<i class="fas fa-check mr-1"></i> ${message}`);
    } else if (state === 'saving') {
        statusBadge.addClass('badge-info').html(`<i class="fas fa-spinner fa-spin mr-1"></i> ${message}`);
    } else {
        statusBadge.addClass('badge-danger').html(`<i class="fas fa-exclamation-triangle mr-1"></i> ${message}`);
    }
}

// =============================================================
// Render Canvas Structure
// =============================================================

function renderCanvas() {
    const canvas = $('#survey-canvas');
    canvas.empty();
    
    // Sort sections by sort_order
    surveyData.sections.sort((a, b) => a.sort_order - b.sort_order);
    
    // Group questions by section_id
    const sectionQuestions = {};
    const unsectionedQuestions = [];
    
    surveyData.questions.sort((a, b) => a.sort_order - b.sort_order);
    
    surveyData.questions.forEach(q => {
        if (q.section_id) {
            if (!sectionQuestions[q.section_id]) sectionQuestions[q.section_id] = [];
            sectionQuestions[q.section_id].push(q);
        } else {
            unsectionedQuestions.push(q);
        }
    });
    
    // 1. Render Section Cards
    surveyData.sections.forEach(section => {
        const questionsInSection = sectionQuestions[section.id] || [];
        const sectionHtml = getSectionTemplate(section, questionsInSection);
        canvas.append(sectionHtml);
    });
    
    // 2. Render Unsectioned questions if any (usually fallback or at the start)
    if (unsectionedQuestions.length > 0 || surveyData.sections.length === 0) {
        let unsectionedHtml = '';
        if (surveyData.sections.length > 0) {
            // Render as a placeholder "Default Section" if sections exist
            unsectionedHtml = `
                <div class="card card-outline card-secondary shadow-sm mb-3">
                    <div class="card-header py-2">
                        <span class="text-muted small font-weight-bold">Bagian Awal (Tanpa Section)</span>
                    </div>
                    <div class="card-body p-3 question-list-container" data-section-id="">
                        ${unsectionedQuestions.map(q => getQuestionTemplate(q)).join('')}
                    </div>
                </div>
            `;
        } else {
            // If no sections at all, render questions directly into canvas
            unsectionedHtml = `
                <div class="question-list-container" data-section-id="">
                    ${unsectionedQuestions.map(q => getQuestionTemplate(q)).join('')}
                </div>
            `;
        }
        canvas.append(unsectionedHtml);
    }
    
    // Initialize tooltips / colorpickers
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize Quill rich text editors
    initQuillEditors();
}

function getSectionTemplate(section, questions) {
    return `
        <div class="card section-card mb-4" id="section-card-${section.id}" data-id="${section.id}">
            <div class="section-header">
                <div class="d-flex align-items-center flex-grow-1">
                    <span class="section-drag-handle mr-2"><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span>
                    <input type="text" class="form-control form-control-sm border-0 font-weight-bold section-title-input px-1" 
                           value="${escapeHtml(section.title)}" style="font-size: 1.15rem; background: transparent;" 
                           data-id="${section.id}" placeholder="Judul Bagian">
                </div>
                <div class="section-actions ml-2">
                    <button class="btn btn-tool text-danger btn-delete-section" data-id="${section.id}" title="Hapus Bagian">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-2 bg-light">
                <div class="form-group px-3 pt-2 mb-2">
                    <div class="quill-section-desc-editor" data-id="${section.id}">
                        <div class="quill-editor-area">${section.description || ''}</div>
                    </div>
                    <input type="hidden" class="section-desc-input" data-id="${section.id}" value="${escapeHtml(section.description || '')}">
                </div>
            </div>
            <div class="section-body question-list-container" data-section-id="${section.id}">
                ${questions.map(q => getQuestionTemplate(q)).join('')}
            </div>
        </div>
    `;
}

function getQuestionTemplate(q) {
    const typeLabel = getQuestionTypeLabel(q.question_type);
    const requiredChecked = q.is_required == 1 ? 'checked' : '';
    
    // Parse settings
    let settings = {};
    if (q.settings) {
        try {
            settings = typeof q.settings === 'string' ? JSON.parse(q.settings) : q.settings;
        } catch(e) { settings = {}; }
    }

    return `
        <div class="card question-card mb-3" id="question-card-${q.id}" data-id="${q.id}">
            <div class="question-card-header d-flex align-items-center">
                <span class="question-drag-handle mr-2"><i class="fas fa-grip-horizontal"></i></span>
                <span class="badge badge-light border text-muted">${typeLabel}</span>
                <div class="ml-auto d-flex align-items-center">
                    <div class="custom-control custom-switch mr-2">
                        <input type="checkbox" class="custom-control-input q-required-switch" id="req-${q.id}" data-id="${q.id}" ${requiredChecked}>
                        <label class="custom-control-label small font-weight-normal text-muted" for="req-${q.id}">Wajib isi</label>
                    </div>
                </div>
            </div>
            <div class="question-card-body">
                <div class="form-group">
                    <!-- Rich Text Editor for Question Title -->
                    <div class="quill-question-editor" data-id="${q.id}">
                        <div class="quill-editor-area quill-title-area">${q.question_text || ''}</div>
                    </div>
                    <input type="hidden" class="question-text-input" data-id="${q.id}" value="${escapeHtml(q.question_text)}">
                    <!-- Rich Text Editor for Question Description -->
                    <div class="quill-question-desc-editor mt-2" data-id="${q.id}">
                        <div class="quill-editor-area quill-desc-area">${q.description || ''}</div>
                    </div>
                    <input type="hidden" class="q-desc-input" data-id="${q.id}" value="${escapeHtml(q.description || '')}">
                </div>
                
                <div class="question-content-preview-${q.id} mt-3">
                    ${getQuestionPreviewContent(q, settings)}
                </div>
                
                <div class="question-actions-footer">
                    <button class="question-action-btn btn-settings-question" data-id="${q.id}" data-type="${q.question_type}">
                        <i class="fas fa-sliders-h mr-1"></i> Pengaturan
                    </button>
                    <div class="divider-vertical"></div>
                    <button class="question-action-btn btn-duplicate-question" data-id="${q.id}">
                        <i class="far fa-copy mr-1"></i> Salin
                    </button>
                    <div class="divider-vertical"></div>
                    <button class="question-action-btn text-danger btn-delete-question" data-id="${q.id}">
                        <i class="far fa-trash-alt mr-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    `;
}

function getQuestionTypeLabel(type) {
    const labels = {
        'text_short': 'Jawaban Singkat',
        'text_paragraph': 'Paragraf',
        'multiple_choice': 'Pilihan Ganda',
        'checkbox': 'Kotak Centang',
        'dropdown': 'Dropdown',
        'file_upload': 'Upload File',
        'linear_scale': 'Skalar Linier',
        'rating': 'Rating Bintang',
        'grid_multiple': 'Kisi Pilihan Ganda',
        'grid_checkbox': 'Petak Kotak Centang',
        'date': 'Tanggal',
        'time': 'Waktu',
        'master_tpq': 'Dropdown TPQ',
        'master_guru': 'Dropdown Guru',
        'master_santri': 'Dropdown Santri',
        'image_display': 'Konten Gambar',
        'video_display': 'Konten Video'
    };
    return labels[type] || 'Pertanyaan';
}

function getBranchingDropdownHtml(selectedVal, questionId, optionId) {
    let html = `<select class="form-control form-control-sm option-branch-select ml-2" style="width: 220px;" data-q-id="${questionId}" data-opt-id="${optionId}">`;
    html += `<option value="" ${!selectedVal ? 'selected' : ''}>Lanjutkan ke bagian berikutnya</option>`;
    html += `<option value="submit" ${selectedVal === 'submit' ? 'selected' : ''}>Kirim formulir</option>`;
    
    // Sort sections by sort_order
    const sortedSections = [...surveyData.sections].sort((a, b) => a.sort_order - b.sort_order);
    sortedSections.forEach((sec, idx) => {
        const isSelected = String(selectedVal) === String(sec.id) ? 'selected' : '';
        html += `<option value="${sec.id}" ${isSelected}>Buka bagian ${idx + 1} (${escapeHtml(sec.title)})</option>`;
    });
    
    html += `</select>`;
    return html;
}

function getQuestionPreviewContent(q, settings) {
    const type = q.question_type;
    
    if (['text_short', 'text_paragraph', 'date', 'time'].includes(type)) {
        let placeholder = settings.placeholder || 'Respon Anda...';
        if (type === 'date') placeholder = 'hh/bb/tttt';
        if (type === 'time') placeholder = '00:00';
        return `<input type="text" class="form-control form-control-sm w-50" disabled placeholder="${escapeHtml(placeholder)}">`;
    }
    
    if (['multiple_choice', 'checkbox', 'dropdown'].includes(type)) {
        let html = '<div class="options-edit-container" data-id="'+q.id+'">';
        const options = q.options || [];
        
        options.sort((a,b) => a.sort_order - b.sort_order);
        
        options.forEach(opt => {
            const icon = type === 'multiple_choice' ? 'far fa-circle' : (type === 'checkbox' ? 'far fa-square' : 'fas fa-arrow-right');
            const showBranch = ['multiple_choice', 'dropdown'].includes(type);
            const branchSelect = showBranch ? getBranchingDropdownHtml(opt.option_value, q.id, opt.id) : '';
            
            html += `
                <div class="option-row" data-opt-id="${opt.id}">
                    <span class="drag-option-handle"><i class="fas fa-grip-vertical"></i></span>
                    <i class="${icon} text-muted mr-2"></i>
                    <input type="text" class="form-control form-control-sm option-text-input" 
                           value="${escapeHtml(opt.option_text)}" data-id="${opt.id}" data-q-id="${q.id}">
                    ${branchSelect}
                    <span class="remove-option-btn" data-id="${opt.id}" data-q-id="${q.id}"><i class="fas fa-times"></i></span>
                </div>
            `;
        });
        
        html += `
            <div class="add-option-btn mt-2" data-q-id="${q.id}">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Opsi
            </div>
        `;
        html += '</div>';
        return html;
    }
    
    if (type === 'file_upload') {
        const allowed = settings.allowed_types || 'Semua File';
        const maxSize = settings.max_size_mb || '5';
        return `
            <div class="media-display-box text-left py-2 px-3">
                <i class="fas fa-cloud-upload-alt mr-2 text-primary"></i> <span class="text-muted">Tombol Upload File</span>
                <div class="small text-muted mt-1">Ekstensi: ${allowed} | Max Size: ${maxSize} MB</div>
            </div>
        `;
    }
    
    if (type === 'linear_scale') {
        const min = settings.min || 1;
        const max = settings.max || 5;
        const minLabel = settings.min_label || '';
        const maxLabel = settings.max_label || '';
        
        let radioHtml = '';
        for (let i = min; i <= max; i++) {
            radioHtml += `
                <div class="text-center mx-2">
                    <div>${i}</div>
                    <input type="radio" disabled name="preview-scale-${q.id}">
                </div>
            `;
        }
        
        return `
            <div class="d-flex align-items-center">
                <span class="mr-2 font-weight-bold text-muted">${minLabel}</span>
                ${radioHtml}
                <span class="ml-2 font-weight-bold text-muted">${maxLabel}</span>
            </div>
        `;
    }
    
    if (type === 'rating') {
        const maxStars = settings.max_stars || 5;
        let stars = '';
        for (let i = 0; i < maxStars; i++) {
            stars += '<i class="far fa-star text-warning fa-lg mr-1"></i>';
        }
        return `<div>${stars}</div>`;
    }
    
    if (['grid_multiple', 'grid_checkbox'].includes(type)) {
        const rows = settings.rows || ['Baris 1'];
        const cols = settings.columns || ['Kolom 1'];
        
        return `
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped text-center mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th></th>
                            ${cols.map(c => `<th>${escapeHtml(c)}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map(r => `
                            <tr>
                                <td class="text-left font-weight-bold">${escapeHtml(r)}</td>
                                ${cols.map(() => `<td><input type="${type === 'grid_multiple' ? 'radio' : 'checkbox'}" disabled></td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <small class="text-muted mt-1 d-block">Atur baris dan kolom di Menu Pengaturan</small>
            </div>
        `;
    }
    
    if (['master_tpq', 'master_guru', 'master_santri'].includes(type)) {
        const linkedText = settings.linked_tpq_question_id ? ' (Terhubung ke Dropdown TPQ)' : '';
        return `
            <div class="media-display-box text-left py-2 px-3">
                <i class="fas fa-database mr-2 text-info"></i> <span class="text-muted font-weight-bold">Dropdown Database: ${getQuestionTypeLabel(type)}</span> ${linkedText}
                <select class="form-control form-control-sm mt-2 w-50" disabled>
                    <option>Pilih data...</option>
                </select>
            </div>
        `;
    }
    
    if (type === 'image_display') {
        const url = settings.image_url ? `${BASE_URL}uploads/survey/images/${settings.image_url}` : 'https://placehold.co/600x300?text=Pilih+Gambar';
        return `
            <div class="media-display-box">
                <img src="${url}" alt="Display Preview" class="img-fluid" style="max-height: 200px;">
                <div class="text-muted small mt-2">${escapeHtml(settings.caption || 'Klik Pengaturan untuk upload gambar')}</div>
            </div>
        `;
    }
    
    if (type === 'video_display') {
        const url = settings.video_url || '';
        let embedUrl = '';
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            if (match && match[2].length == 11) {
                embedUrl = `https://www.youtube.com/embed/${match[2]}`;
            }
        }
        
        if (embedUrl) {
            return `
                <div class="media-display-box">
                    <div class="embed-responsive embed-responsive-16by9 mx-auto" style="max-width: 400px;">
                        <iframe class="embed-responsive-item" src="${embedUrl}" allowfullscreen></iframe>
                    </div>
                    <div class="text-muted small mt-2">${escapeHtml(settings.caption || '')}</div>
                </div>
            `;
        }
        return `
            <div class="media-display-box py-3 text-center">
                <i class="fab fa-youtube text-danger fa-2x"></i>
                <div class="text-muted small mt-2">Belum ada video tersemat. Masukkan URL YouTube di Pengaturan.</div>
            </div>
        `;
    }
    
    return '';
}

// =============================================================
// SortableJS Drag & Drop Sorting Bindings
// =============================================================

// =============================================================
// Quill Rich Text Editor Initialization
// =============================================================

const quillInstances = {}; // Store Quill instances by key

function initQuillEditors() {
    // Quill toolbar configuration (Google Forms-like)
    const titleToolbar = [
        [{ 'size': [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'align': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];

    const descToolbar = [
        ['bold', 'italic', 'underline'],
        [{ 'color': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];

    // Init question title editors
    document.querySelectorAll('.quill-question-editor .quill-editor-area').forEach(el => {
        const wrapper = el.closest('.quill-question-editor');
        const qId = wrapper.getAttribute('data-id');
        const key = `q-title-${qId}`;
        if (quillInstances[key]) return; // already initialized

        const quill = new Quill(el, {
            theme: 'snow',
            placeholder: 'Teks pertanyaan...',
            modules: { toolbar: titleToolbar }
        });

        quillInstances[key] = quill;

        // Save on blur
        quill.on('selection-change', function(range) {
            if (!range) {
                // Blur event
                const html = quill.root.innerHTML;
                const cleanHtml = html === '<p><br></p>' ? '' : html;
                $(wrapper).closest('.question-card').find('.question-text-input').val(cleanHtml);
                saveQuestion(qId);
            }
        });
    });

    // Init question description editors
    document.querySelectorAll('.quill-question-desc-editor .quill-editor-area').forEach(el => {
        const wrapper = el.closest('.quill-question-desc-editor');
        const qId = wrapper.getAttribute('data-id');
        const key = `q-desc-${qId}`;
        if (quillInstances[key]) return;

        const quill = new Quill(el, {
            theme: 'snow',
            placeholder: 'Deskripsi/instruksi tambahan (opsional)...',
            modules: { toolbar: descToolbar }
        });

        quillInstances[key] = quill;

        quill.on('selection-change', function(range) {
            if (!range) {
                const html = quill.root.innerHTML;
                const cleanHtml = html === '<p><br></p>' ? '' : html;
                $(wrapper).closest('.question-card').find('.q-desc-input').val(cleanHtml);
                saveQuestion(qId);
            }
        });
    });

    // Init section description editors
    document.querySelectorAll('.quill-section-desc-editor .quill-editor-area').forEach(el => {
        const wrapper = el.closest('.quill-section-desc-editor');
        const secId = wrapper.getAttribute('data-id');
        const key = `sec-desc-${secId}`;
        if (quillInstances[key]) return;

        const quill = new Quill(el, {
            theme: 'snow',
            placeholder: 'Deskripsi bagian (opsional)...',
            modules: { toolbar: descToolbar }
        });

        quillInstances[key] = quill;

        quill.on('selection-change', function(range) {
            if (!range) {
                const html = quill.root.innerHTML;
                const cleanHtml = html === '<p><br></p>' ? '' : html;
                $(wrapper).closest('.section-card').find('.section-desc-input').val(cleanHtml);
                saveSection(secId);
            }
        });
    });

    // Init survey description editor
    const surveyDescEl = document.querySelector('.quill-survey-desc-editor .quill-editor-area');
    if (surveyDescEl && !quillInstances['survey-desc']) {
        const quill = new Quill(surveyDescEl, {
            theme: 'snow',
            placeholder: 'Deskripsi atau instruksi pengerjaan survey...',
            modules: { toolbar: descToolbar }
        });
        quillInstances['survey-desc'] = quill;
        quill.on('selection-change', function(range) {
            if (!range) {
                const html = quill.root.innerHTML;
                const cleanHtml = html === '<p><br></p>' ? '' : html;
                $('#survey-description').val(cleanHtml);
                saveSurveyMetadata();
            }
        });
    }
}

function initSortable() {
    // 1. Sortable Canvas for Section sorting
    new Sortable(document.getElementById('survey-canvas'), {
        handle: '.section-drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function() {
            saveSectionOrder();
        }
    });

    // 2. Sortable Questions within & between sections
    document.querySelectorAll('.question-list-container').forEach(el => {
        new Sortable(el, {
            group: 'questions',
            handle: '.question-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            placeholderClass: 'sortable-placeholder',
            onEnd: function(evt) {
                saveQuestionOrder(evt);
            }
        });
    });

    // 3. Sortable Options within question multiple choice
    document.querySelectorAll('.options-edit-container').forEach(el => {
        new Sortable(el, {
            handle: '.drag-option-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const qId = el.getAttribute('data-id');
                saveOptionsOrder(qId);
            }
        });
    });
}

function saveSectionOrder() {
    showSaveStatus('saving', 'Menyimpan urutan bagian...');
    const items = [];
    $('#survey-canvas > .section-card').each(function(index) {
        items.push({
            id: $(this).data('id'),
            sort_order: index
        });
    });

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/reorder`,
        method: 'POST',
        data: JSON.stringify({ survey_id: SURVEY_ID, type: 'sections', items: items }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Urutan bagian disimpan');
                toastr.success(response.message);
            } else {
                showSaveStatus('error', 'Gagal menyimpan urutan');
                toastr.error(response.message);
            }
        }
    });
}

function saveQuestionOrder(evt) {
    showSaveStatus('saving', 'Menyimpan urutan...');
    
    const items = [];
    // Collect order from all sections
    $('.question-list-container').each(function() {
        const sectionId = $(this).data('section-id');
        $(this).children('.question-card').each(function(index) {
            items.push({
                id: $(this).data('id'),
                sort_order: index,
                section_id: sectionId ? parseInt(sectionId) : null
            });
        });
    });

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/reorder`,
        method: 'POST',
        data: JSON.stringify({ survey_id: SURVEY_ID, type: 'questions', items: items }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Urutan pertanyaan disimpan');
                toastr.success(response.message);
                
                // Update local model
                items.forEach(item => {
                    const q = surveyData.questions.find(x => x.id == item.id);
                    if (q) {
                        q.sort_order = item.sort_order;
                        q.section_id = item.section_id;
                    }
                });
            } else {
                showSaveStatus('error', 'Gagal menyimpan urutan');
                toastr.error(response.message);
            }
        }
    });
}

function saveOptionsOrder(questionId) {
    showSaveStatus('saving', 'Menyimpan opsi...');
    const qId = parseInt(questionId);
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    const optContainer = $(`.options-edit-container[data-id="${qId}"]`);
    const options = [];
    optContainer.find('.option-row').each(function(index) {
        const optId = $(this).data('opt-id');
        const originalOpt = q.options.find(o => o.id == optId);
        
        options.push({
            id: optId,
            option_text: $(this).find('.option-text-input').val(),
            option_value: $(this).find('.option-branch-select').val() || (originalOpt ? originalOpt.option_value : ''),
            sort_order: index
        });
    });

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-options`,
        method: 'POST',
        data: JSON.stringify({ question_id: qId, options: options }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Opsi berhasil diurutkan');
                q.options = response.options;
            } else {
                showSaveStatus('error', 'Gagal mengurutkan opsi');
            }
        },
        error: function() {
            showSaveStatus('error', 'Gagal mengurutkan opsi');
        }
    });
}

// =============================================================
// CRUD Operations
// =============================================================

function initEvents() {
    // 1. Survey Title and Description auto-save
    $('#survey-title, #survey-description').on('blur', function() {
        saveSurveyMetadata();
    });

    // 2. Click Toolbox Item -> Add Question
    $('.toolbox-item').on('click', function() {
        const type = $(this).data('type');
        addQuestion(type);
    });

    // 3. Add Section
    $('#btn-add-section').on('click', function() {
        addSection();
    });

    // 4. Inline Edit: Section Title & Desc
    $(document).on('blur', '.section-title-input, .section-desc-input', function() {
        const id = $(this).data('id');
        saveSection(id);
    });

    // 5. Delete Section
    $(document).on('click', '.btn-delete-section', function() {
        const id = $(this).data('id');
        deleteSection(id);
    });

    // 6. Inline Edit: Question Text & Desc
    $(document).on('blur', '.question-text-input, .q-desc-input', function() {
        const id = $(this).data('id');
        saveQuestion(id);
    });

    // 7. Toggle Required Switch
    $(document).on('change', '.q-required-switch', function() {
        const id = $(this).data('id');
        saveQuestion(id);
    });

    // 8. Delete Question
    $(document).on('click', '.btn-delete-question', function() {
        const id = $(this).data('id');
        deleteQuestion(id);
    });

    // 9. Duplicate Question
    $(document).on('click', '.btn-duplicate-question', function() {
        const id = $(this).data('id');
        duplicateQuestion(id);
    });

    // 10. Inline Edit Option Text
    $(document).on('blur', '.option-text-input', function() {
        const qId = $(this).data('q-id');
        saveOptions(qId);
    });

    // 11. Add Option Button
    $(document).on('click', '.add-option-btn', function() {
        const qId = $(this).data('q-id');
        addOptionRow(qId);
    });

    // 12. Remove Option Button
    $(document).on('click', '.remove-option-btn', function() {
        const id = $(this).data('id');
        const qId = $(this).data('q-id');
        removeOptionRow(id, qId);
    });

    // 13. Settings Modal Show
    $(document).on('click', '.btn-settings-question', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        showQuestionSettings(id, type);
    });

    // 14. Save Settings Modal Form
    $('#question-settings-form').on('submit', function(e) {
        e.preventDefault();
        saveQuestionSettings();
    });

    // 15. Change Option Branch Select
    $(document).on('change', '.option-branch-select', function() {
        const qId = $(this).data('q-id');
        saveOptions(qId);
    });

    // 16. Change Validation Rule Type
    $(document).on('change', '#val-rule-type', function() {
        updateValidationUI();
    });

    // 17. Change Validation Condition
    $(document).on('change', '#val-condition', function() {
        updateConditionInputs();
    });

    // Focus highlighing
    $(document).on('click', '.question-card', function() {
        $('.question-card').removeClass('active-question');
        $(this).addClass('active-question');
    });
}

function saveSurveyMetadata() {
    showSaveStatus('saving', 'Menyimpan metadata...');
    $.ajax({
        url: `${BASE_URL}backend/survey/update/${SURVEY_ID}`,
        method: 'POST',
        data: {
            title: $('#survey-title').val(),
            description: $('#survey-description').val()
        },
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Survey disimpan');
                surveyData.survey.title = $('#survey-title').val();
                surveyData.survey.description = $('#survey-description').val();
            } else {
                showSaveStatus('error', 'Gagal menyimpan');
            }
        }
    });
}

function addSection() {
    showSaveStatus('saving', 'Membuat bagian...');
    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-section`,
        method: 'POST',
        data: JSON.stringify({ survey_id: SURVEY_ID, title: 'Bagian Baru' }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                surveyData.sections.push(response.section);
                renderCanvas();
                initSortable();
                showSaveStatus('saved', 'Bagian dibuat');
                toastr.success(response.message);
                $(`#section-card-${response.id} .section-title-input`).focus().select();
            } else {
                showSaveStatus('error', 'Gagal membuat bagian');
            }
        }
    });
}

function saveSection(id) {
    showSaveStatus('saving', 'Menyimpan bagian...');
    const card = $(`#section-card-${id}`);
    const title = card.find('.section-title-input').val();
    
    // Get value from Quill if available, else fall back to hidden input
    let desc = '';
    const descQuill = quillInstances[`sec-desc-${id}`];
    if (descQuill) {
        const html = descQuill.root.innerHTML;
        desc = html === '<p><br></p>' ? '' : html;
    } else {
        desc = card.find('.section-desc-input').val();
    }

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-section`,
        method: 'POST',
        data: JSON.stringify({ survey_id: SURVEY_ID, id: id, title: title, description: desc }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Bagian disimpan');
                const sec = surveyData.sections.find(x => x.id == id);
                if (sec) {
                    sec.title = title;
                    sec.description = desc;
                }
            } else {
                showSaveStatus('error', 'Gagal menyimpan bagian');
            }
        }
    });
}

function deleteSection(id) {
    Swal.fire({
        title: 'Hapus Bagian?',
        text: 'Apakah Anda yakin ingin menghapus bagian ini? Pertanyaan di dalamnya akan dipindahkan ke tanpa bagian.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus Bagian',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showSaveStatus('saving', 'Menghapus...');
            $.ajax({
                url: `${BASE_URL}backend/survey/builder/delete-section/${id}`,
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Move questions locally to section_id null
                        surveyData.questions.forEach(q => {
                            if (q.section_id == id) q.section_id = null;
                        });
                        surveyData.sections = surveyData.sections.filter(x => x.id != id);
                        
                        renderCanvas();
                        initSortable();
                        showSaveStatus('saved', 'Bagian dihapus');
                        toastr.success(response.message);
                    } else {
                        showSaveStatus('error', 'Gagal menghapus');
                    }
                }
            });
        }
    });
}

function addQuestion(type) {
    showSaveStatus('saving', 'Membuat pertanyaan...');
    
    // Choose section_id to add. If active card is in a section, use it. Otherwise, use first section, or null.
    let sectionId = null;
    const activeCard = $('.active-question');
    if (activeCard.length > 0) {
        const parentSection = activeCard.closest('.section-card');
        if (parentSection.length > 0) {
            sectionId = parseInt(parentSection.data('id'));
        }
    } else if (surveyData.sections.length > 0) {
        sectionId = surveyData.sections[0].id;
    }

    const newQuestion = {
        survey_id: SURVEY_ID,
        section_id: sectionId,
        question_text: 'Pertanyaan Baru',
        question_type: type,
        is_required: 0,
        settings: {},
        options: []
    };

    // Preset some basic options for multiple choice types
    if (['multiple_choice', 'checkbox', 'dropdown'].includes(type)) {
        newQuestion.options = [
            { id: 0, option_text: 'Opsi 1', sort_order: 0 },
            { id: 0, option_text: 'Opsi 2', sort_order: 1 }
        ];
    }

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-question`,
        method: 'POST',
        data: JSON.stringify(newQuestion),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                surveyData.questions.push(response.question);
                
                renderCanvas();
                initSortable();
                showSaveStatus('saved', 'Pertanyaan dibuat');
                toastr.success(response.message);
                
                // Select and focus
                $(`#question-card-${response.id}`).addClass('active-question');
                $(`#question-card-${response.id} .question-text-input`).focus().select();
                
                // Scroll into view
                $('html, body').animate({
                    scrollTop: $(`#question-card-${response.id}`).offset().top - 150
                }, 400);
            } else {
                showSaveStatus('error', 'Gagal membuat pertanyaan');
            }
        }
    });
}

function saveQuestion(id) {
    showSaveStatus('saving', 'Menyimpan pertanyaan...');
    const card = $(`#question-card-${id}`);
    
    // Get value from Quill if available, else fall back to hidden input
    let text = '';
    const titleQuill = quillInstances[`q-title-${id}`];
    if (titleQuill) {
        const html = titleQuill.root.innerHTML;
        text = html === '<p><br></p>' ? '' : html;
    } else {
        text = card.find('.question-text-input').val();
    }
    
    let desc = '';
    const descQuill = quillInstances[`q-desc-${id}`];
    if (descQuill) {
        const html = descQuill.root.innerHTML;
        desc = html === '<p><br></p>' ? '' : html;
    } else {
        desc = card.find('.q-desc-input').val();
    }
    const isRequired = card.find('.q-required-switch').is(':checked') ? 1 : 0;
    
    const originalQ = surveyData.questions.find(x => x.id == id);
    if (!originalQ) return;

    // Use current settings and options
    const payload = {
        id: id,
        survey_id: SURVEY_ID,
        section_id: originalQ.section_id,
        question_text: text,
        question_type: originalQ.question_type,
        is_required: isRequired,
        sort_order: originalQ.sort_order,
        description: desc,
        settings: originalQ.settings
    };

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-question`,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Pertanyaan disimpan');
                originalQ.question_text = text;
                originalQ.description = desc;
                originalQ.is_required = isRequired;
            } else {
                showSaveStatus('error', 'Gagal menyimpan pertanyaan');
            }
        }
    });
}

function deleteQuestion(id) {
    Swal.fire({
        title: 'Hapus Pertanyaan?',
        text: 'Apakah Anda yakin ingin menghapus pertanyaan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showSaveStatus('saving', 'Menghapus...');
            $.ajax({
                url: `${BASE_URL}backend/survey/builder/delete-question/${id}`,
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        surveyData.questions = surveyData.questions.filter(x => x.id != id);
                        renderCanvas();
                        initSortable();
                        showSaveStatus('saved', 'Pertanyaan dihapus');
                        toastr.success(response.message);
                    } else {
                        showSaveStatus('error', 'Gagal menghapus');
                    }
                }
            });
        }
    });
}

function duplicateQuestion(id) {
    showSaveStatus('saving', 'Menyalin...');
    $.ajax({
        url: `${BASE_URL}backend/survey/builder/duplicate-question/${id}`,
        method: 'POST',
        success: function(response) {
            if (response.success) {
                surveyData.questions.push(response.question);
                renderCanvas();
                initSortable();
                showSaveStatus('saved', 'Pertanyaan disalin');
                toastr.success(response.message);
            } else {
                showSaveStatus('error', 'Gagal menyalin');
            }
        }
    });
}

function saveOptions(questionId) {
    showSaveStatus('saving', 'Menyimpan opsi...');
    const qId = parseInt(questionId);
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    const optContainer = $(`.options-edit-container[data-id="${qId}"]`);
    const options = [];
    optContainer.find('.option-row').each(function(index) {
        options.push({
            id: $(this).data('opt-id'),
            option_text: $(this).find('.option-text-input').val(),
            option_value: $(this).find('.option-branch-select').val() || '',
            sort_order: index
        });
    });

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-options`,
        method: 'POST',
        data: JSON.stringify({ question_id: qId, options: options }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Opsi disimpan');
                q.options = response.options;
            } else {
                showSaveStatus('error', 'Gagal menyimpan opsi');
            }
        },
        error: function() {
            showSaveStatus('error', 'Gagal menyimpan opsi');
        }
    });
}

function addOptionRow(qId) {
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    const optContainer = $(`.options-edit-container[data-id="${qId}"]`);
    const newSortOrder = q.options.length;
    
    // Add row to DOM temporarily
    const tempId = `temp-${Date.now()}`;
    const icon = q.question_type === 'multiple_choice' ? 'far fa-circle' : (q.question_type === 'checkbox' ? 'far fa-square' : 'fas fa-arrow-right');
    const showBranch = ['multiple_choice', 'dropdown'].includes(q.question_type);
    const branchSelect = showBranch ? getBranchingDropdownHtml('', qId, 0) : '';
    const newRow = `
        <div class="option-row" data-opt-id="0" id="${tempId}">
            <span class="drag-option-handle"><i class="fas fa-grip-vertical"></i></span>
            <i class="${icon} text-muted mr-2"></i>
            <input type="text" class="form-control form-control-sm option-text-input" 
                   value="Opsi Baru" data-id="0" data-q-id="${qId}">
            ${branchSelect}
            <span class="remove-option-btn" data-id="0" data-q-id="${qId}"><i class="fas fa-times"></i></span>
        </div>
    `;
    
    // Insert before "Add Option" button
    optContainer.find('.add-option-btn').before(newRow);
    $(`#${tempId} .option-text-input`).focus().select();
    
    // Trigger save immediately
    saveOptions(qId);
}

function removeOptionRow(optId, qId) {
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    if (q.options.length <= 1) {
        toastr.warning('Minimal harus terdapat 1 opsi jawaban.');
        return;
    }

    const card = $(`.options-edit-container[data-id="${qId}"]`);
    if (optId === 0 || isNaN(optId)) {
        // Just remove from DOM if it was temporary
        $(`.option-row[data-opt-id="${optId}"]`).remove();
    } else {
        // Remove from DOM & trigger batch save
        $(`.option-row[data-opt-id="${optId}"]`).remove();
        saveOptions(qId);
    }
}

// =============================================================
// Settings Modal Injection & Handlers
// =============================================================

function showQuestionSettings(qId, type) {
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    $('#settings-q-id').val(qId);
    $('#modalTitle').text(`Pengaturan: ${getQuestionTypeLabel(type)}`);
    
    let settings = {};
    if (q.settings) {
        try {
            settings = typeof q.settings === 'string' ? JSON.parse(q.settings) : q.settings;
        } catch(e) { settings = {}; }
    }
    
    let rules = {};
    if (q.validation_rules) {
        try {
            rules = typeof q.validation_rules === 'string' ? JSON.parse(q.validation_rules) : q.validation_rules;
        } catch(e) { rules = {}; }
    }

    let html = '';
    
    // Render validation configurations depending on type
    if (type === 'text_short') {
        const ruleType = rules.rule_type || 'none';
        const errorMsg = rules.error_message || '';
        html += `
            <div class="form-group">
                <label for="set-placeholder">Teks Placeholder (Hint)</label>
                <input type="text" class="form-control" id="set-placeholder" name="settings[placeholder]" value="${escapeHtml(settings.placeholder || '')}">
            </div>
            <div class="card card-outline card-info shadow-xs mt-3">
                <div class="card-header py-2">
                    <h6 class="card-title font-weight-bold mb-0 text-info"><i class="fas fa-shield-alt mr-1"></i> Validasi Respon</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Jenis Validasi</label>
                                <select class="form-control form-control-sm" id="val-rule-type" name="rules[rule_type]">
                                    <option value="none" ${ruleType === 'none' ? 'selected' : ''}>Tanpa Validasi</option>
                                    <option value="number" ${ruleType === 'number' ? 'selected' : ''}>Angka</option>
                                    <option value="text" ${ruleType === 'text' ? 'selected' : ''}>Teks</option>
                                    <option value="length" ${ruleType === 'length' ? 'selected' : ''}>Panjang</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="val-condition-col" style="display: ${ruleType !== 'none' ? 'block' : 'none'};">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Kondisi</label>
                                <select class="form-control form-control-sm" id="val-condition" name="rules[condition]"></select>
                            </div>
                        </div>
                        <div class="col-md-3" id="val-values-col" style="display: ${ruleType !== 'none' ? 'block' : 'none'};">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold" id="val-value-label">Nilai</label>
                                <div class="d-flex align-items-center" id="val-inputs-container"></div>
                            </div>
                        </div>
                        <div class="col-md-3" id="val-error-col" style="display: ${ruleType !== 'none' ? 'block' : 'none'};">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Teks Kesalahan Khusus</label>
                                <input type="text" class="form-control form-control-sm" id="val-error-message" name="rules[error_message]" value="${escapeHtml(errorMsg)}" placeholder="Teks kesalahan khusus">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'text_paragraph') {
        html += `
            <div class="form-group">
                <label for="set-placeholder">Teks Placeholder (Hint)</label>
                <input type="text" class="form-control" id="set-placeholder" name="settings[placeholder]" value="${escapeHtml(settings.placeholder || '')}">
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="rule-min">Jumlah Karakter Minimal</label>
                        <input type="number" class="form-control" id="rule-min" name="rules[min_length]" value="${rules.min_length || ''}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="rule-max">Jumlah Karakter Maksimal</label>
                        <input type="number" class="form-control" id="rule-max" name="rules[max_length]" value="${rules.max_length || ''}">
                    </div>
                </div>
            </div>
        `;
    }
    
    else if (type === 'file_upload') {
        html += `
            <div class="form-group">
                <label for="set-allowed">Tipe File yang Diizinkan (Koma sebagai pemisah)</label>
                <input type="text" class="form-control" id="set-allowed" name="settings[allowed_types]" value="${escapeHtml(settings.allowed_types || 'jpg,png,pdf,docx,xlsx')}" placeholder="pdf,jpg,png,zip">
                <small class="form-text text-muted">Contoh: pdf, png, jpg, jpeg</small>
            </div>
            <div class="form-group">
                <label for="set-maxsize">Ukuran File Maksimal (MB)</label>
                <input type="number" class="form-control w-25" id="set-maxsize" name="settings[max_size_mb]" value="${settings.max_size_mb || 5}">
            </div>
        `;
    }
    
    else if (type === 'linear_scale') {
        html += `
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="scale-min">Mulai Dari (Min)</label>
                        <select class="form-control w-50" id="scale-min" name="settings[min]">
                            <option value="0" ${settings.min == 0 ? 'selected' : ''}>0</option>
                            <option value="1" ${settings.min == 1 || !settings.min ? 'selected' : ''}>1</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="scale-max">Hingga (Max)</label>
                        <select class="form-control w-50" id="scale-max" name="settings[max]">
                            <option value="5" ${settings.max == 5 || !settings.max ? 'selected' : ''}>5</option>
                            <option value="10" ${settings.max == 10 ? 'selected' : ''}>10</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="scale-min-lbl">Label Nilai Minimum (Opsional)</label>
                        <input type="text" class="form-control" id="scale-min-lbl" name="settings[min_label]" value="${escapeHtml(settings.min_label || '')}" placeholder="Buruk / Kurang">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="scale-max-lbl">Label Nilai Maksimum (Opsional)</label>
                        <input type="text" class="form-control" id="scale-max-lbl" name="settings[max_label]" value="${escapeHtml(settings.max_label || '')}" placeholder="Sangat Baik">
                    </div>
                </div>
            </div>
        `;
    }
    
    else if (type === 'rating') {
        html += `
            <div class="form-group">
                <label for="star-max">Jumlah Bintang Maksimum</label>
                <select class="form-control w-25" id="star-max" name="settings[max_stars]">
                    <option value="3" ${settings.max_stars == 3 ? 'selected' : ''}>3 Bintang</option>
                    <option value="5" ${settings.max_stars == 5 || !settings.max_stars ? 'selected' : ''}>5 Bintang</option>
                    <option value="10" ${settings.max_stars == 10 ? 'selected' : ''}>10 Bintang</option>
                </select>
            </div>
        `;
    }
    
    else if (['grid_multiple', 'grid_checkbox'].includes(type)) {
        const rows = settings.rows || ['Baris 1'];
        const cols = settings.columns || ['Kolom 1'];
        
        html += `
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Daftar Baris (Satu baris per baris)</label>
                        <textarea class="form-control" name="settings[rows]" rows="5" placeholder="Baris 1\nBaris 2">${rows.join('\n')}</textarea>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Daftar Kolom (Satu kolom per baris)</label>
                        <textarea class="form-control" name="settings[columns]" rows="5" placeholder="Kolom 1\nKolom 2">${cols.join('\n')}</textarea>
                    </div>
                </div>
            </div>
        `;
    }
    
    else if (['master_guru', 'master_santri'].includes(type)) {
        // Find if there are other master_tpq questions in the survey for cascading dropdown
        const tpqQuestions = surveyData.questions.filter(x => x.question_type === 'master_tpq');
        
        let tpqOptions = '<option value="">-- Pilih Dropdown TPQ --</option>';
        tpqQuestions.forEach(tq => {
            const isSelected = settings.linked_tpq_question_id == tq.id ? 'selected' : '';
            tpqOptions += `<option value="${tq.id}" ${isSelected}>[Q-${tq.id}] ${escapeHtml(tq.question_text)}</option>`;
        });

        html += `
            <div class="form-group">
                <label for="set-link-tpq">Hubungkan dengan Pertanyaan Dropdown TPQ (Cascading)</label>
                <select class="form-control" id="set-link-tpq" name="settings[linked_tpq_question_id]">
                    ${tpqOptions}
                </select>
                <small class="form-text text-muted">
                    Jika dipilih, data guru/santri pada dropdown ini akan ter-filter otomatis secara real-time berdasarkan TPQ yang dipilih responden di pertanyaan TPQ tersebut.
                </small>
            </div>
        `;
    }
    
    else if (type === 'image_display') {
        html += `
            <div class="form-group">
                <label>Upload Gambar Baru</label>
                <input type="file" class="form-control-file" id="image-upload-input" accept="image/*">
                <input type="hidden" id="set-image-url" name="settings[image_url]" value="${escapeHtml(settings.image_url || '')}">
                <div class="progress mt-2 d-none" id="image-progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="mt-2 text-center" id="image-preview-container">
                    ${settings.image_url ? `<img src="${BASE_URL}uploads/survey/images/${settings.image_url}" class="img-thumbnail" style="max-height:150px;">` : ''}
                </div>
            </div>
            <div class="form-group">
                <label for="set-caption">Keterangan Gambar (Caption)</label>
                <input type="text" class="form-control" id="set-caption" name="settings[caption]" value="${escapeHtml(settings.caption || '')}">
            </div>
        `;
    }
    
    else if (type === 'video_display') {
        html += `
            <div class="form-group">
                <label for="set-video-url">URL Video YouTube</label>
                <input type="url" class="form-control" id="set-video-url" name="settings[video_url]" value="${escapeHtml(settings.video_url || '')}" placeholder="https://www.youtube.com/watch?v=...">
                <small class="form-text text-muted">Masukkan URL video youtube lengkap.</small>
            </div>
            <div class="form-group">
                <label for="set-caption">Keterangan Video (Caption)</label>
                <input type="text" class="form-control" id="set-caption" name="settings[caption]" value="${escapeHtml(settings.caption || '')}">
            </div>
        `;
    } else {
        html += '<p class="text-muted text-center py-3">Tidak ada konfigurasi tambahan untuk tipe pertanyaan ini.</p>';
    }

    $('#modal-settings-content').html(html);
    
    // Bind image upload logic if file input is present
    const fileInput = document.getElementById('image-upload-input');
    if (fileInput) {
        fileInput.addEventListener('change', uploadBuilderImage);
    }

    $('#questionSettingsModal').modal('show');
    
    if (type === 'text_short') {
        const container = $('#val-inputs-container');
        container.attr('data-val', rules.value !== undefined ? rules.value : '');
        container.attr('data-val2', rules.value_2 !== undefined ? rules.value_2 : '');
        
        const condSelect = $('#val-condition');
        condSelect.attr('data-selected', rules.condition || '');
        
        updateValidationUI(true);
    }
}

function uploadBuilderImage(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    const progressBar = $('#image-progress');
    const bar = progressBar.find('.progress-bar');
    progressBar.removeClass('d-none');
    bar.css('width', '0%');

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/upload-image`,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(evt) {
                if (evt.lengthComputable) {
                    const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                    bar.css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        success: function(response) {
            progressBar.addClass('d-none');
            if (response.success) {
                $('#set-image-url').val(response.file_name);
                $('#image-preview-container').html(`<img src="${response.url}" class="img-thumbnail" style="max-height:150px;">`);
                toastr.success('Gambar berhasil diunggah.');
            } else {
                toastr.error(response.message || 'Gagal mengunggah.');
            }
        },
        error: function() {
            progressBar.addClass('d-none');
            toastr.error('Kesalahan server.');
        }
    });
}

function saveQuestionSettings() {
    showSaveStatus('saving', 'Menyimpan pengaturan...');
    const qId = parseInt($('#settings-q-id').val());
    const q = surveyData.questions.find(x => x.id == qId);
    if (!q) return;

    const formData = $('#question-settings-form').serializeArray();
    const settings = {};
    let rules = {};

    formData.forEach(item => {
        // Parse settings[key]
        if (item.name.startsWith('settings[')) {
            const key = item.name.substring(9, item.name.length - 1);
            if (['rows', 'columns'].includes(key)) {
                // Split rows and columns text area into arrays
                settings[key] = item.value.split('\n').map(v => v.trim()).filter(v => v !== '');
            } else {
                settings[key] = item.value;
            }
        }
        // Parse rules[key]
        else if (item.name.startsWith('rules[')) {
            const key = item.name.substring(6, item.name.length - 1);
            if (item.value !== '') {
                rules[key] = isNaN(item.value) ? item.value : parseInt(item.value);
            }
        }
    });

    const payload = {
        id: qId,
        survey_id: SURVEY_ID,
        section_id: q.section_id,
        question_text: q.question_text,
        question_type: q.question_type,
        is_required: q.is_required,
        sort_order: q.sort_order,
        description: q.description,
        settings: settings,
        validation_rules: rules
    };

    if (payload.validation_rules.rule_type === 'none') {
        payload.validation_rules = {};
    }

    $.ajax({
        url: `${BASE_URL}backend/survey/builder/save-question`,
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showSaveStatus('saved', 'Pengaturan disimpan');
                toastr.success('Pertanyaan diperbarui.');
                
                // Update local state
                q.settings = response.question.settings;
                q.validation_rules = response.question.validation_rules;
                
                // Update preview in DOM
                const previewDiv = $(`.question-content-preview-${qId}`);
                previewDiv.html(getQuestionPreviewContent(q, response.question.settings));
                
                $('#questionSettingsModal').modal('hide');
            } else {
                showSaveStatus('error', 'Gagal menyimpan pengaturan');
                toastr.error(response.message || 'Gagal menyimpan.');
            }
        }
    });
}

// =============================================================
// Helper Helpers
// =============================================================

function escapeHtml(string) {
    if (!string) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return string.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// =============================================================
// Validation Conditions Mapping and Logic Helpers
// =============================================================

const validationConditions = {
    number: [
        { value: 'between', label: 'Antara' },
        { value: 'not_between', label: 'Tidak di antara' },
        { value: 'greater_than', label: 'Lebih dari' },
        { value: 'greater_than_or_equal', label: 'Lebih dari atau sama dengan' },
        { value: 'less_than', label: 'Kurang dari' },
        { value: 'less_than_or_equal', label: 'Kurang dari atau sama dengan' },
        { value: 'equal', label: 'Sama dengan' },
        { value: 'not_equal', label: 'Tidak sama dengan' },
        { value: 'is_number', label: 'Adalah angka' },
        { value: 'is_integer', label: 'Bilangan bulat' }
    ],
    text: [
        { value: 'contains', label: 'Berisi' },
        { value: 'not_contains', label: 'Tidak berisi' },
        { value: 'email', label: 'Alamat email' },
        { value: 'url', label: 'URL' }
    ],
    length: [
        { value: 'min_length', label: 'Jumlah karakter minimum' },
        { value: 'max_length', label: 'Jumlah karakter maksimum' }
    ]
};

function updateValidationUI(init = false) {
    const ruleType = $('#val-rule-type').val();
    const condCol = $('#val-condition-col');
    const valCol = $('#val-values-col');
    const errCol = $('#val-error-col');

    if (!ruleType || ruleType === 'none') {
        condCol.hide();
        valCol.hide();
        errCol.hide();
        return;
    }

    condCol.show();
    errCol.show();

    // Populate conditions
    const condSelect = $('#val-condition');
    const oldCond = condSelect.val() || (init ? condSelect.attr('data-selected') : '');
    condSelect.empty();

    const list = validationConditions[ruleType] || [];
    list.forEach(item => {
        const selected = item.value === oldCond ? 'selected' : '';
        condSelect.append(`<option value="${item.value}" ${selected}>${item.label}</option>`);
    });

    updateConditionInputs(init);
}

function updateConditionInputs(init = false) {
    const ruleType = $('#val-rule-type').val();
    const condition = $('#val-condition').val();
    const valCol = $('#val-values-col');
    const container = $('#val-inputs-container');
    
    const initVal = init ? container.attr('data-val') : '';
    const initVal2 = init ? container.attr('data-val2') : '';

    if (!ruleType || ruleType === 'none') {
        valCol.hide();
        return;
    }

    let numInputs = 1;
    let label = 'Nilai';

    if (['is_number', 'is_integer', 'email', 'url'].includes(condition)) {
        numInputs = 0;
    } else if (condition === 'between' || condition === 'not_between') {
        numInputs = 2;
        label = 'Rentang Nilai';
    }

    if (numInputs === 0) {
        valCol.hide();
        container.empty();
    } else {
        valCol.show();
        $('#val-value-label').text(label);
        
        let html = '';
        if (numInputs === 1) {
            const inputType = ruleType === 'number' || ruleType === 'length' ? 'number' : 'text';
            html = `<input type="${inputType}" class="form-control form-control-sm" name="rules[value]" value="${escapeHtml(initVal)}" required>`;
        } else if (numInputs === 2) {
            html = `
                <input type="number" class="form-control form-control-sm mr-2" name="rules[value]" value="${escapeHtml(initVal)}" placeholder="Min" required>
                <span class="small text-muted mr-2">dan</span>
                <input type="number" class="form-control form-control-sm" name="rules[value_2]" value="${escapeHtml(initVal2)}" placeholder="Max" required>
            `;
        }
        container.html(html);
    }
}
