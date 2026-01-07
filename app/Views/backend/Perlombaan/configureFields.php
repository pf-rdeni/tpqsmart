<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    /* Ensure image fits in cropper container */
    #image-to-crop {
        max-width: 100%;
        display: block;
    }
    .cropper-container {
        max-height: 500px;
    }
</style>
<section class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i> Konfigurasi Field Sertifikat
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Lomba:</strong> <?= esc($template['NamaLomba']) ?></p>
                        <p><strong>Cabang:</strong> <?= esc($template['NamaCabang']) ?></p>
                        <p><strong>Template:</strong> <?= esc($template['NamaTemplate']) ?> 
                           (<?= $template['Width'] ?>x<?= $template['Height'] ?>px)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Panel: Configuration Tabs -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-pills ml-auto p-2">
                             <li class="nav-item dropdown" style="width: 100%;">
                                <a class="nav-link dropdown-toggle font-weight-bold d-flex justify-content-between align-items-center" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" id="configMenuButton">
                                    <span>
                                        <i class="fas fa-edit mr-2"></i> <span id="currentSectionTitle">Fields (Data Sertifikat)</span>
                                    </span>
                                    <!-- <i class="fas fa-angle-down"></i> -->
                                </a>
                                <div class="dropdown-menu w-100 mt-0" aria-labelledby="configMenuButton">
                                    <span class="dropdown-header">Pilih Menu Konfigurasi</span>
                                    <div class="dropdown-divider"></div>
                                    
                                    <a href="#" class="dropdown-item config-menu-item active" data-section="fields" data-title="Fields (Data Sertifikat)">
                                        <i class="fas fa-check text-warning mr-2 icon-indicator"></i> 
                                        Fields (Data Sertifikat)
                                    </a>
                                    <a href="#" class="dropdown-item config-menu-item" data-section="rank" data-title="Label Peringkat & Juara">
                                        <i class="far fa-circle mr-2 icon-indicator"></i> 
                                        Label Peringkat & Juara
                                    </a>
                                    <a href="#" class="dropdown-item config-menu-item" data-section="sign" data-title="Penandatangan">
                                        <i class="far fa-circle mr-2 icon-indicator"></i> 
                                        Penandatangan
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="config-content-container">
                            <!-- Help Card - Collapsible -->
                            <div class="card card-warning card-outline collapsed-card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0"><i class="fas fa-question-circle text-warning"></i> Panduan</h6>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2" style="display: none;">
                                    <small class="text-muted">
                                        <ul class="mb-0 pl-3">
                                            <li><strong>Klik field</strong> di canvas untuk memilih</li>
                                            <li><strong>Drag & drop</strong> untuk memindahkan posisi</li>
                                            <li><strong>Resize handle</strong> (kotak biru) untuk mengubah ukuran</li>
                                            <li><strong>Arrow keys</strong> untuk pergerakan presisi (+ Shift = 10px)</li>
                                            <li><strong>Input X/Y</strong> untuk posisi manual</li>
                                            <li>Gambar tanda tangan: ukuran = font size</li>
                                            <li>Klik <strong>Preview PDF</strong> untuk melihat hasil akhir</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Section 1: Fields -->
                            <div class="config-section" id="section-fields">
                                <div class="form-group">
                                    <label>Tambah Field</label>
                                    <select class="form-control" id="selectField">
                                        <option value="">-- Pilih Field --</option>
                                        <?php foreach ($available_fields as $af): ?>
                                            <option value="<?= $af['name'] ?>" 
                                                    data-label="<?= $af['label'] ?>" 
                                                    data-sample="<?= $af['sample'] ?>">
                                                <?= $af['label'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <!-- Dynamic Signatory Fields will be appended here via JS -->
                                    </select>
                                    <button type="button" class="btn btn-sm btn-success mt-2" id="btnAddField">
                                        <i class="fas fa-plus"></i> Tambah Field
                                    </button>
                                </div>
                                <hr>
                                <div id="fieldsList" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Fields will be added here dynamically -->
                                </div>
                            </div>

                            <!-- Section 2: Rank Configuration -->
                            <div class="config-section" id="section-rank" style="display:none;">
                                <div class="form-group">
                                    <label>Jumlah Juara (1-X)</label>
                                    <input type="number" class="form-control" id="rankJuaraCount" value="3">
                                </div>
                                <div class="form-group">
                                    <label>Label Juara</label>
                                    <input type="text" class="form-control" id="rankLabelJuara" value="Juara">
                                    <small class="text-muted">Contoh Output: Juara 1</small>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label>Jumlah Harapan (1-Y)</label>
                                    <input type="number" class="form-control" id="rankHarapanCount" value="3">
                                </div>
                                <div class="form-group">
                                    <label>Label Harapan</label>
                                    <input type="text" class="form-control" id="rankLabelHarapan" value="Harapan">
                                    <small class="text-muted">Contoh Output: Harapan 1</small>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label>Label Peserta</label>
                                    <input type="text" class="form-control" id="rankLabelPeserta" value="Peserta">
                                    <small class="text-muted">Untuk peringkat setelah Harapan</small>
                                </div>
                            </div>

                            <!-- Section 3: Signatories -->
                            <div class="config-section" id="section-sign" style="display:none;">
                                <button type="button" class="btn btn-info btn-block mb-3" id="btnAddSignatory">
                                    <i class="fas fa-user-plus"></i> Tambah Penandatangan
                                </button>
                                <!-- Hidden input for replacing signature -->
                                <input type="file" id="hidden-file-input" style="display: none;" accept="image/png, image/jpeg">
                                <div id="signatoriesList"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-block mb-2" id="btnSaveConfig">
                            <i class="fas fa-save"></i> Simpan Konfigurasi
                        </button>
                        <a href="<?= base_url('backend/perlombaan/preview-sertifikat/' . $template['id']) ?>" target="_blank" class="btn btn-info btn-block mb-2">
                             <i class="fas fa-eye"></i> Preview PDF
                        </a>
                        <a href="<?= base_url('backend/perlombaan/template-sertifikat/' . $template['cabang_id']) ?>" 
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Canvas Preview -->
            <div class="col-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> Preview Template</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="btnTogglePreview">
                                <i class="fas fa-sync"></i> Refresh Preview
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="background: #f4f4f4; overflow: auto;">
                        <div style="position: relative; display: inline-block;">
                            <canvas id="templateCanvas" 
                                    width="<?= $template['Width'] ?>" 
                                    height="<?= $template['Height'] ?>"
                                    style="border: 1px solid #ddd; cursor: crosshair; max-width: 100%; height: auto;">
                            </canvas>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Klik pada canvas untuk menempatkan field. Drag untuk memindahkan posisi.
                                <br><i class="fas fa-keyboard"></i> Gunakan tombol panah keyboard atau klik tombol arrow untuk geser field (Shift+Arrow = 10px).
                                <br><i class="fas fa-expand-arrows-alt"></i> Drag kotak biru di sudut kanan bawah untuk mengubah ukuran font.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" id="templateId" value="<?= $template['id'] ?>">
<input type="hidden" id="templatePath" value="<?= base_url('uploads/' . $template['FileTemplate']) ?>">

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    let canvas, ctx;
    let templateImage = new Image();
    let fields = [];
    let selectedFieldIndex = -1;
    let isDragging = false;
    let isResizing = false;
    let resizeStartY = 0;
    let resizeStartFontSize = 0;
    let dragOffset = {x: 0, y: 0};
    var hasUnsavedChanges = false;
    var autoSaveKey = 'tq_smart_cert_config_' + <?= $template['id'] ?>;

    // Initialize Settings
    var rankSettings = <?= !empty($template['RankSettings']) ? $template['RankSettings'] : '{"juaraCount":3, "labelJuara":"Juara", "harapanCount":3, "labelHarapan":"Harapan", "labelPeserta":"Peserta"}' ?>;
    var signatorySettings = <?= !empty($template['SignatorySettings']) ? $template['SignatorySettings'] : '[]' ?>;

    // Helper to generate dynamic fields from signatories
    function updateAvailableFields() {
        var select = $('#selectField');
        // Remove old dynamic fields
        select.find('option[data-dynamic="true"]').remove();

        signatorySettings.forEach((sig, index) => {
            var prefix = 'Sig' + (index + 1) + '_';
            // Name
            select.append(`<option value="${prefix}Name" data-label="Ttd ${index+1}: Nama" data-sample="${sig.name}" data-dynamic="true">Ttd ${index+1}: Nama (${sig.name})</option>`);
            // Job
            select.append(`<option value="${prefix}Jabatan" data-label="Ttd ${index+1}: Jabatan" data-sample="${sig.jabatan}" data-dynamic="true">Ttd ${index+1}: Jabatan (${sig.jabatan})</option>`);
            // Image/QR
            if (sig.type === 'qr') {
                select.append(`<option value="${prefix}QR" data-label="Ttd ${index+1}: QR Code" data-sample="[QR CODE]" data-dynamic="true">Ttd ${index+1}: QR Code</option>`);
            } else {
                select.append(`<option value="${prefix}Image" data-label="Ttd ${index+1}: Gambar Ttd" data-sample="[IMG]" data-dynamic="true">Ttd ${index+1}: Gambar Ttd</option>`);
            }
        });
    }

    $(document).ready(function() {
        // Init Rank UI
        renderRankSettings();
        
        // Init Signatory UI
        renderSignatoriesList();
        updateAvailableFields();

        // Rank Change Events
        $('#rankJuaraCount, #rankLabelJuara, #rankHarapanCount, #rankLabelHarapan, #rankLabelPeserta').on('change keyup', function() {
            rankSettings.juaraCount = $('#rankJuaraCount').val();
            rankSettings.labelJuara = $('#rankLabelJuara').val();
            rankSettings.harapanCount = $('#rankHarapanCount').val();
            rankSettings.labelHarapan = $('#rankLabelHarapan').val();
            rankSettings.labelPeserta = $('#rankLabelPeserta').val();
            markAsDirty();
        });

        // Config Menu Dropdown Logic (Navbar Style)
        $('.config-menu-item').click(function(e) {
            e.preventDefault();
            var section = $(this).data('section');
            var title = $(this).data('title');
            
            // Toggle Sections
            $('.config-section').hide();
            $('#section-' + section).show();
            
            // Update Title
            $('#currentSectionTitle').text(title);
            
            // Update Icons (Check/Circle logic)
            $('.config-menu-item .icon-indicator').removeClass('fas fa-check text-warning').addClass('far fa-circle');
            $(this).find('.icon-indicator').removeClass('far fa-circle').addClass('fas fa-check text-warning');

            // Update Active Class (Optional, for styling)
            $('.config-menu-item').removeClass('active');
            $(this).addClass('active');
        });

        // Add Signatory Button
        $('#btnAddSignatory').click(function() {
            addSignatoryPopup();
        });

        canvas = document.getElementById('templateCanvas');
        ctx = canvas.getContext('2d');
        
        // Initial button state
        updateSaveButtonState();

        // Load template image
        templateImage.src = $('#templatePath').val();
        templateImage.onload = function() {
            drawCanvas();
        };

        // Load existing fields
        <?php if (!empty($fields)): ?>
        var dbFields = <?= json_encode($fields) ?>;
        fields = dbFields.map(function(f) {
            return {
                name: f.FieldName,
                label: f.FieldLabel,
                sample: f.FieldLabel, 
                x: parseFloat(f.PosX),
                y: parseFloat(f.PosY),
                font_family: f.FontFamily,
                font_size: parseInt(f.FontSize),
                font_style: f.FontStyle,
                text_align: f.TextAlign,
                text_color: f.TextColor,
                max_width: parseInt(f.MaxWidth)
            };
        });
        <?php endif; ?>

    renderFieldsList();
    
    // Add field button
    $('#btnAddField').click(function() {
        var selectedOption = $('#selectField option:selected');
        var fieldName = selectedOption.val();
        
        if (!fieldName) {
            Swal.fire('Perhatian', 'Pilih field terlebih dahulu', 'warning');
            return;
        }

        // Check if field already exists
        if (fields.find(f => f.name === fieldName)) {
            Swal.fire('Perhatian', 'Field sudah ditambahkan', 'warning');
            return;
        }

        var field = {
            name: fieldName,
            label: selectedOption.data('label'),
            sample: selectedOption.data('sample'),
            x: 100,
            y: 100,
            font_family: 'Arial',
            font_size: 24,
            font_style: 'B',
            text_align: 'C',
            text_color: '#000000',
            max_width: 0
        };

        fields.push(field);
        renderFieldsList();
        drawCanvas();
        markAsDirty(); // Mark as dirty
        
        $('#selectField').val('');
    });

    // Canvas click to place field
    canvas.addEventListener('click', function(e) {
        if (selectedFieldIndex >= 0) {
            var rect = canvas.getBoundingClientRect();
            var scaleX = canvas.width / rect.width;
            var scaleY = canvas.height / rect.height;
            
            fields[selectedFieldIndex].x = (e.clientX - rect.left) * scaleX;
            fields[selectedFieldIndex].y = (e.clientY - rect.top) * scaleY;
            
            drawCanvas();
            updateFieldForm(selectedFieldIndex);
            markAsDirty(); // Mark as dirty
        }
    });

    // Canvas drag and resize
    canvas.addEventListener('mousedown', function(e) {
        e.preventDefault(); // Prevent text selection during drag
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var mouseX = (e.clientX - rect.left) * scaleX;
        var mouseY = (e.clientY - rect.top) * scaleY;

        // First, check if clicking on resize handle of selected field
        if (selectedFieldIndex >= 0) {
            var field = fields[selectedFieldIndex];
            if (field._handleBounds) {
                var h = field._handleBounds;
                if (mouseX >= h.x && mouseX <= h.x + h.width &&
                    mouseY >= h.y && mouseY <= h.y + h.height) {
                    isResizing = true;
                    resizeStartY = mouseY;
                    resizeStartFontSize = field.font_size;
                    return;
                }
            }
        }

        // Check if clicking on a field (for selection and drag)
        for (let i = fields.length - 1; i >= 0; i--) {
            var field = fields[i];
            ctx.font = `${field.font_style === 'B' ? 'bold' : 'normal'} ${field.font_size}px ${field.font_family}`;
            var textWidth = ctx.measureText(field.sample).width;
            
            // Calculate bounding box based on alignment
            var boxX = field.x;
            if (field.text_align === 'C') {
                boxX = field.x - textWidth / 2;
            } else if (field.text_align === 'R') {
                boxX = field.x - textWidth;
            }
            
            if (mouseX >= boxX - 10 && mouseX <= boxX + textWidth + 10 &&
                mouseY >= field.y - 10 && mouseY <= field.y + field.font_size + 10) {
                selectedFieldIndex = i;
                isDragging = true;
                dragOffset.x = mouseX - field.x;
                dragOffset.y = mouseY - field.y;
                drawCanvas(); // Redraw to show selection
                renderFieldsList();
                break;
            }
        }
    });

    // Use document-level mousemove for better drag accuracy (even when mouse leaves canvas)
    document.addEventListener('mousemove', function(e) {
        if (!isDragging && !isResizing) {
            // Only handle cursor change for canvas hover
            if (e.target === canvas && selectedFieldIndex >= 0) {
                var rect = canvas.getBoundingClientRect();
                var scaleX = canvas.width / rect.width;
                var scaleY = canvas.height / rect.height;
                var mouseX = (e.clientX - rect.left) * scaleX;
                var mouseY = (e.clientY - rect.top) * scaleY;
                
                var field = fields[selectedFieldIndex];
                if (field && field._handleBounds) {
                    var h = field._handleBounds;
                    if (mouseX >= h.x && mouseX <= h.x + h.width &&
                        mouseY >= h.y && mouseY <= h.y + h.height) {
                        canvas.style.cursor = 'nwse-resize';
                    } else {
                        canvas.style.cursor = 'crosshair';
                    }
                }
            }
            return;
        }
        
        e.preventDefault(); // Prevent text selection
        
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var mouseX = (e.clientX - rect.left) * scaleX;
        var mouseY = (e.clientY - rect.top) * scaleY;
        
        // Handle resizing
        if (isResizing && selectedFieldIndex >= 0) {
            var deltaY = mouseY - resizeStartY;
            var newFontSize = Math.max(8, Math.min(200, resizeStartFontSize + Math.round(deltaY / 2)));
            fields[selectedFieldIndex].font_size = newFontSize;
            
            drawCanvas();
            renderFieldsList();
            markAsDirty();
            return;
        }
        
        // Handle dragging - clamp to canvas bounds for accuracy
        if (isDragging && selectedFieldIndex >= 0) {
            var newX = mouseX - dragOffset.x;
            var newY = mouseY - dragOffset.y;
            
            // Clamp to canvas bounds (with some padding)
            newX = Math.max(0, Math.min(canvas.width - 50, newX));
            newY = Math.max(10, Math.min(canvas.height - 10, newY));
            
            fields[selectedFieldIndex].x = Math.round(newX);
            fields[selectedFieldIndex].y = Math.round(newY);
            
            drawCanvas();
            renderFieldsList(); // Update form values in real-time
            markAsDirty();
        }
    });

    // Use document-level mouseup to ensure we always catch the release
    document.addEventListener('mouseup', function(e) {
        if (isDragging || isResizing) {
            isDragging = false;
            isResizing = false;
            canvas.style.cursor = 'crosshair';
        }
    });

    // Save configuration
    $('#btnSaveConfig').click(function() {
        if (fields.length === 0) {
            Swal.fire('Perhatian', 'Tambahkan minimal 1 field', 'warning');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        // Clean fields before sending (remove non-serializable properties)
        var cleanFields = fields.map(function(f) {
            return {
                name: f.name,
                label: f.label,
                sample: f.sample,
                x: f.x,
                y: f.y,
                font_family: f.font_family,
                font_size: f.font_size,
                font_style: f.font_style,
                text_align: f.text_align,
                text_color: f.text_color,
                max_width: f.max_width
            };
        });

        $.ajax({
            url: '<?= base_url('backend/perlombaan/save-field-config') ?>',
            type: 'POST',
            data: {
                template_id: $('#templateId').val(),
                fields: cleanFields,
                rank_settings: rankSettings,
                signatory_settings: signatorySettings
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success');
                    hasUnsavedChanges = false;
                    localStorage.removeItem(autoSaveKey); // Clear backup on success
                    updateSaveButtonState();
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Konfigurasi');
                updateSaveButtonState(); // Re-check button state
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Konfigurasi');
            }
        });
    });

    $('#btnTogglePreview').click(function() {
        drawCanvas();
    });

    // Prevent navigation if unsaved
    window.addEventListener('beforeunload', function (e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Intercept back button specifically
    $('a.btn-secondary').click(function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            var link = $(this).attr('href');
            Swal.fire({
                title: 'Perubahan Belum Disimpan',
                text: 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tinggalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    hasUnsavedChanges = false; // Disable flag so beforeunload doesn't trigger
                    window.location.href = link;
                }
            });
        }
    });

    // Keyboard arrow keys handler
    $(document).keydown(function(e) {
        if (selectedFieldIndex < 0) return;
        
        // Don't capture if typing in input field
        if ($(e.target).is('input, select, textarea')) return;
        
        var step = e.shiftKey ? 10 : 1; // Hold Shift for larger steps
        var moved = false;
        
        switch(e.keyCode) {
            case 37: // Left arrow
                fields[selectedFieldIndex].x -= step;
                moved = true;
                break;
            case 38: // Up arrow
                fields[selectedFieldIndex].y -= step;
                moved = true;
                break;
            case 39: // Right arrow
                fields[selectedFieldIndex].x += step;
                moved = true;
                break;
            case 40: // Down arrow
                fields[selectedFieldIndex].y += step;
                moved = true;
                break;
        }
        
        if (moved) {
            e.preventDefault(); // Prevent page scrolling
            drawCanvas();
            renderFieldsList();
            markAsDirty();
        }
    });
});

function drawCanvas() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw template image
    ctx.drawImage(templateImage, 0, 0, canvas.width, canvas.height);
    
    // Draw fields
    fields.forEach((field, index) => {
        // Check if this is a signature image field
        var isImageField = field.name.endsWith('_Image');
        var isQRField = field.name.endsWith('_QR');
        
        if (isImageField || isQRField) {
            // Extract signatory index from field name (e.g., "Sig1_Image" -> 0)
            var sigMatch = field.name.match(/Sig(\d+)_/);
            if (sigMatch) {
                var sigIndex = parseInt(sigMatch[1]) - 1;
                var signatory = signatorySettings[sigIndex];
                
                // Calculate dimensions (font_size = height, matches PDF)
                var imgHeight = field.font_size;
                var imgWidth = imgHeight; // QR is square, images will adjust
                
                // Handle QR Code fields
                if (isQRField) {
                    // QR Code - draw a styled QR placeholder (square)
                    var drawX = field.x;
                    if (field.text_align === 'C') drawX = field.x - imgWidth / 2;
                    else if (field.text_align === 'R') drawX = field.x - imgWidth;
                    
                    // Draw QR-like pattern background
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(drawX, field.y, imgWidth, imgHeight);
                    ctx.strokeStyle = '#333333';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(drawX, field.y, imgWidth, imgHeight);
                    
                    // Draw QR pattern representation
                    var cellSize = imgWidth / 10;
                    ctx.fillStyle = '#000000';
                    // Corner markers
                    ctx.fillRect(drawX + cellSize, field.y + cellSize, cellSize * 2, cellSize * 2);
                    ctx.fillRect(drawX + imgWidth - cellSize * 3, field.y + cellSize, cellSize * 2, cellSize * 2);
                    ctx.fillRect(drawX + cellSize, field.y + imgHeight - cellSize * 3, cellSize * 2, cellSize * 2);
                    // Center pattern
                    ctx.fillRect(drawX + cellSize * 4, field.y + cellSize * 4, cellSize * 2, cellSize * 2);
                    
                    // Label
                    ctx.fillStyle = '#666666';
                    ctx.font = Math.max(8, imgHeight / 6) + 'px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillText('QR', drawX + imgWidth / 2, field.y + imgHeight - 2);
                    
                    field._imgBounds = { x: drawX, y: field.y, width: imgWidth, height: imgHeight };
                    
                } else if (isImageField && signatory && signatory.image) {
                    // Signature Image - load and render actual image
                    imgWidth = imgHeight * 1.5; // Default aspect ratio
                    
                    // Check if image is already loaded in cache
                    if (!field._loadedImage) {
                        field._loadedImage = new Image();
                        field._loadedImage.crossOrigin = 'anonymous';
                        field._loadedImage.onload = function() {
                            drawCanvas(); // Redraw when image loads
                        };
                        field._loadedImage.src = '<?= base_url('uploads/') ?>' + signatory.image;
                    }
                    
                    // Draw image if loaded
                    if (field._loadedImage && field._loadedImage.complete && field._loadedImage.naturalWidth > 0) {
                        // Calculate aspect ratio
                        var aspectRatio = field._loadedImage.naturalWidth / field._loadedImage.naturalHeight;
                        imgWidth = imgHeight * aspectRatio;
                        
                        // Calculate position based on alignment
                        var drawX = field.x;
                        if (field.text_align === 'C') {
                            drawX = field.x - imgWidth / 2;
                        } else if (field.text_align === 'R') {
                            drawX = field.x - imgWidth;
                        }
                        
                        ctx.drawImage(field._loadedImage, drawX, field.y, imgWidth, imgHeight);
                        
                        // Store bounds for selection box
                        field._imgBounds = { x: drawX, y: field.y, width: imgWidth, height: imgHeight };
                    } else {
                        // Show placeholder while loading
                        ctx.fillStyle = '#cccccc';
                        var drawX = field.x;
                        if (field.text_align === 'C') drawX = field.x - imgWidth / 2;
                        else if (field.text_align === 'R') drawX = field.x - imgWidth;
                        ctx.fillRect(drawX, field.y, imgWidth, imgHeight);
                        ctx.fillStyle = '#666666';
                        ctx.font = '14px Arial';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText('Loading...', drawX + imgWidth/2, field.y + imgHeight/2);
                        field._imgBounds = { x: drawX, y: field.y, width: imgWidth, height: imgHeight };
                    }
                } else {
                    // No image uploaded - show placeholder
                    imgWidth = imgHeight * 1.5;
                    var drawX = field.x;
                    if (field.text_align === 'C') drawX = field.x - imgWidth / 2;
                    else if (field.text_align === 'R') drawX = field.x - imgWidth;
                    
                    ctx.fillStyle = '#ffeeee';
                    ctx.fillRect(drawX, field.y, imgWidth, imgHeight);
                    ctx.strokeStyle = '#ff9999';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([5, 3]);
                    ctx.strokeRect(drawX, field.y, imgWidth, imgHeight);
                    ctx.setLineDash([]);
                    ctx.fillStyle = '#cc0000';
                    ctx.font = '12px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('No Image', drawX + imgWidth/2, field.y + imgHeight/2);
                    field._imgBounds = { x: drawX, y: field.y, width: imgWidth, height: imgHeight };
                }
            }
        } else {
            // Regular text field
            ctx.font = `${field.font_style === 'B' ? 'bold' : 'normal'} ${field.font_size}px ${field.font_family}`;
            ctx.fillStyle = field.text_color;
            
            ctx.textAlign = field.text_align === 'C' ? 'center' : (field.text_align === 'R' ? 'right' : 'left');
            ctx.textBaseline = 'top'; // Match CSS top positioning
            
            ctx.fillText(field.sample, field.x, field.y);
        }
        
        // Draw selection box and resize handles for selected field
        if (index === selectedFieldIndex) {
            var textWidth, textHeight, boxX, boxY;
            
            if (field._imgBounds) {
                // Use image bounds
                boxX = field._imgBounds.x;
                boxY = field._imgBounds.y;
                textWidth = field._imgBounds.width;
                textHeight = field._imgBounds.height;
            } else {
                // Use text bounds
                ctx.font = `${field.font_style === 'B' ? 'bold' : 'normal'} ${field.font_size}px ${field.font_family}`;
                textWidth = ctx.measureText(field.sample).width;
                textHeight = field.font_size;
                
                // Calculate bounding box based on alignment
                boxX = field.x;
                if (field.text_align === 'C') {
                    boxX = field.x - textWidth / 2;
                } else if (field.text_align === 'R') {
                    boxX = field.x - textWidth;
                }
                boxY = field.y;
            }
            
            // Draw bounding box
            ctx.strokeStyle = '#0066ff';
            ctx.lineWidth = 2;
            ctx.setLineDash([5, 3]);
            ctx.strokeRect(boxX - 5, boxY - 5, textWidth + 10, textHeight + 10);
            ctx.setLineDash([]);
            
            // Draw resize handle (bottom-right corner)
            var handleSize = 12;
            var handleX = boxX + textWidth + 5 - handleSize/2;
            var handleY = boxY + textHeight + 5 - handleSize/2;
            
            ctx.fillStyle = '#0066ff';
            ctx.fillRect(handleX, handleY, handleSize, handleSize);
            
            // Draw resize icon in handle
            ctx.fillStyle = '#ffffff';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('↘', handleX + handleSize/2, handleY + handleSize/2);
            
            // Store handle bounds for click detection
            field._handleBounds = {
                x: handleX,
                y: handleY,
                width: handleSize,
                height: handleSize
            };
            
            // Store text bounds for reference
            field._textBounds = {
                x: boxX - 5,
                y: boxY - 5,
                width: textWidth + 10,
                height: textHeight + 10
            };
        }
    });
}

function renderFieldsList() {
    var html = '';
    fields.forEach((field, index) => {
        html += `
            <div class="card card-outline ${index === selectedFieldIndex ? 'card-primary' : 'card-default'} mb-2">
                <div class="card-header p-2">
                    <h6 class="card-title">${field.label}</h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool btn-sm" onclick="selectField(${index})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-tool btn-sm" onclick="removeField(${index})">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-2" id="fieldForm${index}" style="display: ${index === selectedFieldIndex ? 'block' : 'none'}">
                    <div class="row">
                        <div class="col-6">
                            <label>X:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, -1, 0)" title="Geser Kiri">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                </div>
                                <input type="number" class="form-control form-control-sm text-center" value="${Math.round(field.x)}" 
                                       onchange="updateFieldValue(${index}, 'x', this.value)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 1, 0)" title="Geser Kanan">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label>Y:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 0, -1)" title="Geser Atas">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </div>
                                <input type="number" class="form-control form-control-sm text-center" value="${Math.round(field.y)}" 
                                       onchange="updateFieldValue(${index}, 'y', this.value)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="moveField(${index}, 0, 1)" title="Geser Bawah">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                         <div class="col-12">
                            <label>Font Family:</label>
                            <select class="form-control form-control-sm" onchange="updateFieldValue(${index}, 'font_family', this.value)">
                                <option value="Arial" ${field.font_family === 'Arial' ? 'selected' : ''}>Arial</option>
                                <option value="Times New Roman" ${field.font_family === 'Times New Roman' ? 'selected' : ''}>Times New Roman</option>
                                <option value="Courier New" ${field.font_family === 'Courier New' ? 'selected' : ''}>Courier New</option>
                                <option value="Dejavu Sans" ${field.font_family === 'Dejavu Sans' ? 'selected' : ''}>Dejavu Sans</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label>Font Size:</label>
                        <input type="number" class="form-control form-control-sm" value="${field.font_size}" 
                               onchange="updateFieldValue(${index}, 'font_size', this.value)">
                    </div>
                    <div class="form-group">
                        <label>Color:</label>
                        <input type="color" class="form-control form-control-sm" value="${field.text_color}" 
                               onchange="updateFieldValue(${index}, 'text_color', this.value)">
                    </div>
                    <div class="form-group">
                        <label>Align:</label>
                        <select class="form-control form-control-sm" onchange="updateFieldValue(${index}, 'text_align', this.value)">
                            <option value="L" ${field.text_align === 'L' ? 'selected' : ''}>Left</option>
                            <option value="C" ${field.text_align === 'C' ? 'selected' : ''}>Center</option>
                            <option value="R" ${field.text_align === 'R' ? 'selected' : ''}>Right</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
    });
    $('#fieldsList').html(html);
}

function selectField(index) {
    // Toggle: if clicking on already selected field, deselect it
    if (selectedFieldIndex === index) {
        selectedFieldIndex = -1;
    } else {
        selectedFieldIndex = index;
    }
    renderFieldsList();
    drawCanvas();
}

function removeField(index) {
    fields.splice(index, 1);
    selectedFieldIndex = -1;
    renderFieldsList();
    drawCanvas();
    markAsDirty();
}

function updateFieldValue(index, key, value) {
    fields[index][key] = key === 'x' || key === 'y' || key === 'font_size' ? parseInt(value) : value;
    drawCanvas();
    markAsDirty();
}

// Move field by delta X and Y (for arrow buttons)
function moveField(index, deltaX, deltaY) {
    if (index < 0 || index >= fields.length) return;
    
    fields[index].x += deltaX;
    fields[index].y += deltaY;
    
    selectedFieldIndex = index;
    drawCanvas();
    renderFieldsList();
    markAsDirty();
}

function updateFieldForm(index) {
    renderFieldsList();
}

// Helpers for Dirty State
function markAsDirty() {
    if (!hasUnsavedChanges) {
        hasUnsavedChanges = true;
        updateSaveButtonState();
    }
    // Save to local storage for crash recovery
    localStorage.setItem(autoSaveKey, JSON.stringify(fields));
}

function updateSaveButtonState() {
    var btn = $('#btnSaveConfig');
    if (hasUnsavedChanges) {
        btn.prop('disabled', false);
        btn.removeClass('btn-secondary').addClass('btn-primary');
        btn.html('<i class="fas fa-save"></i> Simpan Konfigurasi');
    } else {
        btn.prop('disabled', true);
        btn.removeClass('btn-primary').addClass('btn-secondary');
        btn.html('<i class="fas fa-check"></i> Tersimpan');
    }
}
// Rank & Signatory Functions
function renderRankSettings() {
    $('#rankJuaraCount').val(rankSettings.juaraCount || 3);
    $('#rankLabelJuara').val(rankSettings.labelJuara || 'Juara');
    $('#rankHarapanCount').val(rankSettings.harapanCount || 3);
    $('#rankLabelHarapan').val(rankSettings.labelHarapan || 'Harapan');
    $('#rankLabelPeserta').val(rankSettings.labelPeserta || 'Peserta');
}

function renderSignatoriesList() {
    var html = '';
    signatorySettings.forEach((sig, index) => {
        html += `
            <div class="card card-outline card-info mb-2">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${sig.name}</strong><br>
                            <small class="text-muted">${sig.jabatan} (${sig.type})</small>
                        </div>
                        <div>
                            ${sig.type === 'manual' ? `<button class="btn btn-xs btn-warning mr-1" onclick="replaceSignatoryImage(${index})" title="Ganti Gambar"><i class="fas fa-edit"></i></button>` : ''}
                            <button class="btn btn-xs btn-danger" onclick="removeSignatory(${index})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    $('#signatoriesList').html(html);
}

function addSignatoryPopup() {
    Swal.fire({
        title: 'Tambah Penandatangan',
        html: `
            <input id="swal-sig-name" class="swal2-input" placeholder="Nama Lengkap">
            <input id="swal-sig-jabatan" class="swal2-input" placeholder="Jabatan (ex: Kepala TPQ)">
            <select id="swal-sig-type" class="swal2-input">
                <option value="qr">QR Code (Verifikasi Digital)</option>
                <option value="manual">Upload Gambar Tanda Tangan</option>
            </select>
            <div id="swal-sig-upload-container" style="display:none; margin-top:10px;">
                <div class="custom-file text-left">
                    <input type="file" class="custom-file-input" id="swal-sig-file" accept="image/png, image/jpeg">
                    <label class="custom-file-label" for="swal-sig-file">Pilih Gambar...</label>
                </div>
            </div>
        `,
        didOpen: () => {
            const typeSelect = Swal.getPopup().querySelector('#swal-sig-type');
            const uploadContainer = Swal.getPopup().querySelector('#swal-sig-upload-container');
            const fileInput = Swal.getPopup().querySelector('#swal-sig-file');
            const fileLabel = Swal.getPopup().querySelector('.custom-file-label');
            typeSelect.addEventListener('change', () => {
                uploadContainer.style.display = typeSelect.value === 'manual' ? 'block' : 'none';
            });
            
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) fileLabel.textContent = fileInput.files[0].name;
                else fileLabel.textContent = 'Pilih Gambar...';
            });
        },
        focusConfirm: false,
        preConfirm: () => {
            const name = document.getElementById('swal-sig-name').value;
            const jabatan = document.getElementById('swal-sig-jabatan').value;
            const type = document.getElementById('swal-sig-type').value;
            const fileInput = document.getElementById('swal-sig-file');
            
            if (!name || !jabatan) {
                Swal.showValidationMessage('Nama dan Jabatan harus diisi');
                return false;
            }

            if (type === 'manual') {
                if (fileInput.files.length === 0) {
                     Swal.showValidationMessage('Silakan upload gambar tanda tangan');
                     return false;
                }
                // Return file to be processed AFTER Swal closes
                return {
                    name: name,
                    jabatan: jabatan,
                    type: type,
                    file: fileInput.files[0]
                };
            }

            return {
                name: name,
                jabatan: jabatan,
                type: type,
                image: null
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            if (data.type === 'manual' && data.file) {
                // Process Crop & Upload
                openCropModal(data.file).then(blob => {
                     const formData = new FormData();
                     formData.append('signature_image', blob, 'signature.png');
                     
                     fetch('<?= base_url('backend/perlombaan/uploadSignatureImage') ?>', {
                        method: 'POST',
                        body: formData
                     })
                     .then(response => response.json())
                     .then(resp => {
                        if (!resp.success) {
                            throw new Error(resp.message);
                        }
                        const finalData = {
                            name: data.name,
                            jabatan: data.jabatan,
                            type: data.type,
                            image: resp.filename
                        };
                        signatorySettings.push(finalData);
                        renderSignatoriesList();
                        updateAvailableFields();
                        markAsDirty();
                        Swal.fire('Berhasil', 'Penandatangan ditambahkan.', 'success');
                     })
                     .catch(err => Swal.fire('Error', 'Upload gagal: ' + err, 'error'));
                });
            } else {
                signatorySettings.push(data);
                renderSignatoriesList();
                updateAvailableFields();
                markAsDirty();
                Swal.fire('Berhasil', 'Penandatangan ditambahkan.', 'success');
            }
        }
    });
}

function removeSignatory(index) {
    Swal.fire({
        title: 'Hapus Penandatangan?',
        text: "Anda harus menghapus field terkait secara manual jika sudah ditambahkan ke canvas.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            signatorySettings.splice(index, 1);
            renderSignatoriesList();
            updateAvailableFields();
            markAsDirty();
        }
    });
}

let replaceIndex = -1;

function replaceSignatoryImage(index) {
    replaceIndex = index;
    $('#hidden-file-input').trigger('click');
}

// Handle hidden input change
$('#hidden-file-input').on('change', function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const index = replaceIndex;
        
        // Reset input value to allow selecting same file again
        $(this).val('');

        openCropModal(file).then(blob => {
            const oldFilename = signatorySettings[index].image;
            // Assuming oldFilename is relative to uploads/ directory based on previous logic
            const oldImageUrl = '<?= base_url('uploads') ?>/' + oldFilename;
            const newImageUrl = URL.createObjectURL(blob);
            
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                html: `
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted small font-weight-bold">Sebelumnya</p>
                            <div style="border:1px solid #ddd; padding:5px; min-height:100px; display:flex; align-items:center; justify-content:center; background-color:#f8f9fa;">
                                <img src="${oldImageUrl}" style="max-width:100%; max-height:150px;" onerror="this.src=''; this.alt='Gambar Lama Tidak Ditemukan'">
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="text-info small font-weight-bold">Baru (Preview)</p>
                            <div style="border:1px solid #17a2b8; padding:5px; min-height:100px; display:flex; align-items:center; justify-content:center; background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;">
                                <img src="${newImageUrl}" style="max-width:100%; max-height:150px;">
                            </div>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan Perubahan',
                cancelButtonText: 'Batal',
                width: '600px',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('signature_image', blob, 'signature.png');
                    
                    if (oldFilename) {
                        formData.append('old_image', oldFilename);
                    }
                    
                    fetch('<?= base_url('backend/perlombaan/uploadSignatureImage') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message);
                        }
                        // Update image path
                        signatorySettings[index].image = data.filename;
                        renderSignatoriesList();
                        markAsDirty();
                        Swal.fire('Berhasil', 'Gambar tanda tangan diperbarui.', 'success');
                    })
                    .catch(error => {
                        Swal.fire('Gagal', `Upload gagal: ${error}`, 'error');
                    });
                }
            });
        });
    }
});

// --- Cropper Helper Variables & Functions ---
let cropper;
let cropResolve;
let cropReject;

// Initialize Listeners when DOM is ready
$(function() {
    // Handle Save Button in Modal
    $('#btnCropAndSave').on('click', function() {
        if (!cropper) return;
        
        // Get cropped canvas
        let canvas = cropper.getCroppedCanvas({
            maxWidth: 600, // Limit size
        });

        if ($('#removeBgSwitch').is(':checked')) {
            const ctx = canvas.getContext('2d');
            const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imgData.data;
            
            // Iterate pixels and make white transparent
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                // Threshold: if R,G,B > 220 (Light gray to white)
                if (r > 200 && g > 200 && b > 200) {
                    data[i + 3] = 0; // Set Alpha to 0
                }
            }
            ctx.putImageData(imgData, 0, 0);
        }

        canvas.toBlob((blob) => {
            if (cropResolve) cropResolve(blob);
            $('#cropModal').modal('hide');
        }, 'image/png');
    });

    // Cancel Handler
    // Cancel Handler
    $('#cropModal').on('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        $('#removeBgSwitch').prop('checked', false);
        $('#preview-container').hide();
    });

    // Preview Logic
    function updatePreview() {
        if (!cropper) return;
        
        let canvas = cropper.getCroppedCanvas({ maxWidth: 600 });
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imgData.data;
        
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            if (r > 200 && g > 200 && b > 200) {
                data[i + 3] = 0;
            }
        }
        ctx.putImageData(imgData, 0, 0);
        
        $('#preview-image').attr('src', canvas.toDataURL());
    }

    $('#removeBgSwitch').on('change', function() {
        if (this.checked) {
            $('#preview-container').show();
            updatePreview();
        } else {
            $('#preview-container').hide();
        }
    });
});

function openCropModal(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Set image verify it is loaded
            $('#image-to-crop').attr('src', e.target.result);
            
            // Show modal
            $('#cropModal').modal('show');
            
            // Init Cropper after modal is shown (for correct sizing)
            // Use setTimeout to ensure modal is rendered
            setTimeout(() => {
                if (cropper) cropper.destroy();
                const image = document.getElementById('image-to-crop');
                cropper = new Cropper(image, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    cropend: function() {
                        if ($('#removeBgSwitch').is(':checked')) {
                            updatePreview();
                        }
                    }
                });
            }, 200);
            
            cropResolve = resolve;
            cropReject = reject;
        };
        reader.readAsDataURL(file);
    });
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<!-- Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tanda Tangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="image-to-crop" src="">
                </div>
                <div class="form-group mt-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="removeBgSwitch">
                        <label class="custom-control-label" for="removeBgSwitch">Hapus Background Putih (Transparan)</label>
                    </div>
                    <small class="text-muted">Aktifkan untuk membuat latar belakang putih menjadi transparan.</small>
                    
                    <div id="preview-container" style="display:none; margin-top: 15px; border: 1px solid #ddd; padding: 10px; background-color: #f0f0f0; background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;">
                        <label>Preview Hasil:</label><br>
                        <img id="preview-image" style="max-width: 100%; max-height: 200px; display: block; margin: auto;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnCropAndSave">Simpan & Upload</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
